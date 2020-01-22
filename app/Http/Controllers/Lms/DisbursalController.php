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
		$userIvoices = $this->lmsRepo->getAllUserInvoice($userId);

		return view('lms.disbursal.view_invoice')
				->with([
					'userIvoices'=>$userIvoices, 
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
		$disburseType = $request->disburse_type;
		$record = array_filter(explode(",",$invoiceIds));
		
		$allinvoices = $this->lmsRepo->getInvoices($record)->toArray();
		$supplierIds = $this->lmsRepo->getInvoiceSupplier($record)->toArray();
		
		$params = array('http_header' => '', 'header' => '', 'request' => []);
		$fundedAmount = 0;
		$interest = 0;
		$disburseAmount = 0;
		foreach ($supplierIds as $userid) {
			foreach ($allinvoices as $invoice) {
				$disburseRequestData = $this->createInvoiceDisbursalData($invoice, $disburseType);
				$createDisbursal = $this->lmsRepo->saveDisbursalRequest($disburseRequestData);
				
				if($disburseType == 1) {
					$updateInvoiceStatus = $this->lmsRepo->updateInvoiceStatus($invoice['invoice_id'], 10);
					if($invoice['supplier_id'] = $userid) {
						$fundedAmount += $invoice['invoice_approve_amount'] - (($invoice['invoice_approve_amount']*$invoice['program_offer']['margin'])/100);
						$interest += (($fundedAmount*$invoice['program_offer']['interest_rate']*$invoice['program_offer']['tenor'])/360);
						$disburseAmount += round($fundedAmount - $interest);
					}			
					$requestData[$userid]['RefNo'] = 'CAP'.$userid;
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
				}
				else {
					$updateInvoiceStatus = $this->lmsRepo->updateInvoiceStatus($invoice['invoice_id'], 12);
				}
			}
		}
		if($disburseType == 1 && !empty($record)) {
			
			$http_header = [
				'timestamp' => date('Y-m-d H:i:s'),
				'txn_id' => _getRand(18)
				];

			$header = [
				'Maker_ID' => 10,
				'Checker_ID' => 11,
				'Approver_ID' => 12
				];

			$params = [
				'http_header' => $http_header,
				'header' => $header,
				'request' => $requestData
				];

			$idfcObj= new Idfc_lib();
			$result = $idfcObj->api_call(Idfc_lib::MULTI_PAYMENT, $params);
			return redirect()->route('lms_disbursal_request_list')->withErrors($result);      
		} elseif (empty($record)) {
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
        $getAppStatus = ['' => 'Please select'] + $this->masterRepo->getAppStatus()->toArray();
        return view('lms.disbursal.disbursed_list')->with(['getAppStatus'=> $getAppStatus]);
    }

}