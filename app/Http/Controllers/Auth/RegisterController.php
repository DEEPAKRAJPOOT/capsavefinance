<?php

namespace App\Http\Controllers\Auth;

use File;
use Crypt;
use Event;
use Helpers;
use Session;
use DateTime;
use Auth;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\RegistersUsers;
use App\Http\Requests\RegistrationFormRequest;
use App\Http\Requests\NonAnchorLeadRegistrationRequest;
use App\Http\Requests\PartnerFormRequest;
use App\Http\Requests\BusinessDocumentRequest;
use Illuminate\Contracts\Encryption\DecryptException;
use App\Inv\Repositories\Contracts\UserInterface as InvUserRepoInterface;
use App\Inv\Repositories\Contracts\ApplicationInterface as InvAppRepoInterface;
use App\Inv\Repositories\Libraries\Storage\Contract\StorageManagerInterface;
use App\Inv\Repositories\Contracts\Traits\StorageAccessTraits;
use App\Inv\Repositories\Contracts\Traits\ApiAccessTrait;
use App\Inv\Repositories\Models\DocumentMaster;
use App\Inv\Repositories\Models\UserReqDoc;
use App\Inv\Repositories\Models\Userkyc;
use App\Inv\Repositories\Models\Business;
use App\Inv\Repositories\Models\UserDetail;
use App\Libraries\Gupshup_lib;

class RegisterController extends Controller {
    /*
      |--------------------------------------------------------------------------
      | Register Controller
      |--------------------------------------------------------------------------
      |
      | This controller handles the registration of new users as well as their
      | validation and creation. By default this controller uses a trait to
      | provide this functionality without requiring any additional code.
      |
     */

use RegistersUsers,
    StorageAccessTraits,
    ApiAccessTrait;

    /**
     * User repository
     *
     * @var object
     */
    protected $userRepo;
    protected $businessRepo;

    /**
     * Where to redirect users after registration.
     *
     * @var string
     */
    protected $redirectTo = '/dashboard';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct(InvUserRepoInterface $user, InvAppRepoInterface $application) {
        $this->middleware('guest');
        $this->userRepo = $user;
        $this->application = $application;
    }

    /**
     * Create a new user instance after a valid registration.
     *
     * @param  array  $data
     * @return \App\User
     */
    protected function create(array $data) {
//        dd($data);
        $arrData = [];
        $arrAnchUser=[];
        $arrDetailData = [];
        $arrLeadAssingData =[];
        //$userDataArray = $this->userRepo->getUserByAnchorId($data['h_anchor_id']);        
        //$arrData['anchor_user_id'] = $userDataArray->user_id;        
        $lead_type = $data['lead_type'];
        $arrData['anchor_id'] = $data['h_anchor_id'];
        $arrData['f_name'] = $data['f_name'];
        //$arrData['m_name'] = $data['m_name'];
        $arrData['l_name'] = $data['l_name'];
        $arrData['biz_name'] = $data['business_name'];
//        $arrData['pan_no'] = $data['pan_no'];
        $arrData['email'] = $data['email'];
        $arrData['password'] = bcrypt($data['password']);
        $arrData['mobile_no'] = $data['mobile_no'];
        $arrData['user_type'] = 1;
        $arrData['is_email_verified'] = 0;
        $arrData['is_pwd_changed'] = 0;
        $arrData['is_otp_verified'] = 0;
        $arrData['is_buyer'] = $lead_type;
        $arrData['parent_id'] = 0;
        $arrData['is_active'] = 0;
        // $arrData['supplier_code'] = isset($data['supplier_code']) ? $data['supplier_code'] : null;
        $userId = null;
        
        $userData = $this->userRepo->getUserByemail($data['email']);
        if ($userData && $userData->user_id) {
            $userDataArray = $userData;
        } else {
            $userDataArray = $this->userRepo->save($arrData, $userId);
            if ($userDataArray) {
                $detailArr['user_id'] = $userDataArray->user_id;
                $detailArr['access_token'] = bcrypt($userDataArray->email);
                $detailArr['created_by'] = $userDataArray->user_id;
                $this->userRepo->saveUserDetails($detailArr);
            }
        }
        
        if ($userDataArray) {

            if (isset($data['anch_user_id']) && !empty($data['anch_user_id'])) {
                
                //Associate Business
                //$bizData = $this->application->getBizDataByPan($data['pan_no']);
                //if (isset($bizData[0])) {
                //    $bizId = $bizData[0]->biz_id;
                //} else {
                    /*
                    $insBizData=[];
                    $insBizData['user_id'] = $userDataArray->user_id;
                    $newBizData = $this->application->createBusiness($insBizData);
                    $bizId = $newBizData->biz_id;

                    $bizPanGstArrData=[];
                    $bizPanGstArrData['user_id']= $userDataArray->user_id;
                    $bizPanGstArrData['biz_id'] = $bizId;
                    $bizPanGstArrData['type']   = 1;
                    $bizPanGstArrData['pan_gst_hash'] = $data['pan_no'];
                    $this->appRepo->saveBizPanGstData($bizPanGstArrData);
                     * 
                     */
                    //$bizId = null;
                //}
            
            
                $arrAnchUser['is_registered']=1;

                //$arrAnchUser['token']='';
                $arrAnchUser['user_id']=$userDataArray->user_id;
                $arrAnchUser['pan_no']=$data['pan_no'];
                //$arrAnchUser['biz_id']=$bizId;
                $arrAnchUser['anchor_id']=$data['h_anchor_id'];                
                //$anchId=$this->userRepo->getAnchorUsersByEmail($userDataArray->email);            
                $this->userRepo->updateAnchorUser($data['anch_user_id'], $arrAnchUser);
            }
            
            $saleMngId=$this->userRepo->getLeadSalesManager($userDataArray->user_id);
            
              $arrLeadAssingData = [
                'from_id' => $userDataArray->user_id,
                'to_id' => $saleMngId,
                  'is_owner'=>1,
                'assigned_user_id' => $userDataArray->user_id,             
                'created_by' => $userDataArray->user_id,
                'created_at' => \Carbon\Carbon::now(),
                ];
                     $this->userRepo->createLeadAssign($arrLeadAssingData);
            
            //Add application workflow stages
            Helpers::addWfAppStage('new_case', $userDataArray->user_id);                          
        }
        return $userDataArray;
    }

