<?php

namespace App\Http\Controllers\Backend;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Auth;
use App\Inv\Repositories\Contracts\UserInterface as InvUserRepoInterface;
use App\Contracts\Ui\DataProviderInterface;
use App\Libraries\Idfc_lib;
use Illuminate\Support\Facades\DB;


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
        try 
        {
            $corp_user_id = @$request->get('corp_user_id');
            $user_kyc_id = @$request->get('user_kyc_id');

            $userId = (int) Auth::user()->user_id;
            $role_id = DB::table('role_user')->where(['user_id' => $userId])->pluck('role_id');
            $userRole = DB::table('roles')->whereIn('id',$role_id)->first();

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
            $data = [];

            if($userRole->id == 11) {
                $anchorId = Auth::user()->anchor_id;

                $data['anchorData'] = $this->userRepo->getAnchorDetail($anchorId);
                $data['anchorData']->totalLimit = $data['anchorData']->anchor_limit;
                $data['anchorData']->utilizedLimit = \Helpers::getAnchorUtilizedLimit($data['anchorData']->prgm_id);
                if(isset($data['anchorData']->utilizedLimit)) {
                    $data['anchorData']->remainingLimit = $data['anchorData']->totalLimit - $data['anchorData']->utilizedLimit;
                }

                $data['prgmData'] = $this->userRepo->getPrgmDetail($anchorId, $data['anchorData']->prgm_id);
                
                $data['anchorUserData'] = $this->userRepo->getAnchorUserDataDetail($anchorId);
                $data['anchorUserData']->inactiveUsers = $this->userRepo->getAnchorInactiveUserDataDetail($anchorId);
                
                $data['anchorAppData'] = $this->userRepo->getAnchorAppDataDetail($anchorId);
                $data['anchorInvoiceData'] = $this->userRepo->getAnchorInvoiceDataDetail($anchorId);
            } else {

                $data['lenderAnchorData'] = $this->userRepo->getLenderAnchorDetail();
                $data['anchorUserData'] = $this->userRepo->getAnchorUserDataDetail();
                $data['anchorUserData']->inactiveUsers = $this->userRepo->getAnchorInactiveUserDataDetail();
                
                $data['anchorAppData'] = $this->userRepo->getAnchorAppDataDetail();
                $data['anchorInvoiceData'] = $this->userRepo->getAnchorInvoiceDataDetail();
            }
            // dd($data);
            return view('backend.dashboard',compact('benifinary','data'));
        } catch (Exception $ex) {
            return redirect()->back()->withErrors(Helpers::getExceptionMessage($ex))->withInput();
        }
        
    }

    public function idfc(){
       $getRespWithoutParse = true;
       $result = [];
       $idfcObj= new Idfc_lib();
       $request = $this->getIdfcRequest();
       $result = $idfcObj->api_call(Idfc_lib::MULTI_PAYMENT, $request, $getRespWithoutParse);
       $transId = NULL;
       if ($getRespWithoutParse) {
           $transId = $result[1];
       }else{
            if (!empty($result) && $result['status'] == 'success') {
             $result = $result['result']; 
             $transId = $result['header']['Tran_ID'];
             sleep(5);
           }else{
             $result = [];
           }
       }
       
       dd($result);
       // $transId = '2RFJS5825ZBUWI0JPU'; // 35505
       $transId = '2RFPR57599AE0X2TFJ'; //887.625

       // if ($getRespWithoutParse) {
       //     $transId = $result[0];
       // }else{
       //      if (!empty($result) && $result['status'] == 'success') {
       //       $result = $result['result']; 
       //       $transId = $result['header']['Tran_ID'];
       //     }
       // }
       // sleep(5);

       $enquiryReq = $this->getIdfcEnquiryRequest($transId);
       $enquiryRes = $idfcObj->api_call(Idfc_lib::BATCH_ENQ, $enquiryReq, $getRespWithoutParse);
       // dd($result, $enquiryRes);
    }

    private function getIdfcRequest() {
       $uatParams = array ( 
            'http_header' => array(
                'timestamp' => date('Y-m-d H:i:s'), 
                'txn_id' => _getRand('18'), 
            ), 
            'header' => array (
                'Maker_ID' => 'CAPSAVE.M', 
                'Checker_ID' => 'CAPSAVE.C1', 
                'Approver_ID' => 'CAPSAVE.C2', 
            ), 
            'request' => array ( 
                617 => array (
                    'RefNo' => _getRand('12'), 
                    'Amount' => 1.00,
                    'Debit_Acct_No' => '21480259346', 
                    'Debit_Acct_Name' => 'Debit Account Name', 
                    'Debit_Mobile' => '1234567890', 
                    'Ben_IFSC' => 'HDFC0001897', 
                    'Ben_Acct_No' => '10100318923146', 
                    'Ben_Name' => 'Akash Kumar', 
                    'Ben_BankName' => 'HDFC Bank', 
                    'Ben_Email' => 'akash.kumar@prolitus.com', 
                    'Ben_Mobile' => '8595445454', 
                    'Mode_of_Pay' => 'NEFT', 
                    'Nature_of_Pay' => 'MPYMT', 
                    'Remarks' => 'No remarks it is testing purpose', 
                ), 
            ), 
        );
       $prodParams = array ( 
            'http_header' => array(
                'timestamp' => date('Y-m-d H:i:s'), 
                'txn_id' => _getRand('18'), 
            ), 
            'header' => array (
                'Maker_ID' => 'CAPSAVE.M', 
                'Checker_ID' => 'CAPSAVE.C1', 
                'Approver_ID' => 'CAPSAVE.C2', 
            ), 
            'request' => array ( 
                617 => array ( 
                    "RefNo" => _getRand('12'),
                    "Amount" => 1.51,
                    "Debit_Acct_No" => "10062193074",
                    "Debit_Acct_Name" => "Capsave Finance Pvt Ltd",
                    "Debit_Mobile" => "9930840248",
                    "Ben_IFSC" => "HDFC0001897",
                    "Ben_Acct_No" => "50100318923146",
                    "Ben_Name" => "Akash Kumar",
                    "Ben_BankName" => "HDFC Bank",
                    "Ben_Email" => "akash.kumar@prolitus.com",
                    "Ben_Mobile" => "8744037213",
                    "Mode_of_Pay" => "NEFT",
                    "Nature_of_Pay" => "MPYMT",
                    "Remarks" => "test remarks",
                ), 
            ), 
        );
       return $prodParams;
    }

     private function getIdfcEnquiryRequest($transId = '2RFPR57599AE0X2TFJ') {
       if (empty($transId)) {
          $transId = _getRand('18');
       }
       $params = array (
           'http_header' => array(
               'timestamp' => date('Y-m-d H:i:s'),
               'txn_id' => $transId,
           ),
           'header' => array (
               'Maker_ID' => 'CAPSAVE.M',
               'Checker_ID' => 'CAPSAVE.C1',
               'Approver_ID' => 'CAPSAVE.C2',
           ),
           'request' => array (
               'txn_id' => $transId
           ),
       );
      return $params;
   }
}