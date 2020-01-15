<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Http\Requests\FinanceInformationRequest as FinanceRequest;
use App\Http\Requests\AnchorInfoRequest;
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
use App\Inv\Repositories\Models\BusinessAddress;
use App\Inv\Repositories\Models\CamHygiene;
use Auth;
use Session;
use App\Libraries\Gupshup_lib;
date_default_timezone_set('Asia/Kolkata');
use Helpers;
use Illuminate\Support\Facades\Hash;

class CamController extends Controller
{
    protected $download_xls = TRUE;
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
        try{
            $arrRequest['biz_id'] = $request->get('biz_id');
            $arrRequest['app_id'] = $request->get('app_id');
            $arrBizData = Business::getApplicationById($arrRequest['biz_id']);
            $arrOwnerData = BizOwner::getCompanyOwnerByBizId($arrRequest['biz_id']);
            foreach ($arrOwnerData as $key => $arr) {
                  $arrOwner[$key] =  $arr['first_name'];
            }
            $arrEntityData = Business::getEntityByBizId($arrRequest['biz_id']);
            if(isset($arrEntityData['industryType'])){
                  $arrBizData['industryType'] = $arrEntityData['industryType'];
            }
            if(isset($arrEntityData['name'])){      
                  $arrBizData['legalConstitution'] = $arrEntityData['name'];
            }

            $whereCondition = [];
            //$whereCondition['anchor_id'] = $anchorId;
            $prgmData = $this->appRepo->getProgramData($whereCondition);
            if(!empty($prgmData))
            {
               $arrBizData['prgm_name'] = $prgmData['prgm_name'];
            }
            $arrBizData['email']  = $arrEntityData['email'];
            $arrBizData['mobile_no']  = $arrEntityData['mobile_no'];
            $arrCamData = Cam::where('biz_id','=',$arrRequest['biz_id'])->where('app_id','=',$arrRequest['app_id'])->first();
           
            if(isset($arrCamData['t_o_f_security_check'])){
                $arrCamData['t_o_f_security_check'] = explode(',', $arrCamData['t_o_f_security_check']);
            }
            return view('backend.cam.overview')->with(['arrCamData' =>$arrCamData ,'arrRequest' =>$arrRequest, 'arrBizData' => $arrBizData, 'arrOwner' =>$arrOwner]);
        } catch (Exception $ex) {
            return redirect()->back()->withErrors(Helpers::getExceptionMessage($ex));
        } 

    }

    public function camInformationSave(Request $request){
       try{
            $arrCamData = $request->all();
            $userId = Auth::user()->user_id;
            if(!isset($arrCamData['rating_no'])){
                    $arrCamData['rating_no'] = NULL;
            }
            if(!isset($arrCamData['t_o_f_security_check'])){
                    $arrCamData['t_o_f_security_check'] = NULL;
            }else{
                  $arrCamData['t_o_f_security_check'] = implode(',', $arrCamData['t_o_f_security_check']);
            }
            //dd($arrCamData);

            if($arrCamData['cam_report_id'] != ''){
                 $updateCamData = Cam::updateCamData($arrCamData, $userId);
                 if($updateCamData){
                        Session::flash('message',trans('CAM information updated sauccessfully'));
                 }else{
                       Session::flash('message',trans('CAM information not updated successfully'));
                 }
            }else{
                $saveCamData = Cam::creates($arrCamData, $userId);
                if($saveCamData){
                        Session::flash('message',trans('CAM information saved successfully'));
                 }else{
                       Session::flash('message',trans('CAM information not saved successfully'));
                 }
            }    
            return redirect()->route('cam_overview', ['app_id' => request()->get('app_id'), 'biz_id' => request()->get('biz_id')]);
        } catch (Exception $ex) {
            return redirect()->back()->withErrors(Helpers::getExceptionMessage($ex));
        }
    }

    public function showCibilForm(Request $request){
        try{
            $arrRequest['biz_id'] = $biz_id = $request->get('biz_id');
            $arrRequest['app_id'] = $request->get('app_id');
            $arrHygieneData = CamHygiene::where('biz_id','=',$arrRequest['biz_id'])->where('app_id','=',$arrRequest['app_id'])->first();
            if(!empty($arrHygieneData)){
                  $arrHygieneData['remarks'] = json_decode($arrHygieneData['remarks'], true);  

            }
            $arrCompanyDetail = Business::getCompanyDataByBizId($biz_id);
            $arrCompanyOwnersData = BizOwner::getCompanyOwnerByBizId($biz_id);
            //dd($arrCompanyOwnersData);
            return view('backend.cam.cibil', compact('arrCompanyDetail', 'arrCompanyOwnersData', 'arrRequest', 'arrHygieneData'));
        } catch (Exception $ex) {
            return redirect()->back()->withErrors(Helpers::getExceptionMessage($ex));
        }
    }

    private function getRangeFromdates(array $array=[]){
       if (empty($array)) {
         return array(
          'from' => date('Y-m-01', strtotime('-6 months', strtotime(date('Y-m-01')))),
          'to' => date("Y-m-d", strtotime('Last day of Last month')),
         ); 
       }
       $temp=[];
       foreach ($array as $key => $value) {
        $no = preg_replace('#[^0-9]+#', '', $value);
        $temp[] = strlen($no) >= 8 ? substr($no, 0, 8) : substr($no, 0, 6).'01';
       }
       $x = str_split(min($temp), 2);
       $y = str_split(max($temp), 2);
       $min_date = $x[0].$x[1].'-'.$x[2].'-'.$x[3];
       $max_date = $y[0].$y[1].'-'.$y[2].'-'.$y[3];
      return array(
        'from' => $min_date,
        'to' => $max_date,
       );
    }

    public function finance(Request $request, FinanceModel $fin){
        $appId = $request->get('app_id');
        $pending_rec = $fin->getPendingFinanceStatement($appId);
        $financedocs = $fin->getFinanceStatements($appId);
        $contents = array();
        if (file_exists(storage_path('app/public/user/'.$appId.'_finance.json'))) {
          $contents = json_decode(base64_decode(file_get_contents(storage_path('app/public/user/'.$appId.'_finance.json'))),true);
        }
        $borrower_name = $contents['FinancialStatement']['NameOfTheBorrower'] ?? '';
        $latest_finance_year = 2010;
        $fy = $contents['FinancialStatement']['FY'] ?? array();
        $financeData = [];
        if (!empty($fy)) {
          foreach ($fy as $k => $v) {
            $latest_finance_year = $latest_finance_year < $v['year'] ? $v['year'] : $latest_finance_year;
            $financeData[$v['year']] = $v;
          }
        }
        return view('backend.cam.finance', [
          'financedocs' => $financedocs, 
          'appId'=> $appId, 
          'pending_rec'=> $pending_rec,
          'borrower_name'=> $borrower_name,
          'finance_data'=> $financeData,
          'latest_finance_year'=> $latest_finance_year,
        ]);

    }

    public function banking(Request $request, FinanceModel $fin){
        $appId = $request->get('app_id');
        $pending_rec = $fin->getPendingBankStatement($appId);        
        $bankdocs = $fin->getBankStatements($appId);
        $contents = array();
        if (file_exists(storage_path('app/public/user/'.$appId.'_banking.json'))) {
          $contents = json_decode(base64_decode(file_get_contents(storage_path('app/public/user/'.$appId.'_banking.json'))),true);
        }
        $customers_info = [];
        if (!empty($contents)) {
          foreach ($contents['statementdetails'] as $key => $value) {
            $account_no = $contents['accountXns'][0]['accountNo'];
            $customer_data = $value['customerInfo'];
            $customers_info[] = array(
              'name' => $customer_data['name'],
              'email' => $customer_data['email'],
              'mobile' => $customer_data['mobile'],
              'account_no' => $account_no,
              'bank' => $customer_data['bank'],
              'pan' => $customer_data['pan'],
            );
          }
        }
        return view('backend.cam.bank', [
          'bankdocs' => $bankdocs,
          'appId'=> $appId,
          'pending_rec'=> $pending_rec,
          'bank_data'=> $contents,
          'customers_info'=> $customers_info,
          ]);
    }

    public function finance_store(FinanceRequest $request, FinanceModel $fin){
        $financeid = _getRand();
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

    public function updateBankDocument(Request $request){
      $perfios_txn_id = $request->session()->get('perfios_txn_id');
      $allData = $request->all();
      $appId = $request->get('app_id');
      $biz_id = $request->get('biz_id');
      $doc_id = $request->get('doc_id');
      $app_doc_file_id = $request->get('app_doc_file_id');
      $fin = new FinanceModel;
      $bank_doc_data = $fin->getSingleBankStatement($appId, $app_doc_file_id);
      $bankdata = $fin->getBankData();
      $req_data = _encrypt("$appId|$doc_id|$biz_id|$app_doc_file_id|$perfios_txn_id");
      return view('backend.cam.upload_bank',compact('bank_doc_data', 'req_data','bankdata','perfios_txn_id'));
    }

    public function saveBankDocument(Request $request){
        $arrFileData = $request->all();
        $perfios_txn_id = $request->session()->get('perfios_txn_id');
        if (empty($perfios_txn_id)) {
          return response()->json(['message' => 'You can\'t re-process without analyse the files.','status' => 0]);
        }
        $userId = Auth::user()->user_id;
        $doc_id = $request->get('doc_id');
        $biz_id = $request->get('biz_id');
        $appId = $request->get('app_id');
        $app_doc_file_id = $request->get('app_doc_file_id');
        $req_data = $request->get('req_data');
        $encrypted = "$appId|$doc_id|$biz_id|$app_doc_file_id|$perfios_txn_id";
        if ($encrypted != _decrypt($req_data)) {
         return response()->json(['message' => 'Requested Data is not valid','status' => 0]);
        }
        if (empty($arrFileData['doc_file']) && !empty($arrFileData['reupload'])) {
         return response()->json(['message' => 'File is required to Re-upload','status' => 0]);
        }
        $fin = new FinanceModel;
        $appUser = $fin->getUserByAPP($appId);
        $appUserId = $appUser['user_id'];
        $file_bank_id = $arrFileData['file_bank_id'];
        $bankData = $fin->getBankDetail($file_bank_id);
        $arrFileData['doc_name'] = $bankData['bank_name'] ?? NULL;
        $arrFileData['finc_year'] = NULL;
        $arrFileData['gst_month'] = $arrFileData['bank_month'];
        $arrFileData['gst_year'] = $arrFileData['bank_year'];
        $arrFileData['pwd_txt'] = $arrFileData['is_pwd_protected'] ? $arrFileData['pwd_txt'] :NULL;
        if (empty($arrFileData['doc_file']) && !empty($arrFileData['reprocess'])) {
          $bank_errors = $request->session()->get('bank_errors');
          if (empty($bank_errors[$app_doc_file_id])) {
            return response()->json(['message' => 'You can\'t re-process without error on this file.','status' => 0]);
          }
          $fileid = $bank_errors[$app_doc_file_id]['fileid'];
          $arrayToUpdate = array(
            "is_pwd_protected" => $arrFileData['is_pwd_protected'],
            "is_scanned" => $arrFileData['is_scanned'],
            "pwd_txt" => $arrFileData['pwd_txt'],
            "doc_name" => $arrFileData['doc_name'],
            "file_bank_id" => $arrFileData['file_bank_id'],
            "finc_year" => NULL,
            "gst_month" => $arrFileData['gst_month'],
            "gst_year" => $arrFileData['gst_year'],
          );
          $is_updated = $this->docRepo->UpdateAppDocument($arrayToUpdate, $app_doc_file_id);
          if ($is_updated) {
             $reprocess_data = array(
              'doc_id' => $doc_id,
              'biz_id' => $biz_id,
              'app_id' => $appId,
              'app_doc_file_id' => $app_doc_file_id,
              'fileid' => $fileid,
            );
            return $this->_reProcessStmt($reprocess_data);
          }
        }else{
          $arrFile['doc_file'] = $arrFileData['doc_file']['0'];
          $uploadData = Helpers::uploadAppFile($arrFile, $appId);
          $userFile = $this->docRepo->saveFile($uploadData);
          $appDocData = Helpers::appDocData($arrFileData, $userFile->file_id);
          $appDocResponse = $this->docRepo->saveAppDoc($appDocData);
          $is_deleted = $this->docRepo->deleteDocument($app_doc_file_id);
          $app_doc_file_id = $appDocResponse['app_doc_file_id'];
          if ($app_doc_file_id && $is_deleted) {
            $reupload_data = array(
              'app_id' => $appId,
              'app_doc_file_id' => $app_doc_file_id,
              'perfiostransactionid' => $perfios_txn_id,
              'prolitus_txn_id' => $request->session()->get('prolitus_txn_id'),
              'doc_id' => $doc_id,
              'biz_id' => $biz_id,
            );
            return $this->_reUploadBankStmt($reupload_data);
          }else{
            return response()->json(['message' => 'Unable to Re-Upload the statement.','status' => 0]);
          }
        } 
    }


    private function _reUploadBankStmt($reupload_data){
       $app_id = $reupload_data['app_id'];
       $doc_id = $reupload_data['doc_id'];
       $biz_id = $reupload_data['biz_id'];
       $app_doc_file_id = $reupload_data['app_doc_file_id'];
       $perfiostransactionid = $reupload_data['perfiostransactionid'];
       $prolitus_txn = $reupload_data['prolitus_txn_id'];
       $fin = new FinanceModel();
       $file_doc = $fin->getSingleBankStatement($app_id, $app_doc_file_id);
       dd($file_doc);
       $bank_detail = $fin->getBankDetail($file_doc->file_bank_id);
       $perfios_bank_id = $bank_detail->perfios_bank_id ?? NULL;
       $filepath = $file_doc['file_path'];
       $app_doc_file_id = $file_doc['app_doc_file_id'];
       $password = $file_doc['file_password'];
        $req_arr = array(
          'perfiosTransactionId' => $perfiostransactionid,
          'file_content' => public_path('storage/'.$filepath),
         );
        $bsa = new Bsa_lib();
        $upl_file = $bsa->api_call(Bsa_lib::UPL_FILE, $req_arr);
        if ($upl_file['status'] == 'success') {
            $reprocess_data = array(
              'doc_id' => $doc_id,
              'biz_id' => $biz_id,
              'app_id' => $app_id,
              'app_doc_file_id' => $app_doc_file_id,
              'fileid' => $upl_file['fileid']
            );
            return $this->_reProcessStmt($reprocess_data);
        }else{
           return response()->json(['message' => $proc_txn['message'],'status' => 0]);
        }
        return response()->json(['message' => 'Unable to upload the file.','status' => 1]);
    }


    private function _reProcessStmt($reprocess_data){
        $perfios_txn_id = Session::get('perfios_txn_id');
        if (empty($perfios_txn_id)) {
          return response()->json(['message' => 'You can\'t re-process without analyse the files.','status' => 0]);
        }
        $userId = Auth::user()->user_id;
        $doc_id = $reprocess_data['doc_id'];
        $biz_id = $reprocess_data['biz_id'];
        $app_id = $reprocess_data['app_id'];
        $app_doc_file_id = $reprocess_data['app_doc_file_id'];

       $fin = new FinanceModel();
       $file_doc = $fin->getSingleBankStatement($app_id, $app_doc_file_id);
       $bank_detail = $fin->getBankDetail($file_doc->file_bank_id);
       $perfios_bank_id = $bank_detail->perfios_bank_id ?? NULL;
       $filepath = $file_doc['file_path'];
       $password = $file_doc['file_password'];
       $prolitus_txn = Session::get('prolitus_txn_id');
        $req_arr = array(
          'perfiosTransactionId' => $perfios_txn_id,
          'fileId' => $reprocess_data['fileid'],
          'institutionId' => $perfios_bank_id,
          'password' => $password,
        );
        $bsa = new Bsa_lib();
        $proc_txn = $bsa->api_call(Bsa_lib::REPRC_STMT, $req_arr);
        if ($proc_txn['status'] == 'success') {
             return response()->json(['message' => "File processed Successfully",'status' => 1, 'biz_perfios_id' => $perfios_txn_id]);
        }else{
            return response()->json(['message' => $proc_txn['message'],'status' => 0]);
        }
    }



    public function _uploadFiles($path, $reqFiles){
      $inputArr = [];
      foreach ($reqFiles as $key => $reqFile) {
        $fileName = $reqFile->hashName();
        $filePath = storage_path("app/public/") . $path;
        $fullPath = $filePath . $fileName;
        $inputArr[$key]['file_path'] = $path . $fileName;
        $inputArr[$key]['file_type'] = $reqFile->getClientMimeType();
        $inputArr[$key]['file_name'] = $reqFile->getClientOriginalName();
        $inputArr[$key]['file_size'] = $reqFile->getClientSize();
        $inputArr[$key]['file_encp_key'] =  md5($fullPath);
        $inputArr[$key]['created_by'] = 1;
        $inputArr[$key]['updated_by'] = 1;
        $reqFile->move($filePath, $fileName);
      }
        return $inputArr[0] ?? $inputArr;
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
      if (!empty($response['perfiosTransactionId'])) {
         $request->session()->put('perfios_txn_id', $response['perfiosTransactionId']);
         $request->session()->put('prolitus_txn_id', $response['prolitusTransactionId']);
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
      $errors = [];
      if (!empty($response['errors'])) {
        $request->session()->put('bank_errors', $response['errors']);
        foreach ($response['errors'] as $app_doc_file_id => $perfios_err) {
         $errors[$app_doc_file_id] = $perfios_err['message'];
        }
      }
      if ($response['status'] == 'success') {
        return response()->json(['message' =>'Bank Statement analysed successfully.','status' => 1,
          'value' => $response]);
      }else{
        return response()->json(['message' =>$response['message'] ?? 'Something went wrong','errors' => $errors,'perfios_log_id' => $response['perfiosTransactionId'],'status' => 0,'value'=>['file_url'=>'']]);
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
        $dates = [];
        foreach ($financedocs as $doc) {
          $files[] = array(
            'app_doc_file_id' => $doc->app_doc_file_id,
            'app_id' => $doc->app_id,
            'file_id' => $doc->file_id,
            'fin_year' => $doc->finc_year,
            'file_path' => public_path('storage/'.$doc->file_path),
            'is_scanned' => $doc->is_scanned == 1 ? 'true' : 'false',
            'file_password' => $doc->pwd_txt ?? NULL,
          );
           if (!empty($doc->gst_year) && !empty($doc->gst_month)) {
              $dates[] = $doc->gst_year .'-'.sprintf('%02d',$doc->gst_month);
           }
        }
        $files[] = $dates;
        return $files;
    }

    private function getBankFilePath($appId) {
        $fin = new FinanceModel();
        $bankdocs = $fin->getBankStatements($appId);
        $files = [];
        $dates = [];
        foreach ($bankdocs as $doc) {
           $bank_detail = $fin->getBankDetail($doc->file_bank_id);
           $files[] = array(
            'app_doc_file_id' => $doc->app_doc_file_id,
            'facility' => $doc->facility,
            'sanctionlimitfixed' => $doc->sanctionlimitfixed,
            'drawingpowervariableamount' => $doc->drawingpowervariableamount,
            'sanctionlimitvariableamount' => $doc->sanctionlimitvariableamount,
            'app_id' => $doc->app_id,
            'file_id' => $doc->file_id,
            'institutionId' => $bank_detail->perfios_bank_id ?? NULL,
            'fin_year' => $doc->finc_year,
            'file_path' => public_path('storage/'.$doc->file_path),
            'is_scanned' => $doc->is_scanned == 1 ? 'true' : 'false',
            'file_password' => $doc->pwd_txt ?? NULL,
          );
           if (!empty($doc->gst_year) && !empty($doc->gst_month)) {
              $dates[] = $doc->gst_year .'-'. sprintf('%02d',$doc->gst_month);
           }
        }
        $files[] = $dates;
        return $files;
    }

    private function _callBankApi($filespath, $appId){
        $user = FinanceModel::getUserByAPP($appId);
        $loanAmount = (int)$user['loan_amt'];
        $dates = array_pop($filespath);
        $ranges = $this->getRangeFromdates($dates);
        $bsa = new Bsa_lib();
        $reportType = 'json';
        $prolitus_txn = _getRand(18);
        $process_txn_cnt = 0;
        $req_arr = array(
            'txnId' => $prolitus_txn, //'bharatSTmt',
            'loanAmount' => $loanAmount,
            'loanDuration' => '12',
            'loanType' => 'SME Loan',
            'processingType' => 'STATEMENT',
            'facility' => 'NONE',
            'sanctionLimitFixed' => 'FALSE',
            'acceptancePolicy' => 'atLeastOneTransactionInRange',
            'yearMonthFrom' => date('Y-m',strtotime($ranges['from'])),
            'yearMonthTo' => date('Y-m',strtotime($ranges['to'])),
            'transactionCompleteCallbackUrl' => route('api_perfios_bsa_callback'),
         );
        foreach ($filespath as $bankdocs) {
          $req_arr['drawingpowervariableamount'][] = $bankdocs['drawingpowervariableamount'];
          $req_arr['sanctionlimitvariableamount'][] = $bankdocs['sanctionlimitvariableamount'];


          if ($loanAmount < $bankdocs['drawingpowervariableamount'] || $loanAmount < $bankdocs['sanctionlimitvariableamount']) {
            return ["status" => "fail","message" => "Loan amount can not be less than drawingpowervariableamount or sanctionlimitvariableamount","api_type"=>Bsa_lib::INIT_TXN];
          }
        }
        $init_txn = $bsa->api_call(Bsa_lib::INIT_TXN, $req_arr);
        if ($init_txn['status'] == 'success') {
          foreach ($filespath as $file_doc) {
             $filepath = $file_doc['file_path'];
             $app_doc_file_id = $file_doc['app_doc_file_id'];
             $password = $file_doc['file_password'];
             $institutionId = $file_doc['institutionId'];
              $req_arr = array(
                'perfiosTransactionId' => $init_txn['perfiostransactionid'],
                'file_content' => $filepath,
               );
              $upl_file = $bsa->api_call(Bsa_lib::UPL_FILE, $req_arr);
              if ($upl_file['status'] == 'success') {
                  $req_arr = array(
                    'perfiosTransactionId' => $init_txn['perfiostransactionid'],
                    'fileId' => $upl_file['fileid'],
                    'institutionId' => $institutionId,
                    'password' => $password,
                  );
                  $proc_txn = $bsa->api_call(Bsa_lib::PRC_STMT, $req_arr);
                  if ($proc_txn['status'] == 'success') {
                      $process_txn_cnt++;
                  }else{
                      $error[$app_doc_file_id] = $proc_txn;
                      $error[$app_doc_file_id]['fileid'] = $upl_file['fileid'];
                      $error[$app_doc_file_id]['api_type'] = Bsa_lib::PRC_STMT;
                  }
              }else{
                $error[$app_doc_file_id] = $upl_file;
                $error[$app_doc_file_id]['fileid'] = $upl_file['fileid'];
                $error[$app_doc_file_id]['api_type'] = Bsa_lib::UPL_FILE;
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
                $final_res['errors'] = $error;
                $final_res['status'] = 'fail';
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

        if (!empty($is_scanned) && strtolower($is_scanned) == '1') {
           $final_res['api_type'] = Bsa_lib::REP_GEN;
           return $final_res;
        }

        $file_name = $appId.'_banking.xls';
        $req_arr = array(
          'perfiosTransactionId' => $init_txn['perfiostransactionid'],
          'types' => 'xls',
        );
        if ($this->download_xls) {
          $final_res = $bsa->api_call(Bsa_lib::GET_REP, $req_arr);
          if ($final_res['status'] != 'success') {
              $final_res['api_type'] = Bsa_lib::GET_REP;
              $final_res['prolitusTransactionId'] = $prolitus_txn;
              $final_res['perfiosTransactionId'] = $init_txn['perfiostransactionid'];
              return $final_res;
          }
          $myfile = fopen(storage_path('app/public/user').'/'.$file_name, "w");
          \File::put(storage_path('app/public/user').'/'.$file_name, $final_res['result']); 
        }
        $file= url('storage/user/'. $file_name);
        $req_arr['types'] =  $reportType;
        $final_res = $bsa->api_call(Bsa_lib::GET_REP, $req_arr);
        if ($final_res['status'] == 'success') {
          $final_res['result'] = base64_encode($final_res['result']);
          $json_file_name = $appId.'_banking.json';
          $myfile = fopen(storage_path('app/public/user').'/'.$json_file_name, "w");
          \File::put(storage_path('app/public/user').'/'.$json_file_name, $final_res['result']);
          $log_data = array(
            'status' => $final_res['status'],
            'updated_by' => Auth::user()->user_id,
          );
          FinanceModel::updatePerfios($log_data,'biz_perfios',$init_txn['perfiostransactionid'],'biz_perfios_id');
        }
        $final_res['api_type'] = Bsa_lib::GET_REP;
        $final_res['file_url'] = $file;
        $final_res['prolitusTransactionId'] = $prolitus_txn;
        $final_res['perfiosTransactionId'] = $init_txn['perfiostransactionid'];
        return $final_res;
    }

    public function _callFinanceApi($filespath, $appId) {
        $user = FinanceModel::getUserByAPP($appId);
        $loanAmount = (int)$user['loan_amt'];
        $dates = array_pop($filespath);
        $perfios = new Perfios_lib();
        $reportType = 'json';
        $prolitus_txn = _getRand(18);
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
            'transactionCompleteCallbackUrl' => route('api_perfios_fsa_callback'),
        );
        $start_txn = $perfios->api_call(Perfios_lib::STRT_TXN, $req_arr);
         if ($start_txn['status'] == 'success') {
          foreach ($filespath as $file_doc) {
            $financial_year = substr($file_doc['fin_year'], -4);
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
                       $process_txn_cnt++;
                  }else{
                      $upl_stmt_error = $upl_stmt;
                      $upl_stmt_error['prolitusTransactionId'] = $prolitus_txn;
                      $upl_stmt_error['perfiosTransactionId'] = $start_txn['perfiostransactionid'];
                      $upl_stmt_error['api_type'] = Perfios_lib::UPL_STMT;
                  }
              }else{
                $add_year_error = $add_year;
                $add_year_error['prolitusTransactionId'] = $prolitus_txn;
                $add_year_error['perfiosTransactionId'] = $start_txn['perfiostransactionid'];
                $add_year_error['api_type'] = Perfios_lib::ADD_YEAR;
              }
          }
          if ($process_txn_cnt == count($filespath)) {
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
                $final_res['api_type'] = Perfios_lib::CMPLT_TXN;        
              }else{
                $final_res = $cmplt_txn;
                $final_res['prolitusTransactionId'] = $prolitus_txn;
                $final_res['perfiosTransactionId'] = $start_txn['perfiostransactionid'];
                $final_res['api_type'] = Perfios_lib::CMPLT_TXN;
              }
          }else{
            $final_res = $add_year_error ?? $upl_stmt_error;
            $final_res['prolitusTransactionId'] = $prolitus_txn;
            $final_res['perfiosTransactionId'] = $start_txn['perfiostransactionid'];
            $final_res['api_type'] = !empty($add_year_error) ? Perfios_lib::ADD_YEAR : Perfios_lib::UPL_STMT;
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
        $file_name = $appId.'_finance.xls';
        $req_arr = array(
            'apiVersion' => $apiVersion,
            'vendorId' => $vendorId,
            'perfiosTransactionId' => $start_txn['perfiostransactionid'],
            'reportType' => 'xls',
            'txnId' => $prolitus_txn,
        );
        if ($this->download_xls) {
          $final_res = $perfios->api_call(Perfios_lib::GET_STMT, $req_arr);
          if ($final_res['status'] != 'success') {
              $final_res['api_type'] = Perfios_lib::GET_STMT;
              $final_res['prolitusTransactionId'] = $prolitus_txn;
              $final_res['perfiosTransactionId'] = $start_txn['perfiostransactionid'];
              return $final_res;
          }
          $myfile = fopen(storage_path('app/public/user').'/'.$file_name, "w");
          \File::put(storage_path('app/public/user').'/'.$file_name, $final_res['result']);
        }
        $file= url('storage/user/'. $file_name);
        $req_arr['reportType'] = $reportType;
        $final_res = $perfios->api_call(Perfios_lib::GET_STMT, $req_arr);
        if ($final_res['status'] == 'success') {
          $final_res['result'] = base64_encode($final_res['result']);
          $json_file_name = $appId.'_finance.json';
          $myfile = fopen(storage_path('app/public/user').'/'.$json_file_name, "w");
          \File::put(storage_path('app/public/user').'/'.$json_file_name, $final_res['result']);
          $log_data = array(
            'status' => $final_res['status'],
            'updated_by' => Auth::user()->user_id,
          );
          FinanceModel::updatePerfios($log_data,'biz_perfios',$start_txn['perfiostransactionid'],'biz_perfios_id');
        }
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
        $file_name = $appId.'_finance.xls';
        $req_arr = array(
              'apiVersion' => $apiVersion,
              'vendorId' => $vendorId,
              'perfiosTransactionId' => $perfiostransactionid,
              'reportType' => 'xls',
              'txnId' => $prolitus_txn,
        );
        if ($this->download_xls) {
          $final_res = $perfios->api_call(Perfios_lib::GET_STMT, $req_arr);
          if ($final_res['status'] != 'success') {
              $final_res['api_type'] = Perfios_lib::GET_STMT;
              $final_res['prolitusTransactionId'] = $prolitus_txn;
              $final_res['perfiosTransactionId'] = $perfiostransactionid;
              return response()->json(['message' => $final_res['message'] ?? 'Something went wrong','status' => 0,'value'=>['file_url'=>'']]);
          }else{
            $myfile = fopen(storage_path('app/public/user').'/'.$file_name, "w");
            \File::put(storage_path('app/public/user').'/'.$file_name, $final_res['result']);
          }
        }
        $file= url('storage/user/'. $file_name);
        $req_arr['reportType'] = 'json';
        $final_res = $perfios->api_call(Perfios_lib::GET_STMT, $req_arr);
        $final_res['api_type'] = Perfios_lib::GET_STMT;
        $final_res['file_url'] = $file;
        $final_res['prolitusTransactionId'] = $prolitus_txn;
        $final_res['perfiosTransactionId'] = $perfiostransactionid;
        if ($final_res['status'] == 'success') {
          $final_res['result'] = base64_encode($final_res['result']);
          $json_file_name = $appId.'_finance.json';
          $myfile = fopen(storage_path('app/public/user').'/'.$json_file_name, "w");
          \File::put(storage_path('app/public/user').'/'.$json_file_name, $final_res['result']);
          $log_data = array(
            'status' => $final_res['status'],
            'updated_by' => Auth::user()->user_id,
          );
          FinanceModel::updatePerfios($log_data,'biz_perfios',$biz_perfios_id,'biz_perfios_id');
        }
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
        $bsa = new Bsa_lib();
        $apiVersion = '2.1';
        $vendorId = 'capsave';
        $file_name = $appId.'_banking.xls';

        $req_arr = array(
          'perfiosTransactionId' => $perfiostransactionid,
          'types' => 'xls',
        );
        if ($this->download_xls) {
          $final_res = $bsa->api_call(Bsa_lib::GET_REP, $req_arr);
          if ($final_res['status'] != 'success') {
              $final_res['api_type'] = Bsa_lib::GET_REP;
              $final_res['prolitusTransactionId'] = $prolitus_txn;
              $final_res['perfiosTransactionId'] = $perfiostransactionid;
               $log_data = array(
                'app_id' =>  $appId,
                'status' => $final_res['status'],
                'type' => '1',
                'api_name' => Bsa_lib::GET_REP,
                'prolitus_txn_id' => $prolitus_txn ?? NULL,
                'perfios_log_id' => $perfiostransactionid,
                'created_by' => Auth::user()->user_id,
              );
              FinanceModel::insertPerfios($log_data);
              return response()->json(['message' => $final_res['message'] ?? 'Something went wrong','status' => 0,'value'=>['file_url'=>'']]);
          }else{
             $myfile = fopen(storage_path('app/public/user').'/'.$file_name, "w");
             \File::put(storage_path('app/public/user').'/'.$file_name, $final_res['result']);
          }
        }
        $file= url('storage/user/'. $file_name);
        $req_arr['types'] = 'json'; 
        $final_res = $bsa->api_call(Bsa_lib::GET_REP, $req_arr);
        $final_res['api_type'] = Bsa_lib::GET_REP;
        $final_res['file_url'] = $file;
        $final_res['prolitusTransactionId'] = $prolitus_txn;
        $final_res['perfiosTransactionId'] = $perfiostransactionid;
        if ($final_res['status'] == 'success') {
          $final_res['result'] = base64_encode($final_res['result']);
          $json_file_name = $appId.'_banking.json';
          $myfile = fopen(storage_path('app/public/user').'/'.$json_file_name, "w");
          \File::put(storage_path('app/public/user').'/'.$json_file_name, $final_res['result']);
          $log_data = array(
            'status' => $final_res['status'],
            'updated_by' => Auth::user()->user_id,
          );
          FinanceModel::updatePerfios($log_data,'biz_perfios',$biz_perfios_id,'biz_perfios_id');
        }

        if ($final_res['status'] == 'success') {
          return response()->json(['message' =>'Banking Statement analysed successfully.','status' => 1,
            'value' => $final_res]);
        }else{
          return response()->json(['message' => $final_res['message'] ?? 'Something went wrong','status' => 0,'value'=>['file_url'=>'']]);
        }
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

        $prgmLimitData = $this->appRepo->getProgramLimitData($appId);
        $limitData = $this->appRepo->getAppLimit($appId);

        $approveStatus = $this->appRepo->getApproverStatus(['app_id'=>$appId, 'approver_user_id'=>Auth::user()->user_id, 'is_active'=>1]);
        $currStage = Helpers::getCurrentWfStage($appId);                
        $currStageCode = $currStage->stage_code;                    
                
        return view('backend.cam.limit_assessment')
                ->with('appId', $appId)
                ->with('bizId', $bizId)
                ->with('limitData', $limitData)
                ->with('approveStatus', $approveStatus)
                ->with('prgmLimitData', $prgmLimitData)
                ->with('currStageCode', $currStageCode);
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

            $checkProgram = $this->appRepo->checkduplicateProgram([
              'app_id'=>$appId,
              'anchor_id'=>$request->anchor_id,
              'prgm_id'=>$request->prgm_id
              ]);

            if($checkProgram->count()){
              Session::flash('message',trans('backend_messages.already_exist'));
              return redirect()->route('limit_assessment',['app_id' =>  $appId, 'biz_id' => $bizId]);
            }

            $totalLimit = $this->appRepo->getAppLimit($appId);

            if($totalLimit){
              $this->appRepo->saveAppLimit(['tot_limit_amt'=>str_replace(',', '', $request->tot_limit_amt)], $totalLimit->app_limit_id);
            }else{
              $app_limit = $this->appRepo->saveAppLimit([
                          'app_id'=>$appId,
                          'biz_id'=>$bizId,
                          'tot_limit_amt'=>str_replace(',', '', $request->tot_limit_amt),
                          'created_by'=>\Auth::user()->user_id,
                          'created_at'=>\Carbon\Carbon::now(),
                          ]);
              
            }

            $app_prgm_limit = $this->appRepo->saveProgramLimit([
                          'app_limit_id'=>($totalLimit)? $totalLimit->app_limit_id : $app_limit->app_limit_id,
                          'app_id'=>$appId,
                          'biz_id'=>$bizId,
                          'anchor_id'=>$request->anchor_id,
                          'prgm_id'=>$request->prgm_id,
                          'limit_amt'=>str_replace(',', '', $request->limit_amt),
                          'created_by'=>\Auth::user()->user_id,
                          'created_at'=>\Carbon\Carbon::now(),
                          ]);

            if ($app_prgm_limit) {
                //Update workflow stage
                //Helpers::updateWfStage('approver', $appId, $wf_status = 1, $assign_role = true);  
                Session::flash('message',trans('backend_messages.limit_assessment_success'));
                return redirect()->route('limit_assessment',['app_id' =>  $appId, 'biz_id' => $bizId]);
            } else {
                return redirect()->back()->withErrors(trans('backend_messages.limit_assessment_fail'));
            }
            
        } catch (Exception $ex) {
            return redirect()->back()->withErrors(Helpers::getExceptionMessage($ex));
        }
    }

    public function approveOffer(Request $request){
        $appApprData = [
            'app_id' => $request->get('app_id'),
            'approver_user_id' => \Auth::user()->user_id,
            'status' => 1
          ];
        $this->appRepo->saveAppApprovers($appApprData);
        Session::flash('message',trans('backend_messages.offer_approved'));
        return redirect()->back();
    }

    public function showLimitOffer(Request $request){
      $appId = $request->get('app_id');
      $biz_id = $request->get('biz_id');
      $aplid = $request->get('app_prgm_limit_id');
      $offerData= $this->appRepo->getProgramOffer($aplid);
      $limitData= $this->appRepo->getLimit($aplid);
      $current_offer_limit = isset($offerData->prgm_limit_amt)? $offerData->prgm_limit_amt: 0;
      $totalLimit = $this->appRepo->getAppLimit($appId);
      $offeredLimit= $this->appRepo->getProgramBalanceLimit($limitData->prgm_id);
      return view('backend.cam.limit_offer', ['offerData'=>$offerData, 'limitData'=>$limitData, 'limit_amt'=>$limitData->limit_amt, 'totalLimit'=> $totalLimit->tot_limit_amt, 'offeredLimit'=> $offeredLimit+$current_offer_limit]);
    }

    public function updateLimitOffer(Request $request){
      try{
        $appId = $request->get('app_id');
        $bizId = $request->get('biz_id');
        $aplid = (int)$request->get('app_prgm_limit_id');
        $request['prgm_limit_amt'] = str_replace(',', '', $request->prgm_limit_amt);
        $request['processing_fee'] = str_replace(',', '', $request->processing_fee);
        $request['check_bounce_fee'] = str_replace(',', '', $request->check_bounce_fee);

        $offerData= $this->appRepo->addProgramOffer($request->all(), $aplid);

        if($offerData){
          Session::flash('message',trans('backend_messages.limit_assessment_success'));
          return redirect()->route('limit_assessment',['app_id' =>  $appId, 'biz_id' => $bizId]);
        }else{
          Session::flash('message',trans('backend_messages.limit_assessment_fail'));
          return redirect()->route('limit_assessment',['app_id' =>  $appId, 'biz_id' => $bizId]);
        }
      }catch(\Exception $ex){
        return redirect()->back()->withErrors(Helpers::getExceptionMessage($ex));
      }
    }

    public function showLimit(Request $request){
      $appId = $request->get('app_id');
      $biz_id = $request->get('biz_id');
      $aplid = $request->get('app_prgm_limit_id');
      $limitData= $this->appRepo->getLimit($aplid);
      $offerData= $this->appRepo->getProgramOffer($aplid);
      $current_offer_limit = isset($offerData->prgm_limit_amt)? $offerData->prgm_limit_amt: 0;
      $totalLimit = $this->appRepo->getAppLimit($appId);
      $offeredLimit= $this->appRepo->getProgramBalanceLimit($limitData->prgm_id);
      return view('backend.cam.limit', ['limitData'=>$limitData, 'totalLimit'=> $totalLimit->tot_limit_amt, 'offeredLimit'=> $offeredLimit+$current_offer_limit, 'prgmLimit'=> $limitData->program->anchor_sub_limit]);
    }

    public function updateLimit(Request $request){
      try{
        $appId = $request->get('app_id');
        $bizId = $request->get('biz_id');
        $aplid = (int)$request->get('app_prgm_limit_id');
        $request['limit_amt'] = str_replace(',', '', $request->limit_amt);
        $limitData= $this->appRepo->saveProgramLimit($request->all(), $aplid);

        if($limitData){
          Session::flash('message',trans('backend_messages.limit_assessment_success'));
          return redirect()->route('limit_assessment',['app_id' =>  $appId, 'biz_id' => $bizId]);
        }else{
          Session::flash('message',trans('backend_messages.limit_assessment_fail'));
          return redirect()->route('limit_assessment',['app_id' =>  $appId, 'biz_id' => $bizId]);
        }
      }catch(\Exception $ex){
        return redirect()->back()->withErrors(Helpers::getExceptionMessage($ex));
      }
    }

    public function gstin(Request $request, FinanceModel $fin){
      $appId = $request->get('app_id');
      $biz_id = $request->get('biz_id');
      $gstdocs = $fin->getGSTStatements($appId);
      $user = $fin->getUserByAPP($appId);
      $user_id = $user['user_id'];
      $gst_details = $fin->getSelectedGstForApp($user_id);
      $all_gst_details = $fin->getAllGstForApp($biz_id);
      $gst_no = $gst_details['pan_gst_hash'];
        return view('backend.cam.gstin', ['gstdocs' => $gstdocs, 'appId'=> $appId, 'gst_no'=> $gst_no,'all_gst_details'=> $all_gst_details]);
    }




    public function showPromoter(Request $request){
        $attribute['biz_id'] = $request->get('biz_id'); 
        $attribute['app_id'] = $request->get('app_id');
        $arrPromoterData = $this->userRepo->getOwnerApiDetail($attribute);
        $arrCamData = Cam::where('biz_id','=',$attribute['biz_id'])->where('app_id','=',$attribute['app_id'])->first();
        return view('backend.cam.promoter')->with([
            'arrPromoterData' => $arrPromoterData, 
            'attribute' => $attribute,
            'arrCamData' => $arrCamData
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
    
    /**
     * View Anchor Form
     * 
     * @param Request $request
     * @return null
     * 
     * @author Anand
     */
    public function anchorViewForm(Request $request, Gupshup_lib $gupshup)            
    {
        try {
            $biz_id = $request->get('biz_id'); 
            $app_id = $request->get('app_id');
            $liftingData = $this->appRepo->getLiftingDetail($app_id);
            $anchorRelationData = $this->appRepo->getAnchorRelationDetails($app_id);
            $data = [];
            if (!empty($liftingData)) {
                foreach ($liftingData as $key => $value) {
                    $year = $value['year'];
                    $data[$year]['mt_value'][] = $value['mt_value'];
                    $data[$year]['mt_type'] = $value['mt_type'];
                    $data[$year]['anchor_lift_detail_id'][] = $value['anchor_lift_detail_id'];
                    $data[$year]['year'] = $year;
                    $data[$year]['mt_amount'][] = $value['amount'];
                }
            }
            return view('backend.cam.cam_anchor_view',['data'=> $data])
                ->with('biz_id',$biz_id)
                ->with('anchorRelationData', $anchorRelationData)
                ->with('app_id',$app_id);

        } catch (Exception $ex) {
            return redirect()->back()->withErrors(Helpers::getExceptionMessage($ex));
        }
    }
  
    /**
     * Save Anchor Form
     * 
     * @param Request $request
     * @return null
     * 
     * @author Anand
     */ 
   public function  SaveAnchorForm(Request $request,AnchorInfoRequest $anchor)            
    {   
        try {
            $allData = $request->all();
            $userId = Auth::user()->user_id;
            $relationShipArr = [];
            $liftingArr = [];
            
            $relationShipArr['biz_id']                = $allData['biz_id'];
            $relationShipArr['app_id']                = $allData['app_id'];
            $relationShipArr['year_of_association']   = $allData['year_of_association'];
            $relationShipArr['payment_terms']         = $allData['payment_terms'];
            $relationShipArr['grp_rating']            = $allData['grp_rating'];
            $relationShipArr['contact_person']        = $allData['contact_person'];
            $relationShipArr['contact_number']        = $allData['contact_number'];
            $relationShipArr['security_deposit']      = $allData['security_deposit'];
            $relationShipArr['note_on_lifting']       = $allData['note_on_lifting'];
            $relationShipArr['reference_from_anchor'] = $allData['reference_from_anchor'];
            $relationShipArr['anchor_risk_comments']  = $allData['anchor_risk_comments'];
            $anchorRelationData = $this->appRepo->getAnchorRelationDetails($allData['app_id']);
            if (!empty($anchorRelationData)) {
                $relationShipArr['updated_by'] = $userId;
                $this->appRepo->updateAnchorRelationDetails($relationShipArr, $anchorRelationData['anchor_relation_id']);
            }else{
                $relationShipArr['created_by'] = $userId;
                $this->appRepo->saveAnchorRelationDetails($relationShipArr);
            }

            
            //need to saveddd $relationShipArr and pass its id to lifting table
            
            //store array date of month
            $months = $allData['month'];
            $mtType = $allData['mt_type'];
            $years = $allData['year'];
            $countMonths = count($months);
            #dd($months, $mtType, $years,$countMonths);

           $liftingData = $this->appRepo->getLiftingDetail($allData['app_id']);
            for($i = 0; $i < $countMonths; $i++){
               foreach($months[$i]['mt_value'] as $key => $value){
                   if (!empty($liftingData)) {
                     $liftingArr['anchor_lift_detail_id'] = $months[$i]['anchor_lift_detail_id'][$key];;
                   }
                   $liftingArr['app_id'] = $allData['app_id'];
                   $liftingArr['year'] = $years[$i];
                   $liftingArr['month'] = $key+1;
                   $liftingArr['mt_type'] = $mtType[$i] ?? 0;
                   $liftingArr['mt_value'] = $value ?? 0;
                   $liftingArr['amount'] = $months[$i]['mt_amount'][$key] ?? 0;
                   if (!empty($liftingData)) {
                      $this->appRepo->updateLiftingDetail($liftingArr, $liftingArr['anchor_lift_detail_id']);
                   }else{
                        $this->appRepo->creates($liftingArr);
                   }
               }
           }
           return redirect()->back()->with('message', 'Lifiting Data Saved Successfully.');
        } catch (Exception $ex) {
            return redirect()->back()->withErrors(Helpers::getExceptionMessage($ex));
        }
    }




     public function camHygieneSave(Request $request){
      try {
            $arrHygieneData = $request->all();
            if(isset($arrHygieneData['remarks'])){
                $arrRemarkData = array();
                foreach ($arrHygieneData['remarks'] as $key => $value) {
                    $arrRemarkData[$arrHygieneData['promoterPan'][$key]]  = $value;
                }
                $arrHygieneData['remarks'] = json_encode($arrRemarkData);

            }else{
                $arrHygieneData['remarks'] = '';
            }
            if(!isset($arrHygieneData['comment'])){
               $arrHygieneData['comment'] = '';
            }
            $userId = Auth::user()->user_id;
            if($arrHygieneData['cam_hygiene_id'] != ''){
                 $updateHygieneDat = CamHygiene::updateHygieneData($arrHygieneData, $userId);
                 if($updateHygieneDat){
                        Session::flash('message',trans('CAM hygiene information updated sauccessfully'));
                 }else{
                       Session::flash('message',trans('CAM hygiene information not updated successfully'));
                 }
            }else{
                $saveHygieneData = CamHygiene::creates($arrHygieneData, $userId);
                if($saveHygieneData){
                        Session::flash('message',trans('CAM hygiene information saved successfully'));
                 }else{
                       Session::flash('message',trans('CAM hygiene information not saved successfully'));
                 }
            }    
            return redirect()->route('cam_cibil', ['app_id' => request()->get('app_id'), 'biz_id' => request()->get('biz_id')]);
        } catch (Exception $ex) {
            return redirect()->back()->withErrors(Helpers::getExceptionMessage($ex));
        }
    }


    public function promoterCommentSave(Request $request){
       try{
            $arrCamData = $request->all();
            //  dd($arrCamData);
            $userId = Auth::user()->user_id;
            if($arrCamData['cam_report_id'] != ''){
                 $updateCamData = Cam::updatePromoterComment($arrCamData, $userId);
                 if($updateCamData){
                        Session::flash('message',trans('Management information updated sauccessfully'));
                 }else{
                       Session::flash('message',trans('Management information not updated successfully'));
                 }
            }else{
                $saveCamData = Cam::savePromoterComment($arrCamData, $userId);
                if($saveCamData){
                        Session::flash('message',trans('Management information saved successfully'));
                 }else{
                       Session::flash('message',trans('Management information not saved successfully'));
                 }
            }    
            return redirect()->route('cam_promoter', ['app_id' => request()->get('app_id'), 'biz_id' => request()->get('biz_id')]);
        } catch (Exception $ex) {
            return redirect()->back()->withErrors(Helpers::getExceptionMessage($ex));
        }
    }



}
