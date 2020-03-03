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
use App\Inv\Repositories\Models\User;
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
class ApplicationController extends Controller
{
    use ApplicationTrait;
    use LmsTrait;
    
    protected $appRepo;
    protected $userRepo;
    protected $docRepo;
    protected $masterRepo;

    /**
     * The pdf instance.
     *
     * @var App\Libraries\Pdf
     */
    protected $pdf;
    
    public function __construct(InvAppRepoInterface $app_repo, InvUserRepoInterface $user_repo, InvDocumentRepoInterface $doc_repo, InvMasterRepoInterface $master_repo, Pdf $pdf){
        $this->appRepo = $app_repo;
        $this->userRepo = $user_repo;
        $this->docRepo = $doc_repo;
        $this->masterRepo = $master_repo;
        $this->pdf = $pdf;
        $this->middleware('checkBackendLeadAccess');
    }
    
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {  
    
       return view('backend.app.index');              
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
            
            foreach($app_data->products as $product){
              //  array_push($product_ids, $product->pivot->product_id);

                $product_ids[$product->pivot->product_id]= array( 
                    "loan_amount" => $product->pivot->loan_amount,
                    "tenor_days" => $product->pivot->tenor_days
                );
            }
            $states = State::getStateList()->get();
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
                        ->with('segmentList',$segmentList);
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
                Session::flash('message',trans('success_messages.update_company_detail_successfully'));
                return redirect()->route('promoter_details',['app_id' =>  $appId, 'biz_id' => $bizId]);
            } else {
                return redirect()->back()->withErrors(trans('auth.oops_something_went_wrong'));
            }
        } catch (Exception $ex) {
            return redirect()->back()->withErrors(Helpers::getExceptionMessage($ex));
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
       if($getCin==false)
       {
          return redirect()->back();
       }
      
        $OwnerPanApi = $this->userRepo->getOwnerApiDetail($attribute);
      // dd($OwnerPanApi);
        return view('backend.app.promoter-details')->with([
            'ownerDetails' => $OwnerPanApi, 
            'cin_no' => $getCin->cin,
            'appId' => $appId, 
            'bizId' => $bizId,
            'edit' => $editFlag
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

            $wf_status = 1;                
            Helpers::updateWfStage('doc_upload', $appId, $wf_status);
            
            $document_info = $this->docRepo->saveDocument($arrFileData, $docId, $userId);
            if ($document_info) {
                //Add/Update application workflow stages    
                //$response = $this->docRepo->isUploadedCheck($userId, $appId);            
                //$wf_status = $response->count() < 1 ? 1 : 2;
                //$wf_status = 1;                
                //Helpers::updateWfStage('doc_upload', $appId, $wf_status);
                
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
            $appId  = (int)$request->app_id;
            $userData = User::getUserByAppId($appId);
            $userId = $userData->user_id;
            // $response = $this->docRepo->isUploadedCheck($userId, $appId);
            
            // if ($response->count() < 1) {
                
                $this->appRepo->updateAppData($appId, ['status' => 1]);
                                  
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
            $assign_case = $request->has('assign_case') ? $request->get('assign_case') : 0;
            
            $currentStage = Helpers::getCurrentWfStage($app_id);            
            $curr_role_id = $currentStage ? $currentStage->role_id : null;
            
            //$last_completed_wf_stage = WfAppStage::getCurrentWfStage($app_id);
            $wf_order_no = $currentStage->order_no;
            $nextStage = Helpers::getNextWfStage($wf_order_no);  
            $next_role_id = $nextStage ? $nextStage->role_id : null;
            
            if ($assign_case) {
                $rolesArr = explode(',', $currentStage->assign_role);
                //$roles = $this->userRepo->getRoleByArray($rolesArr);
                $roles = $this->appRepo->getBackStageUsers($app_id, $rolesArr);
                $roleDropDown = [];
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
                ->with('next_role_id', $next_role_id);
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
            $user_id = $request->get('user_id');
            $app_id = $request->get('app_id');
            $sel_assign_role = $request->get('sel_assign_role');
            $assign_case = $request->get('assign_case');
            $sharing_comment = $request->get('sharing_comment');
            $curr_role_id = $request->get('curr_role_id');
            
                        
            $addl_data = [];
            $addl_data['sharing_comment'] = $sharing_comment;

            if ($curr_role_id && $assign_case) {
                $selData = explode('-', $sel_assign_role);
                $selRoleId = $selData[0];
                $selUserId = $selData[1];                
                $selRoleStage = Helpers::getCurrentWfStagebyRole($selRoleId);                
                $currStage = Helpers::getCurrentWfStage($app_id);
                Helpers::updateWfStageManual($app_id, $selRoleStage->order_no, $currStage->order_no, $wf_status = 2, $selUserId, $addl_data);
            } else {
                $currStage = Helpers::getCurrentWfStage($app_id);
                //Validate the stage
                if ($currStage->stage_code == 'credit_mgr') {
                    $whereCondition = ['app_id' => $app_id];
                    $offerData = $this->appRepo->getOfferData($whereCondition);
                    if (!$offerData) {
                        Session::flash('error_code', 'no_offer_found');
                        return redirect()->back();
                    }
                } else if ($currStage->stage_code == 'approver') {
                    $whereCondition = ['app_id' => $app_id];
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
                    $whereCondition = ['app_id' => $app_id];
                    $offerData = $this->appRepo->getOfferData($whereCondition);
                    if (isset($offerData->is_approve) && $offerData->is_approve != 1) {
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
						'created_by' => Auth::user()->user_id
						);
                  	$createCustomer = $this->appRepo->createCustomerId($lmsCustomerArray);
                  	if($createCustomer != null) {
	                  	$capId = sprintf('%07d', $createCustomer->lms_user_id);
	                  	$virtualId = 'CAPVA'.$capId;
              			$createCustomerId = $this->appRepo->createVirtualId($createCustomer, $virtualId);

              			$prcsAmt = $this->appRepo->getPrgmLimitByAppId($app_id);
						        if(isset($prcsAmt->offer)) {
                        foreach ($prcsAmt->offer as $key => $offer) {
                                  // $tranType = 4 for processing acc. to mst_trans_type table
                          $pf = round((($offer->prgm_limit_amt * $offer->processing_fee)/100),2);
                          $pfWGst = round((($pf*18)/100),2);

                          $pfDebitData = $this->createTransactionData($user_id,['amount' => $pf, 'gst' => $pfWGst] , null, 4);
                          $pfDebitCreate = $this->appRepo->saveTransaction($pfDebitData);

                          // $pfCreditData = $this->createTransactionData($user_id, ['amount' => $pf, 'gst' => $pfWGst], null, 4, 1);
                          // $pfCreditCreate = $this->appRepo->saveTransaction($pfCreditData);

                          // $tranType = 20 for document fee acc. to mst_trans_type table
                          $df = round((($offer->prgm_limit_amt * $offer->document_fee)/100),2);
                          $dfWGst = round((($df*18)/100),2);

                          $dfDebitData = $this->createTransactionData($user_id, ['amount' => $df, 'gst' => $dfWGst], null, 20);
                          $createTransaction = $this->appRepo->saveTransaction($dfDebitData);

                          // $dfCreditData = $this->createTransactionData($user_id, ['amount' => $df, 'gst' => $dfWGst], null, 20, 1);
                          // $createTransaction = $this->appRepo->saveTransaction($dfCreditData);
                        }
                      
                    }
                  	}
                }
                $wf_order_no = $currStage->order_no;
                $nextStage = Helpers::getNextWfStage($wf_order_no);
                $roleArr = [$nextStage->role_id];
                
                if ($nextStage->stage_code == 'approver') {
                    $apprAuthUsers = Helpers::saveApprAuthorityUsers($app_id);
                    if (count($apprAuthUsers) == 0) {
                        Session::flash('error_code', 'no_approval_users_found');
                        return redirect()->back();                           
                    }
                    foreach($apprAuthUsers as $approver) {
                        $appAssignData = [
                            'app_id' => $app_id,
                            'to_id' => $approver->user_id,
                            'assigned_user_id' => $user_id,
                            'sharing_comment' => '',
                        ];
                        Helpers::assignAppUser($appAssignData);
                    }
                    $assign = false;
                    $wf_status = 1;
                } else {
                    $roles = $this->appRepo->getBackStageUsers($app_id, $roleArr);
                    $addl_data['to_id'] = isset($roles[0]) ? $roles[0]->user_id : null;
                    $assign = true;
                    $wf_status = 1;
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
        $states = State::getStateList()->get();
        $product_types = $this->masterRepo->getProductDataList();
        $industryList = $this->appRepo->getIndustryDropDown()->toArray();
        $constitutionList = $this->appRepo->getConstitutionDropDown()->toArray();
        $segmentList = $this->appRepo->getSegmentDropDown()->toArray();

        return view('backend.app.business_information',compact(['states', 'product_types','industryList','constitutionList', 'segmentList']));
    }

    /**
     * 
     */

    public function saveBusinessInformation(BusinessInformationRequest $request)
    {
        try {

            $arrFileData = $request->all();

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
        $offerStatus = $this->appRepo->getOfferStatus($appId);//to check the offer status
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

        return view('backend.app.offer')
                ->with('appId', $appId)
                ->with('bizId', $bizId)                
                ->with('supplyOfferData', $supplyOfferData)
                ->with('termOfferData', $termOfferData)
                ->with('leaseOfferData', $leaseOfferData)
                ->with('offerStatus', $offerStatus)
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
        
        try {
            $offerData = [];
            if ($request->has('btn_accept_offer')) {
                $offerData['status'] = 1;           
                $message = trans('backend_messages.accept_offer_success');
                
                $addl_data = [];
                $currStage = Helpers::getCurrentWfStage($appId);
                $wf_order_no = $currStage->order_no;
                $nextStage = Helpers::getNextWfStage($wf_order_no);
                $roleArr = [$nextStage->role_id];
                $roles = $this->appRepo->getBackStageUsers($appId, $roleArr);
                $addl_data['to_id'] = isset($roles[0]) ? $roles[0]->user_id : null;
                                    
                //Update workflow stage
                Helpers::updateWfStage('sales_queue', $appId, $wf_status = 1, $assign_case=true, $addl_data);                
                
            } else if($request->has('btn_reject_offer')) {
                $addl_data = [];
                $addl_data['sharing_comment'] = 'Reject comment goes here';
                $offerData['status'] = 2; 
                $message = trans('backend_messages.reject_offer_success');
                
                //Update workflow stage
                //Helpers::updateWfStage('approver', $appId, $wf_status = 2);
                //Helpers::updateWfStage('sales_queue', $appId, $wf_status = 2);
                //Helpers::updateWfStage('sanction_letter', $appId, $wf_status = 2);
                //Helpers::updateWfStage('upload_exe_doc', $appId, $wf_status = 2);
                $selRoleId = 6;
                $roles = $this->appRepo->getBackStageUsers($app_id, [$selRoleId]);
                $selUserId = $roles[0]->user_id;
                $selRoleStage = Helpers::getCurrentWfStagebyRole($selRoleId);                
                $currStage = Helpers::getCurrentWfStage($appId);
                Helpers::updateWfStageManual($appId, $selRoleStage->order_no, $currStage->order_no, $wf_status = 2, $selUserId, $addl_data);
            }
            
            //Insert Pre Sanctions Documents
            $prgmDocsWhere = [];
            $prgmDocsWhere['stage_code'] = 'upload_pre_sanction_doc';
            $appData = $this->appRepo->getAppDataByAppId($appId);
            $userId = $appData ? $appData->user_id : null;
            $reqdDocs = $this->createAppRequiredDocs($prgmDocsWhere, $userId, $appId);
            
            // $savedOfferData = $this->appRepo->saveOfferData($offerData, $offerId);
            $savedOfferData = $this->appRepo->updateActiveOfferByAppId($appId, $offerData);
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
        $data = $this->getSanctionLetterData($appId, $bizId, $offerId, $sanctionId);
        $supplyChaindata = $this->getSanctionLetterSupplyChainData($appId, $bizId, $offerId, $sanctionId);
        return view('backend.app.sanction_letter')->with($data)->with(['supplyChaindata'=>$supplyChaindata]);   
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

            return view('backend.app.send_sanction_letter')->with($data);

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
                $assignees = AppAssignment::getAppAssignees($app_id);

                $data = array();
                foreach($assignees as $key => $assignee){
                    $from_role_name = '';

                   
                    if($assignee->from_user_id){
                        $from_role_name = User::getUserRoles($assignee->from_user_id);
                        if($from_role_name->count()!=0)
                            $from_role_name = $from_role_name[0];
                    }

                    if($assignee->to_user_id){
                        $to_role_name = User::getUserRoles($assignee->to_user_id);
                        if($to_role_name->count()!=0)
                        $to_role_name = $to_role_name[0];
                    }else{
                        $to_role_name = Role::getRole($assignee->role_id);
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
                'validity_date'=>  Carbon::createFromFormat('d/m/Y', $request->sanction_validity_date)->format('Y-m-d')  , 
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
                'curr_status_id'=>config('common.mst_status_id')['DISBURSED'] 
            ];
            $appStatus = $this->appRepo->updateAppDetails($app_id,  $arrUpdateApp);           

             //Update workflow stage
             $addl_data = [];
             $currStage = Helpers::getCurrentWfStage($app_id);
             $wf_order_no = $currStage->order_no;
             $nextStage = Helpers::getNextWfStage($wf_order_no);
             $roleArr = [$nextStage->role_id];
             $roles = $this->appRepo->getBackStageUsers($app_id, $roleArr);
             $addl_data['to_id'] = isset($roles[0]) ? $roles[0]->user_id : null;            
             Helpers::updateWfStage('opps_checker', $app_id, $wf_status = 1, $assign_case=true, $addl_data);
             Helpers::updateCurrentWfStage('disbursed', $app_id, $wf_status=1);

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

}