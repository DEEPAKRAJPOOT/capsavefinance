<?php
namespace App\Http\Controllers\Backend;

use Auth;
use Crypt;
use Event;
use Session;
use Helpers;
use DateTime;
use File;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\ClientException;
use Illuminate\Http\UploadedFile;
use Carbon\Carbon;
use App\Helpers\Helper;
use Illuminate\Http\Request;
use App\Libraries\Gupshup_lib;
use App\Http\Controllers\Controller;
use App\Http\Requests\DocumentRequest;
use Illuminate\Support\Facades\Storage;
use App\Inv\Repositories\Models\Business;
use App\Inv\Repositories\Models\BizOwner;
use App\Http\Requests\PartnerFormRequest;
use App\Inv\Repositories\Models\UserFile;
use App\Inv\Repositories\Models\Application;
use App\Inv\Repositories\Models\UserCkycConsent;
use App\Inv\Repositories\Models\UserCkycApiLog;
use App\Inv\Repositories\Models\UserCkycDoc;
use App\Inv\Repositories\Models\UcicUserUcic;
use App\Inv\Repositories\Models\UserCkycReport;
use App\Http\Requests\BusinessInformationRequest;
use Illuminate\Support\Facades\Validator;
use App\Inv\Repositories\Models\UcicUser;
use App\Helpers\FileHelper;
use App\Libraries\Ui\KarzaApi;
use App\Inv\Repositories\Models\User;
use App\Inv\Repositories\Contracts\Traits\LmsTrait;
use App\Inv\Repositories\Contracts\Traits\ApplicationTrait;
use App\Inv\Repositories\Contracts\Traits\ActivityLogTrait;
use App\Inv\Repositories\Contracts\LmsInterface as InvLmsRepoInterface;
use App\Inv\Repositories\Contracts\UserInterface as InvUserRepoInterface;
use App\Inv\Repositories\Contracts\MasterInterface as InvMasterRepoInterface;
use App\Inv\Repositories\Contracts\ApplicationInterface as InvAppRepoInterface;
use App\Inv\Repositories\Contracts\DocumentInterface as InvDocumentRepoInterface;

class CkycController extends Controller
{

    use ApplicationTrait;
	use LmsTrait;
    protected $appRepo;
	protected $userRepo;
	protected $docRepo;
	protected $masterRepo;
	protected $lmsRepo;
	protected $UserInvRepo;
    protected $karzaApi;

    use ActivityLogTrait;

    public function __construct(InvAppRepoInterface $app_repo, InvUserRepoInterface $user_repo, InvDocumentRepoInterface $doc_repo, InvLmsRepoInterface $lms_repo, InvMasterRepoInterface $master_repo, FileHelper $file_helper, KarzaApi $karzaApi){
        ini_set("memory_limit", "-1");
        ini_set('max_execution_time', 10000);
		$this->appRepo = $app_repo;
		$this->userRepo = $user_repo;
		$this->docRepo = $doc_repo;
		$this->masterRepo = $master_repo;
		$this->lmsRepo = $lms_repo;
        $this->fileHelper = $file_helper;
        $this->karzaApi = $karzaApi;
	}

    public function index(Request $request){
        ini_set("memory_limit", "-1");
        ini_set('max_execution_time', 10000);
        $arrRequestData = $request->all();
        if($request->has('userUcicId') && !is_null($request->get('userUcicId'))){
            $ucic_id = $request->get('userUcicId');
            $ucic = UcicUser::find($ucic_id);
            $user_id = $ucic->user_id;
        }else{
            $user_id = $request->get('user_id');
            $where['user_id'] = $user_id;
            $ucic = UcicUser::getUcicData($where);
            $ucic_id = $ucic->user_ucic_id;
        }
        
        $bizData = $this->appRepo->getlatestBizDataByPan($ucic->pan_no);
        $where['user_ucic_user.ucic_id'] = $ucic->user_ucic_id;
        $arrCompanyOwnersData = $this->appRepo->getappByUcicId($where);
        
        $userData = $this->userRepo->getUserDetail($user_id);
        $BizDataArray = $this->appRepo->getBizDataByUserId($user_id)->first();
        $biz_id = $BizDataArray ? $BizDataArray->biz_id : '';
        $arrCompanyDetail = Business::getCompanyDataByBizId($biz_id);
        $reportWhere = ['ucic_id'=>$ucic_id];
        $companyCkycReport = $this->appRepo->getCompanyReport($reportWhere);
        
        $consentData = UserCkycConsent::where(['user_id'=>$user_id])->whereNull('biz_owner_id')->first();
        
        return view('backend.app.ckyc_details',compact('user_id','arrCompanyDetail','arrCompanyOwnersData','arrRequestData','consentData','userData','ucic','bizData','companyCkycReport'));
    }

