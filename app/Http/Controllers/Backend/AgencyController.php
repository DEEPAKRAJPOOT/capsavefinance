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
use App\Http\Requests\AgencyUserFormRequest;
use App\Inv\Repositories\Contracts\UserInterface as InvUserRepoInterface;
use App\Inv\Repositories\Contracts\ApplicationInterface as InvAppRepoInterface;
use App\Inv\Repositories\Contracts\MasterInterface as InvMasterRepoInterface;
use Event;
use App\Inv\Repositories\Contracts\Traits\ActivityLogTrait;

class AgencyController extends Controller {

    protected $userRepo;
    protected $appRepo;
    protected $masterRepo;

    use ActivityLogTrait;
    
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct(InvUserRepoInterface $user, InvAppRepoInterface $app_repo, InvMasterRepoInterface $master_repo) {
        $this->middleware('guest')->except('logout');
        $this->middleware('checkBackendLeadAccess');

        $this->userRepo = $user;
        $this->appRepo = $app_repo;
        $this->masterRepo = $master_repo;
    }

    /**
     * Display agency listing
     *
     * @return \Illuminate\Http\Response
     */
    public function allAgencyList() {
        return view('backend.agency.agency_list');
    }

    /**
    * Display agency user listing
     *
     * @return \Illuminate\Http\Response
    */
     public function getAgencyUserList() {
        return view('backend.agency.agency_user_list');
    }

    /**
    * Add agency view page
    */
    public function addAgencyReg(Request $request) {
        try {
            $states = State::getStateList()->get();
            $agency_types = $this->masterRepo->getAppStatus(5);
            return view('backend.agency.add_agency_reg')->with(['states'=>$states, 'agency_types'=>$agency_types]);
        } catch (Exception $ex) {
             return redirect()->back()->withErrors(Helpers::getExceptionMessage($ex));
        }
    }

    /**
     * function for save agency info
     * @param Request $request
     * @return type
     */
    public function saveAgencyReg(AgencyRegistrationFormRequest $request) {
        try {
            $arrAgencyData = $request->all();
            $arrAgencyData['created_at'] = \carbon\Carbon::now();
            $arrAgencyData['created_by'] = Auth::user()->user_id;
            $status = $this->userRepo->saveAgency($arrAgencyData);
            
            $whereActivi['activity_code'] = 'save_agency_reg';
            $activity = $this->masterRepo->getActivity($whereActivi);
            if(!empty($activity)) {
                $activity_type_id = isset($activity[0]) ? $activity[0]->id : 0;
                $activity_desc = 'Add Agency in Manage Agency';
                $this->activityLogByTrait($activity_type_id, $activity_desc, response()->json($arrAgencyData));                
            }            
            if($status){
                Session::flash('message', trans('backend_messages.agency_registration_success'));
                Session::flash('operation_status', 1); 
                return redirect()->route('get_agency_list');
            }else{
                Session::flash('message', trans('backend_messages.something_went_wrong'));
                Session::flash('operation_status', 1); 
                return redirect()->route('get_agency_list');
            }
        } catch (Exception $ex) {
            return redirect()->back()->withErrors(Helpers::getExceptionMessage($ex));
        }
    }


    /**
     * function for update agency page info
     * @param Request $request
     * @return type
     */
    public function editAgencyReg(Request $request) {
        try {
            $type_ids = [];
            $agencyId = $request->get('agency_id');
            if($agencyId) {
                $agencyData = $this->userRepo->getAgencyById($agencyId);

                foreach($agencyData->agencyType as $type){
                  array_push($type_ids, $type->pivot->type_id);
                }
            }

            $states = State::getStateList()->get();
            $agency_types = $this->masterRepo->getAppStatus(5);
            return view('backend.agency.edit_agency_reg')
                    ->with(['agencyData'=>$agencyData, 'type_ids'=>$type_ids, 'states'=>$states, 'agency_types'=>$agency_types]);
        } catch (Exception $ex) {
            return redirect()->back()->withErrors(Helpers::getExceptionMessage($ex));
        }
    }