    /**
     * Create a new company user instance after a valid registration.
     *
     * @param  array  $data
     * @return \App\User
     */
    protected function compcreate(array $data) {
        $arrData = [];
        $arrKyc = [];
        $arrKycData = [];
        $userName = $this->changeuserName($data['first_name'], $data['last_name'], $data['phone']);
        $arrData['f_name'] = $data['first_name'];
        $arrData['m_name'] = $data['middle_name'];
        $arrData['l_name'] = $data['last_name'];
        $arrData['email'] = $data['email'];
        $arrData['username'] = $userName;
        $arrData['phone_no'] = $data['phone'];
        $dateofBirth = str_replace('/', '-', $data['dob']);
        $arrData['date_of_birth'] = date('Y-m-d', strtotime($dateofBirth));
        $arrData['user_type'] = 2;
        $arrData['is_email_verified'] = 0;
        $arrData['is_pwd_changed'] = 0;
        $arrData['is_email_verified'] = 0;
        $arrData['is_otp_verified'] = 0;
        $arrData['is_active'] = 0;
        $arrData['is_active'] = 0;
        $userId = null;
        $userDataArray = $this->userRepo->save($arrData, $userId);
        if ($userDataArray->user_id > 0) {
            $arrDetailData['user_id'] = $userDataArray->user_id;
            $arrDetailData['country_id'] = $data['country_id'];
            $arrDetailData['corp_name'] = $data['company_name'];
            $arrDetailData['corp_license_number'] = $data['comp_trade_in'];
            $dateofCorpration = str_replace('/', '-', $data['comp_dof']);
            $arrDetailData['corp_date_of_formation'] = date('Y-m-d', strtotime($dateofCorpration));
            $CorpDetail = $this->userRepo->saveCorpDetails($arrDetailData);



            $arrKyc['user_id'] = $CorpDetail->user_id;
            $arrKyc['corp_detail_id'] = $CorpDetail->corp_detail_id;
            $arrKyc['is_by_company'] = 0;
            $arrKyc['is_approve'] = 0;
            $arrKyc['is_kyc_completed'] = 0;
            $arrKyc['is_api_pulled'] = 0;
            $kycDetail = $this->userRepo->saveKycDetails($arrKyc);
            $arrKycData['user_kyc_id'] = $kycDetail->kyc_id;
            $this->userRepo->save($arrKycData, $CorpDetail->user_id);
        }

        return $userDataArray;
    }

    /**
     * Show the application registration form.
     *
     * @return \Illuminate\Http\Response
     */
    public function showRegistrationForm(Request $request) {
           try{
                $anchortoken = $request->get('token');
                $userId = Session::has('userId') ? Session::get('userId') : 0;
                $userArr = [];
                $anchorDetail = [];
                if ($userId > 0) {
                    $userArr = $this->userRepo->find($userId);
                }
                $anchorLeadInfo = $this->userRepo->getAnchorUsersByToken($anchortoken);
                // dd($anchorLeadInfo);
                if(!empty($anchorLeadInfo) && $anchorLeadInfo->is_registered == 1){
                   $email = $anchorLeadInfo->email;
                   return redirect(route('otp', ['token' => Crypt::encrypt($email)]));
                } else if(!empty($anchorLeadInfo) && $anchorLeadInfo->is_registered == 0){
                    $anchorDetail = $anchorLeadInfo;
                }else{
                    $anchorDetail = '';
                    return redirect(route('login_open'));
                }
                return view('auth.sign-up', compact('userArr','anchorDetail'));
           
           }catch (Exception $ex) {
            return redirect()->back()->withErrors(Helpers::getExceptionMessage($ex));
         }
    }

    /**
     * Show the application registration form.
     *
     * @return \Illuminate\Http\Response
     */
    public function showCompRegistrationForm() {

        $userId = Session::has('userId') ? Session::get('userId') : 0;
        $userArr = [];
        if ($userId > 0) {
            $userArr = $this->userRepo->find($userId);
        }
        return view('auth.company-sign-up', compact('userArr'));
    }

    /**
     * Handle a registration request for the application.
     *
     * @param  \Illuminate\Http\RegistrationFormRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function register(RegistrationFormRequest $request, StorageManagerInterface $storage) {

        try {
            $data = [];
            $arrFileData = [];
            $arrFileData = $request->all();
            $AnchorData = $this->userRepo->getAnchorByPan($arrFileData['pan_no']);                        
            $user = $this->create($arrFileData);
            /// dd($user);
            if ($user) {
                if (!Session::has('userId')) {
                    Session::put('userId', (int) $user->user_id);
                }

                $whereCond=[];
                $whereCond[] = ['pan_no', '=', $arrFileData['pan_no']];         
                $whereCond[] = ['email', '=', trim($arrFileData['email'])];
                $whereCond[] = ['anchor_id', '!=', $arrFileData['h_anchor_id']];
                $whereCond[] = ['is_registered', '=', '1'];
                $AnchorData = $this->userRepo->getAnchorUserData($whereCond);        
                if (isset($AnchorData[0])) {
                    $userMailArr=[];
                    $userMailArr['name'] = $user->f_name . ' ' . $user->l_name;
                    $userMailArr['email'] = $user->email;
                    Event::dispatch("NOTIFY_EXISTING_USER", serialize($userMailArr));

                    Session::flash('message', trans('success_messages.registration_success'));
                    return redirect(route('login_open'));             
                } else {            

                    /// $this->sendVerificationLink($user->user_id);
                    $verifyLink = Crypt::encrypt($user['email']);
                    $this->verifyUser($verifyLink);
                    Session::flash('message', trans('success_messages.basic_saved_successfully'));
                    //return redirect(route('education_details'));
                    // return redirect()->route('otp', ['token' => Crypt::encrypt($user['email'])]);
                    return redirect()->route('otp', Crypt::encrypt($user['email']));
                }                
            } else {
                return redirect()->back()->withErrors(trans('auth.oops_something_went_wrong'));
            }
        } catch (Exception $ex) {
            return redirect()->back()->withErrors(Helpers::getExceptionMessage($ex));
        }
    }

    /**
     * Handle a registration request for the application.
     *
     * @param  \Illuminate\Http\RegistrationFormRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function compregister(RegistrationFormRequest $request, StorageManagerInterface $storage) {


        try {
            $data = [];
            $arrFileData = [];
            $arrFileData = $request->all();
            //dd($arrFileData);
            //echo "ddddssssd"; exit;
            //Saving data into database
            //unset($user);
            $user = $this->compcreate($arrFileData);



            if ($user) {
                if (!Session::has('userId')) {
                    Session::put('userId', (int) $user->user_id);
                }
                // echo $user->id; exit;
                $verifyLink = route('verify_email', ['token' => Crypt::encrypt($user['email'])]);
                $this->sendVerificationLink($user->user_id);
                Session::flash('message', trans('Registration is done successfully.'));
                //return redirect(route('education_details'));
                return redirect()->route('thanks');
            } else {
                return redirect()->back()->withErrors(trans('auth.oops_something_went_wrong'));
            }
        } catch (Exception $ex) {
            return redirect()->back()->withErrors(Helpers::getExceptionMessage($ex));
        }
    }

    /**
     * Show the application Thanks page.
     *
     * @return \Illuminate\Http\Response
     */
    public function showThanksForm() {

        $userId = Session::has('userId') ? Session::get('userId') : 0;
        $userArr = [];
        if ($userId > 0) {
            $userArr = $this->userRepo->find($userId);
        }
        return view('auth.thanks', compact('userArr'));
    }

