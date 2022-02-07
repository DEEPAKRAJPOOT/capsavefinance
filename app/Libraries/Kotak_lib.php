<?php
namespace App\Libraries;
use Illuminate\Support\Facades\Config;
//ini_set('display_errors', 1);
//error_reporting(E_ALL);
class Kotak_lib
{
  private $url = null;
  private $headers = [];
  private $httpMethod = 'POST';

  public function __construct()
  {
    $this->url = config('lms.KOTAK_API_URL').'?apikey='.config('lms.KOTAK_API_KEY');
    $this->headers = [
      "Content-type: application/xml",
      "Accept: application/xml"
    ];
    date_default_timezone_set("Asia/Kolkata");
  }

  private function _makePaymentRequest($requestData = [])
  {
    $xml = '<soap:Envelope xmlns:soap="http://www.w3.org/2003/05/soap-envelope" xmlns:pay="http://www.kotak.com/schemas/CMS_Generic/Payment_Request.xsd">
            <soap:Header/>
            <soap:Body>';
              foreach ($requestData as $key => $requestDataValue) {
                $xml .= '<pay:Payment>
                            <pay:RequestHeader>
                              <pay:MessageId>'.$requestDataValue['MessageId'].'</pay:MessageId>
                              <pay:MsgSource>'.$requestDataValue['MsgSource'].'</pay:MsgSource>
                              <pay:ClientCode>'.$requestDataValue['ClientCode'].'</pay:ClientCode>
                              <pay:BatchRefNmbr>'.$requestDataValue['BatchRefNmbr'].'</pay:BatchRefNmbr>
                              <pay:ReqRF1>'.$requestDataValue['TransId'].'</pay:ReqRF1>
                            </pay:RequestHeader>
                            <pay:InstrumentList>
                              <pay:instrument>
                                  <pay:InstRefNo>'.$requestDataValue['InstRefNo'].'</pay:InstRefNo>
                                  <pay:MyProdCode>'.$requestDataValue['MyProdCode'].'</pay:MyProdCode>
                                  <pay:PayMode>'.$requestDataValue['PayMode'].'</pay:PayMode>
                                  <pay:TxnAmnt>'.$requestDataValue['TxnAmnt'].'</pay:TxnAmnt>
                                  <pay:AccountNo>'.$requestDataValue['AccountNo'].'</pay:AccountNo>
                                  <pay:DrRefNmbr>'.$requestDataValue['DrRefNmbr'].'</pay:DrRefNmbr>
                                  <pay:DrDesc>'.$requestDataValue['DrDesc'].'</pay:DrDesc>
                                  <pay:PaymentDt>'.$requestDataValue['PaymentDt'].'</pay:PaymentDt>
                                  <pay:RecBrCd>'.$requestDataValue['RecBrCd'].'</pay:RecBrCd>
                                  <pay:BeneAcctNo>'.$requestDataValue['BeneAcctNo'].'</pay:BeneAcctNo>
                                  <pay:BeneName>'.$requestDataValue['BeneName'].'</pay:BeneName>
                                  <pay:BeneEmail>'.$requestDataValue['BeneEmail'].'</pay:BeneEmail>
                                  <pay:EnrichmentSet>
                                    <!--1 or more repetitions:-->
                                    <pay:Enrichment>'.$requestDataValue['Enrichment'].'</pay:Enrichment>
                                  </pay:EnrichmentSet>
                              </pay:instrument>
                            </pay:InstrumentList>
                        </pay:Payment>';
              }
      $xml .= '</soap:Body>
          </soap:Envelope>';
      return $xml;
  }

  private function _makeReversalRequest($requestData = [])
  {
    $xml = '<soap:Envelope xmlns:soap="http://www.w3.org/2003/05/soap-envelope" xmlns:rev="http://www.kotak.com/schemas/CMS_Generic/Reversal_Request.xsd">
            <soap:Header/>
            <soap:Body>
                <rev:Reversal>
                    <rev:Header>
                        <rev:Req_Id>'.$requestData['Req_Id'].'</rev:Req_Id>
                        <rev:Msg_Src>'.$requestData['Msg_Src'].'</rev:Msg_Src>
                        <rev:Client_Code>'.$requestData['Client_Code'].'</rev:Client_Code>
                        <rev:Date_Post>'.$requestData['Date_Post'].'</rev:Date_Post>
                    </rev:Header>
                    <rev:Details>
                        <!--Zero or more repetitions:-->
                        <rev:Msg_Id>'.$requestData['Msg_Id'].'</rev:Msg_Id>
                    </rev:Details>
                </rev:Reversal>
            </soap:Body>
          </soap:Envelope>';
    return $xml;
  }

