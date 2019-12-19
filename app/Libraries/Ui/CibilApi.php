<?php

namespace App\Libraries\Ui;

use GuzzleHttp\Client;
use GuzzleHttp\RequestOptions;


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
                                        'userId ' => 'crif1_cpu_uat@capsavefinance.com',
                                        'password' => '55DE689372D33C9876D1E09CFFF8BBBFF74B9445',
                                        'mbrid' => 'NBF0002966',
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
                                  <SUB-MBR-ID>CAPSAVE FINANCE PRIVATE LIMITED </SUB-MBR-ID>
                                  <INQ-DT-TM>18-12-2017 12:13:51</INQ-DT-TM>
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
                                       <VALUE>'.$arrOwnerData->pan_gst_hash.'</VALUE>
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
                                     <BRANCH-ID>PUN3008</BRANCH-ID>
                                     <LOS-APP-ID>18122017INDVTEST121351</LOS-APP-ID>
                                     <LOAN-AMOUNT>200000</LOAN-AMOUNT>
                              </APPLICATION-SEGMENT>
                           </INQUIRY>
                        </REQUEST-REQUEST-FILE>';

                return $requestXml;
    }    
    

   

   public function getCommercialCibilAcknowledgement($arrOwnerData) {
        try {
            $api_url = 'Inquiry/Inquiry/CPUAction.action';
            $options = [
                            'headers' => [
                                        'inquiryXML' => $this->prepareRequestXmlForAcknowledgement($arrOwnerData),
                                        'userId ' => 'crif1_cpu_uat@capsavefinance.com',
                                        'password' => '55DE689372D33C9876D1E09CFFF8BBBFF74B9445',
                            ]
                       ];
            $response = $this->client->post($api_url, $options);
            $response = $response->getBody()->getContents();
            return $response;
        } catch (\Exception $e) {
            //dd($e);
        }
    }




     public function prepareRequestXmlForAcknowledgement($arrOwnerData) {
    

        $requestXml = '<?xml version="1.0" encoding="UTF-8"?>
                          <REQUEST-REQUEST-FILE>
                             <HEADER-SEGMENT>
                                <PRODUCT-TYP>COMM_ACE_PLUS_REPORT</PRODUCT-TYP>
                                <PRODUCT-VER>4.0</PRODUCT-VER>
                                <REQ-MBR>NBF0002966</REQ-MBR>
                                <SUB-MBR-ID>CAPSAVE FINANCE PRIVATE LIMITED</SUB-MBR-ID>
                                <REQ-VOL-TYP>INDV</REQ-VOL-TYP>
                                <REQ-ACTN-TYP>SUBMIT</REQ-ACTN-TYP>
                                <TEST-FLG>Y</TEST-FLG>
                                <USER-ID>crif1_cpu_uat@capsavefinance.com</USER-ID>
                                <PWD>55DE689372D33C9876D1E09CFFF8BBBFF74B9445</PWD>
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
                                   <BORROWER-NAME>'.$arrOwnerData->first_name.' '.$arrOwnerData->last_name.'</BORROWER-NAME>
                                   <LEGAL-CONSTITUTION>20</LEGAL-CONSTITUTION>
                                   <IDS>
                                      <ID>
                                         <TYPE>ID07</TYPE>
                                         <VALUE>'.$arrOwnerData->pan_gst_hash.'</VALUE>
                                      </ID>
                                      <ID>
                                         <TYPE>ID08</TYPE>
                                         <VALUE>'.$arrOwnerData->biz_cin.'</VALUE>
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
                                      <ADDRESS-LINE>'.$arrOwnerData->owner_addr.'</ADDRESS-LINE>
                                      
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


    public function getCommercialCibilData($arrOwnerData) {
        try {
            $api_url = 'Inquiry/Inquiry/CPUAction.action';
            $options = [
                            'headers' => [
                                        'inquiryXML' => $this->prepareRequestXmlForIssue($arrOwnerData),
                                        'userId ' => 'crif1_cpu_uat@capsavefinance.com',
                                        'password' => '55DE689372D33C9876D1E09CFFF8BBBFF74B9445',
                            ]
                       ];
            $response = $this->client->post($api_url, $options);
            $response = $response->getBody()->getContents();
            return $response;
        } catch (\Exception $e) {
            //dd($e);
        }
    }


  public function prepareRequestXmlForIssue($arrOwnerData) {
    

        $requestXml = '<?xml version="1.0" encoding="UTF-8"?>
                          <REQUEST-REQUEST-FILE>
                             <HEADER-SEGMENT>
                                <PRODUCT-TYP>COMM_ACE_PLUS_REPORT</PRODUCT-TYP>
                                <PRODUCT-VER>4.0</PRODUCT-VER>
                                <REQ-MBR>NBF0002966</REQ-MBR>
                                <SUB-MBR-ID>CAPSAVE FINANCE PRIVATE LIMITED</SUB-MBR-ID>
                                <INQ-DT-TM>24-08-2017</INQ-DT-TM>
                                <REQ-VOL-TYP>INDV</REQ-VOL-TYP>
                                <REQ-ACTN-TYP>AT02</REQ-ACTN-TYP>
                                <TEST-FLG>Y</TEST-FLG>
                                <USER-ID>crif1_cpu_uat@capsavefinance.com</USER-ID>
                                <PWD>55DE689372D33C9876D1E09CFFF8BBBFF74B9445</PWD>
                                <AUTH-FLG>N</AUTH-FLG>
                                <AUTH-TITLE>USER</AUTH-TITLE>
                                <MEMBER-PRE-OVERRIDE>N</MEMBER-PRE-OVERRIDE>
                                <RES-FRMT>XML/HTML</RES-FRMT>
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
                                <INQUIRY-UNIQUE-REF-NO>'.$arrOwnerData->inquiry_unique_ref_no.'</INQUIRY-UNIQUE-REF-NO>
                                <REQUEST-DT-TM>24-08-2017 21:10:00</REQUEST-DT-TM>
                                <REPORT-ID>'.$arrOwnerData->report_id.'</REPORT-ID>
                             </INQUIRY>
                          </REQUEST-REQUEST-FILE>';

return $requestXml;
    }  


}
