<?php
namespace App\Http\Controllers\Backend;
use App\Http\Controllers\Controller;
use Auth;
use Illuminate\Http\Request;
use Carbon\Carbon;
use App\Http\Requests\BusinessInformationRequest;
use App\Http\Requests\PartnerFormRequest;
use App\Http\Requests\DocumentRequest;
use Illuminate\Support\Facades\Storage;
use App\Inv\Repositories\Contracts\UserInterface as InvUserRepoInterface;
use App\Inv\Repositories\Contracts\ApplicationInterface as InvAppRepoInterface;
use App\Inv\Repositories\Contracts\DocumentInterface as InvDocumentRepoInterface;
use App\Inv\Repositories\Contracts\MasterInterface as InvMasterRepoInterface;
use App\Inv\Repositories\Models\Master\State;
use App\Inv\Repositories\Models\BizApiLog;
use App\Inv\Repositories\Models\AppProgramOffer;
use App\Inv\Repositories\Models\User;
use App\Inv\Repositories\Models\UserDetail;
use App\Inv\Repositories\Models\Master\Role;
use App\Libraries\MobileAuth_lib;
use App\Inv\Repositories\Models\BizApi;
use Session;
use Helpers;
use App\Libraries\Pdf;
use App\Inv\Repositories\Contracts\Traits\ApplicationTrait;
use App\Inv\Repositories\Contracts\Traits\LmsTrait;
use App\Inv\Repositories\Models\AppApprover;
use App\Inv\Repositories\Models\AppAssignment;
use Mail;
use App\Helpers\Helper;
use App\Inv\Repositories\Contracts\LmsInterface as InvLmsRepoInterface;
use App\Inv\Repositories\Contracts\Traits\ActivityLogTrait;
use App\Inv\Repositories\Models\Master\LocationType;

class ApplicationController extends Controller
{
	use ApplicationTrait;
	use LmsTrait;
	
	protected $appRepo;
	protected $userRepo;
	protected $docRepo;
	protected $masterRepo;
	protected $lmsRepo;

	use ActivityLogTrait;

	/**
	 * The pdf instance.
	 *
	 * @var App\Libraries\Pdf
	 */
	protected $pdf;
	
	public function __construct(InvAppRepoInterface $app_repo, InvUserRepoInterface $user_repo, InvDocumentRepoInterface $doc_repo, InvLmsRepoInterface $lms_repo, InvMasterRepoInterface $master_repo, Pdf $pdf){
		$this->appRepo = $app_repo;
		$this->userRepo = $user_repo;
		$this->docRepo = $doc_repo;
		$this->masterRepo = $master_repo;
		$this->pdf = $pdf;
		$this->lmsRepo = $lms_repo;
		$this->middleware('checkBackendLeadAccess');
	}
	
	/**
	 * Display a listing of the resource.
	 *
	 * @return \Illuminate\Http\Response
	 */
	public function index()
	{  
            
            $whereCond=[];
            $roleData = $this->userRepo->getBackendUser(\Auth::user()->user_id);
           
            if ($roleData && $roleData->id == 11) {
                $whereCond[] = ['anchor_id', '=', \Auth::user()->anchor_id];                
            }
            $anchUserData = $this->userRepo->getAnchorUserData($whereCond);
            $panList = [];
            foreach($anchUserData as $anchUser) {
                $panList[$anchUser->pan_no] = $anchUser->pan_no . " (". $anchUser->biz_name . ")";
                //$panList[$anchUser->pan_no] = $anchUser->pan_no . " (". $anchUser->name . " " . $anchUser->l_name . ")";
            }
            $appStatusList = \Helpers::getAppStatusList($statusType=1)->toArray(); 
            $appStatusList = ['1'=>'Ready for Renewal','2' => 'Renewed', '3' => 'Limit Enhanced', '4' => 'Limit Reduced']+$appStatusList;
            asort($appStatusList);            
	    return view('backend.app.index')->with('panList', $panList)->with('appStatusList', $appStatusList);              
	}
	
	public function addAppCopy(Request $request)
	{  
		$data['user_id']  = $request->get('user_id');
		$data['app_id']  = $request->get('app_id');
		$data['biz_id']  = $request->get('biz_id');
		return view('backend.app.app_copy')->with(['res' =>$data]);              
	} 

	
	/**
	 * Render view for company detail page according to biz id
	 * 
	 * @param Request $request
	 * @return view
	 */
	public function showCompanyDetails(Request $request){
		try {
			$arrFileData = $request->all();
			$appId = $request->get('app_id');
			$bizId = $request->get('biz_id');
			$userId = $request->get('user_id');
			$product_ids = [];
		   
			$business_info = $this->appRepo->getApplicationById($request->biz_id);
			$app_data = $this->appRepo->getAppDataByBizId($request->biz_id);
			
			if (!empty($app_data->products)) {
				foreach($app_data->products as $product){
					$product_ids[$product->pivot->product_id]= array( 
						"loan_amount" => $product->pivot->loan_amount,
						"tenor_days" => $product->pivot->tenor_days
					);
				}
			}
			
			$states = State::getStateList()->get();
			$locationType = LocationType::getLocationDropDown();
			$product_types = $this->masterRepo->getProductDataList();
			//dd($business_info->gst->pan_gst_hash);
			$industryList = $this->appRepo->getIndustryDropDown()->toArray();
			$constitutionList = $this->appRepo->getConstitutionDropDown()->toArray();
			$segmentList = $this->appRepo->getSegmentDropDown()->toArray();                        
			if ($business_info) {
				return view('backend.app.company_details')
						->with(['business_info'=>$business_info, 'states'=>$states, 'product_ids'=> $product_ids])
						->with('user_id',$userId)
						->with('product_types',$product_types)
						->with('app_id',$appId)
						->with('biz_id',$bizId)
						->with('industryList',$industryList)
						->with('constitutionList',$constitutionList)
						->with('segmentList',$segmentList)
						->with('locationType',$locationType);
			} else {
				return redirect()->back()->withErrors(trans('auth.oops_something_went_wrong'));
			}
		} catch (Exception $ex) {
			return redirect()->back()->withErrors(Helpers::getExceptionMessage($ex));
		}
	}

	/**
	 * Update company detail page according to biz id
	 * 
	 * @param Request $request
	 * @return to promoter detail page
	 */
	public function updateCompanyDetail(BusinessInformationRequest $request){
		try {
			$arrFileData = $request->all();
			if(request()->is_gst_manual == 1){
				$arrFileData['biz_gst_number'] = request()->get('biz_gst_number_text');
			}
			$appId = $request->app_id;
			$bizId = $request->biz_id;
			
			$business_info = $this->appRepo->updateCompanyDetail($arrFileData, $bizId, Auth::user()->user_id);

			if ($business_info) {
                                //Update Anchor Pan and Biz Id
                                $appData = $this->appRepo->getAppData($appId);
                                $arrAnchUser=[];
                                //$arrAnchUser['pan_no'] = $arrFileData['biz_pan_number'];
                                $arrAnchUser['biz_id'] = $bizId;                                     
                                $this->userRepo->updateAnchorUserData($arrAnchUser, ['user_id' => $appData->user_id]); 

            $whereActivi['activity_code'] = 'company_details_save';
            $activity = $this->masterRepo->getActivity($whereActivi);
            if(!empty($activity)) {
                $activity_type_id = isset($activity[0]) ? $activity[0]->id : 0;
                $activity_desc = 'Save Company Details (Business Information) Application Information. AppID '. $appId;
                $arrActivity['app_id'] = $appId;
                $this->activityLogByTrait($activity_type_id, $activity_desc, response()->json($arrFileData), $arrActivity);
            } 								
				Session::flash('message',trans('success_messages.update_company_detail_successfully'));
				return redirect()->route('promoter_details',['app_id' =>  $appId, 'biz_id' => $bizId]);
			} else {
				return redirect()->back()->withInput()->withErrors(trans('auth.oops_something_went_wrong'));
			}
		} catch (Exception $ex) {
			return redirect()->back()->withInput()->withErrors(Helpers::getExceptionMessage($ex));
		}
	}


	 /* Show promoter details page  */
	 public function showPromoterDetails(Request $request){
		try
		{
	   
		$id = Auth::user()->user_id;
		$appId = $request->get('app_id');  
		$bizId = $request->get('biz_id'); 
		$editFlag = $request->get('edit'); 
		$attribute['biz_id'] = $bizId;
		$attribute['app_id'] = $appId;
		$getCin = $this->userRepo->getCinByUserId($bizId);
                $getProductType  =  $this->userRepo->checkLeasingProduct($appId);
		if(!empty($getCin))
		{
			$cin =    $getCin->cin; 
		}
		else
		{
			$cin =    ""; 
		}
		$OwnerPanApi = $this->userRepo->getOwnerApiDetail($attribute);
		return view('backend.app.promoter-details')->with([
			'ownerDetails' => $OwnerPanApi, 
			'cin_no' => $cin,
			'appId' => $appId, 
			'bizId' => $bizId,
			 'edit' => $editFlag,
                         'is_lease' => $getProductType
                        ]);
			 
		} catch (Exception $ex) {
				return false;
		}
	}
	 /**
	 * Save Promoter details form.
	 *
	 * @return \Illuminate\Http\Response
	 */
	//////////////////Save Promoter Multiple Details///////////////////////// 
	public function savePromoter(Request $request) {
	   try {
		  $arrFileData = json_decode($request->getContent(), true);
		  $owner_info = $this->userRepo->saveOwner($arrFileData); //Auth::user()->id
		
		  if ($owner_info) {
				return response()->json(['message' =>trans('success_messages.promoter_saved_successfully'),'status' => 1, 'data' => $owner_info]);
			} else {
			   return response()->json(['message' =>trans('success_messages.oops_something_went_wrong'),'status' => 0]);
			}
		} catch (Exception $ex) {
			return redirect()->back()->withErrors(Helpers::getExceptionMessage($ex));
		}
	}
	
	 /**
	 * Save Promoter details form.
	 *
	 * @return \Illuminate\Http\Response
	 */
	//////////////////Save Promoter Multiple Details///////////////////////// 
	public function updatePromoterDetail(Request $request) {
	   try {
			$arrFileData = $request->all();
			$owner_info = $this->userRepo->updateOwnerInfo($arrFileData); //Auth::user()->id
				  
			if ($owner_info) {
			
				//Add application workflow stages
				$appId = $arrFileData['app_id']; 
				$appData = $this->appRepo->getAppDataByAppId($appId);               
				$userId = $appData ? $appData->user_id : null;     
				$prgmDocsWhere = [];
				$prgmDocsWhere['stage_code'] = 'doc_upload';
				$reqdDocs = $this->createAppRequiredDocs($prgmDocsWhere, $userId, $appId);
			
				$currentStage = \Helpers::getCurrentWfStage($appId);
				if ($currentStage && $currentStage->stage_code == 'promo_detail') {
					$userData = $this->userRepo->getfullUserDetail($userId);
					if ($userData && !empty($userData->anchor_id)) {
						$toUserId = $this->userRepo->getLeadSalesManager($userId);
					} else {
						$toUserId = $this->userRepo->getAssignedSalesManager($userId);
					}

					if ($toUserId) {
					   Helpers::assignAppToUser($toUserId, $appId);
					}                    
				}                
				Helpers::updateWfStage('promo_detail', $appId, $wf_status = 1); 

				$whereActivi['activity_code'] = 'promoter_detail_save';
				$activity = $this->masterRepo->getActivity($whereActivi);
				if(!empty($activity)) {
					$activity_type_id = isset($activity[0]) ? $activity[0]->id : 0;
					$activity_desc = 'Save Promoter Details (Management Information) Application Information. AppID '. $appId;
					$arrActivity['app_id'] = $appId;
					$this->activityLogByTrait($activity_type_id, $activity_desc, response()->json($arrFileData), $arrActivity);
				}				
				
				return response()->json(['message' =>trans('success_messages.promoter_saved_successfully'),'status' => 1]);
			}
			else {
			   //Add application workflow stages 
			   Helpers::updateWfStage('promo_detail', $request->get('app_id'), $wf_status = 2);
			   return response()->json(['message' =>trans('success_messages.oops_something_went_wrong'),'status' => 0]);
			}
		} catch (Exception $ex) {
			//Add application workflow stages
			Helpers::updateWfStage('promo_detail', $request->get('app_id'), $wf_status = 2);
			return redirect()->back()->withErrors(Helpers::getExceptionMessage($ex));
		}
	}
	/** get karza api response     */
	 public function getKarzaApiRes(Request $request)
	  {
			$request  = $request->all();
			$result   = $this->userRepo->getOwnerAppRes($request);
		  if( $result!='')
		  {
			if($result->karza->res_file!='[]')
			{
				return response()->json(['res'=>$result->karza->res_file,'status' =>1,'type' =>$request['type']]); 
			}
			else {
			   return response()->json(['status' =>0]);
			}
		  }
		  else
		  {
			  return response()->json(['status' =>2]);
			  
		  }
		   
	   }
	/**
	 * Handle a Business documents for the application.
	 *
	 * @param  \Illuminate\Http\Request  $request
	 * @return \Illuminate\Http\Response
	 */
	
