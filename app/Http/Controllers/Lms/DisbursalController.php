<?php
namespace App\Http\Controllers\Lms;
use App\Http\Controllers\Controller;
use Auth;
use Illuminate\Http\Request;
use App\Libraries\Idfc_lib;
use Illuminate\Support\Facades\Storage;
use App\Inv\Repositories\Contracts\UserInterface as InvUserRepoInterface;
use App\Inv\Repositories\Contracts\ApplicationInterface as InvAppRepoInterface;
use App\Inv\Repositories\Contracts\DocumentInterface as InvDocumentRepoInterface;
use App\Inv\Repositories\Contracts\LmsInterface as InvLmsRepoInterface;
use Session;
use Helpers;
use App\Inv\Repositories\Contracts\Traits\ApplicationTrait;
use App\Inv\Repositories\Contracts\Traits\LmsTrait;
use App\Inv\Repositories\Contracts\MasterInterface as InvMasterRepoInterface;
use App\Inv\Repositories\Models\Lms\Disbursal;
use App\Inv\Repositories\Models\Lms\InvoiceDisbursed;
use App\Inv\Repositories\Models\AppProgramOffer;
use App\Inv\Repositories\Models\Master\BaseRate;
use Illuminate\Support\Facades\View;
use Carbon\Carbon;
use App\Helpers\ManualApportionmentHelper;

class DisbursalController extends Controller
{
	use ApplicationTrait;
	use LmsTrait;
        
	protected $appRepo;
	protected $userRepo;
	protected $docRepo;
	protected $lmsRepo;
	protected $masterRepo;

	/**
	 * The pdf instance.
	 *
	 * @var App\Libraries\Pdf
	 */
	protected $pdf;
	
	public function __construct(InvAppRepoInterface $app_repo, InvUserRepoInterface $user_repo, InvDocumentRepoInterface $doc_repo, InvLmsRepoInterface $lms_repo ,InvMasterRepoInterface $master){
		$this->appRepo = $app_repo;
		$this->userRepo = $user_repo;
		$this->docRepo = $doc_repo;
		$this->lmsRepo = $lms_repo;
        $this->masterRepo = $master;
		$this->middleware('checkBackendLeadAccess');
            $this->middleware('checkEodProcess');
	}
	
	/**
	 * Display a listing of the customer.
	 *
	 * @return \Illuminate\Http\Response
	 */
	public function requestList()
	{
		return view('lms.disbursal.request_list');              
	}

	/**
	 * Display a listing of the customer.
	 *
	 * @return \Illuminate\Http\Response
	 */
	public function viewInvoice(Request $request)
	{
		$userId = $request->get('user_id');
		$status = $request->get('status');
		$userIvoices = $this->lmsRepo->getAllUserInvoice($userId);
		
		return view('lms.disbursal.view_invoice')
				->with([
					'userIvoices'=>$userIvoices, 
					'status'=>$status,
					'userId' => $userId 
				]);              
	}

