<?php 
namespace App\Libraries;

use phpseclib\Crypt\RSA;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Storage;
use Auth;


define('FIXED', array('vendorId' => 'capsave','time' => date('Ymd\THis\Z')));
define('BSA_LIB_URL', config('proin.BSA_LIB_URL'));

class Idfc_lib{
	private $httpMethod = 'POST';
	const BASE_URL    =  'https://ESBUAT1RTN0140.idfcbank.com:9444/capsave/';
	const SECRET_KEY    =  'wdqrEbgYfilAWJXCLRrqfYGdGJJGSShf';
	const CORP_ID    =  'CAPSAVEUAT';

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



	public function api_call($method, array $params = array()){
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
		list($payload, $http_header, $txn_id) = $this->_genReq($method, $params);
		$file_name = md5($txn_id).'.txt';
		$this->_saveLogFile($payload, $file_name, 'Outgoing');
     	$response = $this->_curl_call($url, $payload, $http_header);
     	dd($response);
     	$this->_saveLogFile($response, $file_name, 'Incoming');

		if (!empty($response['error_no'])) {
			$resp['code'] 	 = "CurlError";
			$resp['message'] = $response['error'] ?? "Unable to get response. Please retry.";
			return $resp;
		}

		$result = $this->_parseResult($response['result'], $method);
		return $result;
    }

    private function _genReq($method, $params){

 		$resp = array(
	 		'code' => "FileRequired",
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
    	$req_http_header = $this->_gen_header($params['http_header']);
    	$this->httpMethod = $httpMethod;
    	return [$req_json, $req_http_header, $params['http_header']['txn_id']];
    }

    private function _curl_call($url, $postdata, $header ,$timeout= 300){
		$curl = curl_init();
		curl_setopt($curl, CURLOPT_URL, $url);
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($curl, CURLOPT_MAXREDIRS, 10);
		curl_setopt($curl, CURLOPT_TIMEOUT, $timeout);
		curl_setopt($curl, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_1);
		curl_setopt($curl, CURLOPT_CUSTOMREQUEST, $this->httpMethod);
		curl_setopt($curl, CURLOPT_POSTFIELDS, $postdata);
		curl_setopt($curl, CURLOPT_HTTPHEADER, $header);
		$output = curl_exec($curl);
		$resp['error'] = curl_error($curl);
		$resp['error_no'] = curl_errno($curl);
		$resp['result'] = $output;
		curl_close($curl);
		return $resp;
    }

    private function _gen_header($http_header){
    	$sign_string = $http_header['timestamp'].' '.SELF::SECRET_KEY.$http_header['txn_id'];
    	$resp_data = array(
    		'TimeStamp' => $http_header['timestamp'],
    		'Tran_ID' => $http_header['txn_id'],
    		'Sign' => $this->_genSignature($sign_string),
    		'Corp_ID' => SELF::CORP_ID
    	);
    	return $resp_data;
    }

    private function _parseResult($json, $method) {
    	$result = ['status' => 'success','result' => ''];
    	$is_valid = true;//@$this->_is_valid_json($json);
    	if (!$is_valid) {
    		$result['status'] = "fail";
    		$result['code'] = "InvalidJSON";
    		$result['message'] = "response json is not valid";
    		return $result;
    	}
    	
    	$response = json_decode($json, true);
    	$header = $response['doMultiPaymentCorpRes']['Header'];
    	$body = $response['doMultiPaymentCorpRes']['Body'];

	    if (strtolower($header['Status']) != 'success' ) {
	    	$result['status'] = 'fail';
	    	$result['message'] = 'Some error occured';
	    }else{
	    	$result['message'] = 'success';
	    	$result['result'] = ['header'=>$header,'body'=> $body];
	    }
	    return $result;
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

    private function _is_valid_json($json){
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

}

 ?>