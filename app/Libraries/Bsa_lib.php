<?php 
namespace App\Libraries;

use phpseclib\Crypt\RSA;
use Illuminate\Support\Facades\Config;
use Auth;
use App\Inv\Repositories\Models\FinanceModel;

define('FIXED', array('vendorId' => 'capsave','time' => date('Ymd\THis\Z')));
define('BSA_LIB_URL', config('proin.BSA_LIB_URL'));

class Bsa_lib{
	private $httpMethod = 'POST';
	const BASE_URL    =  BSA_LIB_URL;
	const PRIVATE_KEY = 'LS0tLS1CRUdJTiBSU0EgUFJJVkFURSBLRVktLS0tLQpNSUlKS2dJQkFBS0NBZ0VBaXQrMjUzTTRSZHRXeUFzN0ZuOUZqUWN6eVNXOVB1TnBmUU5udEdxdFpWVXpuNFJmCmtIMUVHdVgxOERCclFzT0NtY1gwbFJ2eG5QeDJMY3I1WHdFTjVpSzdwNlJPU2NQZ0pNczJZb2RLZVg0TW5sc3YKNHBZVlF3RjM3Z2JHSWlTcmxlTkR5T3MvTEpkT1FVU1dzSUhaMU11cHNOTitrS21Bc1ZseGw1TjdBYkZvbXpzdgpSbHhLZkhpeUVTbjRmVS9NMldob3RDZVpSMTJWMmFmV1Rhbi9sUTJoZEdGd2NPeC94RnExMjFET1Y3OUNFeU03CkJNZXp6MkRGeHVINFRYSTlWak9XN2FGVzNSZ0VSMGlPYTV5TVF6RWg3SWVLWTdwQjU5eGJzYnBkNWFlSkc0WloKM2gxUExEZ0lSaEZ2S01NaDFYY1RuaTcvSnpmRCtmWmtFMFI4TjFBVnZUTXV2YVJjTzNhVW1kcXJ4UStwWXZqcgo4b1J1V3htelI0ZVF0ekEvMnB6c1R2azcrdytadzY3Y2o0YTFGK280WFozV1FlK2YrL0YwZnlET3R6Q3c3RXRrClc1bkF5UkZwelVheUh2ZmhsejNWcGhFUkRWNWVOVG9RRFlWdXV6SldVVkM1ZFNMZnJXV1I4U3BaVmxKT3JML3IKNnZpK0lTbXBpUk5MTU9RYm4rZTZPVzNKcHROcEI4Tmo3Rmt5TnVBeWtiemtINFFsY3BlTm4yd1BtUjIzZVVOagpJSlM2T1Z1OE5OQmNPY2NQVEUyVk5qaVJmUlBMM3ZuWTVDUWs1RjJtMVJDR3RSbzYyUHFxYnlsYXBQZVpBcUlBCkxEcm9sUUgwNHpueE14TXFYU3BrQjNReCs1eEIwUmNFeC9Jc1FDQUUrTldMUXgxVUE2TTlzZkYrdDkwQ0F3RUEKQVFLQ0FnQlVpY0VXL2czWWdLQkFZSTNVc1I5T3JYNjV5UTU5OVFQblJTWmhMcnRsUDB1UXBJMWtNTTAvcnBOYgpacTQwMHoxeWpJM0tJMXVlbkJUNDVXR09rdDY2SkpTbGhhRGV5SzF1MklHLzB1bmtNWFpQQStXUGdwdG8wY0VICkZmcVJHRStYaStSY3I5QWlQZTAyVUNHSFMxU21kR0JWTXpvTnFCR2VaSHVBc1JlbFBURVhwaHl1TVVwcW9US00KdmtiMVNabkRFd3NCK0VVWHE0R29JTUtWUHBKR1V5amc1dHdDOGdVSks4c2V1ZjhTYld3MHFES3FRME5UVDI2MgpGaU1JU2kzS2h0Y3V5eE5NdmUwRTA4ZFcvTkpwcU1Razc1R3dDZGRLNU5xd3cybWJmclE1SEQwaXVDYlZIVHIwCkpITE5scFBIRlpqTXA0Rk5oMlgyaVNYOGtLa29RVFYrb1dsMDkyMXlSS0lHUWEzYU9BNlh3Sjl4L1IwSndoazkKQUtUMlc0U25Rc0lqS0VESVlQcEtvWDdPbXZueEhoeXJLOEJqVHZKNWcrZ1ltVTU1dkltMFUyS0g3WlBVNDY3QwpUZHdBNW8yaDkwMGtBSmx5S1h5Nms4aUJaTjlJNFNrYUJ1NnlsbmwvaURhbXVwd3JBeW1hMkxiZytvOFJZYklWCmdpVmlNbkdib1h3WVFSRjFiWnBNdGhYMlMvNlFrcU95ZEw4cE5vbXVHQXJxN2dlSVE2MDZmNy9FMEV2T3dQbTMKck53WnJhM0Z4TTVLWHlLTnhLNU95a1ZNQm50OUlPWE9BaHgxQU9VOXpMZVUyTlZMclVaczJKNzh0MXg5cTVnRwo0ZkZOcEthMFdZNUhyZEE5QTdUUURpL093QktkYTA0YS94WU1Ndmd1ZFhBZGVGS2dRUUtDQVFFQXZzRGxkUXlqClJLeUU3T2psUzBLaUZpQkxSR1g5SE1nUnhLZ2VIVUxpZ05LUmpweDRqVkVLZFp0UGFYZzIwenRTL0hZRHIxNi8KZVV4Q1lxemhRR3B0WUlMWmVlaVUvdHM4OTBHdEdNUk40U1p4SXplR2dTYzVOdkE1TVdHOTFuNWg4T2hCM01XTQphb0tHWGhXSlJDZjhONVljSk1uUG5XYVFZM3dEU0tTQTkyTkZreXFWZTdoMHJ3VzZkTjdjUGQrQkZ6cVkwaXNXCjM3NjJRcklWU29zOEpKcVZWSHliQnVSaEtzb1hRemxyR1AyVU9iQjFOWEplb1YwVWE3bmZUV0Z0RU5lOEk5N08KRGxVQlc1WEs4bnpYdm5BY3Q4bGZvR1FNMy8ycHAzSHV0WmhJSlE4VEFWMVRKSTZNQ2c1bDBxL2F0VHNPdnFoRApKUVBDN2xrSk9mM2dUUUtDQVFFQXVtQUtpZjdzWjN5U24wZmhFYis1NWtYNjVHUkVLV0ZkTEJXOGNGRWNHNnFKCkd6QWJXVW8reEVPS1dsQ0M2WmVCR2pRZ1pUaER5d3pQTDd4bk9tVFlxSktIczFhTXQ5QUF3Q3p6aFVlTksxbnoKRnlvclFWeW5QSTM0MlEzM1RUNlZuQXg0eDErZWJtY2NnZWxvdnBuOHlkdThTOVhyOEorTExIcUVXL0xMa2M2NgpXRTl2WmFGRkpGdUpHL2Fyc2p3OXQ5akY1TW9uZHRlbnZkbVRnWnMzMU1CNVRmMXV3U3dwMzJKNFh5S1NxSUMvCk1MWVRvZnZkaTZncTZjZGdNTWFXVEIzTFRPZlI2ZDRqT1pVODRaZVE1cnB6V3N4MUJqWFVOM2JPMWF5RHp5UGYKY0VJVE5ESkg2NHR2ZkRmemRYZ2pmYklnRDJ4MENhbUZhZ2w2d0FsOTBRS0NBUUVBbEpFaVpaYXhOdFJ6TngxeApNTHUyQ0N1ZzA2WG5qRm9hMUtMbnlYeUZjellOWGhocUlBNkZhZkhMMk9aak5RT1liNzd1d2RDMnFwK0ZlTnNUCkdSdUxFc2IyNE9jUDNLc2VnYWtxU3diaGJVR0dqcG81YlRBQ2ROS0dpUHFLWTV2TlpsZE9yTWREeE5UaUdEY0gKMFFpZmZSK3h6Y2xNcFZmemp6aFFTbXl0Y1lCaytPa0t3ZkI1R0xRS2MrbjdlVWt4ZnNrSnEyOGFBcXZEd3BCMgoycmMwNFlGd1d2d3R0aUY3dUZycjRWVEFJQmVvTVlKSTE1YTdNMHlPa2hTVFllNUdodjZ2cnZSaVluRThmSHRmCk5KdWRZTnBxMDQzSXArQWswdlA3QmNwTDFDM0Z5dTlCcXlkbmtTcGcwWmhESCtRTklHZmQ1UXFpT1JzRDRLV0IKOVNQUlpRS0NBUUVBcmNTK0RzdlFQdXkzQ0N0TWZlLzBzKzZyYmliT0pvYlJDRGw3Nkh5M2FGUW9ZV0VKSHNkSwpubE1hdS9vMUZQWmRPTUZCTXg2eWxxN3F2ZWM3bUFaT3UxMWppS0k1c1dnT1N4K2VMYlg0dVdLMGMvU3RQOVBUCldYSkZncHF5NlNKZys0M2xUbjZvaU9jNmZTWFNzMk4vZmZXU0ttTGFDUSs0QTFCMytBTDRLb3BFMC9HOE8xV1IKMGNSR29mdnJPZE5RK0FqMCtjeEIzMXhTMlgvek4vUXdxZnlMSFZ3ZDE1NEZySEZ6S2NCaVhSa3RLWTJaNEgyawpvb0c1QVI4VFlHYkgyMUFzTFJnM2I0WGQ4ZUpqVVRLdnQzQS8vWGlENDdac2x3bis3dHBhU3RkM2pJTU94S3ZjCndwRE84Vko1ZkJ6M2VrcUorZGQwbVUybFJxaHYrbnpmQVFLQ0FRRUFvUXJaRVFOOUtKUmVlMVhjSkp3alMrdHEKWDlyUmo5dlQvamowcUJ3YitIbzAyRG5LczFPZExFR2M4NjBQYW5UU0JuNzVEY05KZkcwVUxab2RSTVQyOHFBZwp1L1V4V01DSmZ1SFRkemJPS3hxSEhHVUs5bHNHa0Y3MDlaNjNZTzZrZWRTRTgvQThqTStjbVdXaStXa0FMdWwvCkltaVdRS1hpanF5RnhLeExQZkVTQXhuZ3ZFblBvZEFnSkRQSk1XdHNwU0MrR3VhOVRHTzhVSG84cFlxb1VWOUYKUWdjNTFWQkVPSThJaUl3enROdTlvOXVkc1kyaWUvMWFxTGJ0WVMwY0lyK1JGL1hqUXZob0R2M2llWS9LWFI4SgpCS2JwN0lScGlDZmpsazhsYllSVVpqQkEzL2ZqbGNyakd1RWoyTHFVNVo4NWxSUnFiYng0V1NmdWZGcDVDZz09Ci0tLS0tRU5EIFJTQSBQUklWQVRFIEtFWS0tLS0tCg==';
	const PUBLIC_KEY  = 'LS0tLS1CRUdJTiBQVUJMSUMgS0VZLS0tLS0KTUlJQ0lqQU5CZ2txaGtpRzl3MEJBUUVGQUFPQ0FnOEFNSUlDQ2dLQ0FnRUFpdCsyNTNNNFJkdFd5QXM3Rm45RgpqUWN6eVNXOVB1TnBmUU5udEdxdFpWVXpuNFJma0gxRUd1WDE4REJyUXNPQ21jWDBsUnZ4blB4MkxjcjVYd0VOCjVpSzdwNlJPU2NQZ0pNczJZb2RLZVg0TW5sc3Y0cFlWUXdGMzdnYkdJaVNybGVORHlPcy9MSmRPUVVTV3NJSFoKMU11cHNOTitrS21Bc1ZseGw1TjdBYkZvbXpzdlJseEtmSGl5RVNuNGZVL00yV2hvdENlWlIxMlYyYWZXVGFuLwpsUTJoZEdGd2NPeC94RnExMjFET1Y3OUNFeU03Qk1lenoyREZ4dUg0VFhJOVZqT1c3YUZXM1JnRVIwaU9hNXlNClF6RWg3SWVLWTdwQjU5eGJzYnBkNWFlSkc0WlozaDFQTERnSVJoRnZLTU1oMVhjVG5pNy9KemZEK2Zaa0UwUjgKTjFBVnZUTXV2YVJjTzNhVW1kcXJ4UStwWXZqcjhvUnVXeG16UjRlUXR6QS8ycHpzVHZrNyt3K1p3NjdjajRhMQpGK280WFozV1FlK2YrL0YwZnlET3R6Q3c3RXRrVzVuQXlSRnB6VWF5SHZmaGx6M1ZwaEVSRFY1ZU5Ub1FEWVZ1CnV6SldVVkM1ZFNMZnJXV1I4U3BaVmxKT3JML3I2dmkrSVNtcGlSTkxNT1FibitlNk9XM0pwdE5wQjhOajdGa3kKTnVBeWtiemtINFFsY3BlTm4yd1BtUjIzZVVOaklKUzZPVnU4Tk5CY09jY1BURTJWTmppUmZSUEwzdm5ZNUNRawo1RjJtMVJDR3RSbzYyUHFxYnlsYXBQZVpBcUlBTERyb2xRSDA0em54TXhNcVhTcGtCM1F4KzV4QjBSY0V4L0lzClFDQUUrTldMUXgxVUE2TTlzZkYrdDkwQ0F3RUFBUT09Ci0tLS0tRU5EIFBVQkxJQyBLRVktLS0tLQo=';
	
