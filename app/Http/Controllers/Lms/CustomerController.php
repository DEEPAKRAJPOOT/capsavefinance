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
		$customerTotalLimit = $this->appRepo->getUserTotalLimit($user_id);
		$AvaliablecustomerLimit = $this->appRepo->getAvaliableUserLimit($customerLimit);

		$getUserProgramLimit = $this->appRepo->getUserProgramLimit($user_id);
		
        $getAccountClosure =  $this->appRepo->getAccountActiveClosure($user_id);
		$getAppLimitReview = $this->appRepo->getAppReviewLimit($user_id);
		$getUserActiveProgramLimit = $this->appRepo->getUserActiveProgramLimit($user_id);
		return view('lms.customer.limit_management')
			->with(['userAppLimit' => $getUserProgramLimit,
					'avaliablecustomerLimit' => $AvaliablecustomerLimit,
					'userId' => $user_id,
					'getAccountClosure' => $getAccountClosure,
					'getAppLimitReview' => $getAppLimitReview,
					'customerTotalLimit' => $customerTotalLimit,
					'userActiveAppLimit' => $getUserActiveProgramLimit
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
		$fileId = $request->get('file_id');
        $fileData = $this->docRepo->getFileByFileId($fileId);

        $filePath = 'public/'.$fileData->file_path;
        if (Storage::exists($filePath)) {
            $fileName = time().$fileData->file_name;
            $temp_filepath = tempnam(sys_get_temp_dir(), 'file');
            $file_data = Storage::get($filePath);
            file_put_contents($temp_filepath, $file_data);

            return response()
                ->download($temp_filepath, $fileName, [], 'inline')
                ->deleteFileAfterSend();
        }else{
            exit('Requested file does not exist on our server!');
        }
	} catch (Exception $ex) {
		return redirect()->back()->withErrors(Helpers::getExceptionMessage($ex));
	}
}
/**
 * Display Edit Review Date popup
 *
 * @return \Illuminate\Http\Response
 */
public function editReviewDate(Request $request){
	$userId = $request->get('user_id');
	$appLimitId = $request->get('app_limit_id');
	$whereCond = ['app_limit_id' => $appLimitId, 'user_id' => $userId, 'status' => 1];
	$data = $this->appRepo->getAppLimitData($whereCond);
	$whereCond = ['app_limit_id'=>$appLimitId];
	$AppLimitReview = $this->appRepo->getAppReviewLimitLatestData($whereCond);
	return view('lms.customer.edit_review_date')
		->with([
			'data' => $data,
			'AppLimitReview' => $AppLimitReview
		]);
}

/**
 *  Update Review Date
 *
 * @return \Illuminate\Http\Response
 */
public function updateReviewDate(Request $request){
	try {
		\DB::beginTransaction();
		$arrData = $request->all();
		$appLimitId = (int)$request->app_limit_id;
		$reviewDate = $request->review_date;
		$commentTxt = $request->comment_txt;
		$whereCond = ['app_limit_id' => $appLimitId, 'status' => 1];
		$data = $this->appRepo->getAppLimitData($whereCond);
		$userId = $data[0]->user_id;
		$appId = $data[0]->app_id;
		$startDate = $data[0]->start_date;
		$endDate = $data[0]->end_date;
		$limitExpirationDate = $data[0]->limit_expiration_date;
		if ($data[0]->limit_expiration_date == NULL && empty($data[0]->limit_expiration_date)) {
			$limitExpirationDate = date('Y-m-d', strtotime('+1 years -1 day', strtotime($startDate)));
		}

		$validator = Validator::make($request->all(), [
			'review_date' => 'required|date_format:"d/m/Y"|after_or_equal:' . date('d/m/Y', strtotime($endDate)) . '|before_or_equal:' . date('d/m/Y', strtotime($limitExpirationDate)),
		]);

		if ($validator->fails()) {
			Session::flash('error', $validator->messages()->first());
			return redirect()->route('limit_management', ['user_id' => $userId])->withInput();
		}

		if ($data) {
			$limitReviewData = array(
				'app_limit_id' => $appLimitId,
				'review_date' => (!empty($reviewDate)) ? date("Y-m-d", strtotime(str_replace('/', '-', $reviewDate))) : NULL,
				'comment_txt' => $commentTxt
			);
			$file_id = NULL;
			if ($request->doc_file) {
				$supplier_id = $userId;
				$uploadApprovalDocData = Helpers::uploadAppLimitReviewApprovalFile($arrData, $supplier_id, $appId);
				$userFile = $this->docRepo->saveFile($uploadApprovalDocData);
				$file_id = $userFile->file_id;
			}
			$status = 1;
			if ($file_id) {
				$status = 2;
				if ($data[0]->limit_expiration_date == NULL && empty($data[0]->limit_expiration_date)) {
					$this->appRepo->updateAppLimit(['end_date' => (!empty($reviewDate)) ? date("Y-m-d", strtotime(str_replace('/', '-', $reviewDate))) : NULL, 'limit_expiration_date' => $limitExpirationDate], ['app_limit_id' => $appLimitId]);
				} else {
					$this->appRepo->updateAppLimit(['end_date' => (!empty($reviewDate)) ? date("Y-m-d", strtotime(str_replace('/', '-', $reviewDate))) : NULL], ['app_limit_id' => $appLimitId]);
				}
				$this->appRepo->updatePrgmLimitByLimitId(['end_date' => (!empty($reviewDate)) ? date("Y-m-d", strtotime(str_replace('/', '-', $reviewDate))) : NULL], $appLimitId);
			}
			$limitReviewData['file_id'] = $file_id;
			$limitReviewData['status'] = $status;
			$whereCond = ['app_limit_id'=>$appLimitId,'status'=>1];
			$AppLimitReview = $this->appRepo->getAppReviewLimitLatestData($whereCond);
			if (!$AppLimitReview){
				$limitReviewData['created_by'] = Auth::user()->user_id;
				$limitReviewData['created_at'] = \Carbon\Carbon::now(config('common.timezone'))->format('Y-m-d h:i:s');
				$createAppLimitReview = $this->appRepo->saveAppLimitReview($limitReviewData);
			} else {
				$limitReviewData['updated_by'] = Auth::user()->user_id;
				$limitReviewData['updated_at'] = \Carbon\Carbon::now(config('common.timezone'))->format('Y-m-d h:i:s');
				$createAppLimitReview = $this->appRepo->updateAppLimitReview($limitReviewData,['app_limit_review_id'=>$AppLimitReview->app_limit_review_id]);
			}
		}
		$whereActivi['activity_code'] = 'update_review_date';
		$activity = $this->master->getActivity($whereActivi);
		if (!empty($activity)) {
			$activity_type_id = isset($activity[0]) ? $activity[0]->id : 0;
			$activity_desc = 'Update Review Date in Limit Management (Manage Sanction Cases)';
			$arrActivity['app_id'] = null;
			$this->activityLogByTrait($activity_type_id, $activity_desc, response()->json($limitReviewData), $arrActivity);
		}
		if ($createAppLimitReview) {
			if ($AppLimitReview) {
				Session::flash('message', trans('success_messages.AppLimitReviewUpdated'));
			} else {
				Session::flash('message', trans('success_messages.AppLimitReviewCreated'));
			}
			\DB::commit();
			return redirect()->route('limit_management', ['user_id' => $userId]);
		}
	} catch (Exception $ex) {
		\DB::rollback();
		return redirect()->back()->withErrors(Helpers::getExceptionMessage($ex));
	}
}

}
