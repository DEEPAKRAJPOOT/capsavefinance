<?php

namespace App\Http\Controllers\Lms;

use Auth;
use Session;
use Helpers;
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
        return view('lms.writeoff.index')->with(['userInfo' => $userInfo, 'woData' => $woData]);
    }

    public function generateWriteOff(Request $request)
    {   
        try {
            $user_id = $request->get('user_id');
//            $woLogData = [];
//            $this->lmsRepo->saveWriteOffReqLog($woLogData);
            $woData = [];
            $woData['user_id'] = $user_id;
            $woData['created_by'] = Auth::user()->user_id;
            $this->lmsRepo->saveWriteOffReq($woData);
            return redirect()->route('write_off_customer_list', ['user_id' => $user_id]);
            // end for default button
        }catch (Exception $ex) {
            return redirect()->back()->withErrors(Helpers::getExceptionMessage($ex));
        }
        
    }

    
}
