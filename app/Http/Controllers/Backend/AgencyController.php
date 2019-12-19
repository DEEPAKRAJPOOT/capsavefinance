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
            return view('backend.agency.add_agency_reg')->with(['states'=>$states]);
        } catch (Exception $ex) {
             return redirect()->back()->withErrors(Helpers::getExceptionMessage($ex));
        }
    }

    /**
     * function for save agency info
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
     * function for update agency page info
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
     * function for update agency information
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
    public function updateAgencyUserReg(Request $request){
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
                //'is_active' => 1,
            ];
            $current_user_info = $this->userRepo->save($arrAgencyUserData, $user_id);
            
            
            Session::flash('message', trans('backend_messages.agency_user_registration_updated'));
            return redirect()->route('get_agency_user_list');
        } catch (Exception $ex) {
            return redirect()->back()->withErrors(Helpers::getExceptionMessage($ex));
        }
    }
    
}
