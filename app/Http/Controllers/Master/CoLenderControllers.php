<?php

namespace App\Http\Controllers\Master;

use Auth;
use Session;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Inv\Repositories\Models\Master\State;
use App\Inv\Repositories\Contracts\UserInterface as InvUserRepoInterface;
use App\Inv\Repositories\Contracts\MasterInterface as InvMasterRepoInterface;

class CoLenderControllers extends Controller {

    protected $userRepo;

    public function __construct(InvMasterRepoInterface $master, InvUserRepoInterface $user)
    {
        $this->middleware('auth');
        $this->middleware('checkBackendLeadAccess');
        $this->masterRepo = $master;
        $this->userRepo = $user;
    }

    /**
     * co lenders list
     * 
     * @return mixed
     */
    public function getColenders()
    {
        return view('backend.coLenders.co_lenders_list');
    }

    /**
     * add co lender
     * 
     * @return mixed
     */
    public function addCoLender()
    {
        $states = State::getStateList()->get();

        return view('backend.coLenders.add_co_lender_frm')->with(['states' => $states]);
    }

    public function saveCoLender(Request $request)
    {
        try {
            //$string = Helpers::randomPassword();
            $string = time();
            $request = $request->all();
            $user_info = $this->userRepo->getUserByEmail($request['email']);
            
            if (!$user_info) {
                $data = [
                    'comp_name' => $request['comp_name'],
                    'comp_email' => $request['email'],
                    'comp_phone' => $request['phone'],
                    'comp_addr' => $request['comp_addr'],
                    'comp_state' => $request['state'],
                    'comp_city' => $request['city'],
                    'comp_zip' => $request['pin_code']
                ];
                
                  $arrAnchorData = [
                'name' => trim($request['f_name']),
                 'l_name' => trim($request['l_name']),
                'biz_name' => $request['comp_name'],
                'email' => trim($request['email']),
                'phone' => $request['phone'],
                'user_type' => $request['anchor_user_type'],
                 'is_registered'=>0,
                 'registered_type'=>0,
                'created_by' => Auth::user()->user_id,
                 'created_at' => \Carbon\Carbon::now(),
                // 'token' => $token,
            ];
           
                
                
             //   $anchor_info = $this->userRepo->saveColenderUsers($data);
     dd($arrAnchorData ,$data);
                $arrAnchUserData = [
                    'anchor_id' => $anchor_info,
                    'f_name' => $request['employee'],
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
                    Session::flash('message', trans('backend_messages.anchor_registration_success'));
                    return redirect()->route('get_anchor_list');
                }
            } else {
                Session::flash('error', trans('error_messages.email_already_exists'));
                return redirect()->route('get_anchor_list');
            }
        } catch (Exception $ex) {
            return redirect()->back()->withErrors(Helpers::getExceptionMessage($ex));
        }
    }

}