	public function promoterDocumentSave(Request $request)
	{
		try {
			
			$userId = Auth::user()->user_id;
			$arrFileData = $request->all();
			$docId = $request->get('doc_id'); //  fetch document id
			$appId = $request->get('app_id'); //  fetch document id
			$ownerId = $request->get('owner_id'); //  fetch document id

//            $uploadData = Helpers::uploadAwsBucket($arrFileData, $appId);
			$uploadData = Helpers::uploadAppFile($arrFileData, $appId);
			$userFile = $this->docRepo->saveFile($uploadData);
			if(!empty($userFile->file_id)) {
				$ownerDocCheck = $this->docRepo->appOwnerDocCheck($appId, $docId, $ownerId);
				if(!empty($ownerDocCheck)) {
					$appDocResponse = $this->docRepo->updateAppDocFile($ownerDocCheck, $userFile->file_id);
					$fileId = $appDocResponse->file_id;
					$response = $this->docRepo->getFileByFileId($fileId);
					
				} else {
					$appDocData = Helpers::appDocData($arrFileData, $userFile->file_id);
					$appDocData['is_ovd_enabled'] = 1;
					$appDocResponse = $this->docRepo->saveAppDoc($appDocData);
					$fileId = $appDocResponse->file_id;
					$response = $this->docRepo->getFileByFileId($fileId);
				}   
				
			}
			if ($response) {
				return response()->json([
					'result' => $response, 
					'status' => 1, 
					'file_path' => Storage::url($response->file_path)  
//                    'file_path' => Storage::disk('s3')->url($response->file_path)  
				]);
			} else {
				return response()->json([
					'result' => '', 
					'status' => 0 
				]);
			}
		} catch (Exception $ex) {
			return Helpers::getExceptionMessage($ex);
		}
	}
	
	
	/**
	 * Render view for company detail page according to biz id
	 * 
	 * @param Request $request
	 * @return view
	 */
	public function showDocuments(Request $request){
		try {
			$arrFileData = $request->all();
			$appId = $request->get('app_id');
			$bizId = $request->get('biz_id');
			$editFlag = $request->get('edit');
			$bankdata = User::getBankData();
			$userData = User::getUserByAppId($appId);
			if ($appId > 0) {
				$requiredDocs = $this->docRepo->findRequiredDocs($userData->user_id, $appId);
				if($requiredDocs->count() != 0){
					$docData = $this->docRepo->appDocuments($requiredDocs, $appId);
				}
				else {
					Session::flash('message',trans('error_messages.document'));
					return redirect()->back();
				}
			}
			else {
				return redirect()->back()->withErrors(trans('error_messages.noAppDoucment'));
			}
			if ($docData) {
				return view('backend.app.documents', [
					'requiredDocs' => $requiredDocs,
					'documentData' => $docData,
					'user_id' => $userData->user_id,
					'app_id' => $appId,
					'biz_id' => $bizId,
					'edit' => $editFlag,
					'bankdata' => $bankdata,
				]);
			} else {
				return redirect()->back()->withErrors(trans('auth.oops_something_went_wrong'));
			}
		} catch (Exception $ex) {
			return redirect()->back()->withErrors(Helpers::getExceptionMessage($ex));
		}
	}
	
	 /**
	 * showing form for uploading document
	 */
	
	public function uploadDocument()
	{
		$bankdata = User::getBankData();
		
		return view('backend.app.upload_document', [
					'bankdata' => $bankdata
				]);   
	}
	
	public function editUploadDocument(Request $request)
	{
		$fileId = $request->get('app_doc_file_id');
		$data = $this->docRepo->getAppDocFileById($fileId);

		return view('backend.app.edit_upload_document', [
					'data' => $data
				]);   
	}

	public function ppEditUploadDocument(Request $request)
	{
		$fileId = $request->get('app_doc_file_id');
		$data = $this->docRepo->getAppDocFileById($fileId);

		return view('backend.document.edit_upload_document', [
					'data' => $data
				]);   
	}

	public function updateEditUploadDocument(Request $request)
	{
		$fileId = $request->get('app_doc_file_id');
		$comment = $request->get('comment');
		$appId = (int)$request->app_id; //  fetch app id
		$bizId = (int)$request->biz_id; //  fetch biz id
		$data = ['comment' => $comment ];
		$document_info = $this->docRepo->updateDocument($data, $fileId);

		Session::flash('message',trans('success_messages.documentUpdated'));
		return redirect()->route('documents', ['app_id' => $appId, 'biz_id' => $bizId]);

	}

	public function ppUpdateEditUploadDocument(Request $request)
	{
		$fileId = $request->get('app_doc_file_id');
		$comment = $request->get('comment');
		$appId = (int)$request->app_id; //  fetch app id
		$bizId = (int)$request->biz_id; //  fetch biz id
		$data = ['comment' => $comment ];
		$document_info = $this->docRepo->updateDocument($data, $fileId);

		Session::flash('message',trans('success_messages.documentUpdated'));
		return redirect()->route('pp_document_list', ['app_id' => $appId, 'biz_id' => $bizId]);

	}

	/**
	 * Handle a Business documents for the application.
	 *
	 * @param  \Illuminate\Http\Request  $request
	 * @return \Illuminate\Http\Response
	 */
	
	public function saveDocument(DocumentRequest $request)
	{
		try {
			$arrFileData = $request->all();
			$docId = (int)$request->doc_id; //  fetch document id
			$appId = (int)$request->app_id; //  fetch app id
			$bizId = (int)$request->biz_id; //  fetch biz id
			$userData = $this->userRepo->getUserByAppId($appId);
			$userId = $userData->user_id;

			switch ($docId) {
				case '4':
					$file_bank_id = $arrFileData['file_bank_id'];
					$bankData = State::getBankName($file_bank_id);
					$arrFileData['doc_name'] = $bankData['bank_name'] ?? NULL;
					$arrFileData['finc_year'] = NULL;
					$arrFileData['gst_month'] = $arrFileData['bank_month'];
					$arrFileData['gst_year'] = $arrFileData['bank_year'];
					$arrFileData['pwd_txt'] = $arrFileData['is_pwd_protected'] ? $arrFileData['pwd_txt'] :NULL;
					$arrFileData['facility'] = $arrFileData['facility'] ?? 'NONE';
					$arrFileData['sanctionlimitfixed'] = $arrFileData['sanctionlimitfixed'] ?? '0';;
					$arrFileData['drawingpowervariableamount'] = $arrFileData['drawingpowervariableamount'] ?? '0';
					$arrFileData['sanctionlimitvariableamount'] = $arrFileData['sanctionlimitvariableamount'] ?? '0';
					break;
				case '5':
					$arrFileData['file_bank_id'] = NULL;
					$arrFileData['gst_month'] = NULL;
					$arrFileData['gst_year'] = NULL;
					$arrFileData['pwd_txt'] = $arrFileData['is_pwd_protected'] ? $arrFileData['pwd_txt'] :NULL;
					break;

				case '6':
					$arrFileData['file_bank_id'] = NULL;
					$arrFileData['finc_year']    = NULL;
					$arrFileData['is_pwd_protected'] = NULL;
					$arrFileData['is_scanned'] = NULL;
					$arrFileData['pwd_txt'] = NULL;
					break;
				
				default:
					//$arrFileData = "Invalid Doc ID";
					$arrFileData['file_bank_id'] = NULL;
					$arrFileData['finc_year']    = NULL;
					$arrFileData['is_pwd_protected'] = NULL;
					$arrFileData['is_scanned'] = NULL;
					$arrFileData['pwd_txt'] = NULL;                                        
					break;
			}

                        $wfStage = Helpers::getWfDetailById('doc_upload', $userId, $appId);
                        if ($wfStage && $wfStage->app_wf_status != 1 ) {                        
                            $wf_status = 2;                
                            Helpers::updateWfStage('doc_upload', $appId, $wf_status);
			}
			$document_info = $this->docRepo->saveDocument($arrFileData, $docId, $userId);
			if ($document_info) {
				//Add/Update application workflow stages    
				//$response = $this->docRepo->isUploadedCheck($userId, $appId);            
				//$wf_status = $response->count() < 1 ? 1 : 2;
				//$wf_status = 1;                
				//Helpers::updateWfStage('doc_upload', $appId, $wf_status);

				$whereActivi['activity_code'] = 'document_save';
				$activity = $this->masterRepo->getActivity($whereActivi);
				if(!empty($activity)) {
					$activity_type_id = isset($activity[0]) ? $activity[0]->id : 0;
					$activity_desc = 'Save Documents (Documents) Application Information. AppID '. $appId;
					$arrActivity['app_id'] = $appId;
					$this->activityLogByTrait($activity_type_id, $activity_desc, response()->json($arrFileData), $arrActivity);
				}				
				
				Session::flash('message',trans('success_messages.uploaded'));
				return redirect()->route('documents', ['app_id' => $appId, 'biz_id' => $bizId]);
			} else {
				//Add application workflow stages
				//Helpers::updateWfStage('doc_upload', $appId, $wf_status=2);
				//return redirect()->route('documents', ['app_id' => $appId, 'biz_id' => $bizId]);
			}
		} catch (Exception $ex) {
			//Add application workflow stages
			//Helpers::updateWfStage('doc_upload', $request->get('appId'), $wf_status=2);
				
			return redirect()->route('documents', ['app_id' => $appId, 'biz_id' => $bizId])->withErrors(Helpers::getExceptionMessage($ex));
		}
	}
	
	 /**
	 * Handling deleting documents file for the application.
	 *
	 * @param  \Illuminate\Http\Request  $request
	 * @return \Illuminate\Http\Response
	 */
	public function documentDelete(Request $request)
	{
            try {
                    $appDocFileId = $request->get('app_doc_file_id');
                    $response = $this->docRepo->deleteDocument($appDocFileId);
                    if ($response) {
                            Session::flash('message',trans('success_messages.deleted'));
                            return redirect()->back();
                    } else {
                            return redirect()->back()->withErrors(trans('auth.oops_something_went_wrong'));
                    }
            } catch (Exception $ex) {
                    return redirect()->back()->withErrors(Helpers::getExceptionMessage($ex));
            }
	}
	
	/**
	 * Handling deleting documents file for the application.
	 *
	 * @param  \Illuminate\Http\Request  $request
	 * @return \Illuminate\Http\Response
	 */
	
