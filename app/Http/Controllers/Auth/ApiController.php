<?php 

namespace App\Http\Controllers\Auth;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

/**
 * 
 */
class ApiController
{

	protected $download_xlsx = true;
	
	function __construct(){
		
	}


	public function fsa_callback(Request $request){
		$response = array(
			'status' => 'fail',
			'message' => 'Request method not allowed',
		);
		$headers = getallheaders();
		if ($request->isMethod('post')) {
			$content_type = $headers['Content-Type'];
			if ($content_type != 'application/x-www-form-urlencoded') {
				$response['message'] =  'Content Type is not valid';
				return print(json_encode($response));
			}
    		$postdata = $request->all();

    		$perfiostransactionid = $postdata['perfiosTransactionId'];
    		$prolitustxnid = $postdata['clientTransactionId'];
    		$status = $postdata['status'];
    		$err_code = $postdata['errorCode'];
    		$err_msg = $postdata['errorMessage'];
    		if (strtolower($status) != 'completed') {
    			$response['message'] =  $err_msg ?? "Some error occured.";
    			return print(json_encode($response));
    		}
    		$perfios_data = FinanceModel::getPerfiosData($perfiostransactionid);
    		if (empty($perfios_data)) {
    			$response['message'] = "Perfios Transaction Id is not valid.";
    			return print(json_encode($response));
    		}
    		$appId = $perfios_data['app_id'];
    		$final = $this->_getFinanceReport($perfiostransactionid, $prolitustxnid, $appId);
    		if ($final['status'] != 'success') {
    			$response['message'] = $final['message'] ?? "Some error occured.";
    		}else{
    			$response['status'] = "success";
    			$response['message'] = "success";
    		}
    		return print(json_encode($response));
		}else{
			return print(json_encode($response));
		}
		
	}

	public function bsa_callback(Request $request){
		$response = array(
			'status' => 'fail',
			'message' => 'Request method not allowed',
		);
		$headers = getallheaders();
		if ($request->isMethod('post')) {
			$content_type = $headers['Content-Type'];
			if ($content_type != 'application/x-www-form-urlencoded') {
				$response['message'] =  'Content Type is not valid';
				return print(json_encode($response));
			}
    		$postdata = $request->all();

    		$perfiostransactionid = $postdata['perfiosTransactionId'];
    		$prolitustxnid = $postdata['clientTransactionId'];
    		$status = $postdata['status'];
    		$err_code = $postdata['errorCode'];
    		$err_msg = $postdata['errorMessage'];
    		if (strtolower($status) != 'completed') {
    			$response['message'] =  $err_msg ?? "Some error occured.";
    			return print(json_encode($response));
    		}

    		$perfios_data = FinanceModel::getPerfiosData($perfiostransactionid);
    		if (empty($perfios_data)) {
    			$response['message'] = "Perfios Transaction Id is not valid.";
    			return print(json_encode($response));
    		}
    		$appId = $perfios_data['app_id'];
    		$final = $this->_getBankReport($perfiostransactionid, $prolitustxnid, $appId);
    		if ($final['status'] != 'success') {
    			$response['message'] = $final['message'] ?? "Some error occured.";
    		}else{
    			$response['status'] = "success";
    			$response['message'] = "success";
    		}
    		return print(json_encode($response));
		}else{
			return print(json_encode($response));
		}
		
	}


	private function _getFinanceReport($perfiostransactionid, $prolitus_txn, $appId) {
        $biz_perfios_id = $perfiostransactionid;
    	$perfios = new Perfios_lib();
        $apiVersion = '2.1';
        $vendorId = 'capsave';
        $file_name = $appId.'_finance.xlsx';

        $req_arr = array(
              'apiVersion' => $apiVersion,
              'vendorId' => $vendorId,
              'perfiosTransactionId' => $perfiostransactionid,
              'reportType' => 'xlsx',
              'txnId' => $prolitus_txn,
        );
        if ($this->download_xlsx) {
          $final_res = $perfios->api_call(Perfios_lib::GET_STMT, $req_arr);
          if ($final_res['status'] != 'success') {
              $final_res['api_type'] = Perfios_lib::GET_STMT;
              $final_res['prolitusTransactionId'] = $prolitus_txn;
              $final_res['perfiosTransactionId'] = $perfiostransactionid;
              return $final_res;
          }else{
          	 $myfile = fopen(storage_path('app/public/user').'/'.$file_name, "w");
          	 \File::put(storage_path('app/public/user').'/'.$file_name, $final_res['result']);
          }
        }
        $req_arr['reportType'] = 'json';
        $final_res = $perfios->api_call(Perfios_lib::GET_STMT, $req_arr);
       	$final_res['api_type'] = Perfios_lib::GET_STMT;
	    $final_res['prolitusTransactionId'] = $prolitus_txn;
	    $final_res['perfiosTransactionId'] = $perfiostransactionid;
        if ($final_res['status'] == 'success') {
	        $final_res['result'] = base64_encode($final_res['result']);
	        $log_data = array(
	          'status' => $final_res['status'],
	          'updated_by' => Auth::user()->user_id,
	        );
	        FinanceModel::updatePerfios($log_data,'biz_perfios',$biz_perfios_id,'biz_perfios_id');
        }
        return $final_res;
    }


    private function _getBankReport($perfiostransactionid, $prolitus_txn, $appId) {
        $biz_perfios_id = $perfiostransactionid;
        $perfios = new Perfios_lib();
        $apiVersion = '2.1';
        $vendorId = 'capsave';
        $file_name = $appId.'_banking.xlsx';
        $req_arr = array(
            'perfiosTransactionId' => $perfiostransactionid,
            'types' => 'xlsx',
        );
        if ($this->download_xlsx) {
          $final_res = $bsa->api_call(Bsa_lib::GET_REP, $req_arr);
          if ($final_res['status'] != 'success') {
              $final_res['api_type'] = Bsa_lib::GET_REP;
              $final_res['prolitusTransactionId'] = $prolitus_txn;
              $final_res['perfiosTransactionId'] = $perfiostransactionid;
              return $final_res;
          }else{
          	$myfile = fopen(storage_path('app/public/user').'/'.$file_name, "w");
            \File::put(storage_path('app/public/user').'/'.$file_name, $final_res['result']);
          } 
        }
        $req_arr['types'] = 'json'
        $final_res = $bsa->api_call(Bsa_lib::GET_REP, $req_arr);
        $final_res['api_type'] = Bsa_lib::GET_REP;
        $final_res['prolitusTransactionId'] = $prolitus_txn;
        $final_res['perfiosTransactionId'] = $perfiostransactionid;
        if ($final_res['status'] == 'success') {
        	$final_res['result'] = base64_encode($final_res['result']);
	        $log_data = array(
	          'status' => $final_res['status'],
	          'updated_by' => Auth::user()->user_id,
	        );
	        FinanceModel::updatePerfios($log_data,'biz_perfios',$biz_perfios_id,'biz_perfios_id');
        }
        return $final_res;
    }
}


 ?>