    public function ckycApplicableSave(Request $request){
        try {
        $data = $request->all();
        $is_applicable = $data['is_applicable'];
        $userId = $data['user_id'];
        $biz_id = $data['biz_id'];
        $biz_owner_id = $data['biz_owner_id'];
        $ucic_id = $data['ucic_id'];
        $reportData =[];
        $reportData['ucic_id'] = $ucic_id;
        $reportData['user_id'] = $userId;
        $reportData['biz_id'] = $biz_id;
        $reportData['ckyc_applicable'] = (int)$is_applicable;
        if($biz_owner_id == '0'){
          $reportData['owner_typ'] = 'Company';
          $arrCompanyDetail = Business::getCompanyDataByBizId($biz_id)->toArray();
          $reportData['pan_no'] = $arrCompanyDetail[0]['pan_gst_hash'];
          $reportData['entity_name'] = $arrCompanyDetail[0]['biz_entity_name'];
          $reportData['entity_type'] = 1;
        }else{
          $reportData['biz_owner_id'] = $biz_owner_id;
          $bizOwner = $this->appRepo->getBizOwnerDataByOwnerId($biz_owner_id);
          if($bizOwner->applicant_type == '1')
              $reportData['owner_typ'] = 'Promoter';
          if($bizOwner->applicant_type == '2')
              $reportData['owner_typ'] = 'Management Person';
          if($bizOwner->applicant_type == '3')    
              $reportData['owner_typ'] = 'Co-Borrower';
          if($bizOwner->applicant_type == '4')     
              $reportData['owner_typ'] = 'Guarantor';
          if($bizOwner->applicant_type == '4')     
              $reportData['owner_typ'] = 'Authorised Signatory';
         
          $reportData['pan_no'] = $bizOwner->pan_number;
          $reportData['entity_name'] = $bizOwner->first_name;        
          $reportData['entity_type'] = 2;
        }
        if($biz_owner_id == '0'){
           $is_already_applied = UserCkycReport::where(['ucic_id'=>$ucic_id])->whereNull('biz_owner_id')->count();
           if($is_already_applied){

              $applied = UserCkycReport::where(['ucic_id'=>$ucic_id])->whereNull('biz_owner_id')->update(['ckyc_applicable'=>(int)$is_applicable]);
           }else{

               $applied = UserCkycReport::create($reportData);
           }
        }else{
            $is_already_applied = UserCkycReport::where(['ucic_id'=>$ucic_id,'biz_owner_id'=>$biz_owner_id])->count();
            if($is_already_applied){
                $applied = UserCkycReport::where(['ucic_id'=>$ucic_id,'biz_owner_id'=>$biz_owner_id])->update(['ckyc_applicable'=>(int)$is_applicable]);
            }else{
                $applied = UserCkycReport::create($reportData);
            }
        }

        if($applied)
            $response = ['status'=>200,'message'=>'ckyc applied successfully.']; 
        else
            $response = ['status'=>403,'message'=>'Something went wrong.'];
        
          $res = json_encode($response);
          return $res;

        } catch (Exception $ex) {
            $response = ['status'=>403,'message'=>'Something went wrong.'];
            $res = json_encode($response);
            return $res;
        } 
        
    }