    /**
     * Show the consent Thanks page.
     *
     * @return \Illuminate\Http\Response
     */

    public function showConsentThanksForm(){

        $userId = Session::has('userId') ? Session::get('userId') : 0;
        $userArr = [];
        if ($userId > 0) {
            $userArr = $this->userRepo->find($userId);
        }
        return view('auth.consent-thanks', compact('userArr'));
    }

    /**
     * Show the application OTP page.
     *
     * @return \Illuminate\Http\Response
     */
    public function otpForm($token) {
        $tokenarr = [];
        $userArr = [];
        $date = new DateTime;
        $currentDate = $date->format('Y/m/d H:i:s');
        if (isset($token) && !empty($token)) {
            $decryptedData = Crypt::decrypt($token);
            if(is_array($decryptedData) && isset($decryptedData['otp_route'])){
                $email = $decryptedData['email'];
                $redirectUrl = $decryptedData['otp_route'];
                $otp_type = 2;
                $decryptedData['otp_type'] = $otp_type;
                $token = Crypt::encrypt($decryptedData);
            }else{
                $email = Crypt::decrypt($token);
                $redirectUrl = 'verify_otp';
                $otp_type = 1;
            }
            $tokenarr['otp_type'] = $otp_type;
            $tokenarr['redirectUrl'] = $redirectUrl;
            $tokenarr['token'] = $token;
            $userArr = $this->userRepo->getUserByEmailforOtp($email);
        }
    
        if (isset($userArr)) {
            return view('auth.otp', compact('userArr'), compact('tokenarr'));
        } else {
            return redirect()->route('/otp');
        }
    }

    /**
     * Send Verification link to user to verify email
     * @param Integer $userId
     */
    protected function sendVerificationLinkold($userId) {

       // $Otpstring = Helpers::randomOTP();
        $Otpstring = mt_rand(1000, 9999);
        $userArr['otp'] = $Otpstring;
        $this->userRepo->save($userArr, $userId);

        $userArr = [];
        $userArr = $this->userRepo->find($userId, ['email', 'first_name', 'last_name']);
        $verifyUserArr = [];
        $verifyUserArr['name'] = $userArr->first_name . ' ' . $userArr->last_name;
        $verifyUserArr['email'] = $userArr->email;
        $verifyUserArr['otp'] = $Otpstring;

        Event::fire("user.email.verify", serialize($verifyUserArr));
    }

    protected function sendVerificationLink($userId) {
        $userArr = [];
        $userArr = $this->userRepo->find($userId, ['email', 'f_name', 'l_name']);
        $verifyLink = route('verify_email', ['token' => Crypt::encrypt($userArr['email'])]);
        $verifyUserArr = [];
        $verifyUserArr['name'] = $userArr->f_name . ' ' . $userArr->l_name;
        $verifyUserArr['email'] = $userArr->email;
        $verifyUserArr['vlink'] = $verifyLink;
        Event::dispatch("user.email.verify", serialize($verifyUserArr));
        //echo "debug11111"; exit;
        //exit;
    }

    /**
     * Verifying user email
     * 
     * @param string $token
     * @return Response
     */
    public function verifyUser($token) {

        try {
            if (isset($token) && !empty($token)) {
                $email = Crypt::decrypt($token);
                $userCheckArr = $this->userRepo->getuserByEmail($email);

                // echo "==>".count($userCheckArr); exit;
                if ($userCheckArr != false) {
                    /* if ($userCheckArr->status == config('inv_common.USER_STATUS.Active')) {
                      return redirect(route('login_open'))->withErrors(trans('error_messages.email_already_verified'));
                      } */

                    $date = new DateTime;
                    $currentDate = $date->format('Y-m-d H:i:s');
                    $date->modify('+30 minutes');
                    $formatted_date = $date->format('Y-m-d H:i:s');

                    $userId = (int) $userCheckArr->user_id;
                    $userArr = [];
                    $userArr['is_email_verified'] = 1;
                    $userArr['is_pwd_changed'] = 1;
                    $userArr['email_verified_updatetime'] = $currentDate;
                    $this->userRepo->save($userArr, $userId);
                    //save opt
                    // echo "Current Date :->".$date;
                    $userMailArr = [];
                    $otpArr = [];
                    $Otpstring = mt_rand(1000, 9999);
                    ///$Otpstring = Helpers::randomOTP();
                    $otpArr['otp_no'] = $Otpstring;
                    $otpArr['activity_id'] = 1;
                    $otpArr['user_id'] = $userId;
                    $otpArr['is_otp_expired'] = 0;
                    $otpArr['is_otp_resent'] = 0;
                    $otpArr['otp_exp_time'] = $formatted_date;
                    $otpArr['is_verified'] = 1;
                    $otpArr['mobile_no'] = $userCheckArr->mobile_no;
                    $this->userRepo->saveOtp($otpArr);
                    $userMailArr['name'] = $name = $userCheckArr->f_name . ' ' . $userCheckArr->l_name;
                    $userMailArr['email'] = $userCheckArr->email;
                    $userMailArr['otp'] = $Otpstring;
                    $gupshup = new Gupshup_lib();
                    $mobile_no = $userCheckArr->mobile_no;
                    $otp_msg = "Dear $name,\r\n OTP:$Otpstring is your otp to verify your mobile on Capsave.\r\n Regards";
                    // Send OTP mobile to User
                    $otp_resp = $gupshup->api_call(['mobile'=>$mobile_no, 'message' => $otp_msg]);
                    //if ($otp_resp['status'] != 'success') {
                       // Send OTP mail to User
                       Event::dispatch("user.sendotp", serialize($userMailArr));
                    //}
                    Session::flash('message_div', trans('success_messages.email_verified_please_login'));

                    $alluserData = $this->userRepo->getUserDetail((int) $userId);
                    //$verifyLink             = route('verify_email', ['token' => Crypt::encrypt($userArr['email'])]);
                    // return redirect()->route('otp', ['token' => Crypt::encrypt($userMailArr['email'])]);
                    return redirect()->route('otp', Crypt::encrypt($userMailArr['email']));
                } else {
                    return redirect(route('login_open'))->withErrors(trans('error_messages.invalid_token'));
                }
            } else {
                return redirect(route('login_open'))->withErrors(trans('error_messages.data_not_found'));
            }
        } catch (DecryptException $ex) {
            return redirect()->back()->withErrors(Helpers::getExceptionMessage($ex));
        }
    }

