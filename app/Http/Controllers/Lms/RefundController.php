<?php
namespace App\Http\Controllers\Lms;

use Auth;
use Session;
use Helpers;

use Illuminate\Http\Request;
use App\Libraries\Idfc_lib;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Storage;
use App\Inv\Repositories\Contracts\Traits\LmsTrait;
use App\Inv\Repositories\Contracts\Traits\ApplicationTrait;

use App\Inv\Repositories\Contracts\LmsInterface as InvLmsRepoInterface;
use App\Inv\Repositories\Contracts\ApplicationInterface as InvAppRepoInterface;

class RefundController extends Controller
{
	use ApplicationTrait;
	use LmsTrait;
        
	protected $appRepo;
	protected $lmsRepo;

	/**
	 * The pdf instance.
	 *
	 * @var App\Libraries\Pdf
	 */
	protected $pdf;
	
	public function __construct(InvAppRepoInterface $app_repo,  InvLmsRepoInterface $lms_repo ){
		$this->appRepo = $app_repo;
		$this->lmsRepo = $lms_repo;
		$this->middleware('checkBackendLeadAccess');
	}
	
	/**
	 * Display a listing of the refund.
	 *
	 * @return \Illuminate\Http\Response
	 */
	public function refundList()
	{
		return view('lms.refund.refund_list');              
	}

	/**
	 * Display a listing of the customer.
	 *
	 * @return \Illuminate\Http\Response
	 */
	public function confirmRefund(Request $request)
	{
		return view('lms.refund.confirm_refund');              
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
				]);              
	}

}