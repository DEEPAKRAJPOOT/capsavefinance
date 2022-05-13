<?php

namespace App\Http\Controllers\Backend;

use Auth;
use Session;
use Crypt;
use Helpers;
use Response;
use App\Helpers\FileHelper;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Inv\Repositories\Models\Master\State;
use App\Http\Requests\AnchorRegistrationFormRequest;
use App\Http\Requests\Lms\BankAccountRequest;
use App\Inv\Repositories\Contracts\UserInterface as InvUserRepoInterface;
use App\Inv\Repositories\Contracts\ApplicationInterface as InvAppRepoInterface;
use App\Inv\Repositories\Contracts\DocumentInterface as InvDocumentRepoInterface;
use Event;
use PHPExcel;
use PHPExcel_IOFactory;
use Illuminate\Support\Facades\Storage;
use App\Http\Requests\Backend\CreateLeadRequest;
use App\Inv\Repositories\Models\UserAppDoc;
use Illuminate\Support\Facades\Validator;
use App\Inv\Repositories\Models\Master\City as CityModel;
use App\Inv\Repositories\Models\User;
use DB;
use App\Inv\Repositories\Contracts\Traits\ActivityLogTrait;
use App\Inv\Repositories\Contracts\MasterInterface as InvMasterRepoInterface;

class LeadController extends Controller {

