<?php
namespace App\Http\Controllers\Lms;

use Auth;
use Session;
use Helpers;

use PHPExcel; 
use PHPExcel_IOFactory;

use Illuminate\Http\Request;
use App\Libraries\Idfc_lib;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use App\Inv\Repositories\Contracts\Traits\LmsTrait;
use App\Inv\Repositories\Contracts\Traits\ApplicationTrait;

use App\Inv\Repositories\Contracts\LmsInterface as InvLmsRepoInterface;
use App\Inv\Repositories\Contracts\UserInterface as InvUserRepoInterface;
use App\Inv\Repositories\Contracts\ApplicationInterface as InvAppRepoInterface;
use App\Inv\Repositories\Contracts\DocumentInterface as InvDocumentRepoInterface;
use Carbon\Carbon;
use App\Helpers\RefundHelper;
use App\Inv\Repositories\Contracts\MasterInterface;
use App\Inv\Repositories\Contracts\Traits\ActivityLogTrait;
use DB;
use App\Inv\Repositories\Models\Lms\Refund\RefundReq;


class RefundController extends Controller
{
	use ApplicationTrait;
    use LmsTrait;
    use ActivityLogTrait;
        
    protected $appRepo;
    protected $userRepo;
	protected $lmsRepo;
    protected $docRepo;

	public function __construct(InvAppRepoInterface $app_repo, InvUserRepoInterface $user_repo, InvLmsRepoInterface $lms_repo, InvDocumentRepoInterface $docRepo, MasterInterface $master){
		$this->appRepo = $app_repo;
        $this->userRepo = $user_repo;
		$this->lmsRepo = $lms_repo;
        $this->docRepo = $docRepo;
        $this->master = $master;
		$this->middleware('checkBackendLeadAccess');
        $this->middleware('checkEodProcess');
    }
      
    public function paymentAdvise(Request $request){
        try{
            $this->validate($request,[
                'payment_id'=>'required|integer',
                'apportionment_id'=>'required|integer',
            ],[
                'payment_id.required' => 'Payment Detail missing',
                'payment_id.integer' => 'Payment ID must be integre',
                'apportionment_id.required' => 'Apportionment Detail missing',
                'apportionment_id.integer' => 'Apportionment ID must be integre'

            ]);
            $paymentId = $request->get('payment_id');
            $apportionmentId = $request->get('apportionment_id');
            $data = RefundHelper::calculateRefund($paymentId,$apportionmentId);
            return view('lms.refund.createRefundRequest', $data);
        }catch(Exception $exception){
            return redirect()->back()->withErrors(Helpers::getExceptionMessage($ex))->withInput();
        }
    }

    public function createRefundRequest(Request $request){
        \DB::beginTransaction();
        try{
            if ($request->get('eod_process')) {
                \DB::rollback();
                Session::flash('error', trans('backend_messages.lms_eod_batch_process_msg'));
                return back();
            }
            $this->validate($request,[
                'paymentId'=>'required|integer',
                'apportionmentId'=>'required|integer',
            ],[
                'paymentId.required' => 'Payment Detail missing',
                'paymentId.required' => 'Payment ID must be integre',
                'apportionmentId.required' => 'Apportionment Detail missing',
                'apportionmentId.required' => 'Apportionment ID must be integre'
            ]);
            $paymentId = $request->get('paymentId');
            $refundData = RefundHelper::getRefundRqBypaymentIds($paymentId)->count();
            if($refundData > 0) {
                \DB::rollback();
                Session::flash('error', "Unable to process transaction, This has been already processed.");
                return back(); 
            }
            $apportionmentId = $request->get('apportionmentId');
            $data = RefundHelper::createRefundRequest($paymentId,$apportionmentId);

            $whereActivi['activity_code'] = 'lms_refund_request_create';
            $activity = $this->master->getActivity($whereActivi);
            if(!empty($activity)) {
                $activity_type_id = isset($activity[0]) ? $activity[0]->id : 0;
                $activity_desc = 'Payment Refund(Manage Payment) '. null;
                $arrActivity['app_id'] = null;
                $this->activityLogByTrait($activity_type_id, $activity_desc, response()->json(['data'=>$data, 'request'=>$request->all()]), $arrActivity);
            }               
            \DB::commit();
            Session::flash('msg', trans('Refund created successfully.'));
            Session::flash('is_accept', 1);
            return view('lms.refund.viewRefundRequest', $data);
        }catch(Exception $exception){
            \DB::rollback();
            return redirect()->back()->withErrors(Helpers::getExceptionMessage($ex))->withInput();
        }
    }

    public function refundListNew(){
        return view('lms.refund.refund_new');
    }
    
    public function refundListPending(){
        return view('lms.refund.refund_pending');
    }
    
    public function refundListApproved(){
        return view('lms.refund.refund_approved');
    }

    public function refundListQueue(){
        return view('lms.refund.refund_queue');
    }

    public function refundListSentBank(){
		return view('lms.refund.refund_sentbank');
    }

    public function refundListRefunded(){
		return view('lms.refund.refund_refunded');
    }

    public function viewRefundRequest(Request $request){
        try{
            if($request->has('req_id')){
                $refundReqId = $request->get('req_id');
                $data = RefundHelper::getRequest($refundReqId);
                return view('lms.refund.viewRefundRequest', $data);
            }else{
                return redirect()->back()->withErrors(Helpers::getExceptionMessage($ex))->withInput();
            }
        }catch(Exception $exception){
            return redirect()->back()->withErrors(Helpers::getExceptionMessage($ex))->withInput();
        }
    }

    public function updateRequestStatus(Request $request){
        DB::beginTransaction();
        try{
            if ($request->get('eod_process')) {
                \DB::rollback();
                Session::flash('error', trans('backend_messages.lms_eod_batch_process_msg'));
                return back();
            }
            
            $refundRequests = $request->refundRequest;
            $status = $request->status;
            $newStatus  = $request->newStatus;
            $ref_code ='';
            $refunddatas  = RefundHelper::getRefundRqByIds($refundRequests,$status)->count();
            if(count($refundRequests) > $refunddatas) {
                \DB::rollback();
                Session::flash('error', 'We are unable to process the selected transaction as some transaction has been already processed.');
                return back();
            }

            foreach ($refundRequests as $key => $reqId) {
                RefundHelper::updateRequest($reqId, $status, $newStatus);            
            }
            $redirectRoute = null;
            $message = null;
            switch ($newStatus) {
                case '1': //New Request,
                    $redirectRoute = 'lms_refund_new';
                    $message = "New Request Created Successfully!";
                    break;
                case '2': //Deleted
                $redirectRoute = null;
                    break;
                case '3': //Pending
                $redirectRoute = 'lms_refund_pending';
                $message = "Successfully Submitted your Request!";
                    break;
                case '4': //Rejected
                $redirectRoute = null;
                    break;
                case '5': //Approved
                $redirectRoute = 'lms_refund_approved';
                $message = "Successfully Approved your Request!";
                    break;
                case '6': //Refund Queue
                $redirectRoute = 'request_list';
                $message = "Successfully Refund queue your Request!";
                    break;
                case '7': //Sent to Bank
                $redirectRoute = 'lms_refund_sentbank';
                $message = "Successfully Sent To Bank your Request!";
                    break;
                case '8': //Disbursed
                $redirectRoute = 'lms_refund_refunded';
                $message = "Successfully Refunded your Request!";
                    break;
            }

            $whereActivi['activity_code'] = 'lms_refund_request_udate';
            $activity = $this->master->getActivity($whereActivi);
            if(!empty($activity)) {
                $activity_type_id = isset($activity[0]) ? $activity[0]->id : 0;
                $activity_desc = 'Update Request Status (Manage Refund) '. null;
                $arrActivity['app_id'] = null;
                $this->activityLogByTrait($activity_type_id, $activity_desc, response()->json($request->all()), $arrActivity);
            }             
            DB::commit();
            Session::flash('message',$message);
            return redirect()->route($redirectRoute);
        }catch(Exception $exception){
            DB::rollback();
            return redirect()->back()->withErrors(Helpers::getExceptionMessage($ex))->withInput();
        }
    }

