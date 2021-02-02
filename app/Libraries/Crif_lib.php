<?php

namespace App\Libraries;
use Carbon\Carbon;
use Auth;
use App\Inv\Repositories\Models\BizCrifLog;
class Crif_lib {

    const INIT_TXN    = '1001';    #Intiate the transaction
    const PULL_XML    = '1002';    #Pull the XML File
    const PULL_HTML   = '1003';    #Pull the HTML File

    public function __construct() {
        $this->arr = [
            '2' => '11',
            '3' => '12',
            '5' => '30',
            '1' => '40',
            '4' => '40'
        ];
        $this->url = 'https://test.crifhighmark.com/Inquiry/Inquiry/CPUAction.action';//config('library.CRIF_URL');
        $this->req_mbr = 'NBF0002966';//config('library.CRIF_REQ_MBR');
        $this->mbr_id = 'BOROCTSAN005';//config('library.CRIF_MBR_ID');
        $this->sub_mbr_id = 'CAPSAVE FINANCE PRIVATE LIMITED';//config('library.CRIF_MBR_ID');
        $this->usrId = 'crif1_cpu_uat@capsavefinance.com';//config('library.CRIF_USRID');
        $this->PassCode = '55DE689372D33C9876D1E09CFFF8BBBFF74B9445';//config('library.CRIF_PASSCODE');
        $this->res_frmt = 'XML';
    }

    public function _callApi($method, $data, $biz_crif_id, $random_no) {
        $resp = ['status' => 'fail','message' => 'Some Error Occured While fetching the Response.'];
        switch ($method) {
            case SELF::INIT_TXN:
                $xml = $this->getCommercialCibilAcknowledgement($data);
                break;
           case SELF::PULL_XML:
                $xml = $this->getCommercialCibilData($data);
                break;
           case SELF::PULL_HTML:
                $xml = $this->getCommercialCibilData($data);
                break;
           default:
               $xml = "";
               break;
        }
        if (empty($xml)) {
           $resp['code']    = "EmptyXML";
           $resp['message']    = "Request XML cannot be empty.";
           return $resp;
        }
        $logCurlRequest = [
            'biz_crif_id' => $biz_crif_id,
            'api_name' => $method,
            'report_id' => $data['report_id'] ?? NULL,
            'inquiry_ref' => $data['inquiry_unique_ref_no'] ?? NULL,
            'req_file' => $xml,
            'url' => $this->url,
            'status' => 'pending',
            'created_by' => Auth::user()->user_id,
            'created_at' => Carbon::now(),
        ];
        $BizCrifLog = BizCrifLog::saveBizCrifLogData($logCurlRequest);
        $biz_crif_log_id = $BizCrifLog->biz_crif_log_id ?? NULL;
        if (!isset($biz_crif_log_id)) {
            return response()->json(['message' =>'','status' => 0]);
            $resp['code']    = "UnableLog";
            $resp['message']  = "Unable to log Request. Please Retry.";
           return $resp;
        }
        $response =  $this->_curlCall($xml);
        $updateLog = [
            'status' => 'fail',
            'updated_by' => Auth::user()->user_id,
            'updated_at' => Carbon::now(),
        ];
        if (!empty($response['error_no'])) {
            $resp['code']    = "CurlError : " . $response['error_no'];
            $resp['message'] = $response['error'] ?? "Unable to response from Curl. Please retry.";
            $updateLog['res_file'] =  base64_encode($resp['message']);
            $bizCrifUpdated = BizCrifLog::updateBizCrifLog($updateLog, ['biz_crif_log_id' => $biz_crif_log_id]);
            return $resp;
        }
        if (empty($response['error_no']) && $response['curl_info']['http_code'] != 200) {
            $resp['code']    = "HTTPCode : " . $response['curl_info']['http_code'];
            $resp['message'] = $response['error'] ?? "Unable to get response. Please retry.";
            $updateLog['res_file'] =  base64_encode($resp['message']);
            $bizCrifUpdated = BizCrifLog::updateBizCrifLog($updateLog, ['biz_crif_log_id' => $biz_crif_log_id]);
            return $resp;
        }
        $resp['status'] = 'success';
        $resp['message'] = 'success';
        $updateLog['status'] = 'success';
        $updateLog['res_file'] =  is_array($response['result']) || is_object($response['result']) ? base64_encode($response['result']) : base64_encode($response['result']);
        $bizCrifUpdated = BizCrifLog::updateBizCrifLog($updateLog, ['biz_crif_log_id' => $biz_crif_log_id]);
        $resp['result'] = $response['result'];
        return $resp;
    }


