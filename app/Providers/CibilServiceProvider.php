<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use GuzzleHttp\Client;

class CibilServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //$baseUrl = env('KARZA_AUTHENTICATION_API_URL');
        $baseUrl = 'https://test.crifhighmark.com';
        $this->app->singleton('GuzzleHttp\Client', function($api) use ($baseUrl) {
            return new Client([
                'base_uri' => $baseUrl,
                'headers' => [
                    'requestXML' => $this->prepareRequestXml(),
                    'userId ' => 'crif1_cpu_uat@capsavefinance.com',
                    'password' => '55DE689372D33C9876D1E09CFFF8BBBFF74B9445',
                    'mbrid' => 'NBF0002966',
                    'productType' => 'INDV',
                    'productVersion' => '1.0',
                    'reqVolType' => 'INDV',
                    

                ]
             ]);
        });
    }

    public function prepareRequestXml() {
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
                        <NAME1>NITIN JAIN</NAME1>
                        <NAME2 />
                        <NAME3 />
                        <NAME4 />
                        <NAME5 />
                     </APPLICANT-NAME>
                     <DOB>
                        <DOB-DATE>05/03/1981</DOB-DATE>
                        <AGE>39</AGE>
                        <AGE-AS-ON>29/07/2015</AGE-AS-ON>
                     </DOB>
                     <IDS>
                        <ID>
                           <TYPE>ID01</TYPE>
                           <VALUE>AFUPJ7365N</VALUE>
                        </ID>
                     </IDS>
                     <RELATIONS>
                        <RELATION>
                           <NAME>Vasu Kumar</NAME>
                           <TYPE>K01</TYPE>
                        </RELATION>
                     </RELATIONS>
                     <KEY-PERSON>
                        <NAME>Vasu K</NAME>
                        <TYPE>K01</TYPE>
                     </KEY-PERSON>
                     <NOMINEE>
                        <NAME>Vasu</NAME>
                        <TYPE>K01</TYPE>
                     </NOMINEE>
                     <PHONES>
                        <PHONE>
                           <TELE-NO>9551542844</TELE-NO>
                           <TELE-NO-TYPE>P03</TELE-NO-TYPE>
                        </PHONE>
                     </PHONES>
                  </APPLICANT-SEGMENT>
                  <ADDRESS-SEGMENT>
                     <ADDRESS>
                        <TYPE>D01</TYPE>
                        <ADDRESS-1>165049,1128,KFC,BANU NAGAR 29TH AVUNUE PUDUR,Silkboard,BANGALORE,KARNATAKA,600053</ADDRESS-1>
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

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        //
    }
   
}