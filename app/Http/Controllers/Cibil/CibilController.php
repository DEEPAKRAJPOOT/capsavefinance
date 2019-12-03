<?php
 
namespace App\Http\Controllers\Cibil;

use App\Http\Controllers\Controller;
use App\Libraries\Ui\CibilApi;
use Auth;
use Illuminate\Http\Request;
use App\Inv\Repositories\Contracts\UserInterface as InvUserRepoInterface;
use App\Inv\Repositories\Contracts\ApplicationInterface as InvAppRepoInterface;
use App\Inv\Repositories\Models\BizOwner;
use App\Inv\Repositories\Models\BizApiLog;
use App\Inv\Repositories\Models\BizApi;
use App\Inv\Repositories\Models\Business;
use Session;
use File;
 
class CibilController extends Controller
{
    protected $appRepo;
    protected $userRepo;

    public function __construct(InvAppRepoInterface $app_repo, InvUserRepoInterface $user_repo, CibilApi $CibilApi){
        $this->userRepo = $user_repo;
        $this->appRepo = $app_repo;
     }
    
    /**
     * Show the business information form.
     *
     * @return \Illuminate\Http\Response
     */
    public function getPromoterCibilRequest(CibilApi $CibilApi, Request $request)
    {      
        $biz_owner_id = $request->get('biz_owner_id');
        $arrOwnerData = BizOwner::getBizOwnerDataByOwnerId($biz_owner_id);
        $arrOwnerData->date_of_birth = date("d/m/Y", strtotime($arrOwnerData->date_of_birth));
        $responce =  $CibilApi->getPromoterCibilRequest($arrOwnerData);
        $p = xml_parser_create('utf-8');
        xml_parse_into_struct($p, $responce, $resp);
        xml_parser_free($p);
        $result = [];
        foreach ($resp as $key => $value) {
            if ($value['type'] == 'complete') {
                $result[strtolower($value['tag'])] = $value['value'] ?? '';
            }
        }
        $result['content'] = base64_encode($result['content']);
        $cibilScore =  $result['scores'];
        $createApiLog = BizApiLog::create(['req_file' =>$arrOwnerData, 'res_file' => $result['content'],'status' => 0,'created_by' => Auth::user()->user_id]);
        if ($createApiLog) {
                $createBizApi= BizApi::create(['user_id' =>$arrOwnerData['user_id'], 
                                            'biz_id' =>   $arrOwnerData['biz_id'],
                                            'biz_owner_id' => $arrOwnerData['biz_owner_id'],
                                            'type' => 1,
                                            'verify_doc_no' => 1,
                                            'status' => 1,
                                           'biz_api_log_id' => $createApiLog['biz_api_log_id'],
                                           'created_by' => Auth::user()->user_id,
                                          ]);

                           if($createBizApi){
                                BizOwner::where('biz_owner_id', $biz_owner_id)->update(['cibilScore'=>$cibilScore ? $cibilScore : NULL]);
                                 return response()->json(['message' =>'CIBIL score pulled successfully.','status' => 1, 'value' => $createApiLog['biz_api_log_id'], 'cibilScore' => $cibilScore]);
                           } 
                           else 
                           {
                                 return response()->json(['message' =>'Something went wrong','status' => 0]);
                           }
        }else{
             return response()->json(['message' =>'Something went wrong','status' => 0]);
        }    

    }

   


    function downloadPromoterCibil(Request $request)
    {
        $biz_owner_id = $request->get('biz_owner_id');
        $arrData  = BizApi::getPromoterCibilData($biz_owner_id);
        if(empty($arrData)){
                return response()->json(['message' =>'Error','status' => 0, 'cibilScoreData' => 'Please Pull the CIBIL Score to view the report.']);
        }else{
                $arrCibilScoreData = $arrData['res_file'];
                return response()->json(['message' =>'cibil score pull successfully','status' => 1, 'cibilScoreData' => $arrCibilScoreData]);
       }
    }




