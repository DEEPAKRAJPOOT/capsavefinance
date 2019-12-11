<?php 
namespace App\Libraries;
use Illuminate\Support\Facades\Config;
use App\Inv\Repositories\Models\BizApiLog;
use Auth;

define('MOBILE_AUTH_LIB_URL', config('proin.MOBILE_AUTH_LIB_URL'));
define('MOBILE_AUTH_LIB_KEY', config('proin.MOBILE_AUTH_LIB_KEY'));

class MobileAuth_lib
{
	private $request_type;
	private $httpMethod = 'POST';
	const BASE_URL    = MOBILE_AUTH_LIB_URL;
	const KARZA_KEY   = MOBILE_AUTH_LIB_KEY;

	const SEND_OTP   = '1001';   #SEND OTP
	const VERF_OTP   = '1002';   #VERIFY OTP
	const GET_DTL    = '1003';	 #GET MOBILE DETAIL
	const MOB_VLD    = '1004';	 #GET MOBILE DETAIL

	const SEND_OTP_URL  = SELF::BASE_URL . 'v2/mobile/otp';
	const VERF_OTP_URL  = SELF::BASE_URL . 'v2/mobile/status';
	const GET_DTL_URL  = SELF::BASE_URL . 'v2/mobile/details';
	const MOB_VLD_URL  = SELF::BASE_URL . 'v3/mobile-auth';


	const METHOD = array(    #Comment if any method is not available
			SELF::SEND_OTP => SELF::SEND_OTP_URL,
			SELF::VERF_OTP => SELF::VERF_OTP_URL,
			SELF::GET_DTL => SELF::GET_DTL_URL,
			SELF::MOB_VLD => SELF::MOB_VLD_URL,

	);

	const STATUS = array(
			SELF::SEND_OTP => 'success',
			SELF::VERF_OTP => 'success',
			SELF::GET_DTL => 'accepted',
			SELF::MOB_VLD => 'accepted',
	);
	

	public function api_call($method , $params){
		$resp = array(
			'status' => 'fail',
			'message'=> 'Some error occured. Please try again',
		 );

		if (!isset(SELF::METHOD[$method])) {
			$resp['code'] = "UnknownMethod";
			$resp['message'] = "Method not available";
			return $resp;
		}
		
		$validate = $this->validate_req($method, $params);
		if ($validate['status'] != 'success') {
			$resp['message'] = $validate['message'];
			return $resp;
		}

		if ($method == SELF::SEND_OTP) {
			$payload = array(
				'mobile' => $params['mobile'],
				'consent' => 'y',
			);
		}

		if ($method == SELF::MOB_VLD) {
			$payload = array(
				'mobile' => $params['mobile'],
				'consent' => 'y',
			);
		}

		if ($method == SELF::VERF_OTP) {
			$payload = array(
				'request_id' => $params['request_id'],
				'otp' => $params['otp'],
			);
		}
		if ($method == SELF::GET_DTL) {
			$payload = array(
				'request_id' => $params['request_id'],
			);
		}

		$payload = json_encode($payload);
		$headers = array(
			"content-type: application/json",
		    "x-karza-key: " . SELF::KARZA_KEY,
		);
		$url = SELF::METHOD[$method];


		$response = $this->_curl_call($url, $payload, $headers);


		
	    if (!empty($response['error_no'])) {
	     	$resp['message'] = $response['error'] ?? "Unable to get response. Please retry.";
			return $resp;
	    }
	    $result = json_decode($response['result'], TRUE);
            if((!empty($result['status-code']) && $result['status-code']==101) || !empty($result['status']))
            {
                 $status = 1;
            }
            else
            {
                $status = 0;
            }
            $createApiLog = @BizApiLog::create(['req_file' =>$payload, 'res_file' => (is_array($response['result']) || is_object($response['result']) ? json_encode($response['result']) : $response['result']),'status' => $status,
                  'created_by' => Auth::user()->user_id]);
	   $resp['createApiLog'] = $createApiLog;
	    if (!empty($result['status-code']) && $result['status-code'] != '101') {
	    	$resp['message'] = $this->error_desc($result['status-code']) ?? "Unable to get response. Please retry.";
			return $resp;
	    }
	    if (!empty($result['status'])) {
	    	$resp['message'] = $result['error'] ?? "Unable to get response. Please retry.";
			return $resp;
	    }
	    $resp['status'] =  "success";
	    $resp['message'] =  "success";
	    $resp['request_id'] = $result['request_id'] ?? '';
	    $resp['result'] =  $result['result'];
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


	private function _curl_call($url, $postdata, $header, $timeout=300){
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