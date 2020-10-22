<?php

namespace App\Http\Controllers\Lms;

use Auth;
use File;
use Session;
use Helpers;
use DateTime;
use PDF as DPDF;
use Carbon\Carbon;
use App\Helpers\FileHelper;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Requests\DocumentRequest;
use Illuminate\Support\Facades\Storage;
use App\Inv\Repositories\Models\UserFile;
use App\Http\Requests\Lms\BankAccountRequest;
use App\Inv\Repositories\Contracts\MasterInterface;
use App\Inv\Repositories\Contracts\UserInterface as InvUserRepoInterface;
use App\Inv\Repositories\Libraries\Storage\Contract\StorageManagerInterface;
use App\Inv\Repositories\Contracts\ApplicationInterface as InvAppRepoInterface;
use App\Inv\Repositories\Contracts\DocumentInterface as InvDocumentRepoInterface;

class NachController extends Controller {

    //  use ApplicationTrait;

    protected $appRepo;
    protected $userRepo;
    protected $docRepo;
    protected $master;
    protected $storage;

    /**
     * The pdf instance.
     *
     * @var Pdf
     */
    protected $pdf;

    public function __construct(InvAppRepoInterface $app_repo, InvUserRepoInterface $user_repo, InvDocumentRepoInterface $doc_repo, MasterInterface $master, StorageManagerInterface $storage, FileHelper $file_helper)
    {
        $this->appRepo = $app_repo;
        $this->userRepo = $user_repo;
        $this->docRepo = $doc_repo;
        $this->master = $master;
        $this->storage = $storage;
        $this->fileHelper = $file_helper;
        $this->middleware('checkBackendLeadAccess');
    }

    public function getNachList() {
        try {
            return view('lms.nach.nach_list');
        } catch (\Exception $ex) {
            return Helpers::getExceptionMessage($ex);
        }
    }
    
    /**
     * Download NACH report xlxs
     * 
     * @param Request $request
     * @return type
     */
    public function downloadNachReport(Request $request) {
        try {
            $nachIds = $request->get('chkstatus');
            $nachRecords = $this->appRepo->getNachDataInNachId($nachIds);
//            dd('$nachRecords--', $nachRecords);
            $nachArr = [];
            $reqFor = [1 => 'Create', 2 => 'Modify', 3 => 'Cancel'];
            $mndtFreq = [1 => 'Monthly', 2 => 'Quarterly', 3 => 'Half Yearly', 4 => 'Yearly', 5 => 'As & When Presented'];
            foreach ($nachRecords as $nach) {
              $nachArr[] = [
                    'MessageID' => $nach->request_id,
                    'ConstDestBankIFSCCode' => $nach->ifsc_code ? $nach->ifsc_code : '',
                    'ConstDestBankName' => $nach->user_bank->bank ? $nach->user_bank->bank->bank_name : '',
                    'MndtRequestID' => $nach->request_id,
                    'CustCategoryCode' => 'U099',
                    'MndtType' => 'RCUR',
                    'MndtFreq' => $nach->frequency ? $mndtFreq[$nach->frequency] : '',
                    'MndtStartDt' => !empty($nach->period_from) ? date('d-m-Y', strtotime($nach->period_from)) : '',
                    'MndtEndDt' => !empty($nach->period_to) ? date('d-m-Y', strtotime($nach->period_to)) : '',
                    'MndtCollAmnt' => '',
                    'MndtMaxAmnt' => $nach->amount,
                    'CustName' => $nach->user->f_name ? $nach->user->f_name .' '. $nach->user->l_name : '',
                    'CustUtilityCd' => $nach->utility_code ? $nach->utility_code : '',
                    'DebtorName' => $nach->acc_name ? $nach->acc_name : '',
                    'ConsumerRefNum' => $nach->lms_user ? $nach->lms_user->customer_id : '',
                    'SchemeRefNum' => $nach->lms_user ? $nach->lms_user->customer_id : '',
                    'PhoneNum' => '',
                    'MobileNum' => $nach->phone_no,
                    'Email' => $nach->email_id,
                    'AdditionalDtl' => '',
                    'DebtorAccNum' => $nach->acc_no ? $nach->acc_no : '',
                    'DebtorAccType' => '',
                    'DestBankIFSCCode' => $nach->ifsc_code ? $nach->ifsc_code : '',
                    'DestBankName' => $nach->user_bank->bank ? $nach->user_bank->bank->bank_name : '',
                    'MndtReqType' => $nach->request_for ? $reqFor[$nach->request_for] : '',
                    'AmdmntRsn' => '',
                    'CxlRsn' => '',
                    'MndtId' => '',
                    'OrgnlMndtId' => ''
              ];
                $logData = $this->appRepo->createNachStatusLog($nach->users_nach_id, config('lms.NACH_STATUS')['SENT_TO_APPROVAL']);
                $nachData = [
                            'nach_status' => config('lms.NACH_STATUS')['SENT_TO_APPROVAL'],
                            'nach_status_log_id' => $logData->nach_status_log_id
                            ];
                $this->appRepo->updateNach($nachData, $nach->users_nach_id);
            }
//            dd('$nachArr--', $nachArr);
                $date = new DateTime;
                $currentDate = $date->format('Y-m-d H:i:s');
                $toExportData['Nach Sheet'] = $nachArr;
                $isFileSave = true;
                return $this->fileHelper->array_to_excel($toExportData, $currentDate.'_nach.xlsx', [], null, $isFileSave);
           } catch (\Exception $ex) {
            return Helpers::getExceptionMessage($ex);
        }

    }
    
