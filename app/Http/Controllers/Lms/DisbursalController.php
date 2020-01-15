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

class DisbursalController extends Controller
{
	use ApplicationTrait;
	use LmsTrait;
        
	protected $appRepo;
	protected $userRepo;
	protected $docRepo;
	protected $lmsRepo;

	/**
	 * The pdf instance.
	 *
	 * @var App\Libraries\Pdf
	 */
	protected $pdf;
	
	public function __construct(InvAppRepoInterface $app_repo, InvUserRepoInterface $user_repo, InvDocumentRepoInterface $doc_repo, InvLmsRepoInterface $lms_repo){
		$this->appRepo = $app_repo;
		$this->userRepo = $user_repo;
		$this->docRepo = $doc_repo;
		$this->lmsRepo = $lms_repo;
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
	public function sendToBank(Request $request)
	{
		$invoiceIds = $request->invoice_ids;
		$record = array_filter(explode(",",$invoiceIds));
		$allinvoices = $this->lmsRepo->getInvoices($record)->toArray();
		$supplierIds = $this->lmsRepo->getInvoiceSupplier($record)->toArray();
		$params = array('http_header' => '', 'header' => '', 'request' => []);
		$http_header = [
			'timestamp' => date('Y-m-d H:i:s'),
    		'txn_id' => _getRand(18)
    		];

    	$header = [
			'Maker_ID' => 10,
    		'Checker_ID' => 11,
    		'Approver_ID' => 12
    		];
		foreach ($supplierIds as $userid) {
			foreach ($allinvoices as $invoice) {
				if($invoice['supplier_id'] = $userid) {
					$fundedAmount = $invoice['invoice_approve_amount'] - (($invoice['invoice_approve_amount']*$invoice['program']['margin'])/100);
					$interest = (($fundedAmount*$invoice['program']['interest_rate']*10)/360);
					$disburseAmount = round($fundedAmount - $interest);
					// dd($disburseAmount);
				}			
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
		
		$params = [
			'http_header' => $http_header,
			'header' => $header,
			'request' => $requestData
			];
		$idfcObj= new Idfc_lib();
		$idfcObj->api_call(Idfc_lib::MULTI_PAYMENT, $params);      
	}

    /**
     * Process Interest Accrual
     *      
     * @return mixed
     */
    public function processAccrualInterest()
    {
        $this->calAccrualInterest();
    }

}