<?php

namespace App\Http\Controllers\Lms;

use App\Http\Controllers\Controller;
use Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use App\Inv\Repositories\Contracts\UserInterface as InvUserRepoInterface;
use App\Inv\Repositories\Contracts\ApplicationInterface as InvAppRepoInterface;
use App\Inv\Repositories\Contracts\DocumentInterface as InvDocumentRepoInterface;
use App\Inv\Repositories\Contracts\LmsInterface as InvLmsRepoInterface;
use Session;
use Helpers;
use App\Inv\Repositories\Contracts\Traits\ApplicationTrait;

class CustomerController extends Controller {

	use ApplicationTrait;

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

	public function __construct(InvAppRepoInterface $app_repo, InvUserRepoInterface $user_repo, InvDocumentRepoInterface $doc_repo, InvLmsRepoInterface $lms_repo) {
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
	public function list()
	{
	return view('lms.customer.list');
}

/**
 * Display a listing of the customer.
 *
 * @return \Illuminate\Http\Response
 */
public function listAppliction(Request $request) {
	try {
		$totalLimit = 0;
		$totalCunsumeLimit = 0;
		$consumeLimit = 0;
		$transactions = 0;
		$user_id = $request->get('user_id');
		$userInfo = $this->userRepo->getCustomerDetail($user_id);
		$application = $this->appRepo->getCustomerApplications($user_id);
		$anchors = $this->appRepo->getCustomerPrgmAnchors($user_id);
	   
		foreach ($application as $key => $app) {
			if (isset($app->prgmLimits)) {
				foreach ($app->prgmLimits as $value) {
					$totalLimit += $value->limit_amt;
				}
			}
			if (isset($app->acceptedOffers)) {
				foreach ($app->acceptedOffers as $value) {
					$totalCunsumeLimit += $value->prgm_limit_amt;
				}
			}
		}
		$userInfo->total_limit = number_format($totalLimit);
		$userInfo->consume_limit = number_format($totalCunsumeLimit);
		$userInfo->utilize_limit = number_format($totalLimit - $totalCunsumeLimit);

		return view('lms.customer.list_applications')
			->with([
				'userInfo' => $userInfo,
				'application' => $application,
				'anchors' => $anchors
		]);
	} catch (Exception $ex) {
		dd($ex);
	}
}

/**
 * Display Limit Management
 *
 * @return \Illuminate\Http\Response
 */
public function limitManagement(Request $request) {
	
	try {
		$user_id = $request->get('user_id');
		$customerLimit = $this->appRepo->getUserLimit($user_id);
		$AvaliablecustomerLimit = $this->appRepo->getAvaliableUserLimit($customerLimit);
		$getUserProgramLimit = $this->appRepo->getUserProgramLimit($user_id);
                $getAccountClosure =  $this->appRepo->getAccountActiveClosure($user_id);
		// dd($getUserProgramLimit);
		return view('lms.customer.limit_management')
			->with(['userAppLimit' => $getUserProgramLimit,
					'avaliablecustomerLimit' => $AvaliablecustomerLimit,
					'userId' => $user_id,
                                        'getAccountClosure' => $getAccountClosure
		]);
	} catch (Exception $ex) {
		dd($ex);
	}
}


/**
 * Display add adhoc popup
 *
 * @return \Illuminate\Http\Response
 */
public function addAdhocLimit(Request $request) {
	$userId = $request->get('user_id');
	$prgmOfferId = $request->get('prgm_offer_id');
	$data = $this->lmsRepo->appLimitByUserId($userId);
	$offer = $this->lmsRepo->appPrgmOfferById($prgmOfferId);

	return view('lms.customer.add_adhoc_limit')
		->with([
			'data' => $data,
			'offer' => $offer
		]);
}

/**
 * Display add adhoc popup
 *
 * @return \Illuminate\Http\Response
 */
public function openApproveAdhocLimit() {

	return view('lms.customer.approve_adhoc_limit');
}

/**
 * Display add adhoc popup
 *
 * @return \Illuminate\Http\Response
 */
public function saveAdhocLimit(Request $request) {

	try {
		$arrFileData = $request->all();
		$prgmOfferId = (int)$request->prgm_offer_id; 
		$startDate = $request->start_date; 
		$endDate = $request->end_date; 
		$adhocLimit = str_replace(',', '', $request->adhoc_limit);

		$data = $this->lmsRepo->appPrgmOfferById($prgmOfferId);
		$userId = $data->programLimit->appLimit->user_id; 

		$validator = Validator::make($request->all(), [
		   'start_date' => 'required|date_format:"d/m/Y"',
		   'end_date' => 'required|date_format:"d/m/Y"|after:'.$startDate,
		]);
		
		if ($validator->fails()) {
			Session::flash('error', $validator->messages()->first());
			return redirect()->route('limit_management', ['user_id' => $userId])->withInput();
		}
		
			
		if($data) {
			$limitData = array(
				'user_id' => $userId,
				'biz_id' => $data->programLimit->biz_id,
				'prgm_offer_id' => $prgmOfferId,
				'limit_amt' => $adhocLimit,
				'status' => config('lms')['STATUS']['PENDING'],
				'limit_type' =>  config('lms')['LIMIT_TYPE']['ADHOC'], 
				'start_date' =>  (!empty($startDate)) ? date("Y-m-d", strtotime(str_replace('/','-',$startDate))) : '', 
				'end_date' =>  (!empty($endDate)) ? date("Y-m-d", strtotime(str_replace('/','-',$endDate))) : '',
				'created_by' => \Auth::user()->user_id,
              	'created_at' => \Carbon\Carbon::now(config('common.timezone'))->format('Y-m-d h:i:s'),
			);
			
			$createAdhocLimit = $this->appRepo->saveAppOfferAdhocLimit($limitData);
		}

		if($createAdhocLimit) {
			Session::flash('message',trans('success_messages.AdhocLimitCreated'));
			return redirect()->route('limit_management', ['user_id' => $userId]);
		} 
	} catch (Exception $ex) {
		dd($ex);
	}
}

/**
 * Approve Adhoc Limit
 *
 * @return \Illuminate\Http\Response
 */
public function approveAdhocLimit(Request $request) {

	try {
		$arrFileData = $request->all();
		$userId = (int)$request->user_id; 
		$app_offer_adhoc_limit_id = $request->app_offer_adhoc_limit_id; 
		$status = $request->status; 

		if($userId) {
			$data = $this->lmsRepo->getUserAdhocLimitById($app_offer_adhoc_limit_id); 
			if($data) {
				$limitData = array(
					'status' => $status,
					'updated_by' => \Auth::user()->user_id,
                  	'updated_at' => \Carbon\Carbon::now(config('common.timezone'))->format('Y-m-d h:i:s'),
				);
				$updateAppLimit = $this->appRepo->saveAppOfferAdhocLimit($limitData, $app_offer_adhoc_limit_id);
			}
		}

		if(isset($updateAppLimit)) {
			Session::flash('message',trans('success_messages.AdhocLimitApproved'));
			return redirect()->route('limit_management', ['user_id' => $userId]);
		} 
	} catch (Exception $ex) {
		dd($ex);
	}
}

/**
 * Display a listing of the invoices.
 *
 * @return \Illuminate\Http\Response
 */
public function listInvoice() {
	return view('lms.customer.list_invoices');
}

}