    protected $userRepo;
    protected $appRepo;
    use ActivityLogTrait;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct(InvUserRepoInterface $user, InvAppRepoInterface $app_repo,InvDocumentRepoInterface $doc_repo, InvMasterRepoInterface $mstRepo, FileHelper $file_helper) {
        $this->middleware('guest')->except('logout');
        $this->middleware('checkBackendLeadAccess');

        $this->userRepo = $user;
        $this->appRepo = $app_repo;
        $this->docRepo = $doc_repo;
        $this->mstRepo = $mstRepo;
        $this->fileHelper = $file_helper;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index() {
        
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
        return view('backend.lead.index')->with('panList', $panList);
    }

    /**
     * Display a listing of the resource.
     * All leads
     * @return \Illuminate\Http\Response
     */
    public function leadspool() {

        return view('backend.lead.leadpoll');
    }

    /**
     * Edit backend Lead
     * 
     * @param Request $request
     * @return type
     */
    public function editBackendLead(Request $request) {
        try {
            
            $user_id = $request->get('user_id');
            $is_registered = $request->get('is_registered');
            $arr = [];
            $anchLeadList = $this->userRepo->getAllAnchor($orderBy='comp_name');
            if ($is_registered === '1') {
                
                
                $userInfo = $this->userRepo->getUserDetail($user_id);
                $arr['full_name'] = $userInfo->f_name;
            }else{

                $userInfo = $this->userRepo->getAnchorUsersByanchorId($user_id);
                $arr['full_name'] = $userInfo->name.' '.$userInfo->l_name;
                $userInfo['f_name'] = $userInfo->name;
                $userInfo['mobile_no'] = $userInfo->phone;

            }

          return view('backend.edit_lead')->with('userInfo', $userInfo)->with('anchDropUserList',$anchLeadList);

        } catch (Exception $ex) {
            dd($ex);
        }
    }

    /**
     * Create lead.
     */
    public function createBackendLead(Request $request) {
        try {            
            return view('backend.lead.create_lead');
        } catch (Exception $ex) {
            dd($ex);
        }        
    }

    // Update lead

    public function updateBackendLead(Request $request) {
        try {
                
                $userId = $request->get('userId'); 
                $attributes['f_name'] = $request->get('f_name');
                $attributes['l_name'] = $request->get('l_name'); 
                $attributes['biz_name'] = $request->get('biz_name'); 
                $email = $request->get('email'); 
                 
                // $attributes['user_type'] = $request->get('anchor_user_type');
                $is_registerd = $request->get('is_registerd');
                $prevanchorInfo = $this->userRepo->getAnchorUsersByUserId($userId);
                
                if($is_registerd === "1"){
                    
                    if($prevanchorInfo['email'] !== $email){

                            $checkallanchorEmail = $this->userRepo->checkallanchorUserEmail($email,$userId,1,1);
                            $checkallUserEmail = $this->userRepo->checkallUserEmail($email,$userId,1);
                            if(($checkallanchorEmail == false && $checkallUserEmail == false) || $checkallUserEmail == false){

                                $attributes['email'] = $email;

                            }else{
                                
                                Session::flash('error', trans('error_messages.anchor_duplicate_email_error'));
                                return redirect()->back();
                            }                            
                    }
                }else{
                        
                        if($prevanchorInfo['email'] !== $email){

                            $checkallanchorEmail = $this->userRepo->checkallanchorUserEmail($email,$userId,0,1);
                            if($checkallanchorEmail == false){

                                $attributes['email'] = $email;
                                
                            }else{
                                
                                Session::flash('error', trans('error_messages.anchor_duplicate_email_error'));
                                return redirect()->back();
                            }                            
                        }
                } 
                
                $whereActivi['activity_code'] = 'update_backend_lead';
                $activity = $this->mstRepo->getActivity($whereActivi);
                if(!empty($activity)) {
                    $activity_type_id = isset($activity[0]) ? $activity[0]->id : 0;
                    $activity_desc = 'Update Non-Anchor Lead registration';
                    $this->activityLogByTrait($activity_type_id, $activity_desc, response()->json($attributes));
                }                
                
                if($is_registerd === "1"){
                    $attributes['mobile_no'] = $request->get('mobile_no');
                    $attributes['assigned_anchor'] = $request->get('assigned_anchor');
                    $userInfo = $this->userRepo->updateUser($attributes, $userId);
                    if($prevanchorInfo['anchor_user_id'] != null){
                        $anchoruserInfo =$this->userRepo->updateAnchorUser($prevanchorInfo['anchor_user_id'],$attributes);
                    }
                        

                }else{

                    $attributes['phone'] = $request->get('mobile_no');
                    $anchoruserInfo =$this->userRepo->updateAnchorUser($userId,$attributes);

                }
                
                Session::flash('operation_status', 1); 
                //return view('backend.lead.index');
                Session::flash('message', 'Lead is updated successfully.'); 
                Session::flash('is_accept', 1);
                return redirect()->back();                      
        } catch (Exception $ex) {
            dd($ex);
        }
    }

    /**
     * Save backend lead
     * @param Request $request
     * @return type
     */
    public function saveBackendLead(CreateLeadRequest $request) {
        try {
            $string = time();
            $reqData = $request->all();            
            $user_info = $this->userRepo->getUserByEmail($reqData['email']);

            if(!$user_info){
                DB::beginTransaction();
                
                $hashval = time() . '2348923NONANCHORLEAD'.$reqData['email'];
                $token   = md5($hashval);
                $arrUserData = [
                    'f_name'              => $reqData['f_name'],
                    'l_name'              => $reqData['l_name'],
                    'biz_name'            => $reqData['comp_name'],
                    'email'               => $reqData['email'],
                    'mobile_no'           => $reqData['phone'],
                    'is_buyer'            => $reqData['is_buyer'],
                    'reg_token'           => $token,
                    'assign_sale_manager' => $reqData['assigned_sale_mgr'],
                    'product_id'          => $reqData['product_type'],
                ];
                $userDataArray = $this->userRepo->saveNonAnchorLead($arrUserData);                

                $whereActivi['activity_code'] = 'save_backend_lead';
                $activity = $this->mstRepo->getActivity($whereActivi);

                if(!empty($activity)) {
                    $activity_type_id = isset($activity[0]) ? $activity[0]->id : 0;
                    $activity_desc = 'Add Non-Anchor Lead registration';
                    $this->activityLogByTrait($activity_type_id, $activity_desc, response()->json(['arrNonAnchorLeadData' => $arrUserData]));
                }                

                if ($userDataArray) {
                    $businessName = $reqData['comp_name'];
                    $mailUrl = config('proin.frontend_uri') . '/lead-sign-up?token=' . $arrUserData['reg_token'];
                    $nonAnchLeadMailArr['name'] = trim($arrUserData['f_name']).' '.trim($arrUserData['l_name']);
                    $nonAnchLeadMailArr['email'] =  trim($arrUserData['email']);
                    $nonAnchLeadMailArr['url'] = $mailUrl;
                    $nonAnchLeadMailArr['businessName'] = $businessName;
                    $nonAnchLeadMailArr['productType']  = $arrUserData['product_id'] == config('common.PRODUCT.TERM_LOAN') ? 'Term Loan' : ($arrUserData['product_id'] == config('common.PRODUCT.LEASE_LOAN') ? 'Leasing' : '');
                    Event::dispatch("NON_ANCHOR_CSV_LEAD_UPLOAD", serialize($nonAnchLeadMailArr));
                }

                DB::commit();

                Session::flash('message', 'Non-Anchor Lead registered successfully'); 
                Session::flash('is_accept', 1);
                return redirect()->back();                      
            }else{
                Session::flash('error', trans('error_messages.email_already_exists'));
                return redirect()->back()->withInput();
            }
        
        } catch (Exception $ex) {
            DB::rollback();
            return redirect()->back()->withErrors(Helpers::getExceptionMessage($ex));
        }
    }

    /**
     * backend Lead Details
     * 
     * @param Request $request
     * @return type
     */
    public function leadDetail(Request $request) {
        try {
            $user_id = $request->get('user_id');
            $userInfo = $this->userRepo->getUserDetail($user_id); //dd($userInfo);
            //$application = $this->appRepo->getApplicationsDetail($user_id)->toArray();
            $application = $this->appRepo->getApplicationsByPan($user_id)->toArray();
            
            return view('backend.lead.lead_details')
                            ->with('userInfo', $userInfo)
                            ->with('application', $application);
        } catch (Exception $ex) {
            dd($ex);
        }
    }

    /**
     * backend Lead Details
     * 
     * @param Request $request
     * @return type
     */
    public function showApplicationPool() {
        try {
            return view('backend.app.case_poll');
        } catch (Exception $ex) {
            dd($ex);
        }
    }

    /**
     * backend Lead Details
     * 
     * @param Request $request
     * @return type
     */
    public function confirmBox(Request $request) {
        try {
            //dd($request->all());
            $user_id = $request->get('user_id');
            $app_id = $request->get('app_id');

            return view('backend.app.confirmBox')
                            ->with('user_id', $user_id)
                            ->with('app_id', $app_id);
        } catch (Exception $ex) {
            dd($ex);
        }
    }

    /**
     * backend Lead Details
     * 
     * @param Request $request
     * @return type
     */

    public function acceptApplicationPool(Request $request){
         try {
             $user_id = $request->get('user_id');
             $app_id = $request->get('app_id');
             
            $whereCond = [];
            $whereCond['app_id'] = $app_id;
            $whereCond['is_owner'] = 1;
            $appAssignData = $this->appRepo->getAppAssignmentData($whereCond);
            $fromUserId = $appAssignData ? $appAssignData->from_id : null;
            
            $roleData = $this->userRepo->getBackendUser(\Auth::user()->user_id);
            
             $dataArr = []; 
             $dataArr['from_id'] = $fromUserId ? $fromUserId : "";     //Auth::user()->user_id;
             $dataArr['to_id'] = Auth::user()->user_id;
             $dataArr['role_id'] = null;  //$roleData->id;
             $dataArr['assigned_user_id'] = $user_id;
             $dataArr['app_id'] = $app_id;
             $dataArr['assign_status'] = '0';
             $dataArr['assign_type'] = '1';
             $dataArr['sharing_comment'] = "";
             $dataArr['is_owner'] = 1;
            $application = $this->appRepo->updateAppDetails($app_id, ['is_assigned'=>1]); 
            $this->appRepo->updateAppAssignById($app_id, ['is_owner'=>0]);
            $application = $this->appRepo->saveShaircase($dataArr); 

            $whereActivi['activity_code'] = 'accept_application_pool';
            $activity = $this->mstRepo->getActivity($whereActivi);
            if(!empty($activity)) {
                $activity_type_id = isset($activity[0]) ? $activity[0]->id : 0;
                $activity_desc = 'Pick Up Lead in Application Pool of AppId. '. $app_id;
                $arrActivity['app_id'] = $app_id;
                $this->activityLogByTrait($activity_type_id, $activity_desc, response()->json($dataArr), $arrActivity);
            }               
             Session::flash('is_accept', 1);
             return redirect()->back();
             
             } catch (Exception $ex) {
            return redirect()->back()->withErrors(Helpers::getExceptionMessage($ex));
        }
    }

    /**
     * Display anchor listing
     *
     * @return \Illuminate\Http\Response
     */
    public function allAnchorList() {
        return view('backend.anchor.index');
    }

    public function addAnchorReg(Request $request) {
        try {
            // $states = State::getStateList()->get();
            $states = State::select("id","name")->get();
            return view('backend.anchor.add_anchor_reg')
            ->with(['states'=>$states]);
                     //->with('state', $stateList);
        } catch (Exception $ex) {
             return redirect()->back()->withErrors(Helpers::getExceptionMessage($ex));
        }
    }

    /**
     * function for save anchor info and also create anchor user
     * @param Request $request
     * @return type
     */
    public function saveaddAnchorReg(Request $request) {
        try {
            //$string = Helpers::randomPassword();
            $string = time();
           
            $validator = Validator::make($request->all(), [
                'doc_file' => 'required|mimes:jpeg,jpg,png,pdf',
                'is_fungible' => 'required|numeric',
            ],['doc_file.mimes'=> 'Invalid file format']);
    
            if ($validator->fails()) {
                return redirect('anchor')
                            ->withErrors($validator)
                            ->withInput();
            }
           
            $arrAnchorVal = $request->all();
            $anchor_user_info = $this->userRepo->getUserByEmail($arrAnchorVal['email']);
            if(!$anchor_user_info){
                $arrAnchorData = [
                    'comp_name' => $arrAnchorVal['comp_name'],
                    'sales_user_id' => $arrAnchorVal['assigned_sale_mgr'],
                    'comp_email' => $arrAnchorVal['email'],
                    'comp_phone' => $arrAnchorVal['phone'],
                    'comp_addr' => $arrAnchorVal['comp_addr'],                
                    'comp_state' => $arrAnchorVal['state'],
                    'comp_city' => $arrAnchorVal['city'],
                    'comp_zip' => $arrAnchorVal['pin_code'],
                    'is_phy_inv_req' => $arrAnchorVal['is_phy_inv_req'],
                    'is_fungible' => $arrAnchorVal['is_fungible'],
                    'gst_no'      => $arrAnchorVal['gst_no'],
                ];

                if(isset($arrAnchorVal['gst_no']) && !empty($arrAnchorVal['gst_no']))
                   $arrAnchorData['gst_no'] = $arrAnchorVal['gst_no'];
                

                if(isset($arrAnchorVal['pan_no']) && !empty($arrAnchorVal['pan_no']))
                    $arrAnchorData['pan_no'] = $arrAnchorVal['pan_no'];
                    
                    if(isset($arrAnchorVal['email']) && !empty($arrAnchorVal['email'])){
        
                            $anchUserInfo=$this->userRepo->getAnchorUsersByEmail(trim($arrAnchorVal['email']));
                            
                            if($anchUserInfo == true){
        
                                Session::flash('error', trans('error_messages.anchor_duplicate_email_error'));
                                return redirect()->route('anchor');
                                
        
                            }                            
                    }

                $anchor_info = $this->userRepo->saveAnchor($arrAnchorData);

                $arrAnchUserData = [
                    'anchor_id' => $anchor_info,
                    'f_name' => $arrAnchorVal['employee'],
                    'biz_name' => $arrAnchorData['comp_name'],
                    'email' => $arrAnchorData['comp_email'],
                    'mobile_no' => $arrAnchorData['comp_phone'],
                    'user_type' => 2,
                    'is_email_verified' => 1,
                    'is_active' => 1,
                    'password' => bcrypt($string)
                ];
                //dd($arrAnchUserData);
                $anchor_user_info = $this->userRepo->save($arrAnchUserData);
                $anchUserMailArr = [];
                $anchUserMailArr['email'] = $arrAnchUserData['email'];
                $anchUserMailArr['name'] = $arrAnchUserData['f_name'];
                $anchUserMailArr['password'] = $string;
                Event::dispatch("ANCHOR_REGISTER_USER_MAIL", serialize($anchUserMailArr));
                if ($anchor_info && $anchor_user_info) {
                    
                    $role = [];
                    $role['role_id'] = 11;
                    $role['user_id'] = $anchor_user_info->user_id;
                    $rr = $this->userRepo->addNewRoleUser($role);
                    if($request->doc_file){
                        self::uploadAnchorDoc($request->all(), $role['user_id'] ,$anchor_info);
                    }

                    if(isset($request->anchor_logo) && !empty($request->anchor_logo)){
                        $validator = Validator::make($request->all(), [
                            'anchor_logo' => 'required|mimes:jpeg,jpg,png',
                            'logo_align' => 'required'
                        ],['anchor_logo.mimes'=> 'Invalid logo file format',
                        'logo_align.required' => 'Logo alignment required if logo uploaded']);
                
                        if ($validator->fails()) {
                            return redirect('anchor')
                                        ->withErrors($validator)
                                        ->withInput();
                        }
                        self::uploadAnchorLogo($request->all(), $role['user_id'] ,$anchor_info);
                    }

                    $whereActivi['activity_code'] = 'add_anchor_reg';
                    $activity = $this->mstRepo->getActivity($whereActivi);
                    if(!empty($activity)) {
                        $activity_type_id = isset($activity[0]) ? $activity[0]->id : 0;
                        $activity_desc = 'Add Anchor';
                        $this->activityLogByTrait($activity_type_id, $activity_desc, response()->json($arrAnchorData));
                    }
                    Session::flash('message', trans('backend_messages.anchor_registration_success'));
                    return redirect()->route('get_anchor_list');
                }        
            }else{
                Session::flash('error', trans('error_messages.email_already_exists'));
                return redirect()->route('get_anchor_list');
            }
        
        } catch (Exception $ex) {
            return redirect()->back()->withErrors(Helpers::getExceptionMessage($ex));
        }
    }
    /**
     *  function for upload anchor documents
     * @param Request $arrFileData
     * @param Int $userId
     * @param Int $anchorId
     * @return type
     */
    private function uploadAnchorDoc($arrFileData, $userId, $anchorId) {
        $uploadData = Helpers::uploadAnchorFile($arrFileData, $anchorId);
        $anchorFile = $this->docRepo->saveFile($uploadData);
        if(!empty($anchorFile->file_id)) {

            UserAppDoc::where('user_id', '=', $userId)
            ->where('file_type', '=', 1)        
            ->update(['is_active' => '0']);

            UserAppDoc::create(array(
                'user_id' => $userId,
                'file_id' => $anchorFile->file_id,
                'file_type' => 1,
                'created_by' => \Auth::user()->user_id,
                'updated_by' => \Auth::user()->user_id
            ));
        }
    } 

    /**
     *  function for upload anchor lead using csv
     * @param Request $request
     * @return type
     */
    public function uploadAnchorlead(Request $request) {
        try {
              $roleData = Helpers::getUserRole();
            $is_superadmin = isset($roleData[0]) ? $roleData[0]->is_superadmin : 0;
                $anchLeadList = $this->userRepo->getAllAnchor($orderBy='comp_name');                
            return view('backend.anchor.upload_anchor_lead')
                 ->with('is_superadmin',$is_superadmin)                    
                ->with('anchDropUserList',$anchLeadList);
        } catch (Exception $ex) {
            dd($ex);
        }
    }

    /**
     *  function for create anchor lead from csv file
     * @param Request $request
     * @return type
     */
    public function saveUploadAnchorlead(Request $request) {
        try {
            $validatedData = Validator::make($request->all(),[
                // 'assigned_anchor' => 'required',
                'anchor_lead' => 'required'
            ],[
                'anchor_lead.required' => 'This field is required.',
                // 'assigned_anchor.required' => 'This field is required.'
            ])->validate();

            if ($request->has('assigned_anchor')){
                $anchorId = $request->get('assigned_anchor');
            } else {
                $anchorId = Auth::user()->anchor_id;
            }

            $uploadedFile = $request->file('anchor_lead');
            $destinationPath = storage_path() . '/uploads';
            
            $fileName = time();
            if ($uploadedFile->isValid()) {
                $uploadedFile->move($destinationPath, $fileName);
            }

            $fullFilePath  = $destinationPath . '/' . $fileName;
            $header = [
                0,1,2,3,4,5
            ];

            // $fileHelper = new FileHelper();
            // $fileArrayData = $fileHelper->excelNcsv_to_array($fullFilePath, $header);
            $fileArrayData = $this->fileHelper->excelNcsv_to_array($fullFilePath, $header);
            // dd($fileArrayData);
            if($fileArrayData['status'] != 'success'){
                Session::flash('message', 'Please fill the data countiously till 6th column in the sheet');
                return redirect()->back();
            }

            $rowData = $fileArrayData['data'];

            if (empty($rowData)) {
                Session::flash('message', 'File does not contain any record');
                return redirect()->back();                     
            }

            foreach($rowData as $key => $value){
                if ($value && (empty($value[0]) || empty($value[1]) || empty($value[2]) || empty($value[3]) || empty($value[4]) || empty($value[5]))) {
                    Session::flash('message', 'Please fill the correct details.');
                    return redirect()->back();                     
                }
        
                $anchUserInfo=$this->userRepo->getAnchorUsersByEmail(trim($value[3]));
                if($anchUserInfo){
                    Session::flash('message', 'User is already registered with '.$value[3]);
                    return redirect()->back(); 
                }
                
                if(!preg_match("/^([a-zA-Z' ]+)$/",$value[0])){
                    Session::flash('message', 'Please fill correct first name'.$value[0]);
                    return redirect()->back();
                }

                if(!preg_match("/^([a-zA-Z' ]+)$/",$value[1])){
                    Session::flash('message', 'Please fill correct last name'.$value[1]);
                    return redirect()->back();
                }
            }

            $anchLeadMailArr = [];
            $arrAnchLeadData = [];
            $arrUpdateAnchor = [];
            foreach ($rowData as $key => $value) {
                if ($value && (empty($value[0]) || empty($value[1]) || empty($value[2]) || empty($value[3]) || empty($value[4]) || empty($value[5]) )) {
                    Session::flash('message', 'Please fill the correct details.');
                    return redirect()->back();                     
                }
                $anchUserInfo=$this->userRepo->getAnchorUsersByEmail(trim($value[3]));
                $anchorData   =   $this->userRepo->getAnchorById($anchorId)->toArray();
                if(!empty($value) && !$anchUserInfo){

                    $hashval = time() . 'ANCHORLEAD' . $key;
                    $token = md5($hashval);
                        if(trim($value[5])=='Buyer'){
                            $userType=2;
                        }else{
                            $userType=1; 
                        }
                    $arrAnchLeadData = [
                        'name' =>  trim($value[0]),
                        'l_name'=>trim($value[1]),
                        'biz_name' =>  trim($value[2]),
                        'email'=>$value[3],
                        'phone' => $value[4],
                        'user_type' => $userType,
                        'created_by' => Auth::user()->user_id,
                        'created_at' => \Carbon\Carbon::now(),
                        'is_registered'=>0,
                        'registered_type'=>1,
                        'token' => $token,
                        'anchor_id' => $anchorId,
                        // 'supplier_code' => isset($value[6]) ? $value[6] : null,
                    ];

                    $anchor_lead = $this->userRepo->saveAnchorUser($arrAnchLeadData);
                    $businessName = trim($value[2]);
                    $anchorName = $anchorData['comp_name'];
                    /*
                    $getAnchorId =$this->userRepo->getUserDetail(Auth::user()->user_id);
                    if($getAnchorId && $getAnchorId->anchor_id!=''){
                    $arrUpdateAnchor ['anchor_id'] = $getAnchorId->anchor_id;
                    }else{
                    $arrUpdateAnchor ['anchor_id'] =$request->post('assigned_anchor');
                    }
                $getAnchorId =$this->userRepo->updateAnchorUser($anchor_lead,$arrUpdateAnchor);
                    */
                    
                    if ($anchor_lead) {
                        $mailUrl = config('proin.frontend_uri') . '/sign-up?token=' . $token;
                        $anchLeadMailArr['name'] = $arrAnchLeadData['name'];
                        $anchLeadMailArr['email'] =  trim($arrAnchLeadData['email']);
                        $anchLeadMailArr['url'] = $mailUrl;
                        $anchLeadMailArr['businessName'] = $businessName;
                        $anchLeadMailArr['anchorName'] = $anchorName;
                        Event::dispatch("ANCHOR_CSV_LEAD_UPLOAD", serialize($anchLeadMailArr));
                    }
                }
            }
            //chmod($destinationPath . '/' . $fileName, 0775, true);
            unlink($destinationPath . '/' . $fileName);
            //Session::flash('message', trans('backend_messages.anchor_registration_success'));
            //return redirect()->route('get_anchor_lead_list');
            Session::flash('is_accept', 1);
            return redirect()->back();            
        } catch (Exception $ex) {
             return redirect()->back()->withErrors(Helpers::getExceptionMessage($ex));
        }
    }

    /**
     * function for update anchor info
     * @param Request $request
     * @return type
     */
    public function editAnchorReg(Request $request) {
        try {
            $anchorId = $request->get('anchor_id');
            if ($anchorId) {
                $anchorUserInfo = $this->userRepo->getUserByAnchorId($anchorId);
                $anchorVal = $this->userRepo->getAnchorById($anchorId);
            }
            // dd($anchorVal);
             $states = State::getStateList()->get();
            return view('backend.anchor.edit_anchor_reg')
                            ->with('anchor_id', $anchorId)
                            ->with('anchorUserData',$anchorUserInfo)
                            ->with(['states'=>$states])
                            ->with('anchorData', $anchorVal);
        } catch (Exception $ex) {
            return redirect()->back()->withErrors(Helpers::getExceptionMessage($ex));
        }
    }
    
    /**
     * function for add anchor bank
     * @param Request $request
     * @return type
     */
    public function addAnchorBank(Request $request) {
        try {
            
            $bankAccount = [];
            $anchor_id = $request->get('anchor_id');
            $bank_acc_id = false;
            $bankAccount['is_default'] = 0;
            
            if (!empty($request->get('bank_account_id')) && $anchor_id != null) {
                $bank_acc_id = preg_replace('#[^0-9]#', '', $request->get('bank_account_id'));
                $bankAccount = $this->appRepo->getBankAccountDataByAnchorId($bank_acc_id,$anchor_id)->first();
            }
            
            $bank_list = ['' => 'Please Select'] + $this->appRepo->getBankList()->toArray();
            return view('backend.anchor.anchor_bank_account')
                            ->with(['bank_list' => $bank_list, 'anchorId' => $anchor_id, 'bankAccount' => $bankAccount]);
        } catch (\Exception $ex) {
            return Helpers::getExceptionMessage($ex);
        }
    }
    
    /**
     * Save Anchor bank account
     * 
     * @param Request $request
     * @return type mixed
     */
    public function saveAnchorBankAccount(BankAccountRequest $request)
    {
        try {
//            dd($request->all());
            $by_default = ($request->get('by_default')) ? ((int)$request->get('by_default')) : 0;
            $bank_acc_id = ($request->get('bank_account_id')) ? \Crypt::decrypt($request->get('bank_account_id')) : null;
            $anchorId = ($request->get('anchor_id')) ? \Crypt::decrypt($request->get('anchor_id')) : null;
           // dd($anchorId);
            $prepareData = [
                'acc_name' => $request->get('acc_name'),
                'acc_no' => $request->get('acc_no'),
                'bank_id' => $request->get('bank_id'),
                'ifsc_code' => $request->get('ifsc_code'),
                'branch_name' => $request->get('branch_name'),
                'is_active' => $request->get('is_active'),
                'user_id' => auth()->user()->user_id,
                'anchor_id' => $anchorId,
                'is_default' => $by_default,
            ];

            $this->appRepo->saveBankAccount($prepareData, $bank_acc_id);

            $whereActivi['activity_code'] = 'save_anchor_bank_account';
            $activity = $this->mstRepo->getActivity($whereActivi);
            if(!empty($activity)) {
                $activity_type_id = isset($activity[0]) ? $activity[0]->id : 0;
                $activity_desc = 'Save Anchor Bank Details';
                $this->activityLogByTrait($activity_type_id, $activity_desc, response()->json($prepareData));
            }
            
            $messges = $bank_acc_id ? trans('success_messages.update_bank_account_successfully') : trans('success_messages.save_bank_account_successfully');
            Session::flash('message', $messges);
            Session::flash('operation_status', 1);
            return redirect()->back();
        } catch (\Exception $ex) {
            return redirect()->back()->withErrors(Helpers::getExceptionMessage($ex))->withInput();
        }
    }

    /**
     * function for update anchor information
     * @param Request $request
     * @return type
     */
    public function updateAnchorReg(Request $request) {
        try {
            $arrAnchorVal = $request->all();
            $validator = Validator::make($request->all(), [
                'doc_file' => 'mimes:jpeg,jpg,png,pdf',
                'is_fungible' => 'required|numeric',
            ],['doc_file.mimes'=> 'Invalid file format']);
    
            if ($validator->fails()) {
                return redirect('anchor')
                            ->withErrors($validator)
                            ->withInput();
            }
            
            if(!$request->get('readonly_gst') || !$request->get('readonly_pan')){

                if((isset($arrAnchorVal['pan_no']) && !empty($arrAnchorVal['pan_no'])) || (isset($arrAnchorVal['gst_no']) && !empty($arrAnchorVal['gst_no']))){

                    if(isset($arrAnchorVal['pan_no'])){
                        $anchrUserDataByPan = $this->userRepo->getAnchorByPan($arrAnchorVal['pan_no']);
                        $anchorDataByPan = $this->userRepo->getAnchorData(['pan_no' => $arrAnchorVal['pan_no']]);
                        if(count($anchorDataByPan) > 0){
                            return redirect('anchor')
                            ->withErrors("Anchor already registered with this Pan No. ".$arrAnchorVal['pan_no'])
                            ->withInput();
                        }
                        
                    }

                    if(isset($arrAnchorVal['gst_no'])){
                        $anchorDataByGst = $this->userRepo->getAnchorData(['gst_no' => $arrAnchorVal['gst_no']]);
                        if(count($anchorDataByGst) > 0){

                            return redirect('anchor')
                            ->withErrors("Anchor already registered with this GST No. ".$arrAnchorVal['gst_no'])
                            ->withInput();

                        }
                    }
                }
                
            } 

            $anchId = $request->post('anchor_id');
            $anchId=(int)$anchId;
            $arrAnchorData = [
                'comp_name' => $arrAnchorVal['comp_name'],
                'sales_user_id' => $arrAnchorVal['assigned_sale_mgr'],
                'comp_phone' => $arrAnchorVal['phone'],
                'comp_addr' => $arrAnchorVal['comp_addr'],
                'comp_state' => $arrAnchorVal['state'],
                'comp_city' => $arrAnchorVal['city'],
                'comp_zip' => $arrAnchorVal['pin_code'],
                'is_phy_inv_req' => $arrAnchorVal['is_phy_inv_req'],
                'is_fungible' => $arrAnchorVal['is_fungible']
            ];
            if(isset($arrAnchorVal['pan_no']) && !empty($arrAnchorVal['pan_no'])){
                $arrAnchorData['pan_no'] = $arrAnchorVal['pan_no'];
            }

            if(isset($arrAnchorVal['gst_no']) && !empty($arrAnchorVal['gst_no'])){
                $arrAnchorData['gst_no'] = $arrAnchorVal['gst_no'];
            }
            
            $prevanchorInfo = $this->userRepo->getAnchorByAnchorId($anchId);
            // $prevanchorInfo = $this->userRepo->getUserByAnchorId($anchId);
            $userEmailMatched = false;
            if(isset($arrAnchorVal['email']) && !empty($arrAnchorVal['email'])){
                
                if($prevanchorInfo['email'] !== $arrAnchorVal['email']){

                    $checkallanchorEmail = $this->userRepo->checkallanchorEmail($arrAnchorVal['email'],$anchId);
                    $checkallUserEmail = $this->userRepo->checkallUserEmail($arrAnchorVal['email'],$anchId,2);
                    $anchUserInfo=$this->userRepo->getAnchorUsersByEmail(trim($arrAnchorVal['email']));
                    
                    if($checkallanchorEmail == false && $checkallUserEmail == false && $anchUserInfo == false){

                        $arrAnchorData['comp_email'] = $arrAnchorVal['email'];
                        $userEmailMatched = true;
                        

                    }else{
                        
                        Session::flash('error', trans('error_messages.anchor_duplicate_email_error'));
                        return redirect()->route('get_anchor_list');
                    }                            
                }
            }
            
            $updateAnchInfo = $this->userRepo->updateAnchor($anchId, $arrAnchorData);
            $arrAnchorUserData = [
                'f_name' => $arrAnchorVal['employee'],
                'biz_name' => $arrAnchorData['comp_name'],
                'mobile_no' => $arrAnchorData['comp_phone'],
            ];  
            if($userEmailMatched)
              $arrAnchorUserData['email'] = $arrAnchorVal['email']; 

              
            $anchorInfo = $this->userRepo->getAnchorByAnchorId($anchId);
            $Updateanchorinfo = $this->userRepo->updateUser($arrAnchorUserData, (int) $anchorInfo->user_id);
            
            if($request->doc_file){
                self::uploadAnchorDoc($request->all(), $anchorInfo->user_id ,$anchId);
            }

            if(isset($request->anchor_logo) && !empty($request->anchor_logo)){
                $validator = Validator::make($request->all(), [
                    'anchor_logo' => 'required|mimes:jpeg,jpg,png',
                    'logo_align' => 'required'
                ],['anchor_logo.mimes'=> 'Invalid logo file format',
                'logo_align.required' => 'Logo alignment required if logo uploaded']);
        
                if ($validator->fails()) {
                    return redirect('anchor')
                                ->withErrors($validator)
                                ->withInput();
                }
                self::uploadAnchorLogo($request->all(), $anchorInfo->user_id ,$anchId);
            }

            $whereActivi['activity_code'] = 'update_anchor_reg';
            $activity = $this->mstRepo->getActivity($whereActivi);
            if(!empty($activity)) {
                $activity_type_id = isset($activity[0]) ? $activity[0]->id : 0;
                $activity_desc = 'Update Anchor';
                $this->activityLogByTrait($activity_type_id, $activity_desc, response()->json($arrAnchorData));            
            }
            if ($updateAnchInfo && $Updateanchorinfo) {
                Session::flash('message', trans('backend_messages.anchor_registration_updated'));
                return redirect()->route('get_anchor_list');
            } else {
                // return response()->json(['message' =>trans('success_messages.oops_something_went_wrong'),'status' => 0]);
            }
        } catch (Exception $ex) {
            return redirect()->back()->withErrors(Helpers::getExceptionMessage($ex));
        }
    }
    
   /**
    * 
    * @return type
    */
     public function getAnchorLeadList() {
         
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
        return view('backend.anchor.anchor_lead_list')->with('panList', $panList);
    }
    
     public function addManualAnchorLead() {
      try{
          $roleData = Helpers::getUserRole();
          $is_superadmin = isset($roleData[0]) ? $roleData[0]->is_superadmin : 0;
       $anchLeadList = $this->userRepo->getAllAnchor($orderBy='comp_name');
        return view('backend.anchor.anchor_manual_lead')
       ->with('anchDropUserList',$anchLeadList)
        ->with('is_superadmin',$is_superadmin);
         } catch (Exception $ex) {
            dd($ex);
        }
    }
  
    /**
     * function for save manual anchor lead
     * @return type
     */
    public function saveManualAnchorLead(Request $request){
       try {
            $arrAnchorVal = $request->all();    
        //    dd($arrAnchorVal);            
                            
            if (!empty($arrAnchorVal['assigned_anchor'])){
                $anchorId = $arrAnchorVal['assigned_anchor'];
            } else {
                $anchorId = Auth::user()->anchor_id;
            }
            
            //$anchUserInfo=$this->userRepo->getAnchorUsersByEmail(trim($arrAnchorVal['email']));                        
            $arrUpdateAnchor =[];
            //if(!$anchUserInfo){
            $whereCond=[];
            $whereCond[] = ['email', '=', trim($arrAnchorVal['email'])];
            $whereCond[] = ['anchor_id', '=', $anchorId];
            $whereCond[] = ['anchor_id', '>', '0'];
            //$whereCond[] = ['is_registered', '!=', '1'];
            $anchUserData = $this->userRepo->getAnchorUserData($whereCond);
            $anchorData   =   $this->userRepo->getAnchorById($anchorId)->toArray();

            if (!isset($anchUserData[0])) { 
                if (isset($arrAnchorVal['email'])) {
                    $userData = $this->userRepo->getBackendUserByEmail(trim($arrAnchorVal['email']));
                    if ($userData) {
                        Session::flash('error', trans('error_messages.email_already_exists'));                        
                        Session::flash('operation_status', 1);
                        return redirect()->route('get_anchor_lead_list');
                    }
                }
                 
                $hashval = time() . '2348923ANCHORLEAD'.$arrAnchorVal['email'];
                $token = md5($hashval);
                $arrAnchorData = [
                    'name' => trim($arrAnchorVal['f_name']),
                    'l_name' => trim($arrAnchorVal['l_name']),
                    'biz_name' => $arrAnchorVal['comp_name'],
//                    'pan_no' => $arrAnchorVal['pan_no'],
                    'email' => trim($arrAnchorVal['email']),
                    'phone' => $arrAnchorVal['phone'],
                    'user_type' => $arrAnchorVal['anchor_user_type'],
                    'is_registered'=>0,
                    'registered_type'=>0,
                    'created_by' => Auth::user()->user_id,
                    'created_at' => \Carbon\Carbon::now(),
                    'token' => $token,
                    'anchor_id' => $anchorId,
                    // 'supplier_code' => isset($arrAnchorVal['supplier_code']) ? trim($arrAnchorVal['supplier_code']) : null,
                ];
                
                $whereActivi['activity_code'] = 'add_manual_anchor_lead';
                $activity = $this->mstRepo->getActivity($whereActivi);
                if(!empty($activity)) {
                    $activity_type_id = isset($activity[0]) ? $activity[0]->id : 0;
                    $activity_desc = 'Add Anchor Lead Manually in Manage Anchor';
                    $this->activityLogByTrait($activity_type_id, $activity_desc, response()->json($arrAnchorData));
                }
                $anchor_lead = $this->userRepo->saveAnchorUser($arrAnchorData);
                $businessName = $arrAnchorVal['comp_name'];
                $anchorName = $anchorData['comp_name'];
                /*
                $getAnchorId =$this->userRepo->getUserDetail(Auth::user()->user_id);
            
                if($getAnchorId && $getAnchorId->anchor_id!=''){
                    $arrUpdateAnchor ['anchor_id'] = $getAnchorId->anchor_id;
                }else{
                     $arrUpdateAnchor ['anchor_id'] =$arrAnchorVal['assigned_anchor'];
                }
            
            
                $getAnchorId =$this->userRepo->updateAnchorUser($anchor_lead,$arrUpdateAnchor);
                 * 
                 */
                if($anchor_lead) {
                    $mailUrl = config('proin.frontend_uri') . '/sign-up?token=' . $token;
                    $anchLeadMailArr['name'] = trim($arrAnchorData['name']);
                    $anchLeadMailArr['email'] =  trim($arrAnchorData['email']);
                    $anchLeadMailArr['url'] = $mailUrl;
                    $anchLeadMailArr['businessName'] = $businessName;
                    $anchLeadMailArr['anchorName'] = $anchorName;
                    Event::dispatch("ANCHOR_CSV_LEAD_UPLOAD", serialize($anchLeadMailArr));
                    Session::flash('message', trans('backend_messages.anchor_lead_created'));
                    Session::flash('operation_status',1);
                    return redirect()->route('get_anchor_lead_list');
                }
            }else{
                Session::flash('error', trans('error_messages.email_already_exists'));
                Session::flash('operation_status', 1);
                return redirect()->route('get_anchor_lead_list');
            }
        } catch (Exception $ex) {
            return redirect()->back()->withErrors(Helpers::getExceptionMessage($ex));
        }
    }

        /**
     * function to dropdown citylist
     * @return type
     */
    public function getCityList(Request $request)
    {
        // $cities = DB::table("mst_city")
        //     ->where("state_id",$request->state_id)
        //     ->pluck("name");
        //     return response()->json($cities);
        $cities = DB::table("mst_city")
            ->select("id","name")
            ->where("state_id",$request->state_id)
            ->pluck("name","id");
            return response()->json($cities);
    }
    
    public function downloadSample(Request $request)
    {
        $filePath = public_path() . '/anchoruserlist.csv';
        header('Content-type: text/csv');
        header('Content-Disposition: attachment; filename="anchoruserlist.csv"');
        readfile($filePath);
        exit;
    }

    public function viewUploadedFile(Request $request){
        try {
            
            $file_id = $request->get('fileId');
            $fileData = $this->docRepo->getFileByFileId($file_id);

            $filePath = 'app/public/'.$fileData->file_path;
            $path = storage_path($filePath);
            
            if (file_exists($path)) {
                return response()->file($path);
            }else{
                exit('Requested file does not exist on our server!');
            }
        } catch (Exception $ex) {                
            return redirect()->back()->withErrors(Helpers::getExceptionMessage($ex));
        }

    }

    /**
     *  function for upload anchor documents
     * @param Request $arrFileData
     * @param Int $userId
     * @param Int $anchorId
     * @return type
     */
    private function uploadAnchorLogo($arrFileData, $userId, $anchorId) {
        $attributes['doc_file'] = $arrFileData['anchor_logo'];
        $uploadData = Helpers::uploadAnchorLogo($attributes, $anchorId);
        $anchorFile = $this->docRepo->saveFile($uploadData);
        if(!empty($anchorFile->file_id)) {

            $anchorData = $this->userRepo->getAnchorById($anchorId);

            $arrData = [
                'logo_file_id' => $anchorFile->file_id,
                'logo_align' => $arrFileData['logo_align']
            ];

            $this->userRepo->updateAnchor($anchorId, $arrData);
            
        }
    }

    public function getNonAnchorLeads()
    {
        return view('backend.non_anchor_lead.index');
    }
    
}