    /**
     * function for update agency information
     * @param Request $request
     * @return type
     */
    public function updateAgencyReg(AgencyRegistrationFormRequest $request) {
        try {
            $arrAgencyData = [
                        'comp_name'=>$request->comp_name,
                        'type_id'=>$request->type_id,
                        'comp_email'=>$request->comp_email,
                        'comp_phone'=>$request->comp_phone,
                        'comp_addr'=>$request->comp_addr,
                        'comp_state'=>$request->comp_state,
                        'comp_city'=>$request->comp_city,
                        'comp_zip'=>$request->comp_zip,
                        'is_active'=>$request->is_active,
                        'updated_at'=>\carbon\Carbon::now(),
                        'updated_by'=>Auth::user()->user_id
                    ];
            $status = $this->userRepo->updateAgency($arrAgencyData, $request->agency_id);

            $whereActivi['activity_code'] = 'update_agency_reg';
            $activity = $this->masterRepo->getActivity($whereActivi);
            if(!empty($activity)) {
                $activity_type_id = isset($activity[0]) ? $activity[0]->id : 0;
                $activity_desc = 'Update Agency User in Manage Agency AgencyId ' .$request->agency_id;
                $this->activityLogByTrait($activity_type_id, $activity_desc, response()->json($arrAgencyData));                
            }            
            
            if($status){
                Session::flash('message', trans('backend_messages.agency_registration_updated'));
                Session::flash('operation_status', 1); 
                return redirect()->route('get_agency_list');
            }else{
                Session::flash('message', trans('backend_messages.something_went_wrong'));
                Session::flash('operation_status', 1); 
                return redirect()->route('get_agency_list');
            }
        } catch (Exception $ex) {
            return redirect()->back()->withErrors(Helpers::getExceptionMessage($ex));
        }
    }

    /**
    * Add agency user view page
    */
    public function addAgencyUserReg(Request $request) {
        try {
            $agencies = Agency::where('is_active',1)->get();
            return view('backend.agency.add_agency_user_reg')->with(['agencies'=>$agencies]);
        } catch (Exception $ex) {
             return redirect()->back()->withErrors(Helpers::getExceptionMessage($ex));
        }
    }

    /**
     * function for create agency user info
     * @param Request $request
     * @return type
     */
    public function saveAgencyUserReg(AgencyUserFormRequest $request) {
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
                    'is_active' => $arrAgencyData['is_active'],
                    'password' => bcrypt($string)
                ];
                $current_user_info = $this->userRepo->save($arrAgencyUserData);

                $whereActivi['activity_code'] = 'save_agency_user_reg';
                $activity = $this->masterRepo->getActivity($whereActivi);
                if(!empty($activity)) {
                    $activity_type_id = isset($activity[0]) ? $activity[0]->id : 0;
                    $activity_desc = 'Add Agency User in Manage Agency User';
                    $this->activityLogByTrait($activity_type_id, $activity_desc, response()->json($arrAgencyUserData));                
                }
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
                Session::flash('operation_status', 1);
                return redirect()->route('get_agency_user_list');
            }else{
                Session::flash('message', trans('backend_messages.something_went_wrong'));
                Session::flash('operation_status', 1);
                return redirect()->route('get_agency_user_list');
            }
        } catch (Exception $ex) {
            return redirect()->back()->withErrors(Helpers::getExceptionMessage($ex));
        }
    }


    /**
     * function for update agency user page info
     * @param Request $request
     * @return type
     */
    public function editAgencyUserReg(Request $request) {
        try {
            $agencies = Agency::where('is_active',1)->get();
            $agencyUser = $this->userRepo->getUserDetail($request->get('user_id'));
            return view('backend.agency.edit_agency_user_reg')->with(['agencyUser'=>$agencyUser, 'agencies'=>$agencies]);
        } catch (Exception $ex) {
             return redirect()->back()->withErrors(Helpers::getExceptionMessage($ex));
        }
    }
  
    /**
     * function for update agency user info
     * @param Request $request
     * @return type
     */
    public function updateAgencyUserReg(AgencyUserFormRequest $request){
        try {
            $arrAgencyData = $request->all();
            $user_id = $request->get('user_id');
            //$user_info = $this->userRepo->getUserByEmail($arrAgencyData['email']);
            $arrAgencyUserData = [
                'agency_id' => $arrAgencyData['agency_id'],
                'f_name' => $arrAgencyData['f_name'],
                'l_name' => $arrAgencyData['l_name'],
                //'email' => $arrAgencyData['email'],
                'mobile_no' => $arrAgencyData['mobile_no'],
                'biz_name' => '',
                'is_active' => $arrAgencyData['is_active']
            ];
            $current_user_info = $this->userRepo->save($arrAgencyUserData, $user_id);
            $whereActivi['activity_code'] = 'update_agency_user_reg';
            $activity = $this->masterRepo->getActivity($whereActivi);
            if(!empty($activity)) {
                $activity_type_id = isset($activity[0]) ? $activity[0]->id : 0;
                $activity_desc = 'Update Agency User in Manage Agency user AgencyId ' .$arrAgencyData['agency_id'];
                $this->activityLogByTrait($activity_type_id, $activity_desc, response()->json($arrAgencyUserData));                
            }            
            Session::flash('message', trans('backend_messages.agency_user_registration_updated'));
            Session::flash('operation_status', 1);
            return redirect()->route('get_agency_user_list');
        } catch (Exception $ex) {
            return redirect()->back()->withErrors(Helpers::getExceptionMessage($ex));
        }
    }
    
}