    public function refundConfirm(Request $request){
        try{
            if ($request->get('eod_process')) {
                Session::flash('error', trans('backend_messages.lms_eod_batch_process_msg'));
                return back();
            }
            $refundType = $request->get('disburse_type');
            $reqIds = $request->get('transaction_ids');
            $record = array_filter(explode(",",$reqIds));
            if(empty($record))  {
                Session::flash('error', trans('backend_messages.noSelectedCustomer'));
                Session::flash('operation_status', 1);
                return redirect()->back();
            }
            $allrecords = array_unique($record);
            $allrecords = array_map('intval', $allrecords);

            $data = $this->lmsRepo->lmsGetCustomerRefund($allrecords);
            //dd($data);
            return view('lms.refund.refund_confirm')->with(['data'=>$data, 'transIds'=>$reqIds]); 
        }catch(Exception $exception){
            return redirect()->back()->withErrors(Helpers::getExceptionMessage($ex))->withInput();
        }
    }

    public function refundOffline(Request $request)
    {
        \DB::beginTransaction();
        try{
            if ($request->get('eod_process')) {
                \DB::rollback();
                Session::flash('error', trans('backend_messages.lms_eod_batch_process_msg'));
                return back();
            }
            
            $transactionIds = $request->get('transaction_ids');
            $record = array_filter(explode(",",$transactionIds));
            $disburseDate = $request->get('disburse_date');
            $refundType = config('lms.DISBURSE_TYPE')['OFFLINE'];
            $creatorId = Auth::user()->user_id;
            $validator = Validator::make($request->all(), [
            'disburse_date' => 'required'
            ]);
            
            if ($validator->fails()) {
                \DB::rollback();
                Session::flash('error', $validator->messages()->first());
                return redirect()->back()->withInput();
            }

            if(empty($record)){
                return redirect()->route('request_list');
            }
            $allrecords = array_unique($record);
            $allrecords = array_map('intval', $allrecords);
            $allAprvls = $this->lmsRepo->getAprvlRqDataByIds($allrecords)->toArray();
            $supplierIds = $this->lmsRepo->getAprvlRqUserByIds($allrecords)->toArray();
            $refundRequestNumbers = '';
            foreach ($allAprvls as $aprvl) {
                $refundReqId = $this->lmsRepo->findRefundByRefundReqId($aprvl['refund_req_id']);
                if($refundReqId->count() > 0) {
                    $refundRequestNumbers.= $aprvl['ref_code'].", ";
               } elseif($aprvl['payment']['user']['is_buyer'] == 2 && empty($aprvl['payment']['user']['anchor_bank_details'])){
                    \DB::rollback();
                    return redirect()->route('request_list')->withErrors(trans('backend_messages.noBankAccount'));
                } elseif ($aprvl['payment']['user']['is_buyer'] == 1 && empty($aprvl['payment']['lms_user']['bank_details'])) {
                    \DB::rollback();
                    return redirect()->route('request_list')->withErrors(trans('backend_messages.noBankAccount'));
                }
            }
            if($refundRequestNumbers!='') {
                \DB::rollback();
                return redirect()->route('request_list')->withErrors('Unable to process transaction as following transactions '.$refundRequestNumbers.' has been already processed.');
            }
            $this->refundUpdation($allrecords, $supplierIds, $allAprvls, $disburseDate, $refundType);
            $whereActivi['activity_code'] = 'refund_offline';
            $activity = $this->master->getActivity($whereActivi);
            if(!empty($activity)) {
                $activity_type_id = isset($activity[0]) ? $activity[0]->id : 0;
                $activity_desc = 'Refund Offline, Refund Queue (Manage Refund) '. null;
                $arrActivity['app_id'] = null;
                $this->activityLogByTrait($activity_type_id, $activity_desc, response()->json(['allrecords'=>$allrecords, 'supplierIds'=>$supplierIds, 'allAprvls'=>$allAprvls, 'disburseDate'=>$disburseDate, 'refundType'=>$refundType]), $arrActivity);
            }
            \DB::commit(); 
            Session::flash('message',trans('backend_messages.proccessed'));
            return redirect()->route('lms_refund_sentbank');
        }catch(Exception $exception){
            \DB::rollback();
            return redirect()->back()->withErrors(Helpers::getExceptionMessage($ex))->withInput();
        }
    }

