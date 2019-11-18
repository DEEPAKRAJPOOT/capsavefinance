<?php

namespace App\Http\Controllers\Backend;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Auth;
use App\Inv\Repositories\Contracts\UserInterface as InvUserRepoInterface;
use App\Contracts\Ui\DataProviderInterface;


class DashboardController extends Controller
{

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    
     
    
    public function __construct( InvUserRepoInterface $user)
    {
        $this->middleware('guest')->except('logout');
        $this->middleware('checkBackendLeadAccess');

        $this->userRepo = $user;
         
    }

    /**
     * Show the application dashboard to the user.
     *
     * @return Response
     */
    public function index(Request $request)
    {
         
       //return view('backend.dashboard');
    
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

            return view('backend.dashboard',compact('benifinary'));
        } catch (Exception $ex) {
            return redirect()->back()->withErrors(Helpers::getExceptionMessage($ex))->withInput();
        }
        
    }
}