    /**
     * Verifying user OTP
     *
     * @param string $token
     * @return Response
     */
    public function verifyotpUser(Request $request) {

        $otp = $request->get('otp');
        $token = $request->get('token');
        $otp_type = $request->get('otp_type');
        if($otp_type == '1'){
          $email = Crypt::decrypt($request->get('token'));
          $message = trans('error_messages.enter_corr_otp');
        }else if($otp_type == '2'){
          $tokenData = Crypt::decrypt($request->get('token'));
          $email = $tokenData['email'];
          $message = trans('error_messages.enter_consent_corr_otp');
        }
          
        
        try {
            if (isset($otp) && !empty($otp)) {

                /////Get user id behalf of email////////////
                $userDetails = $this->userRepo->getUserByEmail($email);
                $isOtpExpired = false;
                if($otp_type == '2'){
                    if(isset($tokenData['biz_owner_id']) && !empty($tokenData['biz_owner_id'])){
                        $bizOwner = $this->application->getBizOwnerDataByOwnerId($tokenData['biz_owner_id']);
                        $userCheckArr = $this->userRepo->getConsentUserByOTP($otp, $bizOwner->user_id,$tokenData['biz_owner_id']);
                        $whereC=['otp_no'=>$otp, 'user_id'=>$bizOwner->user_id,'biz_owner_id'=>$tokenData['biz_owner_id'],'is_otp_expired'=>1,'activity_id'=>94];
                    }else{
                        $userCheckArr = $this->userRepo->getConsentUserByOTP($otp, $userDetails->user_id);
                        $whereC=['otp_no'=>$otp, 'user_id'=>$userDetails->user_id,'is_otp_expired'=>1,'activity_id'=>94];
                    }
                   $isOtpExpired = $this->userRepo->getOtpExpired($whereC);
                }else{
                    $userCheckArr = $this->userRepo->getUserByOPT($otp, $userDetails->user_id);
                }
                if ($userCheckArr != false) {
                    /* if ($userCheckArr->status == config('inv_common.USER_STATUS.Active')) {
                      return redirect(route('otp'))->withErrors(trans('error_messages.email_already_verified'));
                      } */
                    $userId = (int) $userCheckArr->user_id;
                    $otp_trans_id = (int) $userCheckArr->otp_trans_id;
                    if($otp_type == '1'){
                        $userMailArr = [];
                        $userArr = [];
                        $date = new DateTime;
                        $currentDate = $date->format('Y-m-d H:i:s');
                        $userArr['is_otp_verified'] = 1;
                        $userArr['is_otp_resent'] = 0;
                        $userArr['otp_verified_updatetime'] = $currentDate;
                        $userArr['is_active'] = 1;
                        $userCheckArr = $this->userRepo->getfullUserDetail($userId);
                        $this->userRepo->save($userArr, $userId);
                        $arrAnchUser['token']='';                   
                        $this->userRepo->updateAnchorUserByEmailId($userCheckArr->email, $arrAnchUser);
                        $userMailArr['name'] = $userCheckArr->f_name . ' ' . $userCheckArr->l_name;
                        $userMailArr['email'] = $userCheckArr->email;
                        if(Auth::loginUsingId($userDetails->user_id)) {
                            
                            if ($userDetails->is_pwd_changed != 1) {
                                return redirect()->route('changepassword');
                            }
                            
                            $appData = $this->application->checkAppByPan($userId); 
                            if ($appData) {
                                //Session::flash('message', trans('error_messages.active_app_check'));                            
                                return redirect()->route('front_application_list');
                            } else {
                                return redirect()->route('business_information_open');
                            }       
                        }
                    }else{
                        $ckycConsentData = [
                            'status' => 1
                        ];
                        $where = ['user_id'=>$userId, 'otp_trans_id'=>$otp_trans_id];
                        $result = $this->userRepo->updateotpConsent($ckycConsentData, $where);
                        if($result){
                            $arrUpdateOtp = [];
                            $arrUpdateOtp['is_otp_expired'] = 1;
                            $arrUpdateOtp['is_otp_resent'] = 3;
                            $this->userRepo->updateOtp($arrUpdateOtp, (int) $otp_trans_id);
                            return redirect()->route('consentthanks');
                        }else{
                            return redirect(route('otp', ['token' => $token]))->withErrors(trans('error_messages.data_not_found'));
                        }
                    }
                } else {
                    $tokenData = $otp_type == '2' ? $tokenData : $email;
                    if ($isOtpExpired && $otp_type == '2'){
                        return redirect(route('otp', ['token' => Crypt::encrypt($tokenData)]))->withErrors(trans('error_messages.consent_otp_expired'));
                    }
                    return redirect(route('otp', ['token' => Crypt::encrypt($tokenData)]))->withErrors($message);
                }
            } else {
                $tokenData = $otp_type == '2' ? $tokenData : $email;
                return redirect(route('otp', ['token' => Crypt::encrypt($tokenData)]))->withErrors(trans('error_messages.data_not_found'));
            }
        } catch (DecryptException $ex) {
            return redirect()->back()->withErrors(Helpers::getExceptionMessage($ex));
        }
    }

