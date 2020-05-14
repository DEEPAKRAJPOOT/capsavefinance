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
use Mail; 
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
        $arrOwnerAddr = BizOwner::with('address')->where('biz_owner_id', $biz_owner_id)->first();
     
        if($arrOwnerAddr->address!=null) {
            $ex =  explode(' ',$arrOwnerAddr->address->addr_1);
            $count  = count( $ex); 
            $arrOwnerData->address  = $arrOwnerAddr->address->addr_1;
            $arrOwnerData->state =  $ex[$count-2];
            $arrOwnerData->city =  $ex[$count-4];
            $arrOwnerData->pin =  $ex[$count-3];
        }
     else {
            $arrOwnerData->address  = "";
            $arrOwnerData->city =  "";
            $arrOwnerData->pin =   "";
            $arrOwnerData->state =  "";
       }
       
        $arrOwnerData->date_of_birth = date("d/m/Y", strtotime($arrOwnerData->date_of_birth));
        $responce =  $CibilApi->getPromoterCibilRequest($arrOwnerData);
        //dd($responce);
        $p = xml_parser_create('utf-8');
        xml_parse_into_struct($p, $responce, $resp);
        xml_parser_free($p);
        $result = [];
        foreach ($resp as $key => $value) {
            if ($value['type'] == 'complete') {
                $result[strtolower($value['tag'])] = $value['value'] ?? '';
            }
        }
       
        if(isset($result['content'])){
            $result['content'] = base64_encode($result['content']);
            $cibilScore =  $result['scores'] ?? '';
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
                                    BizOwner::where('biz_owner_id', $biz_owner_id)->update(['cibil_score'=>$cibilScore ? $cibilScore : NULL, 'is_cibil_pulled' =>1]);
                                     return response()->json(['message' =>'Bureau score pulled successfully.','status' => 1, 'value' => $createApiLog['biz_api_log_id'], 'cibilScore' => $cibilScore]);
                               } 
                               else 
                               {
                                     return response()->json(['message' =>'Something went wrong','status' => 0]);
                               }
            }else{
                 return response()->json(['message' =>'Something went wrong','status' => 0]);
            } 
        }else{
             return response()->json(['message' =>$result['description'],'status' => 0]);
        }    

    }

   


    function downloadPromoterCibil(Request $request)
    {
        $biz_owner_id = $request->get('biz_owner_id');
        $arrData  = BizApi::getPromoterCibilData($biz_owner_id);
        if(empty($arrData)){
                return response()->json(['message' =>'Error','status' => 0, 'cibilScoreData' => 'Please Pull the Bureau Score to view the report.']);
        }else{
                $arrCibilScoreData = $arrData['res_file'];
                return response()->json(['message' =>'Bureau score pull successfully','status' => 1, 'cibilScoreData' => $arrCibilScoreData]);
       }
    }




    public function getCommercialCibilRequest(CibilApi $CibilApi, Request $request)
    {   
        $arrRequest = array();
        $biz_id = $request->get('biz_id');
        //$arrBizData = Business::getCompanyDataByBizId($biz_id);
        $arrBizData = Business::getApplicationById($biz_id);


        if(!empty($arrBizData)){
                $arrRequest['biz_name'] = $arrBizData->biz_entity_name;
                $arrRequest['pan_gst_hash'] = $arrBizData->pan->pan_gst_hash;
                $arrRequest['biz_cin'] = $arrBizData->pan->cin;
                $arrRequest['biz_address'] = $arrBizData->address[0]->addr_1.' '.(isset($arrBizData->address[0]->city_name) ? $arrBizData->address[0]->city_name : '').' '. (isset($arrBizData->address[0]->state->name) ? $arrBizData->address[0]->state->name : '').' '. (isset($arrBizData->address[0]->pin_code) ? $arrBizData->address[0]->pin_code : '');
        }else{
               return response()->json(['message' =>'Something went wrong1','status' => 0]);
        }


       
        // $arrData = BizOwner::where('biz_id','=',$biz_id)->first();
        // $arrOwnerData = BizOwner::getBizOwnerDataByOwnerId($arrData['biz_owner_id']);


        // $arrOwnerData->pan_gst_hash = $arrBizData['0']['pan_gst_hash'];
        // $arrOwnerData->date_of_birth = date("d/m/Y", strtotime($arrOwnerData->date_of_birth));
        // $arrOwnerData->biz_cin = $arrBizData['0']['cin'];
        // $arrOwnerAddr = BizOwner::with('address')->where('biz_owner_id', $arrData['biz_owner_id'])->first();
        // if($arrOwnerAddr->address!=null) {
        //     $arrOwnerData->owner_addr = $arrOwnerAddr->address->addr_1;
        // }else {
        //     $arrOwnerData->owner_addr = '';
        // }

        $responce =  $CibilApi->getCommercialCibilAcknowledgement($arrRequest);
      // dd($responce);
        $p = xml_parser_create('utf-8');
        xml_parse_into_struct($p, $responce, $resp);
        xml_parser_free($p);
        $acknowledgementResult = [];
        foreach ($resp as $key => $value) {
            if ($value['type'] == 'complete') {
                $acknowledgementResult[strtolower($value['tag'])] = $value['value'] ?? '';
            }
        }
        
        if(isset($acknowledgementResult['response-type']) && $acknowledgementResult['response-type'] == "ACKNOWLEDGEMENT"){
            self::pullCrifApiDebugEmail($arrRequest,$responce,$acknowledgementResult['report-id']);
            sleep(25);
            $arrRequest['inquiry_unique_ref_no'] = $acknowledgementResult['inquiry-unique-ref-no'];
            $arrRequest['report_id'] = $acknowledgementResult['report-id'];
            $arrRequest['resFormat'] = 'XML';
              
            $responseData =  $CibilApi->getCommercialCibilData($arrRequest);
            $q = xml_parser_create('utf-8');
            xml_parse_into_struct($q, $responseData, $cibilRes);
            xml_parser_free($q);
            $resultData = [];
            foreach ($cibilRes as $key => $value) {
                if ($value['type'] == 'complete') {
                    $resultData[strtolower($value['tag'])] = $value['value'] ?? '';
                }
            }
            if(isset($resultData['status'])){
                    self::pullCrifApiDebugEmail($arrRequest,$responseData,$acknowledgementResult['report-id']);
                    $arrRequest['resFormat'] = 'HTML';
                    sleep(10);
                    $resInHTMLFormate =  $CibilApi->getCommercialCibilData($arrRequest);
                   // dd($resInHTMLFormate);
                    $cibilData = base64_encode($resInHTMLFormate);
                    //dd($cibilData);
                    if($resultData['score-value'] > 0){
                        $cibilScore =  $resultData['score-value'];
                    }else{
                        $cibilScore = '';
                    }
                    //$cibilData = json_encode($resultData);
                    self::pullCrifApiDebugEmail($arrRequest,$resInHTMLFormate,$acknowledgementResult['report-id']);
                    $createApiLog = BizApiLog::create(['req_file' =>json_encode($arrRequest), 'res_file' => $cibilData,'status' => 0,'created_by' => Auth::user()->user_id]);
                            if ($createApiLog) {
                                    $createBizApi= BizApi::create(['user_id' =>$arrBizData->user_id, 
                                                                'biz_id' =>   $biz_id,
                                                                'biz_owner_id' => NULL,
                                                                'type' => 1,
                                                                'verify_doc_no' => 1,
                                                                'status' => 1,
                                                               'biz_api_log_id' => $createApiLog['biz_api_log_id'],
                                                               'created_by' => Auth::user()->user_id,
                                                              ]);

                                               if($createBizApi){
                                                     Business::where('biz_id', $biz_id)->update(['cibil_score'=>$cibilScore ? $cibilScore : NULL, 'is_cibil_pulled' =>1]);
                                                     return response()->json(['message' =>'Bureau score pulled successfully.','status' => 1, 'value' => $createApiLog['biz_api_log_id'], 'cibilScore' => $cibilScore]);
                                               } 
                                               else 
                                               {
                                                     return response()->json(['message' =>'Something went wrong','status' => 0]);
                                               }
                            }else{
                                 return response()->json(['message' =>'Something went wrong','status' => 0]);
                            } 
            }else{
                  return response()->json(['message' =>$resultData['description'],'status' => 0]); 
            }               
        }
        else{
            if(!isset($acknowledgementResult['description'])){
                $acknowledgementResult['description'] = 'Something went wrong';
            }
            return response()->json(['message' =>$acknowledgementResult['description'],'status' => 0]);
        }
    }





    function downloadCommercialCibil(Request $request)
    {
        $biz_id = $request->get('biz_id');
        $arrData  = BizApi::getCommercialCibilData($biz_id);
        if(empty($arrData)){
                return response()->json(['message' =>'Error','status' => 0, 'cibilScoreData' => 'Please Pull the Bureau Score to view the report.']);
        }else{
                $arrCibilScoreData = $arrData['res_file'];
                return response()->json(['message' =>'Bureau score pull successfully','status' => 1, 'cibilScoreData' => $arrCibilScoreData]);
       }
    }

    public static function pullCrifApiDebugEmail($req,$res,$sub)
    {
        $data['request']  = $req;
        $data['response'] =  $res;       
        $subject = $sub;
        Mail::raw(
            print_r($data, true),
            function ($message) use ($subject) {
                $message->to(config('errorgroup.error_crif_notification_group'))
                    ->from(
                        config('errorgroup.error_notification_email'),
                        config('errorgroup.error_notification_from')
                    )
                    ->subject($subject);
            }
        );
    }
}