	/**
	 * Display a listing of the customer.
	 *
	 * @return \Illuminate\Http\Response
	 */
	public function confirmDisburse(Request $request)
	{
		return view('lms.disbursal.confirm_disburse');              
	}
	/**
	 * Display a listing of the customer. -- not using
	 *
	 * @return \Illuminate\Http\Response
	 */
	public function sendToBank(Request $request)
	{
            
            if ($request->get('eod_process')) {
                Session::flash('error', trans('backend_messages.lms_eod_batch_process_msg'));
                return back();
            }       
            
		$invoiceIds = $request->invoiceids;
		$customerRecords = $request->user_ids;
		$disburseType = $request->disburse_type;
		$transId = $request->trans_id;
		$disburseDate = $request->disburse_date;
		// $utrNo = $request->utr_no;
		$remarks = $request->remarks;

		// --- UAT code 

		// $allrecords = [1,2];
		// $requestData = $this->_apiData();
		// --- UAT code end 

		// --- production code 

		$record = array_filter(explode(",",$invoiceIds));
		$userIds = array_filter(explode(",",$customerRecords));

		$userIvoices = $this->lmsRepo->getAllUserInvoiceIds($userIds)->toArray();
		$allrecords = array_unique(array_merge($record, $userIvoices));
		$allrecords = array_map('intval', $allrecords);
		$allinvoices = $this->lmsRepo->getInvoices($allrecords)->toArray();
		$supplierIds = $this->lmsRepo->getInvoiceSupplier($allrecords)->toArray();
		

		foreach ($allinvoices as $inv) {
			if($inv['supplier']['is_buyer'] == 2 && empty($inv['supplier']['anchor_bank_details'])){
				return redirect()->route('lms_disbursal_request_list')->withErrors(trans('backend_messages.noBankAccount'));
			} elseif ($inv['supplier']['is_buyer'] == 1 && empty($inv['supplier_bank_detail'])) {
				return redirect()->route('lms_disbursal_request_list')->withErrors(trans('backend_messages.noBankAccount'));
			}
		}

		$params = array('http_header' => '', 'header' => '', 'request' => []);


		$fundedAmount = 0;
		$interest = 0;
		$disburseAmount = 0;
		$totalInterest = 0;
		$totalFunded = 0;
		$totalMargin = 0;

		foreach ($supplierIds as $userid) {
			$disburseAmount = 0;
			foreach ($allinvoices as $invoice) {
				
				$invoice['disburse_date'] = $disburseDate;
				$disburseRequestData = $this->createInvoiceDisbursalData($invoice, $disburseType);
				$createDisbursal = $this->lmsRepo->saveDisbursalRequest($disburseRequestData);
				$refId ='CAP'.$userid;
				if($invoice['supplier_id'] = $userid) {
					$interest= 0;
					$margin= 0;
					$now = strtotime($invoice['invoice_due_date']); // or your date as well
			        $your_date = strtotime($invoice['invoice_date']);
			        $datediff = abs($now - $your_date);

			        $tenor = round($datediff / (60 * 60 * 24));
			        $margin = (($invoice['invoice_approve_amount']*$invoice['program_offer']['margin'])/100);
			        $fundedAmount = $invoice['invoice_approve_amount'] - $margin;
			        $tInterest = $this->calInterest($fundedAmount, $invoice['program_offer']['interest_rate'], $tenor);

			        if($invoice['program_offer']['payment_frequency'] == 1 && $invoice['program']['interest_borne_by'] == 2) {
			            $interest = $tInterest;
			        }

			        $totalInterest += $interest;
			        $totalMargin += $margin;
			        $totalFunded += $fundedAmount;
    				$disburseAmount += round($fundedAmount, 2);
    				//$disburseAmount += round($fundedAmount - $interest, 2);

				}

				if($disburseType == 1) {
					$updateInvoiceStatus = $this->lmsRepo->updateInvoiceStatus($invoice['invoice_id'], 10);
					$bank_name = $invoice['supplier_bank_detail']['bank']['bank_name'];
                                        $bank_id   = $invoice['supplier_bank_detail']['bank']['id'];
                                        $requestData[$userid]['RefNo'] = $refId;
					$requestData[$userid]['Amount'] = $disburseAmount;
					$requestData[$userid]['Debit_Acct_No'] = '123344455';
					$requestData[$userid]['Debit_Acct_Name'] = 'testing name';
					$requestData[$userid]['Debit_Mobile'] = '9876543210';
					$requestData[$userid]['Ben_IFSC'] = ($bank_id == config('lms.IDFC_BANK_ID')) ? null : $invoice['supplier_bank_detail']['ifsc_code'];
					$requestData[$userid]['Ben_Acct_No'] = $invoice['supplier_bank_detail']['acc_no'];
					$requestData[$userid]['Ben_Name'] = $invoice['supplier_bank_detail']['acc_name'];
					$requestData[$userid]['Ben_BankName'] = $invoice['supplier_bank_detail']['bank']['bank_name'];
					$requestData[$userid]['Ben_Email'] = $invoice['supplier']['email'];
					$requestData[$userid]['Ben_Mobile'] = $invoice['supplier']['mobile_no'];
					$requestData[$userid]['Mode_of_Pay'] = 'IFT';
                                        //$requestData[$userid]['Mode_of_Pay'] = 'BT';
					$requestData[$userid]['Nature_of_Pay'] = 'MPYMT';
					$requestData[$userid]['Remarks'] = 'test remarks';
					$requestData[$userid]['Value_Date'] = date('Y-m-d');

				} else {

					$apiLogData['refer_id'] = $refId;
					$apiLogData['tran_id'] = $transId;
					// $apiLogData['utr_no'] = $utrNo;
					$apiLogData['remark'] = $remarks;
					$disburseApiLog = $this->lmsRepo->createDisburseApi($apiLogData);
					$updateDisbursal = $this->lmsRepo->updateDisburse([
							'disbursal_api_log_id' => $disburseApiLog->disbursal_api_log_id
						], $createDisbursal->disbursal_id);
					
					if ($updateDisbursal) {
						$updateInvoiceStatus = $this->lmsRepo->updateInvoiceStatus($invoice['invoice_id'], 12);
					}
				}
				$eventDate = Helpers::getSysStartDate();
				$disburseDate = Carbon::parse($disburseDate)->format('Y-m-d');		 
				if($disburseType == 2) {
					// disburse transaction $tranType = 16 for payment acc. to mst_trans_type table
					$transactionData = $this->createTransactionData($disburseRequestData['user_id'], [
						'amount' => $fundedAmount, 
						'trans_date' => $disburseDate,
						'created_at' => $eventDate
					], $transId, 16);
					$createTransaction = $this->lmsRepo->saveTransaction($transactionData);

					// interest transaction $tranType = 9 for interest acc. to mst_trans_type table
				
					if ($interest > 0.00) {
						$intrstTrnsData = $this->createTransactionData($disburseRequestData['user_id'], [
							'amount' => $interest, 
							'trans_date' => $disburseDate,
							'created_at' => $eventDate
						], $transId, 9);
						$createTransaction = $this->lmsRepo->saveTransaction($intrstTrnsData);

						$intrstTrnsData = $this->createTransactionData($disburseRequestData['user_id'], [
							'amount' => $interest, 
							'trans_date' => $disburseDate,
							'created_at' => $eventDate
						], $transId, 9, 1);
						$createTransaction = $this->lmsRepo->saveTransaction($intrstTrnsData);
					}

					if ($margin > 0.00) {
						$marginTrnsData = $this->createTransactionData($disburseRequestData['user_id'], [
							'amount' => $margin, 
							'trans_date' => $disburseDate,
							'created_at' => $eventDate
						], $transId, 10, 0);
						$createTransaction = $this->lmsRepo->saveTransaction($marginTrnsData);
					}
				}
			}
		}
		// --- production code end 

		if($disburseType == 1 && !empty($allrecords)) {
			
			$http_header = [
				'timestamp' => date('Y-m-d H:i:s'),
				'txn_id' => _getRand(18)
				];

			$header = [
				'Maker_ID' => "CAPSAVE.M",
				'Checker_ID' => "CAPSAVE.C1",
				'Approver_ID' => "CAPSAVE.C2"
				];

			$params = [
				'http_header' => $http_header,
				'header' => $header,
				'request' => $requestData
				];

			$idfcObj= new Idfc_lib();
			$result = $idfcObj->api_call(Idfc_lib::MULTI_PAYMENT, $params);
			if ($result) {
				//save transaction here
				// for disburse type online idfc bank i.e $disburseType == 1
			}
			return redirect()->route('lms_disbursal_request_list')->withErrors($result);      
		} elseif (empty($allrecords)) {
			return redirect()->route('lms_disbursal_request_list')->withErrors(trans('backend_messages.noSelectedInvoice'));
		}
        
        Session::flash('message',trans('backend_messages.disbursed'));
		return redirect()->route('lms_disbursal_request_list');
	}