  public function callPaymentApi($requestData = [], $headersRequest = [], $getApiResponse = false)
  {
    $xmlRequestData = $this->_makePaymentRequest($requestData);
    array_push($this->headers, "SOAPAction: /BusinessServices/StarterProcesses/CMS_Generic_Service.serviceagent/Payment");
    if ($xmlRequestData != null) {
      array_push($this->headers, "Content-Length: " . strlen($xmlRequestData));
      if(!empty($headersRequest)){
        $this->headers = array_merge($this->headers, $headersRequest);
      }
    }
    $getResponse =  $this->_curlCall($xmlRequestData);
    if ($getApiResponse) {
			return ['XMLREQUESTDATA'=>$xmlRequestData, 'DATA'=>$requestData, 'HTTP_HEADER'=>$this->headers, 'RESPONSE'=>$getResponse['result']];
		}

		if (!empty($getResponse['error_no'])) {
			$resp['code'] 	 = "CurlError : " . $getResponse['error_no'];
			$resp['message'] = $getResponse['error'] ?? "Unable to get response. Please retry.";
      $resp['content']  = $getResponse['result']??'';
			return $resp;
		}
		if (empty($getResponse['error_no']) && isset($getResponse['curl_info']) && $getResponse['curl_info'] != 200) {
			$resp['code'] 	 = "HTTPCode : " . $getResponse['curl_info'];
			$resp['message'] = $getResponse['error'] ?? "Unable to get response. Please retry.";
      $resp['content']  = $getResponse['result']??'';
			return $resp;
		}
    $plainXML = $this->convertSOAPXMLToArray($getResponse['result']);
    $result = json_decode(json_encode(SimpleXML_Load_String($plainXML, 'SimpleXMLElement', LIBXML_NOCDATA)), true);
    if ($result['SOAP-ENV_Body'] && isset($result['SOAP-ENV_Body']["SOAP-ENV_Fault"])) {
			$result['code'] = "HTTPCode : " . $getResponse['curl_info'].', '."Error Code : ".$result['SOAP-ENV_Body']["SOAP-ENV_Fault"]['SOAP-ENV_Code']['SOAP-ENV_Value'] . ", Response : ".$getResponse['result']; //change to Error_Code if response changes
			$result['message'] = "Error Reason : " .$result['SOAP-ENV_Body']["SOAP-ENV_Fault"]['SOAP-ENV_Reason']['SOAP-ENV_Text'][0]??'Some error occured';
      return $result;
    }else{
      $result['result']['url'] =  $this->url;
      $result['result']['payload'] = $xmlRequestData;
      $result['result']['http_header'] = $this->headers;
      $result['result']['response'] = $getResponse['result'];
      $result['http_code'] = $getResponse['curl_info']?? '';
      return $result;
    }
  }

  public function callReversalApi($requestData = [], $headersRequest = [], $getApiResponse = false)
  {
    $xmlRequestData = $this->_makeReversalRequest($requestData);
    array_push($this->headers, "SOAPAction: /BusinessServices/StarterProcesses/CMS_Generic_Service.serviceagent/Reversal");
    if ($xmlRequestData != null) {
      array_push($this->headers, "Content-Length: " . strlen($xmlRequestData));
      if(!empty($headersRequest)){
        $this->headers = array_merge($this->headers, $headersRequest);
      }
    }
    $getResponse = $this->_curlCall($xmlRequestData);
    if ($getApiResponse) {
			return ['XMLREQUESTDATA'=>$xmlRequestData, 'DATA'=>$requestData, 'HTTP_HEADER'=>$this->headers, 'RESPONSE'=>$getResponse['result']];
		}

    if (!empty($getResponse['error_no'])) {
			$resp['code'] 	 = "CurlError : " . $getResponse['error_no'];
			$resp['message'] = $getResponse['error'] ?? "Unable to get response. Please retry.";
      $resp['content']  = $getResponse['result']??'';
			return $resp;
		}
		if (empty($getResponse['error_no']) && isset($getResponse['curl_info']) && $getResponse['curl_info'] != 200) {
			$resp['code'] 	 = "HTTPCode : " . $getResponse['curl_info'];
			$resp['message'] = $getResponse['error'] ?? "Unable to get response. Please retry.";
      $resp['content']  = $getResponse['result']??'';
			return $resp;
		}
    $plainXML = $this->convertSOAPXMLToArray($getResponse['result']);
    $result = json_decode(json_encode(SimpleXML_Load_String($plainXML, 'SimpleXMLElement', LIBXML_NOCDATA)), true);
    //if ($result['SOAP-ENV_Body']["ns0_Reversal"]['ns0_Header']['ns0_Details']['ns0_Rev_Detail']['ns0_Status_Code'] != '000') {
			//$result['code'] = "HTTPCode : " . $getResponse['curl_info']; //change to Error_Code if response changes
			//$result['message'] = $result['SOAP-ENV_Body']["ns0_Reversal"]['ns0_Header']['ns0_Details']['ns0_Rev_Detail']['ns0_Status_Desc'] ?? 'Some error occured';
    //}else{
      $result['result']['url'] =  $this->url;
      $result['result']['payload'] = $xmlRequestData;
      $result['result']['http_header'] = $this->headers;
      $result['result']['response'] = $getResponse['result'];
      $result['http_code'] = $getResponse['curl_info']?? '';
      $result['status'] = $getResponse['curl_info']?? '';
    //}
    return $result;
  }

