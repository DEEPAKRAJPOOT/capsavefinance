<?php 
namespace App\Libraries;
use Illuminate\Support\Facades\Config;
use Auth;
use App\Inv\Repositories\Models\FinanceModel;

define('FSA_LIB_URL', config('proin.FSA_LIB_URL'));

class Perfios_lib{
	const BASE_URL    = FSA_LIB_URL;
	const PRIVATE_KEY = 'LS0tLS1CRUdJTiBSU0EgUFJJVkFURSBLRVktLS0tLQpNSUlKS2dJQkFBS0NBZ0VBaXQrMjUzTTRSZHRXeUFzN0ZuOUZqUWN6eVNXOVB1TnBmUU5udEdxdFpWVXpuNFJmCmtIMUVHdVgxOERCclFzT0NtY1gwbFJ2eG5QeDJMY3I1WHdFTjVpSzdwNlJPU2NQZ0pNczJZb2RLZVg0TW5sc3YKNHBZVlF3RjM3Z2JHSWlTcmxlTkR5T3MvTEpkT1FVU1dzSUhaMU11cHNOTitrS21Bc1ZseGw1TjdBYkZvbXpzdgpSbHhLZkhpeUVTbjRmVS9NMldob3RDZVpSMTJWMmFmV1Rhbi9sUTJoZEdGd2NPeC94RnExMjFET1Y3OUNFeU03CkJNZXp6MkRGeHVINFRYSTlWak9XN2FGVzNSZ0VSMGlPYTV5TVF6RWg3SWVLWTdwQjU5eGJzYnBkNWFlSkc0WloKM2gxUExEZ0lSaEZ2S01NaDFYY1RuaTcvSnpmRCtmWmtFMFI4TjFBVnZUTXV2YVJjTzNhVW1kcXJ4UStwWXZqcgo4b1J1V3htelI0ZVF0ekEvMnB6c1R2azcrdytadzY3Y2o0YTFGK280WFozV1FlK2YrL0YwZnlET3R6Q3c3RXRrClc1bkF5UkZwelVheUh2ZmhsejNWcGhFUkRWNWVOVG9RRFlWdXV6SldVVkM1ZFNMZnJXV1I4U3BaVmxKT3JML3IKNnZpK0lTbXBpUk5MTU9RYm4rZTZPVzNKcHROcEI4Tmo3Rmt5TnVBeWtiemtINFFsY3BlTm4yd1BtUjIzZVVOagpJSlM2T1Z1OE5OQmNPY2NQVEUyVk5qaVJmUlBMM3ZuWTVDUWs1RjJtMVJDR3RSbzYyUHFxYnlsYXBQZVpBcUlBCkxEcm9sUUgwNHpueE14TXFYU3BrQjNReCs1eEIwUmNFeC9Jc1FDQUUrTldMUXgxVUE2TTlzZkYrdDkwQ0F3RUEKQVFLQ0FnQlVpY0VXL2czWWdLQkFZSTNVc1I5T3JYNjV5UTU5OVFQblJTWmhMcnRsUDB1UXBJMWtNTTAvcnBOYgpacTQwMHoxeWpJM0tJMXVlbkJUNDVXR09rdDY2SkpTbGhhRGV5SzF1MklHLzB1bmtNWFpQQStXUGdwdG8wY0VICkZmcVJHRStYaStSY3I5QWlQZTAyVUNHSFMxU21kR0JWTXpvTnFCR2VaSHVBc1JlbFBURVhwaHl1TVVwcW9US00KdmtiMVNabkRFd3NCK0VVWHE0R29JTUtWUHBKR1V5amc1dHdDOGdVSks4c2V1ZjhTYld3MHFES3FRME5UVDI2MgpGaU1JU2kzS2h0Y3V5eE5NdmUwRTA4ZFcvTkpwcU1Razc1R3dDZGRLNU5xd3cybWJmclE1SEQwaXVDYlZIVHIwCkpITE5scFBIRlpqTXA0Rk5oMlgyaVNYOGtLa29RVFYrb1dsMDkyMXlSS0lHUWEzYU9BNlh3Sjl4L1IwSndoazkKQUtUMlc0U25Rc0lqS0VESVlQcEtvWDdPbXZueEhoeXJLOEJqVHZKNWcrZ1ltVTU1dkltMFUyS0g3WlBVNDY3QwpUZHdBNW8yaDkwMGtBSmx5S1h5Nms4aUJaTjlJNFNrYUJ1NnlsbmwvaURhbXVwd3JBeW1hMkxiZytvOFJZYklWCmdpVmlNbkdib1h3WVFSRjFiWnBNdGhYMlMvNlFrcU95ZEw4cE5vbXVHQXJxN2dlSVE2MDZmNy9FMEV2T3dQbTMKck53WnJhM0Z4TTVLWHlLTnhLNU95a1ZNQm50OUlPWE9BaHgxQU9VOXpMZVUyTlZMclVaczJKNzh0MXg5cTVnRwo0ZkZOcEthMFdZNUhyZEE5QTdUUURpL093QktkYTA0YS94WU1Ndmd1ZFhBZGVGS2dRUUtDQVFFQXZzRGxkUXlqClJLeUU3T2psUzBLaUZpQkxSR1g5SE1nUnhLZ2VIVUxpZ05LUmpweDRqVkVLZFp0UGFYZzIwenRTL0hZRHIxNi8KZVV4Q1lxemhRR3B0WUlMWmVlaVUvdHM4OTBHdEdNUk40U1p4SXplR2dTYzVOdkE1TVdHOTFuNWg4T2hCM01XTQphb0tHWGhXSlJDZjhONVljSk1uUG5XYVFZM3dEU0tTQTkyTkZreXFWZTdoMHJ3VzZkTjdjUGQrQkZ6cVkwaXNXCjM3NjJRcklWU29zOEpKcVZWSHliQnVSaEtzb1hRemxyR1AyVU9iQjFOWEplb1YwVWE3bmZUV0Z0RU5lOEk5N08KRGxVQlc1WEs4bnpYdm5BY3Q4bGZvR1FNMy8ycHAzSHV0WmhJSlE4VEFWMVRKSTZNQ2c1bDBxL2F0VHNPdnFoRApKUVBDN2xrSk9mM2dUUUtDQVFFQXVtQUtpZjdzWjN5U24wZmhFYis1NWtYNjVHUkVLV0ZkTEJXOGNGRWNHNnFKCkd6QWJXVW8reEVPS1dsQ0M2WmVCR2pRZ1pUaER5d3pQTDd4bk9tVFlxSktIczFhTXQ5QUF3Q3p6aFVlTksxbnoKRnlvclFWeW5QSTM0MlEzM1RUNlZuQXg0eDErZWJtY2NnZWxvdnBuOHlkdThTOVhyOEorTExIcUVXL0xMa2M2NgpXRTl2WmFGRkpGdUpHL2Fyc2p3OXQ5akY1TW9uZHRlbnZkbVRnWnMzMU1CNVRmMXV3U3dwMzJKNFh5S1NxSUMvCk1MWVRvZnZkaTZncTZjZGdNTWFXVEIzTFRPZlI2ZDRqT1pVODRaZVE1cnB6V3N4MUJqWFVOM2JPMWF5RHp5UGYKY0VJVE5ESkg2NHR2ZkRmemRYZ2pmYklnRDJ4MENhbUZhZ2w2d0FsOTBRS0NBUUVBbEpFaVpaYXhOdFJ6TngxeApNTHUyQ0N1ZzA2WG5qRm9hMUtMbnlYeUZjellOWGhocUlBNkZhZkhMMk9aak5RT1liNzd1d2RDMnFwK0ZlTnNUCkdSdUxFc2IyNE9jUDNLc2VnYWtxU3diaGJVR0dqcG81YlRBQ2ROS0dpUHFLWTV2TlpsZE9yTWREeE5UaUdEY0gKMFFpZmZSK3h6Y2xNcFZmemp6aFFTbXl0Y1lCaytPa0t3ZkI1R0xRS2MrbjdlVWt4ZnNrSnEyOGFBcXZEd3BCMgoycmMwNFlGd1d2d3R0aUY3dUZycjRWVEFJQmVvTVlKSTE1YTdNMHlPa2hTVFllNUdodjZ2cnZSaVluRThmSHRmCk5KdWRZTnBxMDQzSXArQWswdlA3QmNwTDFDM0Z5dTlCcXlkbmtTcGcwWmhESCtRTklHZmQ1UXFpT1JzRDRLV0IKOVNQUlpRS0NBUUVBcmNTK0RzdlFQdXkzQ0N0TWZlLzBzKzZyYmliT0pvYlJDRGw3Nkh5M2FGUW9ZV0VKSHNkSwpubE1hdS9vMUZQWmRPTUZCTXg2eWxxN3F2ZWM3bUFaT3UxMWppS0k1c1dnT1N4K2VMYlg0dVdLMGMvU3RQOVBUCldYSkZncHF5NlNKZys0M2xUbjZvaU9jNmZTWFNzMk4vZmZXU0ttTGFDUSs0QTFCMytBTDRLb3BFMC9HOE8xV1IKMGNSR29mdnJPZE5RK0FqMCtjeEIzMXhTMlgvek4vUXdxZnlMSFZ3ZDE1NEZySEZ6S2NCaVhSa3RLWTJaNEgyawpvb0c1QVI4VFlHYkgyMUFzTFJnM2I0WGQ4ZUpqVVRLdnQzQS8vWGlENDdac2x3bis3dHBhU3RkM2pJTU94S3ZjCndwRE84Vko1ZkJ6M2VrcUorZGQwbVUybFJxaHYrbnpmQVFLQ0FRRUFvUXJaRVFOOUtKUmVlMVhjSkp3alMrdHEKWDlyUmo5dlQvamowcUJ3YitIbzAyRG5LczFPZExFR2M4NjBQYW5UU0JuNzVEY05KZkcwVUxab2RSTVQyOHFBZwp1L1V4V01DSmZ1SFRkemJPS3hxSEhHVUs5bHNHa0Y3MDlaNjNZTzZrZWRTRTgvQThqTStjbVdXaStXa0FMdWwvCkltaVdRS1hpanF5RnhLeExQZkVTQXhuZ3ZFblBvZEFnSkRQSk1XdHNwU0MrR3VhOVRHTzhVSG84cFlxb1VWOUYKUWdjNTFWQkVPSThJaUl3enROdTlvOXVkc1kyaWUvMWFxTGJ0WVMwY0lyK1JGL1hqUXZob0R2M2llWS9LWFI4SgpCS2JwN0lScGlDZmpsazhsYllSVVpqQkEzL2ZqbGNyakd1RWoyTHFVNVo4NWxSUnFiYng0V1NmdWZGcDVDZz09Ci0tLS0tRU5EIFJTQSBQUklWQVRFIEtFWS0tLS0tCg==';
	const PUBLIC_KEY  = 'LS0tLS1CRUdJTiBQVUJMSUMgS0VZLS0tLS0KTUlJQ0lqQU5CZ2txaGtpRzl3MEJBUUVGQUFPQ0FnOEFNSUlDQ2dLQ0FnRUFpdCsyNTNNNFJkdFd5QXM3Rm45RgpqUWN6eVNXOVB1TnBmUU5udEdxdFpWVXpuNFJma0gxRUd1WDE4REJyUXNPQ21jWDBsUnZ4blB4MkxjcjVYd0VOCjVpSzdwNlJPU2NQZ0pNczJZb2RLZVg0TW5sc3Y0cFlWUXdGMzdnYkdJaVNybGVORHlPcy9MSmRPUVVTV3NJSFoKMU11cHNOTitrS21Bc1ZseGw1TjdBYkZvbXpzdlJseEtmSGl5RVNuNGZVL00yV2hvdENlWlIxMlYyYWZXVGFuLwpsUTJoZEdGd2NPeC94RnExMjFET1Y3OUNFeU03Qk1lenoyREZ4dUg0VFhJOVZqT1c3YUZXM1JnRVIwaU9hNXlNClF6RWg3SWVLWTdwQjU5eGJzYnBkNWFlSkc0WlozaDFQTERnSVJoRnZLTU1oMVhjVG5pNy9KemZEK2Zaa0UwUjgKTjFBVnZUTXV2YVJjTzNhVW1kcXJ4UStwWXZqcjhvUnVXeG16UjRlUXR6QS8ycHpzVHZrNyt3K1p3NjdjajRhMQpGK280WFozV1FlK2YrL0YwZnlET3R6Q3c3RXRrVzVuQXlSRnB6VWF5SHZmaGx6M1ZwaEVSRFY1ZU5Ub1FEWVZ1CnV6SldVVkM1ZFNMZnJXV1I4U3BaVmxKT3JML3I2dmkrSVNtcGlSTkxNT1FibitlNk9XM0pwdE5wQjhOajdGa3kKTnVBeWtiemtINFFsY3BlTm4yd1BtUjIzZVVOaklKUzZPVnU4Tk5CY09jY1BURTJWTmppUmZSUEwzdm5ZNUNRawo1RjJtMVJDR3RSbzYyUHFxYnlsYXBQZVpBcUlBTERyb2xRSDA0em54TXhNcVhTcGtCM1F4KzV4QjBSY0V4L0lzClFDQUUrTldMUXgxVUE2TTlzZkYrdDkwQ0F3RUFBUT09Ci0tLS0tRU5EIFBVQkxJQyBLRVktLS0tLQo=';
	
