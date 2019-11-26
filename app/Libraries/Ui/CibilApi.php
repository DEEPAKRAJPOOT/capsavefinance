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
                                        <ADDRESS-1>'.$arrOwnerData->owner_addr.'</ADDRESS-1>
                                        <CITY>BANGALORE</CITY>
                                        <STATE>KA</STATE>
                                        <PIN>600053</PIN>
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
    

   

}