    public function getCommercialCibilRequest(CibilApi $CibilApi, Request $request)
    {   
        $biz_id = $request->get('biz_id');
        $arrBizData = Business::getCompanyDataByBizId($biz_id);
        $arrData = BizOwner::where('biz_id','=',$biz_id)->first();
        $arrOwnerData = BizOwner::getBizOwnerDataByOwnerId($arrData['biz_owner_id']);
        $arrOwnerData->date_of_birth = date("d/m/Y", strtotime($arrOwnerData->date_of_birth));
        $responce =  $CibilApi->getCommercialCibilAcknowledgement($arrOwnerData);
        $p = xml_parser_create('utf-8');
        xml_parse_into_struct($p, $responce, $resp);
        xml_parser_free($p);
        $acknowledgementResult = [];
        foreach ($resp as $key => $value) {
            if ($value['type'] == 'complete') {
                $acknowledgementResult[strtolower($value['tag'])] = $value['value'] ?? '';
            }
        }
        //dd($acknowledgementResult);
        if($acknowledgementResult['response-type'] == "ACKNOWLEDGEMENT"){
            sleep(30);
            $arrOwnerData['inquiry_unique_ref_no'] = $acknowledgementResult['inquiry-unique-ref-no'];
            $arrOwnerData['report_id'] = $acknowledgementResult['report-id'];
            $responseData =  $CibilApi->getCommercialCibilData($arrOwnerData);

            $q = xml_parser_create('utf-8');
            xml_parse_into_struct($q, $responseData, $cibilRes);
            xml_parser_free($q);
            $resultData = [];
            foreach ($cibilRes as $key => $value) {
                if ($value['type'] == 'complete') {
                    $resultData[strtolower($value['tag'])] = $value['value'] ?? '';
                }
            }
            if($resultData['status'] == 'SUCCESS' ){
                    
                    if($resultData['score'] > 0){
                        $cibilScore =  $resultData['score'];
                    }else{
                        $cibilScore = '';
                    }
                    $cibilData = json_encode($resultData);
                    $createApiLog = BizApiLog::create(['req_file' =>$arrOwnerData, 'res_file' => $cibilData,'status' => 0,'created_by' => Auth::user()->user_id]);
                            if ($createApiLog) {
                                    $createBizApi= BizApi::create(['user_id' =>$arrOwnerData['user_id'], 
                                                                'biz_id' =>   $arrOwnerData['biz_id'],
                                                                'biz_owner_id' => NULL,
                                                                'type' => 1,
                                                                'verify_doc_no' => 1,
                                                                'status' => 1,
                                                               'biz_api_log_id' => $createApiLog['biz_api_log_id'],
                                                               'created_by' => Auth::user()->user_id,
                                                              ]);

                                               if($createBizApi){
                                                     Business::where('biz_id', $biz_id)->update(['cibilScore'=>$cibilScore ? $cibilScore : NULL]);
                                                     return response()->json(['message' =>'CIBIL score pulled successfully.','status' => 1, 'value' => $createApiLog['biz_api_log_id'], 'cibilScore' => $cibilScore]);
                                               } 
                                               else 
                                               {
                                                     return response()->json(['message' =>'Something went wrong','status' => 0]);
                                               }
                            }else{
                                 return response()->json(['message' =>'Something went wrong','status' => 0]);
                            } 
            }else{
                  return response()->json(['message' =>'Something went wrong','status' => 0]); 
            }               
        }
        else{
            
            return response()->json(['message' =>'Something went wrong','status' => 0]);
        }
    }


    function downloadCommercialCibil(Request $request)
    {
        $biz_id = $request->get('biz_id');
        $arrData  = BizApi::getCommercialCibilData($biz_id);
        if(empty($arrData)){
                return response()->json(['message' =>'Error','status' => 0, 'cibilScoreData' => 'Please Pull the CIBIL Score to view the report.']);
        }else{
                $arrCibilScoreData = $arrData['res_file'];
                return response()->json(['message' =>'cibil score pull successfully','status' => 1, 'cibilScoreData' => $arrCibilScoreData]);
       }
    }


}