    public function ckycOtpConsent(Request $request){
        try {

        $requestData = $request->all();
        $biz_id = $request->get('biz_id');
        $ucic_id = $request->get('userUcicId');
        $ucic = UcicUser::find($ucic_id);
        $user_id = $ucic->user_id;
        $ckyc_consent_id = $request->get('ckyc_consent_id');
        $userData = $this->userRepo->getUserDetail($user_id);
        if($userData){
            $user_id = $userData['user_id'];
            $is_consentotp['email'] = $userData['email'];
            $is_consentotp['otp_route'] = 'verify_otp_consent';
            $bizOwner=null;
            if(isset($requestData['biz_owner_id']) && !empty($requestData['biz_owner_id'])){
                $bizOwner = $this->appRepo->getBizOwnerDataByOwnerId($requestData['biz_owner_id']);
                if(trim($bizOwner->email) !== ""){
                    $is_consentotp['biz_owner_id'] = $requestData['biz_owner_id'];
                    $ckycConsentMailArr['name'] = $bizOwner->first_name.' '.$bizOwner->last_name;
                    $ckycConsentMailArr['email'] =  trim($bizOwner->email);
                    $ckycConsentMailArr['biz_owner_id'] =  $requestData['biz_owner_id'];
                    $ckycConsentMailArr['mobile_no'] = $bizOwner->mobile_no;
                    $ckycConsentMailArr['user_id'] = $bizOwner->user_id;
                }else{

                    Session::flash('error', trans('error_messages.email_not_given'));
                    Session::flash('operation_status', 1);
                    return redirect()->route('ckycdetails',['biz_id' => $biz_id, 'user_id'=>$user_id,'userUcicId'=>$ucic->user_ucic_id]);
                }
            }else{
                $ckycConsentMailArr['name'] = trim($userData['f_name']).' '.trim($userData['l_name']);
                $ckycConsentMailArr['email'] =  trim($userData['email']);
                $ckycConsentMailArr['mobile_no'] = $userData['mobile_no'];
                $ckycConsentMailArr['user_id'] = $userData['user_id'];
            }
            $app = $userData->app ? $userData->app : null;
            $business = $app && $app->business ? $app->business : null;
            $ckycConsentMailArr['ckyc_app_code'] = ($app && $app->app_code) ? $app->app_code : '';
            $ckycConsentMailArr['ckyc_biz_name'] = ($business && $business->biz_entity_name) ? $business->biz_entity_name : '';
            $verifyLink = Crypt::encrypt($ckycConsentMailArr['email']);
            $mailUrl = config('proin.frontend_uri') . '/otp/'.Crypt::encrypt($is_consentotp);
            $ckycConsentMailArr['url'] = $mailUrl;
            $verifyUser = $this->verifyUser($verifyLink,$ckycConsentMailArr);
            $ckycConsentDataData =[];
            $alertMessage = trans('backend_messages.user_otp_consent_sent');
            if($verifyUser){
                
                
                if(isset($requestData['ckyc_consent_id']) && !empty($requestData['ckyc_consent_id'])){
                    $ckycConsentData =[
                        'otp_trans_id'=>$verifyUser
                    ];
                    $where = ['ckyc_consent_id'=>$requestData['ckyc_consent_id']];
                    $this->userRepo->updateotpConsent($ckycConsentData, $where);
                }else{
                    $ckycConsentData = [
                        'consent_type' => 2,
                        'user_id' => $user_id,
                        'otp_trans_id' => $verifyUser,
                        'user_ucic_id' => $ucic->user_ucic_id
                    ];
                    if(isset($requestData['biz_owner_id']) && !empty($requestData['biz_owner_id'])){
                        
                        $ckycConsentData['user_id'] = $bizOwner->user_id;
                        $ckycConsentData['biz_owner_id'] = $requestData['biz_owner_id'];
                        $alertMessage = trans('backend_messages.partner_otp_consent_sent');
                    }
                    $this->userRepo->saveotpConsent($ckycConsentData);
                }
            }
            $whereActivi['activity_code'] = 'sent_ckyc_otp_consent';
            $activity = $this->masterRepo->getActivity($whereActivi);
            if(!empty($activity)) {
                $activity_type_id = isset($activity[0]) ? $activity[0]->id : 0;
                $activity_desc = 'Sent ckyc otp consent to user mail';
                $this->activityLogByTrait($activity_type_id, $activity_desc, response()->json($ckycConsentData));
            }
            Session::flash('message', $alertMessage);
            Session::flash('operation_status',1);
            return redirect()->route('ckycdetails',['biz_id' => $biz_id, 'user_id'=>$user_id,'userUcicId'=>$ucic->user_ucic_id]);
        }else{
                Session::flash('error', trans('error_messages.something_went_wrong'));
                Session::flash('operation_status', 1);
                return redirect()->route('ckycdetails',['biz_id' => $biz_id, 'user_id'=>$user_id,'userUcicId'=>$ucic->user_ucic_id]);
        }
        
        } catch (Exception $ex) {
            return redirect()->back()->withErrors(Helpers::getExceptionMessage($ex));
        } 
    }

    public function verifyUser($token, $ckycConsentMailArr=[]) {

        try {
            if (isset($token) && !empty($token)) {
                $email = Crypt::decrypt($token);
                if(isset($ckycConsentMailArr['biz_owner_id']) && !empty($ckycConsentMailArr['biz_owner_id'])){
                    $userCheckArr = (object)$ckycConsentMailArr;
                }
                else
                  $userCheckArr = $this->userRepo->getuserByEmail($email);

                if ($userCheckArr != false) {

                    $date = new DateTime;
                    $currentDate = $date->format('Y-m-d H:i:s');
                    $date->modify('+30 minutes');
                    $formatted_date = $date->format('Y-m-d H:i:s');
                    $userId = (int) $userCheckArr->user_id;
                    $userMailArr = []; $otpArr = [];
                    if(isset($ckycConsentMailArr['biz_owner_id']) && !empty($ckycConsentMailArr['biz_owner_id'])){
                        $otpArr['biz_owner_id'] = $ckycConsentMailArr['biz_owner_id'];
                    }
                    $Otpstring = mt_rand(1000, 9999);
                    $otpArr['otp_no'] = $Otpstring;
                    $otpArr['activity_id'] = 94;
                    $otpArr['user_id'] = $userId;
                    $otpArr['is_otp_expired'] = 0;
                    $otpArr['is_otp_resent'] = 0;
                    $otpArr['otp_exp_time'] = $formatted_date;
                    $otpArr['is_verified'] = 1;
                    $otpArr['mobile_no'] = $userCheckArr->mobile_no;
                    $sentOtp = $this->userRepo->saveOtp($otpArr);
                    $userMailArr['name'] = $ckycConsentMailArr['name'];
                    $userMailArr['email'] = $ckycConsentMailArr['email'];
                    $userMailArr['ckyc_app_code'] = $ckycConsentMailArr['ckyc_app_code'];
                    $userMailArr['ckyc_biz_name'] = $ckycConsentMailArr['ckyc_biz_name'];
                    
                    
                    $userMailArr['otp'] = $Otpstring;
                    $userMailArr['url'] = '';
                    if(isset($ckycConsentMailArr['url']) && !empty($ckycConsentMailArr)){
                        $userMailArr['url'] = $ckycConsentMailArr['url'];
                    }
                    
                    Event::dispatch("user.sendconsentotp", serialize($userMailArr));
                    
                    return $sentOtp?$sentOtp:false;

                } else {
                    return false;
                }
            } else {
                return false;
            }
        } catch (DecryptException $ex) {
            return redirect()->back()->withErrors(Helpers::getExceptionMessage($ex));
        }
    }