    private function getCommercialCibilAcknowledgement($ArrData) {
      extract($ArrData);
      $this->random_no = $random_no;
      $dummyXML = TRUE;
      if ($dummyXML) {
          $request_xml = $this->_getDummyXML();
      }else{
        $request_xml = '<REQUEST-REQUEST-FILE>' . $this->_getHeaderSegment() . '<INQUIRY>'. 
        $this->_getCommercialSegment($arrCommercialRequest). '<INDIVIDUAL-ENTITIES-SEGMENT>'. 
        $this->_getIndividualSegment($arrIndividualRequest). '</INDIVIDUAL-ENTITIES-SEGMENT> </INQUIRY></REQUEST-REQUEST-FILE>';
      }
      return $request_xml;
    }


    private function getCommercialCibilData($arrRequest) {
        $request_xml = '<REQUEST-REQUEST-FILE>' . $this->prepareXmlToGetData($arrRequest) . '</REQUEST-REQUEST-FILE>';
        return $request_xml;
    }

    private function _curlCall($postXml){
      try {
        $curl = curl_init();
        curl_setopt_array($curl, array(
          CURLOPT_URL => $this->url,
          CURLOPT_RETURNTRANSFER => true,
          CURLOPT_ENCODING => "",
          CURLOPT_MAXREDIRS => 10,
          CURLOPT_TIMEOUT => 0,
          CURLOPT_FOLLOWLOCATION => true,
          CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
          CURLOPT_SSL_VERIFYHOST => false,
          CURLOPT_SSL_VERIFYPEER => false,
          CURLOPT_CUSTOMREQUEST => "POST",
          CURLOPT_POSTFIELDS => $postXml,
          CURLOPT_HTTPHEADER => array(
              "inquiryxml: $postXml",
              "Content-Type: application/xml"
          ),
        ));
        $output = curl_exec($curl);
        $resp['error'] = curl_error($curl);
        $resp['error_no'] = curl_errno($curl);
        $resp['result'] = $output;
        $resp['curl_info'] = curl_getinfo($curl);
        curl_close($curl);
        return $resp;
      } catch (Exception $ex) {
        //echo " Exception : " . $ex->getMessage() . " | " . $ex->getLine();
      }
    }

    private function _getHeaderSegment() {
        $headerSegment = '<HEADER-SEGMENT>
            <PRODUCT-TYP>COMM_ACE_PLUS_REPORT</PRODUCT-TYP>
            <PRODUCT-VER>4.0</PRODUCT-VER>
            <REQ-MBR>'. $this->req_mbr .'</REQ-MBR>
            <SUB-MBR-ID>'.$this->sub_mbr_id.'</SUB-MBR-ID>
            <INQ-DT-TM>'.date('d-m-Y').'</INQ-DT-TM>
            <REQ-VOL-TYP>INDV</REQ-VOL-TYP>
            <REQ-ACTN-TYP>SUBMIT</REQ-ACTN-TYP>
            <TEST-FLG>Y</TEST-FLG>
            <USER-ID>'. $this->usrId .'</USER-ID>
            <PWD>'. $this->PassCode .'</PWD>
            <AUTH-FLG>N</AUTH-FLG>
            <AUTH-TITLE>USER</AUTH-TITLE>
            <RES-FRMT>'.$this->res_frmt.'</RES-FRMT>
            <MEMBER-PRE-OVERRIDE>N</MEMBER-PRE-OVERRIDE>
            <RES-FRMT-EMBD>N</RES-FRMT-EMBD>
            <LOS-NAME>TEST</LOS-NAME>
            <LOS-VENDER>TEST</LOS-VENDER>
            <LOS-VERSION>0.0</LOS-VERSION>
            <COMMERCIAL>
                <CIR>true</CIR>
                <SCORE>true</SCORE>
            </COMMERCIAL>
            <CONSUMER>
                <CIR>true</CIR>
                <SCORE>true</SCORE>
            </CONSUMER></HEADER-SEGMENT>';
        return $headerSegment;
    }