	public function applicationSave(Request $request)
	{
		try {
			$appId  = (int)$request->app_id;
			$userData = User::getUserByAppId($appId);
			$userId = $userData->user_id;
			// $response = $this->docRepo->isUploadedCheck($userId, $appId);
			
			// if ($response->count() < 1) {
				$appData = $this->appRepo->getAppData($appId);
                                $curStatus = $appData ? $appData->curr_status_id : 0;                        
                                $currentStage = Helpers::getCurrentWfStage($appId);
                                $appStatusList = [
                                    config('common.mst_status_id.APP_REJECTED'),
                                    config('common.mst_status_id.APP_CANCEL'),
                                    config('common.mst_status_id.APP_HOLD'),
                                    config('common.mst_status_id.APP_DATA_PENDING')
                                ];
                                if ($currentStage && $currentStage->order_no < 4 && !in_array($curStatus, $appStatusList) ) {                                  
                                    $this->appRepo->updateAppData($appId, ['status' => 1]);
                                    Helpers::updateAppCurrentStatus($appId, config('common.mst_status_id.COMPLETED'));
                                    
                                    $curDate = \Carbon\Carbon::now()->format('Y-m-d');
                                    $endDate = date('Y-m-d', strtotime('+1 years -1 day'));
                                    //$appLimitId = $this->appRepo->getAppLimitIdByUserIdAppId($userId, $appId);

                                    if ($appData && in_array($appData->app_type, [1,2,3]) ) {
                                        $parentAppId = $appData->parent_app_id;
                                        $actualEndDate = $curDate;
                                        $appLimitData = $this->appRepo->getAppLimitData(['user_id' => $userId, 'app_id' => $parentAppId]);
                                        if (in_array($appData->app_type, [2,3])) {
                                           $curDate = isset($appLimitData[0]) ? $appLimitData[0]->start_date : null;
                                           $endDate = isset($appLimitData[0]) ? $appLimitData[0]->end_date : null;
                                        }
                                       $this->appRepo->updateAppData($parentAppId, ['status' => 3, 'is_child_sanctioned' => 1]);
                                       $this->appRepo->updateAppLimit(['status' => 2, 'actual_end_date' => $actualEndDate], ['app_id' => $parentAppId]);
                                       $this->appRepo->updatePrgmLimit(['status' => 2, 'actual_end_date' => $actualEndDate], ['app_id' => $parentAppId, 'product_id' => 1]);  
                                       \Helpers::updateAppCurrentStatus($parentAppId, config('common.mst_status_id.APP_CLOSED'));
                                    }            
                                    /*
                                    if (!is_null($appLimitId)) {
                                        $this->appRepo->saveAppLimit([
                                                'status' => 1,
                                                'start_date' => $curDate,
                                                'end_date' => $endDate], $appLimitId);
                                        $this->appRepo->updatePrgmLimitByLimitId([
                                                'status' => 1,
                                                'start_date' => $curDate,
                                                'end_date' => $endDate], $appLimitId);
                                    }
                                     * 
                                     */                                      
                                }				  
				Helpers::updateWfStage('doc_upload', $appId, $wf_status = 1);
			 
				//Add application workflow stages                
				Helpers::updateWfStage('app_submitted', $appId, $wf_status = 1);				
                                
				//Update workflow stage
				//$currentStage = Helpers::getCurrentWfStage($app_id);            
				//$curr_wf_stage_code = $currentStage ? $currentStage->stage_code : null;
				//Helpers::updateWfStage($curr_wf_stage_code, $appId, $wf_status = 1);
			
				//Insert Pre Offer Documents
				$prgmDocsWhere = [];
				$prgmDocsWhere['stage_code'] = 'pre_offer';
				//$appData = $this->appRepo->getAppDataByAppId($appId);
				//$userId = $appData ? $appData->user_id : null;
				$reqdDocs = $this->createAppRequiredDocs($prgmDocsWhere, $userId, $appId);
			                                                                                              
				return redirect()->route('application_list')->with('message', trans('success_messages.app.saved'));
			// } else {
			//     //Add application workflow stages                
			//     Helpers::updateWfStage('app_submitted', $request->get('app_id'), $wf_status = 2);
				
			//     return redirect()->back()->withErrors(trans('error_messages.app.incomplete'));
			// }
		} catch (Exception $ex) {
			//Add application workflow stages                
			Helpers::updateWfStage('app_submitted', $request->get('app_id'), $wf_status = 2);
				
			return redirect()->back()->withErrors(Helpers::getExceptionMessage($ex));
		}
	}
	
	/**
	 * Render view for change application status
	 * 
	 * @param Request $request
	 * @return view
	 */
	public function changeAppStatus(Request $request) {
		$app_id = $request->get('app_id');
		$biz_id = $request->get('biz_id');
		$appStatus = [''=>'Select status', '1'=>'Completed','0'=> 'Pending', '2' => 'Onhold'];
		
		return view('backend.app.change_app_status')
				->with('app_id', $app_id)
				->with('biz_id', $biz_id)
				->with('appStatus', $appStatus);
	}
 
	/**
	 * Render view for assign case
	 * 
	 * @param Request $request
	 * @return view
	 */    
	public function assignCase(Request $request) {
		$app_id = $request->get('app_id');
		$biz_id = $request->get('biz_id');
		$assignee = [
			'' => 'Select assignee',
			'1' => 'credit manager 1',
			'2' => 'credit manager 2',
			'3' => 'credit manager 3',
		];
		return view('backend.app.assign_case')
				  ->with('app_id', $app_id)
				  ->with('biz_id', $biz_id)
				  ->with('assignee', $assignee);
	}  
	
	/**
	 * Update application status
	 * 
	 * @param Request $request
	 * @return view
	 */    
	public function updateAppStatus(Request $request) {
		try {
			$app_id = $request->get('app_id');
			$biz_id = $request->get('biz_id');
			$app_status = $request->get('app_status');
			$updateData = ['app_status' => $app_status];
			
			$this->appRepo->updateAppStatus($app_id, $updateData);
			Session::flash('message',trans('backend_messages.change_app_status'));
			//return redirect()->route('company_details', ['app_id' => $app_id, 'biz_id' => $biz_id]);
			return redirect()->route('application_list');
		} catch (Exception $ex ) {
			return redirect()->back()->withErrors(Helpers::getExceptionMessage($ex));
		}
	}
	
	/**
	 * Save assign case
	 * 
	 * @param Request $request
	 * @return view
	 */    
	public function updateAssignee(Request $request) {
		try {
			$app_id = $request->get('app_id');
			$biz_id = $request->get('biz_id');
			$app_status = $request->get('assignee');
			$updateData = ['app_status' => $app_status];
			
			$this->appRepo->updateAssignee($app_id, $updateData);
			Session::flash('message',trans('backend_messages.update_assignee'));
			//return redirect()->route('company_details', ['app_id' => $app_id, 'biz_id' => $biz_id]);
			return redirect()->route('application_list');
		} catch (Exception $ex) {
			return redirect()->back()->withErrors(Helpers::getExceptionMessage($ex));
		}
	}
	
	/**
	 * Render view for add application note
	 * 
	 * @param Request $request
	 * @return view
	 */
	public function addAppNote(Request $request) {
		$app_id = $request->get('app_id');
		$biz_id = $request->get('biz_id');        
		
		return view('backend.app.add_app_note')
				->with('app_id', $app_id)
				->with('biz_id', $biz_id);
	} 
	
	/**
	 * Save application note
	 * 
	 * @param Request $request
	 * @return view
	 */    
	public function saveAppNote(Request $request) {
		
		try {
			$app_id = $request->get('app_id');
			$biz_id = $request->get('biz_id');
			$notes = $request->get('notes');
			$noteData = [
				'app_id' => $app_id, 
				'note_data' => $notes,
				'created_at' => \Carbon\Carbon::now(),
				'created_by' => \Auth::user()->user_id
			];
			
			$this->appRepo->saveAppNote($noteData);

            $whereActivi['activity_code'] = 'save_app_note';
            $activity = $this->masterRepo->getActivity($whereActivi);
            if(!empty($activity)) {
                $activity_type_id = isset($activity[0]) ? $activity[0]->id : 0;
                $activity_desc = 'Add App Note in action tap of My Application in Manage Application. AppID '. $app_id;
                $arrActivity['app_id'] = $app_id;
                $this->activityLogByTrait($activity_type_id, $activity_desc, response()->json($noteData), $arrActivity);
            } 			
			
			Session::flash('message',trans('backend_messages.add_note'));
			//return redirect()->route('company_details', ['app_id' => $app_id, 'biz_id' => $biz_id]);
			return redirect()->route('application_list');
		} catch (Exception $ex) {
			return redirect()->back()->withErrors(Helpers::getExceptionMessage($ex));
		}
	} 
	
