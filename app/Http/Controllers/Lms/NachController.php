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
use App\Inv\Repositories\Contracts\Traits\ActivityLogTrait;

class NachController extends Controller {

    //  use ApplicationTrait;
    use ActivityLogTrait;

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
                    'ConsumerRefNum' => $nach->cust_ref_no ?? '',
                    'SchemeRefNum' => $nach->cust_ref_no ?? '',
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
            $arrFileData = $request->files;           
            $user_id = $request->get('user_id');
            $inputArr = [];
            $path = '';
            $userId = Auth::user()->user_id;
            if ($request['doc_file']) {
                if (!Storage::exists('/public/nach/response')) {
                    Storage::makeDirectory('/public/nach/response');
                }
                $path = Storage::disk('public')->put('/nach/response', $request['doc_file'], null);
            }
            $uploadedFile = $request->file('doc_file');
            $destinationPath = storage_path() . '/app/public/nach/response';
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
            $header = [
                0,1,2,3,4,5,6,7,8,9,10,11,12,13,14,15,16,17,18,19,20,21,22,23,24,25,26,27,28
            ];
            $fileArrayData = $this->fileHelper->excelNcsv_to_array($fullFilePath, $header);
            if($fileArrayData['status'] != 'success'){
                Session::flash('message', 'Please import correct format sheet,');
                return redirect()->route('users_nach_list');
            }
            $rowData = $fileArrayData['data'];
            if (empty($rowData)) {
                Session::flash('message', 'File does not contain any record');
                return redirect()->route('users_nach_list');
            }
            foreach ($rowData as $key => $value) {
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
                    if (!empty($customer_id) && $customer_id != null) {
                            $arrUpdatePre = [
                                'nach_status' => config('lms.NACH_STATUS')['CLOSED']
                            ];
                            $whereCon = [];
                            $whereCon[] = ['cust_ref_no', '=', $customer_id];
                            $whereCon[] = ['nach_status', '>', config('lms.NACH_STATUS')['SENT_TO_APPROVAL']];
                            $resPreUp = $this->appRepo->updateNachByUserId($arrUpdatePre, $whereCon);
                            $arrUpdateData = [
                                'nach_status' => $nachStatus,
                                'umrn' =>  trim($value[5]),
                                'ack_date' => !empty($value[19]) ? date('Y-m-d', strtotime($value[19])) : '',
                                'response_date' =>  !empty($value[20]) ? date('Y-m-d', strtotime($value[20])) : ''
                            ];
                            $whereCondition = [];
                            $whereCondition[] = ['cust_ref_no', '=', $customer_id];
                            $whereCondition[] = ['nach_status', '=', config('lms.NACH_STATUS')['SENT_TO_APPROVAL']];
                            $resUpdate = $this->appRepo->updateNachByUserId($arrUpdateData, $whereCondition);
                    }
                }
            }

            $whereActivi['activity_code'] = 'import_nach_response';
            $activity = $this->master->getActivity($whereActivi);
            if(!empty($activity)) {
                $activity_type_id = isset($activity[0]) ? $activity[0]->id : 0;
                $activity_desc = 'Upload NACH Response LMS in Manage NACH (Manage NACH List)';
                $arrActivity['app_id'] = null;
                $this->activityLogByTrait($activity_type_id, $activity_desc, response()->json($arrFileData), $arrActivity);
            }            
            
            Session::flash('message',trans('Excel Data Imported successfully.'));
            Session::flash('operation_status', 1);
            return redirect()->route('users_nach_list');
        } catch (\Exception $ex) {
            return Helpers::getExceptionMessage($ex);
        }
    }
}
