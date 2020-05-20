<?php

namespace App\Http\Controllers\Lms;

use Auth;
use Session;
use Helpers;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Inv\Repositories\Contracts\MasterInterface;
use App\Inv\Repositories\Models\Lms\UserInvoiceRelation;
use App\Inv\Repositories\Contracts\UserInterface as InvUserRepoInterface;
use App\Inv\Repositories\Contracts\ApplicationInterface as InvAppRepoInterface;
use App\Inv\Repositories\Contracts\LmsInterface as InvLmsRepoInterface;

class WriteOffController extends Controller
{
    //use ApplicationTrait;

    protected $master;
    protected $docRepo;
    protected $appRepo;
    protected $userRepo;
    protected $lmsRepo;

    public function __construct(InvAppRepoInterface $app_repo, InvUserRepoInterface $user_repo, MasterInterface $master, InvLmsRepoInterface $lms_repo)
    {
        $this->appRepo = $app_repo;
        $this->userRepo = $user_repo;
        $this->master = $master;
        $this->lmsRepo = $lms_repo;
        $this->middleware('checkBackendLeadAccess');
    }

    /**
     * Display a listing of the customer.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $totalLimit = 0;
        $totalCunsumeLimit = 0;
        $consumeLimit = 0;
        $transactions = 0;
        $user_id = $request->get('user_id');
        $userInfo = $this->userRepo->getCustomerDetail($user_id);
        $application = $this->appRepo->getCustomerApplications($user_id);
        $anchors = $this->appRepo->getCustomerPrgmAnchors($user_id);

        foreach ($application as $key => $app) {
            if (isset($app->prgmLimits)) {
                foreach ($app->prgmLimits as $value) {
                    $totalLimit += $value->limit_amt;
                }
            }
            if (isset($app->acceptedOffers)) {
                foreach ($app->acceptedOffers as $value) {
                    $totalCunsumeLimit += $value->prgm_limit_amt;
                }
            }
        }
        $userInfo->total_limit = number_format($totalLimit);
        $userInfo->consume_limit = number_format($totalCunsumeLimit);
        $userInfo->utilize_limit = number_format($totalLimit - $totalCunsumeLimit);
        $woData = $this->lmsRepo->getWriteOff($user_id);
        $roleData = Helpers::getUserRole();
        $userRoleId = $roleData[0]->id;
        return view('lms.writeoff.index')->with(['userInfo' => $userInfo, 'woData' => $woData, 'role_id' => $userRoleId]);
    }

    /**
     * Generate write off
     * 
     * @param Request $request
     * @return type
     */
    public function generateWriteOff(Request $request)
    {   
        try {
            $user_id = $request->get('user_id');
            $woData = [];
            $woData['user_id'] = $user_id;
            $woReqId = $this->lmsRepo->saveWriteOffReq($woData);
            $woLogData = [];
            $woLogData['wo_req_id'] = $woReqId->wo_req_id;
            $woLogData['status_id'] = config('lms')['WRITE_OFF_STATUS']['NEW'];
            $woStatusLogId = $this->lmsRepo->saveWriteOffReqLog($woLogData);
            $updateData = [];
            $updateData['wo_status_log_id'] = $woStatusLogId->wo_status_log_id;
            $this->lmsRepo->updateWriteOffReqById((int) $woReqId->wo_req_id, $updateData);
            return redirect()->route('write_off_customer_list', ['user_id' => $user_id]);
        }catch (Exception $ex) {
            return redirect()->back()->withErrors(Helpers::getExceptionMessage($ex));
        }
        
    }
    
    /**
     * Open write off Pop Up
     * 
     * @param Request $request
     * @return type
     */
    public function getWriteOffPopUP(Request $request)
    {
        try {
            $custId = $request->get('user_id');
            $woReqId = $request->get('wo_req_id');
            $action_type = $request->get('action_type');
            $status_id = $request->get('status_id');
            return view('lms.writeoff.move_to_next_stage')
            ->with(['user_id' => $custId, 'wo_req_id' => $woReqId, 'action_type' => $action_type, 'status_id'=>$status_id]);
        } catch (\Exception $ex) {
            return Helpers::getExceptionMessage($ex);
        }
    }

    /**
     * Save write off comment
     * 
     * @param Request $request
     * @return type
     */
    public function saveWriteOffComment(Request $request)
    {   
        try {
            $woReqId = $request->get('wo_req_id');
            $custId = $request->get('customer_id');
            $actionType = $request->get('action_type');
            $cmntTxt = $request->get('comment_txt');
            $roleData = Helpers::getUserRole();
            $userRoleId = $roleData[0]->id;
            $cur_status_id = $request->get('status_id');
            $status_id = '';
            $messges = '';

            if($actionType == 1){
                switch ($cur_status_id) {
                    case config('lms.WRITE_OFF_STATUS.NEW'):
                        $status_id = config('lms.WRITE_OFF_STATUS.IN_PROCESS');
                        $messges = "Case moved from 'NEW' to 'IN PROCESS'.";
                        break;
                    case config('lms.WRITE_OFF_STATUS.IN_PROCESS'):
                        $status_id = config('lms.WRITE_OFF_STATUS.APPROVED');
                        $messges = "Case moved from 'IN PROCESS' to 'APPROVED'.";
                        break;
                    case config('lms.WRITE_OFF_STATUS.APPROVED'):
                        $status_id = config('lms.WRITE_OFF_STATUS.TRANSACTION_SETTLED');
                        $messges = "Case moved from 'APPROVED' to 'TRANSACTION SETTLED'.";
                        break;
                    case config('lms.WRITE_OFF_STATUS.TRANSACTION_SETTLED'):
                        $status_id = config('lms.WRITE_OFF_STATUS.COMPLETED');
                        $messges = "Case moved from 'TRANSACTION SETTLED' to 'COMPLETED'.";
                        break;
                    case config('lms.WRITE_OFF_STATUS.REVERT_BACK'):
                        $status_id = config('lms.WRITE_OFF_STATUS.IN_PROCESS');
                        $messges = "Case moved from 'REVERT BACK' to 'IN PROCESS'.";
                        break;
                    default:
                        $status_id = config('lms.WRITE_OFF_STATUS.NEW');
                        $messges = "New case created.";
                        break;
                }
            }
            elseif($actionType == 2){
                switch ($cur_status_id){
                    case config('lms.WRITE_OFF_STATUS.IN_PROCESS'):
                        $status_id = config('lms.WRITE_OFF_STATUS.REVERT_BACK');
                        $messges = 'Case moved to back stage.';
                        break;
                }
            }

            $woLogData['wo_req_id'] = $woReqId;
            $woLogData['status_id'] = $status_id;
            $woLogData['comment_txt'] = $cmntTxt;
            $woStatusLogId = $this->lmsRepo->saveWriteOffReqLog($woLogData);
            $updateData = [];
            $updateData['wo_status_log_id'] = $woStatusLogId->wo_status_log_id;
            $this->lmsRepo->updateWriteOffReqById((int) $woReqId, $updateData);
            if($status_id == config('lms.WRITE_OFF_STATUS.APPROVED')){
                $this->lmsRepo->writeOff($custId);
            }
            Session::flash('message', $messges);
            Session::flash('operation_status', 1);
            return redirect()->route('write_off_customer_list', ['user_id' => $custId]);
        }catch (Exception $ex) {
            return redirect()->back()->withErrors(Helpers::getExceptionMessage($ex));
        }
    }
}
