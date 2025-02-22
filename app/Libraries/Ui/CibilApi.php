<?php

namespace App\Libraries\Ui;

use GuzzleHttp\Client;
use GuzzleHttp\RequestOptions;
use Carbon\Carbon;

class CibilApi {

    protected $client;
    protected $key;
    protected $headers;
    protected $url;
    protected $path = '';
    protected $consent = 'Y';
    protected $body;
    

    public function __construct(Client $client) {
        $this->client = $client;
 //         $this->headers = [
//            'cache-control' => "no-cache",
//            'Content-Type' => "application/json",
//            'x-karza-key' => env('KARZA_AUTHENTICATION_API_KEY'),
//        ];
    }


    public function getPromoterCibilRequest($arrOwnerData) {
        try {
            $api_url = 'Inquiry/doGet.service/requestResponseSync';
            $options = [
                            'headers' => [
                                        'requestXML' => $this->prepareRequestXml($arrOwnerData),
                                        'userId ' => 'crif1_cpu_prd@capsavefinance.com',
                                        'password' => 'D261F46CFA7E1C0DB5A80FB02269668E1A3F05B9',
                                        'mbrid' => 'NBF0000834',
                                        'productType' => 'INDV',
                                        'productVersion' => '1.0',
                                        'reqVolType' => 'INDV',
                            ]
                       ];
             $response = $this->client->post($api_url, $options);
             $response = $response->getBody()->getContents();
            // dd($response);
             return $response;
            
        } catch (\Exception $e) {
            return [];
        }
    }


    public function prepareRequestXml($arrOwnerData) {
      //  dd($arrOwnerData->first_name);

        $requestXml = '<?xml version="1.0" encoding="UTF-8"?>
                        <REQUEST-REQUEST-FILE>
                           <HEADER-SEGMENT>
                                  <SUB-MBR-ID>CAPSAVE FINANCE PRIVATE LTD</SUB-MBR-ID>
                                  <REQ-ACTN-TYP>SUBMIT</REQ-ACTN-TYP>
                                  <TEST-FLG>N</TEST-FLG>
                                  <AUTH-FLG>Y</AUTH-FLG>
                                  <AUTH-TITLE>USER</AUTH-TITLE>
                                  <RES-FRMT>XML/HTML</RES-FRMT>
                                  <MEMBER-PRE-OVERRIDE>N</MEMBER-PRE-OVERRIDE>
                                  <RES-FRMT-EMBD>Y</RES-FRMT-EMBD>
                                  <MFI>
                                     <INDV>true</INDV>
                                     <SCORE>false</SCORE>
                                     <GROUP>true</GROUP>
                                  </MFI>
                                  <CONSUMER>
                                     <INDV>true</INDV>
                                     <SCORE>true</SCORE>
                                  </CONSUMER>
                                  <IOI>true</IOI>
                           </HEADER-SEGMENT>
                           <INQUIRY>
                              <APPLICANT-SEGMENT>
                                 <APPLICANT-NAME>
                                    <NAME1>'.$arrOwnerData->first_name.' '.$arrOwnerData->last_name.'</NAME1>
                                 </APPLICANT-NAME>
                                 <DOB>
                                    <DOB-DATE>'.$arrOwnerData->date_of_birth.'</DOB-DATE>
                                 </DOB>
                                 <IDS>
                                    <ID>
                                       <TYPE>ID01</TYPE>
                                       <VALUE>'.$arrOwnerData->pan_number.'</VALUE>
                                    </ID>
                                 </IDS>
                              </APPLICANT-SEGMENT>
                              <ADDRESS-SEGMENT>
                                 <ADDRESS>
                                        <TYPE>D01</TYPE>
                                        <ADDRESS-1>'.$arrOwnerData->address.'</ADDRESS-1>
                                        <CITY>'.$arrOwnerData->city.'</CITY>
                                        <STATE>'.$arrOwnerData->state.'</STATE>
                                        <PIN>'.$arrOwnerData->pin.'</PIN>
                                 </ADDRESS>
                              </ADDRESS-SEGMENT>
                              <APPLICATION-SEGMENT>
                                     <INQUIRY-UNIQUE-REF-NO>18122017INDVTEST121351</INQUIRY-UNIQUE-REF-NO>
                                     <CREDT-INQ-PURPS-TYP>ACCT-ORIG</CREDT-INQ-PURPS-TYP>
                                     <CREDIT-INQUIRY-STAGE>PRE-DISB</CREDIT-INQUIRY-STAGE>
                                     <CREDT-REQ-TYP>INDV</CREDT-REQ-TYP>
                                     <LOS-APP-ID>18122017INDVTEST121351</LOS-APP-ID>
                              </APPLICATION-SEGMENT>
                           </INQUIRY>
                        </REQUEST-REQUEST-FILE>';
                return $requestXml;
    }    
    

   

