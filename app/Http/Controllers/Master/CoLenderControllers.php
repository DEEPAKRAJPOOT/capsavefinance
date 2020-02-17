<?php

namespace App\Http\Controllers\Master;

use Auth;
use Session;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Inv\Repositories\Models\Master\State;
use App\Inv\Repositories\Contracts\UserInterface as InvUserRepoInterface;
use App\Inv\Repositories\Contracts\ApplicationInterface as InvAppRepoInterface;
use App\Inv\Repositories\Contracts\MasterInterface as InvMasterRepoInterface;

class CoLenderControllers extends Controller {

    protected $userRepo;

    public function __construct(InvMasterRepoInterface $master, InvUserRepoInterface $user, InvAppRepoInterface $app_repo)
    {
        $this->middleware('auth');
        $this->middleware('checkBackendLeadAccess');
        $this->masterRepo = $master;
        $this->appRepo = $app_repo;
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
    public function addCoLender(Request $request)
    {
        $states = State::getStateList()->get();

        $co_lender_id = (int) $request->get('co_lender_id');
        $coLenderData = $this->userRepo->getCoLenderData(['co_lenders_user.co_lender_id' => $co_lender_id])->first();
       // dd($coLenderData);
        Session::forget('operation_status');
        return view('backend.coLenders.add_co_lender_frm')
                        ->with(['states' => $states, 'coLenderData' => $coLenderData]);
    }

    public function saveCoLender(Request $request)
    {
        try {
            //$string = Helpers::randomPassword();
            $string = time();
            $request = $request->all();
            $user_info = false;
            $co_lender_id = $request['co_lender_id'] ? $request['co_lender_id'] : null;
            
            $user_id = $request['user_id'] ? $request['user_id'] : null;
            if (isset($request['email'])) {
                $user_info = $this->userRepo->getUserByEmail($request['email']);
            }


            if (!$user_info) {
                $data = [
                    'comp_name' => $request['comp_name'],
                    'comp_email' => isset($request['email']) ? $request['email'] : null,
                    'comp_phone' => $request['phone'],
                    'comp_addr' => $request['comp_addr'],
                    'comp_state' => $request['state'],
                    'comp_city' => $request['city'],
                    'comp_zip' => $request['pin_code'],
                    'gst' => $request['gst'],
                    'pan_no' => $request['pan_no'],
                    'is_active'=>$request['is_active']
                ];
               
                if ($co_lender_id > 0) {
                    unset($data['comp_email']);
                }
                
      

                $lastInsertID = $this->userRepo->saveColenderUsers($data, (int) $co_lender_id);
                $lastInsertID = $co_lender_id ? $co_lender_id : $lastInsertID;
                $userData = [
                    'co_lender_id' => $lastInsertID,
                    'f_name' => $request['employee'],
                    'biz_name' => $data['comp_name'],
                    'email' => isset($data['comp_email']) ? $data['comp_email'] : null,
                    'mobile_no' => $data['comp_phone'],
                    'user_type' => 2,
                    'is_email_verified' => 1,
                    'is_active' => 1,
                    'password' => bcrypt($string)
                ];
                if ($co_lender_id > 0) {
                    unset($userData['co_lender_id']);
                    unset($userData['email']);
                    unset($userData['user_type']);
                    unset($userData['is_email_verified']);
                    unset($userData['is_active']);
                    unset($userData['password']);
                }


                $userInfo = $this->userRepo->save($userData, $user_id);
                if (!$co_lender_id) {
                    $mail = [];
                    $userData['email'] = $userData['email'];
                    $userData['name'] = $userData['f_name'];
                    $userData['password'] = $string;
                    \Event::dispatch("CO_LENDER_USER_REGISTER_MAIL", serialize($userData));
                    if ($lastInsertID && $userInfo) {
                        $role = [];
                        $role['role_id'] = 15;
                        $role['user_id'] = $userInfo->user_id;
                        $rr = $this->userRepo->addNewRoleUser($role);
                        Session::flash('message', trans('backend_messages.co_lender_registration_success'));
                        Session::put('operation_status', 1);
                        return redirect()->back();
                    }
                }
                Session::flash('message', trans('backend_messages.co_lender_registration_update'));
                Session::put('operation_status', 1);
                return redirect()->back();
            } else {
                Session::flash('error', trans('error_messages.email_already_exists'));
                return redirect()->back();
            }
        } catch (Exception $ex) {
            return redirect()->back()->withErrors(Helpers::getExceptionMessage($ex));
        }
    }

    public function shareToColender(){
        $coLenders = $this->userRepo->getCoLenderData(['co_lenders_user.is_active'=>1, 'u.is_active'=>1]);
        return view('backend.coLenders.share_colender')->with('coLenders', $coLenders);
    }

    public function saveShareToColender(Request $request){
        try {
            $arrShareColenderData = $request->all();
            $appId = $arrShareColenderData['app_id'];
            $bizId = $arrShareColenderData['biz_id'];
            $arrShareColenderData['created_at'] = \carbon\Carbon::now();
            $arrShareColenderData['created_by'] = Auth::user()->user_id;
            $data = $this->appRepo->getSharedColender([
                    'app_id'=>$arrShareColenderData['app_id'],
                    'app_prgm_limit_id' =>  $arrShareColenderData['app_prgm_limit_id'],
                    'co_lender_id'      =>  $arrShareColenderData['co_lender_id'],
                    'is_active'         =>  1
                ]);
            if($data->count()){
                Session::flash('message', 'This colender is already associated with this offer.');
                return redirect()->back()->withInput($request->input());
            }else{
                $status = $this->appRepo->saveShareToColender($arrShareColenderData);
                if($status){
                    Session::flash('message', 'Offer shared successfully');
                    Session::flash('operation_status', 1); 
                    return redirect()->route('limit_assessment', ['app_id'=>(int)$appId, 'biz_id'=>(int)$bizId, 'view_only'=>$arrShareColenderData['view_only']]);
                }else{
                    Session::flash('message', trans('backend_messages.something_went_wrong'));
                    Session::flash('operation_status', 1); 
                    return redirect()->route('limit_assessment', ['app_id'=>(int)$appId, 'biz_id'=>(int)$bizId, 'view_only'=>$arrShareColenderData['view_only']]);
                }
            }
        } catch (Exception $ex) {
            return redirect()->back()->withErrors(Helpers::getExceptionMessage($ex));
        }
    }

}
