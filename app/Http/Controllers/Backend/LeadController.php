<?php

namespace App\Http\Controllers\Backend;

use Auth;
use Session;
use Crypt;
use Helpers;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Inv\Repositories\Models\Master\State;
use App\Http\Requests\AnchorRegistrationFormRequest;
use App\Inv\Repositories\Contracts\UserInterface as InvUserRepoInterface;
use App\Inv\Repositories\Contracts\ApplicationInterface as InvAppRepoInterface;
use Event;

class LeadController extends Controller {

    protected $userRepo;
    protected $appRepo;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct(InvUserRepoInterface $user, InvAppRepoInterface $app_repo) {
        $this->middleware('guest')->except('logout');
        $this->middleware('checkBackendLeadAccess');

        $this->userRepo = $user;
        $this->appRepo = $app_repo;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index() {
        return view('backend.lead.index');
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
            $arr = [];
            if ($user_id) {
                $userInfo = $this->userRepo->getUserDetail($user_id);
                $arr['full_name'] = $userInfo->f_name;
            }

            return view('backend.edit_lead');
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
    public function leadDetail(Request $request) {
        try {
            $user_id = $request->get('user_id');
            $userInfo = $this->userRepo->getUserDetail($user_id); //dd($userInfo);
            $application = $this->appRepo->getApplicationsDetail($user_id)->toArray();
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
            $roleData = $this->userRepo->getBackendUser(\Auth::user()->user_id);
             $user_id = $request->get('user_id');
             $app_id = $request->get('app_id');
            
             $dataArr = []; 
             $dataArr['from_id'] = Auth::user()->user_id;
             $dataArr['to_id'] = Auth::user()->user_id;
             $dataArr['role_id'] = $roleData->id;
             $dataArr['assigned_user_id'] = $user_id;
             $dataArr['app_id'] = $app_id;
             $dataArr['assign_status'] = '0';
             $dataArr['sharing_comment'] = "comment";
             $dataArr['is_owner'] = 1;
            $application = $this->appRepo->updateAppDetails($app_id, ['is_assigned'=>1]); 
            $this->appRepo->updateAppAssignById($app_id, ['is_owner'=>0]);
            $application = $this->appRepo->saveShaircase($dataArr); 
             
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
            //$stateList= $this->userRepo->getStateList();
            $states = State::getStateList()->get();
            //dd($states);
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
                    'comp_zip' => $arrAnchorVal['pin_code']
                ];
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
     *  function for upload anchor lead using csv
     * @param Request $request
     * @return type
     */
    public function uploadAnchorlead(Request $request) {
        try {
              $roleData = Helpers::getUserRole();
            $is_superadmin = isset($roleData[0]) ? $roleData[0]->is_superadmin : 0;
                $anchLeadList = $this->userRepo->getAllAnchor();                
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
            $uploadedFile = $request->file('anchor_lead');
            $destinationPath = storage_path() . '/uploads';
            $fileName = time() . '.csv';
            if ($uploadedFile->isValid()) {
                $uploadedFile->move($destinationPath, $fileName);
            }
            $fileD = fopen($destinationPath . '/' . $fileName, "r");
            $column = fgetcsv($fileD);
            while (!feof($fileD)) {
                $rowData[] = fgetcsv($fileD);
            }

            $anchLeadMailArr = [];
            $arrAnchLeadData = [];
            $arrUpdateAnchor = [];
            foreach ($rowData as $key => $value) {
                
                $anchUserInfo=$this->userRepo->getAnchorUsersByEmail(trim($value[3]));  
                if(!empty($value) && !$anchUserInfo){
                $hashval = time() . 'ANCHORLEAD' . $key;
                $token = md5($hashval);
                    if(trim($value[4])=='Buyer'){
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
                ];
                $anchor_lead = $this->userRepo->saveAnchorUser($arrAnchLeadData);
                
                $getAnchorId =$this->userRepo->getUserDetail(Auth::user()->user_id);
                if($getAnchorId && $getAnchorId->anchor_id!=''){
                $arrUpdateAnchor ['anchor_id'] = $getAnchorId->anchor_id;
                }else{
                 $arrUpdateAnchor ['anchor_id'] =$request->post('assigned_anchor');
                }
               $getAnchorId =$this->userRepo->updateAnchorUser($anchor_lead,$arrUpdateAnchor);
                
                if ($anchor_lead) {
                    $mailUrl = config('proin.frontend_uri') . '/sign-up?token=' . $token;
                    $anchLeadMailArr['name'] = $arrAnchLeadData['name'];
                    $anchLeadMailArr['email'] =  trim($arrAnchLeadData['email']);
                    $anchLeadMailArr['url'] = $mailUrl;
                    Event::dispatch("ANCHOR_CSV_LEAD_UPLOAD", serialize($anchLeadMailArr));
                }
          }
            }
            //chmod($destinationPath . '/' . $fileName, 0775, true);
            unlink($destinationPath . '/' . $fileName);
            Session::flash('message', trans('backend_messages.anchor_registration_success'));
           return redirect()->route('get_anchor_lead_list');
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
     * function for update anchor information
     * @param Request $request
     * @return type
     */
    public function updateAnchorReg(Request $request) {
        try {
            $arrAnchorVal = $request->all();
            $anchId = $request->post('anchor_id');
            $anchId=(int)$anchId;
            $arrAnchorData = [
                'comp_name' => $arrAnchorVal['comp_name'],
                'comp_email' => $arrAnchorVal['email'],
                'sales_user_id' => $arrAnchorVal['assigned_sale_mgr'],
                'comp_phone' => $arrAnchorVal['phone'],
                'comp_addr' => $arrAnchorVal['comp_addr'],
                'comp_state' => $arrAnchorVal['state'],
                'comp_city' => $arrAnchorVal['city'],
                'comp_zip' => $arrAnchorVal['pin_code']
            ];
            $updateAnchInfo = $this->userRepo->updateAnchor($anchId, $arrAnchorData);            
            $anchorInfo = $this->userRepo->getUserByAnchorId($anchId);
            $arrAnchorUserData = [
                'f_name' => $arrAnchorVal['employee'],
                'biz_name' => $arrAnchorData['comp_name'],
                'email' => $arrAnchorData['comp_email'],
                'mobile_no' => $arrAnchorData['comp_phone'],
            ];
            $Updateanchorinfo = $this->userRepo->updateUser($arrAnchorUserData, (int) $anchorInfo->user_id);
            
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
        return view('backend.anchor.anchor_lead_list');
    }
    
     public function addManualAnchorLead() {
      try{
          $roleData = Helpers::getUserRole();
          $is_superadmin = isset($roleData[0]) ? $roleData[0]->is_superadmin : 0;
       $anchLeadList = $this->userRepo->getAllAnchor();
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
             $anchUserInfo=$this->userRepo->getAnchorUsersByEmail(trim($arrAnchorVal['email']));
             $arrUpdateAnchor =[];
             if(!$anchUserInfo){
              $hashval = time() . '2348923ANCHORLEAD'.$arrAnchorVal['email'];
                $token = md5($hashval);
             $arrAnchorData = [
                'name' => trim($arrAnchorVal['f_name']),
                 'l_name' => trim($arrAnchorVal['l_name']),
                'biz_name' => $arrAnchorVal['comp_name'],
                'email' => trim($arrAnchorVal['email']),
                'phone' => $arrAnchorVal['phone'],
                'user_type' => $arrAnchorVal['anchor_user_type'],
                 'is_registered'=>0,
                 'registered_type'=>0,
                'created_by' => Auth::user()->user_id,
                 'created_at' => \Carbon\Carbon::now(),
                 'token' => $token,
            ];
            
             $anchor_lead = $this->userRepo->saveAnchorUser($arrAnchorData);
            $getAnchorId =$this->userRepo->getUserDetail(Auth::user()->user_id);
            
            if($getAnchorId && $getAnchorId->anchor_id!=''){
                $arrUpdateAnchor ['anchor_id'] = $getAnchorId->anchor_id;
            }else{
                 $arrUpdateAnchor ['anchor_id'] =$arrAnchorVal['assigned_anchor'];
            }
            
//            $arrUpdateAnchor = [
//                'anchor_id' => $getAnchorId->anchor_id
//                ];
            
            $getAnchorId =$this->userRepo->updateAnchorUser($anchor_lead,$arrUpdateAnchor);
            if ($anchor_lead) {
                $mailUrl = config('proin.frontend_uri') . '/sign-up?token=' . $token;
                $anchLeadMailArr['name'] = trim($arrAnchorData['name']);
                $anchLeadMailArr['email'] =  trim($arrAnchorData['email']);
                $anchLeadMailArr['url'] = $mailUrl;
                Event::dispatch("ANCHOR_CSV_LEAD_UPLOAD", serialize($anchLeadMailArr));
                Session::flash('message', trans('backend_messages.anchor_registration_success'));
                return redirect()->route('get_anchor_lead_list');
            }
            }else{
            Session::flash('error', trans('error_messages.email_already_exists'));
            return redirect()->route('get_anchor_lead_list');
            }
        } catch (Exception $ex) {
            return redirect()->back()->withErrors(Helpers::getExceptionMessage($ex));
        }
    }
    
}
