<?php 
namespace App\Libraries;
use Illuminate\Support\Facades\Config;
use App\Inv\Repositories\Models\FinanceModel;

define('KARZA_TXN_LIB_URL', config('proin.KARZA_TXN_LIB_URL'));
define('KARZA_TXN_LIB_KEY', config('proin.KARZA_TXN_LIB_KEY'));

class KarzaTxn_lib
{
	private $request_type;
	private $httpMethod = 'POST';
	const BASE_URL    = KARZA_TXN_LIB_URL;
	const KARZA_KEY   = KARZA_TXN_LIB_KEY;
	

	public function api_call($params, $validate_otp = false){
		$resp = array(
			'status' => 'fail',
			'message'=> 'Some error occured. Please try again',
		 );
		if (!empty($params['password'])) {
			 $this->request_type = 'login';
			 $url = SELF::BASE_URL . 'trrn';
		}else{
			$this->request_type = 'otp';
			$url = SELF::BASE_URL . 'gst-return-auth';
		}

		if (!$validate_otp && (empty($params['username']) || empty($params['gstin']))) {
			$resp['message'] = "Mandate Fields are required";
			return $resp;
		}

		if ($validate_otp && (empty($params['requestId']) || empty($params['otp']))) {
			$resp['message'] = "Mandate Fields are required";
			return $resp;
		}

		if ($validate_otp) {
			$req_arr = array(
				'requestId' => $params['requestId'],
				'otp' => $params['otp'],
			);
		}else{
			$req_arr = array(
				'username' => $params['username'],
				'gstin' => $params['gstin'],
				'consent' => 'y',
			);
			if (!empty($params['password'])) {
				$req_arr['password'] = $params['password'];
			}	
		}
		$payload = json_encode($req_arr);
		$headers = array(
			"content-type: application/json",
		    "x-karza-key: " . SELF::KARZA_KEY,
		);

		$log_req = array(
	     	'req_file' => base64_encode($payload),
	     	'status' => 'pending',
	     	'gstin' => $params['gstin'],
	     	'app_id' => $params['app_id'],
	     	'url' => base64_encode($url),
	     );
	     $inserted_id = FinanceModel::insertPerfios($log_req, 'biz_gst_log');

		$response = $this->_curl_call($url, $payload, $headers);
	    if (!empty($response['error_no'])) {
	     	$resp['message'] = $response['error'] ?? "Unable to get response. Please retry.";
			return $resp;
	    }
	    $update_log = array(
	     	"res_file" => is_array($response['result']) || is_object($response['result']) ? base64_encode(json_encode($response['result'])) : base64_encode($response['result']),
	     	"status" => 'success',
	    );
	    $result = json_decode($response['result'], TRUE);
	    if (!empty($result['statusCode']) && $result['statusCode'] != '101') {
	    	$update_log['status'] = 'fail';
	    	$update_log['request_id'] = $result['requestId'] ?? NULL;
	    	FinanceModel::updatePerfios($update_log,'biz_gst_log', $inserted_id);
	    	$resp['message'] = $this->request_type == 'login' ? $this->error_desc($result['statusCode']) : "Unable to send OTP. Please try again later.";
			return $resp;
	    }
	    if (!empty($result['status'])) {
	    	$update_log['status'] = 'fail';
	    	$update_log['request_id'] = $result['requestId'] ?? NULL;
	    	FinanceModel::updatePerfios($update_log,'biz_gst_log', $inserted_id);
	    	$resp['message'] = $result['error'] ?? "Unable to get response. Please retry.";
			return $resp;
	    }

	    if ($validate_otp && !empty($result['result']['responseStatusCode'])) {
	    	$update_log['status'] = 'fail';
	    	$update_log['request_id'] = $result['requestId'] ?? NULL;
	    	FinanceModel::updatePerfios($update_log,'biz_gst_log', $inserted_id);
	    	$resp['message'] = "Unable to validate OTP. Please try again.";
			return $resp;
	    }

	    $resp['status'] =  "success";
	    $resp['message'] =  "success";
	    $resp['requestId'] = $result['requestId'];
	    $update_log['request_id'] = $result['requestId'];
	    FinanceModel::updatePerfios($update_log,'biz_gst_log', $inserted_id);
	    $resp['result'] =  json_encode($result['result']);
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