    public function resendotpUser(Request $request) {
        $decryptedData = Crypt::decrypt($request->get('token'));
        
        if(is_Array($decryptedData)){
            $email = $decryptedData['email'];
            $otp_type = (int)$decryptedData['otp_type'];
        }else{
            $email = $decryptedData;
            $otp_type = 1;
        }
        $userMailArr = [];
        $i = 0;
        $userCheckArr = $this->userRepo->getuserByEmail($email);
        $userId = (int) $userCheckArr->user_id;
        $date = new DateTime;
        $currentDate = $date->format('Y-m-d H:i:s');
        $date->modify('+30 minutes');
        $formatted_date = $date->format('Y-m-d H:i:s');
        $otpArr = [];
        //$Otpstring = Helpers::randomOTP();
        $Otpstring = mt_rand(1000, 9999);
        if(is_Array($decryptedData) && isset($decryptedData['otp_type']) && $decryptedData['otp_type'] == 2){
            if(isset($decryptedData['biz_owner_id']) && !empty($decryptedData['biz_owner_id'])){
                $bizOwner = $this->application->getBizOwnerDataByOwnerId($decryptedData['biz_owner_id']);
                $where = ['user_id'=>(int)$bizOwner->user_id,'biz_owner_id'=>$decryptedData['biz_owner_id']];
            }else{
                $where = ['user_id'=>(int)$userId];
            }
            $countOtp = $this->userRepo->getConsentOtps($where)->toArray();
        }else{
            $countOtp = $this->userRepo->getOtps($userId)->toArray();
        }
        
        
        if (!empty($countOtp)) {
            $firstData = $countOtp[0]['otp_exp_time'];
            $updatedTime = new DateTime($firstData);
            $currentDate = new DateTime();
            $interval = $updatedTime->diff($currentDate);
            $hours = $interval->format('%h');
            $minutes = $interval->format('%i');
            $expireTime = ($hours * 60 + $minutes);
            if ($expireTime >= 30) {
                $this->userRepo->updateOtpByExpiry(['is_otp_expired' => 1], $userId);
                $this->userRepo->updateOtpByExpiry(['is_otp_resent' => 3], $userId);

                if(is_Array($decryptedData) && isset($decryptedData['biz_owner_id']) && !empty($decryptedData['biz_owner_id'])){
                    $otpArr['biz_owner_id'] = $decryptedData['biz_owner_id']; 
                }
                $otpArr['otp_no'] = $Otpstring;
                $otpArr['activity_id'] = ($otp_type == 2)?94:1;
                $otpArr['user_id'] = $userId;
                $otpArr['is_otp_expired'] = 0;
                $otpArr['is_otp_resent'] = 0;
                $otpArr['otp_exp_time'] = $currentDate;
                $otpArr['is_verified'] = 1;
                $otpArr['mobile_no'] = $userCheckArr->mobile_no;
                $otpSaved = $this->userRepo->saveOtp($otpArr);
                if($otp_type == 2){
                  
                    $ckycConsentData = [
                        'otp_trans_id' => $otpSaved
                    ];
                    $app = $userCheckArr->app ? $userCheckArr->app : null;
                    $business = $app && $app->business ? $app->business : null;
                    if(isset($decryptedData['biz_owner_id']) && !empty($decryptedData['biz_owner_id'])){
                        $bizOwner = $this->application->getBizOwnerDataByOwnerId($decryptedData['biz_owner_id']);
                        $is_consentotp['biz_owner_id'] = $decryptedData['biz_owner_id'];
                        $userCheckArr=(object)array();
                        $userCheckArr->f_name = $bizOwner->first_name ?? '';
                        $userCheckArr->l_name = $bizOwner->last_name ?? '';
                        $userCheckArr->email =  trim($bizOwner->email);
                        $userCheckArr->biz_owner_id =  $decryptedData['biz_owner_id'];
                        $userCheckArr->mobile_no = $bizOwner->mobile_no;
                        $userCheckArr->user_id = $bizOwner->user_id;
                        $where = ['user_id'=>$bizOwner->user_id,'biz_owner_id'=>$decryptedData['biz_owner_id']];
                        $this->userRepo->updateotpConsent($ckycConsentData, $where);
                    }else{
                        $this->userRepo->updateBusinessotpConsent($ckycConsentData, $userId);
                    }
                    $is_consentotp['email'] = $decryptedData['email'];
                    $is_consentotp['otp_route'] = 'verify_otp_consent';

                    $mailUrl = config('proin.frontend_uri') . '/otp/'.Crypt::encrypt($is_consentotp);
                    $ckycConsentMailArr['name'] = trim($userCheckArr->f_name) . ' ' . trim($userCheckArr->l_name);
                    $ckycConsentMailArr['email'] =  trim($userCheckArr->email);
                    $ckycConsentMailArr['url'] = $mailUrl;
                    $ckycConsentMailArr['otp'] = $Otpstring;
                    $ckycConsentMailArr['ckyc_app_code'] = ($app && $app->app_code) ? $app->app_code : '';
                    $ckycConsentMailArr['ckyc_biz_name'] = ($business && $business->biz_entity_name) ? $business->biz_entity_name : '';
                    Event::dispatch("user.sendconsentotp", serialize($ckycConsentMailArr));
                    return redirect(route('otp', ['token' => Crypt::encrypt($decryptedData)]))->withErrors(trans('success_messages.otp_sent_messages'));

                }else{

                    $userMailArr['name'] = $name =$userCheckArr->f_name . ' ' . $userCheckArr->l_name;
                    $userMailArr['email'] = $userCheckArr->email;
                    $userMailArr['otp'] = $Otpstring;
                    $gupshup = new Gupshup_lib();
                    $mobile_no = $userCheckArr->mobile_no;
                    $otp_msg = "Dear $name,\r\n OTP:$Otpstring is your otp to verify your mobile on Capsave.\r\n Regards";
    
                    $otp_resp = $gupshup->api_call(['mobile'=>$mobile_no, 'message' => $otp_msg]);
    
                    Event::dispatch("user.sendotp", serialize($userMailArr));
                    return redirect(route('otp', ['token' => Crypt::encrypt($email)]))->withErrors(trans('success_messages.otp_sent_messages'));
                }
                
            } else {
                
                if(is_Array($decryptedData) && isset($decryptedData['otp_type']) && $decryptedData['otp_type'] == 2){
                    if(isset($decryptedData['biz_owner_id']) && !empty($decryptedData['biz_owner_id'])){
                        $bizOwner = $this->application->getBizOwnerDataByOwnerId($decryptedData['biz_owner_id']);
                        $where = ['user_id'=>(int)$bizOwner->user_id,'biz_owner_id'=>$decryptedData['biz_owner_id']];
                    }else{
                        $where = ['user_id'=>(int)$userId];
                    }
                    $countOtp = $this->userRepo->getConsentOtps($where)->toArray();
                }else{
                    $countOtp = $this->userRepo->getOtps($userId)->toArray();
                }

                if (isset($countOtp) && count($countOtp) >= 3) {
                    $decryptedData = $otp_type == '2' ? $decryptedData : $email;
                    return redirect(route('otp', ['token' => Crypt::encrypt($decryptedData)]))->withErrors(trans('success_messages.otp_attempts_finish'));
                } else {
                    
                    if(is_Array($decryptedData) && isset($decryptedData['otp_type']) && $decryptedData['otp_type'] == 2){
                        if(isset($decryptedData['biz_owner_id']) && !empty($decryptedData['biz_owner_id'])){
                            $bizOwner = $this->application->getBizOwnerDataByOwnerId($decryptedData['biz_owner_id']);
                            $where = ['user_id'=>(int)$bizOwner->user_id,'biz_owner_id'=>$decryptedData['biz_owner_id']];
                        }else{
                            $where = ['user_id'=>(int)$userId];
                        }
                        $prev_otp = $this->userRepo->getConsentOtpsbyActive($where)->toArray();
                    }else{
                        $prev_otp = $this->userRepo->getOtpsbyActive($userId)->toArray();
                    }

                   
                    $savedOtp = null;
                    if (isset($prev_otp) && count($prev_otp) == 1) {
                        $arrUpdateOtp = [];
                        $arrUpdateOtp['is_otp_expired'] = 1;
                        $arrUpdateOtp['otp_exp_time'] = $currentDate;
                        //dd($prev_otp[0]['otp_trans_id']);
                        $this->userRepo->updateOtp($arrUpdateOtp, (int) $prev_otp[0]['otp_trans_id']);
                        if(is_Array($decryptedData) && isset($decryptedData['biz_owner_id']) && !empty($decryptedData['biz_owner_id'])){
                            $otpArr['biz_owner_id'] = $decryptedData['biz_owner_id']; 
                        }
                        $otpArr['otp_no'] = $Otpstring;
                        $otpArr['activity_id'] = ($otp_type == 2)?94:1;
                        $otpArr['user_id'] = $userId;
                        $otpArr['is_otp_expired'] = 0;
                        $otpArr['is_otp_resent'] = 0;
                        $otpArr['otp_exp_time'] = $currentDate;
                        $otpArr['is_verified'] = 1;
                        $otpArr['mobile_no'] = $userCheckArr->mobile_no;
                       $savedOtp =  $this->userRepo->saveOtp($otpArr);
                    }
                    if($otp_type == 2){
                        $ckycConsentData = [
                            'otp_trans_id' => $savedOtp
                        ];
                        $app = $userCheckArr->app ? $userCheckArr->app : null;
                        $business = $app && $app->business ? $app->business : null;
                        if(isset($decryptedData['biz_owner_id']) && !empty($decryptedData['biz_owner_id'])){
                            $is_consentotp['biz_owner_id'] = $decryptedData['biz_owner_id'];
                            $bizOwner = $this->application->getBizOwnerDataByOwnerId($decryptedData['biz_owner_id']);
                            $userCheckArr=(object)array();
                            $userCheckArr->f_name = $bizOwner->first_name ?? '';
                            $userCheckArr->l_name = $bizOwner->last_name ?? '';
                            $userCheckArr->email =  trim($bizOwner->email);
                            $userCheckArr->biz_owner_id =  $decryptedData['biz_owner_id'];
                            $userCheckArr->mobile_no = $bizOwner->mobile_no;
                            $userCheckArr->user_id = $bizOwner->user_id;
                            $where = ['user_id'=>$bizOwner->user_id,'biz_owner_id'=>$decryptedData['biz_owner_id']];
                            $this->userRepo->updateotpConsent($ckycConsentData, $where);
                        }else{
                            $this->userRepo->updateBusinessotpConsent($ckycConsentData, $userId);
                        }
                        $is_consentotp['email'] = $decryptedData['email'];
                        $is_consentotp['otp_route'] = 'verify_otp_consent';
    
                        $mailUrl = config('proin.frontend_uri') . '/otp/'.Crypt::encrypt($is_consentotp);
                        $ckycConsentMailArr['name'] = trim($userCheckArr->f_name) . ' ' . trim($userCheckArr->l_name);
                        $ckycConsentMailArr['email'] =  trim($userCheckArr->email);
                        $ckycConsentMailArr['url'] = $mailUrl;
                        $ckycConsentMailArr['otp'] = $Otpstring;
                        $ckycConsentMailArr['ckyc_app_code'] = ($app && $app->app_code) ? $app->app_code : '';
                        $ckycConsentMailArr['ckyc_biz_name'] = ($business && $business->biz_entity_name) ? $business->biz_entity_name : '';
                        Event::dispatch("user.sendconsentotp", serialize($ckycConsentMailArr));
                        return redirect(route('otp', ['token' => Crypt::encrypt($decryptedData)]))->withErrors(trans('success_messages.otp_sent_messages'));
    
                    }else{
                        $userMailArr['name'] = $name = $userCheckArr->f_name . ' ' . $userCheckArr->l_name;
                        $userMailArr['email'] = $userCheckArr->email;
                        $userMailArr['otp'] = $Otpstring;
                        $gupshup = new Gupshup_lib();
                        $mobile_no = $userCheckArr->mobile_no;
                        $otp_msg = "Dear $name,\r\n OTP:$Otpstring is your otp to verify your mobile on Capsave.\r\n Regards";

                        $otp_resp = $gupshup->api_call(['mobile'=>$mobile_no, 'message' => $otp_msg]);
                        Event::dispatch("user.sendotp", serialize($userMailArr));
                        return redirect(route('otp', ['token' => Crypt::encrypt($email)]))->withErrors(trans('success_messages.otp_sent_messages'));
                    }
                    
                }
            }
        }
    }

