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
use App\Helpers\FinanceHelper;
use App\Inv\Repositories\Contracts\FinanceInterface;
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
	
	public function __construct(InvAppRepoInterface $app_repo, InvUserRepoInterface $user_repo, InvDocumentRepoInterface $doc_repo, InvLmsRepoInterface $lms_repo ,InvMasterRepoInterface $master,FinanceInterface $finRepo){
		$this->appRepo = $app_repo;
		$this->userRepo = $user_repo;
		$this->docRepo = $doc_repo;
		$this->lmsRepo = $lms_repo;
        $this->masterRepo = $master;
        $this->finRepo = $finRepo;
		$this->middleware('checkBackendLeadAccess');
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
	 * Display a listing of the customer.
	 *
	 * @return \Illuminate\Http\Response
	 */
	public function sendToBank(Request $request)
	{
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
			        $tInterest = $this->calInterest($fundedAmount, $invoice['program_offer']['interest_rate']/100, $tenor);

			        if($invoice['program_offer']['payment_frequency'] == 1) {
			            $interest = $tInterest;
			        }

			        $totalInterest += $interest;
			        $totalMargin += $margin;
			        $totalFunded += $fundedAmount;
    				$disburseAmount += round($fundedAmount - $interest, 2);

				}

				if($disburseType == 1) {
					$updateInvoiceStatus = $this->lmsRepo->updateInvoiceStatus($invoice['invoice_id'], 10);
					$requestData[$userid]['RefNo'] = $refId;
					$requestData[$userid]['Amount'] = $disburseAmount;
					$requestData[$userid]['Debit_Acct_No'] = '123344455';
					$requestData[$userid]['Debit_Acct_Name'] = 'testing name';
					$requestData[$userid]['Debit_Mobile'] = '9876543210';
					$requestData[$userid]['Ben_IFSC'] = $invoice['supplier_bank_detail']['ifsc_code'];
					$requestData[$userid]['Ben_Acct_No'] = $invoice['supplier_bank_detail']['acc_no'];
					$requestData[$userid]['Ben_Name'] = $invoice['supplier_bank_detail']['acc_name'];
					$requestData[$userid]['Ben_BankName'] = $invoice['supplier_bank_detail']['bank']['bank_name'];
					$requestData[$userid]['Ben_Email'] = $invoice['supplier']['email'];
					$requestData[$userid]['Ben_Mobile'] = $invoice['supplier']['mobile_no'];
					$requestData[$userid]['Mode_of_Pay'] = 'IFT';
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


			}
			
			if ($disburseAmount) {
				if($disburseType == 2) {
					// disburse transaction $tranType = 16 for payment acc. to mst_trans_type table
					$transactionData = $this->createTransactionData($disburseRequestData['user_id'], ['amount' => $disburseAmount, 'trans_date' => $disburseDate], $transId, 16);
					$createTransaction = $this->lmsRepo->saveTransaction($transactionData);

					
					// interest transaction $tranType = 9 for interest acc. to mst_trans_type table
					$intrstAmt = round($totalInterest,2);
					if ($intrstAmt > 0.00) {
						$intrstTrnsData = $this->createTransactionData($disburseRequestData['user_id'], ['amount' => $intrstAmt, 'trans_date' => $disburseDate], $transId, 9);
						$createTransaction = $this->lmsRepo->saveTransaction($intrstTrnsData);
					}

					// $marginAmt = round($totalMargin,2);
					// if ($marginAmt > 0.00) {
					// 	$marginTrnsData = $this->createTransactionData($disburseRequestData['user_id'], ['amount' => $marginAmt, 'trans_date' => $disburseDate], $transId, 10, 1);
					// 	$createTransaction = $this->lmsRepo->saveTransaction($marginTrnsData);
					// }

					// $intrstTrnsData = $this->createTransactionData($disburseRequestData['user_id'], ['amount' => $intrstAmt, 'trans_date' => $disburseDate], $transId, 9, 1);
					// $createTransaction = $this->lmsRepo->saveTransaction($intrstTrnsData);

				}
			}
		}
		foreach ($allinvoices as $inv_k => $inv_arr) {
			 $finHelperObj = new FinanceHelper($this->finRepo);
        	 $finHelperObj->finExecution(config('common.TRANS_CONFIG_TYPE.DISBURSAL'), $inv_arr['invoice_id'], $inv_arr['app_id'], $inv_arr['supplier_id'], $inv_arr['biz_id']);
		}
		// dd($allrecords);
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
        $returnData = $this->calAccrualInterest();
        foreach($returnData as $disbursal_id => $interest) {
            echo "<br>\nDisbursal ID#{$disbursal_id} -  Accrued Interest {$interest}";
        }
    }

    /**
     * Display a listing of the customer.
     *
     * @return \Illuminate\Http\Response
     */
    public function disbursedList()
    {
        $getAppStatus = ['' => 'Please select'] + $this->masterRepo->getAppStatus(4)->toArray();
        return view('lms.disbursal.disbursed_list')->with(['getAppStatus'=> $getAppStatus]);
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
            $disbursalId = $request->get('disbursal_id');            
            $whereCond = [];
            $whereCond['disbursal_id'] = $disbursalId;
			//$whereCond['interest_date_eq'] = $intAccrualDt;      
			$disbursalData = $this->lmsRepo->getDisbursalRequests($whereCond)->first();          
			
			$intAccrualData = $this->lmsRepo->getAccruedInterestData($whereCond);    
            //dd('rrrrrr', $intAccrualData);
            return view('lms.disbursal.view_interest_accrual')->with(['data'=> $intAccrualData,'disbursal'=>$disbursalData]);
        }


	// public function processInvoiceSettlement()
	// {
	// 	$returnData = $this->paySettlement('315');

	// 	echo "Invoice Settled 315";
	// }
}