	const STRT_TXN    = '1001';   #Intiate the transaction
	const ADD_YEAR    = '1002';   #Add the financial year
	const UPL_STMT    = '1003';	  #Upload the statement
	const CMPLT_TXN   = '1004';	  #Complete the transaction
	const GET_STMT    = '1005';	  #Get uploaded statement detail
	const DEL_DATA    = '1006';	  #Delete the transaction
	const TXN_RVU     = '1007';	  #Review the transaction
	const GET_INST    = '1008';	  #Get list of institutes


	const STRT_TXN_URL  = SELF::BASE_URL . 'api/financial/start/';
	const ADD_YEAR_URL  = SELF::BASE_URL . 'api/financial/year/';
	const UPL_STMT_URL  = SELF::BASE_URL . 'api/financial/upload/';
	const CMPLT_TXN_URL = SELF::BASE_URL . 'api/financial/done/';
	const GET_STMT_URL  = SELF::BASE_URL . 'retrieve/';
	const DEL_DATA_URL  = SELF::BASE_URL . 'delete/';
	const TXN_RVU_URL   = SELF::BASE_URL . 'review/';
	const GET_INST_URL  = SELF::BASE_URL . 'institutions/';

	const METHOD = array(    #Comment if any method is not available
			SELF::STRT_TXN => SELF::STRT_TXN_URL,
			SELF::ADD_YEAR => SELF::ADD_YEAR_URL,
			SELF::UPL_STMT => SELF::UPL_STMT_URL,
			SELF::CMPLT_TXN => SELF::CMPLT_TXN_URL,
			SELF::GET_STMT => SELF::GET_STMT_URL,
			SELF::DEL_DATA => SELF::DEL_DATA_URL,
			SELF::TXN_RVU => SELF::TXN_RVU_URL,
			SELF::GET_INST => SELF::GET_INST_URL,
	);