    private function _getCommercialSegment($arrRequest) { 
      $commercialSegment = '<COMM-APPLICANT-SEGMENT>
            <BORROWER-NAME>'.$arrRequest['biz_name'].'</BORROWER-NAME>
            <BORROWER-SHORT-NAME>'.$arrRequest['biz_name'].'</BORROWER-SHORT-NAME>
            <LEGAL-CONSTITUTION>'. ($this->arr[$arrRequest['biz_constitution']] ?? '20') .'</LEGAL-CONSTITUTION>
            <IDS>
                <ID>
                    <TYPE>ID07</TYPE>
                    <VALUE>'.$arrRequest['pan_gst_hash'].'</VALUE>
                </ID>
                <ID>
                    <TYPE>ID08</TYPE>
                    <VALUE>'.$arrRequest['biz_cin'].'</VALUE>
                </ID>
            </IDS>
            <CLASS-OF-ACTIVITY-1>FINANCIAL INTERMEDIATION</CLASS-OF-ACTIVITY-1>
            <PHONES>
                <PHONE>
                    <TELE-NO>'. $arrRequest['mobile_no'] .'</TELE-NO>
                    <TELE-NO-TYPE>P01</TELE-NO-TYPE>
                </PHONE>
            </PHONES>
        </COMM-APPLICANT-SEGMENT>
        <COMM-ADDRESS-SEGMENT>
            <ADDRESS>
                <TYPE>D01</TYPE>
                <ADDRESS-LINE>'.$arrRequest['biz_address'].'</ADDRESS-LINE>
                <LOCALITY></LOCALITY>
                <CITY>'.$arrRequest['city_name'].'</CITY>
                <STATE>'.$arrRequest['state_code'].'</STATE>
                <PIN>'.$arrRequest['pincode'].'</PIN>
            </ADDRESS>
        </COMM-ADDRESS-SEGMENT>
        <APPLICATION-SEGMENT>
            <INQUIRY-UNIQUE-REF-NO>'. $this->random_no .'</INQUIRY-UNIQUE-REF-NO>
            <CREDT-INQ-PURPS-TYP>ACCT-ORI</CREDT-INQ-PURPS-TYP>
            <CREDT-INQ-PURPS-TYP-DESC>Loan_Purpose_Desc</CREDT-INQ-PURPS-TYP-DESC>
            <CREDIT-INQUIRY-STAGE>PRE_DISB</CREDIT-INQUIRY-STAGE>
            <CREDT-RPT-ID>CRDRQINQR</CREDT-RPT-ID>
            <CREDT-REQ-TYP>INDV</CREDT-REQ-TYP>
            <CREDT-RPT-TRN-DT-TM>'. date('d-m-Y H:i:s') .'</CREDT-RPT-TRN-DT-TM>
            <MBR-ID>'. $this->mbr_id .'</MBR-ID>
            <KENDRA-ID>PUNE</KENDRA-ID>
            <BRANCH-ID>PUNE</BRANCH-ID>
            <LOS-APP-ID>'.$arrRequest['app_code'].'</LOS-APP-ID>
            <LOAN-TYPE>0801</LOAN-TYPE>
            <LOAN-AMOUNT>'. $arrRequest['loan_amount'].'</LOAN-AMOUNT>
        </APPLICATION-SEGMENT>';
        return $commercialSegment;
    }

    private function _getIndividualSegment($arrIndividualRequest){
      $individualSegment = '';
      foreach ($arrIndividualRequest as $arrOwnerData) {
         $individualSegment .= '
            <INDIVIDUAL-ENTITY>
                <INDIVIDUAL-ENTITY-TYPE>20</INDIVIDUAL-ENTITY-TYPE>
                <INDV-APPLICANT-SEGMENT>
                    <APPLICANT-NAME>
                        <NAME-1>'.$arrOwnerData['name'].'</NAME-1>
                    </APPLICANT-NAME>
                    <MARITAL-STATUS></MARITAL-STATUS>
                    <GENDER></GENDER>
                    <DOB>
                        <DOB-DATE>'.$arrOwnerData['date_of_birth'].'</DOB-DATE>
                        <AGE></AGE>
                    </DOB>
                    <IDS>
                        <ID>
                            <TYPE>ID07</TYPE>
                            <VALUE>'.$arrOwnerData['pan_number'].'</VALUE>
                        </ID>
                    </IDS>
                    <PHONES>
                        <PHONE>
                            <TELE-NO>'.$arrOwnerData['mobile_no'].'</TELE-NO>
                            <TELE-NO-TYPE>P03</TELE-NO-TYPE>
                        </PHONE>
                    </PHONES>
                </INDV-APPLICANT-SEGMENT>
                <INDV-ADDRESS-SEGMENT>
                    <ADDRESS>
                        <TYPE>D01</TYPE>
                        <ADDRESS-LINE>'.$arrOwnerData['address'].'</ADDRESS-LINE>
                        <CITY>'.$arrOwnerData['city_name'].'</CITY>
                        <STATE>'.$arrOwnerData['state_code'].'</STATE>
                        <PIN>'.$arrOwnerData['pin_code'].'</PIN>
                    </ADDRESS>
                </INDV-ADDRESS-SEGMENT>
            </INDIVIDUAL-ENTITY>'; 
      }
      return $individualSegment;
    }

