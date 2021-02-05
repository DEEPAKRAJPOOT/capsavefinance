<?php 
namespace App\Libraries;

use phpseclib\Crypt\RSA;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Storage;
use Auth;

define('FIXED', array('vendorId' => 'cfpl','time' => date('Ymd\THis\Z')));
define('IDFC_LIB_URL', config('lms.IDFC_API_URL'));
define('IDFC_CRYPTO_KEY', config('lms.IDFC_CRYPTO_KEY'));
define('IDFC_CORP_ID', config('lms.IDFC_CORP_ID'));
date_default_timezone_set("Asia/Kolkata");

class Idfc_lib{
	private $httpMethod = 'POST';
	const BASE_URL    =  IDFC_LIB_URL . FIXED['vendorId']. '/';
	const SECRET_KEY    =  IDFC_CRYPTO_KEY; //NEW
	const CORP_ID    =  IDFC_CORP_ID;

	const MULTI_PAYMENT    = '1001';    #Intiate Multi Payment API
	const BATCH_ENQ    = '1002';    #Check Batch Transaction Inquiry API

	const MULTI_PAYMENT_URL    = SELF::BASE_URL . 'multiPayment';
	const BATCH_ENQ_URL    = SELF::BASE_URL . 'multiPaymentStatusEnquiry';
	const METHOD = array(
			SELF::MULTI_PAYMENT 	 => SELF::MULTI_PAYMENT_URL,
			SELF::BATCH_ENQ 	 => SELF::BATCH_ENQ_URL,
	);

	const STATUS = array(
			SELF::MULTI_PAYMENT => 'transaction',
			SELF::BATCH_ENQ => 'success',
	);

	public function api_call($method = NULL, array $params = array(), $getApiResponse = false){
		 $resp = array(
			'status' => 'fail',
			'message'=> 'Some error occured. Please try again',
		 );
		 if (!isset(SELF::METHOD[$method])) {
			$resp['code'] = "UnknownMethod";
			$resp['message'] = "Method not available";
			return $resp;
		 }
		$url = SELF::METHOD[$method];
		$query_string = '';
		$request = $this->_genReq($method, $params);
		if (!empty($request['status']) && $request['status'] == 'fail') {
			return $request;
		}
		list($payload, $http_header, $txn_id) = $request;
		// dd($url, $payload, $http_header);
     	$response = $this->_curlCall($url, $payload, $http_header);
     	// $response = $this->staticEnquiryResponse();
     	// $response = $this->staticPaymentResponse();
     	if ($getApiResponse) {
     		return [$txn_id, $payload, $http_header, $response['result']];
     	}
		if (!empty($response['error_no'])) {
			$resp['code'] 	 = "CurlError : " . $response['error_no'];
			$resp['message'] = $response['error'] ?? "Unable to get response. Please retry.";
			return $resp;
		}
		if (empty($response['error_no']) && $response['curl_info']['http_code'] != 200) {
			$resp['code'] 	 = "HTTPCode : " . $response['curl_info']['http_code'];
			$resp['message'] = $response['error'] ?? "Unable to get response. Please retry.";
			return $resp;
		}
		$result = $this->_parseResult($response['result'], $method);
		$result['result']['url'] = $url;
		$result['result']['payload'] = $payload;
		$result['result']['http_header'] = $http_header;
		$result['result']['response'] = $response['result'];
		$result['http_code'] = $response['curl_info']['http_code'] ?? '';

		return $result;
    }

