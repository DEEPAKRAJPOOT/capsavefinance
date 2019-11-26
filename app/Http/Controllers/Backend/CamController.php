<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Http\Requests\FinanceInformationRequest as FinanceRequest;
use Illuminate\Http\Request;
use App\Inv\Repositories\Models\FinanceModel;
date_default_timezone_set('Asia/Kolkata');
use App\Inv\Repositories\Models\Cam;
use App\Libraries\Perfios_lib;
use App\Libraries\Bsa_lib;
use Auth;
use Session;

class CamController extends Controller
{
     protected $appRepo;
	  public function __construct(){
        $this->middleware('auth');
       
        }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(){
        return view('backend.cam.overview');
    }

    public function camInformationSave(Request $request){
    	$arrCamData = $request->all();
        $arrCamData['biz_id'] = '12';
        $arrCamData['app_id'] = '12';
        $userId = Auth::user()->user_id;
        if(!isset($arrCamData['rating_no'])){
            $arrCamData['rating_no'] = NULL;
        }
        Cam::creates($arrCamData, $userId);
        Session::flash('message',trans('Cam Information Saved Successfully'));
        return redirect()->route('cam_overview');
    }

    public function finance(){
        return view('backend.cam.finance');

    }
    public function finance_store(FinanceRequest $request, FinanceModel $fin){
        $financeid = $this->_getFinanceId();
        $insert_data = [];
        $post_data = $request->all();
        unset($post_data['_token']);
        $curr = date('Y');
        foreach ($post_data as $key => $value) {
            if (is_array($value)) {
                foreach ($value as $k => $v) {
                   $insert_data[$curr- 2 + $k][$key]= $v;
                }
            }else{
                $insert_data[$curr-2][$key]= $value;
               $insert_data[$curr-1][$key]= $value;
               $insert_data[$curr][$key]= $value;
            }
        }
        $insert_data[$curr-2]['finance_id'] = $financeid;
        $insert_data[$curr-2]['period_ended'] = date($curr-2 . '-03-31');
        $insert_data[$curr-1]['finance_id'] = $financeid;
        $insert_data[$curr-1]['period_ended'] = date($curr-1 . '-03-31');
        $insert_data[$curr]['finance_id'] = $financeid;
        $insert_data[$curr]['period_ended'] = date($curr . '-03-31');
        
        foreach ($insert_data as  $ins_arr) {
            $fin->create($ins_arr);
        }
        Session::flash('message',trans('Record Inserted Successfully'));
        return redirect()->route('cam_finance');
    }


    private function _rand_str($length = 2){
       $rand_num = '';
       $permitted_chars = str_shuffle('0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ');
       $min_select_from = 36 - $length; 
       for ($i = 0; $i < $length ; $i++) { 
          $rand_num .= substr($permitted_chars, mt_rand(0, $min_select_from), 1);
       }
       return $rand_num;
    }

    private function _getFinanceId() {
        $rand_length = 4;
        $min_finance_id = $rand_length + 8;
        $temp =  $y = date('Y') - 2018;
        $append = '';
        $div = $temp / 26;
        if (is_int($div)) {
            $temp =  $temp - 26;
        }
        $fixed = $temp >= 26 ? floor($temp / 26) : 0;
        $y = $y % 26 == 0 ?  90 : ($y % 26) + 64;
        if ($fixed) {
           for ($i=0; $i < $fixed; $i++) { 
            $append .= 'Z';   
           }
        }
        $year = $append. chr($y);
        $m = date('m') + 64;
        $d = date('d');
        $d = (($d <= 25) ? ($d + 64) : ($d + 23));
        $h = date('H') + 65;
        $i = date('i');
        $s = date('s');
        $no = $year . chr($m) . chr($d) . chr($h). $i . $s. $this->_rand_str($rand_length);
        return $no;
    }