	/**
	 * Save application note
	 * 
	 * @param Request $request
	 * @return view
	 */    
	public function sendCaseConfirmbox(Request $request) {
		try{
                                 
			$user_id = $request->get('user_id');
			$app_id = $request->get('app_id');
                        $approvers = Helpers::getProductWiseDoAUsersByAppId($app_id);
			$assign_case = $request->has('assign_case') ? $request->get('assign_case') : 0;
			
			$currentStage = Helpers::getCurrentWfStage($app_id);            
			$curr_role_id = $currentStage ? $currentStage->role_id : null;
			
			//$last_completed_wf_stage = WfAppStage::getCurrentWfStage($app_id);
			$wf_order_no = $currentStage ? $currentStage->order_no : null;
			$nextStage = Helpers::getNextWfStage($wf_order_no);  
			$next_role_id = $nextStage ? $nextStage->role_id : null;
			$roleDropDown = [];
                        
			if ($assign_case && $currentStage) {
				$rolesArr = explode(',', $currentStage->assign_role);
				//$roles = $this->userRepo->getRoleByArray($rolesArr);
				$roles = $this->appRepo->getBackStageUsers($app_id, $rolesArr);
				
				foreach($roles as $role) {
					$roleDropDown[$role->id . '-' . $role->user_id] = $role->assignee_role . ' (' . $role->assignee. ')';
				}
			} else {
				$roleDropDown = $this->userRepo->getAllRole()->toArray();
			}
			$appData = $this->appRepo->getAppData($app_id);
                        
			return view('backend.app.next_stage_confirmBox')
				->with('app_id', $app_id)
				->with('biz_id', $appData->biz_id)
				->with('roles', $roleDropDown)
				->with('user_id', $user_id)
				->with('assign_case', $assign_case)    
				->with('curr_role_id', $curr_role_id)
				->with('next_role_id', $next_role_id)
				->with('biz_id', $appData->biz_id)
                                ->with('approvers',$approvers)
                                ->with('nextStage', $nextStage);
		} catch (Exception $ex) {
			return redirect()->back()->withErrors(Helpers::getExceptionMessage($ex));
		}
	}
	/**
	 * Save application note
	 * 
	 * @param Request $request
	 * @return view
	 */    
	public function AcceptNextStage(Request $request) {
                          
		try{    
                   
                       $approver_list = $request->get('approver_list');
                        $user_id = $request->get('user_id');
			$app_id = $request->get('app_id');
                        $approvers = Helpers::getProductWiseDoAUsersByAppId($app_id);
                        $sel_assign_role = $request->get('sel_assign_role');
			$assign_case = $request->get('assign_case');
			$sharing_comment = $request->get('sharing_comment');
			$curr_role_id = $request->get('curr_role_id');
			$movedInLms = false;
						
			$addl_data = [];
			$addl_data['sharing_comment'] = $sharing_comment;

			if ($curr_role_id && $assign_case) {
				$currStage = Helpers::getCurrentWfStage($app_id);
				$selData = explode('-', $sel_assign_role);
				$selRoleId = $selData[0];
				$selUserId = $selData[1];				
				$currStage = Helpers::getCurrentWfStage($app_id);
				$selRoleStage = Helpers::getCurrentWfStagebyRole($selRoleId, $user_journey=2, $wf_start_order_no=$currStage->order_no, $orderBy='DESC');
				Helpers::updateWfStageManual($app_id, $selRoleStage->order_no, $currStage->order_no, $wf_status = 2, $selUserId, $addl_data);
			} else {
				$currStage = Helpers::getCurrentWfStage($app_id);
                               
				//Validate the stage
				if ($currStage->stage_code == 'credit_mgr') {
					$whereCondition = ['app_id' => $app_id, 'status_is_null_or_accepted' =>1];                                        
					$offerData = $this->appRepo->getOfferData($whereCondition);
					if (!$offerData) {
						Session::flash('error_code', 'no_offer_found');
						return redirect()->back();
					}
                                        
                                        $appData = $this->appRepo->getAppData($app_id);
                                        if ($appData && in_array($appData->curr_status_id, [config('common.mst_status_id.OFFER_LIMIT_REJECTED')]) ) {
                                            Session::flash('error_code', 'limit_rejected');
                                            return redirect()->back();
                                        }                                        
				} else if ($currStage->stage_code == 'approver') {
					$whereCondition = ['app_id' => $app_id, 'status' => null];
					$offerData = $this->appRepo->getOfferData($whereCondition);
					if (!$offerData) {
						Session::flash('error_code', 'no_offer_found');
						return redirect()->back();
					} else {
						$isAppApprByAuthority = Helpers::isAppApprByAuthority($app_id);
						if (!$isAppApprByAuthority) {
							Session::flash('error_code', 'no_approved');
							return redirect()->back();
						} else {
							//$whereCondition = ['app_id' => $app_id];
							//$offerData = $this->appRepo->getOfferData($whereCondition);
							$this->appRepo->updateActiveOfferByAppId($app_id, ['is_approve' => 1]);
						}
					}
				} else if ($currStage->stage_code == 'sales_queue') {
					$whereCondition = ['app_id' => $app_id, 'status' => null];
					$offerData = $this->appRepo->getOfferData($whereCondition);
					if ($offerData && is_null($offerData->status) ) {                                            
						Session::flash('error_code', 'no_offer_accepted');
						return redirect()->back();
					}  
				} else if ($currStage->stage_code == 'upload_post_sanction_doc') {
					
					$requiredDocs = $this->getProgramDocs(['app_id'=> $app_id, 'stage_code' => 'upload_post_sanction_doc']);
					$docIds = [];
					foreach($requiredDocs as $doc) {
						$docIds[] = $doc['doc_id'];
					}
					$uploadDocStatus = $this->appRepo->isDocsUploaded($app_id, $docIds);                    
					if(count($docIds) == 0 || !$uploadDocStatus)  {                    
						Session::flash('error_code', 'no_post_docs_uploaded');
						return redirect()->back();                                            
					}                                  
				} else if ($currStage->stage_code == 'upload_pre_sanction_doc') {
					
					$requiredDocs = $this->getProgramDocs(['app_id'=> $app_id, 'stage_code' => 'upload_pre_sanction_doc']);                    
					$docIds = [];
					foreach($requiredDocs as $doc) {
						$docIds[] = $doc['doc_id'];
					}
					$uploadDocStatus = $this->appRepo->isDocsUploaded($app_id, $docIds);                    
					if(count($docIds) == 0 || !$uploadDocStatus)  {                    
						Session::flash('error_code', 'no_pre_docs_uploaded');
						return redirect()->back();                                            
					}
				} else if ($currStage->stage_code == 'opps_checker') {
				  $capId = sprintf('%09d', $user_id);
				  $customerId = 'CAP'.$capId;
				  $lmsCustomerArray = array(
					'user_id' => $user_id, 
					'customer_id' => $customerId,
					'app_id' => $app_id, 
                    'created_by' => Auth::user()->user_id,
                    'created_at' => \carbon\Carbon::now()
				  );
			  	$curDate = \Carbon\Carbon::now()->format('Y-m-d');
			  	$endDate = date('Y-m-d', strtotime('+1 years -1 day'));
			  	$appLimitId = $this->appRepo->getAppLimitIdByUserIdAppId($user_id, $app_id);
                                $appData = $this->appRepo->getAppData($app_id);
                                if ($appData && in_array($appData->app_type, [1,2,3]) ) {
                                    $parentAppId = $appData->parent_app_id;
                                    $actualEndDate = $curDate;
                                    /*$appLimitData = $this->appRepo->getAppLimitData(['user_id' => $user_id, 'app_id' => $parentAppId]);
                                    if (in_array($appData->app_type, [2,3])) {
                                        $curDate = isset($appLimitData[0]) ? $appLimitData[0]->start_date : null;
                                        $endDate = isset($appLimitData[0]) ? $appLimitData[0]->end_date : null;
                                    }
                                    
                                    $this->appRepo->updateAppLimit(['status' => 2, 'actual_end_date' => $actualEndDate], ['app_id' => $parentAppId]);
                                    $this->appRepo->updatePrgmLimit(['status' => 2, 'actual_end_date' => $actualEndDate], ['app_id' => $parentAppId, 'product_id' => 1]);  
                                    \Helpers::updateAppCurrentStatus($parentAppId, config('common.mst_status_id.APP_CLOSED'));*/                                    
                                    $this->appRepo->updateAppData($parentAppId, ['is_child_sanctioned' => 2]);
                                }
                                
        		if (!is_null($appLimitId)) {
				  	$this->appRepo->saveAppLimit([
				  		'status' => 1,
				  		'start_date' => $curDate,
				  		'end_date' => $endDate], $appLimitId);
				  	$this->appRepo->updatePrgmLimitByLimitId([
				  		'status' => 1,
				  		'start_date' => $curDate,
				  		'end_date' => $endDate], $appLimitId);
			  	}			  	
			  	$createCustomer = $this->appRepo->createCustomerId($lmsCustomerArray);
                                UserDetail::where('user_id',$user_id)->update(['is_active' =>1]);
                                $this->appRepo->updateAppDetails($app_id, ['status' => 2]); //Mark Sanction  
                                \Helpers::updateAppCurrentStatus($app_id, config('common.mst_status_id.APP_SANCTIONED'));
                                
                                $prcsAmt = $this->appRepo->getPrgmLimitByAppId($app_id);
                                if($prcsAmt && isset($prcsAmt->offer)) {
				  if($createCustomer != null) {   
                                      $whereCond=[];
                                      $whereCond['user_id'] = $user_id;
                                      $lmsData = $this->appRepo->getLmsUsers($whereCond);
                                      if (isset($lmsData[0]) && !empty($lmsData[0]->virtual_acc_id)) {
                                        $virtualId =  $lmsData[0]->virtual_acc_id;
                                      } else {
					$capId = sprintf('%07d', $createCustomer->lms_user_id);
					$virtualId = 'CAPVA'.$capId;					
                                      }
                                      $createCustomerId = $this->appRepo->createVirtualId($createCustomer, $virtualId);
					//$prcsAmt = $this->appRepo->getPrgmLimitByAppId($app_id);
					$userStateId = $this->appRepo->getUserAddress($app_id);
					$companyStateId = $this->appRepo->companyAdress();
					//if(isset($prcsAmt->offer)) {
					  foreach ($prcsAmt->offer as $key => $offer) {
						$offer_charges = AppProgramOffer::getProgramOfferByAppId($app_id, $offer->prgm_offer_id);
						if (empty($offer_charges))
						  continue;
						foreach ($offer_charges as $key => $chrgs) {
						  $ChargeMasterData = $this->appRepo->getTransTypeDataByChargeId($chrgs->charge_id);
						  $ChargeId = (int) $ChargeMasterData->id;
						  $PrgmChrg = $this->appRepo->getPrgmChrgeData($offer->prgm_id, $ChargeMasterData->chrg_master_id);
						  
						  $pf_amt = round((($offer->prgm_limit_amt * $chrgs->chrg_value)/100),2);
						 
						  if($chrgs->chrg_type == 1)
						  $pf_amt = $chrgs->chrg_value;

						  $fData = [];
						  $fData['amount'] = $pf_amt;
						  $getPercentage  = $this->masterRepo->getLastGSTRecord();
							if($getPercentage)
							{
								$tax_value  = $getPercentage['tax_value'];
								$chid  = $getPercentage['tax_id'];
							}
							else
							{
								$tax_value  =0; 
								$chid  = 0;
							}
						  if(isset($PrgmChrg->is_gst_applicable) && $PrgmChrg->is_gst_applicable == 1 ) {
							if($userStateId == $companyStateId) {
							  $fWGst = round((($pf_amt*$tax_value)/100),2);
							  $fData['gst'] = $PrgmChrg->is_gst_applicable;
							  $fData['igst'] = 0; //$fWGst
							  $fData['amount'] += $fWGst;
							  $fData['base_amt'] = $pf_amt;
							  $fData['gst_amt']  = $tax_value;
							  $fData['chrg_gst_id']  = $chid;
							  $fData['trans_mode']  = 1;

							} else {
							  $fWGst = round((($pf_amt*$tax_value)/100),2);
							  $fData['gst'] = $PrgmChrg->is_gst_applicable;
							  $fData['cgst'] = 0; //$fWGst
							  $fData['sgst'] = 0; //$fWGst;
							  $totalGst = $fWGst + $fWGst; //$fData['cgst'] + $fData['sgst'];
							  $fData['amount'] += $fWGst;
							  $fData['base_amt'] = $pf_amt;
							  $fData['gst_amt']  = $tax_value;
							  $fData['chrg_gst_id']  = $chid;
							  $fData['trans_mode']  = 1;
							}
						  }
						  	if ($fData['amount'] > 0.00) {

							  	$fDebitData = $this->createTransactionData($user_id, $fData, $ChargeId, 0);
								$fDebitCreate = $this->appRepo->saveTransaction($fDebitData);
								$id  = Auth::user()->user_id;
								$mytime = Carbon::now();    
								$arr  = [   
									'app_id'=> $app_id,
									"prgm_id" => $offer->prgm_id,
									'trans_id' => $fDebitCreate->trans_id,
									"chrg_master_id" => $chrgs->charge_id,
									"percent" => $chrgs->chrg_value,
									"chrg_applicable_id" =>  $chrgs->chrg_applicable_id, 
									"amount" =>   $fData['amount'],
									"virtual_acc_id" =>  $this->lmsRepo->getVirtualAccIdByUserId($user_id),
									'created_by' =>  $id,
									'created_at' =>  $mytime ];
								$chrgTransId =   $this->lmsRepo->saveChargeTrans($arr);
							}
						}
					  }
					//}
				  }
                                  $movedInLms=true;
                                  }
				}
                               
				$wf_order_no = $currStage->order_no;
				$nextStage = Helpers::getNextWfStage($wf_order_no);
				$roleArr = [$nextStage->role_id];
                                $roles = $this->appRepo->getBackStageUsers($app_id, $roleArr);
                                $addl_data['to_id'] = isset($roles[0]) ? $roles[0]->user_id : null;
                                $assign = true;
                                $wf_status = 1;

				if ($nextStage->stage_code == 'approver') {
                                    $whereCondition = ['app_id' => $app_id, 'is_approve' => 1];
                                    $offerData = $this->appRepo->getOfferData($whereCondition);

                                    if (!$offerData) {                                        
					$apprAuthUsers = Helpers::saveApprAuthorityUsers($app_id,$approver_list);
                                	if (count($apprAuthUsers) == 0) {
						Session::flash('error_code', 'no_approval_users_found');
						return redirect()->back();                           
					}                    
					foreach($apprAuthUsers as $approver) {
                                             if(in_array($approver->user_id,$approver_list))
                                             {                                
						$appAssignData = [
							'app_id' => $app_id,
							'to_id' => $approver->user_id,
							'assigned_user_id' => $user_id,
							'sharing_comment' => $addl_data['sharing_comment'],
						];
						Helpers::assignAppUser($appAssignData);
                                            }
					}
					$assign = false;
					$wf_status = 1;
                                    }
				}

				if ($nextStage->stage_code == 'upload_post_sanction_doc') {
					$prgmDocsWhere = [];
					$prgmDocsWhere['stage_code'] = 'upload_post_sanction_doc';
					$reqdDocs = $this->createAppRequiredDocs($prgmDocsWhere, $user_id, $app_id);
					if(count($reqdDocs) == 0)  {
						Session::flash('error_code', 'no_docs_found');
						return redirect()->back();                                            
					}                    
				} 
				
				Helpers::updateWfStage($currStage->stage_code, $app_id, $wf_status, $assign, $addl_data);
                                if ($movedInLms) {
                                    //Helpers::updateCurrentWfStage('disbursed_or_in_lms', $app_id, $wf_status=1);
                                }
			}


			$application = $this->appRepo->updateAppDetails($app_id, ['is_assigned'=>1]);                                                                         
			Session::flash('is_accept', 1);
			return redirect()->back();
		   
			//return redirect()->route('company_details', ['app_id' => $app_id, 'biz_id' => $biz_id]);
		   
		} catch (Exception $ex) {
			return redirect()->back()->withErrors(Helpers::getExceptionMessage($ex));
		}
	}

