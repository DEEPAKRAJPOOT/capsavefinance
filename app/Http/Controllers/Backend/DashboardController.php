<?php

namespace App\Http\Controllers\Backend;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Auth;
use App\Inv\Repositories\Contracts\UserInterface as InvUserRepoInterface;
use App\Contracts\Ui\DataProviderInterface;
use App\Libraries\Idfc_lib;


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

    public function idfc(){
       $idfcObj= new Idfc_lib();
       $request = $this->getIdfcRequest();
       $result = $idfcObj->api_call(Idfc_lib::BATCH_ENQ, $request);
       dd($result);
    }

    private function getIdfcRequest() {
       $params = array ( 
            'http_header' => array(
                'timestamp' => date('Y-m-d H:i:s'), 
                'txn_id' => '2RFEQ02300WTFXBL50', 
            ), 
            'header' => array (
                'Maker_ID' => 'CAPSAVE.M', 
                'Checker_ID' => 'CAPSAVE.C1', 
                'Approver_ID' => 'CAPSAVE.C2', 
            ), 
            'request' => array ( 
                'txn_id' => '2RFEQ02300WTFXBL50', 
            ), 
        );
       return $params;
    }
}