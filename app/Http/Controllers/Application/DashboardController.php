<?php

namespace App\Http\Controllers\Application;

use Auth;
use Helpers;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Inv\Repositories\Contracts\Traits\StorageAccessTraits;
use App\Inv\Repositories\Contracts\LmsInterface as InvLmsRepoInterface;
use App\Inv\Repositories\Contracts\UserInterface as InvUserRepoInterface;
use App\Inv\Repositories\Contracts\ApplicationInterface;


class DashboardController extends Controller
{
    
    /**
     * User repository
     *
     * @var object
     */
    protected $userRepo;
    protected $application;
    protected $lmsRepo;

    use StorageAccessTraits;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct(InvUserRepoInterface $user,
                                ApplicationInterface $application,
                                InvLmsRepoInterface $lms_repo)
    {
        $this->middleware('auth');
        $this->userRepo    = $user;
        $this->application = $application;
        $this->lmsRepo = $lms_repo;
    }

    /**
     * Show the application dashboard to the user.
     *
     * @return Response
     */
    public function index(Request $request)
    {
        try {
           $corp_user_id = @$request->get('corp_user_id');
            $user_kyc_id = @$request->get('user_kyc_id');

            $recentRights = [];
            $benifinary = [];
            $userPersonalData = [];
            $userDocumentType = [];
            $userSocialMedia = [];
       
            if ($corp_user_id > 0 && $user_kyc_id > 0) {

                $benifinary['user_kyc_id'] = (int) $user_kyc_id;
                $benifinary['corp_user_id'] = (int) $corp_user_id;
                $benifinary['is_by_company'] = 1;
                $userKycId = (int) $user_kyc_id;
                $userId = null;
            } else {
                $userId = (int) Auth::user()->user_id;
                $userKycId = (int) Auth::user()->user_kyc_id;
                $benifinary['user_kyc_id'] = (int) Auth::user()->user_kyc_id;
                $benifinary['corp_user_id'] = 0;
                $benifinary['is_by_company'] = 0;
                
            }
            $benifinary['user_type'] = (int) Auth::user()->user_type;

            $outstandingAmt = $this->lmsRepo->getUnsettledTrans($userId)->sum('outstanding');
            if ((int) Auth::user()->user_type == 1) {
                $suppData = $this->userRepo->getSupplierDataById($userId);
                $supplierData = isset($suppData[0]) ? $suppData[0] : [];
//               dd('$supplierData--', $supplierData, '$outstandingAmt---', $outstandingAmt);
            return view('frontend.supplier_dashboard')
                        ->with('supplierData', $supplierData)
                        ->with('outstandingAmt', $outstandingAmt);
            } else {
                return view('frontend.dashboard',compact('benifinary'));
            }
        } catch (Exception $ex) {
            return redirect()->back()->withErrors(Helpers::getExceptionMessage($ex))->withInput();
        }
    }
 }