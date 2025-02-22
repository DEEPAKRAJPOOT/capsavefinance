<?php

namespace App\Http\Controllers\Application;
use PDF;
use Auth;
use Helpers;
use Session;
use Carbon\Carbon;
use App\Helpers\Helper;
use Illuminate\Http\Request;
use App\Libraries\KarzaTxn_lib;
use Eastwest\Json\Facades\Json;
use App\Libraries\MobileAuth_lib;
use App\Http\Controllers\Controller;
use App\Inv\Repositories\Models\User;
use App\Http\Requests\DocumentRequest;
use App\Inv\Repositories\Models\BizApi;
use Illuminate\Support\Facades\Storage;
use App\Http\Requests\PartnerFormRequest;
use App\Inv\Repositories\Models\UcicUser;
use App\Inv\Repositories\Models\Master\State;
use App\Inv\Repositories\Models\UcicUserUcic;
use App\Http\Requests\BusinessInformationRequest;
use App\Inv\Repositories\Models\Master\LocationType;
use App\Inv\Repositories\Contracts\Traits\ApplicationTrait;
use App\Inv\Repositories\Contracts\UserInterface as InvUserRepoInterface;
use App\Inv\Repositories\Contracts\MasterInterface as InvMasterRepoInterface;
use App\Inv\Repositories\Contracts\ApplicationInterface as InvAppRepoInterface;
use App\Inv\Repositories\Contracts\DocumentInterface as InvDocumentRepoInterface;
use App\Inv\Repositories\Contracts\UcicUserInterface as InvUcicUserRepoInterface;

class ApplicationController extends Controller
{
    use ApplicationTrait;
    
    protected $appRepo;
    protected $userRepo;
    protected $docRepo;
    protected $masterRepo;

    public function __construct(InvAppRepoInterface $app_repo, InvUserRepoInterface $user_repo, InvDocumentRepoInterface $doc_repo, InvMasterRepoInterface $master_repo, InvUcicUserRepoInterface $ucicuser_repo){
        $this->appRepo = $app_repo;
        $this->userRepo = $user_repo;
        $this->docRepo = $doc_repo;
        $this->masterRepo = $master_repo;
        $this->ucicuser_repo = $ucicuser_repo;
    }
    
    /**
     * Show the business information form.
     *
     * @return \Illuminate\Http\Response
     */
    public function showBusinessInformationForm(Request $request)
    {
        $userId  = Session::has('userId') ? Session::get('userId') : (\Auth::user() ? \Auth::user()->user_id : 0);
        if (!$request->get('app_id')) {            
            //$where=[];
            //$where['user_id'] = $userId;
            //$where['status'] = [0,1];
            //$appData = $this->appRepo->getApplicationsData($where);
            $appData = $this->appRepo->checkAppByPan($userId) ?? $this->appRepo->checkAppByPanForNonAnchorLeads($userId);
            $userData = $this->userRepo->getfullUserDetail($userId);           
            $isAnchorLead = $userData && !empty($userData->anchor_id);

            if ($appData) {
                if(Auth::user()->anchor_id == config('common.LENEVO_ANCHOR_ID')){
                    return redirect()->route('front_application_list');
                } else {
                    Session::flash('message', trans('error_messages.active_app_check'));
                    return redirect()->back();
                }
            }
        }
        
        $userArr = [];
        $product_ids = [];
        $states = State::getStateList()->get();
        $product_types = $this->masterRepo->getProductDataList();

        if ($userId > 0) {
            $userArr = $this->userRepo->find($userId);
        }
        $industryList = $this->appRepo->getIndustryDropDown()->toArray();
        $constitutionList = $this->appRepo->getConstitutionDropDown()->toArray();
        $segmentList = $this->appRepo->getSegmentDropDown()->toArray();

        $anchUserData = $this->userRepo->getAnchorUserData(['user_id' => $userId]);        
        $pan_no = isset($anchUserData[0]) ? $anchUserData[0]->pan_no : null;
        $locationType = LocationType::getLocationDropDown();


        if (!count($anchUserData) && !$pan_no) {
			$nonAnchorLead = $this->userRepo->getNonAnchorLeadByUserId(['user_id' => $userId]);
			$pan_no = $nonAnchorLead ? $nonAnchorLead->pan_no : null;
		}

        $cinList = [];
        $gstList = [];
        $data = [];
        $ucic_code = '';

       $ucicDetails = UcicUser::where('pan_no', $pan_no)->first();        

        if($ucicDetails){
            $ucic_code = $ucicDetails->ucic_code;
            $pan_no = $ucicDetails->pan_no;
            $appId = $ucicDetails->app_id;
            $requestAppId = (int) $request->app_id;
            
            if($requestAppId){
                $app_data = $this->appRepo->getAppDataByAppId($requestAppId);
                if($app_data && $app_data->biz_id){
                    $business_info = $this->appRepo->getApplicationById($app_data->biz_id);
                    $cinList = $business_info->cins;
                    $gstList = $business_info->gsts;
                    if (!empty($app_data->products)) {
                        foreach($app_data->products as $product){
                            $product_ids[$product->pivot->product_id]= array(
                                "loan_amount" => $product->pivot->loan_amount,
                                "tenor_days" => $product->pivot->tenor_days
                            );
                        }
                    }

                    $data = $this->ucicuser_repo->formatBusinessInfoDb($business_info, $product_ids);
                }
            }
        }

        if($request->has('__signature') && $request->has('biz_id')){
            $business_info = $this->appRepo->getApplicationById($request->biz_id);
            $app_data = $this->appRepo->getAppDataByBizId($request->biz_id);
            foreach($app_data->products as $product){
              $product_ids[$product->pivot->product_id]= array( 
                  "loan_amount" => $product->pivot->loan_amount,
                  "tenor_days" => $product->pivot->tenor_days
              );
          }
            return view('frontend.application.company_details')
                        ->with(['business_info'=>$business_info, 'states'=>$states, 'product_types'=>$product_types, 'product_ids'=> $product_ids])
                        ->with('user_id',$request->get('user_id'))
                        ->with('app_id',$request->get('app_id'))
                        ->with('biz_id',$request->get('biz_id'))
                        ->with('industryList',$industryList)
                        ->with('constitutionList',$constitutionList)
                        ->with('segmentList',$segmentList)
                        ->with('locationType',$locationType)
                        ->with('cinList',$cinList)
                        ->with('ucicCode',$ucic_code)
                        ->with('data',$data)
                        ->with('pan_no',$pan_no)
                        ->with('gstList',$gstList);
        }else{
            return view('frontend.application.business_information', compact(['userArr', 'states', 'product_types','industryList','constitutionList', 'segmentList', 'pan_no', 'locationType', 'data', 'gstList', 'cinList', 'ucic_code']));
        }
    }

