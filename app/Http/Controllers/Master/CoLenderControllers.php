<?php

namespace App\Http\Controllers\Master;

use Auth;
use Session;
use Illuminate\Http\Request;
use App\Http\Requests\Master\CoLenderRequest;
use App\Http\Controllers\Controller;
use App\Inv\Repositories\Models\Master\State;
use App\Inv\Repositories\Contracts\UserInterface as InvUserRepoInterface;
use App\Inv\Repositories\Contracts\ApplicationInterface as InvAppRepoInterface;
use App\Inv\Repositories\Contracts\MasterInterface as InvMasterRepoInterface;
use App\Inv\Repositories\Contracts\LmsInterface as InvLmsRepoInterface;
use App\Inv\Repositories\Contracts\Traits\ActivityLogTrait;

class CoLenderControllers extends Controller {

    protected $userRepo;
    protected $lmsRepo;
    use ActivityLogTrait;

    public function __construct(InvMasterRepoInterface $master, InvUserRepoInterface $user, InvAppRepoInterface $app_repo, InvLmsRepoInterface $lms_repo)
    {
        $this->middleware('auth');
        $this->middleware('checkBackendLeadAccess');
        $this->masterRepo = $master;
        $this->appRepo = $app_repo;
        $this->lmsRepo = $lms_repo;
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
        $coLenderData = $this->userRepo->getCoLenderData(['co_lenders.co_lender_id' => $co_lender_id])->first();
       // dd($coLenderData);
        Session::forget('operation_status');
        return view('backend.coLenders.add_co_lender_frm')
                        ->with(['states' => $states, 'coLenderData' => $coLenderData]);
    }