	/**
	 * Show the business information form.
	 *
	 * @return \Illuminate\Http\Response
	 */
	public function showBusinessInformation()
	{       
            $userId = request()->get('user_id');
            //$where=[];
            //$where['user_id'] = $userId;
            //$where['status'] = [0,1];
            //$appData = $this->appRepo->getApplicationsData($where);
            $appData = $this->appRepo->checkAppByPan($userId);            
            $userData = $this->userRepo->getfullUserDetail($userId);           
            $isAnchorLead = $userData && !empty($userData->anchor_id);
            
            //if (isset($appData[0])) {
            if ($appData) {                
                Session::flash('message', trans('error_messages.active_app_check'));   
                return redirect()->back();
            }
            
		$states = State::getStateList()->get();
		$product_types = $this->masterRepo->getProductDataList();
		$industryList = $this->appRepo->getIndustryDropDown()->toArray();
		$constitutionList = $this->appRepo->getConstitutionDropDown()->toArray();
		$segmentList = $this->appRepo->getSegmentDropDown()->toArray();

                $anchUserData = $this->userRepo->getAnchorUserData(['user_id' => $userId]);        
                $pan = isset($anchUserData[0]) ? $anchUserData[0]->pan_no : '';
        $locationType = LocationType::getLocationDropDown();
		return view('backend.app.business_information',compact(['states', 'product_types','industryList','constitutionList', 'segmentList', 'pan', 'locationType']));
	}

	/**
	 * 
	 */

	public function saveBusinessInformation(BusinessInformationRequest $request)
	{
		try {

			$arrFileData = $request->all();

                        $whereCond=[];
                        //$whereCond[] = ['anchor_id', '=', \Auth::user()->anchor_id];      
                        $whereCond[] = ['user_id', '=', $request->user_id];            
                        $anchUserData = $this->userRepo->getAnchorUserData($whereCond);            
                        if (isset($anchUserData[0]) && $anchUserData[0]->pan_no != $arrFileData['biz_pan_number']) {                
                            Session::flash('message', 'You can\'t changed the registered pan number.');
                            return redirect()->back();
                        }
      
			if(request()->is_gst_manual == 1){
				$arrFileData['biz_gst_number'] = request()->get('biz_gst_number_text');
			}
			
			$user_id = $request->user_id;
			$business_info = $this->appRepo->saveBusinessInfo($arrFileData, $user_id);
			//$appId  = Session::put('appId', $business_info['app_id']);
			
			//Add application workflow stages
			Helpers::addWfAppStage('new_case', $user_id);
			
			//update application workflow stages
			Helpers::updateWfStage('new_case', $business_info['app_id'], $wf_status = 1);
			
						
			if ($business_info) {
				//Add application workflow stages
				Helpers::updateWfStage('biz_info', $business_info['app_id'], $wf_status = 1, $assign_role = false);
				
				return redirect()->route('promoter_details',['app_id'=>$business_info['app_id'], 'biz_id'=>$business_info['biz_id'], 'edit' => 0]);
			} else {
				//Add application workflow stages
				Helpers::updateWfStage('biz_info', $business_info['app_id'], $wf_status = 2, $assign_role = false);
				
				return redirect()->back()->withErrors(trans('auth.oops_something_went_wrong'));
			}
		} catch (Exception $ex) {                
			return redirect()->back()->withErrors(Helpers::getExceptionMessage($ex));
		}
	}
	
	/**
	 * Show the offer
	 *
	 * @return \Illuminate\Http\Response
	 */
	public function showOffer(Request $request)
	{
		$appId = $request->get('app_id');
		$bizId = $request->get('biz_id');
		//$appData = $this->appRepo->getAppDataByAppId($appId);        
		//$loanAmount = $appData ? $appData->loan_amt : 0;
		
		$supplyOfferData = $this->appRepo->getAllOffers($appId, 1);//for supply chain
		$termOfferData = $this->appRepo->getAllOffers($appId, 2);//for term loan
		$leaseOfferData = $this->appRepo->getAllOffers($appId, 3);//for lease loan
		$offerStatus = $this->appRepo->getOfferStatus(['app_id' => $appId, 'is_approve'=>1, 'is_active'=>1, 'status'=>NULL]);//to check the offer status
		$is_shown = $this->appRepo->getOfferStatus([['app_id', $appId], ['is_approve', 1], ['is_active', 1]]);
		$currentStage = Helpers::getCurrentWfStage($appId);   
		$roleData = Helpers::getUserRole();        
		$viewGenSancLettertBtn = ($currentStage && $currentStage->role_id == $roleData[0]->id) ? 1 : 0;

		/*code for getting the sales manager*/     
		$appData = $this->appRepo->getAppDataByAppId($appId);               
		$userId = $appData ? $appData->user_id : null;     
		$userData = $this->userRepo->getfullUserDetail($userId);
		if ($userData && !empty($userData->anchor_id)) {
			$toUserId = $this->userRepo->getLeadSalesManager($userId);
		} else {
			$toUserId = $this->userRepo->getAssignedSalesManager($userId);
		}
		$authUser = Auth::user();
		if($authUser->user_id == $toUserId){
		  $isSalesManager = 1;
		}else{
		  $isSalesManager = 0;
		}
		/*code for getting the sales manager*/

		foreach($supplyOfferData as $key=>$data) {
			$supplyOfferData[$key]['anchorData'] = $this->userRepo->getAnchorDataById($data->anchor_id)->pluck('f_name')->first();
			$supplyOfferData[$key]['programData'] = $this->appRepo->getSelectedProgramData(['prgm_id' => $data->prgm_id])->first();
		}

		return view('backend.app.offer')
				->with('appId', $appId)
				->with('bizId', $bizId)                
				->with('supplyOfferData', $supplyOfferData)
				->with('termOfferData', $termOfferData)
				->with('leaseOfferData', $leaseOfferData)
				->with('offerStatus', $offerStatus)
				->with('is_shown', $is_shown)
				->with('isSalesManager', $isSalesManager)
				->with('currentStage', $currentStage)
				->with('viewGenSancLettertBtn', $viewGenSancLettertBtn);      
	}

	/**
	 * Accept Offer
	 * 
	 * @param Request $request
	 */
	public function acceptOffer(Request $request)
	{
		$appId = $request->get('app_id');        
		$offerId = $request->get('offer_id');
		$bizId = $request->get('biz_id');        
        $cmntText = $request->get('comment_txt');
		
		try {
			$offerData = [];
			if ($request->has('btn_accept_offer')) {
				$offerData['status'] = 1;           
				$message = trans('backend_messages.accept_offer_success');
				
				$addl_data = [];
				$addl_data['sharing_comment'] = $cmntText;
				$currStage = Helpers::getCurrentWfStage($appId);
				$wf_order_no = $currStage->order_no;
				$nextStage = Helpers::getNextWfStage($wf_order_no);
				$roleArr = [$nextStage->role_id];
				$roles = $this->appRepo->getBackStageUsers($appId, $roleArr);
				$addl_data['to_id'] = isset($roles[0]) ? $roles[0]->user_id : null;
							             		
				//Update workflow stage
				Helpers::updateWfStage('sales_queue', $appId, $wf_status = 1, $assign_case=true, $addl_data, $sendEmail = false);
				//Insert Pre Sanctions Documents
				$prgmDocsWhere = [];
				$prgmDocsWhere['stage_code'] = 'upload_pre_sanction_doc';
				$appData = $this->appRepo->getAppDataByAppId($appId);
				$userId = $appData ? $appData->user_id : null;
				$reqdDocs = $this->createAppRequiredDocs($prgmDocsWhere, $userId, $appId);
                                
                                Helpers::updateAppCurrentStatus($appId, config('common.mst_status_id.OFFER_ACCEPTED'));
			} else if($request->has('btn_reject_offer')) {				
				$offerData['status'] = 2;
				$message = trans('backend_messages.offer_rejected');
                                $appApprData = [
                                    'app_id' => $appId,
                                    'approver_user_id' => \Auth::user()->user_id,
                                    'status' => 2
                                  ];
                                //$this->appRepo->saveAppApprovers($appApprData);
                                AppApprover::updateAppApprActiveFlag($appId);
                                $addl_data = [];
                                $addl_data['sharing_comment'] = $cmntText;
                                $selRoleId = 6;
                                $roles = $this->appRepo->getBackStageUsers($appId, [$selRoleId]);
                                $selUserId = $roles[0]->user_id;
                                $currStage = Helpers::getCurrentWfStage($appId);
                                //$selRoleStage = Helpers::getCurrentWfStagebyRole($selRoleId);                                                
                                $selRoleStage = Helpers::getCurrentWfStagebyRole($selRoleId, $user_journey=2, $wf_start_order_no=$currStage->order_no, $orderBy='DESC');
                                Helpers::updateWfStageManual($appId, $selRoleStage->order_no, $currStage->order_no, $wf_status = 2, $selUserId, $addl_data);
                                Session::flash('operation_status', 1);
	 
                                Helpers::updateAppCurrentStatus($appId, config('common.mst_status_id.OFFER_REJECTED'));
                                				
			}
			
			// $savedOfferData = $this->appRepo->saveOfferData($offerData, $offerId);
			$savedOfferData = $this->appRepo->updateActiveOfferByAppId($appId, $offerData); 
                                                
                        $appPrgmOffers = $this->appRepo->getAllOffers($appId);
                        foreach($appPrgmOffers as $appPrgmOffer) {
                            if (!empty($appPrgmOffer->prgm_id) && ($appPrgmOffer->status == 1 || $appPrgmOffer->status == 2)) {
                                $prgmId = $appPrgmOffer->prgm_id;

                                $updatePrgmData = [];
                                $updatePrgmData['is_edit_allow'] = 1;

                                $whereUpdatePrgmData = [];
                                $whereUpdatePrgmData['prgm_id'] = $prgmId;                                        
                                $this->appRepo->updateProgramData($updatePrgmData, $whereUpdatePrgmData);
                            }
                        }

			if ($savedOfferData) {
				Session::flash('message', $message);
				//return redirect()->route('gen_sanction_letter', ['app_id' => $appId, 'biz_id' => $bizId, 'offer_id' => $offerId ]);
				return redirect()->route('view_offer', ['app_id' => $appId, 'biz_id' => $bizId ]);
			}
			
		} catch (Exception $ex) {
			return redirect()->back()->withErrors(Helpers::getExceptionMessage($ex));
		}        
	}
	