    public function ckycManualConsent(Request $request){
        $requestData = $request->all();
        return view('backend.app.ckyc_manual_consent',compact('requestData'));
    }

    public function saveManualConsent(Request $request){
        try{
            $validatedData = Validator::make($request->all(),[
                'consent_upload' => ['required', 'mimes:png,jpg,jpeg,pdf'],
                'consent_upload' => 'required',
                'comment' => 'required|max:100'

            ],[
                'consent_upload.required' => 'Please select file.',
                'comment.required' => 'Please enter comment.',
                'comment.max' => 'You can provide max. of 100 characters.',
                'consent_upload.required' => 'Please select file.',
                'consent_upload.mimes' => 'Please select only png, jpg, jpeg,pdf format.'
            ])->validate();
            
            $biz_id = $request->get('biz_id');
            $ucic_id = $request->get('userUcicId');
            $ucic = UcicUser::find($ucic_id);
            $user_id = $ucic->user_id;
            $ckyc_consent_id = false;
            $biz_owner_id = false;
            if ($request->has('ckyc_consent_id')){
                $ckyc_consent_id = $request->get('ckyc_consent_id');
            }

            if ($request->has('biz_owner_id')){
                $biz_owner_id = $request->get('biz_owner_id');
            }
            $consentData['user_ucic_id'] = $ucic->user_ucic_id;
            $consentData['comment'] = htmlspecialchars($request->get('comment'));
            $uploadedFile['doc_file'] = $request->file('consent_upload');
            $fileData = Helpers::uploadDirectoryFile($uploadedFile, 'ckyconsent');
            $savedFile = $this->docRepo->saveFile($fileData);
            $consentData['file_id'] = $savedFile->file_id;
            $consentData['otp_trans_id'] = null;
            $consentData['consent_type'] = 1;
            $consentData['status'] = 1;
            if($ckyc_consent_id){
                
                $uploadedConsent = $this->appRepo->updateConsentByConsentId($ckyc_consent_id,$consentData);

            }else{

                $where = ['user_id'=>(int)$user_id];
                if($biz_owner_id){
                $where['biz_owner_id'] = (int)$biz_owner_id;
                }
                $uploadedConsent = $this->appRepo->updateConsentByuserId($where,$consentData);

            }
            if($uploadedConsent){

                Session::flash('message', trans('backend_messages.user_manual_consent'));
                Session::flash('operation_status',1);
                Session::flash('is_accept', 1);
                return redirect()->back();
            }else{

                Session::flash('error', trans('error_messages.something_went_wrong'));
                Session::flash('operation_status', 1);
                return redirect()->back();

            }
        }catch (Exception $ex) {
            return redirect()->back()->withErrors(Helpers::getExceptionMessage($ex));
       }
        
    }