    public function saveCoLender(CoLenderRequest $request)
    {
        try {
            $string = time();
            $request = $request->all();
            $user_info = false;
            $co_lender_id = $request['co_lender_id'] ??  null;
            $user_id = $request['user_id'] ?? null;

            if (isset($request['email'])) {
                $user_info = $this->userRepo->getUserByEmail($request['email']);
            }

            if (!$user_info) {
                $data = [
                    'comp_name' => $request['comp_name'],
                    'comp_email' => $request['email'] ?? null,
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

    public function shareToColender(Request $request){
        $appId = $request->get('app_id');
        $user_id = State::getUserByAPP($appId)->user_id ?? NULL;
        $sharedColender = $this->appRepo->getSharedColender([
            'user_id'=>$user_id,
            'is_active'   =>  1
        ]);
        if($sharedColender->count()){
          $sharedColender = $sharedColender[0];
        }
        $coLenders = $this->userRepo->getCoLenderData(['co_lenders.is_active'=>1, 'u.is_active'=>1]);
        return view('backend.coLenders.share_colender')->with('coLenders', $coLenders)->with('sharedColender', $sharedColender);
    }

    public function saveShareToColender(Request $request){
        try {
            $arrShareColenderData = $request->all();
            $appId = $arrShareColenderData['app_id'];
            $bizId = $arrShareColenderData['biz_id'];
            $arrShareColenderData['user_id'] = State::getUserByAPP($appId)->user_id ?? NULL;
            $arrShareColenderData['start_date'] = \carbon\Carbon::now();
            $arrShareColenderData['end_date'] = NULL;
            $arrShareColenderData['is_active'] = 1;
            $arrShareColenderData['created_at'] = \carbon\Carbon::now();
            $arrShareColenderData['created_by'] = Auth::user()->user_id;
            $data = $this->appRepo->getSharedColender([
                'app_id'=>$arrShareColenderData['app_id'],
                'user_id'=>$arrShareColenderData['user_id'],
                'app_prgm_limit_id' =>  $arrShareColenderData['app_prgm_limit_id'],
                'is_active'   =>  1
            ], $arrShareColenderData['co_lender_id']);
            if($data->count()){
                Session::flash('error', 'You can\'t share this application offer to more than one co-lender.');
                return redirect()->back()->withInput($request->input());
            }else{
                $this->appRepo->updateColenderData(['is_active' => 0, 'end_date' => \carbon\Carbon::now()], ['user_id' => $arrShareColenderData['user_id'], 'co_lender_id' => $arrShareColenderData['co_lender_id']]);
                $status = $this->appRepo->saveShareToColender($arrShareColenderData);

                $whereActivi['activity_code'] = 'save_share_to_colender';
                $activity = $this->masterRepo->getActivity($whereActivi);
                if(!empty($activity)) {
                    $activity_type_id = isset($activity[0]) ? $activity[0]->id : 0;
                    $activity_desc = 'Share with Co-Lender. AppID '. $arrShareColenderData['app_id'];
                    $arrActivity['app_id'] = $arrShareColenderData['app_id'];
                    $this->activityLogByTrait($activity_type_id, $activity_desc, response()->json($arrShareColenderData), $arrActivity);
                }                   
                
                if($status){
                    Session::flash('message', 'Offer shared successfully');
                    Session::flash('operation_status', 1); 
                    return redirect()->route('limit_assessment', ['app_id'=>(int)$appId, 'biz_id'=>(int)$bizId, 'view_only'=>$arrShareColenderData['view_only']]);
                }else{
                    Session::flash('error', trans('backend_messages.something_went_wrong'));
                    Session::flash('operation_status', 1); 
                    return redirect()->route('limit_assessment', ['app_id'=>(int)$appId, 'biz_id'=>(int)$bizId, 'view_only'=>$arrShareColenderData['view_only']]);
                }
            }
        } catch (Exception $ex) {
            return redirect()->back()->withErrors(Helpers::getExceptionMessage($ex));
        }
    }

    public function viewSharedColender(Request $request){
        $shared_colenders = $this->appRepo->getSharedColender(['app_id'=>$request->app_id, 'app_prgm_limit_id'=>$request->app_prgm_limit_id, 'is_active'=>1]);
        return view('backend.coLenders.view_shared_colender')->with('sharedCoLenders', $shared_colenders);
    }

    public function viewColenderSoa(Request $request){
       $userData = [];
        if($request->has('user_id')){
            $user = $this->userRepo->lmsGetCustomer($request->user_id);
            $maxInterestDPD = $this->lmsRepo->getMaxDpdTransaction($request->user_id , config('lms.TRANS_TYPE.INTEREST'));
            $maxPrincipalDPD = $this->lmsRepo->getMaxDpdTransaction($request->user_id , config('lms.TRANS_TYPE.PAYMENT_DISBURSED'));
            if($user && $user->app_id){
                $userData['user_id'] = $user->user_id;
                $userData['customer_id'] = $user->customer_id;
                $appDetail = $this->appRepo->getAppDataByAppId($user->app_id);
                if($appDetail){
                    $userData['app_id'] = $appDetail->app_id;
                    $userData['biz_id'] = $appDetail->biz_id;
                }
            }
        }
        return view('backend.coLenders.view_soa')
        ->with('user',$userData)
        ->with('maxDPD',1)
        ->with('maxPrincipalDPD',$maxPrincipalDPD)
        ->with('maxInterestDPD',$maxInterestDPD);
    }

    /**
     * Display a application for Colender
     */
    public function appList()
    {
        return view('backend.coLenders.app_list');   
    }

    public function showOffer(Request $request)
    {
        $appId = $request->get('app_id');
        $bizId = $request->get('biz_id');
        $user_id  = Auth::user()->user_id;
        //$appData = $this->appRepo->getAppDataByAppId($appId);        
        //$loanAmount = $appData ? $appData->loan_amt : 0;
        
        $supplyOfferData = $this->appRepo->getAllOffers($appId, 1);//for supply chain
        $offerStatus = $this->appRepo->getOfferStatus(['app_id' => $appId, 'is_approve'=>1, 'is_active'=>1, 'status'=>1]);//to check the offer status
        
        $colenderShare = $this->appRepo->getAppDataByAppId($appId)->colender;

        return view('backend.coLenders.offer')
            ->with('appId', $appId)
            ->with('bizId', $bizId)                
            ->with('supplyOfferData', $supplyOfferData)
            ->with('offerStatus', $offerStatus)
            ->with('colenderShare', $colenderShare)
            ->with('user_id', $user_id);
    }

    /**
     * Accept Offer
     * 
     * @param Request $request
     */
    public function acceptOffer(Request $request)
    {
        $appId = $request->get('app_id');        
        $bizId = $request->get('biz_id');        
        $viewOnly = $request->get('view_only');        
        $co_lenders_share_id = $request->get('co_lenders_share_id');
        try {
            if ($request->has('btn_accept_offer')) {
                $message = trans('backend_messages.accept_offer_success');
                $status = $this->appRepo->saveShareToColender(['co_lender_status'=> 1], $co_lenders_share_id);                
            } else if($request->has('btn_reject_offer')) {
                $addl_data['sharing_comment'] = 'Reject comment goes here';
                $message = trans('backend_messages.reject_offer_success');
                $status = $this->appRepo->saveShareToColender(['co_lender_status'=> 2], $co_lenders_share_id);
            }
            
            if($status) {
                Session::flash('message', $message);
                return redirect()->route('colender_view_offer', ['app_id' => $appId, 'biz_id' => $bizId, 'view_only' => $viewOnly]);
            }
            
        } catch (Exception $ex) {
            return redirect()->back()->withErrors(Helpers::getExceptionMessage($ex));
        }        
    }

}