	/**
	 * Generate sanction letter
	 * 
	 * @param Request $request
	 * @return View
	 */
	public function genSanctionLetter(Request $request)
	{  
		$appId = $request->get('app_id');
		$bizId = $request->get('biz_id');
		$sanctionId = $request->get('sanction_id');
		$offerId = null;
		if ($request->has('offer_id') && !empty($request->get('offer_id'))) {
			$offerId = $request->get('offer_id');
		}
		$supplyChainFormFile = storage_path('app/public/user/'.$appId.'_supplychain.json');
		$supplyChainFormData = [];
		if (file_exists($supplyChainFormFile)) {
		  $supplyChainFormData = json_decode(base64_decode(file_get_contents($supplyChainFormFile)),true); 
		}
		// $data = $this->getSanctionLetterData((int)$appId, (int)$bizId, (int)$offerId, (int)$sanctionId);
		$data = $this->getSanctionLetterData((int)$appId, (int)$bizId, (int)$offerId, (int)$sanctionId);
		$supplyChaindata = $this->getSanctionLetterSupplyChainData($appId, $bizId, $offerId, $sanctionId);
		$appLimit = $this->appRepo->getAppLimit((int)$appId);
		return view('backend.app.sanction_letter')->with($data)->with(['supplyChaindata'=>$supplyChaindata, 'supplyChainFormData'=>$supplyChainFormData, 'appLimit' => $appLimit]);  
	}

   /* For Promoter pan verify iframe model    */
	
	public function showPanVerifyResponseData(Request $request)
	{
		$request =  $request->all();
		$result   = $this->userRepo->getOwnerAppRes($request);
		$res = json_decode($result->karza->res_file);
		return view('backend.app.promoter_pan_verify_data')->with('res', $res);
		
	} 
	   /* For Promoter pan iframe model    */
	public function showPanResponseData(Request $request)
	{
		$request =  $request->all();
		$result   = $this->userRepo->getOwnerAppRes($request);
		$res = json_decode((isset($result->karza)) ? $result->karza->res_file : '');
		return view('backend.app.promoter_pan_data')->with('res', $res);
		
	} 
	/* For Promoter driving  iframe model    */
	public function showDlResponseData(Request $request)
	{
		 $request =  $request->all();
		 $result   = $this->userRepo->getOwnerAppRes($request);
		 $res = json_decode((isset($result->karza)) ? $result->karza->res_file : '');
		return view('backend.app.promoter_dl_data')->with('res', $res);
		
	} 
	/* For Promoter voter iframe model    */
	public function showVoterResponseData(Request $request)
	{
		 $request =  $request->all();
		 $result   = $this->userRepo->getOwnerAppRes($request);
		 $res = json_decode((isset($result->karza)) ? $result->karza->res_file : '');
		return view('backend.app.promoter_voter_data')->with('res', $res);
		
	} 
	/* For Promoter passport iframe model    */
	public function showPassResponseData(Request $request)
	{
		 $request =  $request->all();
		 $result   = $this->userRepo->getOwnerAppRes($request);
		 $res = json_decode((isset($result->karza)) ? $result->karza->res_file : '');
		return view('backend.app.promoter_pass_data')->with('res', $res);
		
	} 
	

  /* For mobile Promoter iframe model    */
	public function mobileModel(Request $request){
		 $request =  $request->all();
		 $result   = $this->userRepo->getOwnerAppRes($request);
		 $res = json_decode($result->karza->res_file,1);
		
		 return view('backend.app.mobile_verification_detail')->with('response', $res);
	}
	
   
  /* For mobile  otp Promoter iframe model    */ 
	public function mobileOtpModel(Request $request){
		$request =  $request->all();
		$result   = $this->userRepo->getOwnerAppRes($request);
		$res = json_decode($result->karza->res_file,1);
		return view('backend.app.otp_verification_detail')->with('response', $res);
	}
	

 public function sentOtpmobile(Request $request){
	  $post_data = $request->all();
	  $mobile_no = trim($request->get('mobile_no'));
	   $mob = new MobileAuth_lib();
		$req_arr = array(
			'mobile' => $mobile_no,//'09AALCS4138B1ZE',
		);
		
	  $userData = State::getUserByAPP($post_data['appId']);
	  $response = $mob->api_call(MobileAuth_lib::SEND_OTP, $req_arr);
	  if ($response['status'] == 'success') {
		return response()->json(['message' =>"OTP Sent to $mobile_no.",'status' => 1,
		  'value' => $response['result'], 'request_id'=> $response['request_id']]);
	  }else{
		return response()->json(['message' =>'Something went wrong. Please try again','status' => 0]);
	  }
	}
	

	 public function verify_mobile(Request $request){
	  $post_data = $request->all();
	  $mobile_no = trim($request->get('mobile_no'));
	  $appId = trim($request->get('appId'));
	  if (empty($mobile_no) || !ctype_digit($mobile_no) || strlen($mobile_no) != 10) {
		return response()->json(['message' =>'Mobile Number is not valid.','status' => 0]);
	  }

	  $mob = new MobileAuth_lib();
		$req_arr = array(
			'mobile' => $mobile_no,//'09AALCS4138B1ZE',
		);
		
	  $userData = State::getUserByAPP($appId);
	  $response = $mob->api_call(MobileAuth_lib::MOB_VLD, $req_arr);
	  if(!empty($response['result']))
	  {     $status = 1;
			$createApiLog = @BizApiLog::create(['req_file' =>$response['payload'], 'res_file' => (is_array($response['result']) || is_object($response['result']) ? json_encode($response['result']) : $response['result']),'status' => $status,
			  'created_by' => Auth::user()->user_id]);
		   
			$createBizApi= @BizApi::create([
				'user_id' =>$userData['user_id'], 
				'biz_id' =>   $userData['biz_id'],
				'biz_owner_id' => $post_data['biz_owner_id'] ?? NULL,
				'type' => 7,
				'verify_doc_no' => 1,
				'status' => 1,
				'biz_api_log_id' => $createApiLog['biz_api_log_id'],
				'created_by' => Auth::user()->user_id
			 ]);
	  }
	  else
	  {
			$status = 0;
			$createApiLog = @BizApiLog::create(['req_file' =>$response['payload'], 'res_file' => (is_array($response['result']) || is_object($response['result']) ? json_encode($response['result']) : $response['result']),'status' => $status,
			  'created_by' => Auth::user()->user_id]);
			$resp['createApiLog'] = $createApiLog;
	  }
	  
	  if (empty($response['result'])) {
		$response['status'] = 'fail';
	  }
	  if ($response['status'] == 'success') {
		return response()->json(['message' =>"OTP Sent to $mobile_no.",'status' => 1,
		  'value' => $response['result'], 'request_id'=> $response['request_id']]);
	  }else{
		return response()->json(['message' =>$response['message'] ?? 'Something went wrong. Please try again','status' => 0]);
	  }
	}
	
	public function verify_otp_mobile(Request $request){
	  $post_data = $request->all();
	  $request_id = trim($request->get('request_id'));
	  $otp = trim($request->get('otp'));
	  $appId = trim($request->get('appId'));
	  $mob = new MobileAuth_lib();
		$req_arr = array(
			'request_id' => $request_id,
			'otp' => $otp,
		);
		
	  $userData = State::getUserByAPP($appId);
	  $response = $mob->api_call(MobileAuth_lib::VERF_OTP, $req_arr);  
   
	   if( $response['result'])
	  {
			$status = 1;
			$createApiLog = BizApiLog::create(['req_file' =>$response['payload'], 'res_file' => (is_array($response['result']) || is_object($response['result']) ? json_encode($response['result']) : $response['result']),'status' => $status,
				'created_by' => Auth::user()->user_id]);
		   
			$createBizApi= BizApi::create([
				'user_id' =>$userData['user_id'], 
				'biz_id' =>   $userData['biz_id'],
				'biz_owner_id' => $post_data['biz_owner_id'] ?? NULL,
				'type' => 9,
				'verify_doc_no' => 1,
				'status' => 1,
				'biz_api_log_id' => $createApiLog['biz_api_log_id'],
				'created_by' => Auth::user()->user_id
			 ]);
			  $response1 = $mob->api_call(MobileAuth_lib::GET_DTL, $req_arr);  
			  if($response1)
			  {
			   $status = 1;
			   $createApiLog1 = BizApiLog::create(['req_file' =>$response1['payload'], 'res_file' => (is_array($response1['result']) || is_object($response1['result']) ? json_encode($response1['result']) : $response1['result']),'status' => $status,
			  'created_by' => Auth::user()->user_id]);
			   
				$createBizApi= BizApi::create([
				'user_id' =>$userData['user_id'], 
				'biz_id' =>   $userData['biz_id'],
				'biz_owner_id' => $post_data['biz_owner_id'] ?? NULL,
				'type' => 8,
				'verify_doc_no' => 1,
				'status' => 1,
				'biz_api_log_id' => $createApiLog1['biz_api_log_id'],
				'created_by' => Auth::user()->user_id
			 ]);
				
			}
			
		 
	  }  
	  else
	  {
			   $status = 0;
			   $createApiLog = @BizApiLog::create(['req_file' =>$response['payload'], 'res_file' => (is_array($response['result']) || is_object($response['result']) ? json_encode($response['result']) : $response['result']),'status' => $status,
			  'created_by' => Auth::user()->user_id]);
			  
			   $createApiLog1 = @BizApiLog::create(['req_file' =>$response1['payload'], 'res_file' => (is_array($response1['result']) || is_object($response1['result']) ? json_encode($response1['result']) : $response1['result']),'status' => $status,
			  'created_by' => Auth::user()->user_id]);
	  }
	  if (empty($response['result'])) {
		$response['status'] = 'fail';
	  }
	///  dd($response['status']);
	  if ($response['status'] == 'success') {
		  $stts = $response['result']['sim_details']['otp_validated'] ?? NULL;
		  if ($stts) {
			  return response()->json(['message' =>'Verified Successfully','status' => 1,
		  'value' => $request_id, 'request_id'=> base64_encode($request_id)]);
		  }else{
			 return response()->json(['message' =>'','status' => 0]); 
		  }
		
	  }else{
		return response()->json(['message' =>'Something went wrong. Please try again','status' => 0]);
	  }
	}

	/**
	 * Send sanction letter
	 * 
	 * @return \Illuminate\Http\Response
	 */
	public function sendSanctionLetter(Request $request)
	{
		try {
			$appId = $request->get('app_id');
			$bizId = $request->get('biz_id');
			$sanctionId = $request->get('sanction_id');
			$offerId = null;
			if ($request->has('offer_id') && !empty($request->get('offer_id'))) {
				$offerId = $request->get('offer_id');
			} 

			$data = $this->getSanctionLetterData($appId, $bizId, $offerId, $sanctionId);
			$date = \Carbon\Carbon::now();
			$data['date'] = $date;
			$htmlContent = view('backend.app.send_sanction_letter')->with($data)->render();
			$userData =  $this->userRepo->getUserByAppId($appId);

			$emailData['email'] = $userData->email;
			$emailData['name'] = $userData->f_name . ' ' . $userData->l_name;
			$emailData['body'] = $htmlContent;
			$emailData['attachment'] = $this->pdf->render($htmlContent);
			$emailData['subject'] ="Sanction Letter for ".$data['biz_entity_name'];

			\Event::dispatch("SANCTION_LETTER_MAIL", serialize($emailData));
			Session::flash('message',trans('success_messages.send_sanction_letter_successfully'));
			return redirect()->back()->with('is_send',1);
		} catch (Exception $ex) {
			return redirect()->back()->withErrors(Helpers::getExceptionMessage($ex));
		}
	}