    public function saveBusinessInformation(BusinessInformationRequest $request)
    {
        try {
            $arrFileData = $request->all();
            
            $whereCond=[];
            $whereCond[] = ['user_id', '=', Auth::user()->user_id];            
            $anchUserData = $this->userRepo->getAnchorUserData($whereCond);            
            if (isset($anchUserData[0]) && $anchUserData[0]->pan_no != $arrFileData['biz_pan_number']) {                
                Session::flash('message', 'You can\'t changed the registered pan number.');
                return redirect()->back();
            }

            $ucicDetails = UcicUser::where(['ucic_code'=> $arrFileData['ucic_code']])->first();
            
            if(request()->is_gst_manual == 1){
              $arrFileData['biz_gst_number'] = request()->get('biz_gst_number_text');
            }

            if(!$ucicDetails) {                
                Session::flash('message', 'Ucic ('.$arrFileData['ucic_code'].') details not found, Please verify your Pan No!');
                return redirect()->back();
            }else{
                $userUcicId = $ucicDetails->user_ucic_id ?? null;
            }

            if($request->has('__signature') && $request->has('biz_id')){
                $bizId = $request->biz_id;
                $business_info = $this->appRepo->updateCompanyDetail($arrFileData, $bizId, Auth::user()->user_id);

                if ($business_info) {
                    
                    //Update Anchor Pan and Biz Id
                    $arrAnchUser=[];
                    $arrAnchUser['biz_id'] = $bizId;           
                    $this->userRepo->updateAnchorUserData($arrAnchUser, ['user_id' => Auth::user()->user_id]); 
                    
                    $appId = $request->app_id;
                    $appData = $this->appRepo->getAppData($appId);
                    //Update UCIC Data only if appllication is not approved
                    if(!Helper::isAppApprByAuthorityForGroup($appId) && $ucicDetails->is_sync == 1) {
                        $product_ids = [];
                        $business_info = $this->appRepo->getApplicationById($appData->biz_id);
                        if (!empty($appData->products)) {
                            foreach($appData->products as $product){
                                $product_ids[$product->pivot->product_id]= array(
                                    "loan_amount" => $product->pivot->loan_amount,
                                    "tenor_days" => $product->pivot->tenor_days
                                );
                            }
                        }
                        $businessInfo = $this->ucicuser_repo->formatBusinessInfoDb($business_info,$product_ids);
                        $ownerPanApi = $this->userRepo->getOwnerApiDetail(['biz_id' => $appData->biz_id]);
                        $documentData = \Helpers::makeManagementInfoDocumentArrayData($ownerPanApi);
                        $managementData = $this->ucicuser_repo->formatManagementInfoDb($ownerPanApi,NULL);
                        $managementInfo = array_merge($managementData,$documentData);
                        $this->ucicuser_repo->saveApplicationInfo($userUcicId, $businessInfo, $managementInfo, $appData->app_id);
                    }

                    Session::flash('message',trans('success_messages.update_company_detail_successfully'));
                    return redirect()->route('promoter-detail',['app_id' =>  $request->app_id, 'biz_id' => $bizId, 'app_status'=>0]);
                } else {
                    return redirect()->back()->withErrors(trans('auth.oops_something_went_wrong'));
                }
            }else{
                $business_info = $this->appRepo->saveBusinessInfo($arrFileData, Auth::user()->user_id);
                
                //Update UCIC Data only if appllication is not approved
                $appData = $this->appRepo->getAppData($business_info['app_id']);
                if($ucicDetails && isset($business_info['app_id'])){
                    UcicUserUcic::firstOrCreate(
                        [
                        'app_id' => $business_info['app_id'], 
                        'ucic_id' => $ucicDetails->user_ucic_id
                    ],[
                        'ucic_id' => $ucicDetails->user_ucic_id,
                        'user_id' => Auth::user()->user_id ?? NULL,
                        'app_id' => $business_info['app_id']  ?? NULL,
                        'group_id' => $ucicDetails->group_id
                        ]       
                    );
                }
                if(!Helper::isAppApprByAuthorityForGroup($business_info['app_id']) && $ucicDetails->is_sync == 1) {
                    if(isset($business_info['biz_id']) && isset($business_info['app_id']) && $appData){
                        $product_ids = [];
                        $businessInfo = $this->appRepo->getApplicationById($appData->biz_id);
                        if (!empty($appData->products)) {
                            foreach($appData->products as $product){
                                $product_ids[$product->pivot->product_id]= array(
                                    "loan_amount" => $product->pivot->loan_amount,
                                    "tenor_days" => $product->pivot->tenor_days
                                );
                            }
                        }
                        $data = $this->ucicuser_repo->formatBusinessInfoDb($businessInfo,$product_ids);
                        
                        if(!$userUcicId){
                            $pan_no = $appData->business->pan->pan_gst_hash;
                            $ucicDetails = $this->ucicuser_repo->createUpdateUcic(['pan_no' => $pan_no, 'user_id'=>$appData->user_id, 'app_id' => $appData->app_id]);
                            $userUcicId = $ucicDetails->user_ucic_id;
                        }
                        if($userUcicId){
                            $this->ucicuser_repo->saveBusinessInfoApp($data,$userUcicId,$appData->app_id);
                        }
                    }
                }

                //Add application workflow stages
                Helpers::updateWfStage('new_case', $business_info['app_id'], $wf_status = 1);
                
                            
                if ($business_info) {
                    
                    //Update Anchor Pan and Biz Id
                    $arrAnchUser=[];
                    $arrAnchUser['biz_id'] = $business_info['biz_id'];           
                    $this->userRepo->updateAnchorUserData($arrAnchUser, ['user_id' => Auth::user()->user_id]); 
                    
                    //Add application workflow stages
                    Helpers::updateWfStage('biz_info', $business_info['app_id'], $wf_status = 1);
                    
                    Session::flash('message',trans('success_messages.save_company_detail_successfully'));
                    return redirect()->route('promoter-detail',['app_id'=>$business_info['app_id'], 'biz_id'=>$business_info['biz_id'], 'edit' => 1]);
                } else {
                    //Add application workflow stages
                    Helpers::updateWfStage('biz_info', $business_info['app_id'], $wf_status = 2);
                    
                    return redirect()->back()->withErrors(trans('auth.oops_something_went_wrong'));
                }
            }
        } catch (Exception $ex) {                
            return redirect()->back()->withErrors(Helpers::getExceptionMessage($ex));
        }
    }

