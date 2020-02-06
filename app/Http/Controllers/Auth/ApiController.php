<?php 

namespace App\Http\Controllers\Auth;
use App\Http\Controllers\Controller;
use Event;
use Illuminate\Http\Request;
use App\Inv\Repositories\Models\FinanceModel;
use App\Libraries\Bsa_lib;
use App\Libraries\Perfios_lib;
use Storage;

/**
 * 
 */
class ApiController
{

	//protected $secret_key = "Rentalpha__vkzARY";
  protected $secret_key = "0702f2c9c1414b70efc1e69f2ff31af0";
  protected $download_xlsx = true;
	
	function __construct(){
		
	}

  public function karza_webhook(Request $request){
    $allrequest = $request->all();
    \File::put(storage_path('app/public/user/gst_header.json'), json_encode(getallheaders()));
    \File::put(storage_path('app/public/user/gst_data.json'), json_encode($allrequest));

     $karzaMailArr['name'] = "Ravi Prakash";
     $karzaMailArr['email'] = 'ravi.awasthi93@gmail.com';
     $karzaMailArr['otp'] = base64_encode(json_encode($allrequest));
     Event::dispatch("user.sendotp", serialize($karzaMailArr));

    $response = array(
      'status' => 'failure',
      'message' => 'Request method not allowed',
    );

    $headers = getallheaders();
    if ($request->method() === 'POST') {
       $content_type = $headers['Content-Type'] ?? '';
       $secret_key = $headers['key'] ?? '';

       if ($content_type != 'application/json') {
         $response['message'] =  'Content Type is not valid.';
         return $this->_setResponse($response, 431);
       }

      if ($secret_key != $this->secret_key) {
         $response['message'] =  'Secret Key is not valid';
         return $this->_setResponse($response, 401);
       }
      $result = $request->all();
      if (empty($result)) {
        $response['message'] = "No data found. Server rejected the request";
         return $this->_setResponse($response, 411);
      }

      if (!empty($result['statusCode']) && $result['statusCode'] != '101') {
        $response['message'] = "We are getting statusCode with error.";
         return $this->_setResponse($response, 403);
      }

      if (!empty($result['status'])) {
        $response['message'] = $result['error'] ?? "Unable to get success response.";
        return $this->_setResponse($response, 406);
      }

      $request_id =    $result['requestId'] ?? '';
      $result =    $result['result'];

      if (empty($request_id)) {
        $response['message'] = "Insufficiant data to update the report.";
        return $this->_setResponse($response, 417);
      }

      $gst_data = FinanceModel::getGstData($request_id);
      if (empty($gst_data)) {
         $response['message'] = "Unable to get record against the requestId.";
         return $this->_setResponse($response, 422);
      }
      $app_id = $gst_data['app_id'];
      $gst_no = $gst_data['gstin'];
      $fname = $app_id.'_'.$gst_no;
      $this->logdata($result, 'F', $fname.'.json');
      $file_name = $fname.'.pdf';
      $myfile = fopen(storage_path('app/public/user').'/'.$file_name, "w");
      \File::put(storage_path('app/public/user').'/'.$file_name, file_get_contents($result['pdfDownloadLink'])); 
      $response['message'] =  'Response generated Successfully';
      $response['status'] =  'success';
      return $this->_setResponse($response, 200);
    }else{
       return $this->_setResponse($response, 405);
    }
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

  private function getToUploadPath($appId, $type = 'banking'){
      $touploadpath = storage_path('app/public/user/docs/'.$appId);
      if(!Storage::exists('public/user/docs/' .$appId)) {
          Storage::makeDirectory('public/user/docs/' .$appId.'/banking', 0777, true);
          Storage::makeDirectory('public/user/docs/' .$appId.'/finance', 0777, true);
          $touploadpath = storage_path('public/user/docs/' .$appId);
      }
      return $touploadpath .= ($type == 'banking' ? '/banking' : '/finance');
  }

  private function getLatestFileName($appId, $fileType='banking', $extType='json'){
      $scanpath = $this->getToUploadPath($appId, $fileType);
      if (is_dir($scanpath) == false) {
        $files = [];
      }else{
        $files = scandir($scanpath, SCANDIR_SORT_DESCENDING);
      }
      $files = array_diff($files, [".", ".."]);
      $filename = "";
      if (!empty($files)) {
        foreach ($files as $key => $file) {
          $fileparts = pathinfo($file);
          $filename = $fileparts['filename'];
          $ext = $fileparts['extension'];
          if ($extType == $ext) {
             break;
          }
        }
      }
      $included_no = preg_replace('#[^0-9]+#', '', $filename);
      $file_no = str_replace($appId, '', $included_no);
      if (empty($file_no) && empty($filename)) {
        $new_file = $appId.'_'.$fileType.".$extType";
        $curr_file = '';
      }else{
        $file_no = (int)$file_no + 1;
        $curr_file = $filename.".$extType";
        $new_file = $appId.'_'.$fileType.'_'.$file_no . ".$extType";
      }
      $fileArr = array(
        'curr_file' => $curr_file,
        'new_file' => $new_file,
      );
      return $fileArr;
    }


	private function _getFinanceReport($perfiostransactionid, $prolitus_txn, $appId) {
        $biz_perfios_id = $perfiostransactionid;
    	  $perfios = new Perfios_lib();
        $apiVersion = '2.1';
        $vendorId = 'capsave';
        $nameArr = $this->getLatestFileName($appId, 'finance', 'xlsx');
        $file_name = $nameArr['new_file'];
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
          	 $myfile = fopen($this->getToUploadPath($appId, 'finance').'/'.$file_name, "w");
          	 \File::put($this->getToUploadPath($appId, 'finance').'/'.$file_name, $final_res['result']);
          }
        }
        $file= url("storage/user/docs/$appId/finance/". $file_name);
        $req_arr['reportType'] = 'json';
        $final_res = $perfios->api_call(Perfios_lib::GET_STMT, $req_arr);
       	$final_res['api_type'] = Perfios_lib::GET_STMT;
        $final_res['file_url'] = $file;
	      $final_res['prolitusTransactionId'] = $prolitus_txn;
	      $final_res['perfiosTransactionId'] = $perfiostransactionid;
        if ($final_res['status'] == 'success') {
          $final_res['result'] = base64_encode($final_res['result']);
          $nameArr = $this->getLatestFileName($appId, 'finance', 'json');
          $json_file_name = $nameArr['new_file'];
          $myfile = fopen($this->getToUploadPath($appId, 'finance') .'/'.$json_file_name, "w");
          \File::put($this->getToUploadPath($appId, 'finance') .'/'.$json_file_name, $final_res['result']);
          $log_data = array(
            'status' => $final_res['status'],
            'updated_by' => '999999',
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
        $nameArr = $this->getLatestFileName($appId, 'banking', 'xlsx');
        $file_name = $nameArr['new_file'];
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
          	$myfile = fopen($this->getToUploadPath($appId, 'banking').'/'.$file_name, "w");
            \File::put($this->getToUploadPath($appId, 'banking').'/'.$file_name, $final_res['result']);
          } 
        }
        $req_arr['types'] = 'json';
        $final_res = $bsa->api_call(Bsa_lib::GET_REP, $req_arr);
        $final_res['api_type'] = Bsa_lib::GET_REP;
        $final_res['file_url'] = $file;
        $final_res['prolitusTransactionId'] = $prolitus_txn;
        $final_res['perfiosTransactionId'] = $perfiostransactionid;
        if ($final_res['status'] == 'success') {

          $final_res['result'] = base64_encode($final_res['result']);
          $nameArr = $this->getLatestFileName($appId, 'banking', 'json');
          $json_file_name = $nameArr['new_file'];
          $myfile = fopen($this->getToUploadPath($appId, 'banking') .'/'.$json_file_name, "w");
          \File::put($this->getToUploadPath($appId, 'banking') .'/'.$json_file_name, $final_res['result']);
          $log_data = array(
            'status' => $final_res['status'],
            'updated_by' => '999999',
          );
          FinanceModel::updatePerfios($log_data,'biz_perfios',$biz_perfios_id,'biz_perfios_id');
        }
        return $final_res;
    }

    private function _setResponse($response, $statusCode){
      $result = $response;
      $result['status_code'] = $statusCode;
      $logdata = json_encode($result);
      $this->logdata($logdata, 'F', 'error.log');
      return response($response, $statusCode)
                  ->header('Content-Type', 'application/json');
    }


    public function logdata($data, $w_mode = 'D', $w_filename = '', $w_folder = '') {
      list($year, $month, $date, $hour) = explode('-', strtolower(date('Y-M-dmy-H')));
      $main_dir = storage_path('app/public/user/');
     /*$year_dir = $main_dir . "$year/";
      $month_dir = $year_dir . "$month/";
      $date_dir = $month_dir . "$date/";
      $hour_dir = $date_dir . "$hour/";

      if (!file_exists($year_dir)) {
        mkdir($year_dir, 0777, true);
      }
      if (!file_exists($month_dir)) {
        mkdir($month_dir, 0777, true);
      }
      if (!file_exists($date_dir)) {
        mkdir($date_dir, 0777, true);
      }
      if (!file_exists($hour_dir)) {
        mkdir($hour_dir, 0777, true);
      }*/
      $hour_dir = $main_dir;
      $data = is_array($data) || is_object($data) ? json_encode($data) : $data;
      $data = base64_encode($data);
      if (strtolower($w_mode) == 'f') {
        $final_dir = $hour_dir;
        $filepath = explode('/', $w_folder);
        foreach ($filepath as $value) {
          $final_dir .= "$value/";
          if (!file_exists($final_dir)) {
            mkdir($final_dir, 0777, true);
          }
        }
        $my_file = $final_dir . $w_filename;
        $handle = fopen($my_file, 'w');
        return fwrite($handle, PHP_EOL . $data . PHP_EOL);
      } else {
        $my_file = $hour_dir . date('ymd') . '.log';
        $handle = fopen($my_file, 'a');
        $time = date('H:i:s');
        fwrite($handle, PHP_EOL . 'Log ' . $time);
        return fwrite($handle, PHP_EOL . $data . PHP_EOL);
      }
      return FALSE;
  }
}


 ?>