    private function _financeid_reverse($string = '') {
        $min_finance_id = 12;
        $strlen = strlen($string);
        $extra_year = ($strlen -  $min_finance_id) * 26;
        $value = substr($string, - $min_finance_id);
        $date = substr($value, 0, 4);
        $time = substr($value, 4, 4);
        list($y , $m, $d, $h) = str_split($date);
        $y = ord($y) + 2018 - 64 + $extra_year;
        $m = ord($m) - 64;
        $d = is_numeric($d) ? ord($d) - 23 : ord($d) - 64;
        $h = ord($h) - 65;
        $i = substr($time, 0, 2);
        $s = substr($time,-2);
        $datetime = "$y-$m-$d $h:$i:$s";
        return $datetime;
    }

    public function uploadBankStatement(){
        $bsa = new Bsa_lib();
        $filepath = public_path('storage/916010062301973.pdf');
        $reportType = 'xml';
        $prolitus_txn = date('YmdHis').mt_rand(1000,9999).mt_rand(1000,9999);
        $req_arr = array(
            'txnId' => $prolitus_txn, //'bharatSTmt',
            'loanAmount' => '20000000',
            'loanDuration' => '6',
            'loanType' => 'SME Loan',
            'processingType' => 'STATEMENT',
            'transactionCompleteCallbackUrl' => 'http://122.170.7.185:8080/CallbackTest/CallbackStatus',
            #optional params
            'acceptancePolicy' => 'atLeastOneTransactionInRange',
            'uploadingScannedStatements' => 'false',
         );
        $init_txn = $bsa->api_call(Bsa_lib::INIT_TXN, $req_arr);
        if ($init_txn['status'] == 'success') {
              $req_arr = array(
                'perfiosTransactionId' => $init_txn['perfiostransactionid'],
                'file_content' => $filepath,
                 );
              $upl_file = $bsa->api_call(Bsa_lib::UPL_FILE, $req_arr);
              if ($upl_file['status'] == 'success') {
                  $req_arr = array(
                    'perfiosTransactionId' => $init_txn['perfiostransactionid'],
                    'fileId' => $upl_file['fileid'],
                    'institutionId' => '',
                    'password' => '',
                  );
                  $proc_txn = $bsa->api_call(Bsa_lib::PRC_STMT, $req_arr);
                  if ($proc_txn['status'] == 'success') {
                     $req_arr = array(
                        'perfiosTransactionId' => $init_txn['perfiostransactionid'],
                     );
                     $rep_gen = $bsa->api_call(Bsa_lib::REP_GEN, $req_arr);
                     if ($rep_gen['status'] == 'success') {
                        $rep_gen['prolitusTransactionId'] = $prolitus_txn;
                        $rep_gen['perfiosTransactionId'] = $init_txn['perfiostransactionid'];
                        $final_res = $rep_gen;
                        $final_res['api_type'] = "Report Generation";
                     }else{
                        $final_res = $rep_gen;
                        $final_res['prolitusTransactionId'] = $prolitus_txn;
                        $final_res['perfiosTransactionId'] = $init_txn['perfiostransactionid'];
                        $final_res['api_type'] = "Report Generation";
                     }
                  }else{
                        $final_res = $proc_txn;
                        $final_res['prolitusTransactionId'] = $prolitus_txn;
                        $final_res['perfiosTransactionId'] = $init_txn['perfiostransactionid'];
                        $final_res['api_type'] = "Process Statement";
                  }
              }else{
                    $final_res = $upl_file;
                    $final_res['prolitusTransactionId'] = $prolitus_txn;
                    $final_res['perfiosTransactionId'] = $init_txn['perfiostransactionid'];
                    $final_res['api_type'] = "Upload File";
              }
        }else{
            $final_res = $init_txn;
            $final_res['api_type'] = "Initiate Txn";
        }

        dd($final_res);
        $req_arr = array(
            'perfiosTransactionId' => 'WFD81574748279324',//$final_res['perfiosTransactionId'],
            'types' => $reportType,
         );
        $get_rep = $bsa->api_call(Bsa_lib::GET_REP, $req_arr);
        dd($get_rep);
    }