	const INIT_TXN    = '1001';    #Intiate the transaction
	const CNCL_TXN    = '1002';    #Cancel the transaction
	const UPL_FILE    = '1003';    #Upload the file
	const PRC_STMT    = '1004';    #Process the statement
	const REPRC_STMT  = '1005';    #Re-Process the statement
	const REP_GEN     = '1006';    #Generate the report
	const GET_REP     = '1007';    #Get the generated report


	const INIT_TXN_URL    = SELF::BASE_URL . 'organisations/'.FIXED['vendorId'].'/transactions';
	const CNCL_TXN_URL    = SELF::BASE_URL . 'organisations/'.FIXED['vendorId'].'/transactions/';
	const UPL_FILE_URL    = SELF::BASE_URL . 'organisations/'.FIXED['vendorId'].'/transactions/';
	const PRC_STMT_URL    = SELF::BASE_URL . 'organisations/'.FIXED['vendorId'].'/transactions/';
	const REPRC_STMT_URL  = SELF::BASE_URL . 'organisations/'.FIXED['vendorId'].'/transactions/';
	const REP_GEN_URL     = SELF::BASE_URL . 'organisations/'.FIXED['vendorId'].'/transactions/';
	const GET_REP_URL     = SELF::BASE_URL . 'organisations/'.FIXED['vendorId'].'/transactions/';