    /**
     * Add Nach view
     * 
     * @param Request $request
     * @return type mixed
     */
    public function addNachDetail(Request $request)
    {
        try {
            $acc_id = $request->get('bank_account_id');
            $user_id = $request->get('user_id');
            $whereCondition = ['bank_acc_id' => $acc_id, 'user_id' => $user_id];
            $nachDetail = $this->appRepo->getNachData($whereCondition);
            return view('lms.nach.add_nach')
                            ->with(['acc_id' => $acc_id, 'user_id' => $user_id, 'nachDetail' => $nachDetail]);
        } catch (\Exception $ex) {
            return Helpers::getExceptionMessage($ex);
        }
    }
    
    /**
     * Save Nach
     * 
     * @param Request $request
     * @return type
     */
    public function saveNachDetail(Request $request) {
        try {
            $acc_id = $request->get('acc_id');
            $user_id = $request->get('user_id');
            $users_nach_id = $request->get('users_nach_id');
            $bankAccount = $this->appRepo->getBankAccountData(['bank_account_id' => $acc_id])->first();
            $compDetail = '';
            $whereCond = [];
            if ($bankAccount) {
                $whereCond = ['comp_addr_id' => $bankAccount['comp_addr_id']];
                $compDetail = $this->appRepo->getCompAddByCompanyName($whereCond);
            }
            $status = '';
            if ($request->get('submit') == 'submit') {
                $status = 1;
            } else if ($request->get('submit') == 'modify'){
                $status = 2;
            } else if ($request->get('submit') == 'cancel'){
                $status = 3;
            }
            $nachData = [
                'bank_acc_id' => $acc_id ? $acc_id : '',
                'user_id' => $user_id,
                'acc_name' => $bankAccount['acc_name'] ? $bankAccount['acc_name'] : '',
                'acc_no' => $bankAccount['acc_no'] ? $bankAccount['acc_no'] : '',
                'ifsc_code' => $bankAccount['ifsc_code'] ? $bankAccount['ifsc_code'] : '',
                'branch_name' => $bankAccount['branch_name'] ? $bankAccount['branch_name'] : '',
                'sponsor_bank_code' => $bankAccount['sponser_bank_code'] ? $bankAccount['sponser_bank_code'] : '',
                'utility_code' => $compDetail['utility_code'] ? $compDetail['utility_code'] : '',
                'here_by_authorize' => $compDetail['cmp_name'] ? $compDetail['cmp_name'] : '',
                'frequency' => $request->get('frequency'),
                'nach_date' => !empty($request->get('nach_date')) ? Carbon::createFromFormat('d/m/Y', $request->get('nach_date'))->format('Y-m-d') : null,
                'debit_tick' => $request->get('debit_tick'),
                'amount' => $request->get('amount'),
                'debit_type' => $request->get('debit_type'),
                'phone_no' => $request->get('phone_no'),
                'email_id' => $request->get('email_id'),
                'reference_1' => $request->get('reference_1'),
                'reference_2' => $request->get('reference_2'),
                'period_from' => !empty($request->get('period_from')) ? Carbon::createFromFormat('d/m/Y', $request->get('period_from'))->format('Y-m-d') : null,
                'period_to' => !empty($request->get('period_to')) ? Carbon::createFromFormat('d/m/Y', $request->get('period_to'))->format('Y-m-d') : null,
                'period_until_cancelled' => $request->get('period_until_cancelled'),
                'is_active' => 1,
                'status' => $status,
            ];
            if ($users_nach_id != null) {
                $this->appRepo->updateNach($nachData, $users_nach_id);
            } else {
                $users_nach_id = $this->appRepo->saveNach($nachData);
            }
            return redirect()->route('nach_detail_preview', ['users_nach_id' => $users_nach_id, 'user_id' => $user_id]);
        } catch (\Exception $ex) {
            return Helpers::getExceptionMessage($ex);
        }
    }
    