    /**
     * Show the application OTP page.
     *
     * @return \Illuminate\Http\Response
     */
    public function verifiedotpUser() {

        $userId = Session::has('userId') ? Session::get('userId') : 0;
        $userArr = [];
        if ($userId > 0) {
            $userArr = $this->userRepo->find($userId);
        }
        return view('auth.otp-thanks', compact('userArr'));
    }

    public function changePassword(Request $request) {
        //echo "<pre>";
        //print_r($request);

        $userId = Session::has('userId') ? Session::get('userId') : 0;

        $userArr = [];
        if ($userId > 0) {
            $userArr = $this->userRepo->find($userId);
        }
        return view('auth.otp-thanks', compact('userArr'));

        //===========================


        if (!(Hash::check($request->get('current-password'), Auth::user()->password))) {
            // The passwords matches
            return redirect()->back()->with("error", "Your old password does not matches with the password you provided. Please try again.");
        }
        if (strcmp($request->get('current-password'), $request->get('new-password')) == 0) {
            //Current password and new password are same
            return redirect()->back()->with("error", "New Password cannot be same as your old password. Please choose a different password.");
        }
        $validatedData = $request->validate([
            'current-password' => 'required',
            'new-password' => 'required|string|min:6|confirmed',
        ]);
        //Change Password
        $user = Auth::user();
        $user->password = bcrypt($request->get('new-password'));
        $user->save();
        return redirect()->back()->with("success", "Password changed successfully !");
    }