	/**
	 * Send sanction letter
	 * 
	 * @return \Illuminate\Http\Response
	 */
	public function sendSanctionLetterSupplyChain(Request $request)
	{
		try {
			$appId = $request->get('app_id');
			$bizId = (int) $request->get('biz_id');
			$offerId = null;
			$supplyChaindata = $this->getSanctionLetterSupplyChainData($appId, $bizId);
			$supplyChainFormFile = storage_path('app/public/user/'.$appId.'_supplychain.json');
			$arrFileData = [];
			if (file_exists($supplyChainFormFile)) {
			  $arrFileData = json_decode(base64_decode(file_get_contents($supplyChainFormFile)),true); 
			}
			$data = ['appId' => $appId, 'bizId' => $bizId, 'offerId'=>$offerId,'download'=> false];
			$htmlContent = view('backend.app.sanctionSupply')->with($data)->with(['supplyChaindata'=>$supplyChaindata,'postData'=>$arrFileData])->render();
			$userData =  $this->userRepo->getUserByAppId($appId);
			$emailData['email'] = $userData->email;
			$emailData['name'] = $userData->f_name . ' ' . $userData->l_name;
			$emailData['body'] = $htmlContent;
			$emailData['attachment'] = $this->pdf->render($htmlContent);
			$emailData['subject'] ="Sanction Letter for SupplyChain";
			\Event::dispatch("SANCTION_LETTER_MAIL", serialize($emailData));
			Session::flash('message',trans('Sanction Letter for Supply Chain sent successfully.'));

            $whereActivi['activity_code'] = 'send_sanction_letter_supplychain';
            $activity = $this->masterRepo->getActivity($whereActivi);
            if(!empty($activity)) {
                $activity_type_id = isset($activity[0]) ? $activity[0]->id : 0;
                $activity_desc = 'Send mail Sanction Letter Of Supplychain My Application in Manage Application. AppID '. $appId;
                $arrActivity['app_id'] = $appId;
                $this->activityLogByTrait($activity_type_id, $activity_desc, response()->json(['supplyChaindata'=>$supplyChaindata,'postData'=>$arrFileData]), $arrActivity);
            }			
			
			return redirect()->back()->with('is_send',1);
		} catch (Exception $ex) {
			return redirect()->back()->withErrors(Helpers::getExceptionMessage($ex));
		}
	}
	
	/**
	 * Show upload sanction letter
	 * 
	 * @param Request $request
	 * @return View
	 */
	public function showUploadSanctionLetter(Request $request)
	{
		$appId = $request->get('app_id');
		$offerId = $request->get('offer_id');
		$bizId = $request->get('biz_id');
		//$prgmDocsWhere = [];
		//$prgmDocsWhere['app_id'] = $appId;
		//$prgmDocsWhere['stage_code'] = 'upload_exe_doc';
		//$prgmDocs = $this->appRepo->getProgramDocs($prgmDocsWhere);    //33;
		
		//$docId = $prgmDocs ? $prgmDocs[0]->doc_id : null;
		$docId = 33;
		
		return view('backend.app.upload_sanction_letter')
				->with('appId', $appId)
				->with('bizId', $bizId)
				->with('offerId', $offerId)               
				->with('docId', $docId);
	}
	
	/**
	 * Upload signed sanction letter
	 * 
	 * @param Request $request
	 * @return Response
	 */
	public function uploadSanctionLetter(Request $request)
	{
		
		$arrFileData = $request->all();
				
		$docId = $request->get('doc_id');
		$appId = $request->get('app_id');
		$bizId = $request->get('biz_id');
		$offerId = $request->get('offer_id');
		
		try {
			$uploadData = Helpers::uploadAwsBucket($arrFileData, $appId);

			$userFile = $this->docRepo->saveFile($uploadData);

			if(!empty($userFile->file_id)) {
				
				$appDocData = Helpers::appDocData($arrFileData, $userFile->file_id);
				$appDocResponse = $this->docRepo->saveAppDoc($appDocData);
				
				//Update workflow stage
				//Helpers::updateWfStage('upload_exe_doc', $appId, $wf_status = 1);
											 
				Session::flash('message',trans('backend_messages.upload_sanction_letter_success'));
			} else {
				Session::flash('message',trans('backend_messages.upload_sanction_letter_fail'));
			}            
			return redirect()->route('gen_sanction_letter', ['app_id' => $appId, 'biz_id' => $bizId, 'offer_id' => $offerId ]);
			
		} catch (Exception $ex) {
			return redirect()->back()->withErrors(Helpers::getExceptionMessage($ex));
		}       
	}

	/**
	 * View Approver List
	 * 
	 * @param Request $request
	 * @return view
	 */    
	public function viewApprovers(Request $request){
		try{
			$app_id = $request->get('app_id');
			if($app_id){
				$approvers = AppApprover::getAppApproversDetails($app_id);
				$data = array();
				foreach($approvers as $key => $approver){
					$data[$key]['approver'] = $approver->approver;
					$data[$key]['approver_email'] = $approver->approver_email;
					$data[$key]['approver_role'] = $approver->approver_role;
					$data[$key]['approved_date'] = ($approver->updated_at)? date('d-M-Y',strtotime($approver->updated_at)) : '---';
					$data[$key]['stauts'] = ($approver->status == '1')?"Approved":"";
				}
				return view('backend.app.view_approvers')->with('approvers', $data);
			}
		} catch (Exception $ex){
			return redirect()->back()->withErrors(Helpers::getExceptionMessage($ex));
		}
	}

	/**
	 * View Shared Details
	 * 
	 * @param Request $request
	 * @return view
	 */  
	public function viewSharedDetails(Request $request){
		try{
			$app_id = $request->get('app_id');
			if($app_id){
				$assignees = AppAssignment::getAppAssignees((int) $app_id);

				$data = array();
				foreach($assignees as $key => $assignee){
					$from_role_name = '';

				   
					if($assignee->from_user_id){
						$from_role_name = User::getUserRoles((int) $assignee->from_user_id);
						if($from_role_name->count()!=0)
							$from_role_name = $from_role_name[0];
					}

					if($assignee->to_user_id){
						$to_role_name = User::getUserRoles((int) $assignee->to_user_id);
						if($to_role_name->count()!=0)
						$to_role_name = $to_role_name[0];
					}else{
						$to_role_name = Role::getRole((int) $assignee->role_id);
					}
					
				   // dump($to_role_name);
					
					$data[$key]['assignby'] = $assignee->assignby;
					$data[$key]['assignto'] = $assignee->assignto;
					$data[$key]['sharing_comment'] = $assignee->sharing_comment;
					$data[$key]['assigne_date'] = ($assignee->created_at)? date('d-M-Y',strtotime($assignee->created_at)) : '---';
					$data[$key]['assignby_role'] = ($from_role_name && $from_role_name->count()!=0)? $from_role_name->name:'';
					$data[$key]['assignto_role'] = ($to_role_name && $to_role_name->count()!=0)? $to_role_name->name:'';
				}
				return view('backend.app.view_shared_details')->with('approvers', $data);
			}
		} catch (Exception $ex){
			return redirect()->back()->withErrors(Helpers::getExceptionMessage($ex));
		}
	}

	  /**
	 * Save Sanction Letter
	 * 
	 * @param Request $request
	 * @return view
	 */  
	public function saveSanctionLetter(Request $request){
		
		try {
			$arrFileData = $request->all();
			$appId = (int)$request->app_id; 
			$offerId = (int)$request->offer_id; 
			$bizId = (int) $request->get('biz_id');
			$sanctionId = null;

			if($request->has('sanction_id')){
				$sanctionId = $request->sanction_id; 
			}
			$sanctionData = array(
				'prgm_offer_id' => $offerId,
				'lessor' => $request->lessor,
				'validity_date'=>  Carbon::createFromFormat('d/m/Y', $request->sanction_validity_date)->format('Y-m-d')  , 
				'expire_date'=>  Carbon::createFromFormat('d/m/Y', $request->sanction_expire_date)->format('Y-m-d')  , 
				'validity_comment' =>  $request->sanction_validity_comment, 
				'payment_type' =>  $request->payment_type, 
				'payment_type_other' =>  $request->payment_type_comment,
				'delay_pymt_chrg' => $request->delay_pymt_chrg,
				'insurance' => $request->insurance,
				'bank_chrg' => $request->bank_chrg,
				'legal_cost' => $request->legal_cost,
				'po' => $request->po,
				'pdp' => $request->pdp,
				'disburs_guide' => $request->disburs_guide,
				'other_cond' => $request->other_cond,
				'covenants' => $request->covenants,
			);
			$sanction_info = $this->appRepo->saveSanctionData($sanctionData,$sanctionId);

			if($sanction_info){
				Session::flash('message',trans('success_messages.save_sanction_letter_successfully'));
				return redirect()->route('gen_sanction_letter', ['app_id' => $appId, 'offer_id' => $offerId, 'sanction_id' => $sanction_info->sanction_id,'biz_id' => $bizId]);
			} 
		} catch (Exception $ex) {
			return redirect()->route('gen_sanction_letter', ['app_id' => $appId, 'biz_id' => $bizId, 'offer_id' => $offerId, 'sanction_id' => $sanction_info->sanction_id])->withErrors(Helpers::getExceptionMessage($ex));
		}
	}

	 /**
	 * Save Sanction Letter SupplyChain
	 * 
	 * @param Request $request
	 * @return view
	 */  
	public function saveSanctionLetterSupplychain(Request $request){
		try {
			$arrFileData = $request->all();
			$arrFileData['pdc_facility_amt'] = preg_replace('#[^0-9]+#', '', $arrFileData['pdc_facility_amt']);
			$arrFileData['nach_facility_amt'] = preg_replace('#[^0-9]+#', '', $arrFileData['nach_facility_amt']);
			$arrFileData['dsra_amt'] = preg_replace('#[^0-9]+#', '', $arrFileData['dsra_amt']);
			$appId = (int)$request->app_id; 
			$offerId = (int)$request->offer_id; 
			$bizId = (int) $request->get('biz_id');
			$supplyChaindata = $this->getSanctionLetterSupplyChainData($appId, $bizId, $offerId);
			$filepath = storage_path('app/public/user/'.$appId.'_supplychain.json');
			\File::put($filepath, base64_encode(json_encode($arrFileData)));
                        
						Helpers::updateAppCurrentStatus($appId, config('common.mst_status_id.SANCTION_LETTER_GENERATED'));
						
            $whereActivi['activity_code'] = 'save_sanction_letter_supplychain';
            $activity = $this->masterRepo->getActivity($whereActivi);
            if(!empty($activity)) {
                $activity_type_id = isset($activity[0]) ? $activity[0]->id : 0;
                $activity_desc = 'Save Sanction Letter Of Supplychain My Application in Manage Application. AppID '. $appId;
                $arrActivity['app_id'] = $appId;
                $this->activityLogByTrait($activity_type_id, $activity_desc, response()->json($supplyChaindata), $arrActivity);
            } 						
                        
			Session::flash('message',trans('success_messages.save_sanction_letter_successfully'));
			return redirect()->route('gen_sanction_letter', ['app_id' => $appId, 'offer_id' => $offerId, 'sanction_id' => null,'biz_id' => $bizId]);  
		} catch (Exception $ex) {
			return redirect()->route('gen_sanction_letter', ['app_id' => $appId, 'biz_id' => $bizId, 'offer_id' => $offerId, 'sanction_id' => $sanction_info->sanction_id])->withErrors(Helpers::getExceptionMessage($ex));
		}
	}