    /**
     * Nach Preview
     * 
     * @param Request $request
     * @return type
     */
    public function nachDetailPreview(Request $request) {
        try {
            $users_nach_id = $request->get('users_nach_id');
            $whereCondition = ['users_nach_id' => $users_nach_id];
            $nachDetail = $this->appRepo->getNachData($whereCondition);
            return view('lms.nach.nach_preview')
                            ->with(['nachDetail' => $nachDetail[0]]);
        } catch (\Exception $ex) {
            return Helpers::getExceptionMessage($ex);
        }
    }
    
    /**
     * Download Nach Pdf
     * 
     * @param Request $request
     * @return type
     */
    public function generateNach(Request $request){
      try{
          $users_nach_id = $request->get('users_nach_id');
          $whereCondition = ['users_nach_id' => $users_nach_id];
          $nachDetail = $this->appRepo->getNachData($whereCondition);
        ob_start();
        DPDF::setOptions(['isHtml5ParserEnabled'=> true,'isRemoteEnabled', true]);               
        $pdf = DPDF::loadView('lms.nach.downloadNach', ['nachDetail' => $nachDetail[0]]);
        return $pdf->download('Nach.pdf');          
      } catch (Exception $ex) {
        return redirect()->back()->withErrors(Helpers::getExceptionMessage($ex));
      } 
    }
    
    /**
     * Upload Signed Nach Pdf
     * 
     * @param Request $request
     * @return type
     */
    public function uploadNachDocument(Request $request)
    {
        $user_id = $request->get('user_id');
        $users_nach_id = $request->get('users_nach_id');
        return view('lms.nach.upload_nach_document')
                    ->with(['user_id' => $user_id, 'users_nach_id' => $users_nach_id]);
    }
    
    /**
     * Save Nach Signed Pdf.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    
    public function saveNachDocument(DocumentRequest $request)
    {
            $arrFileData = $request->all();
        try {
            $user_id = $request->get('user_id');
            $users_nach_id = $request->get('users_nach_id');
            $document_info = $this->docRepo->saveNachDocument($arrFileData, $user_id);
            if ($document_info) {
                $nachData = ['uploaded_file_id' =>  $document_info->file_id, 'status' => 4];
                $this->appRepo->updateNach($nachData, $users_nach_id);
                Session::flash('message',trans('success_messages.uploaded'));
                Session::flash('operation_status', 1);
                return redirect()->route('nach_detail_preview', ['users_nach_id' => $users_nach_id, 'user_id' => $user_id]);
            }
        } catch (\Exception $ex) {
            return Helpers::getExceptionMessage($ex);
        }
    }
    
    /**
     * Upload excel file for import
     * 
     * @param Request $request
     * @return type
     */
    public function uploadNachResponse(Request $request)
    {
        $user_id = $request->get('user_id');
        $users_nach_id = $request->get('users_nach_id');
        return view('lms.nach.upload_nach_xlsx_res')
                    ->with(['user_id' => $user_id]);
    }
    
