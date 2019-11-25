<?php
 
namespace App\Http\Controllers\Karza;

use App\Http\Controllers\Controller;
use App\Libraries\Ui\KarzaApi;
use App\Inv\Repositories\Models\BizApiLog;
use App\Inv\Repositories\Models\BizApi;
use App\Inv\Repositories\Models\User;
use Auth;
use Illuminate\Http\Request;
use App\Inv\Repositories\Contracts\UserInterface as InvUserRepoInterface;
use App\Inv\Repositories\Contracts\ApplicationInterface as InvAppRepoInterface;
use Session;
 
class KarzaController extends Controller
{
    protected $appRepo;
    protected $userRepo;

    public function __construct(InvAppRepoInterface $app_repo, InvUserRepoInterface $user_repo){
        $this->userRepo = $user_repo;
        $this->appRepo = $app_repo;
    }
    
    /**
     * Show the business information form.
     *
     * @return \Illuminate\Http\Response
     */
    public function checkPanVerification(KarzaApi $KarzaApi, Request $request)
    {
          $requestPan   = $request->all();
          return $KarzaApi->panVerificationRequest($requestPan['pan']);
    }

    /**
     * Pan status verification API
     *
     * @return \Illuminate\Http\Response
     */
    public function checkPanStatusVerification(KarzaApi $KarzaApi, Request $request)
    {
          $requestPan   = $request->all();
          return $KarzaApi->checkPanStatusVerification($requestPan);
    }
    
    
    /**
     * Voter ID Card Verification status verification API
     *
     * @return \Illuminate\Http\Response
     */
    public function checkVoterIdVerification(KarzaApi $KarzaApi, Request $request)
    {
          $requestPan   = $request->all();
          $result = $KarzaApi->checkVoterIdVerification($requestPan['epic_no']);
          $createApiLog = BizApiLog::create(['req_file' =>$requestPan['epic_no'], 'res_file' => json_encode($result['response']->result),'status' => 0]);
          if ($createApiLog) {
                return response()->json(['message' =>trans('success_messages.basic_saved_successfully'),'status' => 1, 'value' => $createApiLog['biz_api_log_id']]);
            } else {
               return response()->json(['message' =>trans('success_messages.oops_something_went_wrong'),'status' => 0]);
            }

         
    }
    
       /**
     * Voter ID Card Verification status verification API
     *
     * @return \Illuminate\Http\Response
     */
    public function checkDlVerification(KarzaApi $KarzaApi, Request $request)
    {
          $requestDl   = $request->all();
          $result =   $KarzaApi->checkDlVerification($requestDl);
          $get_dec = json_decode($result,1);
          $status =  $get_dec['status-code'];
          if($status==101) { 
              $status =1; 
              
          } else { 
              $status =0; 
              
          }
          
          $req =   json_encode(array('dl' => $requestDl['dl_no'],'dob' => $requestDl['dob']));
          $createApiLog = BizApiLog::create(['req_file' =>$req, 'res_file' => json_encode($get_dec['result']),'status' => $status]);
          if ($createApiLog) {
               if($status==1)
               {
                   $userData  =  User::getUserByAppId($requestDl['app_id']);
                   $user_id    =  $userData->user_id;
                   $createBizApi= BizApi::create(['user_id' =>$user_id, 
                                            'biz_id' =>   $requestDl['biz_id'],
                                            'biz_owner_id' => $requestDl['ownerid'],
                                            'type' => 3,
                                            'verify_doc_no' => 1,
                                            'status' => 1,
                                           'biz_api_log_id' => $createApiLog['biz_api_log_id'],
                                           'created_by' => Auth::user()->user_id,
                                          ]);
                            if($createBizApi){

                                 return response()->json(['message' =>trans('success_messages.basic_saved_successfully'),'status' => 1, 'value' => $createApiLog['biz_api_log_id']]);
                           } 
                           else 
                          {
                                 return response()->json(['message' =>trans('success_messages.oops_something_went_wrong'),'status' => 0]);
                           }
                  }
               return response()->json(['message' =>trans('success_messages.basic_saved_successfully'),'status' => 0, 'value' => $createApiLog['biz_api_log_id']]);
          
               } 
            else {
               return response()->json(['message' =>trans('success_messages.oops_something_went_wrong'),'status' => 0]);
            }
    }
    
    /**
     * Passport ID Card Verification status verification API
     *
     * @return \Illuminate\Http\Response
     */
    public function checkPassportVerification(KarzaApi $KarzaApi, Request $request)
    {
          $requestPassport   = $request->all();
          return $KarzaApi->checkPassportVerification($requestPassport);
    }
}