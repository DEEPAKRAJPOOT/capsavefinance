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

class WriteOffController extends Controller
{
    //use ApplicationTrait;

    protected $master;
    protected $docRepo;
    protected $appRepo;
    protected $userRepo;

    public function __construct(InvAppRepoInterface $app_repo, InvUserRepoInterface $user_repo, MasterInterface $master)
    {
        $this->appRepo = $app_repo;
        $this->userRepo = $user_repo;
        $this->master = $master;
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
        return view('lms.writeoff.index')->with(['userInfo' => $userInfo]);
    }

    public function generateWriteOff(Request $request)
    {   
        try {
            $user_id = $request->get('user_id');

            $gsts = $this->appRepo->getGSTsByUserId($user_id);
            $app_gsts = $this->appRepo->getAppGSTsByUserId($user_id);
            // start for default button
            $currCompData = UserInvoiceRelation::getUserCurrCompany($user_id);
            $is_show_default = ($currCompData)? 0: 1;
            // end for default button
        }catch (Exception $ex) {
            return redirect()->back()->withErrors(Helpers::getExceptionMessage($ex));
        }
        
    }

    
}