	 /**
	 * Save Sanction Letter SupplyChain
	 * 
	 * @param Request $request
	 * @return view
	 */  
	public function previewSanctionLetterSupplychain(Request $request){
		try {
			$arrFileData = $request->all();
			$appId = (int)$request->app_id; 
			$offerId = (int)$request->offer_id; 
			$bizId = (int) $request->get('biz_id');
			$supplyChaindata = $this->getSanctionLetterSupplyChainData($appId, $bizId, $offerId);
			$supplyChainFormFile = storage_path('app/public/user/'.$appId.'_supplychain.json');
			$arrFileData = [];
			if (file_exists($supplyChainFormFile)) {
			  $arrFileData = json_decode(base64_decode(file_get_contents($supplyChainFormFile)),true); 
			}
			$data = ['appId' => $appId, 'bizId' => $bizId, 'offerId'=>$offerId,'download'=> true];
			$html = view('backend.app.sanctionSupply')->with($data)->with(['supplyChaindata'=>$supplyChaindata,'postData'=>$arrFileData])->render();
			return  $html;
		} catch (Exception $ex) {
			return redirect()->route('gen_sanction_letter', ['app_id' => $appId, 'biz_id' => $bizId, 'offer_id' => $offerId, 'sanction_id' => $sanction_info->sanction_id])->withErrors(Helpers::getExceptionMessage($ex));
		}
	}

	public function previewSanctionLetter(Request $request){
		try {
			$appId = $request->get('app_id');
			$bizId = $request->get('biz_id');
			$sanctionId = $request->get('sanction_id');
			$offerId = null;
			if ($request->has('offer_id') && !empty($request->get('offer_id'))) {
				$offerId = $request->get('offer_id');
			} 
			
			$data = $this->getSanctionLetterData($appId, $bizId, $offerId, $sanctionId);
			$date = \Carbon\Carbon::now();
			$data['date'] = $date;
			$html = view('backend.app.send_sanction_letter')->with($data)->render();
			if(!Session::get('is_send')){

				$html .='<div align="center">
				<a href="'. route('send_sanction_letter', ['app_id' => $appId, 'biz_id' => $bizId, 'offer_id' => $offerId, 'download'=>1, 'sanction_id'=>$data['sanctionData']->sanction_id ]).'" class="btn btn-success btn-sm">Send Mail</a>
				</div>';
			}
			return  $html;
		} catch (Exception $ex) {
			return redirect()->back()->withErrors(Helpers::getExceptionMessage($ex));
		}
	}
	/**
	 * function for change the app status
	 * 
	 * @param Request $request
	 * @return View
	 */
	public function showAppStatusForm(Request $request) {
		try {
			  $app_id = $request->get('app_id');
			  $biz_id = $request->get('biz_id');
		return view('backend.app.change_app_disbursed_status')
				->with('app_id', $app_id)
				->with('biz_id', $biz_id);
			} catch (Exception $ex) {
				return redirect()->back()->withErrors(Helpers::getExceptionMessage($ex));
			}
	}

	/** 
	 * @Author: Rent Alpha
	 * @Date: 2020-01-30 18:22:07 
	 * @Desc:  function for save disburse app status 
	 */    
	public function saveShowAppStatusForm(Request $request){
		try {
			$app_id = (int)$request->post('app_id');
			$biz_id = (int)$request->post('biz_id');
		   // dd($app_id,$biz_id , config('common.mst_status_id')['DISBURSED']);
			$arrUpdateApp=[
				'curr_status_id'=>config('common.mst_status_id')['DISBURSED'],
                            'status' => 2,
			];
			$appStatus = $this->appRepo->updateAppDetails($app_id,  $arrUpdateApp);           

                        /*
			 //Update workflow stage
			 $addl_data = [];
			 $currStage = Helpers::getCurrentWfStage($app_id);
			 $wf_order_no = $currStage->order_no;
			 $nextStage = Helpers::getNextWfStage($wf_order_no);
			 $roleArr = [$nextStage->role_id];
			 $roles = $this->appRepo->getBackStageUsers($app_id, $roleArr);
			 $addl_data['to_id'] = isset($roles[0]) ? $roles[0]->user_id : null;            
			 Helpers::updateWfStage('opps_checker', $app_id, $wf_status = 1, $assign_case=true, $addl_data);
			 Helpers::updateCurrentWfStage('disbursed_or_in_lms', $app_id, $wf_status=1);
                        */
                        
			if($appStatus){
				$getAppDetails = $this->appRepo->getAppData($app_id);
				$arrAppStatusLog=[
					'user_id'=>$getAppDetails['user_id'],
					'app_id'=>$app_id,
					'status_id'=>config('common.mst_status_id')['DISBURSED'],
					'created_by'=>Auth::user()->user_id,
					'created_at'=>\Carbon\Carbon::now(),   
				];
				  $this->appRepo->saveAppStatusLog($arrAppStatusLog);
				Session::flash('message',trans('backend_messages.change_app_disbursed_status'));
				return redirect()->route('cam_overview', ['app_id' => $app_id,'biz_id'=>$biz_id]);
			}
		} catch (Exception $ex) {
			return redirect()->back()->withErrors(Helpers::getExceptionMessage($ex));
		}
	}

		 /**
	 * Handling deleting PAN documents file for the application.
	 *
	 * @param  \Illuminate\Http\Request  $request
	 * @return \Illuminate\Http\Response
	 */
	
	public function promoterDocumentDelete(Request $request)
	{
		try {
			$fileId = $request->file_id;
			$fileId = $request;

			$where = [
				'app_id' => $fileId['app_id'],
				'biz_owner_id' => $fileId['owner_id'],
				'doc_id' => $fileId['doc_id'],
				'file_id' => $fileId['file_id']
			];
			$this->docRepo->disableIsOVD($where);
			$response = $this->docRepo->deleteFile($fileId);
			
			if ($response) {
				Session::flash('message',trans('success_messages.deleted'));
				return redirect()->back();
			} else {
				return redirect()->back()->withErrors(trans('auth.oops_something_went_wrong'));
			}
		} catch (Exception $ex) {
			return redirect()->back()->withErrors(Helpers::getExceptionMessage($ex));
		}
	}

    /**
	 * Reject application
	 * 
	 * @param Request $request
	 * @return view
	 */
	public function rejectApp(Request $request) {
		$app_id = $request->get('app_id');
		$biz_id = $request->get('biz_id');        
		$user_id = $request->get('user_id');
		$status_id = $request->get('curr_status_id') ?: '';
		$note_id = $request->get('note_id');
		$reason = '';
		// dd($request->all());
		if($note_id){
			$noteData = $this->appRepo->getNoteDataById($note_id, $app_id);
			$reason =  $noteData->note_data;
		}
                $appData = $this->appRepo->getAppData($app_id);
                $curStatusId = $appData ? $appData->curr_status_id : 0;
		// dd($request->all());
		return view('backend.app.reject_app_form')
				->with('app_id', $app_id)
				->with('biz_id', $biz_id)
				->with('user_id', $user_id)
				->with('reason', $reason)
				->with('status_id', $status_id)
				->with('note_id', $note_id)
                                ->with('cur_status_id', $curStatusId);
	}
        
    /**
	 * Save application rejection
	 * 
	 * @param Request $request
	 * @return view
	 */    
	public function saveAppRejection(Request $request) {
            try {

                $app_id = $request->get('app_id');
                $biz_id = $request->get('biz_id');
                $user_id = $request->get('user_id');
                $reason = $request->get('reason');
				$status = $request->get('status');
				$note_id = $request->get('note_id');
                $cur_status_id = $request->get('cur_status_id');
                $appStatus = '';
                if($status == 1){
                    $appStatus = 'APP_REJECTED';
                }else if($status == 2){
                    $appStatus = 'APP_CANCEL';
                }else if($status == 3){
                    $appStatus = 'APP_HOLD';
                }else if($status == 4){
                    $appStatus = 'APP_DATA_PENDING';
				}
		            
            if (isset(config('common.mst_status_id')[$appStatus]) && $cur_status_id != (int)config('common.mst_status_id')[$appStatus]) {
                $noteData = [
                        'app_id' => $app_id, 
                        'note_data' => $reason,
                        'created_at' => \Carbon\Carbon::now(),
                        'created_by' => \Auth::user()->user_id
				];   
				
				// if($note_id){
				// 	$noteData = $this->appRepo->findNoteDatabyNoteId($note_id);
				// 	dd($noteData);
				// 	if($noteData){
				// 		$this->appRepo->updateAppNote($noteData, $note_id);
				// 	}
				// }
				// dd('save');

                $result = $this->appRepo->saveAppNote($noteData)->latest()->first()->toArray();
                if($result){
                    $appStatusData = [
                        'app_id' => $app_id,
                        'user_id' => $user_id,
                        'note_id' => $result['note_id'],
                        'status_id' => (int) config('common.mst_status_id')[$appStatus],
                        'created_at' => $result['created_at'],
                        'created_by' => \Auth::user()->user_id
                    ];
                    $this->appRepo->saveAppStatusLog($appStatusData);
                    
                    $arrUpdateApp=[
			'curr_status_id'=>(int) config('common.mst_status_id')[$appStatus],
                        'curr_status_updated_at' => \Carbon\Carbon::now()
                    ];
			
                    $this->appRepo->updateAppDetails($app_id,  $arrUpdateApp);
                    if (in_array($appStatus, ['APP_REJECTED', 'APP_CANCEL'])) {
                    	$updtData = [
                    		'status' => 2, 
                    		'is_active' => 0,
                    	];
                    	$this->appRepo->updateOfferByAppId($app_id, $updtData);
                    }
                    
                    $appStatusList = Helpers::getAppStatusList();
                    $arrActivity = [];
                    $arrActivity['activity_code'] = 'app_status_changed';
                    $arrActivity['activity_desc'] = 'Application status is modified from ' . (isset($appStatusList[$cur_status_id]) ? $appStatusList[$cur_status_id] : '') . ' to ' . (isset($appStatusList[$arrUpdateApp['curr_status_id']]) ? $appStatusList[$arrUpdateApp['curr_status_id']] : '');
                    $arrActivity['user_id'] = $user_id;
                    $arrActivity['app_id'] = $app_id;
                    \Event::dispatch("ADD_ACTIVITY_LOG", serialize($arrActivity));                    
                }
            }
				$whereActivi['activity_code'] = 'save_app_rejection';
				$activity = $this->masterRepo->getActivity($whereActivi);
				if(!empty($activity)) {
					$activity_type_id = isset($activity[0]) ? $activity[0]->id : 0;
					$activity_desc = 'App Rejection of My Application in Manage Application. AppID '. $app_id;
					$arrActivity['app_id'] = $app_id;
					$this->activityLogByTrait($activity_type_id, $activity_desc, response()->json(['noteData'=>$noteData, 'saveAppStatusLog' => $appStatusData]), $arrActivity);
				} 			
                Session::flash('message',trans('backend_messages.reject_app'));
                return redirect()->route('application_list');
            } catch (Exception $ex) {
                    return redirect()->back()->withErrors(Helpers::getExceptionMessage($ex));
            }
	}

	public function getAppStatusList(Request $request){
		// dd($request->all());
		$app_id = $request->get('app_id');      
		$user_id = $request->get('user_id');
		$status_id = $request->get('curr_status_id');
		$note_id = $request->get('note_id');

		if($app_id){
			$allCommentsData = $this->appRepo->getAllCommentsByAppId($app_id);
			//dd($allCommentsData);
		}

		return view('backend.app.view_application_status')
					->with('allCommentsData',$allCommentsData);

	}
        
	public function acceptOfferForm(Request $request){
		try {
            $appId = $request->get('app_id');
            $bizId = $request->get('biz_id');
            return view('backend.cam.accept_offer')
            ->with(['app_id' => $appId, 'biz_id' => $bizId]);
        } catch (\Exception $ex) {
            return Helpers::getExceptionMessage($ex);
        }
	}   
    
}