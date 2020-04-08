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
use App\Inv\Repositories\Contracts\ApplicationInterface as InvAppRepoInterface;

class RefundController extends Controller
{
	use ApplicationTrait;
	use LmsTrait;
        
	protected $appRepo;
	protected $lmsRepo;

	public function __construct(InvAppRepoInterface $app_repo,  InvLmsRepoInterface $lms_repo ){
		$this->appRepo = $app_repo;
		$this->lmsRepo = $lms_repo;
		$this->middleware('checkBackendLeadAccess');
	}

    /**
     * Display a listing of the Refund Request
     * @return \Illuminate\Http\Response
     */
    public function refundListNew(){
        return view('lms.common.refund_list');
    }
    /**
     * Display a listing of the Refund Request
     * @return \Illuminate\Http\Response
     */
    public function refundListPending(){
        return view('lms.common.refund_pending');
    }
    /**
     * Display a listing of the Refund Request
     * @return \Illuminate\Http\Response
     */
    public function refundListApproved(){
        return view('lms.common.refund_approved');
    }
    /**
     * Display a listing of the Refund Request
     * @return \Illuminate\Http\Response
     */
    public function refundListRequest(){
        return view('lms.common.refund_request');
    }
    /**
     * Display a listing of the Refund Request
     * @return \Illuminate\Http\Response
     */
    public function refundConfirm(Request $request){
        $disburseType = $request->get('disburse_type');
        $reqIds = $request->get('transaction_ids');
        if(empty($reqIds)) {
            Session::flash('message', trans('backend_messages.noSelectedInvoice'));
            Session::flash('operation_status', 1);
            
            return redirect()->route('request_list');
        }
        $record = array_filter(explode(",",$reqIds));
        $allrecords = array_unique($record);
        $allrecords = array_map('intval', $allrecords);

        $data = $this->lmsRepo->lmsGetCustomerRefund($allrecords);
        return view('lms.common.refund_confirm')
            ->with([
                'data' => $data,
                'transIds' => $reqIds 
            ]);; 
    }

