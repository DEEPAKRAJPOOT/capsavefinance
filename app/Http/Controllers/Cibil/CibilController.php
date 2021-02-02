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
use Carbon\Carbon;
use App\Libraries\Crif_lib;
use App\Inv\Repositories\Models\BizCrif;
use App\Inv\Repositories\Models\BizCrifLog;

class CibilController extends Controller
{
    protected $appRepo;
    protected $userRepo;

    public function __construct(InvAppRepoInterface $app_repo, InvUserRepoInterface $user_repo, CibilApi $CibilApi){
        $this->userRepo = $user_repo;
        $this->appRepo = $app_repo;
        $this->middleware('checkBackendLeadAccess');
     }


     public function getCommercialCibilRequest(Crif_lib $Crif_lib, Request $request) {
        /*$encoded = _decrypt($request->get('crifData'));
        if (empty($encoded)) {
           return response()->json(['message' =>'Modified Data cannot be accepted.','status' => 0]);
        }
        $biz_biz_owner = json_decode($encoded, true);*/
        $biz_biz_owner = [
            'app_id' => 2,
            'biz_id' => [2],
            'commercial' => [2],
            'individual' => [1,2,3,4],
        ];
        $arrCommercialRequest = array();
        $arrIndividualRequest = array();
        $app_id = $biz_biz_owner['app_id'];
        $biz_id = $biz_biz_owner['biz_id'];
        $bizids = $biz_biz_owner['commercial'];
        $bizOwnerIds = $biz_biz_owner['individual'];
        if (empty($bizOwnerIds)) {
            return response()->json(['message' =>'No Promoter found For the request','status' => 0]);
        }
        foreach ($bizids as $biz_id) {
            $arrBizData = Business::getApplicationById($biz_id);
            $appId = $arrBizData->app->app_id;
            $loanAmount = $arrBizData->app->app_product_supply->loan_amount ?? '50000000';
            $created_at  = Carbon::now()->toDateTimeString();
            $sub1 = "Get Company (".$arrBizData->biz_entity_name.") Civil Score For Return Report Id";
            $sub2 = "Get Company (".$arrBizData->biz_entity_name.") Civil Score For Return XML";
            $sub3 = "Get Company (".$arrBizData->biz_entity_name.") Civil Score For Return HTML";
            if(!empty($arrBizData)){
                    $arrCommercialRequest['biz_name'] = $arrBizData->biz_entity_name;
                    $arrCommercialRequest['pan_gst_hash'] = $arrBizData->pan->pan_gst_hash;
                    $arrCommercialRequest['biz_cin'] = $arrBizData->pan->cin;
                    $arrCommercialRequest['mobile_no'] = $arrBizData->users->mobile_no;
                    $arrCommercialRequest['app_code'] = $arrBizData->app->app_code;
                    $arrCommercialRequest['biz_constitution'] = $arrBizData->biz_constitution;
                    $arrCommercialRequest['city_name'] = $arrBizData->address[0]->city_name ?? '';
                    $arrCommercialRequest['state_name'] = $arrBizData->address[0]->state->name ?? '';
                    $arrCommercialRequest['state_code'] = $arrBizData->address[0]->state->state_code ?? '';
                    $arrCommercialRequest['pincode'] = $arrBizData->address[0]->pin_code ?? '';
                    $arrCommercialRequest['biz_address'] = $arrBizData->address[0]->addr_1 ?? '';
                    $arrCommercialRequest['loan_amount'] = (int)$loanAmount ?? NULL;
            }else{
                   return response()->json(['message' =>'Company Details not found.','status' => 0]);
            }
        }
        $scores = [];
        foreach ($bizOwnerIds as  $key => $biz_owner_id) {
            $scores[$biz_owner_id] = 0;
            $arrOwnerData = BizOwner::getBizOwnerDataByOwnerId($biz_owner_id);
            $created_at  = Carbon::now()->toDateTimeString();
            $sub = "Get Promomter (".$arrOwnerData['first_name'].") Civil Score";
            $addressData = $arrOwnerData->address;
            if(empty($addressData->addr_1)) {
               return response()->json(['message' =>'Promoter Address cannot be empty.','status' => 0]);  
            }
            $addrArr = explode(' ', $addressData->addr_1);
            $countryCode = $addrArr[count($addrArr)-1] ?? '';
            if(strtoupper($countryCode) != 'IN') {
               // return response()->json(['message' =>'Not allowed except Indian Promoter.','status' => 0]);  
            }
            $stateCode = $addrArr[count($addrArr)-2] ?? 'AP';
            $arrIndividualRequest[$key]['name'] = $arrOwnerData->first_name . ' '. $arrOwnerData->last_name;
            $arrIndividualRequest[$key]['date_of_birth'] = $arrOwnerData->date_of_birth;
            $arrIndividualRequest[$key]['pan_number'] = $arrOwnerData->pan;
            $arrIndividualRequest[$key]['mobile_no'] = $arrOwnerData->mobile_no;
            $arrIndividualRequest[$key]['address'] = $addressData->addr_1 ?? '--';
            $arrIndividualRequest[$key]['state_code'] =  $addressData->state->state_code ?? $stateCode;
            $arrIndividualRequest[$key]['state_name'] =  $addressData->state->name ?? '--';
            $arrIndividualRequest[$key]['city_name'] =  $addressData->city_name ?? '';
            $arrIndividualRequest[$key]['pin_code'] =  $addressData->pin_code ?? '';
            $arrIndividualRequest[$key]['date_of_birth'] = date("d/m/Y", strtotime($arrOwnerData->date_of_birth));
        }

        $bizCrifApiData = BizCrif::getLastCrifRequest($biz_id);
        if (!empty($bizCrifApiData) && $bizCrifApiData->status == 'fail' && isset($bizCrifApiData->api_name) && $bizCrifApiData->api_name != Crif_lib::INIT_TXN) {
            $biz_crif_id = $bizCrifApiData->biz_crif_id;
            $api_name = $bizCrifApiData->api_name;
            $random_no = $bizCrifApiData->unique_ref;
            $bizCrifLogs = $bizCrifApiData->getBizCrifLog;
            if (empty($bizCrifLogs)) {
               return response()->json(['message' =>'Unable to Call Api. Please Retry.','status' => 0]);
            }
            foreach ($bizCrifLogs as $bizCrifLog) {
               if ($bizCrifLog->api_name == $api_name) {
                    $crifLogs = $bizCrifLog;
                    break;
               }
            }
            if (empty($crifLogs)) {
               return response()->json(['message' =>'Something Went wrong. Please Contact to Administrator.','status' => 0]);
            }
            $acknowledgementResult['response-type'] = "ACKNOWLEDGEMENT";
            $acknowledgementResult['report-id'] = $crifLogs->report_id;
            $acknowledgementResult['inquiry-unique-ref-no'] = $crifLogs->inquiry_ref;

        }else{
            $random_no = _getRand(18);
            $logReq = [
                'app_id' => $app_id,
                'biz_id' => $biz_id,
                'biz_owner_ids' => implode(',', $bizOwnerIds),
                'unique_ref' => $random_no,
                'status' => 'pending',
                'created_by' => Auth::user()->user_id,
                'created_at' => Carbon::now(),
            ];
            $res = BizCrif::saveBizCrifData($logReq);
            $biz_crif_id = $res->biz_crif_id ?? NULL;
            if (!isset($biz_crif_id)) {
                return response()->json(['message' =>'Unable to log Request. Please Retry.','status' => 0]);
            }
            $requestArrData = array(
                'arrCommercialRequest' => $arrCommercialRequest,
                'arrIndividualRequest' => $arrIndividualRequest,
                'random_no' => $random_no,
                'app_id' => $app_id,
                'biz_id' => $biz_id,
            );
            $response = $Crif_lib->_callApi(Crif_lib::INIT_TXN, $requestArrData, $biz_crif_id, $random_no);
            if ($response['status'] != 'success') {
                $update_log = array(
                    'status' => $response['status'],
                    'api_name' => Crif_lib::INIT_TXN,
                    'resp_msg' => $response['message'],
                    'updated_by' => Auth::user()->user_id,
                    'updated_at' => Carbon::now(),
                );
               $res = BizCrif::updateBizCrif($update_log, ['biz_crif_id' => $biz_crif_id]);
               return response()->json(['message' => $response['message'], 'status' => 0]); 
            }
            $responce = $response['result'];
            $p = xml_parser_create('utf-8');
            xml_parse_into_struct($p, $responce, $resp);
            xml_parser_free($p);
            $acknowledgementResult = [];
            foreach ($resp as $key => $value) {
                if ($value['type'] == 'complete') {
                    $acknowledgementResult[strtolower($value['tag'])] = $value['value'] ?? '';
                }
            }
        }
        $arrRequest = array(
            'commercial' => $arrCommercialRequest,
            'individual' => $arrIndividualRequest,
        );
    
        if(isset($acknowledgementResult['response-type']) && $acknowledgementResult['response-type'] == "ACKNOWLEDGEMENT"){
            //self::pullCrifApiDebugEmail($arrRequest,$responce,$sub1);
            sleep(25);
            $arrRequest['inquiry_unique_ref_no'] = $acknowledgementResult['inquiry-unique-ref-no'];
            $arrRequest['report_id'] = $acknowledgementResult['report-id'];
            $arrRequest['resFormat'] = 'XML';
              
            $respXmlData =  $Crif_lib->_callApi(Crif_lib::PULL_XML, $arrRequest, $biz_crif_id, $random_no);
            if ($respXmlData['status'] != 'success') {
               $update_log = array(
                'status' => $respXmlData['status'],
                'api_name' => Crif_lib::PULL_XML,
                'resp_msg' => $respXmlData['message'],
                'updated_by' => Auth::user()->user_id,
                'updated_at' => Carbon::now(),
               );
               $res = BizCrif::updateBizCrif($update_log, ['biz_crif_id' => $biz_crif_id]);
               return response()->json(['message' => $respXmlData['message'], 'status' => 0]); 
            }
            $responseData = $respXmlData['result'];
            $xmlArray = xmlToArray($responseData);
            $commercial = $xmlArray['COMMERCIAL-ACE-REPORTS']['COMMERCIAL-REPORT'] ?? [];
            $individual = $xmlArray['COMMERCIAL-ACE-REPORTS']['BASE-PLUS-REPORT'] ?? [];
            if(isset($commercial['HEADER']['STATUS']) && $commercial['HEADER']['STATUS'] == 'SUCCESS'){
                //self::pullCrifApiDebugEmail($arrRequest,$responseData,$sub2);
                $arrRequest['resFormat'] = 'HTML';
                sleep(20);
                $respHtmlData =  $Crif_lib->_callApi(Crif_lib::PULL_HTML, $arrRequest, $biz_crif_id, $random_no);
                if ($respHtmlData['status'] != 'success') {
                   $update_log = array(
                    'status' => $respHtmlData['status'],
                    'api_name' => Crif_lib::PULL_HTML,
                    'resp_msg' => $respHtmlData['message'],
                    'updated_by' => Auth::user()->user_id,
                    'updated_at' => Carbon::now(),
                   );
                   $res = BizCrif::updateBizCrif($update_log, ['biz_crif_id' => $biz_crif_id]);
                   return response()->json(['message' => $respHtmlData['message'], 'status' => 0]); 
                }
                $resInHTMLFormate = $respHtmlData['result'];
                $update_log = array(
                    'status' => $respHtmlData['status'],
                    'api_name' => Crif_lib::PULL_HTML,
                    'resp_msg' => $respHtmlData['message'],
                    'updated_by' => Auth::user()->user_id,
                    'updated_at' => Carbon::now(),
                );
                $res = BizCrif::updateBizCrif($update_log, ['biz_crif_id' => $biz_crif_id]);
                $cibilData = base64_encode($resInHTMLFormate);
                $commercialScore = $commercial['SCORES']['SCORE']['SCORE-VALUE'] ?? '';
                Business::where('biz_id', $biz_id)->update(['cibil_score'=> ($commercialScore ?? NULL), 'is_cibil_pulled' => 1]);
                if (count($bizOwnerIds) == 1) {
                    $scores[$biz_owner_id] = $individual['SCORES']['SCORE']['SCORE-VALUE'] ?? 0;
                    BizOwner::where('biz_owner_id', $biz_owner_id)->update(['cibil_score'=> $scores[$biz_owner_id], 'is_cibil_pulled' =>1]);
                }else{
                    foreach ($bizOwnerIds as $key => $biz_owner_id) {
                       $scores[$biz_owner_id] = $individual[$key]['SCORES']['SCORE']['SCORE-VALUE'] ?? 0;
                       BizOwner::where('biz_owner_id', $biz_owner_id)->update(['cibil_score'=> $scores[$biz_owner_id], 'is_cibil_pulled' =>1]);  
                    }
                }
                 return response()->json(['message' =>'Bureau score pulled successfully.','status' => 1, 'value' => $biz_crif_id, 'commercialScore' => $commercialScore, 'individualScore' => $scores[$biz_owner_id]]);
            }else{
                $resp_msg = $xmlArray['INQUIRY-STATUS']['INQUIRY']['DESCRIPTION'] ?? $xmlArray['INQUIRY-STATUS']['INQUIRY']['ERRORS']['ERROR']['DESCRIPTION'] ?? "Unable to get success header in response";
                $update_log = array(
                    'status' => 'fail',
                    'api_name' => Crif_lib::PULL_XML,
                    'resp_msg' => $resp_msg,
                    'updated_by' => Auth::user()->user_id,
                    'updated_at' => Carbon::now(),
                );
                $res = BizCrif::updateBizCrif($update_log, ['biz_crif_id' => $biz_crif_id]);
                return response()->json(['message' => $resp_msg,'status' => 0]); 
            }               
        }else{
            if(!isset($acknowledgementResult['description'])){
                $acknowledgementResult['description'] = 'Something went wrong while initiating the request';
            }
            $update_log = array(
                'status' => 'fail',
                'api_name' => Crif_lib::INIT_TXN,
                'resp_msg' => $acknowledgementResult['description'],
                'updated_by' => Auth::user()->user_id,
                'updated_at' => Carbon::now(),
            );
            $res = BizCrif::updateBizCrif($update_log, ['biz_crif_id' => $biz_crif_id]);
            return response()->json(['message' =>$acknowledgementResult['description'],'status' => 0]);
        }
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
        $created_at  = Carbon::now()->toDateTimeString();
        $sub = "Get Promomter (".$arrOwnerData['first_name'].") Civil Score";
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
            /* mail sent for monitoring **/
            self::pullCrifApiDebugEmail($arrOwnerData,$responce,$sub);
            $result['content'] = base64_encode($result['content']);
            $cibilScore =  $result['scores'] ?? '';
            $createApiLog = BizApiLog::create(['req_file' =>$arrOwnerData, 'res_file' => $result['content'],'status' => 0,'created_by' => Auth::user()->user_id]);
            if ($createApiLog) {
                    $createBizApi= BizApi::create(['user_id' =>$arrOwnerData['user_id'], 
                                                'biz_id' =>   $arrOwnerData['biz_id'],
                                                'biz_owner_id' => $arrOwnerData['biz_owner_id'],
                                                'type' => 2,
                                                'verify_doc_no' => 1,
                                                'status' => 1,
                                               'biz_api_log_id' => $createApiLog['biz_api_log_id'],
                                               'created_by' => Auth::user()->user_id,
                                               'created_at' => $created_at
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




    public function getCommercialCibilRequest1(CibilApi $CibilApi, Request $request)
    {   
        $arrRequest = array();
        $biz_id = $request->get('biz_id');
        //$arrBizData = Business::getCompanyDataByBizId($biz_id);
        $arrBizData = Business::getApplicationById($biz_id);
        $created_at  = Carbon::now()->toDateTimeString();
        $sub1 = "Get Company (".$arrBizData->biz_entity_name.") Civil Score For Return Report Id";
        $sub2 = "Get Company (".$arrBizData->biz_entity_name.") Civil Score For Return XML";
        $sub3 = "Get Company (".$arrBizData->biz_entity_name.") Civil Score For Return HTML";
        if(!empty($arrBizData)){
                $arrRequest['biz_name'] = $arrBizData->biz_entity_name;
                $arrRequest['pan_gst_hash'] = $arrBizData->pan->pan_gst_hash;
                $arrRequest['biz_cin'] = $arrBizData->pan->cin;
                $arrRequest['biz_address'] = $arrBizData->address[0]->addr_1.' '.(isset($arrBizData->address[0]->city_name) ? $arrBizData->address[0]->city_name : '').' '. (isset($arrBizData->address[0]->state->name) ? $arrBizData->address[0]->state->name : '').' '. (isset($arrBizData->address[0]->pin_code) ? $arrBizData->address[0]->pin_code : '');
        }else{
               return response()->json(['message' =>'Something went wrong','status' => 0]);
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
      	//dd($responce);
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
            self::pullCrifApiDebugEmail($arrRequest,$responce,$sub1);
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
                    self::pullCrifApiDebugEmail($arrRequest,$responseData,$sub2);
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
                    self::pullCrifApiDebugEmail($arrRequest,$resInHTMLFormate,$sub3);
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
                                                               'created_at'  => $created_at
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