    public function refundOnline(Request $request)
    {
        \DB::beginTransaction();
        try{
            if ($request->get('eod_process')) {
                \DB::rollback();
                Session::flash('error', trans('backend_messages.lms_eod_batch_process_msg'));
                return back();
            }
            $validateTimeHour = config('lms.DISBURSAL_TIME_VALIDATE');
            if (date('H') >= $validateTimeHour) { 
                \DB::rollback();
                Session::flash('error', 'Disbursment can not be done after '. Carbon::createFromFormat('H', $validateTimeHour)->format('g:i A'));
                return redirect()->route('request_list');
            }
            date_default_timezone_set("Asia/Kolkata");
            $transactionIds = $request->get('transaction_ids');
            $refundType = config('lms.DISBURSE_TYPE')['ONLINE'];
            $record = array_filter(explode(",",$transactionIds));
            $disburseDate =  \Helpers::getSysStartDate();
            $creatorId = Auth::user()->user_id;

            if(empty($record)){
                \DB::rollback();
                return redirect()->route('request_list');
            }
            $allrecords = array_unique($record);
            $allrecords = array_map('intval', $allrecords);
            $allAprvls = $this->lmsRepo->getAprvlRqDataByIds($allrecords)->toArray();
            $supplierIds = $this->lmsRepo->getAprvlRqUserByIds($allrecords)->toArray();

            foreach ($allAprvls as $aprvl) {
                if($aprvl['payment']['user']['is_buyer'] == 2 && empty($aprvl['payment']['user']['anchor_bank_details'])){
                    \DB::rollback();
                    return redirect()->route('request_list')->withErrors(trans('backend_messages.noBankAccount'));
                } elseif ($aprvl['payment']['user']['is_buyer'] == 1 && empty($aprvl['payment']['lms_user']['bank_details'])) {
                    \DB::rollback();
                    return redirect()->route('request_list')->withErrors(trans('backend_messages.noBankAccount'));
                }
            }

            $requestData = [];
            $aprvlRfd = [];
            $transId = _getRand(18);
            
            foreach ($supplierIds as $userId) {
                $refNo = _getRand(12);
                $disburseAmount = 0;
                $userId = $userId['user_id'];
                foreach ($allAprvls as $aprvl) {
                    if($aprvl['payment']['user_id'] == $userId) {
                        $userid = $aprvl['payment']['user']['user_id'];
                        $disburseAmount += round($aprvl['refund_amount'], 5);

                        $aprvlRfd['bank_account_id'] = ($aprvl['payment']['user']['is_buyer'] == 2) ? $aprvl['payment']['user']['anchor_bank_details']['bank_account_id'] : $aprvl['payment']['lms_user']['bank_details']['bank_account_id'];
                        $aprvlRfd['refund_date'] = (!empty($disburseDate)) ? date("Y-m-d h:i:s", strtotime(str_replace('/','-',$disburseDate))) : \Carbon\Carbon::now()->format('Y-m-d h:i:s');
                        $aprvlRfd['bank_name'] = ($aprvl['payment']['user']['is_buyer'] == 2) ? $aprvl['payment']['user']['anchor_bank_details']['bank']['bank_name'] : $aprvl['payment']['lms_user']['bank_details']['bank']['bank_name'] ;
                        $aprvlRfd['ifsc_code'] = ($aprvl['payment']['user']['is_buyer'] == 2) ? $aprvl['payment']['user']['anchor_bank_details']['ifsc_code'] : $aprvl['payment']['lms_user']['bank_details']['ifsc_code'];
                        $aprvlRfd['acc_no'] = ($aprvl['payment']['user']['is_buyer'] == 2) ? $aprvl['payment']['user']['anchor_bank_details']['acc_no'] : $aprvl['payment']['lms_user']['bank_details']['acc_no'];           
                        
                    }
                } 
                $modePay = ($disburseAmount < 200000) ? 'NEFT' : 'RTGS' ;
                $requestData[$userId]['RefNo'] = $refNo;
                $requestData[$userId]['Amount'] = $disburseAmount;
                $requestData[$userId]['Debit_Acct_No'] = config('lms.IDFC_PROD.IDFC_DEBIT_BANK')['DEBIT_ACC_NO'];
                $requestData[$userId]['Debit_Acct_Name'] = config('lms.IDFC_PROD.IDFC_DEBIT_BANK')['DEBIT_ACC_NAME'];
                $requestData[$userId]['Debit_Mobile'] = config('lms.IDFC_PROD.IDFC_DEBIT_BANK')['DEBIT_MOBILE'];
                $requestData[$userId]['Ben_IFSC'] = $aprvlRfd['ifsc_code'];
                $requestData[$userId]['Ben_Acct_No'] = $aprvlRfd['acc_no'];
                // $requestData[$userid]['Ben_IFSC'] = 'UTIB0000001';
                // $requestData[$userid]['Ben_Acct_No'] = '21480314831';
                $requestData[$userId]['Ben_Name'] = $aprvl['payment']['user']['f_name'].' '.$aprvl['payment']['user']['l_name'];
                $requestData[$userId]['Ben_BankName'] = $aprvlRfd['bank_name'];
                $requestData[$userId]['Ben_Email'] = $aprvl['payment']['user']['email'];
                $requestData[$userId]['Ben_Mobile'] = $aprvl['payment']['user']['mobile_no'];
                $requestData[$userId]['Mode_of_Pay'] = $modePay;
                $requestData[$userId]['Nature_of_Pay'] = 'MPYMT';
                $requestData[$userId]['Remarks'] = 'test remarks';

            }
            if(!empty($allrecords)) {
                
                $http_header = [
                    'timestamp' => date('Y-m-d H:i:s',strtotime($disburseDate)),
                    'txn_id' => $transId
                    ];

                $header = [
                    'Maker_ID' => "CAPSAVE.M",
                    'Checker_ID' => "CAPSAVE.C1",
                    'Approver_ID' => "CAPSAVE.C2"
                    ];

                $params = [
                    'http_header' => $http_header,
                    'header' => $header,
                    'request' => $requestData
                    ];
                $idfcObj= new Idfc_lib();
                $result = $idfcObj->api_call(Idfc_lib::MULTI_PAYMENT, $params);
                // dd($result);
                if ($result['status'] == 'success') {

                    $fileDirPath = getPathByTxnId($transId);
                    $time = date('y-m-d H:i:s',strtotime($disburseDate));
                    
                    $result['result']['http_header'] = (is_array($result['result']['http_header'])) ? json_encode($result['result']['http_header']): $result['result']['http_header'];
                    $fileContents = PHP_EOL .' Log  '.$time .PHP_EOL. $result['result']['url'].  PHP_EOL
                        .PHP_EOL .' Log  '.$time .PHP_EOL. $result['result']['payload']  .PHP_EOL
                        .PHP_EOL .' Log  '.$time .PHP_EOL. $result['result']['http_header']  .PHP_EOL
                        .PHP_EOL .' Log  '.$time .PHP_EOL. $result['result']['response'] . PHP_EOL;
                    
                    $createOrUpdatefile = Helpers::uploadOrUpdateFileWithContent($fileDirPath, $fileContents, true);
                    // dd($createOrUpdatefile);
                    if(is_array($createOrUpdatefile)) {
                        $userFileSaved = $this->docRepo->saveFile($createOrUpdatefile)->toArray();
                    } else {
                        $userFileSaved = $createOrUpdatefile;
                    }
                    
                    $otherData['bank_type'] = config('lms.BANK_TYPE')['IDFC'];
                    $disbusalApiLogData = $this->createDisbusalApiLogData($userFileSaved, $result['result'], $otherData);
                    $createDisbusalApiLog = $this->lmsRepo->saveUpdateDisbursalApiLog($disbusalApiLogData);
                    if ($createDisbusalApiLog) {
                        $disbursalApiLogId = $createDisbusalApiLog->disbursal_api_log_id;
                    }


                    $this->refundUpdation($allrecords, $supplierIds, $allAprvls, $disburseDate, $refundType, $disbursalApiLogId);
                } else {
                    \DB::rollback();
                    Session::flash('message',trans('backend_messages.disbursed_error'));
                    return redirect()->route('request_list')->withErrors('message',trans('backend_messages.disbursed_error'));
                }
            }
            $whereActivi['activity_code'] = 'refund_online';
            $activity = $this->master->getActivity($whereActivi);
            if(!empty($activity)) {
                $activity_type_id = isset($activity[0]) ? $activity[0]->id : 0;
                $activity_desc = 'Refund Online, Refund Queue (Manage Refund) '. null;
                $arrActivity['app_id'] = null;
                $this->activityLogByTrait($activity_type_id, $activity_desc, response()->json(['allrecords'=>$allrecords, 'supplierIds'=>$supplierIds, 'allAprvls'=>$allAprvls, 'disburseDate'=>$disburseDate, 'refundType'=>$refundType, 'disbursalApiLogId'=>$disbursalApiLogId]), $arrActivity);
            }
            \DB::commit(); 
            Session::flash('message',trans('backend_messages.proccessed'));
            return redirect()->route('lms_refund_sentbank');
        }catch(Exception $exception){
            \DB::rollback();
            return redirect()->back()->withErrors(Helpers::getExceptionMessage($ex))->withInput();
        }
    }

