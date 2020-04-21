<?php

namespace App\Http\Controllers\Lms;

use App\Http\Controllers\Controller;
use Auth;
use Illuminate\Http\Request;
use App\Inv\Repositories\Contracts\MasterInterface;
use App\Inv\Repositories\Contracts\UserInterface as InvUserRepoInterface;
use App\Inv\Repositories\Contracts\ApplicationInterface as InvAppRepoInterface;
use App\Inv\Repositories\Contracts\UserInvoiceInterface as InvUserInvRepoInterface;
use App\Inv\Repositories\Models\Master\State;
use DB;
use Session;
use Helpers;
// use App\Inv\Repositories\Contracts\Traits\ApplicationTrait;

class userInvoiceController extends Controller
{
    //use ApplicationTrait;

    protected $appRepo;
    protected $userRepo;
    protected $docRepo;
    protected $master;
    protected $UserInvRepo;

    /**
     * The pdf instance.
     *
     * @var App\Libraries\Pdf
     */
    protected $pdf;

    public function __construct(InvAppRepoInterface $app_repo, InvUserRepoInterface $user_repo, MasterInterface $master, InvUserInvRepoInterface $UserInvRepo) {
        $this->appRepo = $app_repo;
        $this->userRepo = $user_repo;
        $this->master = $master;
        $this->UserInvRepo = $UserInvRepo;
        $this->middleware('checkBackendLeadAccess');
    }

    /**
     * Display invoice as per User.
     *
     */
    public function getUserInvoice(Request $request) {
        try {
            $user_id = $request->get('user_id');
            $userInfo = $this->userRepo->getCustomerDetail($user_id);

            return view('lms.invoice.user_invoice_list')->with(['userInfo' => $userInfo]);
        } catch (Exception $ex) {
             return redirect()->back()->withErrors(Helpers::getExceptionMessage($ex));
        }
    }

    /**
     * Create invoice as per User.
     *
     */
    public function createUserInvoice(Request $request) {
        try {
            $user_id = $request->get('user_id');
            $userInfo = $this->userRepo->getCustomerDetail($user_id);
            $appInfo = $this->UserInvRepo->getAppsByUserId($user_id);
            $appID = $appInfo[0]->app_id;
            $gstInfo = $this->UserInvRepo->getGSTs($appID);

            $state_list = ['' => 'Please Select'] + $this->UserInvRepo->getStateListCode()->toArray();
            // $state_list = $this->UserInvRepo->getStateListCode();

            // dd($gstInfo);

            return view('lms.invoice.create_user_invoice')
            ->with(['userInfo' => $userInfo, 'state_list' => $state_list, 'appInfo' => $appInfo, 'gstInfo' => $gstInfo]);
        } catch (Exception $ex) {
             return redirect()->back()->withErrors(Helpers::getExceptionMessage($ex));
        }
    }

}
