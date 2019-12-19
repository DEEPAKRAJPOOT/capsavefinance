<?php

namespace App\Http\Controllers\Backend;

use Auth;
use Session;
use Crypt;
use Helpers;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Inv\Repositories\Models\Master\State;
use App\Inv\Repositories\Models\Agency;
use App\Http\Requests\AgencyRegistrationFormRequest;
use App\Inv\Repositories\Contracts\UserInterface as InvUserRepoInterface;
use App\Inv\Repositories\Contracts\ApplicationInterface as InvAppRepoInterface;
use Event;

class AgencyController extends Controller {

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
     * Display anchor listing
     *
     * @return \Illuminate\Http\Response
     */
    public function allAgencyList() {
        return view('backend.agency.agency_list');
    }

    /**
    * 
    * @return type
    */
     public function getAgencyUserList() {
        return view('backend.agency.agency_user_list');
    }

    public function addAgencyReg(Request $request) {
        try {
            $states = State::getStateList()->get();
            return view('backend.agency.add_agency_reg')->with(['states'=>$states]);
        } catch (Exception $ex) {
             return redirect()->back()->withErrors(Helpers::getExceptionMessage($ex));
        }
    }

    /**
     * function for save anchor info and also create anchor user
     * @param Request $request
     * @return type
     */
    public function saveAgencyReg(Request $request) {
        try {
            $arrAgencyData = $request->all();
            $arrAgencyData['created_at'] = \carbon\Carbon::now();
            $status = $this->userRepo->saveAgency($arrAgencyData);
            if($status){
                Session::flash('message', trans('backend_messages.agency_registration_success'));
                return redirect()->route('get_agency_list');
            }else{
                Session::flash('message', trans('backend_messages.something_went_wrong'));
                return redirect()->route('get_agency_list');
            }
        } catch (Exception $ex) {
            return redirect()->back()->withErrors(Helpers::getExceptionMessage($ex));
        }
    }


    /**
     * function for update anchor info
     * @param Request $request
     * @return type
     */
    public function editAgencyReg(Request $request) {
        try {
            $agencyId = $request->get('agency_id');
            if($agencyId) {
                $agencyData = $this->userRepo->getAgencyById($agencyId);
            }
            $states = State::getStateList()->get();
            return view('backend.agency.edit_agency_reg')
                    ->with(['agencyData'=>$agencyData, 'states'=>$states]);
        } catch (Exception $ex) {
            return redirect()->back()->withErrors(Helpers::getExceptionMessage($ex));
        }
    }

    /**
     * function for update anchor information
     * @param Request $request
     * @return type
     */
    public function updateAgencyReg(Request $request) {
        try {
            $arrAgencyData = [
                        'comp_name'=>$request->comp_name,
                        'comp_email'=>$request->comp_email,
                        'comp_phone'=>$request->comp_phone,
                        'comp_addr'=>$request->comp_addr,
                        'comp_state'=>$request->comp_state,
                        'comp_city'=>$request->comp_city,
                        'comp_zip'=>$request->comp_zip,
                        'updated_at'=>\carbon\Carbon::now(),
                        'updated_by'=>Auth::user()->user_id
                    ];
            $status = $this->userRepo->updateAgency($arrAgencyData, $request->agency_id);
            if($status){
                Session::flash('message', trans('backend_messages.agency_registration_updated'));
                return redirect()->route('get_agency_list');
            }else{
                Session::flash('message', trans('backend_messages.something_went_wrong'));
                return redirect()->route('get_agency_list');
            }
        } catch (Exception $ex) {
            return redirect()->back()->withErrors(Helpers::getExceptionMessage($ex));
        }
    }

    public function addAgencyUserReg(Request $request) {
        try {
            $agencies = Agency::where('is_active',1)->get();
            return view('backend.agency.add_agency_user_reg')->with(['agencies'=>$agencies]);
        } catch (Exception $ex) {
             return redirect()->back()->withErrors(Helpers::getExceptionMessage($ex));
        }
    }

    /**
     * function for save anchor info and also create anchor user
     * @param Request $request
     * @return type
     */
    public function saveAgencyUserReg(Request $request) {
        try {
            $string = time();
            $arrAgencyData = $request->all();
            $user_info = $this->userRepo->getUserByEmail($arrAgencyData['email']);
            if(!$user_info){
                $arrAgencyUserData = [
                    'agency_id' => $arrAgencyData['agency_id'],
                    'f_name' => $arrAgencyData['f_name'],
                    'l_name' => $arrAgencyData['l_name'],
                    'email' => $arrAgencyData['email'],
                    'mobile_no' => $arrAgencyData['mobile_no'],
                    'biz_name' => '',
                    'user_type' => 2,
                    'is_email_verified' => 1,
                    'is_active' => 1,
                    'password' => bcrypt($string)
                ];
                $current_user_info = $this->userRepo->save($arrAgencyUserData);
                $this->userRepo->saveUserDetails([
                    'user_id'=>$current_user_info->user_id,
                    'created_by'=>Auth::user()->user_id,
                    'created_at'=>\Carbon\Carbon::now(),
                    ]);
                // for role user entry
                $current_user_info = $this->userRepo->addNewRoleUser(['user_id'=>$current_user_info->user_id, 'role_id'=>12]);
                $agencyUserMailArr = [];
                $agencyUserMailArr['email'] = $arrAgencyUserData['email'];
                $agencyUserMailArr['name'] = $arrAgencyUserData['f_name'];
                $agencyUserMailArr['password'] = $string;
                Event::dispatch("AGENCY_USER_REGISTER_MAIL", serialize($agencyUserMailArr));
                Session::flash('message', trans('backend_messages.agency_user_registration_success'));
                return redirect()->route('get_agency_user_list');
            }else{
                Session::flash('message', trans('backend_messages.something_went_wrong'));
                return redirect()->route('get_agency_user_list');
            }
        } catch (Exception $ex) {
            return redirect()->back()->withErrors(Helpers::getExceptionMessage($ex));
        }
    }

    /*------------------------------------------------------*/   
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