    public function refundUpdation($refundIds = [], $supplierIds = [], $allAprvls = [], $disburseDate = null, $refundType = null, $disbursalApiLogId = null)
    {
        \DB::beginTransaction();
        try{
        $exportData = [];
        $aprvlRfd = [];
        $aprvlRfdProcess = [];
        $batchNo = _getRand(12);

        foreach ($supplierIds as $userId) {
            $refNo = _getRand(12);
            $disburseAmount = 0;
            $userId = $userId['user_id'];
            foreach ($allAprvls as $aprvl) {
                if($aprvl['payment']['user_id'] == $userId) {
                    $userid = $aprvl['payment']['user']['user_id'];
                    $disburseAmount += round($aprvl['refund_amount'], 5);

                    $refId = $aprvl['payment']['lms_user']['virtual_acc_id'];

                    $aprvlRfd['bank_account_id'] = ($aprvl['payment']['user']['is_buyer'] == 2) ? $aprvl['payment']['user']['anchor_bank_details']['bank_account_id'] : $aprvl['payment']['lms_user']['bank_details']['bank_account_id'];
                    $aprvlRfd['refund_date'] = (!empty($disburseDate)) ? date("Y-m-d h:i:s", strtotime(str_replace('/','-',$disburseDate))) : \Carbon\Carbon::now()->format('Y-m-d h:i:s');
                    $aprvlRfd['bank_name'] = ($aprvl['payment']['user']['is_buyer'] == 2) ? $aprvl['payment']['user']['anchor_bank_details']['bank']['bank_name'] : $aprvl['payment']['lms_user']['bank_details']['bank']['bank_name'] ;
                    $aprvlRfd['ifsc_code'] = ($aprvl['payment']['user']['is_buyer'] == 2) ? $aprvl['payment']['user']['anchor_bank_details']['ifsc_code'] : $aprvl['payment']['lms_user']['bank_details']['ifsc_code'];
                    $aprvlRfd['acc_no'] = ($aprvl['payment']['user']['is_buyer'] == 2) ? $aprvl['payment']['user']['anchor_bank_details']['acc_no'] : $aprvl['payment']['lms_user']['bank_details']['acc_no'];           
                    $aprvlRfd['status'] = 7;
                    $aprvlRfd['process_status'] = RefundReq::REDUND_PENDING;
                    
                    if (isset($aprvlRfd)) {
                        $refundDisbursed = $this->lmsRepo->updateAprvlRqst($aprvlRfd, $aprvl['refund_req_id']);
                    }
                    if (isset($refundDisbursed)) {
                        $aprvlRfdProcess['process_status'] = RefundReq::REDUND_PROCESSING;
                        $this->lmsRepo->updateAprvlRqst($aprvlRfdProcess, $aprvl['refund_req_id']);
                    }

                }
            }
            $modePay = ($disburseAmount < 200000) ? 'NEFT' : 'RTGS' ;
            $exportData[$userId]['RefNo'] = $refId;
            $exportData[$userId]['Amount'] = $disburseAmount;
            $exportData[$userId]['Debit_Acct_No'] = '12334445511111';
            $exportData[$userId]['Debit_Acct_Name'] = 'testing name';
            $exportData[$userId]['Debit_Mobile'] = '9876543210';
            // $exportData[$userId]['Ben_IFSC'] = $aprvlRfd['ifsc_code'];
            $exportData[$userid]['Ben_IFSC'] = 'UTIB0000001';
            // $exportData[$userId]['Ben_Acct_No'] = $aprvlRfd['acc_no'];
            $exportData[$userid]['Ben_Acct_No'] = '21480314831';
            $exportData[$userId]['Ben_BankName'] = $aprvlRfd['bank_name'];
            $exportData[$userId]['Ben_Name'] = $aprvl['payment']['user']['f_name'].' '.$aprvl['payment']['user']['l_name'];
            $exportData[$userId]['Ben_Email'] = $aprvl['payment']['user']['email'];
            $exportData[$userId]['Ben_Mobile'] = $aprvl['payment']['user']['mobile_no'];
            $exportData[$userId]['Mode_of_Pay'] = $modePay;
            $exportData[$userId]['Nature_of_Pay'] = 'MPYMT';
            $exportData[$userId]['Remarks'] = 'refund';
            $exportData[$userId]['Value_Date'] = date('Y-m-d');
        }
        $result = $this->export($exportData, $batchNo);
        $file['file_path'] = ($result['file_path']) ?? null;
        if ($file) {
            $createBatchFileData = $this->createBatchFileData($file);
            $createBatchFile = $this->lmsRepo->saveBatchFile($createBatchFileData);
            if ($createBatchFile) {
                $data['batch_no'] = $batchNo;
                $data['refund_type'] = $refundType ?? null;
                $data['disbursal_api_log_id'] = $disbursalApiLogId ?? null;
                $createBatch = $this->lmsRepo->createRefundBatch($createBatchFile, $data);
                if($createBatch) {
                    foreach ($refundIds as $key => $value) {
                        $refundData = $this->lmsRepo->lmsGetCustomerRefundById($value);
                        if($exportData[$refundData->payment->user_id]['RefNo']!=NULL) {
                                $updateDisbursal = $this->lmsRepo->updateAprvlRqst([
                                        'refund_req_batch_id' => $createBatch->refund_req_batch_id,
                                        'tran_no' => $exportData[$refundData->payment->user_id]['RefNo']
                                    ], $value);
                                }
                    }
                }
            }
        }
        \DB::commit();
        return true;
    }catch(Exception $exception){
        \DB::rollback();
        return redirect()->back()->withErrors(Helpers::getExceptionMessage($ex))->withInput();
    }

        
    }