    ////make userName
    public function changeuserName($fistName, $lastName, $phone) {
        //echo "<pre>";
        //print_r($request);
        $fistNameNew = substr($fistName, 0, 3);
        $lastNameNew = substr($lastName, 0, 3);
        $phoneNew = substr($phone, 0, 4);
        $userName = $fistNameNew . $lastNameNew . $phoneNew;
        return $userName;
    }

    public function checkExistUserPan(Request $request)
    {
        $email = $request->get('email');
        $pan = $request->get('pan');
        $assocAnchId = $request->get('anchor_id');
        $validate = $request->get('validate');
        $result = [];
        $result['message'] = '';
        $result['status'] = true;
        $result['validate'] = $validate;
        $whereCond=[];
        $whereCond[] = ['email', '=', $email];
        $whereCond[] = ['anchor_id', '=', $assocAnchId];
        $whereCond[] = ['is_registered', '=', '1'];
        $anchUserData = $this->userRepo->getAnchorUserData($whereCond);
        if (isset($anchUserData[0])) {
            $result['validate'] = 0;
            $result['status'] = false;
            $result['message'] = trans('success_messages.existing_email');
            return response()->json($result);
        } else {        
            $whereCond=[];       
            $whereCond[] = ['email', '=', $email];
            $whereCond[] = ['pan_no', '!=', $pan];
            $whereCond[] = ['is_registered', '=', '1'];
            $AnchorData = $this->userRepo->getAnchorUserData($whereCond);             
            if (isset($AnchorData[0])) { 
                $result['validate'] = 0;
                $result['status'] = false;
                $result['message'] = trans('success_messages.existing_email');
                return response()->json($result);
            } else {
                $whereCond=[];       
                $whereCond[] = ['pan_no', '=', $pan];
                $whereCond[] = ['is_registered', '=', '1'];
                $whereCond[] = ['anchor_id', '=', $assocAnchId];
                $AnchorData = $this->userRepo->getAnchorUserData($whereCond); 
                if(!isset($AnchorData[0])) {
                    $whereCond=[];       
                    $whereCond[] = ['pan_no', '=', $pan];
                    $whereCond[] = ['email', '=', $email];
                    $whereCond[] = ['is_registered', '=', '1'];
                    $AnchorData =   $this->userRepo->getNonAnchorUserData($whereCond);  
                   }
                if (isset($AnchorData[0])) { 
                    $result['validate'] = 0;
                    $result['status'] = false;
                    $click_here = "<a href='".url('/')."'>Click here</a>";
                    $result['message'] = 'User already registered with this Pan no.<br> Please '. $click_here .' to login with old credentials.';
                    return response()->json($result);
                } 

            }
        }
        
        $whereCond=[];
        $whereCond[] = ['pan_no', '=', $pan];         
        $whereCond[] = ['email', '=', $email];
        $whereCond[] = ['anchor_id', '!=', $assocAnchId];
        $whereCond[] = ['is_registered', '=', '1'];
        $AnchorData = $this->userRepo->getAnchorUserData($whereCond);        
        if (!empty($pan) && isset($AnchorData[0])) {
            $result['validate'] = '0';
            $result['status'] = false;
            $result['message'] = trans('success_messages.register_different_anchor');
            return response()->json($result);
        }
                
        return response()->json($result);
    }

    public function showtermCondition() {

        return view('auth.term-condition');
    }
    
    /**
     * Show the non anchor lead application registration form.
     *
     * @return \Illuminate\Http\Response
     */
    public function showRegistrationFormForNonAnchorLead(Request $request)
    {
        try{
            $token = $request->get('token');
            $leadInfo = $this->userRepo->getNonAnchorLeadByRegToken($token);
            
            $userArr = [];
            if(!empty($leadInfo) && $leadInfo->is_registered == 1){
               $email = $leadInfo->email;
            //    return redirect(route('otp', ['token' => Crypt::encrypt($email)]));
               return redirect()->route('otp', Crypt::encrypt($email));
            } else if(!empty($leadInfo) && $leadInfo->is_registered == 0){
                $leadDetail = $leadInfo;
            }else{
                $leadDetail = '';
                return redirect(route('login_open'));
            }
            return view('auth.non_anchor_lead_sign_up', compact('userArr','leadDetail'));
        
        }catch (Exception $ex) {
            return redirect()->back()->withErrors(Helpers::getExceptionMessage($ex));
        }
    }

    public function registerNonAnchorLead(NonAnchorLeadRegistrationRequest $request, StorageManagerInterface $storage)
    {
        try {
            \DB::beginTransaction();

            $arrFileData = $request->all();
            $user = $this->nonAnchorLeadCreate($arrFileData);

            if ($user) {
                if (!Session::has('userId')) {
                    Session::put('userId', (int) $user->user_id);
                }
                $verifyLink = Crypt::encrypt($user['email']);
                $this->verifyUser($verifyLink);
                Session::flash('message', trans('success_messages.basic_saved_successfully'));

                \DB::commit();                
                return redirect()->route('otp', Crypt::encrypt($user['email']));
            } else {
                \DB::rollback();
                return redirect()->back()->withErrors(trans('auth.oops_something_went_wrong'));
            }
        } catch (Exception $ex) {
            \DB::rollback();
            return redirect()->back()->withErrors(Helpers::getExceptionMessage($ex));
        }
    }