	const STATUS = array(
			SELF::STRT_TXN => 'success',
			SELF::ADD_YEAR => 'success',
			SELF::UPL_STMT => 'accepted',
			SELF::CMPLT_TXN => 'accepted',
			SELF::GET_STMT => 'financialstatement',
			SELF::DEL_DATA => 'status',
			SELF::TXN_RVU => 'status',
			SELF::GET_INST => 'institutions',
	);

	public function error_handler($err_no, $err_msg = "Some error occured which cannot b handled") { 
    	$resp = array(
			'status' => 'fail',
			'message'=> $err_msg,
		);
		return $resp;
	}  


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

		 $concat = '';
		 if ($method == SELF::UPL_STMT) {
		 	if (!isset($params['file_content'])) {
		 		$resp['code'] = "FileRequired";
		 		$resp['message'] = "File is mandatory for this API";
				return $resp;
		 	}

		 	$filename =  $params['file_content'];
		 	$mimetype = mime_content_type($params['file_content']);
		 	$file['file'] = file_get_contents($params['file_content']);
		 	$cfile = curl_file_create($filename, $mimetype, basename($filename));
	     	$postdata = ['file' => $cfile];

	     	if (!empty($params['file_password']) && !empty(trim($params['file_password']))) {
	     		$postdata['password'] = $this->_pass_encrypt($params['file_password']);
	     	}
		 	$header = array('Content-Type: multipart/form-data');
		 	$concat = $params['perfiosTransactionId']. '/'. $params['financialYear'];
		 }else {
	     	$data['payload'] = $this->_genPayload($params);
	     	$data['signature'] = $this->_genSignature($data['payload']);
	     	$header = array("Content-Type: application/x-www-form-urlencoded");
	     	$postdata = http_build_query($data);
	     }
	     $url = SELF::METHOD[$method]. $concat;
	     $log_req = array(
	     	'perfios_log_id' => $params['perfiosTransactionId'] ?? NULL,
	     	'req_file' => is_array($postdata) || is_object($postdata) ? base64_encode(json_encode($postdata)) : base64_encode($postdata),
	     	'status' => 'pending',
	     	'created_by' => Auth::user()->user_id,
	     	'url' => base64_encode($url),
	     );
	     $inserted_id = FinanceModel::insertPerfios($log_req, 'biz_perfios_log');
	     $response = $this->_curl_call($method, $url, $postdata, $header);
	     if (!empty($response['error_no'])  || !empty($response['error'])) {
	     	$resp['code'] 	 = "CurlError";
	     	$resp['message'] = $response['error'] ?? "Unable to get response. Please retry.";
			return $resp;
	     }
	     $update_log = array(
	     	"res_file" => is_array($response['result']) || is_object($response['result']) ? base64_encode(json_encode($response['result'])) : base64_encode($response['result']),
	     );
	     if ($method == SELF::GET_STMT && !in_array($params['reportType'], ['xml','json'])) {
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

    private function _curl_call($method, $url, $postdata, $header ,$timeout= 300){
    	$curl = curl_init();
		curl_setopt($curl, CURLOPT_URL, $url);
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($curl, CURLOPT_MAXREDIRS, 10);
		curl_setopt($curl, CURLOPT_TIMEOUT, $timeout);
		curl_setopt($curl, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_1);
		curl_setopt($curl, CURLOPT_CUSTOMREQUEST, "POST");
		curl_setopt($curl, CURLOPT_POSTFIELDS, $postdata);
		curl_setopt($curl, CURLOPT_HTTPHEADER, $header);
		$output = curl_exec($curl);
		$resp['error'] = curl_error($curl);
		$resp['error_no'] = curl_errno($curl);
		$resp['result'] = $output;
		curl_close($curl);
		return $resp;
    }

    private function _parseResult($xml, $method) {
    	$result = ['status' => 'success', 'result' => ''];
    	$is_valid = true;//@$this->_is_valid_xml($xml);
    	if (!$is_valid) {
    		$result['status'] = "fail";
    		$result['code'] = "InvalidRespXML";
    		$result['message'] = "Response is not valid xml";
    		return $result;
    	}

    	$p = xml_parser_create();
	    xml_parse_into_struct($p, $xml, $resp);
	    xml_parser_free($p);
	    $status = strtolower($resp[0]['tag']);

	    if (SELF::STATUS[$method] != strtolower($status)) {
	    	$result['status'] = "fail";
	    }

	    if ($method == SELF::GET_INST && strtolower($result['status']) == 'success') {
    		$xml_obj = simplexml_load_string($xml);
    		$xml_arr = json_decode(json_encode($xml_obj), TRUE);
    		$institutions = [];
    		foreach ($xml_arr['Institution'] as $key => $value) {
    			$bankname = "'".explode(',', $value['name'])[0]."'";
    			$institutions[$key] = $bankname;
    		}
    		$result['institutions'] = $institutions;
    		return $result;
    	}

	    foreach ($resp as $key => $value) {
	    	if ($value['type'] == 'complete' && !empty($value['value'])) {
	    		$result[strtolower($value['tag'])] = $value['value'];
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

    private function _genSignature($payload){
       $hexval = bin2hex(hash('SHA1', $payload, TRUE));
       $signature = $this->_encrypt($hexval);
       return $signature;
    }

    private function _encrypt($payload){
     	openssl_private_encrypt($payload, $crypted, base64_decode(SELF::PRIVATE_KEY));
     	return bin2hex($crypted);
    }


    private function _pass_encrypt($password){
     	openssl_public_encrypt($password, $encrypted, base64_decode(SELF::PUBLIC_KEY));
     	return bin2hex($encrypted);
    }

    private function _decrypt($ciphertext){
        openssl_public_decrypt(hex2bin($ciphertext), $decrypted, base64_decode(SELF::PUBLIC_KEY));
        return $decrypted;
    }

}

 ?>