   public function getCommercialCibilAcknowledgement($arrRequest) {

        try {
            $api_url = 'Inquiry/Inquiry/CPUAction.action';
            $options = [
                            'headers' => [
                                        'inquiryXML' => $this->prepareRequestXmlForAcknowledgement($arrRequest),
                                        'userId ' => 'crif1_cpu_prd@capsavefinance.com',
                                        'password' => 'D261F46CFA7E1C0DB5A80FB02269668E1A3F05B9',
                            ]
                       ];
           // dd($options);       
            $response = $this->client->post($api_url, $options);
	    $response = $response->getBody()->getContents();
            return $response;
        } catch (\Exception $e) {
            //dd($e);
        }
    }




     public function prepareRequestXmlForAcknowledgement($arrRequest) {
        $requestXml = '<?xml version="1.0" encoding="UTF-8"?>
                          <REQUEST-REQUEST-FILE>
                             <HEADER-SEGMENT>
                                <PRODUCT-TYP>COMM_ACE_PLUS_REPORT</PRODUCT-TYP>
                                <PRODUCT-VER>4.0</PRODUCT-VER>
                                <REQ-MBR>NBF0000834</REQ-MBR>
                                <SUB-MBR-ID>CAPSAVE FINANCE PRIVATE LTD</SUB-MBR-ID>
                                <REQ-VOL-TYP>INDV</REQ-VOL-TYP>
                                <REQ-ACTN-TYP>SUBMIT</REQ-ACTN-TYP>
                                <TEST-FLG>Y</TEST-FLG>
                                <USER-ID>crif1_cpu_prd@capsavefinance.com</USER-ID>
                                <PWD>D261F46CFA7E1C0DB5A80FB02269668E1A3F05B9</PWD>
                                <AUTH-FLG>N</AUTH-FLG>
                                <AUTH-TITLE>USER</AUTH-TITLE>
                                <RES-FRMT>XML/HTML</RES-FRMT>
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
                                </CONSUMER>
                             </HEADER-SEGMENT>
                             <INQUIRY>
                                <COMM-APPLICANT-SEGMENT>
                                   <BORROWER-NAME>'.$arrRequest['biz_name'].'</BORROWER-NAME>
                                   <LEGAL-CONSTITUTION>20</LEGAL-CONSTITUTION>
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
                                   <CLASS-OF-ACTIVITY-1>OTHER COMMUNITY, SOCIAL AND PERSONAL SERVICE ACTIVITIES</CLASS-OF-ACTIVITY-1>
                                   <PHONES>
                                      <PHONE>
                                         <TELE-NO>9875987468</TELE-NO>
                                         <TELE-NO-TYPE>P01</TELE-NO-TYPE>
                                      </PHONE>
                                   </PHONES>
                                </COMM-APPLICANT-SEGMENT>
                                <COMM-ADDRESS-SEGMENT>
                                   <ADDRESS>
                                      <TYPE>D01</TYPE>
                                      <ADDRESS-LINE>'.$arrRequest['biz_address'].'</ADDRESS-LINE>
                                      
                                      <CITY>Mumbai</CITY>
                                      <STATE>MH</STATE>
                                      <PIN>400053</PIN>
                                   </ADDRESS>
                                </COMM-ADDRESS-SEGMENT>
                                <APPLICATION-SEGMENT>
                                   <INQUIRY-UNIQUE-REF-NO>1908927821COMM54647</INQUIRY-UNIQUE-REF-NO>
                                   <CREDT-INQ-PURPS-TYP>ACCT-ORI</CREDT-INQ-PURPS-TYP>
                                   <CREDT-INQ-PURPS-TYP-DESC>Loan_Purpose_Desc</CREDT-INQ-PURPS-TYP-DESC>
                                   <CREDIT-INQUIRY-STAGE>PRE_DISB</CREDIT-INQUIRY-STAGE>
                                   <CREDT-RPT-ID>CRDRQINQR</CREDT-RPT-ID>
                                   <CREDT-REQ-TYP>INDV</CREDT-REQ-TYP>
                                   <CREDT-RPT-TRN-DT-TM>24-08-2017 21:10:00</CREDT-RPT-TRN-DT-TM>
                                   <MBR-ID>BOROCTSAN005</MBR-ID>
                                   <KENDRA-ID>PUNE</KENDRA-ID>
                                   <BRANCH-ID>PUNE</BRANCH-ID>
                                   <LOS-APP-ID>0507RE2015003215</LOS-APP-ID>
                                   <LOAN-TYPE>9999</LOAN-TYPE>
                                   <LOAN-AMOUNT>100000</LOAN-AMOUNT>
                                </APPLICATION-SEGMENT>
                             </INQUIRY>
                          </REQUEST-REQUEST-FILE>';

return $requestXml;
    }  