    public function refundOffline(Request $request)
    {
        $transactionIds = $request->get('transaction_ids');
        $disburseDate = $request->get('disburse_date');
        $creatorId = Auth::user()->user_id;
        $validator = Validator::make($request->all(), [
           'disburse_date' => 'required'
        ]);
        
        if ($validator->fails()) {
            Session::flash('error', $validator->messages()->first());
            return redirect()->back()->withInput();
        }

        $disburseType = config('lms.DISBURSE_TYPE')['OFFLINE']; // Online by Bank Api i.e 2
        
        if(empty($transactionIds)){
            return redirect()->route('request_list');
        }
        $record = array_filter(explode(",",$transactionIds));
        $allrecords = array_unique($record);
        $allrecords = array_map('intval', $allrecords);
        $allAprvls = $this->lmsRepo->getAprvlRqDataByIds($allrecords)->toArray();

        foreach ($allAprvls as $aprvl) {
            if($aprvl['transaction']['user']['is_buyer'] == 2 && empty($aprvl['transaction']['user']['anchor_bank_details'])){
                return redirect()->route('request_list')->withErrors(trans('backend_messages.noBankAccount'));
            } elseif ($aprvl['transaction']['user']['is_buyer'] == 1 && empty($aprvl['transaction']['lms_user']['bank_details'])) {
                return redirect()->route('request_list')->withErrors(trans('backend_messages.noBankAccount'));
            }
        }

        $disburseAmount = 0;
        $exportData = [];
        $aprvlRfd = [];
        $disbursalIds = [];
        $batchId= _getRand(12);
        $transId = _getRand(18);

        foreach ($allAprvls as $aprvl) {
            $userid = $aprvl['transaction']['user']['user_id'];
            $disburseAmount = round($aprvl['amount'], 5);
            $aprvlRfd['tran_id'] = $transId;

            $refId = $aprvl['transaction']['lms_user']['virtual_acc_id'];

            $aprvlRfd['bank_account_id'] = ($aprvl['transaction']['user']['is_buyer'] == 2) ? $aprvl['transaction']['user']['anchor_bank_details']['bank_account_id'] : $aprvl['transaction']['lms_user']['bank_details']['bank_account_id'];
            $aprvlRfd['refund_date'] = (!empty($disburseDate)) ? date("Y-m-d h:i:s", strtotime(str_replace('/','-',$disburseDate))) : \Carbon\Carbon::now()->format('Y-m-d h:i:s');
            $aprvlRfd['bank_name'] = ($aprvl['transaction']['user']['is_buyer'] == 2) ? $aprvl['transaction']['user']['anchor_bank_details']['bank']['bank_name'] : $aprvl['transaction']['lms_user']['bank_details']['bank']['bank_name'] ;
            $aprvlRfd['ifsc_code'] = ($aprvl['transaction']['user']['is_buyer'] == 2) ? $aprvl['transaction']['user']['anchor_bank_details']['ifsc_code'] : $aprvl['transaction']['lms_user']['bank_details']['ifsc_code'];
            $aprvlRfd['acc_no'] = ($aprvl['transaction']['user']['is_buyer'] == 2) ? $aprvl['transaction']['user']['anchor_bank_details']['acc_no'] : $aprvl['transaction']['lms_user']['bank_details']['acc_no'];           
            $aprvlRfd['status'] = 7;
            $aprvlRfd['refund_amount'] = $disburseAmount;
            $aprvlRfd['disburse_type'] = $disburseType;
            if (isset($aprvlRfd)) {
                $refundDisbursed = $this->lmsRepo->updateAprvlRqst($aprvlRfd, $aprvl['req_id']);
            }
            if($disburseType == 2) {

                $exportData[$userid]['RefNo'] = $refId;
                $exportData[$userid]['Amount'] = $disburseAmount;
                $exportData[$userid]['Debit_Acct_No'] = '12334445511111';
                $exportData[$userid]['Debit_Acct_Name'] = 'testing name';
                $exportData[$userid]['Debit_Mobile'] = '9876543210';
                $exportData[$userid]['Ben_IFSC'] = $aprvlRfd['ifsc_code'];
                $exportData[$userid]['Ben_Acct_No'] = $aprvlRfd['acc_no'];
                $exportData[$userid]['Ben_Name'] = $aprvlRfd['bank_name'];
                $exportData[$userid]['Ben_BankName'] = $aprvlRfd['bank_name'];
                $exportData[$userid]['Ben_Email'] = $aprvl['transaction']['user']['email'];
                $exportData[$userid]['Ben_Mobile'] = $aprvl['transaction']['user']['mobile_no'];
                $exportData[$userid]['Mode_of_Pay'] = 'IFT';
                $exportData[$userid]['Nature_of_Pay'] = 'MPYMT';
                $exportData[$userid]['Remarks'] = 'test remarks';
                $exportData[$userid]['Value_Date'] = date('Y-m-d');

            } 
        }

        $result = $this->export($exportData, $batchId);
        $file['file_path'] = ($result['file_path']) ?? null;
        if ($file) {
            $createBatchFileData = $this->createBatchFileData($file);
            $createBatchFile = $this->lmsRepo->saveBatchFile($createBatchFileData);
            if ($createBatchFile) {
                $data['batch_id'] = $batchId;
                $data['batch_type'] = 2;
                $createBatch = $this->lmsRepo->createRefundBatch($createBatchFile, $data);
                if($createBatch) {
                    $updateDisbursal = $this->lmsRepo->updateAprvlRqst([
                            'refund_batch_id' => $createBatch->refund_batch_id
                        ], $allrecords);
                }
            }
        }

        Session::flash('message',trans('backend_messages.proccessed'));
        return redirect()->route('request_list');
    }

    public function export($data, $filename) {
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
    }

	/**
	 * Display a listing of the Refund Request
	 * @return \Illuminate\Http\Response
	 */
	public function refundListSentBank(){
		return view('lms.common.refund_sentbank');
    }
    /**
	 * Display a listing of the Refund Request
	 * @return \Illuminate\Http\Response
	 */
	public function refundListRefunded(){
		return view('lms.common.refund_refunded');
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
        
        public function updateRequestStatus(Request $request)
        {
            $reqId = $request->get('req_id');
            $reqData = $this->lmsRepo->getApprRequestData($reqId);
            $curReqStatus = $reqData ? $reqData->status : '';
            
            $statusList = \Helpers::getRequestStatusList($reqId);
            $statusList = ['' => 'Select Status'] + $statusList;                
                           
            return view('lms.common.view_request_status')
                    ->with('reqId', $reqId)
                    ->with('statusList', $statusList);            
        }
        
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
                        //$trData['repay_trans_id'] = $transId;
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
                        //$trData['repay_trans_id'] = $transId;
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
                        //$trData['repay_trans_id'] = $transId;
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
        
}