    /**
     * Create a new user instance after a valid registration.
     *
     * @param  array  $data
     * @return \App\User
     */
    protected function nonAnchorLeadCreate(array $data)
    {
        $arrData['f_name'] = $data['f_name'];
        $arrData['l_name'] = $data['l_name'];
        $arrData['biz_name'] = $data['business_name'];
        $arrData['email'] = $data['email'];
        $arrData['password'] = bcrypt($data['password']);
        $arrData['mobile_no'] = $data['mobile_no'];
        $arrData['user_type'] = 1;
        $arrData['is_pwd_changed'] = 0;
        $arrData['is_email_verified'] = 0;
        $arrData['is_otp_verified'] = 0;
        $arrData['is_buyer'] = $data['lead_type'];
        $arrData['parent_id'] = 0;
        $arrData['is_active'] = 0;
        $userId = null;

        $userData = $this->userRepo->getUserByemail($data['email']);
        
        if ($userData && $userData->user_id) {
            $userDataArray = $userData;
        } else {
            $userDataArray = $this->userRepo->save($arrData, $userId);
            if ($userDataArray) {
                $detailArr['user_id'] = $userDataArray->user_id;
                $detailArr['access_token'] = bcrypt($userDataArray->email);
                $detailArr['created_by'] = $userDataArray->user_id;
                $this->userRepo->saveUserDetails($detailArr);
            }
        }

        if ($userDataArray) {
            if (isset($data['non_anchor_lead_id']) && !empty($data['non_anchor_lead_id'])) {                                                    
                $arrNonAnchUser['is_registered'] = 1;
                $arrNonAnchUser['user_id'] = $userDataArray->user_id;
                $arrNonAnchUser['pan_no']  = $data['pan_no'];
                $arrNonAnchUser['is_term_accept']  = $data['is_term_accept'];
                $this->userRepo->updateNonAnchorLead(['id' => $data['non_anchor_lead_id']], $arrNonAnchUser);
            }
            
            $nonAnchorLead = $this->userRepo->getNonAnchorLeadById($data['non_anchor_lead_id']);
            if ($nonAnchorLead) {
                $arrLeadAssingData = [
                    'from_id'  => $userDataArray->user_id,
                    'to_id'    => $nonAnchorLead->assign_sale_manager,
                    'is_owner' => 1,
                    'assigned_user_id' => $userDataArray->user_id,          
                    'created_by' => $userDataArray->user_id,
                    'created_at' => \Carbon\Carbon::now(),
                ];
                $this->userRepo->createLeadAssign($arrLeadAssingData);
            }
            
            //Add application workflow stages
            Helpers::addWfAppStage('new_case', $userDataArray->user_id);                          
        }
        return $userDataArray;
    }

    public function getExistEmailStatusForNonAnchorLead(Request $req)
    {        
        $email = $req->get('email');
        $status = $this->userRepo->getUserByEmail(trim($email));
        $response['status'] = $status ? false : true;        
        
        return response()->json($response);
    }
    
    public function getExistPanUserStatusForNonAnchorLead(Request $req)
    {
        $pan    = $req->get('pan');
        $email    = $req->get('email');
        $status = $this->userRepo->getNonAnchorLeadByPan(trim($pan));
        if(!$status) {
                $whereCond=[];       
                $whereCond[] = ['pan_no', '=', $pan];
                $whereCond[] = ['is_registered', '=', '1'];
                $checkstatus = $this->userRepo->getAnchorUserData($whereCond);
                $status = isset($checkstatus[0]) ? true : false;
                if(!$status) {
                    $whereCond=[]; 
                    $whereCond[] = ['email', '=', $email];      
                    $whereCond[] = ['pan_no', '=', $pan];
                    $whereCond[] = ['is_registered', '=', '1'];
                    $checkstatus = $this->userRepo->getAnchorUserData($whereCond);
                    $status = isset($checkstatus[0]) ? true : false; 
                    if($status) {
                        $click_here = "<a href='".url('/')."'>Click here</a>"; 
                        $result['validate'] = $status ? '0' : '1';        
                        $result['message'] = 'User already registered with this Pan no.<br> Please '. $click_here .' to login with old credentials.';
                        return response()->json($result);
                    }
                }
        } else {
            $whereCond=[];       
            $whereCond[] = ['pan_no', '=', $pan];
            $whereCond[] = ['email', '=', $email];
            $whereCond[] = ['is_registered', '=', '1'];
            $AnchorData =   $this->userRepo->getNonAnchorUserData($whereCond);
            if (isset($AnchorData[0])) { 
                $result['validate'] = 0;
                $result['status'] = false;
                $click_here = "<a href='".url('/')."'>Click here</a>";
                $result['message'] = 'User already registered with this Pan no.<br> Please '. $click_here .' to login with old credentials.';
                return response()->json($result);
            } 

        }
        $result['validate'] = $status ? '0' : '1';        
        $result['message']  = $status ?  : '';        
        return response()->json($result);
    }


    public function checkExistUserPanNonAnchor(Request $request)
    {
        $email = $request->get('email');
        $pan = $request->get('pan');
        $assocAnchId = $request->get('anchor_id');
        $validate = $request->get('validate');
        $result = [];
        $result['message'] = '';
        $result['status'] = true;
        $result['validate'] = $validate;
        $whereCond=[];
        $whereCond[] = ['email', '=', $email];
        $whereCond[] = ['anchor_id', '=', $assocAnchId];
        $whereCond[] = ['is_registered', '=', '1'];
        $anchUserData = $this->userRepo->getAnchorUserData($whereCond);
        if (isset($anchUserData[0])) {
            $result['validate'] = 0;
            $result['status'] = false;
            $result['message'] = trans('success_messages.existing_email');
            return response()->json($result);
        } else {        
            $whereCond=[];       
            $whereCond[] = ['email', '=', $email];
            $whereCond[] = ['pan_no', '!=', $pan];
            $whereCond[] = ['is_registered', '=', '1'];
            $AnchorData = $this->userRepo->getAnchorUserData($whereCond);             
            if (isset($AnchorData[0])) { 
                $result['validate'] = 0;
                $result['status'] = false;
                $result['message'] = trans('success_messages.existing_email');
                return response()->json($result);
            } else {
                $whereCond=[];       
                $whereCond[] = ['pan_no', '=', $pan];
                $whereCond[] = ['is_registered', '=', '1'];
                $whereCond[] = ['anchor_id', '=', $assocAnchId];
                $AnchorData = $this->userRepo->getAnchorUserData($whereCond); 
                if (isset($AnchorData[0])) { 
                    $result['validate'] = 0;
                    $result['status'] = false;
                    $click_here = "<a href='".url('/')."'>Click here</a>";
                    $result['message'] = 'User already registered with this Pan no.<br> Please '. $click_here .' to login with old credentials.';
                    return response()->json($result);
                }

            }
        }
        
        $whereCond=[];
        $whereCond[] = ['pan_no', '=', $pan];         
        $whereCond[] = ['email', '=', $email];
        $whereCond[] = ['anchor_id', '!=', $assocAnchId];
        $whereCond[] = ['is_registered', '=', '1'];
        $AnchorData = $this->userRepo->getAnchorUserData($whereCond);        
        if (!empty($pan) && isset($AnchorData[0])) {
            $result['validate'] = '0';
            $result['status'] = false;
            $result['message'] = trans('success_messages.register_different_anchor');
            return response()->json($result);
        }
                
        return response()->json($result);
    }
}
