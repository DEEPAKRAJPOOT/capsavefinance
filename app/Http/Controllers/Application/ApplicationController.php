<?php

namespace App\Http\Controllers\Application;
use Auth;
use Helpers;
use Session;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Requests\BusinessInformationRequest;
use App\Http\Requests\PartnerFormRequest;
use App\Http\Requests\DocumentRequest;
use Illuminate\Support\Facades\Storage;
use Eastwest\Json\Facades\Json;
use App\Inv\Repositories\Contracts\UserInterface as InvUserRepoInterface;
use App\Inv\Repositories\Contracts\ApplicationInterface as InvAppRepoInterface;
use App\Inv\Repositories\Contracts\DocumentInterface as InvDocumentRepoInterface;
use App\Inv\Repositories\Contracts\MasterInterface as InvMasterRepoInterface;
use App\Inv\Repositories\Models\Master\State;
use App\Inv\Repositories\Models\User;
use App\Libraries\KarzaTxn_lib;
use App\Libraries\MobileAuth_lib;
use App\Inv\Repositories\Models\BizApi;
use PDF;
use App\Inv\Repositories\Contracts\Traits\ApplicationTrait;

class ApplicationController extends Controller
{
    use ApplicationTrait;
    
    protected $appRepo;
    protected $userRepo;
    protected $docRepo;
    protected $masterRepo;

    public function __construct(InvAppRepoInterface $app_repo, InvUserRepoInterface $user_repo, InvDocumentRepoInterface $doc_repo, InvMasterRepoInterface $master_repo){
        $this->appRepo = $app_repo;
        $this->userRepo = $user_repo;
        $this->docRepo = $doc_repo;
        $this->masterRepo = $master_repo;
    }
    
    /**
     * Show the business information form.
     *
     * @return \Illuminate\Http\Response
     */
    public function showBusinessInformationForm(Request $request)
    {
        $userId  = Session::has('userId') ? Session::get('userId') : 0;
        $userArr = [];
        $product_ids = [];
        $states = State::getStateList()->get();
        $product_types = $this->masterRepo->getProductDataList();

        if ($userId > 0) {
            $userArr = $this->userRepo->find($userId);
        }
        if($request->has('__signature') && $request->has('biz_id')){
            $business_info = $this->appRepo->getApplicationById($request->biz_id);
            $app_data = $this->appRepo->getAppDataByBizId($request->biz_id);
            foreach($app_data->products as $product){
              array_push($product_ids, $product->pivot->product_id);
            }
            return view('frontend.application.company_details')
                        ->with(['business_info'=>$business_info, 'states'=>$states, 'product_types'=>$product_types, 'product_ids'=> $product_ids])
                        ->with('user_id',$request->get('user_id'))
                        ->with('app_id',$request->get('app_id'))
                        ->with('biz_id',$request->get('biz_id'));
        }else{
            return view('frontend.application.business_information', compact(['userArr', 'states', 'product_types']));
        }
    }

    public function saveBusinessInformation(BusinessInformationRequest $request)
    {
        try {
            $arrFileData = $request->all();

            if($request->has('__signature') && $request->has('biz_id')){
                $bizId = $request->biz_id;
                $business_info = $this->appRepo->updateCompanyDetail($arrFileData, $bizId, Auth::user()->user_id);

                if ($business_info) {
                    Session::flash('message',trans('success_messages.update_company_detail_successfully'));
                    return redirect()->route('promoter-detail',['app_id' =>  $request->app_id, 'biz_id' => $bizId, 'app_status'=>0]);
                } else {
                    return redirect()->back()->withErrors(trans('auth.oops_something_went_wrong'));
                }
            }else{
                $business_info = $this->appRepo->saveBusinessInfo($arrFileData, Auth::user()->user_id);
                
                //Add application workflow stages
                Helpers::updateWfStage('new_case', $business_info['app_id'], $wf_status = 1);
                
                            
                if ($business_info) {
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
       if($getCin==false)
       {
          return redirect()->back();
       }
      
        $OwnerPanApi = $this->userRepo->getOwnerApiDetail($attribute);
      // dd($OwnerPanApi);
        return view('frontend.application.promoter-detail')->with([
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
            
                Helpers::updateWfStage('promo_detail', $appId, $wf_status = 1);                                                
                $userData = $this->userRepo->getfullUserDetail($userId);
                if ($userData && !empty($userData->anchor_id)) {
                    $toUserId = $this->userRepo->getLeadSalesManager($userId);
                } else {
                    $toUserId = $this->userRepo->getAssignedSalesManager($userId);
                }                
                
                if ($toUserId) {
                   Helpers::assignAppToUser($toUserId, $appId);
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
            
            $document_info = $this->docRepo->saveDocument($arrFileData, $docId, $userId);
            if ($document_info) {
                $appId = $arrFileData['appId'];       
                $response = $this->docRepo->isUploadedCheck($userId, $appId);            
                $wf_status = $response->count() < 1 ? 1 : 2;
                Helpers::updateWfStage('doc_upload', $appId, $wf_status);
                
                Session::flash('message',trans('success_messages.uploaded'));

                return redirect()->route('document', ['app_id' => $appId, 'biz_id' => $bizId]);
            } else {
                //Add application workflow stages
                Helpers::updateWfStage('doc_upload', $request->get('appId'), $wf_status=2);

                return redirect()->route('document', ['app_id' => $appId, 'biz_id' => $bizId]);
            }
        } catch (Exception $ex) {
            //Add application workflow stages
            Helpers::updateWfStage('doc_upload', $request->get('appId'), $wf_status=2);
        
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
            $response = $this->docRepo->isUploadedCheck($userId, $appId);
            if ($response->count() < 1) {
                
                $this->appRepo->updateAppData($appId, ['status' => 1]);
                
                //Add application workflow stages                
                Helpers::updateWfStage('app_submitted', $appId, $wf_status = 1);
                
                return redirect()->route('front_dashboard')->with('message', trans('success_messages.app.completed'));
            } else {
                //Add application workflow stages                
                Helpers::updateWfStage('app_submitted', $request->get('app_id'), $wf_status = 2);
                
                return redirect()->back()->withErrors(trans('error_messages.app.incomplete'));
            }
        } catch (Exception $ex) {
            //Add application workflow stages                
            Helpers::updateWfStage('app_submitted', $request->get('app_id'), $wf_status = 2);
                
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
       return view('frontend.application.index');   
              
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
          $myfile = fopen(storage_path('app/public/user').'/'.$file_name, "w");
          \File::put(storage_path('app/public/user').'/'.$file_name, file_get_contents($json_decoded['pdfDownloadLink'])); 
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
        $res = json_decode($result->karza->res_file);
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
    $main_dir = storage_path('app/public/user/');
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
        if (!file_exists($final_dir)) {
          mkdir($final_dir, 0777, true);
        }
      }
      $my_file = $final_dir . $w_filename;
      $handle = fopen($my_file, 'w');
      return fwrite($handle, PHP_EOL . $data . PHP_EOL);
    } else {
      $my_file = $hour_dir . date('ymd') . '.log';
      $handle = fopen($my_file, 'a');
      $time = date('H:i:s');
      fwrite($handle, PHP_EOL . 'Log ' . $time);
      return fwrite($handle, PHP_EOL . $data . PHP_EOL);
    }
    return FALSE;
  }
}