    /**
     * Import uploaded excel file
     * 
     * @param Request $request
     * @return type
     */
    public function importNachResponse(Request $request)
    {
        try {
            //dd('$request--', $request->files);
            $arrFileData = $request->files;
//            dd('$arrFileData--', $arrFileData);           
            $user_id = $request->get('user_id');
            $inputArr = [];
            $path = '';
            $userId = Auth::user()->user_id;
//            dd('$request--', $request['doc_file']);
            if ($request['doc_file']) {
                if (!Storage::exists('/public/nach/response')) {
                    Storage::makeDirectory('/public/nach/response');
                }
                $path = Storage::disk('public')->put('/nach/response', $request['doc_file'], null);
//                dd('$path--', $path);
            }
            $uploadedFile = $request->file('doc_file');
            $destinationPath = storage_path() . '/app/public/nach/response';
//            dd('$destinationPath--', $destinationPath);
            $date = new DateTime;
            $currentDate = $date->format('Y-m-d H:i:s');
            $fileName = $currentDate.'_nach.xlsx';
            if ($uploadedFile->isValid()) {
                $uploadedFile->move($destinationPath, $fileName);
                $filePath = $destinationPath.'/'.$fileName;
                $fileContent = $this->fileHelper->readFileContent($filePath);
                $fileData = $this->fileHelper->uploadFileWithContent($filePath, $fileContent);
                $file = UserFile::create($fileData);
                $nachBatchData['res_file_id'] = $file->file_id;
                $this->appRepo->saveNachBatch($nachBatchData, null);
                
            }
            $fullFilePath  = $destinationPath . '/' . $fileName;
//            dd('$fullFilePath', $fullFilePath);
            $header = [
                0,1,2,3,4,5,6,7,8,9,10,11,12,13,14,15,16,17,18,19,20,21,22,23,24,25,26,27,28
            ];
            $fileArrayData = $this->fileHelper->excelNcsv_to_array($fullFilePath, $header);
//            dd('$fileArrayData--', $fileArrayData);
            if($fileArrayData['status'] != 'success'){
                Session::flash('message', 'Please import correct format sheet,');
                return redirect()->back();
            }
            $rowData = $fileArrayData['data'];
            if (empty($rowData)) {
                Session::flash('message', 'File does not contain any record');
                return redirect()->back();                     
            }
            foreach ($rowData as $key => $value) { 
//                dd('$value--', $value);
                if(!empty($value[0])){
                    $nachStatus = '';
                    if (trim($value[18]) == 'Active') {
                        $nachStatus = config('lms.NACH_STATUS')['ACTIVE'];
                    } elseif (trim($value[18]) == 'Failed') {
                        $nachStatus = config('lms.NACH_STATUS')['FAILED'];
                    } elseif (trim($value[18]) == 'ACK') {
                        $nachStatus = config('lms.NACH_STATUS')['ACK'];
                    } elseif (trim($value[18]) == 'Reject') {
                        $nachStatus = config('lms.NACH_STATUS')['REJECT'];
                    }
                    $customer_id = trim($value[0]);
//                    dd('$customer_id--', $customer_id);
                    if (!empty($customer_id) && $customer_id != null) {
                        $wherCond['customer_id'] = $customer_id;
                        $lmsData = $this->appRepo->getLmsUsers($wherCond)->first();
//                        dd('$lmsData--', $lmsData->user_id);
                        if ($lmsData){
                            $arrUpdatePre = [
                                'nach_status' => config('lms.NACH_STATUS')['CLOSED']
                            ];
                            $whereCon = [];
                            $whereCon[] = ['user_id', '=', $lmsData->user_id];
                            $whereCon[] = ['nach_status', '>', config('lms.NACH_STATUS')['SENT_TO_APPROVAL']];
                            $resPreUp = $this->appRepo->updateNachByUserId($arrUpdatePre, $whereCon);
//                            dd('$resPreUp-->>', $resPreUp);
                            $arrUpdateData = [
                                'nach_status' => $nachStatus,
                                'umrn' =>  trim($value[5]),
                                'ack_date' => !empty($value[19]) ? date('Y-m-d', strtotime($value[19])) : '',
                                'response_date' =>  !empty($value[20]) ? date('Y-m-d', strtotime($value[20])) : ''
                            ];
                            $whereCondition = [];
                            $whereCondition[] = ['user_id', '=', $lmsData->user_id];
                            $whereCondition[] = ['nach_status', '=', config('lms.NACH_STATUS')['SENT_TO_APPROVAL']];
                            $resUpdate = $this->appRepo->updateNachByUserId($arrUpdateData, $whereCondition);
//                            dd('$resUpdate--', $resUpdate);
                        }
                    }
                }
            }
            Session::flash('message',trans('Excel Data Imported successfully.'));
            Session::flash('operation_status', 1);
            return redirect()->route('users_nach_list');
        } catch (\Exception $ex) {
            return Helpers::getExceptionMessage($ex);
        }
    }
}
