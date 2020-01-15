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
		 dd($http_header);
     	$response = $this->_curl_call($url, $payload, $http_header);
	     if (!empty($response['error_no'])) {
	     	$resp['code'] 	 = "CurlError";
	     	$resp['message'] = $response['error'] ?? "Unable to get response. Please retry.";
			return $resp;
	     }
	     $update_log = array(
	     	"res_file" => is_array($response['result']) || is_object($response['result']) ? base64_encode(json_encode($response['result'])) : base64_encode($response['result']),
	     );

	     if ($method == SELF::GET_REP && !in_array($params['types'], ['xml','json'])) {
	     	$xml = @simplexml_load_string($response['result']);
	     	if(!$xml){
		     	$update_log['status'] = "success";
		     	FinanceModel::updatePerfios($update_log,'biz_perfios_log', $inserted_id);
		     	$resp['status'] = "success";
		     	$resp['message'] = "success";
			 	$resp['result'] = $response['result'];
			 	return $resp;
		 	}
	     }

	      if ($method == SELF::GET_REP && 'json' == strtolower($params['types'])) {
	     	$xml = @simplexml_load_string($response['result']);
	     	if(!$xml){
	     		$update_log['status'] = "success";
	     		FinanceModel::updatePerfios($update_log,'biz_perfios_log', $inserted_id);
	     		$resp['status'] = "success";
			 	$resp['result'] = $response['result'];
			 	return $resp;
	     	}
	     }
	     
	     $result = $this->_parseResult($response['result'], $method);
	     $update_log['status'] = $result['status'];
	     FinanceModel::updatePerfios($update_log, 'biz_perfios_log', $inserted_id);
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

    private function _parseResult($xml, $method) {
    	$result = ['status' => 'success','result' => ''];
    	$is_valid = true;//@$this->_is_valid_xml($xml);
    	if (!$is_valid) {
    		$result['status'] = "fail";
    		$result['code'] = "InvalidXML";
    		$result['message'] = "response xml is not valid";
    		return $result;
    	}

    	$p = xml_parser_create();
	    xml_parse_into_struct($p, $xml, $resp);
	    xml_parser_free($p);
    	$status = strtolower($resp[0]['tag']);

    	if (SELF::STATUS[$method] != strtolower($status)) {
	    	$result['status'] = "fail";
	    }

    	if ($method == SELF::GET_REP && strtolower($result['status']) == 'success') {
    		$xml_obj = simplexml_load_string($xml);
    		$xml_arr = json_decode(json_encode($xml_obj), TRUE);
    		foreach ($xml_arr as $key => $value) {
    			$result[$key] = $this->_removeAttribute($value);
    		}
    		return $result;
    	}

	    foreach ($resp as $key => $value) {
	    	if ($value['type'] == 'complete' && (!empty($value['value']) || strtolower($value['tag']) == 'success')) {
	    		if (!empty($result[strtolower($value['tag'])])) {
	    			$result[strtolower($value['tag'])] = $result[strtolower($value['tag'])]. ' & ' .$value['value'] ?? 'success';
	    			continue;
	    		}
	    		$result[strtolower($value['tag'])] = $value['value'] ?? 'success';
	    	}
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
		libxml_use_internal_errors(TRUE);
		$doc = new DOMDocument('1.0', 'utf-8');
    	$doc->loadXML( $xml );
    	$errors = libxml_get_errors();
    	libxml_clear_errors();
		return empty($errors);
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