    private function _genReq($method, $params){
 		$resp = array(
	 		'status' => "fail",
	 		'code' => "InputRequired",
	 		'message' => "Error occured during request generation",
	 	);
    	$payload = [];
    	$httpMethod = "POST";
    	if (!isset($params['header'])) {
	 		$resp['code'] = "headerRequired";
	 		$resp['message'] = "Request Header mandatory for this API";
			return $resp;
	 	}
	 	if (!isset($params['http_header'])) {
	 		$resp['code'] = "HTTPheaderRequired";
	 		$resp['message'] = "HTTP Header mandatory for this API";
			return $resp;
	 	}
	 	if (!isset($params['request'])) {
	 		$resp['code'] = "PaymentRequired";
	 		$resp['message'] = "Request Payment mandatory for this API";
			return $resp;
	 	}
    	switch ($method) {
    		case SELF::MULTI_PAYMENT:
	    		$req['doMultiPaymentCorpReq'] = array(
	    			'Header' => $params['header'],
	    			'Body' => array(
	    				'Payment' => array_values($params['request']),
	    			 ),
	    		);
    			break;
    		case SELF::BATCH_ENQ:
    			$req['doMultiPaymentCorpReq'] = array(
	    			'Header' => $params['header'],
	    			'Body' => array(
	    				'Tran_ID' => $params['request']['txn_id'],
	    			 ),
	    		);
    			break;
    		default:
    			/*code if default api will work*/
    			break;
    	}
    	$req_json = json_encode($req);
    	$req_http_header = $this->_genHeader($params['http_header']);
    	$this->httpMethod = $httpMethod;
    	return [$req_json, $req_http_header, $params['http_header']['txn_id']];
    }