    public function uploadFinancialStatement() {
        $perfios = new Perfios_lib();
        $reportType = 'xml';
        $prolitus_txn = date('YmdHis').mt_rand(1000,9999).mt_rand(1000,9999);
        $filepath = public_path('storage/Signed_financial_statement_18_19.pdf');
        $financial_year = 2018;
        $apiVersion = '2.1';
        $vendorId = 'capsave';

        $req_arr = array(
            'apiVersion' => $apiVersion,
            'vendorId' => $vendorId,
            'txnId' => $prolitus_txn,
            'institutionId' => '10996',
            'loanAmount' => '100000',
            'loanDuration' => '24',
            'loanType' => 'Home',
            'transactionCompleteCallbackUrl' => 'http://admin.rent.local/test2',
        );
        $start_txn = $perfios->api_call(Perfios_lib::STRT_TXN, $req_arr);
         if ($start_txn['status'] == 'success') {
            $req_arr = array(
                'apiVersion' => $apiVersion,
                'vendorId' => $vendorId,
                'perfiosTransactionId' => $start_txn['perfiostransactionid'],
                'financialYear' => $financial_year,
             );
             $add_year = $perfios->api_call(Perfios_lib::ADD_YEAR, $req_arr);
             if ($add_year['status'] == 'success') {
                 $req_arr = array(
                    'file_content' => $filepath,
                    'file_password' => '',
                    'perfiosTransactionId' => $start_txn['perfiostransactionid'],
                    'financialYear' => $financial_year,
                 );
                 $upl_stmt = $perfios->api_call(Perfios_lib::UPL_STMT, $req_arr);
                 if ($upl_stmt['status'] == 'success') {
                     $req_arr = array(
                        'apiVersion' => $apiVersion,
                        'vendorId' => $vendorId,
                        'perfiosTransactionId' => $start_txn['perfiostransactionid'],
                     );
                     $cmplt_txn = $perfios->api_call(Perfios_lib::CMPLT_TXN, $req_arr);
                     if ($cmplt_txn['status'] == 'success') {
                        $final_res = $cmplt_txn;
                        $final_res['prolitusTransactionId'] = $prolitus_txn;
                        $final_res['perfiosTransactionId'] = $start_txn['perfiostransactionid'];
                        $final_res['api_type'] = "Complete Transaction";
                     }else{
                        $final_res = $rep_gen;
                        $final_res['prolitusTransactionId'] = $prolitus_txn;
                        $final_res['perfiosTransactionId'] = $start_txn['perfiostransactionid'];
                        $final_res['api_type'] = "Report Generation";
                     }
                 }else{
                    $final_res = $upl_stmt;
                    $final_res['prolitusTransactionId'] = $prolitus_txn;
                    $final_res['perfiosTransactionId'] = $start_txn['perfiostransactionid'];
                    $final_res['api_type'] = "Upload Statement"; 
                 }
             }else{
                $final_res = $add_year;
                $final_res['prolitusTransactionId'] = $prolitus_txn;
                $final_res['perfiosTransactionId'] = $start_txn['perfiostransactionid'];
                $final_res['api_type'] = "Add Financial Year";
             }
         }else{
             $final_res = $start_txn;
             $final_res['api_type'] = "Start New Txn";
         }
         dd($final_res);
        $req_arr = array(
            'apiVersion' => $apiVersion,
            'vendorId' => $vendorId,
            'perfiosTransactionId' => 'PK261574667829233',//'2JGT1574749448671',$final_res['perfiosTransactionId'],
            'reportType' => $reportType,
            'txnId' => '2019112513134830813211',//'2019112611540788638030',//$final_res['prolitusTransactionId'],
         );
        $payload = $perfios->api_call(Perfios_lib::GET_STMT, $req_arr);
        dd($payload);
    }

}