    public function ckycPullRequest(Request $request){

        try{
            
            $biz_id = $request->get('biz_id');
            $ucic_id = $request->get('userUcicId');
            $ucic = UcicUser::find($ucic_id);
            $user_id = $ucic->user_id;
            $userData = $this->userRepo->getUserDetail($user_id);
            $arrCompanyDetail = Business::getCompanyDataByBizId($biz_id)->toArray();
            $searchRequest['idType']  = 'pan';
            $searchRequest['consent'] = 'Y';
            $searchRequest['cersaiResponseRequired'] = false;
            $searchRequest['decryptedXmlRequired'] = false;
            $searchRequest['getMultipleRecord'] = 'Y';
            $search_ckyc_apilog_data['user_id'] = $user_id;
            $search_ckyc_apilog_data['api_type'] = 1;
            $search_ckyc_apilog_data['status'] = 0;
            $check_ckyc_where_clause['user_id'] = $user_id;
            if($request->has('biz_owner_id')){
              $biz_owner_id = $request->get('biz_owner_id'); 
              $check_ckyc_where_clause['biz_owner_id'] = $biz_owner_id;
              $bizOwner = $this->appRepo->getBizOwnerDataByOwnerId($biz_owner_id);
              $is_consent = $this->checkConsent($user_id,$biz_owner_id);
              if($is_consent){
                 if(isset($bizOwner['pan_number']) && !empty($bizOwner['pan_number'])){
                    
                    $searchRequest['idValue'] = $bizOwner['pan_number'];
                    $search_ckyc_apilog_data['biz_owner_id'] = $biz_owner_id;
                    $search_ckyc_apilog_data['request_type'] = 1;
                    
                 }else{

                    Session::flash('error', trans('error_messages.pan_not_given'));
                    Session::flash('operation_status', 1);
                    return redirect()->route('ckycdetails',['biz_id' => $biz_id, 'user_id'=>$user_id,'userUcicId'=>$ucic->user_ucic_id]);
                 }
              }else{
                 
                Session::flash('error', trans('error_messages.consent_not_applied'));
                Session::flash('operation_status', 1);
                return redirect()->route('ckycdetails',['biz_id' => $biz_id, 'user_id'=>$user_id,'userUcicId'=>$ucic->user_ucic_id]);
              }

            }else{
                $is_consent = $this->checkConsent($user_id);
                if($is_consent){
                    if(!empty($arrCompanyDetail) && isset($arrCompanyDetail[0]['pan_gst_hash']) && !empty($arrCompanyDetail[0]['pan_gst_hash'])){

                        $searchRequest['idValue'] = $arrCompanyDetail[0]['pan_gst_hash'];
                        $search_ckyc_apilog_data['request_type'] = 2;
                    }else{

                        Session::flash('error', trans('error_messages.pan_not_given'));
                        Session::flash('operation_status', 1);
                        return redirect()->route('ckycdetails',['biz_id' => $biz_id, 'user_id'=>$user_id,'userUcicId'=>$ucic->user_ucic_id]);
                    }
                    
                }else{

                    Session::flash('error', trans('error_messages.consent_not_applied'));
                    Session::flash('operation_status', 1);
                    return redirect()->route('ckycdetails',['biz_id' => $biz_id, 'user_id'=>$user_id,'userUcicId'=>$ucic->user_ucic_id]);
                }
            }
           
            $is_already_pulled = $this->checkCkycdocumentPulled($check_ckyc_where_clause);
            if ($is_already_pulled == false) {
            /*$download_api_response = json_decode($search_ckyc_log_finished, true);
            $this->saveCkycDownloadDocument($where, $download_api_response['result']);*/
            $search_ckyc_apilog_data['user_ucic_id'] = $ucic->user_ucic_id;    
            $search_ckyc_apilog_data['req_data'] = json_encode($searchRequest);
            $search_ckyc_log_iniate = UserCkycApiLog::create($search_ckyc_apilog_data);
            $pan_api_log_id = $search_ckyc_log_iniate->ckyc_api_log_id;
            if ($search_ckyc_log_iniate) {  
                $ckyc_search_api_response = $this->karzaApi->searchCKYCRequest($searchRequest);
                
                if(!is_null($ckyc_search_api_response)){
                    $api_response = json_decode($ckyc_search_api_response, true);
                    $user_ckyc_api_log_data['status'] = 0;
                    if((isset($api_response['statusCode']) &&  $api_response['statusCode']== 101)){
                        
                        $where['user_id'] = $user_id;
                        $user_ckyc_data['request_type'] = 2;
                        if($request->has('biz_owner_id')){
                            $where['biz_owner_id'] = $request->get('biz_owner_id');
                            $user_ckyc_data['biz_owner_id'] = $request->get('biz_owner_id');
                            $user_ckyc_data['request_type'] = 1;
                        }

                        $user_ckyc_api_log_data['status'] = 1;
                        //$user_ckyc_api_log_data['res_data'] = json_encode($api_response,true);
                        if($user_ckyc_data['request_type'] == 1){
                            
                            if(isset($api_response['result']['individual']) && !empty($api_response['result']['individual'])){
                                $user_ckyc_data['ckyc_no'] = $api_response['result']['individual'][0]['ckycNo'];
                            }else if(isset($api_response['result']['nonIndividual']) && !empty($api_response['result']['nonIndividual'])){
                                $user_ckyc_data['ckyc_no'] = $api_response['result']['nonIndividual'][0]['ckycNo'];
                            }else{
                                $user_ckyc_data['ckyc_no'] = $api_response['result']['ckycNo'];
                            }

                        }else{

                            if(isset($api_response['result']['nonIndividual']) && !empty($api_response['result']['nonIndividual'])){

                                $user_ckyc_data['ckyc_no'] = $api_response['result']['nonIndividual'][0]['ckycNo'];

                            }else if(isset($api_response['result']['individual']) && !empty($api_response['result']['individual'])){
                                
                                $user_ckyc_data['ckyc_no'] = $api_response['result']['individual'][0]['ckycNo'];

                            }else{
                                $user_ckyc_data['ckyc_no'] = $api_response['result']['ckycNo'];
                            }

                        }
                        
                        
                        $user_ckyc_data['pan_ckyc_api_log_id'] = $pan_api_log_id;
                        
                        $user_ckyc_data['user_ucic_id'] = $ucic->user_ucic_id; 
                        $user_ckyc_data['user_id'] = $user_id;
                        //$user_ckyc_data['request_type'] = ($api_response['result']['type'] === 'LEGAL_ENTITY')?2:1;
                        $resFileData = $this->ckycJsonSave(json_encode($api_response,true),$where);
                        $user_ckyc_api_log_data['res_file_id'] = $resFileData['file_id'];
                        $search_ckyc_log_finished = $this->userRepo->userckycApilogUpdate($pan_api_log_id, $user_ckyc_api_log_data);
                        $ckycSearchDataSaved = $this->userRepo->userckycCreateOrUpdate($where, $user_ckyc_data);
                        $downloadRequest = [];
                        if($ckycSearchDataSaved){
                            $downloadRequest['ckycId'] = $ckycSearchDataSaved->ckyc_no;
                            if($request->has('biz_owner_id')){
                                $biz_owner_id = $request->get('biz_owner_id');  
                                $bizOwner = $this->appRepo->getBizOwnerDataByOwnerId($biz_owner_id);
                                $downloadRequest['mobile'] = (!empty($bizOwner->mobile_no))?$bizOwner->mobile_no:'9999999999';
                                $downloadRequest['dob'] = $bizOwner->date_of_birth??'1982-06-02';
                                if(!empty($bizOwner->date_of_birth)){
                                    $explodeDOB = explode("-",$bizOwner->date_of_birth);
                                    $birthYear = $explodeDOB[0];
                                }else
                                 $birthYear = '1982';
                                 $downloadRequest['birthYear'] = $birthYear;
                                 $whereCond['biz_owner_id']=$biz_owner_id;
                                 $whereCond['biz_id'] = $biz_id;  
                                 $ownAddressesData  = $this->appRepo->ownaddress($biz_owner_id, $biz_id, $address_type=0);
                                 if(!empty($ownAddressesData) && !is_null($ownAddressesData)){
                                    $downloadRequest['pinCode'] = $ownAddressesData->pin_code;
                                 }else{
                                    $downloadRequest['pinCode'] = '387320';
                                 }

                            }else{
                                
                                $downloadRequest['dob'] = $arrCompanyDetail[0]['date_of_in_corp']??'1982-06-02';
                                if(!empty($arrCompanyDetail[0]['date_of_in_corp'])){
                                    $explodeDOB = explode("-",$arrCompanyDetail[0]['date_of_in_corp']);
                                    $birthYear = $explodeDOB[0];
                                }else
                                 $birthYear = '1982';

                                $downloadRequest['mobile'] = $userData->mobile_no??'9999999999';
                                $downloadRequest['birthYear'] = $birthYear;
                                $bizAddress = $this->appRepo->addressGetCustomers($user_id,$biz_id);
                                $bizAddress = $bizAddress->first();
                                if(!empty($bizAddress) && !is_null($bizAddress)){
                                    $downloadRequest['pinCode'] = $bizAddress->Pincode??'387320';
                                 }else{
                                    $downloadRequest['pinCode'] = '387320';
                                 }
                            }
                            $downloadRequest['consent'] = 'Y';
                            $downloadRequest['cersaiResponseRequired'] = false;
                            $downloadRequest['decryptedXmlRequired'] = false;
                            $downloadRequest['getMultipleRecord'] = 'Y';
                            $download_ckyc_apilog_data = $search_ckyc_apilog_data;
                            $download_ckyc_apilog_data['api_type'] = 2;
                            $download_ckyc_apilog_data['user_ucic_id'] = $ucic->user_ucic_id;
                            $download_ckyc_apilog_data['req_data'] = json_encode($downloadRequest);
                            $download_ckyc_log_iniate = UserCkycApiLog::create($download_ckyc_apilog_data);
                            if($download_ckyc_log_iniate){
                                $ckyc_download_api_response = $this->karzaApi->downloadCKYCRequest($downloadRequest);
                                if(!is_null($ckyc_download_api_response)){
                                    $doc_api_log_id = $download_ckyc_log_iniate->ckyc_api_log_id;
                                    $download_api_response = json_decode($ckyc_download_api_response, true);
                                    if((isset($download_api_response['statusCode']) &&  $download_api_response['statusCode']== 101)){
                                        
                                        $user_ckyc_api_log_data['status'] = 1;
                                        //$user_ckyc_api_log_data['res_data'] = json_encode($download_api_response,true);
                                        
                                        $user_ckyc_data['request_type'] = 2;
                                        $user_ckyc_data['status'] = 2;
                                        $user_ckyc_data['doc_ckyc_api_log_id'] = $doc_api_log_id;
                                        $where['user_id'] = $user_id;
                                        $ckyc_date = \Carbon\Carbon::now()->format('Y-m-d h:i:s');
                                        if($request->has('biz_owner_id')){
                                            $biz_owner_id = $request->get('biz_owner_id');
                                            $where['biz_owner_id'] = $request->get('biz_owner_id');
                                            $user_ckyc_data['biz_owner_id'] = $request->get('biz_owner_id');
                                            $user_ckyc_data['request_type'] = 1;
                                            UserCkycReport::where(['ucic_id'=>$ucic_id,'biz_owner_id'=>$biz_owner_id])->update(['ckyc_status'=>1,'ckyc_date'=>$ckyc_date]);
                                        }else{
                                            UserCkycReport::where(['ucic_id'=>$ucic_id])->whereNull('biz_owner_id')->update(['ckyc_status'=>1,'ckyc_date'=>$ckyc_date]);
                                        }

                                        $resFileData = $this->ckycJsonSave(json_encode($download_api_response,true),$where);
                                        $user_ckyc_api_log_data['res_file_id'] = $resFileData['file_id'];
                                        $download_ckyc_log_finished = $this->userRepo->userckycApilogUpdate($doc_api_log_id, $user_ckyc_api_log_data);
                                        $ckycDownloadDataSaved = $this->userRepo->userckycCreateOrUpdate($where, $user_ckyc_data);
                                        $where['user_ckyc_id'] = $ckycDownloadDataSaved->user_ckyc_id;
                                        $where['doc_api_log_id'] = $doc_api_log_id;
                                        $this->saveCkycDownloadDocument($where, $download_api_response['result']);
                                        if($ckycDownloadDataSaved){

                                            Session::flash('message', trans('backend_messages.ckyc_pulled_success'));
                                            Session::flash('operation_status',1);
                                            return redirect()->route('ckycdetails',['biz_id' => $biz_id, 'user_id'=>$user_id,'userUcicId'=>$ucic->user_ucic_id]);

                                        }else{

                                            Session::flash('error', trans('error_messages.something_went_wrong'));
                                            Session::flash('operation_status', 1);
                                            return redirect()->route('ckycdetails',['biz_id' => $biz_id, 'user_id'=>$user_id,'userUcicId'=>$ucic->user_ucic_id]);
                                        }
                                    }else{
                                        //$user_ckyc_api_log_data['res_data'] = json_encode($download_api_response,true);
                                        $where['user_id'] = $user_id;
                                        if($request->has('biz_owner_id')){
                                            $where['biz_owner_id'] = $request->get('biz_owner_id');
                                        }
                                        $user_ckyc_api_log_data['status'] = 2;
                                        $resFileData = $this->ckycJsonSave(json_encode($download_api_response,true),$where);
                                        $user_ckyc_api_log_data['res_file_id'] = $resFileData['file_id'];
                                        $download_ckyc_log_finished = $this->userRepo->userckycApilogUpdate($doc_api_log_id, $user_ckyc_api_log_data);
                                        if(isset($download_api_response['statusCode'])){
                                            $statusDesc = config('lms.CKYC_RES_STATUSCODE');
                                            $statusMessage = (string)$download_api_response['statusCode'].' -'.$statusDesc[$download_api_response['statusCode']];

                                        }else if(isset($download_api_response['status']) && isset($download_api_response['error'])){

                                            $statusMessage = (string)$download_api_response['status'].' -'.$download_api_response['error'];
                                        }else if(isset($download_api_response['message'])){

                                            $statusMessage = '502'.' -'.$download_api_response['message'];
                                        }else{

                                            $statusMessage = trans('error_messages.unable_fetch_ckyc_doc');
                                        }
                                        Session::flash('error', $statusMessage);
                                        Session::flash('operation_status', 1);
                                        return redirect()->route('ckycdetails',['biz_id' => $biz_id, 'user_id'=>$user_id,'userUcicId'=>$ucic->user_ucic_id]);
                                    }
                                }else{

                                    Session::flash('error', trans('error_messages.ckyc_API_not_working'));
                                    Session::flash('operation_status', 1);
                                    return redirect()->route('ckycdetails',['biz_id' => $biz_id, 'user_id'=>$user_id,'userUcicId'=>$ucic->user_ucic_id]);
                                }
                            }else{

                                Session::flash('error', trans('error_messages.something_went_wrong'));
                                Session::flash('operation_status', 1);
                                return redirect()->route('ckycdetails',['biz_id' => $biz_id, 'user_id'=>$user_id,'userUcicId'=>$ucic->user_ucic_id]);
                            }

                        }
                    }else{
                        //$user_ckyc_api_log_data['res_data'] = json_encode($api_response,true);
                        $user_ckyc_api_log_data['status'] = 2;
                        $where['user_id'] = $user_id;
                        if($request->has('biz_owner_id')){
                            $where['biz_owner_id'] = $request->get('biz_owner_id');
                        }
                        $user_ckyc_api_log_data['status'] = 2;
                        $resFileData = $this->ckycJsonSave(json_encode($api_response,true),$where);
                        $user_ckyc_api_log_data['res_file_id'] = $resFileData['file_id'];
                        $search_ckyc_log_finished = $this->userRepo->userckycApilogUpdate($pan_api_log_id, $user_ckyc_api_log_data);
                        $statusDesc = config('lms.CKYC_RES_STATUSCODE');
                        
                        if(isset($api_response['statusCode'])){
                            $statusDesc = config('lms.CKYC_RES_STATUSCODE');
                            $statusMessage = $api_response['statusCode'].' -'.$statusDesc[$api_response['statusCode']];

                        }else if(isset($api_response['status']) && isset($api_response['error'])){

                            $statusMessage = (string)$api_response['status'].' -'.$api_response['error'];
                        }else if(isset($api_response['message'])){

                            $statusMessage = '502'.' -'.$api_response['message'];
                        }else{
                            $statusMessage = trans('error_messages.ckyc_data_not_found');
                        }

                        Session::flash('error', $statusMessage);
                        Session::flash('operation_status', 1);
                        return redirect()->route('ckycdetails',['biz_id' => $biz_id, 'user_id'=>$user_id,'userUcicId'=>$ucic->user_ucic_id]);
                    }
                }else{

                    Session::flash('error', trans('error_messages.ckyc_API_not_working'));
                    Session::flash('operation_status', 1);
                    return redirect()->route('ckycdetails',['biz_id' => $biz_id, 'user_id'=>$user_id,'userUcicId'=>$ucic->user_ucic_id]);
                }
            } else{

                Session::flash('error', trans('error_messages.something_went_wrong'));
                Session::flash('operation_status', 1);
                return redirect()->route('ckycdetails',['biz_id' => $biz_id, 'user_id'=>$user_id,'userUcicId'=>$ucic->user_ucic_id]);
            }  
         } else{

            Session::flash('error', trans('error_messages.ckyc_already_process'));
            Session::flash('operation_status', 1);
            return redirect()->route('ckycdetails',['biz_id' => $biz_id, 'user_id'=>$user_id,'userUcicId'=>$ucic->user_ucic_id]);
         } 
            
        }catch (Exception $ex) {
            return redirect()->back()->withErrors(Helpers::getExceptionMessage($ex));
        }
    }