    private function _curlCall($url, $postdata, $header ,$timeout= 600){
    	$idfc_cert_path = getcwd() . '/idfc_cert/prod/';
		$curl = curl_init();
		curl_setopt($curl, CURLOPT_URL, $url);
		curl_setopt($curl, CURLOPT_CONNECTTIMEOUT, $timeout);
		curl_setopt($curl, CURLOPT_TIMEOUT, $timeout);
		curl_setopt($curl, CURLOPT_MAXREDIRS, 10);
		curl_setopt($curl, CURLOPT_HEADER, true);
		curl_setopt($curl, CURLOPT_POSTFIELDS, $postdata);
		curl_setopt($curl, CURLOPT_HTTPHEADER, $header);
		curl_setopt($curl, CURLOPT_CUSTOMREQUEST, $this->httpMethod);
		curl_setopt($curl, CURLOPT_VERBOSE, true);
		curl_setopt($curl, CURLOPT_SSLCERT, $idfc_cert_path . 'cert.pem');
		curl_setopt($curl, CURLOPT_SSLKEY, $idfc_cert_path . 'priv.key');
		curl_setopt($curl, CURLOPT_CAINFO, $idfc_cert_path . 'cacert.pem');
		curl_setopt($curl, CURLOPT_POST, true);
		curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, true);
		curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 2);
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
		$output = curl_exec($curl);
		$resp['error'] = curl_error($curl);
		$resp['error_no'] = curl_errno($curl);
		$resp['curl_info'] = curl_getinfo($curl);
		$resp['result'] = $output;
		curl_close($curl);
		return $resp;
    }

    private function _genHeader($http_header){
    	$timestamp = $http_header['timestamp'];
    	$txn_id = $http_header['txn_id'];
    	$sign_string = $timestamp.$txn_id;
    	$signature = $this->_genSignature($sign_string);
    	$corpId = SELF::CORP_ID;
    	$resp_data = array(
    		"Content-Type: application/json",
    		"Corp_ID: $corpId",
    		"Sign: $signature",
    		"TimeStamp: $timestamp",
    		"Tran_ID: $txn_id",
    	);
    	return $resp_data;
    }

    private function _parseResult($IdfcResp, $method) {
    	$result = ['status' => 'fail','message' => 'Some Error Occured While Parsing the Response.'];
    	$json = $this->_getJsonFromString($IdfcResp);
    	$is_valid = @$this->_isValidJson($json);
    	if (!$is_valid) {
    		$result['code'] = "InvalidJSON";
    		$result['message'] = "Response is not in valid JSON format";
    		return $result;
    	}
    	$response = json_decode($json, true);
    	if (empty($response['doMultiPaymentCorpRes'])) {
    		$result['code'] = 'CAP000';
    		$result['message'] = 'Response cannot be parsed.';
	    	$result['result'] = $response['Response'] ?? $response;
	    	return $result;
    	}
    	$header = $response['doMultiPaymentCorpRes']['Header'];
    	$body = $response['doMultiPaymentCorpRes']['Body'] ?? [];
	    if (strtolower($header['Status']) != 'success' ) {
	    	$result['code'] = $header['Error_Cde'] ?? 'CAP001'; //change to Error_Code if response changes
	    	$result['message'] = $header['Error_Desc'] ?? 'Some error occured';
	    }else{
	    	$result['status'] = 'success';
	    	$result['message'] = 'success';
	    	$result['result'] = [
	    		'header'=> $header,
	    		'body'=> $body
	    		];
	    }
	    return $result;
    }

    private function _getJsonFromString($string) {
    	$simpleString = preg_replace("#\r|\n|\s+#", " ", $string);
    	$exceptJson = preg_replace('#\{(?:[^{}]|(?R))*\}#', '', $simpleString);
    	$json = str_replace($exceptJson, "", $simpleString);
    	return $json;
    }

    private function _removeAttribute($array = array()){
	   $result = isset($array['@attributes']) ? $array['@attributes']:[];
	   foreach ($array as $key => $value) {
		   	if ($key === '@attributes') {
		   		continue;
		   	}else{
		   		if (is_array($value)) {
		   			$result[$key] =  $this->_removeAttribute($value);
		   		}else{
		   			$result[$key] =  $value;
		   		}
		   	}
	   }
	   return $result;
	}

    private function _isValidJson($json){
    	json_decode($json);
		return (json_last_error() == JSON_ERROR_NONE);
    }

    private function _genSignature($checksum){
       $signature = hash_hmac('sha256', $checksum, SELF::SECRET_KEY);
       return $signature;
    }

    private function _saveLogFile($data, $w_filename = '', $w_folder = '') {
      	list($year, $month, $date, $hour) = explode('-', strtolower(date('Y-M-dmy-H')));
      	$path = Storage::disk('public')->put("/IDFCH2H/CAPSAVEUAT/ACHDR/$w_folder/$w_filename", $data);
      	return True;
	}

    private function staticPaymentResponse() {
      	
      	$enquiryRes['result'] = 'HTTP/1.1 200 OK
Date: Thu, 10 Dec 2020 12:02:41 GMT
server: 
Content-Type: application/json;charset=UTF-8
Content-Length: 653

{
  "doMultiPaymentCorpRes":{
    "Header":{
      "Tran_ID":"2RLJQ4955QEVD6FVFJ",
      "Corp_ID":"CAPSAVEAPI",
      "Status":"Success"
    },
    "Body":{
      "Tran_ID":"2RLJQ4955QEVD6FVFJ",
      "TranID_Status":"SUCCESS",
      "TranID_StatusDesc":"FILE HAS BEEN ACCEPTED",
      "Transaction":[
        {
          "RefNo":"2RLJQ4955JFV",
          "UTR_No":null,
          "Mode_of_Pay":"RTGS",
          "Ben_Acct_No":"50200026128604",
          "Ben_Name_as_per_dest_bank":"NA",
          "Ben_IFSC":"HDFC0000891",
          "RefStatus":"FAILED",
          "StatusDesc":"CLEARED BAL/FUNDS/DP NOT AVAILABLE.CARE!"
        }
      ]
    }
  }
}';
      	return $enquiryRes;
	}

    private function staticEnquiryResponse() {
      	
      	$enquiryRes['result'] = 'HTTP/1.1 200 OK
Date: Thu, 04 Feb 2021 13:58:18 GMT
server: 
Content-Type: application/json;charset=UTF-8
Content-Length: 635

{
  "doMultiPaymentCorpRes":{
    "Header":{
      "Tran_ID":"2SBDT0730FTIIY0RJL",
      "Corp_ID":"CAPSAVEAPI",
      "Status":"Success"
    },
    "Body":{
      "Tran_ID":"2SBDT0730FTIIY0RJL",
      "TranID_Status":"SUCCESS",
      "TranID_StatusDesc":"FILE HAS BEEN ACCEPTED",
      "Transaction":[
        {
          "RefNo":"2SBDT0730Q0P",
          "UTR_No":"IDFBH21035982775",
          "Mode_of_Pay":"NEFT",
          "Ben_Acct_No":"01682320002803",
          "Ben_Name_as_per_dest_bank":null,
          "Ben_IFSC":"HDFC0002249",
          "RefStatus":"SUCCESS",
          "StatusDesc":"SUCCESS"
        }
      ]
    }
  }
}';
      	return $enquiryRes;
	}
}

 ?>