    public function downloadSentBank()
    {
        $allAprvls = $this->lmsRepo->getAprvlRqDataByIds()->toArray();
        $downloadFlag = 1;
        $exportData = [];
        $filename = 'download-excel';
        foreach ($allAprvls as $aprvl) {
            $userid = $aprvl['payment']['user']['user_id'];
            $disburseAmount = round($aprvl['refund_amount'], 5);

            $exportData[$aprvl['refund_req_id']]['RefNo'] = $aprvl['payment']['lms_user']['virtual_acc_id'];
            $exportData[$aprvl['refund_req_id']]['Amount'] = round($aprvl['refund_amount'], 5);
            $exportData[$aprvl['refund_req_id']]['Debit_Acct_No'] = '12334445511111';
            $exportData[$aprvl['refund_req_id']]['Debit_Acct_Name'] = 'testing name';
            $exportData[$aprvl['refund_req_id']]['Debit_Mobile'] = '9876543210';
            $exportData[$aprvl['refund_req_id']]['Ben_IFSC'] = $aprvl['ifsc_code'];
            $exportData[$aprvl['refund_req_id']]['Ben_Acct_No'] = $aprvl['acc_no'];
            $exportData[$aprvl['refund_req_id']]['Ben_BankName'] = $aprvl['bank_name'];
            $exportData[$aprvl['refund_req_id']]['Ben_Name'] = $aprvl['payment']['user']['f_name'].' '.$aprvl['payment']['user']['l_name'];
            $exportData[$aprvl['refund_req_id']]['Ben_Email'] = $aprvl['payment']['user']['email'];
            $exportData[$aprvl['refund_req_id']]['Ben_Mobile'] = $aprvl['payment']['user']['mobile_no'];
            $exportData[$aprvl['refund_req_id']]['Mode_of_Pay'] = 'IFT';
            //$exportData[$aprvl['refund_req_id']]['Mode_of_Pay'] = 'BT';
            $exportData[$aprvl['refund_req_id']]['Nature_of_Pay'] = 'MPYMT';
            $exportData[$aprvl['refund_req_id']]['Remarks'] = 'test remarks';
            $exportData[$aprvl['refund_req_id']]['Value_Date'] = date('Y-m-d');
        }
        $result = $this->export($exportData, $filename, $downloadFlag);

    }

    public function export($data, $filename, $downloadFlag = 0) {
        $sheet =  new PHPExcel();
        $sheet->getProperties()
                ->setCreator("Capsave")
                ->setLastModifiedBy("Capsave")
                ->setTitle("Bank Disburse Excel")
                ->setSubject("Bank Disburse Excel")
                ->setDescription("Bank Disburse Excel")
                ->setKeywords("Bank Disburse Excel")
                ->setCategory("Bank Disburse Excel");
    
        $sheet->setActiveSheetIndex(0)
                ->setCellValue('A1', 'Client Code')
                ->setCellValue('B1', 'Debit account no.')
                ->setCellValue('C1', 'Transaction type code')
                ->setCellValue('D1', 'Value date')
                ->setCellValue('E1', 'Amount')
                ->setCellValue('F1', 'Beneficary Name')
                ->setCellValue('G1', 'Beneficary Accunt no.')
                ->setCellValue('H1', 'IFSC code')
                ->setCellValue('I1', 'Customer Ref no.')
                ->setCellValue('J1', 'Beneficary email id')
                ->setCellValue('K1', 'Beneficiary mobile no.')
                ->setCellValue('L1', 'Remarks')
                ->setCellValue('M1', 'Payment Type')
                ->setCellValue('N1', 'Purpose code')
                ->setCellValue('O1', 'Bene a/c type')
                ->setCellValue('P1', 'Payable Location')
                ->setCellValue('Q1', 'Print branch name')
                ->setCellValue('R1', 'Mode of delivery')
                ->setCellValue('S1', 'Transaction currency')
                ->setCellValue('T1', 'BENE_ADD1')
                ->setCellValue('U1', 'BENE_ADD2')
                ->setCellValue('V1', 'BENE_ADD3')
                ->setCellValue('W1', 'BENE_ADD4')
                ->setCellValue('X1', 'Beneficiary ID')
                ->setCellValue('Y1', 'Remote Printing')
                ->setCellValue('Z1', 'Print Branch Location')
                ->setCellValue('AA1', 'Nature Of Payment');
        $rows = 2;

        foreach($data as $rowData){
            $sheet->setActiveSheetIndex(0)
                ->setCellValue('A' . $rows, $rowData['Client_Code'] ?? 'XYZ')
                ->setCellValue('B' . $rows, $rowData['Debit_Acct_No'] ?? '')
                ->setCellValue('C' . $rows, $rowData['Trans_Type_Code'] ?? '')
                ->setCellValue('D' . $rows, $rowData['Value_Date'] ?? '')
                ->setCellValue('E' . $rows, $rowData['Amount'] ?? '')
                ->setCellValue('F' . $rows, $rowData['Ben_Name'] ?? '')
                ->setCellValue('G' . $rows, $rowData['Ben_Acct_No'] ?? '')
                ->setCellValue('H' . $rows, $rowData['Ben_IFSC'] ?? '')
                ->setCellValue('I' . $rows, $rowData['RefNo'] ?? '')
                ->setCellValue('J' . $rows, $rowData['Ben_Email'] ?? '')
                ->setCellValue('K' . $rows, $rowData['Ben_Mobile'] ?? '')
                ->setCellValue('L' . $rows, $rowData['Remarks'] ?? '')
                ->setCellValue('M' . $rows, $rowData['Mode_of_Pay'] ?? '')
                ->setCellValue('N' . $rows, $rowData['column'] ?? '')
                ->setCellValue('O' . $rows, $rowData['column'] ?? '')
                ->setCellValue('P' . $rows, $rowData['column'] ?? '')
                ->setCellValue('Q' . $rows, $rowData['column'] ?? '')
                ->setCellValue('R' . $rows, $rowData['column'] ?? '')
                ->setCellValue('S' . $rows, $rowData['column'] ?? '')
                ->setCellValue('T' . $rows, $rowData['column'] ?? '')
                ->setCellValue('U' . $rows, $rowData['column'] ?? '')
                ->setCellValue('V' . $rows, $rowData['column'] ?? '')
                ->setCellValue('W' . $rows, $rowData['column'] ?? '')
                ->setCellValue('X' . $rows, $rowData['column'] ?? '')
                ->setCellValue('Y' . $rows, $rowData['column'] ?? '')
                ->setCellValue('Z' . $rows, $rowData['column'] ?? '')
                ->setCellValue('AA' . $rows, $rowData['Nature_of_Pay'] ?? '');

            $rows++;
        }

        // Redirect output to a client’s web browser (Excel2007)
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="'.$filename.'.xlsx"');
        header('Cache-Control: max-age=0');
        // If you're serving to IE 9, then the following may be needed
        // header('Cache-Control: max-age=1');

        // // If you're serving to IE over SSL, then the following may be needed
        // header ('Expires: Mon, 26 Jul 1997 05:00:00 GMT'); // Date in the past
        // header ('Last-Modified: '.gmdate('D, d M Y H:i:s').' GMT'); // always modified
        // header ('Cache-Control: cache, must-revalidate'); // HTTP/1.1
        // header ('Pragma: public'); // HTTP/1.0
        if ($downloadFlag == 0) {
            if (!Storage::exists('/public/docs/bank_excel')) {
                Storage::makeDirectory('/public/docs/bank_excel');
            }
            $storage_path = storage_path('app/public/docs/bank_excel');
            $filePath = $storage_path.'/'.$filename.'.xlsx';
           $objWriter = PHPExcel_IOFactory::createWriter($sheet, 'Excel2007');
            $objWriter->save($filePath);

            return [ 'status' => 1,
                'file_path' => $filePath
            ];
        } else {
            $objWriter = PHPExcel_IOFactory::createWriter($sheet, 'Excel2007');
            $objWriter->save('php://output');
        }
    }

