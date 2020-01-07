<?php 
namespace App\Libraries;

use phpseclib\Crypt\RSA;
use Illuminate\Support\Facades\Config;
use Auth;


define('FIXED', array('vendorId' => 'capsave','time' => date('Ymd\THis\Z')));
define('BSA_LIB_URL', config('proin.BSA_LIB_URL'));

class Bsa_lib{
	private $httpMethod = 'POST';
	const BASE_URL    =  'https://ESBUAT1RTN0140.idfcbank.com:9444/capsave/';
	const SECRET_KEY    =  'wdqrEbgYfilAWJXCLRrqfYGdGJJGSShf';

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
		 $query_string = '';
		 
		 list($payload, $concat, $httpMethod) = $this->_genReq($method, $params);
		 $date = FIXED["time"];
		 $url = SELF::METHOD[$method]. $concat;
		 $parsed_url = array_values(parse_url($url));
		 list($scheme, $host, $path) = $parsed_url;
		 if (strtoupper($httpMethod) == 'GET') {
		 	list($scheme, $host, $path, $query_string) = $parsed_url;
		 }
		 if ($method == SELF::UPL_FILE) {
		 	 $req_data = '';
		 	 $content_type = "Content-Type: multipart/form-data";
		 }else {
		 	 $req_data = $payload;
		 	 $content_type = "Content-Type: application/xml";
	     }

		 $sha256Payload = $this->_getHash($req_data);
		 $canonical_url = $this->_uriEncode($path);
		 $signed_headers = "host;x-perfios-content-sha256;x-perfios-date";
		 $canonical_headers = "host:$host"."\n"."x-perfios-content-sha256:$sha256Payload"."\n"."x-perfios-date:$date";
		 $canonical_req = "$httpMethod"."\n"."$canonical_url"."\n"."$query_string"."\n"."$canonical_headers"."\n"."$signed_headers"."\n"."$sha256Payload";
		 $req_hex = $this->_getHash($canonical_req);
		 $string2sign = "PERFIOS-RSA-SHA256"."\n"."$date"."\n"."$req_hex";
		 $checksum = $this->_getHash($string2sign);
		 $signature = $this->_genSignature($checksum);

		 $headers = array(
			'1' =>  "X-Perfios-Algorithm: PERFIOS-RSA-SHA256",
					"X-Perfios-Content-Sha256: $sha256Payload",
					"X-Perfios-Date: $date",
					"X-Perfios-Signature: $signature",
					"X-Perfios-Signed-Headers: host;x-perfios-content-sha256;x-perfios-date",
		 );
		 $headers[0] = $content_type;
		 $log_req = array(
	     	'perfios_log_id' => $params['perfiosTransactionId'] ?? NULL,
	     	'req_file' => is_array($payload) || is_object($payload) ? base64_encode(json_encode($payload)) : base64_encode($payload),
	     	'url' => base64_encode($url),
	     	'status' => 'pending',
	     	'created_by' => Auth::user()->user_id,
	     );
	     $inserted_id = FinanceModel::insertPerfios($log_req, 'biz_perfios_log');
	     $response = $this->_curl_call($url, $payload, $headers);
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
	    				'Payment' => $params['request'],
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
    	$this->httpMethod = $httpMethod;
    	$payload = !empty($payload) ? $payload : '';
    	return [$payload, $concat, $httpMethod];
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
    	$sign_string = $http_header['timeStamp'].' '.SELF::SECRET_KEY.$http_header['txn_id'];
    	$resp_data = array(
    		'TimeStamp' => $http_header['timeStamp'],
    		'Tran_ID' => $http_header['txn_id'],
    		'Sign' => SELF::SECRET_KEY,
    		'Corp_ID' => $http_header['corp_id'],
    	);
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

    private function _is_valid_xml($xml){
		libxml_use_internal_errors(TRUE);
		$doc = new DOMDocument('1.0', 'utf-8');
    	$doc->loadXML( $xml );
    	$errors = libxml_get_errors();
    	libxml_clear_errors();
		return empty($errors);
    }

    private function _genPayload(array $arr){
       $xml = '<payload>';
       foreach ($arr as $k => $v) {
            $xml .= "<$k>$v</$k>";
       }
       $xml .= "</payload>";
       return $xml;
    }

    private function _genSignature($checksum){
       $signature = $this->_encrypt($checksum);
       return $signature;
    }

    private function _encrypt($checksum){
    	$rsa = new RSA();
		if ($rsa->loadKey(base64_decode(SELF::PRIVATE_KEY)) != TRUE) {
		    return false;
		}
		$rsa->setHash("sha256");
		$rsa->setMGFHash("sha256");
		$crypted = $rsa->sign($checksum);
		return strtolower(bin2hex($crypted));
    }


    private function _pass_encrypt($password){
     	openssl_public_encrypt($password, $encrypted, base64_decode(SELF::PUBLIC_KEY));
     	return bin2hex($encrypted);
    }

    private function _decrypt($ciphertext){
        openssl_public_decrypt(hex2bin($ciphertext), $decrypted, base64_decode(SELF::PUBLIC_KEY));
        return $decrypted;
    }

    private function _getHash($string){
    	$hash = bin2hex(hash('sha256', $string, true));
    	return strtolower($hash);
    }

    private function _uriEncode($input){
      $result = '';
      $arr = str_split($input);
      for ($i = 0; $i < strlen($input); $i++) {
          $ch = $arr[$i];
          if (($ch >= 'A' && $ch <= 'Z') || ($ch >= 'a' && $ch <= 'z') || ($ch >= '0' && $ch <= '9') || $ch == '_' || $ch == '-' || $ch == '~' || $ch == '.' || $ch == '/') {
             $result .= $ch; 
          } else {
           $result .= dechex($ch);
          }
      }
      return $result;
	}

}

 ?>