    /**
     * Process Interest Accrual
     *      
     * @return mixed
     */
    public function processAccrualInterest()
    {
		if (request()->get('eod_process')) {
			//Session::flash('error', trans('backend_messages.lms_eod_batch_process_msg'));
			return response()->json(['error' => trans('backend_messages.lms_eod_batch_process_msg')]);
		}
		echo "start-----------------";
		$Obj = new ManualApportionmentHelper($this->lmsRepo);
		$Obj->dailyIntAccrual();
		echo "-------------------end";
    }

    /**
     * Display a listing of the customer.
     *
     * @return \Illuminate\Http\Response
     */
    public function disbursedList()
    {
        return view('lms.disbursal.disbursed_list');
    }

    function _apiData($id = 1) {
    	$requestData[$id]['RefNo'] = "CAP1000";
		$requestData[$id]['Amount'] = 120000;
		$requestData[$id]['Debit_Acct_No'] = '123344455';
		$requestData[$id]['Debit_Acct_Name'] = 'testing name';
		$requestData[$id]['Debit_Mobile'] = '9876543210';
		$requestData[$id]['Ben_IFSC'] = "ICICI00001";
		$requestData[$id]['Ben_Acct_No'] = "111111111111";
		$requestData[$id]['Ben_Name'] = "Beni Name";
		$requestData[$id]['Ben_BankName'] = "icici bank";
		$requestData[$id]['Ben_Email'] = "beni@capsave.in";
		$requestData[$id]['Ben_Mobile'] = "8744037213";
		$requestData[$id]['Mode_of_Pay'] = 'IFT';
		$requestData[$id]['Nature_of_Pay'] = 'MPYMT';
		$requestData[$id]['Remarks'] = 'test remarks';
		$requestData[$id]['Value_Date'] = date('Y-m-d');

		return $requestData;
    }