    public function refundUpdateDisbursal(Request $request){
        try{
            if ($request->get('eod_process')) {
                Session::flash('error', trans('backend_messages.lms_eod_batch_process_msg'));
                return back();
            }
            $payment_id = $request->get('payment_id');
            $refund_req_batch_id = $request->get('refund_req_batch_id');
            $refund_req_id = $request->get('refund_req_id');
            return view('lms.refund.update_refund_disbursal')
                ->with([
                    'payment_id' => $payment_id, 
                    'refund_req_batch_id' => $refund_req_batch_id,
                    'refund_req_id' => $refund_req_id
                ]);
        }catch(Exception $exception){
            return redirect()->back()->withErrors(Helpers::getExceptionMessage($ex))->withInput();
        }
    }
    
    public function updateDisburseRefund(Request $request) {
        DB::beginTransaction();
        try{
            if ($request->get('eod_process')) {
                DB::rollback();
                Session::flash('error', trans('backend_messages.lms_eod_batch_process_msg'));
                return back();
            }

            $validator = Validator::make($request->all(), [
                'trans_no' => 'unique:lms_refund_req,tran_no',
            ]);
            if ($validator->fails()) {
                DB::rollback();
                return redirect()->route('lms_refund_sentbank')->withErrors('Transaction number already exist.');
            }

            $allrecords[] =  $request->refund_req_id;
            $data = $this->lmsRepo->lmsGetCustomerRefund($allrecords);
            if(in_array($data[0]['process_status'], [RefundReq::REDUND_PROCESSED,RefundReq::REDUND_COMPLETED])) {
                DB::rollback();
                return redirect()->route('lms_refund_sentbank')->withErrors('Unable to process transaction as this transaction has been already processed.');
            }
            $transNo = $request->trans_no;
            $remarks = $request->remarks;
            $refund_req_id = $request->refund_req_id;
            $disburse_date = $request->disburse_date;
            $actual_refund_date = (!empty($disburse_date)) ? date("Y-m-d", strtotime(str_replace('/','-',$disburse_date))) : \Carbon\Carbon::now()->format('Y-m-d h:i:s');

            $apiLogData = [];
            $apiProcesData = [];
            $apiLogData['tran_no'] = $transNo;
            $apiLogData['comment'] = $remarks;
            $apiLogData['actual_refund_date'] = $actual_refund_date;
            $apiLogData['status'] = 8;
            $apiLogData['process_status'] = RefundReq::REDUND_PROCESSED;
            $this->lmsRepo->updateAprvlRqst($apiLogData,$refund_req_id);
            $this->finalRefundTransactions($refund_req_id, $actual_refund_date);
            $whereActivi['activity_code'] = 'updateDisburseRefund';
            $activity = $this->master->getActivity($whereActivi);
            if(!empty($activity)) {
                $activity_type_id = isset($activity[0]) ? $activity[0]->id : 0;
                $activity_desc = 'Update Disbursal Refund, Send TO Bank (Manage Refund) '. null;
                $arrActivity['app_id'] = null;
                $this->activityLogByTrait($activity_type_id, $activity_desc, response()->json(['apiLogData'=>$apiLogData, 'Request'=>$request->all()]), $arrActivity);
            }
            $apiProcesData['process_status'] = RefundReq::REDUND_COMPLETED;
            $this->lmsRepo->updateAprvlRqst($apiProcesData,$refund_req_id);
            DB::commit();     
            Session::flash('message',trans('backend_messages.refundedMarked'));
            return redirect()->route('lms_refund_refunded');
        }catch(Exception $exception){
            DB::rollback();
            return redirect()->back()->withErrors(Helpers::getExceptionMessage($ex))->withInput();
        }
    }
	/**
	 * Display a listing of the refund.
	 *
	 * @return \Illuminate\Http\Response
	 */
	public function customerList()
	{
		return view('lms.common.view_customer');              
	}

	/**
	 * Display confirm dialogue.
	 *
	 * @return \Illuminate\Http\Response
	 */
	public function confirmRefund(Request $request)
	{
		return view('lms.refund.confirm_refund');              
	}


	public function sendRefund(Request $request)
	{
            if ($request->get('eod_process')) {
                Session::flash('error', trans('backend_messages.lms_eod_batch_process_msg'));
                return back();
            }
        
		$transId = $request->trans_id;
		$disburseIds = $request->disbursal_ids;

		$record = array_filter(explode(",",$disburseIds));

		$allrecords = array_unique($record);
		$allrecords = array_map('intval', $allrecords);

		$allDisburse = $this->lmsRepo->getDisbursals($allrecords)->toArray();
		foreach ($allDisburse as $disbursal) {
			$refundMstId = (config('lms')['TRANS_TYPE']['PAYMENT_REVERSE']) ?? 2;
			$data = [];
			$data['amount'] = $disbursal['surplus_amount'];
			$data['disbursal_id'] = $disbursal['disbursal_id'];
			$transactionData = $this->createTransactionData($disbursal['user_id'],  $data, $transId, $refundMstId);
			$createTransaction = $this->lmsRepo->saveTransaction($transactionData);

			if (!empty($createTransaction)) {
				$disburseData = [];
				$disburseData['surplus_amount'] = 0;
				$updateDisburse = $this->lmsRepo->updateDisburse($disburseData, $disbursal['disbursal_id']);
			}
		}
		
		if (empty($allrecords)) {
			return redirect()->route('lms_refund_list')->withErrors(trans('backend_messages.noSelectedInvoice'));
		}
        
        Session::flash('message',trans('backend_messages.refunded'));
		return redirect()->route('lms_refund_list');
	}

	public function createBatch(Request $request) 
	{
		return view('lms.common.create_request', $request->all());
	}
	
