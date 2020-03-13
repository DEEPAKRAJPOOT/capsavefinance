<?php

namespace App\Http\Controllers\Lms;

use App\Http\Controllers\Controller;
use Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use App\Inv\Repositories\Contracts\UserInterface as InvUserRepoInterface;
use App\Inv\Repositories\Contracts\ApplicationInterface as InvAppRepoInterface;
use App\Inv\Repositories\Contracts\DocumentInterface as InvDocumentRepoInterface;
use App\Inv\Repositories\Contracts\LmsInterface as InvLmsRepoInterface;
use Session;
use Helpers;
use App\Inv\Repositories\Contracts\Traits\ApplicationTrait;

class CustomerController extends Controller {

    use ApplicationTrait;

    protected $appRepo;
    protected $userRepo;
    protected $docRepo;
    protected $lmsRepo;

    /**
     * The pdf instance.
     *
     * @var App\Libraries\Pdf
     */
    protected $pdf;

    public function __construct(InvAppRepoInterface $app_repo, InvUserRepoInterface $user_repo, InvDocumentRepoInterface $doc_repo, InvLmsRepoInterface $lms_repo) {
        $this->appRepo = $app_repo;
        $this->userRepo = $user_repo;
        $this->docRepo = $doc_repo;
        $this->lmsRepo = $lms_repo;
        $this->middleware('checkBackendLeadAccess');
    }

    /**
     * Display a listing of the customer.
     *
     * @return \Illuminate\Http\Response
     */
    public function list()
    {
    return view('lms.customer.list');
}

/**
 * Display a listing of the customer.
 *
 * @return \Illuminate\Http\Response
 */
public function listAppliction(Request $request) {
    try {
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

        return view('lms.customer.list_applications')
                        ->with([
                            'userInfo' => $userInfo,
                            'application' => $application,
                            'anchors' => $anchors
        ]);
    } catch (Exception $ex) {
        dd($ex);
    }
}

/**
 * Display a listing of the invoices.
 *
 * @return \Illuminate\Http\Response
 */
public function listInvoice() {
    return view('lms.customer.list_invoices');
}

}
