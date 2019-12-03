<?php 
namespace App\Libraries;

define('GUPSHUP_BASE_URL', config('proin.GUPSHUP_BASE_URL'));
define('GUPSHUP_KEY', config('proin.GUPSHUP_KEY'));
define('GUPSHUP_USR', config('proin.GUPSHUP_USR'));
define('GUPSHUP_PASS', config('proin.GUPSHUP_PASS'));

class Gupshup_lib
{
	private $httpMethod = 'POST';

	const BASE_URL    = GUPSHUP_BASE_URL;
	const GUPSHUP_KEY   = GUPSHUP_KEY;
	const ENCRYPTED   = FALSE;
	const GUPSHUP_USR   = GUPSHUP_USR;
	const GUPSHUP_PASS   = GUPSHUP_PASS;
	

	public function api_call($params){
		$resp = array(
			'status' => 'fail',
			'message'=> 'Some error occured. Please try again',
		 );
		$request = array(
		 	'method' => 'SendMessage',
		 	'msg_type' => 'TEXT',
		 	'auth_scheme' => 'plain',
		 	'v' => '1.1',
		 	'format' => 'text',
		 	'password' => SELF::GUPSHUP_PASS,
		);
		if (empty($params['mobile'])) {
		 	$resp['message'] = "Mobile number field is required";
		 	return $resp;
		}

		if (empty($params['message'])) {
		 	$resp['message'] = "Message field is required";
		 	return $resp;
		}
		$request['send_to'] = $params['mobile'];
		$request['msg'] = $params['message'];

		if (SELF::ENCRYPTED) {
		 	$payload['encrdata'] = $this->_aesEncrypt(http_build_query($request));
		}else{
		 	$payload = $request;
		}
		$payload['userid'] = SELF::GUPSHUP_USR;

		$query_string = http_build_query($payload);
		$url = SELF::BASE_URL . '?' . urlencode($query_string);

		$headers = array(
			"content-type: application/x-www-form-urlencoded",
		);
		$payload = '';
		$response = $this->_curl_call($url, $payload, $headers);
	    if (!empty($response['error_no'])) {
	     	$resp['message'] = $response['error'] ?? "Unable to get response. Please retry.";
			return $resp;
	    }
	    
	    list($status, $code, $message) = explode('|', $response['result']);
	    if (strtolower($status) != 'success') {
	    	$resp['message'] =  $message ?? "Unable to get response. Please retry.";
			return $resp;
	    }
	    $resp['status'] =  "success";
	    $resp['message'] =  "success";
	    $resp['result'] =   $message;
		return $resp;
	}

	public function validate_req($method, $req){
		$resp = array(
			'status' => 'fail',
			'message'=> 'Some error occured. Please try again',
		);
		if ($method == SELF::SEND_OTP && (empty($req['mobile']) || !ctype_digit($req['mobile']))) {
			$resp['message'] = "Mobile no is not valid";
			return $resp;
		}

		if ($method == SELF::MOB_VLD && (empty($req['mobile']) || !ctype_digit($req['mobile']))) {
			$resp['message'] = "Mobile no is not valid";
			return $resp;
		}

		if ($method == SELF::VERF_OTP) {
			if (empty($req['request_id'])) {
				$resp['message'] = "Request Id is not valid";
				return $resp;
			}elseif (empty($req['otp']) || !ctype_digit($req['otp'])) {
				$resp['message'] = "Shared OTP is not valid";
				return $resp;
			}
			
		}

		if ($method == SELF::GET_DTL && empty($req['request_id'])) {
			$resp['message'] = "Request Id is not valid";
			return $resp;
		}
		$resp['status'] = "success";
		$resp['message'] = "success";
		return $resp;
	}


	private function _aesEncrypt($string){
	  $method = "aes-128-gcm";
      $key = SELF::GUPSHUP_KEY;
      $iv = '000000000000';//mt_rand(100000,999999).mt_rand(100000,999999);
      $encrypted = openssl_encrypt($string, $method, $key, OPENSSL_RAW_DATA, $iv, $tag);
      return base64_encode($encrypted);
	}

	private function _aesDecrypt($ciphertext){
		 $method = "aes-128-gcm";
	     $key = SELF::GUPSHUP_KEY;
	     $iv = '000000000000';//mt_rand(100000,999999).mt_rand(100000,999999);
		 $decrypted = openssl_decrypt(base64_decode($ciphertext), $method, $key, OPENSSL_RAW_DATA, $iv, $tag);
		 return $decrypted;
	}

	private function _curl_call($url, $postdata, $header, $timeout=300){
		$curl = curl_init();
		curl_setopt($curl, CURLOPT_URL, $url);
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($curl, CURLOPT_MAXREDIRS, 10);
		curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 2);
		curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, 2);
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

	public function error_desc($error_code = '101') {
		$error_desc = array(
			"102" => "Invalid ID number or combination of inputs",
			"103" => "No records found for the given ID or combination of inputs",
			"104" => "Max retries exceeded",
			"105" => "Missing Consent",
			"106" => "Multiple Records Exist",
			"107" => "Not Supported",
		);
		return ($error_desc[$error_code] ?? 'Unknown Error');
	}

}