	const METHOD = array(
			SELF::INIT_TXN 	 => SELF::INIT_TXN_URL,
			SELF::CNCL_TXN 	 => SELF::CNCL_TXN_URL,
			SELF::UPL_FILE 	 => SELF::UPL_FILE_URL,
			SELF::PRC_STMT 	 => SELF::PRC_STMT_URL,
			SELF::REPRC_STMT => SELF::REPRC_STMT_URL,
			SELF::REP_GEN 	 => SELF::REP_GEN_URL,
			SELF::GET_REP 	 => SELF::GET_REP_URL,
	);


	const STATUS = array(
			SELF::INIT_TXN => 'transaction',
			SELF::CNCL_TXN => 'success',
			SELF::UPL_FILE => 'file',
			SELF::PRC_STMT => 'bankstatement',
			SELF::REPRC_STMT => 'needtocheck',
			SELF::REP_GEN => 'success',
			SELF::GET_REP => 'pir:data',
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
	     	$update_log['status'] = "success";
	     	FinanceModel::updatePerfios($update_log,'biz_perfios_log', $inserted_id);
	     	$resp['status'] = "success";
	     	$resp['message'] = "success";
		 	$resp['result'] = $response['result'];
		 	return $resp;
	     }

	      if ($method == SELF::GET_REP && 'json' == strtolower($params['reportType'])) {
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
    	$payload = [];
    	$httpMethod = "POST";
    	switch ($method) {
    		case SELF::CNCL_TXN:
    			$concat = $params['perfiosTransactionId'] . '?cancel';
    			break;
    		case SELF::UPL_FILE:
    			$concat = $params['perfiosTransactionId'] . '/files';
    			$filename =  $params['file_content'];
			 	$mimetype = mime_content_type($params['file_content']);
			 	$file['file'] = file_get_contents($params['file_content']);
			 	$cfile = curl_file_create($filename, $mimetype, basename($filename));
		     	$payload['file'] = $cfile;
    			break;
    		case SELF::PRC_STMT:
    			$concat = $params['perfiosTransactionId'] . '/bank-statements';
				$payload['fileId'] = $params['fileId'];
				if (!empty($params['institutionId']))
					$payload['institutionId'] = $params['institutionId'];
				if (!empty($params['password']))
					$payload['password'] = $params['password'];
    			break;
    		case SELF::REPRC_STMT:
    			$concat = $params['perfiosTransactionId'] . '/bank-statements/'.$params['fileId'];
				if (!empty($params['institutionId']))
					$payload['institutionId'] = $params['institutionId'];
				if (!empty($params['password']))
					$payload['password'] = $params['password'];
    			break;
    		case SELF::REP_GEN:
    			$concat = $params['perfiosTransactionId'] . '/reports';
    			break;
    		case SELF::GET_REP:
    			$httpMethod = "GET";
    			$concat = $params['perfiosTransactionId'] . '/reports?types='.$params['types'];
    			break;
    		default:
    			$concat='';
    			$payload['txnId'] = $params['txnId'];
    			$payload['loanAmount'] = $params['loanAmount'];
    			$payload['loanDuration'] = $params['loanDuration'];
    			$payload['loanType'] = $params['loanType'];
    			$payload['processingType'] = $params['processingType'];
    			$payload['transactionCompleteCallbackUrl'] = $params['transactionCompleteCallbackUrl'];
    			if (!empty($params['acceptancePolicy'])) {
    				$payload['acceptancePolicy'] = $params['acceptancePolicy'];
    			}
    			if (!empty($params['institutionId'])) {
    				$payload['institutionId'] = $params['institutionId'];
    			}
    			if (!empty($params['uploadingScannedStatements'])) {
    				$payload['uploadingScannedStatements'] = $params['uploadingScannedStatements'];
    			}
    			if (!empty($params['yearMonthFrom'])) {
    				$payload['yearMonthFrom'] = $params['yearMonthFrom'];
    			}
    			if (!empty($params['yearMonthTo'])) {
    				$payload['yearMonthTo'] = $params['yearMonthTo'];
    			}
    			break;
    	}
    	if ($method != SELF::UPL_FILE && !empty($payload)) {
    		$payload = $this->_genPayload($payload);
    	}
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