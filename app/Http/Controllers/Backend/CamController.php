<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Http\Requests\FinanceInformationRequest as FinanceRequest;
use Illuminate\Http\Request;
use App\Inv\Repositories\Models\FinanceModel;
use App\Inv\Repositories\Models\Business;
use App\Inv\Repositories\Models\BizOwner;
use App\Inv\Repositories\Models\Cam;
use App\Libraries\Perfios_lib;
use App\Libraries\Bsa_lib;
use App\Libraries\MobileAuth_lib;
use App\Inv\Repositories\Contracts\UserInterface as InvUserRepoInterface;
use App\Inv\Repositories\Contracts\ApplicationInterface as InvAppRepoInterface;
use App\Inv\Repositories\Contracts\DocumentInterface as InvDocumentRepoInterface;
use Auth;
use Session;
date_default_timezone_set('Asia/Kolkata');
use Helpers;

class CamController extends Controller
{
    protected $download_xlsx = TRUE;
    protected $appRepo;
    protected $userRepo;
    protected $docRepo;
    public function __construct(InvAppRepoInterface $app_repo, InvUserRepoInterface $user_repo, InvDocumentRepoInterface $doc_repo){
        $this->appRepo = $app_repo;
        $this->userRepo = $user_repo;
        $this->docRepo = $doc_repo;
        $this->middleware('auth');
        $this->middleware('checkBackendLeadAccess');
    }
    
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request){
        $arrRequest['biz_id'] = $request->get('biz_id');
        $arrRequest['app_id'] = $request->get('app_id');
        $arrBizData = Business::getApplicationById($arrRequest['biz_id']);
        $arrOwnerData = BizOwner::getCompanyOwnerByBizId($arrRequest['biz_id']);
       if(isset($arrOwnerData[0])){
              $arrBizData['ownerName'] = $arrOwnerData[0]['first_name'].' '.$arrOwnerData[0]['last_name'];
       }
        $arrEntityData = Business::getEntityByBizId($arrRequest['biz_id']);
        if(isset($arrEntityData['entity_name'])){
              $arrBizData['entityName'] = $arrEntityData['entity_name'];
        }
        if(isset($arrEntityData['name'])){      
              $arrBizData['legalConstitution'] = $arrEntityData['name'];
        }
			$arrBizData['email']  = $arrEntityData['email'];
			$arrBizData['mobile_no']  = $arrEntityData['mobile_no'];
        $arrCamData = Cam::where('biz_id','=',$arrRequest['biz_id'])->where('app_id','=',$arrRequest['app_id'])->first();
        return view('backend.cam.overview')->with(['arrCamData' =>$arrCamData ,'arrRequest' =>$arrRequest, 'arrBizData' => $arrBizData]);

    }

    public function camInformationSave(Request $request){
    	  $arrCamData = $request->all();
        $userId = Auth::user()->user_id;
        if(!isset($arrCamData['rating_no'])){
                $arrCamData['rating_no'] = NULL;
        }
        if($arrCamData['cam_report_id'] != ''){
             $updateCamData = Cam::updateCamData($arrCamData, $userId);
             if($updateCamData){
                    Session::flash('message',trans('Cam Information Updated Successfully'));
             }else{
                   Session::flash('message',trans('Cam Information Not Updated Successfully'));
             }
        }else{
            $saveCamData = Cam::creates($arrCamData, $userId);
            if($saveCamData){
                    Session::flash('message',trans('Cam Information Saved Successfully'));
             }else{
                   Session::flash('message',trans('Cam Information Not Saved Successfully'));
             }
        }    
        return redirect()->route('cam_overview', ['app_id' => request()->get('app_id'), 'biz_id' => request()->get('biz_id')]);
    }

    public function showCibilForm(Request $request){
        $biz_id = $request->get('biz_id');
        $arrCompanyDetail = Business::getCompanyDataByBizId($biz_id);
        $arrCompanyOwnersData = BizOwner::getCompanyOwnerByBizId($biz_id);
        return view('backend.cam.cibil', compact('arrCompanyDetail', 'arrCompanyOwnersData'));
    }

    public function finance(Request $request, FinanceModel $fin){
    	  $appId = $request->get('app_id');
        $pending_rec = $fin->getPendingFinanceStatement($appId);
        $financedocs = $fin->getFinanceStatements($appId);
        return view('backend.cam.finance', ['financedocs' => $financedocs, 'appId'=> $appId, 'pending_rec'=> $pending_rec]);

    }

    public function banking(Request $request, FinanceModel $fin){
        $appId = $request->get('app_id');
        $pending_rec = $fin->getPendingBankStatement($appId);        
        $bankdocs = $fin->getBankStatements($appId);
        return view('backend.cam.bank', ['bankdocs' => $bankdocs, 'appId'=> $appId, 'pending_rec'=> $pending_rec]);

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

    public function analyse_bank(Request $request) {
      $post_data = $request->all();
      $appId = $request->get('appId');
      $filepath = $this->getBankFilePath($appId);
      $response = $this->_callBankApi($filepath, $appId);
      $response['result'] = base64_encode($response['result'] ?? '');
      if (empty($response['perfiosTransactionId']) && empty($response['perfiostransactionid'])) {
         $response['perfiosTransactionId'] = NULL;
      }else{
          $response['perfiosTransactionId'] = $response['perfiosTransactionId'] ?? $response['perfiostransactionid'];
      }
      $log_data = array(
        'app_id' =>  $appId,
        'status' => $response['status'],
        'type' => '1',
        'api_name' => $response['api_type'] ?? NULL,
        'prolitus_txn_id' => $response['prolitusTransactionId'] ?? NULL,
        'perfios_log_id' => $response['perfiosTransactionId'],
        'created_by' => Auth::user()->user_id,
      );
      FinanceModel::insertPerfios($log_data);
      if ($response['status'] == 'success') {
        return response()->json(['message' =>'Bank Statement analysed successfully.','status' => 1,
          'value' => $response]);
      }else{
        return response()->json(['message' =>$response['message'] ?? 'Something went wrong','status' => 0,'value'=>['file_url'=>'']]);
      }
    }

    public function analyse_finance(Request $request) {
      $post_data = $request->all();
      $appId = $request->get('appId');
      $filepath = $this->getFinanceFilePath($appId);
      $response = $this->_callFinanceApi($filepath, $appId);
      $response['result'] = base64_encode($response['result'] ?? '');
      if (empty($response['perfiosTransactionId']) && empty($response['perfiostransactionid'])) {
         $response['perfiosTransactionId'] = NULL;
      }else{
          $response['perfiosTransactionId'] = $response['perfiosTransactionId'] ?? $response['perfiostransactionid'];
      }

      $log_data = array(
        'app_id' =>  $appId,
        'status' => $response['status'],
        'type' => '2',
        'api_name' => $response['api_type'] ?? NULL,
        'prolitus_txn_id' => $response['prolitusTransactionId'] ?? NULL,
        'perfios_log_id' => $response['perfiosTransactionId'],
        'created_by' => Auth::user()->user_id,
      );
      FinanceModel::insertPerfios($log_data);
      if ($response['status'] == 'success') {
        return response()->json(['message' =>'Financial Statement analysed successfully.','status' => 1,
          'value' => $response]);
      }else{
        return response()->json(['message' =>$response['message'] ?? 'Something went wrong','status' => 0,'value'=>['file_url'=>'']]);
      }
    }


    public function getFinanceFilePath($appId){
    	$fin = new FinanceModel();
        $financedocs = $fin->getFinanceStatements($appId);
        $files = [];
        foreach ($financedocs as $doc) {
          $files[] = array(
            'app_id' => $doc->app_id,
            'file_id' => $doc->file_id,
            'fin_year' => $doc->finc_year,
            'file_path' => public_path('storage/'.$doc->file_path),
            'is_scanned' => $doc->is_scanned == 1 ? 'true' : 'false',
            'file_password' => $doc->pwd_txt ?? NULL,
          );
        }
        return $files;
    }

    private function getBankFilePath($appId) {
        $fin = new FinanceModel();
        $bankdocs = $fin->getBankStatements($appId);
        $files = [];
        foreach ($bankdocs as $doc) {
           $files[] = array(
            'app_id' => $doc->app_id,
            'file_id' => $doc->file_id,
            'fin_year' => $doc->finc_year,
            'file_path' => public_path('storage/'.$doc->file_path),
            'is_scanned' => $doc->is_scanned == 1 ? 'true' : 'false',
            'file_password' => $doc->pwd_txt ?? NULL,
          );
        }
        return $files;
    }

    private function _callBankApi($filespath, $appId){
    	$user = FinanceModel::getUserByAPP($appId);
    	$loanAmount = (int)$user['loan_amt'];
        $bsa = new Bsa_lib();
        $reportType = 'xml';
        $prolitus_txn = date('YmdHis').mt_rand(1000,9999).mt_rand(1000,9999);
        $process_txn_cnt = 0;
        $req_arr = array(
            'txnId' => $prolitus_txn, //'bharatSTmt',
            'loanAmount' => $loanAmount,
            'loanDuration' => '6',
            'loanType' => 'SME Loan',
            'processingType' => 'STATEMENT',
            'transactionCompleteCallbackUrl' => 'http://122.170.7.185:8080/CallbackTest/CallbackStatus',
         );
        $init_txn = $bsa->api_call(Bsa_lib::INIT_TXN, $req_arr);
        if ($init_txn['status'] == 'success') {
          foreach ($filespath as $file_doc) {
             $filepath = $file_doc['file_path'];
             $password = $file_doc['file_password'];
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
                    'password' => $password,
                  );
                  $proc_txn = $bsa->api_call(Bsa_lib::PRC_STMT, $req_arr);
                  if ($proc_txn['status'] == 'success') {
                      $process_txn_cnt++;
                  }
              }
          }
          if ($process_txn_cnt == count($filespath)) {
             $req_arr = array(
                'perfiosTransactionId' => $init_txn['perfiostransactionid'],
             );
             $rep_gen = $bsa->api_call(Bsa_lib::REP_GEN, $req_arr);
             if ($rep_gen['status'] == 'success') {
                $rep_gen['prolitusTransactionId'] = $prolitus_txn;
                $rep_gen['perfiosTransactionId'] = $init_txn['perfiostransactionid'];
                $final_res = $rep_gen;
                $final_res['api_type'] = Bsa_lib::REP_GEN;
             }else{
                $final_res = $rep_gen;
                $final_res['prolitusTransactionId'] = $prolitus_txn;
                $final_res['perfiosTransactionId'] = $init_txn['perfiostransactionid'];
                $final_res['api_type'] = Bsa_lib::REP_GEN;
             }
          }else{
                $final_res = $proc_txn;
                $final_res['prolitusTransactionId'] = $prolitus_txn;
                $final_res['perfiosTransactionId'] = $init_txn['perfiostransactionid'];
                $final_res['api_type'] = Bsa_lib::PRC_STMT;
          } 
        }else{
            $final_res = $init_txn;
            $final_res['api_type'] = Bsa_lib::INIT_TXN;
        }
        if ($final_res['status'] != 'success') {
            return $final_res;
        }

        if (!empty($is_scanned) && strtolower($is_scanned) == 'yes') {
           $final_res['api_type'] = Bsa_lib::REP_GEN;
        	 return $final_res;
        }

        $file_name = $appId.'_banking.xlsx';
        if ($this->download_xlsx) {
          $req_arr = array(
            'perfiosTransactionId' => $init_txn['perfiostransactionid'],
            'types' => 'xlsx',
          );
          $final_res = $bsa->api_call(Bsa_lib::GET_REP, $req_arr);
          if ($final_res['status'] != 'success') {
              $final_res['status']  = ($final_res['status'] == 'success');
              $final_res['api_type'] = Bsa_lib::GET_REP;
              $final_res['prolitusTransactionId'] = $prolitus_txn;
              $final_res['perfiosTransactionId'] = $init_txn['perfiostransactionid'];
              return $final_res;
          }
          $myfile = fopen(storage_path('app/public/user').'/'.$file_name, "w");
          \File::put(storage_path('app/public/user').'/'.$file_name, $final_res['result']); 
        }
        $file= url('storage/user/'. $file_name);
        /*$req_arr = array(
            'perfiosTransactionId' => $final_res['perfiosTransactionId'],
            'types' => $reportType,
        );
        $final_res = $bsa->api_call(Bsa_lib::GET_REP, $req_arr);*/
        $final_res['api_type'] = Bsa_lib::GET_REP;
        $final_res['file_url'] = $file;
        $final_res['prolitusTransactionId'] = $prolitus_txn;
        $final_res['perfiosTransactionId'] = $init_txn['perfiostransactionid'];
        return $final_res;
    }

    public function _callFinanceApi($filespath, $appId) {
    	$user = FinanceModel::getUserByAPP($appId);
    	$loanAmount = (int)$user['loan_amt'];
        $perfios = new Perfios_lib();
        $reportType = 'xml';
        $prolitus_txn = date('YmdHis').mt_rand(1000,9999).mt_rand(1000,9999);
        $process_txn_cnt = 0;
        $apiVersion = '2.1';
        $vendorId = 'capsave';

        $req_arr = array(
            'apiVersion' => $apiVersion,
            'vendorId' => $vendorId,
            'txnId' => $prolitus_txn,
            'institutionId' => '10996',
            'loanAmount' => $loanAmount,
            'loanDuration' => '24',
            'loanType' => 'Home',
            'transactionCompleteCallbackUrl' => 'http://admin.rent.local/test2',
        );
        $start_txn = $perfios->api_call(Perfios_lib::STRT_TXN, $req_arr);
         if ($start_txn['status'] == 'success') {
         	foreach ($filespath as $file_doc) {
            $financial_year = $file_doc['fin_year'];
            $filepath = $file_doc['file_path'];
            $file_password = $file_doc['file_password'];
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
                    'file_password' => $file_password,
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
                          $process_txn_cnt++;
                      }
                  }
             	}else{
                $upl_stmt = $add_year;
                $upl_stmt['prolitusTransactionId'] = $prolitus_txn;
                $upl_stmt['perfiosTransactionId'] = $start_txn['perfiostransactionid'];
                $upl_stmt['api_type'] = Perfios_lib::ADD_YEAR;
              }
          }
          if ($process_txn_cnt == count($filespath)) {
            $final_res = $cmplt_txn;
            $final_res['prolitusTransactionId'] = $prolitus_txn;
            $final_res['perfiosTransactionId'] = $start_txn['perfiostransactionid'];
            $final_res['api_type'] = Perfios_lib::CMPLT_TXN;
          }else{
            $final_res = $cmplt_txn ?? $upl_stmt;
            $final_res['prolitusTransactionId'] = $prolitus_txn;
            $final_res['perfiosTransactionId'] = $start_txn['perfiostransactionid'];
            $final_res['api_type'] = Perfios_lib::CMPLT_TXN;
          }
         }else{
             $final_res = $start_txn;
             $final_res['api_type'] = Perfios_lib::STRT_TXN;
         }


        if ($final_res['status'] != 'success') {
            return $final_res;
        }

        if (!empty($is_scanned) && strtolower($is_scanned) == 'yes') {
          $final_res['api_type'] = Perfios_lib::CMPLT_TXN;
        	 return $final_res;
        }

        $file_name = $appId.'_finance.xlsx';
        if ($this->download_xlsx) {
	         $req_arr = array(
	            'apiVersion' => $apiVersion,
	            'vendorId' => $vendorId,
	            'perfiosTransactionId' => $start_txn['perfiostransactionid'],
	            'reportType' => 'xlsx',
	            'txnId' => $prolitus_txn,
	         );
          $final_res = $perfios->api_call(Perfios_lib::GET_STMT, $req_arr);
          if ($final_res['status'] != 'success') {
              $final_res['status']  = ($final_res['status'] == 'success');
              $final_res['api_type'] = Perfios_lib::GET_STMT;
              $final_res['prolitusTransactionId'] = $prolitus_txn;
              $final_res['perfiosTransactionId'] = $start_txn['perfiostransactionid'];
              return $final_res;
          }
	        $myfile = fopen(storage_path('app/public/user').'/'.$file_name, "w");
	        \File::put(storage_path('app/public/user').'/'.$file_name, $final_res['result']);
        }
        $file= url('storage/user/'. $file_name);


        /*$req_arr = array(
            'apiVersion' => $apiVersion,
            'vendorId' => $vendorId,
            'perfiosTransactionId' => $start_txn['perfiosTransactionId'],
            'reportType' => $reportType,
            'txnId' => $prolitus_txn,
         );
        $final_res = $perfios->api_call(Perfios_lib::GET_STMT, $req_arr);*/
        $final_res['api_type'] = Perfios_lib::GET_STMT;
        $final_res['file_url'] = $file;
        $final_res['prolitusTransactionId'] = $prolitus_txn;
        $final_res['perfiosTransactionId'] = $start_txn['perfiostransactionid'];
        return $final_res;
    }

    public function getFinanceReport(Request $request) {
        $alldata = $request->all();
        $appId = $alldata['appId'];
        $biz_perfios_id = $alldata['biz_perfios_id'];
        $perfios_data = FinanceModel::getPerfiosData($biz_perfios_id);
        if ($perfios_data['app_id'] != $appId) {
          return response()->json(['message' => 'This application is not belonging to you.','status' => 0,'value'=>['file_url'=>'']]);
        }

        $perfiostransactionid = $perfios_data['perfios_log_id'];
        $prolitus_txn = $perfios_data['prolitus_txn_id'];

    	  $perfios = new Perfios_lib();
        $apiVersion = '2.1';
        $vendorId = 'capsave';
        $file_name = $appId.'_finance.xlsx';
        if ($this->download_xlsx) {
           $req_arr = array(
              'apiVersion' => $apiVersion,
              'vendorId' => $vendorId,
              'perfiosTransactionId' => $perfiostransactionid,
              'reportType' => 'xlsx',
              'txnId' => $prolitus_txn,
           );
          $final_res = $perfios->api_call(Perfios_lib::GET_STMT, $req_arr);
          if ($final_res['status'] != 'success') {
              $final_res['status']  = ($final_res['status'] == 'success');
              $final_res['api_type'] = Perfios_lib::GET_STMT;
              $final_res['prolitusTransactionId'] = $prolitus_txn;
              $final_res['perfiosTransactionId'] = $perfiostransactionid;
              return $final_res;
          }
          $myfile = fopen(storage_path('app/public/user').'/'.$file_name, "w");
          \File::put(storage_path('app/public/user').'/'.$file_name, $final_res['result']);
        }
        $file= url('storage/user/'. $file_name);
        $final_res['api_type'] = Perfios_lib::GET_STMT;
        $final_res['file_url'] = $file;
        $final_res['prolitusTransactionId'] = $prolitus_txn;
        $final_res['perfiosTransactionId'] = $perfiostransactionid;
        $final_res['result'] = base64_encode($final_res['result']);
        $log_data = array(
          'status' => $final_res['status'],
          'updated_by' => Auth::user()->user_id,
        );
        FinanceModel::updatePerfios($log_data,'biz_perfios',$biz_perfios_id,'biz_perfios_id');
        if ($final_res['status'] == 'success') {
          return response()->json(['message' =>'Financial Statement analysed successfully.','status' => 1,
            'value' => $final_res]);
        }else{
          return response()->json(['message' => $final_res['message'] ?? 'Something went wrong','status' => 0,'value'=>['file_url'=>'']]);
        }
    }


    public function getBankReport(Request $request) {
        $alldata = $request->all();
        $appId = $alldata['appId'];
        $biz_perfios_id = $alldata['biz_perfios_id'];
        $perfios_data = FinanceModel::getPerfiosData($biz_perfios_id);
        if ($perfios_data['app_id'] != $appId) {
          return response()->json(['message' => 'This application is not belonging to you.','status' => 0,'value'=>['file_url'=>'']]);
        }

        $perfiostransactionid = $perfios_data['perfios_log_id'];
        $prolitus_txn = $perfios_data['prolitus_txn_id'];

        $perfios = new Perfios_lib();
        $apiVersion = '2.1';
        $vendorId = 'capsave';
        $file_name = $appId.'_banking.xlsx';
        if ($this->download_xlsx) {
          $req_arr = array(
            'perfiosTransactionId' => $perfiostransactionid,
            'types' => 'xlsx',
          );
          $final_res = $bsa->api_call(Bsa_lib::GET_REP, $req_arr);
          if ($final_res['status'] != 'success') {
              $final_res['status']  = ($final_res['status'] == 'success');
              $final_res['api_type'] = Bsa_lib::GET_REP;
              $final_res['prolitusTransactionId'] = $prolitus_txn;
              $final_res['perfiosTransactionId'] = $init_txn['perfiostransactionid'];
              return $final_res;
          }
          $myfile = fopen(storage_path('app/public/user').'/'.$file_name, "w");
          \File::put(storage_path('app/public/user').'/'.$file_name, $final_res['result']);
        }
        $file= url('storage/user/'. $file_name);

        $final_res['api_type'] = Bsa_lib::GET_REP;
        $final_res['file_url'] = $file;
        $final_res['prolitusTransactionId'] = $prolitus_txn;
        $final_res['perfiosTransactionId'] = $perfiostransactionid;
        $final_res['result'] = base64_encode($final_res['result']);
        $log_data = array(
          'status' => $final_res['status'],
          'updated_by' => Auth::user()->user_id,
        );
        FinanceModel::updatePerfios($log_data,'biz_perfios',$biz_perfios_id,'biz_perfios_id');
        if ($final_res['status'] == 'success') {
          return response()->json(['message' =>'Banking Statement analysed successfully.','status' => 1,
            'value' => $final_res]);
        }else{
          return response()->json(['message' => $final_res['message'] ?? 'Something went wrong','status' => 0,'value'=>['file_url'=>'']]);
        }
    }


    private function _rand_str($length = 2){
       $random_string = '';
       $permitted_chars = str_shuffle('0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ');
       $input_length = strlen($permitted_chars); 
       for($i = 0; $i < $length; $i++) {
          $random_character = $permitted_chars[mt_rand(0, $input_length - 1)];
          $random_string .= $random_character;
       }
       return $random_string;
    }

    private function _getFinanceId() {
        $rand_length = 3;
        $min_finance_id = $rand_length + 8;
        $temp =  $y = date('Y') - 2018;
        $append = '';
        $div = $temp / 26;
        if (is_int($div)) {
            $temp =  $temp - 26;
        }
        $fixed = $temp >= 26 ? floor($temp / 26) : 0;
        $y = $y % 26 == 0 ?  90 : ($y % 26) + 64;
        $year = $fixed. chr($y);
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
        $min_finance_id = 11;
        $strlen = strlen($string);
        $extra_year = substr($string, 0, 1) * 26;
        $value = substr($string, - $min_finance_id);
        $date = substr($value, 0, 4);
        $time = substr($value, 4, 4);
        $random = substr($value, 8);
        list($y , $m, $d, $h) = str_split($date);
        $y = ord($y) + 2018 - 64 + $extra_year;
        $m = ord($m) - 64;
        $d = is_numeric($d) ? ord($d) - 23 : ord($d) - 64;
        $h = ord($h) - 65;
        $i = substr($time, 0, 2);
        $s = substr($time,-2);
        $datetime = "$y-$m-$d $h:$i:$s-$random";
        return $datetime;
    }

    /**
     * Show Limit Assessment
     * 
     * @param Request $request
     * @return view
     */
    public function showLimitAssessment(Request $request)
    {
        $appId = $request->get('app_id');
        $bizId = $request->get('biz_id');
               
        $anchorData = $this->appRepo->getAnchorDataByAppId($appId);
        $anchorId = $anchorData ? $anchorData->anchor_id : 0;
        $loanAmount = $anchorData ? $anchorData->loan_amt : 0;
                
        $whereCondition = [];
        //$whereCondition['anchor_id'] = $anchorId;
        $prgmData = $this->appRepo->getProgramData($whereCondition);
        
        $offerWhereCond = [];
        $offerWhereCond['app_id'] = $appId;        
        $offerData = $this->appRepo->getOfferData($offerWhereCond);
        $offerId = $offerData ? $offerData->offer_id : 0;
        $offerStatus = $offerData ? $offerData->status : 0;
        
        if ($offerStatus == 2) {
            $offerId = 0;
        }
        return view('backend.cam.limit_assessment')
                ->with('appId', $appId)
                ->with('bizId', $bizId)
                ->with('offerId', $offerId)
                ->with('loanAmount', $loanAmount)
                ->with('prgmData', $prgmData)
                ->with('offerData', $offerData);
    }
    
    /**
     * Save Limit Assessment
     * 
     * @param Request $request
     * @return view
     */
    public function saveLimitAssessment(Request $request)            
    {   
        try {
            $appId = $request->get('app_id');
            $bizId = $request->get('biz_id'); 
            $offerId = $request->get('offer_id') ? $request->get('offer_id') : null; 
            $loanAmount = $request->get('loan_amount') ? $request->get('loan_amount') : null; 
                                  
            $this->appRepo->updateOfferByAppId($appId, ['is_active' => 0]);
                        
            $addlData = [];
            $addlData['app_id'] = $appId;
            $addlData['loan_amount'] = $loanAmount;
            $offerData = $this->prepareOfferData($request->all(), $addlData);
            
            $savedOfferData = $this->appRepo->saveOfferData($offerData, $offerId);
            
            if ($savedOfferData) {
                //Update workflow stage
                Helpers::updateWfStage('approver', $appId, $wf_status = 1, $assign_role = true);
            
                Session::flash('message',trans('backend_messages.limit_assessment_success'));
                return redirect()->route('limit_assessment',['app_id' =>  $appId, 'biz_id' => $bizId]);
            } else {
                return redirect()->back()->withErrors(trans('backend_messages.limit_assessment_fail'));
            }
            
        } catch (Exception $ex) {
            return redirect()->back()->withErrors(Helpers::getExceptionMessage($ex));
        }
    }
    
    /**
     * Prepare Offer Data to save
     * 
     * @param array $requestData
     * @return array
     */
    protected function prepareOfferData($requestData, $addlData=[])
    {
        $offerData = [
            'app_id' => $addlData['app_id'],
            'prgm_id' => $requestData['prgm_id'],
            'loan_amount' => $addlData['loan_amount'],
            'loan_offer' => $requestData['loan_offer'],        
            'interest_rate' => $requestData['interest_rate'],        
            'tenor' => $requestData['tenor'],        
            'tenor_old_invoice' => $requestData['tenor_old_invoice'],        
            'margin' => $requestData['margin'],
            'overdue_interest_rate'=> $requestData['overdue_interest_rate'],        
            'adhoc_interest_rate'=> $requestData['adhoc_interest_rate'],        
            'grace_period' => $requestData['grace_period'],        
            'processing_fee' => $requestData['processing_fee'],
            'check_bounce_fee' => $requestData['check_bounce_fee'],
            'comment' => $requestData['comment'],
            'is_active' => 1,
        ];
        return $offerData;
    }

    public function gstin(Request $request, FinanceModel $fin){
    	$appId = $request->get('app_id');
        $gstdocs = $fin->getGSTStatements($appId);
    	$user = $fin->getUserByAPP($appId);
    	$user_id = $user['user_id'];
	    $gst_details = $fin->getGstbyUser($user_id);
	    $gst_no = $gst_details['pan_gst_hash'];
        return view('backend.cam.gstin', ['gstdocs' => $gstdocs, 'appId'=> $appId, 'gst_no'=> $gst_no]);
    }




    public function showPromoter(Request $request){
        $attribute['biz_id'] = $request->get('biz_id'); 
        $attribute['app_id'] = $request->get('app_id');
        $arrPromoterData = $this->userRepo->getOwnerApiDetail($attribute);
    	return view('backend.cam.promoter')->with([
            'arrPromoterData' => $arrPromoterData 
            ]);;
    }

    
    /*  for iframe model  */
     public function pullCibilCommercial(Request $request){
       $request =  $request->all();
    }
     /*  for iframe model  */
     public function pullCibilPromoter(Request $request){
       $request =  $request->all();
    }
     /*  for iframe model  */
     public function viewCibilReport(Request $request){
       $request =  $request->all();
    }
    
    



}