  private function _curlCall($xmlRequestData = NULL, $timeout = 600)
  {
    $resp = [];
    if(empty($xmlRequestData) || empty($this->headers)){
      $resp['error'] = "xml data or headers is not found in request. Please try again!";
      $resp['error_no'] = 1;
      $resp['curl_info'] = 404;
      $resp['result'] = false;
      return $resp;
    }
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $this->url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
    //curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, true);
		//curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 2);

    if ($xmlRequestData != null) {
      curl_setopt($ch, CURLOPT_POSTFIELDS, "$xmlRequestData");
    }
    //curl_setopt($ch, CURLOPT_USERPWD, "user_name:password"); /* If required */
		curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $timeout);
		curl_setopt($ch, CURLOPT_TIMEOUT, $timeout);
		curl_setopt($ch, CURLOPT_MAXREDIRS, 10);
		curl_setopt($ch, CURLOPT_HEADER, false);
		curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $this->httpMethod);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, $this->headers);
    curl_setopt($ch, CURLINFO_HEADER_OUT, false);
    $output = curl_exec($ch);
    //mail('amit.suman@zuron.in','xml outup responce', $output);
    $code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $err = curl_error($ch);
    curl_close($ch);
    if ($err) {
      $resp['error'] = $err;
      $resp['error_no'] = curl_errno($ch);
      $resp['curl_info'] = $code;
      $resp['result'] = $output;
    } else {
      $resp['curl_info'] = $code;
      $resp['result'] = $output;
    }
    return $resp;
  }

    // FUNCTION TO convertSOAPXMLToArray THE XML SO WE DO NOT HAVE TO DEAL WITH NAMESPACE
   public function convertSOAPXMLToArray($xml)
    {
       //mail('amit.suman@zuron.in','xml responce', $xml);
        $obj = SimpleXML_Load_String($xml,'SimpleXMLElement', LIBXML_NOCDATA);
        if ($obj === FALSE) return $xml;

        // GET NAMESPACES, IF ANY
        $nss = $obj->getNamespaces(TRUE);
        if (empty($nss)) return $xml;

        // CHANGE ns: INTO ns_
        $nsm = array_keys($nss);
        foreach ($nsm as $key)
        {
            // A REGULAR EXPRESSION TO MUNG THE XML
            $rgx
            = '#'               // REGEX DELIMITER
            . '('               // GROUP PATTERN 1
            . '\<'              // LOCATE A LEFT WICKET
            . '/?'              // MAYBE FOLLOWED BY A SLASH
            . preg_quote($key)  // THE NAMESPACE
            . ')'               // END GROUP PATTERN
            . '('               // GROUP PATTERN 2
            . ':{1}'            // A COLON (EXACTLY ONE)
            . ')'               // END GROUP PATTERN
            . '#'               // REGEX DELIMITER
            ;
            // INSERT THE UNDERSCORE INTO THE TAG NAME
            $rep
            = '$1'          // BACKREFERENCE TO GROUP 1
            . '_'           // LITERAL UNDERSCORE IN PLACE OF GROUP 2
            ;
            // PERFORM THE REPLACEMENT
            $xml =  preg_replace($rgx, $rep, $xml);
        }
        return $xml;
    }
}