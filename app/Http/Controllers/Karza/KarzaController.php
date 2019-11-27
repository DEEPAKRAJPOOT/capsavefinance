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
          try{
          $result =  $KarzaApi->checkPanStatusVerification($requestPan);
          $get_dec = json_decode($result,1);
          $status =  $get_dec['status-code'];
          if($status==101) { 
              $status =1; 
              
          } else { 
              $status =0; 
              
          }
          $req =   json_encode(array('name' => $requestPan['name'],'pan' => $requestPan['pan'],'dob' => $requestPan['dob']));
          $createApiLog = BizApiLog::create(['req_file' =>$req, 'res_file' => json_encode($get_dec['result']),'status' => $status]);
          if ($createApiLog) {
               if($status==1)
               {
                   $userData  =  User::getUserByAppId($requestPan['app_id']);
                   $user_id    =  $userData->user_id;
                   $createBizApi= BizApi::create(['user_id' =>$user_id, 
                                            'biz_id' =>   $requestPan['biz_id'],
                                            'biz_owner_id' => $requestPan['ownerid'],
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
         catch (Exception $e) 
        {
             return false;
        }
    }
    
    
    /**
     * Voter ID Card Verification status verification API
     *
     * @return \Illuminate\Http\Response
     */
    public function checkVoterIdVerification(KarzaApi $KarzaApi, Request $request)
    {
          $requestvoterf   = $request->all();
       try{ 
          $result = $KarzaApi->checkVoterIdVerification($requestvoterf);
          $get_dec = json_encode($result);
          $jsonDec= json_decode($get_dec,1);
          $status =  $jsonDec['response']['status-code'];
          if($status==101) { 
              $status =1; 
              
          } else { 
              $status =0; 
              
          }
        
          $req =   json_encode(array('epic_no' => $requestvoterf['epic_no']));
          $createApiLog = BizApiLog::create(['req_file' =>$req, 'res_file' => json_encode($jsonDec['response']['result']),'status' => $status]);
          if ($createApiLog) {
               if($status==1)
               {
                   $userData  =  User::getUserByAppId($requestvoterf['app_id']);
                   $user_id    =  $userData->user_id;
                   $createBizApi= BizApi::create(['user_id' =>$user_id, 
                                            'biz_id' =>   $requestvoterf['biz_id'],
                                            'biz_owner_id' => $requestvoterf['ownerid'],
                                            'type' => 4,
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
         catch (Exception $e) {
                      return false;
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
         try{ 
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
                                            'type' => 5,
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
         catch (Exception $e) {
                      return false;
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
         try{
         $result =  $KarzaApi->checkPassportVerification($requestPassport);
         $get_dec = json_decode($result,1);
          $status =  $get_dec['status-code'];
          if($status==101) { 
              $status =1; 
              
          } else { 
              $status =0; 
              
          }
          $req =   json_encode(array('fileNo' => $requestPassport['fileNo'],'dob' => $requestPassport['dob']));
          $createApiLog = BizApiLog::create(['req_file' =>$req, 'res_file' => json_encode($get_dec['result']),'status' => $status]);
          if ($createApiLog) {
               if($status==1)
               {
                   $userData  =  User::getUserByAppId($requestPassport['app_id']);
                   $user_id    =  $userData->user_id;
                   $createBizApi= BizApi::create(['user_id' =>$user_id, 
                                            'biz_id' =>   $requestPassport['biz_id'],
                                            'biz_owner_id' => $requestPassport['ownerid'],
                                            'type' => 6,
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
         catch (Exception $e) 
        {
             return false;
        }
          
    }
}