	public function uploadPfDf($user_id, $appId) {
	    $prcsAmt = $this->appRepo->getPrgmLimitByAppId($appId);
	    // dd($prcsAmt);
	    if(isset($prcsAmt->offer)){

			foreach ($prcsAmt->offer as $key => $offer) {
					// $tranType = 4 for processing acc. to mst_trans_type table
				$pf = round((($offer->prgm_limit_amt * $offer->processing_fee)/100),2);
				$pfWGst = round((($pf*18)/100),2);

				$pfDebitData = $this->createTransactionData($user_id,['amount' => $pf, 'gst' => $pfWGst] , null, 4);
				$pfDebitCreate = $this->appRepo->saveTransaction($pfDebitData);

				$pfCreditData = $this->createTransactionData($user_id, ['amount' => $pf, 'gst' => $pfWGst], null, 4, 1);
				$pfCreditCreate = $this->appRepo->saveTransaction($pfCreditData);

				// $tranType = 20 for document fee acc. to mst_trans_type table
				$df = round((($offer->prgm_limit_amt * $offer->document_fee)/100),2);
				$dfWGst = round((($df*18)/100),2);

				$dfDebitData = $this->createTransactionData($user_id, ['amount' => $df, 'gst' => $dfWGst], null, 20);
				$createTransaction = $this->appRepo->saveTransaction($dfDebitData);

				$dfCreditData = $this->createTransactionData($user_id, ['amount' => $df, 'gst' => $dfWGst], null, 20, 1);
				$createTransaction = $this->appRepo->saveTransaction($dfCreditData);
			}
	    } else {
	    	die("No offer");
	    }
		die("Done !!!");
	}

	// public function processInvoiceSettlement()
	// {
	// 	$returnData = $this->paySettlement('315');

	// 	echo "Invoice Settled 315";
	// }
        
        /**
         * View Interest Accrual Data
         * 
         * @return type
         */
        public function viewInterestAccrual(Request $request)
        {
            $disbursalId = $request->get('invoice_disbursed_id'); 
			$from = $request->has('from') ? $request->get('from') : null;
			
            $whereCond = [];
            $whereCond['invoice_disbursed_id'] = $disbursalId;
			//$whereCond['interest_date_eq'] = $intAccrualDt;      
			$disbursalData = $this->lmsRepo->getInvoiceDisbursalRequests($whereCond)->first();
			$curr_int_rate = $this->getCurrentInterestRate($disbursalData->interest_rate, $disbursalData->invoice->prgm_offer_id);
			// dd($disbursalData);
			$intAccrualData = $this->lmsRepo->getAccruedInterestData($whereCond);    
            //dd('rrrrrr', $intAccrualData);
                        $prgm_data = AppProgramOffer::find($disbursalData->invoice->prgm_offer_id);
                        $paymentFrequency = $prgm_data ? $prgm_data->payment_frequency : '';
			
			if(isset($from)) {
                return response()->json([
                    'status' => true,
                    'view' => (String)View::make('lms.disbursal.view_interest_accrual',[
                        'data'=> $intAccrualData,'disbursal'=>$disbursalData, 'currentIntRate'=> $curr_int_rate, 'paymentFrequency' => $paymentFrequency
                    ])
                ]); 				
			}
						
            return view('lms.disbursal.view_interest_accrual')->with(['data'=> $intAccrualData,'disbursal'=>$disbursalData, 'currentIntRate'=> $curr_int_rate, 'paymentFrequency' => $paymentFrequency]);
        }

        public function getCurrentInterestRate($intRate, $prgmOfferId){
        	$prgm_data = AppProgramOffer::find($prgmOfferId);
        	$base_rates = BaseRate::where(['bank_id'=> $prgm_data->bank_id, 'is_active'=> 1])->orderBy('id', 'DESC')->first();
        	$bank_base_rate = ($base_rates)? $base_rates->base_rate: 0;
            // $curr_int_rate = $intRate - $prgm_data->base_rate + $bank_base_rate;
            $curr_int_rate = $prgm_data->interest_rate - $prgm_data->base_rate + $bank_base_rate;
        	return $curr_int_rate;
        }


	// public function processInvoiceSettlement()
	// {
	// 	$returnData = $this->paySettlement('315');

	// 	echo "Invoice Settled 315";
	// }
}