    public function getCommercialCibilData($arrRequest) {
        try {
            $api_url = 'Inquiry/Inquiry/CPUAction.action';
            $options = [
                            'headers' => [
                                        'inquiryXML' => $this->prepareRequestXmlForIssue($arrRequest),
                                        'userId ' => 'crif1_cpu_prd@capsavefinance.com',
                                        'password' => 'D261F46CFA7E1C0DB5A80FB02269668E1A3F05B9',
                            ]
                       ];
            $response = $this->client->post($api_url, $options);
            $response = $response->getBody()->getContents();
            return $response;
        } catch (\Exception $e) {
            //dd($e);
        }
    }


  public function prepareRequestXmlForIssue($arrRequest) {
        $requestXml = '<?xml version="1.0" encoding="UTF-8"?>
                          <REQUEST-REQUEST-FILE>
                             <HEADER-SEGMENT>
                                <PRODUCT-TYP>COMM_ACE_PLUS_REPORT</PRODUCT-TYP>
                                <PRODUCT-VER>4.0</PRODUCT-VER>
                                <REQ-MBR>NBF0000834</REQ-MBR>
                                <SUB-MBR-ID>CAPSAVE FINANCE PRIVATE LTD</SUB-MBR-ID>
                                <INQ-DT-TM>24-08-2017</INQ-DT-TM>
                                <REQ-VOL-TYP>INDV</REQ-VOL-TYP>
                                <REQ-ACTN-TYP>AT02</REQ-ACTN-TYP>
                                <TEST-FLG>Y</TEST-FLG>
                                <USER-ID>crif1_cpu_prd@capsavefinance.com</USER-ID>
                                <PWD>D261F46CFA7E1C0DB5A80FB02269668E1A3F05B9</PWD>
                                <AUTH-FLG>N</AUTH-FLG>
                                <AUTH-TITLE>USER</AUTH-TITLE>
                                <MEMBER-PRE-OVERRIDE>N</MEMBER-PRE-OVERRIDE>
                                <RES-FRMT>'.$arrRequest['resFormat'].'</RES-FRMT>
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
                                </CONSUMER>
                             </HEADER-SEGMENT>
                             <INQUIRY>
                                <INQUIRY-UNIQUE-REF-NO>'.$arrRequest['inquiry_unique_ref_no'].'</INQUIRY-UNIQUE-REF-NO>
                                <REQUEST-DT-TM>24-08-2017 21:10:00</REQUEST-DT-TM>
                                <REPORT-ID>'.$arrRequest['report_id'].'</REPORT-ID>
                             </INQUIRY>
                          </REQUEST-REQUEST-FILE>';

return $requestXml;
    }  


}