    public function saveCkycDownloadDocument($userInfo, $download_api_response){

        

       if(isset($download_api_response['identityDetails']) && !empty($download_api_response['identityDetails'])){
        if (count($download_api_response['identityDetails']) != count($download_api_response['identityDetails'], COUNT_RECURSIVE)) {
            foreach($download_api_response['identityDetails'] as $identityDetails){
                if(isset($identityDetails['imageInfo'])){

                    $mstdocData = $this->appRepo->getckycDocs($identityDetails['imageInfo']['imageType']);
                    $fileData = Helper::ckycUploadDocument($identityDetails,$userInfo);
                    $savedFile = $this->docRepo->saveFile($fileData);
                    $documentData['file_id'] = $savedFile->file_id;
                    $documentData['ckyc_api_log_id'] = $userInfo['doc_api_log_id'];
                    $documentData['user_ckyc_id'] = $userInfo['user_ckyc_id'];
                    $documentData['doc_id'] = $mstdocData?$mstdocData->doc_id:null;
                    $documentData['file_type'] = $identityDetails['type'];
                    UserCkycDoc::create($documentData);
                } 
            }
         }else{
             
            if(isset($download_api_response['identityDetails']['imageInfo'])){

                $mstdocData = $this->appRepo->getckycDocs($download_api_response['identityDetails']['imageInfo']['imageType']);
                $fileData = Helper::ckycUploadDocument($download_api_response['identityDetails'],$userInfo);
                $savedFile = $this->docRepo->saveFile($fileData);
                $documentData['file_id'] = $savedFile->file_id;
                $documentData['ckyc_api_log_id'] = $userInfo['doc_api_log_id'];
                $documentData['user_ckyc_id'] = $userInfo['user_ckyc_id'];
                $documentData['doc_id'] = $mstdocData?$mstdocData->doc_id:null;
                $documentData['file_type'] = $download_api_response['identityDetails']['type'];
                UserCkycDoc::create($documentData);
            }
         }
       }

       if(isset($download_api_response['imageDetails']) && !empty($download_api_response['imageDetails'])){
        if (count($download_api_response['imageDetails']) != count($download_api_response['imageDetails'], COUNT_RECURSIVE)) {
            foreach($download_api_response['imageDetails'] as $imageDetails){
                $mstdocData = $this->appRepo->getckycDocs($imageDetails['imageType']);
                $fileData = Helper::ckycUploadDocument($imageDetails,$userInfo);
                $savedFile = $this->docRepo->saveFile($fileData);
                $documentData['file_id'] = $savedFile->file_id;
                $documentData['ckyc_api_log_id'] = $userInfo['doc_api_log_id'];
                $documentData['user_ckyc_id'] = $userInfo['user_ckyc_id'];
                $documentData['doc_id'] = $mstdocData?$mstdocData->doc_id:null;
                $documentData['file_type'] = $imageDetails['imageType'];
                UserCkycDoc::create($documentData);
            }
        }else{

            $mstdocData = $this->appRepo->getckycDocs($download_api_response['imageDetails']['imageType']);
            $fileData = Helper::ckycUploadDocument($download_api_response['imageDetails'],$userInfo);
            $savedFile = $this->docRepo->saveFile($fileData);
            $documentData['file_id'] = $savedFile->file_id;
            $documentData['ckyc_api_log_id'] = $userInfo['doc_api_log_id'];
            $documentData['user_ckyc_id'] = $userInfo['user_ckyc_id'];
            $documentData['doc_id'] = $mstdocData?$mstdocData->doc_id:null;
            $documentData['file_type'] = $download_api_response['imageDetails']['imageType'];
            UserCkycDoc::create($documentData);
        }
        

     }

       

       return true;
    }

    public function checkConsent($user_id,$biz_owner_id=null){

        
        if(!is_null($biz_owner_id)){
            
            $where['biz_owner_id'] = $biz_owner_id;
            $where['status'] = 1;
        }else{
            $where = ['user_id'=>$user_id,'status'=>1];
        }

        return $this->appRepo->getUserConsent($where);
    }

    public function checkCkycdocumentPulled($where){

        return $this->userRepo->checkCkycdocumentPulled($where);
    }

    public function ckycJsonSave($response,$userInfo){
       $fileData = Helper::ckycJsonResponseAsDocument($response,$userInfo);
       $savedFile = $this->docRepo->saveFile($fileData);
       $documentData['file_id'] = $savedFile->file_id;
       
       return $documentData;
    }

    public static function getCKYCResponse($res_file_id){

        $fileData = UserFile::find($res_file_id);
        return Storage::get($fileData['file_path']);
    }

}