    private function prepareXmlToGetData($arrRequest) {
        $this->res_frmt = $arrRequest['resFormat'];
        $requestXml = $this->_getHeaderSegment() . '<INQUIRY>
            <INQUIRY-UNIQUE-REF-NO>'.$arrRequest['inquiry_unique_ref_no'].'</INQUIRY-UNIQUE-REF-NO>
            <REQUEST-DT-TM>'.date('d-m-Y H:i:s').'</REQUEST-DT-TM>
            <REPORT-ID>'.$arrRequest['report_id'].'</REPORT-ID>
        </INQUIRY>';
        return $requestXml;
    }

    private function _getDummyXML() {
            $borrowerName = 'NATRAJ CONSTRUCTION  CO';
            $borrowerPan = 'QDJCW0732S';
            $borrowerTelePhone = '5482958671';
            $borrowerAddress = '1 ST FLOOR VIKAS COMPLEX, NR RDP ROAD, JAKAT NAKA';
            $borrowerLocality = '';
            $borrowerCity = 'MEHSANA';
            $borrowerState = 'GJ';
            $borrowerPin = '384002';


            $promoterName = 'RINA DEVI KALITA';
            $promoterDob = '01-03-1985';
            $promoterPan = 'DTWPB7443Y';
            $promoterTelePhone = '5615219299';
            $promoterAddress = 'VILL PALEPARA PO BARANGHATIKAMRUP ASSAM KAMRUP 781350';
            $promoterCity = 'KAMRUP';
            $promoterState = 'AS';
            $promoterPin = '781350';

            $promoter2 = FALSE;
            $individual2 = '';
            if ($promoter2) {
                $promoterName2 = 'SACHI RANI SINHA';
                $promoterDob2 = '09-01-1967';
                $promoterPan2 = 'DQLPJ9328U';
                $promoterTelePhone2 = '5545793326';
                $promoterAddress2 = 'W O DHRUB JYOTI SINHA VILL SEWTI PT 3 P BHUBANESWAR NAGAR CACHAR 788817';
                $promoterCity2 = 'BHUBANESWAR';
                $promoterState2 = 'AS';
                $promoterPin2 = '788817';
                $individual2 = '<INDIVIDUAL-ENTITY>
                  <INDIVIDUAL-ENTITY-TYPE>54</INDIVIDUAL-ENTITY-TYPE>
                  <INDV-APPLICANT-SEGMENT>
                    <APPLICANT-NAME>
                        <NAME-1>'. $promoterName2 .'</NAME-1>
                    </APPLICANT-NAME>
                    <MARITAL-STATUS></MARITAL-STATUS>
                    <GENDER></GENDER>
                    <DOB>
                        <DOB-DATE>'. $promoterDob2 .'</DOB-DATE>
                        <AGE></AGE>
                    </DOB>
                    <IDS>
                        <ID>
                            <TYPE>ID09</TYPE>
                            <VALUE>12873546</VALUE>
                        </ID>
                        <ID>
                            <TYPE>ID02</TYPE>
                            <VALUE>'. $promoterPan2 .'</VALUE>
                        </ID>
                    </IDS>
                        <PHONES>
                            <PHONE>
                                <TELE-NO>'. $promoterTelePhone2 .'</TELE-NO>
                                <TELE-NO-TYPE>P03</TELE-NO-TYPE>
                            </PHONE>
                        </PHONES>
                    </INDV-APPLICANT-SEGMENT>
                    <INDV-ADDRESS-SEGMENT>
                        <ADDRESS>
                            <TYPE>D01</TYPE>
                            <ADDRESS-LINE>'. $promoterAddress2 .'</ADDRESS-LINE>
                            <CITY>'. $promoterCity2 .'</CITY>
                            <STATE>'. $promoterState2 .'</STATE>
                            <PIN>'. $promoterPin2 .'</PIN>
                        </ADDRESS>
                    </INDV-ADDRESS-SEGMENT>
                </INDIVIDUAL-ENTITY>';
            }

            $request_xml = '<REQUEST-REQUEST-FILE>' . $this->_getHeaderSegment() . '<INQUIRY>
            <COMM-APPLICANT-SEGMENT>
                <BORROWER-NAME>'. $borrowerName .'</BORROWER-NAME>
                <BORROWER-SHORT-NAME>'. $borrowerName .'</BORROWER-SHORT-NAME>
                <LEGAL-CONSTITUTION>20</LEGAL-CONSTITUTION>
                <IDS>
                    <ID>
                        <TYPE>ID07</TYPE>
                        <VALUE>'. $borrowerPan .'</VALUE>
                    </ID>
                </IDS>
                <CLASS-OF-ACTIVITY-1>OTHER COMMUNITY, SOCIAL AND PERSONAL SERVICE ACTIVITIES </CLASS-OF-ACTIVITY-1>
                <PHONES>
                    <PHONE>
                        <TELE-NO>'. $borrowerTelePhone .'</TELE-NO>
                        <TELE-NO-TYPE>P01</TELE-NO-TYPE>
                    </PHONE>
                </PHONES>
            </COMM-APPLICANT-SEGMENT>
            <COMM-ADDRESS-SEGMENT>
                <ADDRESS>
                    <TYPE>D01</TYPE>
                    <ADDRESS-LINE>'. $borrowerAddress .'</ADDRESS-LINE>
                    <LOCALITY>'. $borrowerLocality .'</LOCALITY>
                    <CITY>'. $borrowerCity .'</CITY>
                    <STATE>'. $borrowerState .'</STATE>
                    <PIN>'. $borrowerPin .'</PIN>
                </ADDRESS>
            </COMM-ADDRESS-SEGMENT>
            <APPLICATION-SEGMENT>
                <INQUIRY-UNIQUE-REF-NO>'. $this->random_no .'</INQUIRY-UNIQUE-REF-NO>
                <CREDT-INQ-PURPS-TYP>ACCT-ORI</CREDT-INQ-PURPS-TYP>
                <CREDT-INQ-PURPS-TYP-DESC>Loan_Purpose_Desc</CREDT-INQ-PURPS-TYP-DESC>
                <CREDIT-INQUIRY-STAGE>PRE_DISB</CREDIT-INQUIRY-STAGE>
                <CREDT-RPT-ID>CRDRQINQR</CREDT-RPT-ID>
                <CREDT-REQ-TYP>INDV</CREDT-REQ-TYP>
                <CREDT-RPT-TRN-DT-TM>24-08-2017 21:10:00</CREDT-RPT-TRN-DT-TM>
                <MBR-ID>'. $this->mbr_id .'</MBR-ID>
                <KENDRA-ID>PUNE</KENDRA-ID>
                <BRANCH-ID>PUNE</BRANCH-ID>
                <LOS-APP-ID>0507RE2015003215</LOS-APP-ID>
                <LOAN-TYPE>9999</LOAN-TYPE>
                <LOAN-AMOUNT>100000</LOAN-AMOUNT>
            </APPLICATION-SEGMENT>
            <INDIVIDUAL-ENTITIES-SEGMENT> 
              <INDIVIDUAL-ENTITY>
              <INDIVIDUAL-ENTITY-TYPE>54</INDIVIDUAL-ENTITY-TYPE>
              <INDV-APPLICANT-SEGMENT>
                <APPLICANT-NAME>
                    <NAME-1>'. $promoterName .'</NAME-1>
                </APPLICANT-NAME>
                <MARITAL-STATUS></MARITAL-STATUS>
                <GENDER></GENDER>
                <DOB>
                    <DOB-DATE>'. $promoterDob .'</DOB-DATE>
                    <AGE></AGE>
                </DOB>
                <IDS>
                    <ID>
                        <TYPE>ID09</TYPE>
                        <VALUE>12873546</VALUE>
                    </ID>
                    <ID>
                        <TYPE>ID02</TYPE>
                        <VALUE>'. $promoterPan .'</VALUE>
                    </ID>
                </IDS>
                    <PHONES>
                        <PHONE>
                            <TELE-NO>'. $promoterTelePhone .'</TELE-NO>
                            <TELE-NO-TYPE>P03</TELE-NO-TYPE>
                        </PHONE>
                    </PHONES>
                </INDV-APPLICANT-SEGMENT>
                <INDV-ADDRESS-SEGMENT>
                    <ADDRESS>
                        <TYPE>D01</TYPE>
                        <ADDRESS-LINE>'. $promoterAddress .'</ADDRESS-LINE>
                        <CITY>'. $promoterCity .'</CITY>
                        <STATE>'. $promoterState .'</STATE>
                        <PIN>'. $promoterPin .'</PIN>
                    </ADDRESS>
                </INDV-ADDRESS-SEGMENT>
                </INDIVIDUAL-ENTITY>'. $individual2 .'
            </INDIVIDUAL-ENTITIES-SEGMENT></INQUIRY></REQUEST-REQUEST-FILE>';
            return $request_xml;
    }  


}
