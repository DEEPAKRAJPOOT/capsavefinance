<?php

namespace App\Http\Controllers\Lms;

use Carbon\Carbon;
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
use App\Inv\Repositories\Contracts\Traits\ActivityLogTrait;
use App\Inv\Repositories\Contracts\MasterInterface;

class CustomerController extends Controller {

	use ApplicationTrait;
	use ActivityLogTrait;

	protected $appRepo;
	protected $userRepo;
	protected $docRepo;
	protected $lmsRepo;
	protected $master;

	/**
	 * The pdf instance.
	 *
	 * @var App\Libraries\Pdf
	 */
	protected $pdf;

	public function __construct(InvAppRepoInterface $app_repo, InvUserRepoInterface $user_repo, InvDocumentRepoInterface $doc_repo, InvLmsRepoInterface $lms_repo, MasterInterface $master) {
		$this->appRepo = $app_repo;
		$this->userRepo = $user_repo;
		$this->docRepo = $doc_repo;
		$this->lmsRepo = $lms_repo;
		$this->master = $master;
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
                $userRole = $this->userRepo->getBackendUser(Auth::user()->user_id);
		return view('lms.customer.list_applications')
			->with([
				'userInfo' => $userInfo,
				'application' => $application,
				'anchors' => $anchors,
                                'userRole' => $userRole,
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
 * Display view adhoc document popup
 *
 * @return \Illuminate\Http\Response
 */
public function viewApproveAdhocLimit(Request $request){
	try {
		$data = $request->all();
		$userId = $data['user_id'];
		$app_offer_adhoc_limit_id = $data['app_offer_adhoc_limit_id'];
		$offer_document = $this->lmsRepo->getAdhocDocuments($app_offer_adhoc_limit_id);
		return view('lms.customer.view_adhoc_document')->with(['offer_document'=>$offer_document]);
	} catch (Exception $ex) {
		dd($ex);
	}
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
		$appId = $data->programLimit->appLimit->app_id; 

		$validator = Validator::make($request->all(), [
		   'start_date' => 'required|date_format:"d/m/Y"',
		   'end_date' => 'required|date_format:"d/m/Y"|after:'.$startDate,
		   'doc_file' => 'required',
		   'doc_file.*' => 'mimes:jpeg,jpg,png,pdf',
		],['doc_file.mimes' => 'Invalid file format']);
		
		if ($validator->fails()) {
			Session::flash('error', $validator->messages()->first());
			return redirect()->route('limit_management', ['user_id' => $userId])->withInput();
		}		
		\DB::beginTransaction();
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
			if (!empty($request->remark)){
				$limitData['remark'] = $request->remark;
			}
			
			$createAdhocLimit = $this->appRepo->saveAppOfferAdhocLimit($limitData);
			if ($request->doc_file && $createAdhocLimit) {
				foreach ($request->doc_file as $documentfile){
					$adhocDocUploadData = Helpers::uploadAppAdhocDocFile($documentfile, $userId, $appId, $createAdhocLimit);
					$adhocDocFile = $this->docRepo->saveFile($adhocDocUploadData);
					$docMapData['offer_adhoc_limit_id'] = $createAdhocLimit->app_offer_adhoc_limit_id;
					$docMapData['adhoc_doc_file_id'] = $adhocDocFile->file_id;
					$docMapData['created_at'] = \Carbon\Carbon::now(config('common.timezone'))->format('Y-m-d h:i:s');
					$docMapData['created_by'] = \Auth::user()->user_id;
					$OfferAdhocDocument = $this->docRepo->saveAdhocFile($docMapData);
					if($adhocDocFile) {
						$this->appRepo->saveAppOfferAdhocLimit(['file_id' => $adhocDocFile->file_id], $createAdhocLimit->app_offer_adhoc_limit_id);
					}
				}
			}
		}
		$whereActivi['activity_code'] = 'save_adhoc_limit';
		$activity = $this->master->getActivity($whereActivi);
		if(!empty($activity)) {
			$activity_type_id = isset($activity[0]) ? $activity[0]->id : 0;
			$activity_desc = 'Save Adhoc Limit in Limit Management (Manage Sanction Cases) '. null;
			$arrActivity['app_id'] = null;
			$this->activityLogByTrait($activity_type_id, $activity_desc, response()->json($limitData), $arrActivity);
		}
		\DB::commit(); 
		if($createAdhocLimit) {
			Session::flash('message',trans('success_messages.AdhocLimitCreated'));
			return redirect()->route('limit_management', ['user_id' => $userId]);
		}
	} catch (Exception $ex) {
		\DB::rollback();
		return redirect()->back()->withErrors(Helpers::getExceptionMessage($ex));
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

		$whereActivi['activity_code'] = 'save_approve_adhoc_limit';
		$activity = $this->master->getActivity($whereActivi);
		if(!empty($activity)) {
			$activity_type_id = isset($activity[0]) ? $activity[0]->id : 0;
			$activity_desc = 'Approve Adhoc Limit in Limit Management (Manage Sanction Cases) '. null;
			$arrActivity['app_id'] = null;
			$this->activityLogByTrait($activity_type_id, $activity_desc, response()->json($request->all()), $arrActivity);
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

public function viewAdhocUploadedFile(Request $request){
	try {
		$file_id  = $request->get('file_id');
		$fileData = $this->docRepo->getFileByFileId($file_id);

		$filePath = 'app/public/'.$fileData->file_path;
		$path     = storage_path($filePath);

		if (file_exists($path)) {
			return response()->file($path);
		} else{
			exit('Requested file does not exist on our server!');
		}
	} catch (Exception $ex) {
		return redirect()->back()->withErrors(Helpers::getExceptionMessage($ex));
	}
}

}