	public function editBatch(Request $request) 
	{
		return view('lms.common.edit_request', $request->all());
	}
        
	public function moveReqToNextStage(Request $request) 
	{
            $reqId = $request->get('req_id');
            $back_stage = '';
            $next_stage = '';
            $back_stage_data = null;
            $next_stage_data = null;
            if ($request->has('back_stage')) {
                $back_stage = '1';
                $back_stage_data = $this->getRequestPrevStage($reqId);
            } else {
                $next_stage = '1';
                $next_stage_data = $this->getRequestNextStage($reqId);
            }
            return view('lms.common.move_next_stage')
                    ->with('reqId', $reqId)
                    ->with('back_stage', $back_stage)
                    ->with('back_stage_data', $back_stage_data)
                    ->with('next_stage', $next_stage)
                    ->with('next_stage_data', $next_stage_data);
                    
	}

        public function acceptReqStage(Request $request)
        {
            if ($request->get('eod_process')) {
                Session::flash('error', trans('backend_messages.lms_eod_batch_process_msg'));
                return back();
            }
        
            $reqId = $request->get('req_id');
            $isBackStage = $request->has('back_stage') && !empty($request->get('back_stage')) ? true : false;
            $comment = $request->get('sharing_comment');
            
            try {    
                
                //if(count($reqdDocs) == 0)  {
                //    Session::flash('error_code', 'no_docs_found');
                //    return redirect()->back();                                            
                //
                
                $addlData=[];
                $addlData['sharing_comment'] = $comment;
                if ($isBackStage) {
                    //dd('$isBackStage', $isBackStage, $request->all());
                    $this->moveRequestToPrevStage($reqId, $addlData);
                } else {
                    //dd('$isNextStage', $request->all());
                    $this->moveRequestToNextStage($reqId, $addlData);
                }
                        
                Session::flash('is_accept', 1);
                return redirect()->back();

            } catch (Exception $ex) {
                return redirect()->back()->withErrors(Helpers::getExceptionMessage($ex));
            }
        }
        
        // public function updateRequestStatus(Request $request)
        // {
        //     $reqId = $request->get('req_id');
        //     $reqData = $this->lmsRepo->getApprRequestData($reqId);
        //     $curReqStatus = $reqData ? $reqData->status : '';
            
        //     $statusList = \Helpers::getRequestStatusList($reqId);
        //     $statusList = ['' => 'Select Status'] + $statusList;                
                           
        //     return view('lms.common.view_request_status')
        //             ->with('reqId', $reqId)
        //             ->with('statusList', $statusList);            
        // }
        
        public function saveRequestStatus(Request $request)
        {
            $reqId = $request->get('req_id');
            $reqStatus = $request->get('status');
            $comment = $request->get('comment');
            
            try {    
                
                //if(count($reqdDocs) == 0)  {
                //    Session::flash('error_code', 'no_docs_found');
                //    return redirect()->back();                                            
                //
                
                $addlData=[];
                $addlData['status'] = $reqStatus;
                $addlData['sharing_comment'] = $comment;
                $this->updateApprRequest($reqId, $addlData);
                        
                Session::flash('is_accept', 1);
                return redirect()->back();

            } catch (Exception $ex) {
                return redirect()->back()->withErrors(Helpers::getExceptionMessage($ex));
            }
        }
        
        public function viewProcessRefund(Request $request)
        {
            $reqId = $request->get('req_id');
            $viewFlag = $request->get('view');
            
            $reqData = $this->lmsRepo->getApprRequestData($reqId);
            $transId = $reqData ? $reqData->trans_id : '';
            $currStatus = $reqData ? $reqData->status : '';
            
            $refundData = $this->getRefundData($transId);
            
            $statusList = \Helpers::getRequestStatusList($reqId);
            $statusList = ['' => 'Select Status'] + $statusList;    
            
            return view('lms.common.refund_process')
                    ->with('reqId', $reqId)
                    ->with('refundData', $refundData)
                    ->with('currStatus', $currStatus)
                    ->with('statusList', $statusList)
                    ->with('viewFlag', $viewFlag); 
        }

        public function processRefund(Request $request)
        {
            if ($request->get('eod_process')) {
                Session::flash('error', trans('backend_messages.lms_eod_batch_process_msg'));
                return back();
            }
            
            $reqId = $request->get('req_id');
            $reqStatus = $request->get('status');
            $comment = $request->get('comment');
            
            try {    
                
                //if(count($reqdDocs) == 0)  {
                //    Session::flash('error_code', 'no_docs_found');
                //    return redirect()->back();                                            
                //
                if ($request->has('process') && !empty($request->get('process')) ) {
                    $reqData = $this->lmsRepo->getApprRequestData($reqId);
                    $curReqStatus = $reqData ? $reqData->status : '';
                    $trAmount = $reqData ? $reqData->amount : 0;
                    $userId = $reqData ? $reqData->user_id : 0;
                    $transId = $reqData ? $reqData->trans_id : 0;
                    /*
                    //Non Factored Amount
                    $nonFactoredAmtData = $this->lmsRepo->getRefundData($transId, 'NON_FACTORED');
                    if (count($nonFactoredAmtData) > 0) {
                        $trData = [];                
                        $trData['amount'] = isset($nonFactoredAmtData[0]) ? $nonFactoredAmtData[0]->amount : 0;
                        //$trData['payment_id'] = $transId;
                        $trData['soa_flag'] = 1;
                        $transType = config('lms.TRANS_TYPE.NON_FACTORED_AMT');
                        $ptrData = $this->createTransactionData($userId, $trData, null, $transType, $entryType = 0);
                        $this->appRepo->saveTransaction($ptrData);
                    }
                    
                    //Interest Refund Amount                    
                    $intRefundAmtData = $this->lmsRepo->getRefundData($transId, 'INTEREST_REFUND');
                    if (count($intRefundAmtData) > 0) {
                        $trData = [];                
                        $trData['amount'] = isset($intRefundAmtData[0]) ? $intRefundAmtData[0]->amount : 0;
                        //$trData['payment_id'] = $transId;
                        $trData['soa_flag'] = 1;
                        $transType = config('lms.TRANS_TYPE.INTEREST_REFUND');
                        $ptrData = $this->createTransactionData($userId, $trData, null, $transType, $entryType = 0);
                        $this->appRepo->saveTransaction($ptrData);
                    }
                    
                    //Margin Amount
                    $marginReleasedAmtData = $this->lmsRepo->getRefundData($transId, 'MARGIN_RELEASED');
                    if (count($marginReleasedAmtData) > 0) {
                        $trData = [];                
                        $trData['amount'] = isset($marginReleasedAmtData[0]) ? $marginReleasedAmtData[0]->amount : 0;
                        //$trData['payment_id'] = $transId;
                        $trData['soa_flag'] = 1;
                        $transType = config('lms.TRANS_TYPE.MARGIN');
                        $ptrData = $this->createTransactionData($userId, $trData, null, $transType, $entryType = 0);
                        $this->appRepo->saveTransaction($ptrData);
                    }*/
                    $addlData=[];
                    $addlData['status'] = config('lms.REQUEST_STATUS.REFUND_QUEUE');
                    $addlData['sharing_comment'] = $comment;
                    $this->updateApprRequest($reqId, $addlData);
                } else {
                    $addlData=[];
                    $addlData['status'] = $reqStatus;
                    $addlData['sharing_comment'] = $comment;
                    $this->updateApprRequest($reqId, $addlData);                    
                }
                
                Session::flash('is_accept', 1);
                return redirect()->back();

            } catch (Exception $ex) {
                return redirect()->back()->withErrors(Helpers::getExceptionMessage($ex));
            }            
        }