    /**
     * Show the Promoter Details form.
     *
     * @return \Illuminate\Http\Response
     */
    public function showPromoterDetail(Request $request)
    {
        
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
       if($getCin==false)
       {
          return redirect()->back();
       }
      
        $OwnerPanApi = $this->userRepo->getOwnerApiDetail($attribute);
        if(!empty($getCin))
        {
            $cin =    $getCin->cin; 
        }
        else
        {
            $cin =    ""; 
        }
        $appData 	 = $this->appRepo->getAppData($appId);
        $documentData = \Helpers::makeManagementInfoDocumentArrayData($OwnerPanApi);
		$managementData = $this->ucicuser_repo->formatManagementInfoDb($OwnerPanApi,NULL);

        return view('frontend.application.promoter-detail')->with([
            'ownerDetails' => $OwnerPanApi, 
            'cin_no' => $cin,
            'appId' => $appId, 
            'bizId' => $bizId,
            'edit' => $editFlag,
            'is_lease' => $getProductType,
            'appData' => $appData,
			'manInfoData' => $managementData['management_info']['owners'] ?? [],
			'manInfoDocData' => $documentData ?? []
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

                //Update UCIC Data only if appllication is not approved
                if(!Helper::isAppApprByAuthorityForGroup($appId) && $appData->ucicUserUcic->ucicUser->is_sync == 1) {
                    $ucicUserData = $appData->ucicUserUcic;
                    $userUcicId = $ucicUserData->ucic_id ?? null;
                    $isSync = $ucicUserData->is_sync ?? false;
					if(!$userUcicId){
						$pan_no = $appData->business->pan->pan_gst_hash;
						$ucicDetails = $this->ucicuser_repo->createUpdateUcic(['pan_no' => $pan_no, 'user_id'=>$userId, 'app_id' => $appId]);
						$userUcicId = $ucicDetails->user_ucic_id;
                        $isSync = $ucicDetails->is_sync;
					}
					if($userUcicId){
						$product_ids = [];
						$business_info = $this->appRepo->getApplicationById($appData->biz_id);
						if (!empty($appData->products)) {
							foreach($appData->products as $product){
								$product_ids[$product->pivot->product_id]= array(
									"loan_amount" => $product->pivot->loan_amount,
									"tenor_days" => $product->pivot->tenor_days
								);
							}
						}
                        if($isSync){
                            $businessInfo = $this->ucicuser_repo->formatBusinessInfoDb($business_info,$product_ids);
                            $ownerPanApi = $this->userRepo->getOwnerApiDetail(['biz_id' => $appData->biz_id]);
                            $documentData = \Helpers::makeManagementInfoDocumentArrayData($ownerPanApi);
                            $managementData = $this->ucicuser_repo->formatManagementInfoDb($ownerPanApi,NULL);
                            $managementInfo = array_merge($managementData,$documentData);
                            $this->ucicuser_repo->saveApplicationInfo($userUcicId, $businessInfo, $managementInfo, $appData->app_id);
                        }
					}
				}

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
    /**
     * Show the Business documents form.
     *
     * @return \Illuminate\Http\Response
     */
    public function showDocument(Request $request)
    {
       
        $appId = $request->get('app_id');
        $biz_id = $request->get('biz_id');
        $editFlag = $request->get('edit');
        $userId = Auth::user()->user_id;
        $gstdata = State::getSelectedGstForApp($biz_id);
        $bankdata = State::getBankData();
        $gst_no = $gstdata['pan_gst_hash'] ?? '';
        $appData = $this->appRepo->getAppDataByAppId($appId);
        $userId =  $appData->user_id;
        
        if ($appId > 0) {
            $requiredDocs = $this->docRepo->findRequiredDocs($userId, $appId);
          
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
        
        return view('frontend.application.update_document')->with([
            'requiredDocs' => $requiredDocs,
            'documentData' => $docData,
            'gst_no' => $gst_no,
            'bankdata' => $bankdata,
            'appId' => $appId,
        ]); 
    } 
    
    public function editUploadDocument(Request $request)
    {
        $fileId = $request->get('app_doc_file_id');
        $data = $this->docRepo->getAppDocFileById($fileId);

        return view('frontend.application.edit_upload_document', [
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
        return redirect()->route('document', ['app_id' => $appId, 'biz_id' => $bizId]);

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
    * showing form for uploading document
    */
    
    public function uploadDocument()
    {
        $bankdata = User::getBankData();
        
        return view('frontend.application.upload_document', [
                    'bankdata' => $bankdata
                ]);   
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

            $docId = (int)$request->docId; //  fetch document id
            $appId = (int)$request->appId; //  fetch document id
            $bizId = (int)$request->bizId; //  fetch document id
            $userId = Auth::user()->user_id;

            switch ($docId) {
                case '4':
                    $file_bank_id = $arrFileData['file_bank_id'];
                    $bankData = State::getBankName($file_bank_id);
                    $arrFileData['doc_name'] = $bankData['bank_name'] ?? NULL;
                    $arrFileData['finc_year'] = NULL;
                    $arrFileData['gst_month'] = $arrFileData['bank_month'];
                    $arrFileData['gst_year'] = $arrFileData['bank_year'];
                    $arrFileData['pwd_txt'] = $arrFileData['is_pwd_protected'] ? $arrFileData['pwd_txt'] :NULL;
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
                //$appId = $arrFileData['appId'];       
                //$response = $this->docRepo->isUploadedCheck($userId, $appId);            
                //$wf_status = $response->count() < 1 ? 1 : 2;
                //Helpers::updateWfStage('doc_upload', $appId, $wf_status);
                
                Session::flash('message',trans('success_messages.uploaded'));

                return redirect()->route('document', ['app_id' => $appId, 'biz_id' => $bizId]);
            } else {
                //Add application workflow stages
                //Helpers::updateWfStage('doc_upload', $request->get('appId'), $wf_status=2);

                return redirect()->route('document', ['app_id' => $appId, 'biz_id' => $bizId]);
            }
        } catch (Exception $ex) {
            //Add application workflow stages
            //Helpers::updateWfStage('doc_upload', $request->get('appId'), $wf_status=2);
        
            return redirect()->route('document', ['app_id' => $appId, 'biz_id' => $bizId])->withErrors(Helpers::getExceptionMessage($ex));
        }
    }
    
    /**
     * Handling deleting documents file for the application.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    
    public function documentDelete($appDocFileId)
    {
        try {
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
            $appId  = $request->get('app_id');
            $userId = Auth::user()->user_id;
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
                    if($appData){
                        $product_ids = [];
                        $businessInfo = $this->appRepo->getApplicationById($appData->biz_id);
                        if (!empty($appData->products)) {
                            foreach($appData->products as $product){
                                $product_ids[$product->pivot->product_id]= array(
                                    "loan_amount" => $product->pivot->loan_amount,
                                    "tenor_days" => $product->pivot->tenor_days
                                );
                            }
                        }
                        $data = $this->ucicuser_repo->formatBusinessInfoDb($businessInfo,$product_ids);
                        $userUcicId = $appData->ucicUserUcic->ucic_id ?? null;
                        if(!$userUcicId){
                            $pan_no = $appData->business->pan->pan_gst_hash;
                            $ucicDetails = $this->ucicuser_repo->createUpdateUcic(['pan_no' => $pan_no, 'user_id'=>$appData->user_id, 'app_id' => $appData->app_id]);
                            $userUcicId = $ucicDetails->user_ucic_id;
                        }
                        if($userUcicId){
                            $exitsUcicData = $this->ucicuser_repo->getUcicData(['user_ucic_id' => $userUcicId]);
                            $product_ids = [];
                            $business_info = $this->appRepo->getApplicationById($appData->biz_id);
                            if (!empty($appData->products)) {
                                foreach($appData->products as $product){
                                    $product_ids[$product->pivot->product_id]= array(
                                        "loan_amount" => $product->pivot->loan_amount,
                                        "tenor_days" => $product->pivot->tenor_days
                                    );
                                }
                            }
                            $businessInfo = $this->ucicuser_repo->formatBusinessInfoDb($business_info,$product_ids);
                            $ownerPanApi = $this->userRepo->getOwnerApiDetail(['biz_id' => $appData->biz_id]);
                            $documentData = \Helpers::makeManagementInfoDocumentArrayData($ownerPanApi);
                            $managementData = $this->ucicuser_repo->formatManagementInfoDb($ownerPanApi,NULL);
                            $managementInfo = array_merge($managementData,$documentData);

                            if(isset($exitsUcicData)) {
                                if($exitsUcicData->is_sync == 1){
                                    $this->ucicuser_repo->saveApplicationInfo($userUcicId, $businessInfo, $managementInfo, $appData->app_id);
                                    $attr['user_id'] = $appData->user_id;
                                    $attr['app_id'] = $appData->app_id;
                                    $this->ucicuser_repo->update($attr, $userUcicId);
                                    $whereucic['user_id'] = $appData->user_id;
                                    $whereucic['app_id']  = $appData->app_id;
                                    $ucicuserData = UcicUserUcic::getUcicUserData($whereucic);
                                    if ($ucicuserData == false) {
                                        $ucicNewDataucic['ucic_id'] = $userUcicId;
                                        $ucicNewDataucic['user_id'] = $appData->user_id;
                                        $ucicNewDataucic['app_id'] = $appData->app_id;
                                        UcicUserUcic::create($ucicNewDataucic);
                                    }
                                }
                            }
                        }
                    }

                    if ($appData && in_array($appData->app_type, [1,2,3]) ) {
                        $parentAppId = $appData->parent_app_id;
                        $actualEndDate = $curDate;
                        //$appLimitData = $this->appRepo->getAppLimitData(['user_id' => $userId, 'app_id' => $parentAppId]);
                        //if (in_array($appType, [2,3])) {
                        //    $curDate = isset($appLimitData[0]) ? $appLimitData[0]->start_date : null;
                        //    $endDate = isset($appLimitData[0]) ? $appLimitData[0]->end_date : null;
                        //}
                        $this->appRepo->updateAppData($parentAppId, ['status' => 3]);
                        $this->appRepo->updateAppLimit(['status' => 2, 'actual_end_date' => $actualEndDate], ['app_id' => $parentAppId]);
                        $this->appRepo->updatePrgmLimit(['status' => 2, 'actual_end_date' => $actualEndDate], ['app_id' => $parentAppId, 'product_id' => 1]);  
                        \Helpers::updateAppCurrentStatus($parentAppId, config('common.mst_status_id.APP_CLOSED'));                                    
                    }                      
                }                
                
                
                Helpers::updateWfStage('doc_upload', $appId, $wf_status = 1);
             
                //Add application workflow stages                
                Helpers::updateWfStage('app_submitted', $appId, $wf_status = 1);
                                
                //Insert Pre Offer Documents
                $prgmDocsWhere = [];
                $prgmDocsWhere['stage_code'] = 'pre_offer';
                //$appData = $this->appRepo->getAppDataByAppId($appId);
                //$userId = $appData ? $appData->user_id : null;
                $reqdDocs = $this->createAppRequiredDocs($prgmDocsWhere, $userId, $appId);
                if(Auth::user()->anchor_id == config('common.LENEVO_ANCHOR_ID')) {
                    return redirect()->route('front_application_list')->with('message', trans('success_messages.app.completed'));
                } else {
                    return redirect()->route('front_dashboard')->with('message', trans('success_messages.app.completed'));
                }
            // } else {
            //     //Add application workflow stages                
            //     Helpers::updateWfStage('app_submitted', $request->get('app_id'), $wf_status = 2);
                
            //     return redirect()->back()->withErrors(trans('error_messages.app.incomplete'));
            // }
        } catch (Exception $ex) {
            //Add application workflow stages                
            //Helpers::updateWfStage('app_submitted', $request->get('app_id'), $wf_status = 2);
                
            return redirect()->back()->withErrors(Helpers::getExceptionMessage($ex));
        }
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {   
        $userId = Auth::user()->user_id;
        $appData = $this->appRepo->checkAppByPan($userId);
       if((!$appData) && (Auth::user()->anchor_id == config('common.LENEVO_ANCHOR_ID'))){
            return redirect()->route('business_information_open');
        }
       $appStatus = $this->masterRepo->getAppStatus($status_type=1);
       $appStatusList = [];       
                
       $selStatusList = [
            config('common.mst_status_id.APP_INCOMPLETE'),
            config('common.mst_status_id.COMPLETED'),            
            config('common.mst_status_id.APP_SANCTIONED'),           
            config('common.mst_status_id.APP_REJECTED'),
            config('common.mst_status_id.APP_CANCEL'),
            config('common.mst_status_id.APP_HOLD'),
            config('common.mst_status_id.APP_DATA_PENDING'),           
            config('common.mst_status_id.APP_CLOSED')
        ];
       foreach($appStatus as $statusId=>$appStatusName) {
           if (in_array($statusId, $selStatusList)) {
                $appStatusList[$statusId] = $appStatusName;
           }
       }
       return view('frontend.application.index')->with('appStatusList', $appStatusList);   
              
    }


    public function gstinForm(Request $request){
     $appId = $request->get('app_id');
     $biz_id = $request->get('biz_id');
     $user_id = Auth::user()->user_id;
     $gst_details = State::getSelectedGstForApp($biz_id);
     $all_gst_details = State::getAllGstForApp($biz_id);
     $gst_no = $gst_details['pan_gst_hash'];
     return view('frontend.application.gstin',compact('gst_no','all_gst_details','appId'));   
    }

    public function send_gst_otp(Request $request) {
     if ($request->session()->exists('request_id')) {
        $request->session()->forget(['request_id','request_gstin']);
      }
      $post_data = $request->all();
      $gst_no = trim($request->get('gst_no'));
      $gst_usr = trim($request->get('gst_usr'));
      $appId = trim($request->get('appId'));
      $user_id = Auth::user()->user_id;
      $app_user = State::getUserByAPP($appId);
      $app_userId = $app_user['user_id'];
      if ($app_userId != $user_id) {
        return response()->json(['message' =>'Data can not be manipulated','status' => 0]);
      }

      if(file_exists(public_path("storage/user/".$appId.'_'.$gst_no.".pdf"))){
        return response()->json(['message' =>'GST Report already pulled.','status' => 0]);
      }

      if (empty($gst_no)) {
        return response()->json(['message' =>'GST Number can\'t be empty.','status' => 0]);
      }
      if (empty($gst_usr)) {
        return response()->json(['message' =>'GST Username can\'t be empty.','status' => 0]);
      }

      $karza = new KarzaTxn_lib();
        $req_arr = array(
            'gstin' => $gst_no,//'09AALCS4138B1ZE',
            'username' => $gst_usr,//'prolitus27',
            'app_id' => $appId,
        );

      $response = $karza->api_call($req_arr);
      if (empty($response['requestId'])) {
         return response()->json(['message' =>'Unable to send OTP. Please try again later.','status' => 0]);
      }

      $request_id = $response['requestId'];
      $result = json_decode($response['result'],true);

      if (empty($result['status_cd']) || $result['status_cd'] != 1) {
         return response()->json(['message' =>'Unable to send OTP. Please try again later.','status' => 0]);
      }

     $request->session()->put('request_id', $request_id);
     $request->session()->put('request_gstin', $gst_no);
     return response()->json(['message' =>'OTP Send successfully to registered email ID.','status' => 1]);
    }

    public function verify_gst_otp(Request $request) {
      $post_data = $request->all();
      $gst_no = trim($request->get('gst_no'));
      $gst_usr = trim($request->get('gst_usr'));
      $appId = trim($request->get('appId'));
      $otp = trim($request->get('otp'));
      $user_id = Auth::user()->user_id;
      $app_user = State::getUserByAPP($appId);
      $app_userId = $app_user['user_id'];
      if ($app_userId != $user_id) {
        return response()->json(['message' =>'Data can not be manipulated','status' => 0]);
      }

      $request_id = $request->session()->get('request_id');
      $request_gstin = $request->session()->get('request_gstin');


      if ($request_gstin != $gst_no) {
        return response()->json(['message' =>'Provided GST Number is not valid.','status' => 0]);
      }

      if(file_exists(public_path("storage/user/".$appId.'_'.$gst_no.".pdf"))){
        return response()->json(['message' =>'GST Report already pulled.','status' => 0]);
      }

      if (empty($gst_no)) {
        return response()->json(['message' =>'GST Number can\'t be empty.','status' => 0]);
      }
      if (empty($gst_usr)) {
        return response()->json(['message' =>'GST Username can\'t be empty.','status' => 0]);
      }

      $karza = new KarzaTxn_lib();
        $req_arr = array(
            'gstin' => $gst_no,//'09AALCS4138B1ZE',
            'app_id' => $appId,
            'requestId' => $request_id,
            'otp' => $otp,
        );

      $response = $karza->api_call($req_arr, true);
      if (empty($response['requestId'])) {
         return response()->json(['message' =>'Invalid OTP. Please try again later.','status' => 0]);
      }

      $request_id = $response['requestId'];
      $result = json_decode($response['result'],true);
      if (empty($result['status_cd']) || $result['status_cd'] != 1) {
         return response()->json(['message' =>'Invalid OTP. Please try again later.','status' => 0]);
      }
      $request->session()->forget(['request_id','request_gstin']);
      return response()->json(['message' =>'OTP verified successfully.','status' => 1]);
    }


    public function analyse_gst(Request $request){
      $post_data = $request->all();
      $gst_no = trim($request->get('gst_no'));
      $gst_usr = trim($request->get('gst_usr'));
      $gst_pass = trim($request->get('gst_pass'));
      $appId = trim($request->get('appId'));
      $user_id = Auth::user()->user_id;
      $app_user = State::getUserByAPP($appId);
      $app_userId = $app_user['user_id'];

      if ($app_userId != $user_id) {
        return response()->json(['message' =>'Data can not be manipulated','status' => 0]);
      }

      if(file_exists(public_path("storage/user/".$appId.'_'.$gst_no.".pdf"))){
        return response()->json(['message' =>'GST Report already pulled.','status' => 0]);
      }

      if (empty($gst_no)) {
        return response()->json(['message' =>'GST Number can\'t be empty.','status' => 0]);
      }
      if (empty($gst_usr)) {
        return response()->json(['message' =>'GST Username can\'t be empty.','status' => 0]);
      }
      if (empty($gst_pass)) {
        return response()->json(['message' =>'GST Password can\'t be empty.','status' => 0]);
      }

      $karza = new KarzaTxn_lib();
        $req_arr = array(
            'gstin' => $gst_no,//'09AALCS4138B1ZE',
            'username' => $gst_usr,//'prolitus27',
            'password' => $gst_pass,//'Prolitus@1234',
            'app_id' => $appId,
        );


      $response = $karza->api_call($req_arr);
      if ($response['status'] == 'success') {
          $fname = $appId.'_'.$gst_no;
          $this->logdata($response['result'], 'F', $fname.'.json');
          $json_decoded = json_decode($response['result'], TRUE);
          $file_name = $fname.'.pdf';
          Storage::put('public/user/'.$file_name, file_get_contents($json_decoded['pdfDownloadLink'])); 
          $file= url('storage/user/'. $file_name);
        return response()->json(['message' =>'GST data pulled successfully.','status' => 1]);
      }else{
        return response()->json(['message' => $response['message'] ?? 'Something went wrong','status' => 0]);
      }
    }
  /* For Promoter pan verify iframe model    */
    
    public function showPanVerifyResponseData(Request $request)
    {
        $request =  $request->all();
        $result   = $this->userRepo->getOwnerAppRes($request);
        $res = $result->karza ? json_decode($result->karza->res_file) : '';
        return view('backend.app.promoter_pan_verify_data')->with('res', $res);
        
    }
     /* For Promoter pan iframe model    */
    
    public function showPanResponseData(Request $request)
    {
        $request =  $request->all();
        $result   = $this->userRepo->getOwnerAppRes($request);
        $res = json_decode($result->karza->res_file);
        return view('backend.app.promoter_pan_data')->with('res', $res);
        
    } 
    /* For Promoter driving  iframe model    */
    public function showDlResponseData(Request $request)
    {
         $request =  $request->all();
         $result   = $this->userRepo->getOwnerAppRes($request);
         $res = json_decode($result->karza->res_file);
        return view('backend.app.promoter_dl_data')->with('res', $res);
        
    } 
    /* For Promoter voter iframe model    */
    public function showVoterResponseData(Request $request)
    {
         $request =  $request->all();
         $result   = $this->userRepo->getOwnerAppRes($request);
         $res = json_decode($result->karza->res_file);
        return view('backend.app.promoter_voter_data')->with('res', $res);
        
    } 
    /* For Promoter passport iframe model    */
    public function showPassResponseData(Request $request)
    {
         $request =  $request->all();
         $result   = $this->userRepo->getOwnerAppRes($request);
         $res = json_decode($result->karza->res_file);
        return view('backend.app.promoter_pass_data')->with('res', $res);
        
    } 
    

  /* For mobile Promoter iframe model    */
    public function mobileModel(Request $request){
         $request =  $request->all();
         $result   = $this->userRepo->getOwnerAppRes($request);
         $res = json_decode($result->karza->res_file,1);
         return view('backend.app.mobile_verification_detail')->with('response', $res['result']);
    }
    
   
  /* For mobile  otp Promoter iframe model    */ 
    public function mobileOtpModel(Request $request){
        $request =  $request->all();
        $result   = $this->userRepo->getOwnerAppRes($request);
        $res = json_decode($result->karza->res_file,1);
        return view('backend.app.otp_verification_detail')->with('response', $res['result']);
    }


  public function logdata($data, $w_mode = 'D', $w_filename = '', $w_folder = '') {
    list($year, $month, $date, $hour) = explode('-', strtolower(date('Y-M-dmy-H')));
    $main_dir = 'public/user/';
   /* $year_dir = $main_dir . "$year/";
    $month_dir = $year_dir . "$month/";
    $date_dir = $month_dir . "$date/";
    $hour_dir = $date_dir . "$hour/";

    if (!file_exists($year_dir)) {
      mkdir($year_dir, 0777, true);
    }
    if (!file_exists($month_dir)) {
      mkdir($month_dir, 0777, true);
    }
    if (!file_exists($date_dir)) {
      mkdir($date_dir, 0777, true);
    }
    if (!file_exists($hour_dir)) {
      mkdir($hour_dir, 0777, true);
    }
*/
    $hour_dir = $main_dir;

    $data = is_array($data) || is_object($data) ? json_encode($data) : $data;

    $data = base64_encode($data);
    if (strtolower($w_mode) == 'f') {
      $final_dir = $hour_dir;
      $filepath = explode('/', $w_folder);
      foreach ($filepath as $value) {
        $final_dir .= "$value/";
        if (!Storage::exists($final_dir)) {
            Storage::makeDirectory($final_dir, 0777, true);
        }
      }
      $my_file = $final_dir . $w_filename;
      return Storage::put($my_file,PHP_EOL . $data . PHP_EOL);
      
    } else {

      $my_file = $hour_dir . date('ymd') . '.log';
      $time = date('H:i:s');
      Storage::append($my_file,PHP_EOL . 'Log ' . $time);
      return Storage::append($my_file,PHP_EOL . $data . PHP_EOL);
    }
  }

	 /**
	 * Handling OVD PAN documents file for the application.
	 *
	 * @param  \Illuminate\Http\Request  $request
	 * @return \Illuminate\Http\Response
	 */
	
	public function promoterDocumentOVD(Request $request)
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
			$response = $this->docRepo->disableIsOVD($where);
			
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
}