    public function refundRequest(Request $request)
    {
        try {

            if ($request->get('eod_process')) {
                Session::flash('error', trans('backend_messages.lms_eod_batch_process_msg'));
                return back();
            }
            $batchData = $this->lmsRepo->getallBatch();

            return view('lms.refund.refund_request')->with(['batchData' => $batchData]);
            } catch (Exception $ex) {
            return redirect()->back()->withErrors(Helpers::getExceptionMessage($ex));
        }
    }

    public function refundPaymentEnquiry(Request $request)
    {
        try {

            if ($request->get('eod_process')) {
                Session::flash('error', trans('backend_messages.lms_eod_batch_process_msg'));
                return back();
            }
            
            $refundBatchId = $request->get('refund_req_batch_id');
            $sysDate =  \Helpers::getSysStartDate();
            date_default_timezone_set("Asia/Kolkata");
            $data = $this->lmsRepo->getrefundBatchByDBId($refundBatchId)->toArray();
            $reqData['txn_id'] = $data['disbursal_api_log']['txn_id'];
            $transId = $reqData['txn_id'];
            // $transId = '2RGIK4436OUMXHZGXH';
            $createdBy = Auth::user()->user_id;
            $actual_refund_date = \Carbon\Carbon::now()->format('Y-m-d');
            $transDisbursalIds = [];
            $tranNewIds = [];

            if(!empty($reqData)) {
            
                $http_header = [
                    'timestamp' => date('Y-m-d H:i:s'),
                    'txn_id' => $reqData['txn_id']
                    ];

                $header = [
                    'Maker_ID' => "CAPSAVE.M",
                    'Checker_ID' => "CAPSAVE.C1",
                    'Approver_ID' => "CAPSAVE.C2"
                    ];

                $params = [
                    'http_header' => $http_header,
                    'header' => $header,
                    'request' => $reqData
                    ];

                $idfcObj= new Idfc_lib();
                $result = $idfcObj->api_call(Idfc_lib::BATCH_ENQ, $params);
                // dd($result);
                if ($result['status'] == 'success') {
                    $fileDirPath = getPathByTxnId($transId);
                    $time = date('y-m-d H:i:s');
                    
                    $result['result']['http_header'] = (is_array($result['result']['http_header'])) ? json_encode($result['result']['http_header']): $result['result']['http_header'];
                    $fileContents = PHP_EOL .' Log  '.$time .PHP_EOL. $result['result']['url'].  PHP_EOL
                        .PHP_EOL .' Log  '.$time .PHP_EOL. $result['result']['payload']  .PHP_EOL
                        .PHP_EOL .' Log  '.$time .PHP_EOL. $result['result']['http_header']  .PHP_EOL
                        .PHP_EOL .' Log  '.$time .PHP_EOL. $result['result']['response'] . PHP_EOL;
                    
                    $createOrUpdatefile = Helpers::uploadOrUpdateFileWithContent($fileDirPath, $fileContents, true);
                    if(is_array($createOrUpdatefile)) {
                        $userFileSaved = $this->docRepo->saveFile($createOrUpdatefile)->toArray();
                    } else {
                        $userFileSaved = $createOrUpdatefile;
                    }
                    
                    $otherData['bank_type'] = config('lms.BANK_TYPE')['IDFC'];
                    $otherData['enq_txn_id'] = $transId;
                    $disbusalApiLogData = $this->createDisbusalApiLogData($userFileSaved, $result['result'], $otherData);
                    $createDisbusalApiLog = $this->lmsRepo->saveUpdateDisbursalApiLog($disbusalApiLogData);
                    if ($createDisbusalApiLog) {
                        $disbursalApiLogId = $createDisbusalApiLog->disbursal_api_log_id;
                    }

                    if ($refundBatchId) {
                        $updateDisbursal = $this->lmsRepo->updateRefundBatchById([
                                'batch_status' => 2], $refundBatchId);
                        $oldTimeZone = trim(strtolower(date_default_timezone_get()));
                        date_default_timezone_set("UTC");
                        foreach ($result['result']['body']['Transaction'] as $key => $value) {
                            if ($value['RefStatus'] == 'SUCCESS') {
                                
                                $apiLogData = [];
                                $apiLogData['comment'] = 'online refunded';
                                $apiLogData['actual_refund_date'] = $actual_refund_date;
                                $apiLogData['status'] = config('lms.REFUND_STATUS')['DISBURSED'];
                                
                                $updateRefundByTranId = $this->lmsRepo->updateRefundByTranId($apiLogData, $value['RefNo']);
                                if (isset($updateRefundByTranId->refund_req_id)) {
                                    $this->finalRefundTransactions($updateRefundByTranId->refund_req_id, $actual_refund_date);
                                }

                            } else {
                                $updateRefundByTranId = $this->lmsRepo->updateRefundByTranId([
                                    'status_id' => config('lms.REFUND_STATUS')['FAILED_REFUND']
                                ], $value['RefNo']);
                            }
                        }
                        date_default_timezone_set($oldTimeZone);
                    }            
                    
                } else {
                    Session::flash('message',trans('backend_messages.disbursed_error'));
                    return redirect()->back()->withErrors('message',trans('backend_messages.disbursed_error'));
                }
                 
            }
            $whereActivi['activity_code'] = 'refund_payment_enquiry';
            $activity = $this->master->getActivity($whereActivi);
            if(!empty($activity)) {
                $activity_type_id = isset($activity[0]) ? $activity[0]->id : 0;
                $activity_desc = 'Refund Payment Enquery, Refund Request (Manage Refund) '. null;
                $arrActivity['app_id'] = null;
                $this->activityLogByTrait($activity_type_id, $activity_desc, response()->json(['reqData'=>$reqData]), $arrActivity);
            }                      
            Session::flash('message',trans('backend_messages.disbursed'));
            return redirect()->back()->withErrors('message',trans('backend_messages.disbursed'));
        } catch (Exception $ex) {
            return redirect()->back()->withErrors(Helpers::getExceptionMessage($ex));
        }             
    }
}