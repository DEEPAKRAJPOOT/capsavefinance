<?php

namespace App\Http\Controllers\Backend;

use DB;
use Auth;
use Session;
use Helpers;
use DateTime;
use PhpOffice\PhpSpreadsheet\Spreadsheet; 
use PDF as DPDF;
use Carbon\Carbon;
use App\Libraries\Pdf;
use PhpOffice\PhpSpreadsheet\IOFactory;
use App\Helpers\FileHelper;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Requests\DocumentRequest;
use Illuminate\Support\Facades\Storage;
use App\Inv\Repositories\Models\Payment;
use App\Inv\Repositories\Models\UserFile;
use App\Inv\Repositories\Contracts\Traits\LmsTrait;
use App\Inv\Repositories\Contracts\LmsInterface as InvLmsRepoInterface;
use App\Inv\Repositories\Contracts\ApplicationInterface as AppRepoInterface;
use App\Inv\Repositories\Contracts\DocumentInterface as InvDocumentRepoInterface;
use App\Inv\Repositories\Contracts\UserInterface as InvUserRepoInterface;
use App\Inv\Repositories\Contracts\MasterInterface as InvMasterRepoInterface;
use App\Inv\Repositories\Contracts\Traits\ActivityLogTrait;

class NACHController extends Controller {

    use LmsTrait;
    use ActivityLogTrait;

	protected $appRepo;
	protected $docRepo;
    protected $lmsRepo;
    protected $userRepo;
    protected $masterRepo;

	public function __construct(AppRepoInterface $appRepo, InvDocumentRepoInterface $docRepo, InvLmsRepoInterface $lms_repo, InvUserRepoInterface $user_repo, FileHelper $file_helper, InvMasterRepoInterface $master_repo) {
		$this->appRepo  =  $appRepo;
		$this->docRepo  =  $docRepo;
        $this->lmsRepo = $lms_repo;
        $this->fileHelper = $file_helper;
        $this->userRepo = $user_repo;
        $this->masterRepo = $master_repo;
		$this->middleware('auth');
		// $this->middleware('checkBackendLeadAccess');
	}

	/* nach Listing  */

	public function nachList(Request $request) {
		return view('backend.nach.list');
	}

	/* nach Listing  */

	public function createNACH(Request $request) {
		try {

			return view('backend.nach.create_nach');
				
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
			$userId = $request->get('customer_id');
			$roleType = $request->get('role_type');

			$whereCondition = ['bank_account_id' => $acc_id, 'user_id' => $userId];
			$nachDetail = $this->appRepo->getNachData($whereCondition);

			return view('backend.nach.add_nach')
					->with([
						'acc_id' => $acc_id, 
						'user_id' => $userId, 
						'role_type' => $roleType, 
						'nachDetail' => $nachDetail
					]);

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
	public function EditNachDetail(Request $request)
	{
		try {
			$usersNachId = $request->get('users_nach_id');

			$whereCondition = ['users_nach_id' => $usersNachId];
			$nachDetail = $this->appRepo->getNachData($whereCondition);

			return view('backend.nach.add_nach')
					->with([
						'acc_id' => $nachDetail->bank_account_id, 
						'user_id' => $nachDetail->user_id, 
						'nachDetail' => $nachDetail
					]);

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
			$roleType = $request->get('role_type');
			$users_nach_id = $request->get('users_nach_id');
			$bankAccount = $this->appRepo->getBankAccountData(['bank_account_id' => $acc_id])->first();
			$compDetail = '';
			$whereCond = [];
			if ($bankAccount) {
				$whereCond = ['comp_addr_id' => $bankAccount['comp_addr_id']];
				$compDetail = $this->appRepo->getCompAddByCompanyName($whereCond);
			}
			$status = '';
			if ($request->get('submit') == 'save') {
				$status = 1;
			} else if ($request->get('submit') == 'modify'){
				$status = 2;
			} else if ($request->get('submit') == 'cancel'){
				$status = 3;
			}

			if($roleType == 3) {
				$userData = $this->userRepo->getCustomerDetail($user_id);
			}

			$nachData = [
				'bank_account_id' => $acc_id ? $acc_id : '',
				'user_id' => $user_id,
				'anchor_id' => (isset($userData)) ? $userData->anchor_id : null ,
				'user_type' => ($roleType == 3) ? 2 : 1,
				'cust_ref_no' => ($roleType == 3) ? 'CAPANC000'.$userData->anchor_id : 'CAP000'.$user_id,
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
				'is_active' => 0,
				'request_for' => $status,
				'nach_status' => config('lms.NACH_STATUS')['PENDING'],
			];

			// dd($nachData);
			if ($users_nach_id != null) {
				$whereCondition = ['users_nach_id' => $users_nach_id];
				$nachDetail = $this->appRepo->getNachData($whereCondition);
				if ($nachDetail->nach_status == 4) {
					$nachData += [
                		'parent_users_nach_id' => $users_nach_id
                	];
					$users_nach_id = $this->appRepo->saveNach($nachData);
					$logData = $this->appRepo->createNachStatusLog($users_nach_id, config('lms.NACH_STATUS')['PENDING']);
					$updateNachData = [
                		'nach_status_log_id' => $logData->nach_status_log_id
                	];
                	$this->appRepo->updateNach($updateNachData, $users_nach_id);
                } else {
					$this->appRepo->updateNach($nachData, $users_nach_id);
				}
			} else {
				$users_nach_id = $this->appRepo->saveNach($nachData);
				$logData = $this->appRepo->createNachStatusLog($users_nach_id, config('lms.NACH_STATUS')['PENDING']);
				$updateNachData = [
            		'nach_status_log_id' => $logData->nach_status_log_id
            	];
            	$this->appRepo->updateNach($updateNachData, $users_nach_id);
            }
            
            $whereActivi['activity_code'] = 'backend_save_nach_detail';
            $activity = $this->masterRepo->getActivity($whereActivi);
            if(!empty($activity)) {
                $activity_type_id = isset($activity[0]) ? $activity[0]->id : 0;
                $activity_desc = 'Save Backend NACH in Manage NACH (Register Request)';
                $arrActivity['app_id'] = null;
                $this->activityLogByTrait($activity_type_id, $activity_desc, response()->json($nachData), $arrActivity);
            }             
            
			Session::flash('message',trans('success_messages.nach_updated'));
			return redirect()->route('backend_nach_list');
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
			
			return view('backend.nach.nach_preview')
					->with(['nachDetail' => $nachDetail]);
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
			$pdf = DPDF::loadView('backend.nach.download_nach', ['nachDetail' => $nachDetail]);
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
    	try {
		    $user_id = $request->get('user_id');
		    $users_nach_id = $request->get('users_nach_id');

		    return view('backend.nach.upload_nach_document')
		        	->with(['user_id' => $user_id, 'users_nach_id' => $users_nach_id]);

        } catch (\Exception $ex) {
            return Helpers::getExceptionMessage($ex);
        }
    }
    
    /**
     * Save Nach Signed Pdf.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    
    public function saveNachDocument(DocumentRequest $request)
    {
        try {
        	$arrFileData = $request->all();
            $user_id = $request->get('user_id');
            $users_nach_id = $request->get('users_nach_id');
            
            $path = '/user/'. $user_id .'/nach';
			$uploadData = Helpers::uploadDirectoryFile($arrFileData, $path);
			$userFile = $this->docRepo->saveFile($uploadData);
			
            if ($userFile) {
				$logData = $this->appRepo->createNachStatusLog($users_nach_id, config('lms.NACH_STATUS')['PDF_UPLOADED']);
				$requestId = _getRand(14);
                $nachData = [
                	'uploaded_file_id' =>  $userFile->file_id, 
                	'nach_status' => config('lms.NACH_STATUS')['PDF_UPLOADED'], 
                	'nach_status_log_id' => $logData->nach_status_log_id,
                	'is_active' => 1
                	];
                $this->appRepo->updateNach($nachData, $users_nach_id);
                $whereActivi['activity_code'] = 'backend_nach_document_save';
                $activity = $this->masterRepo->getActivity($whereActivi);
                if(!empty($activity)) {
                    $activity_type_id = isset($activity[0]) ? $activity[0]->id : 0;
                    $activity_desc = 'Upload Document Backend NACH in Manage NACH (Register Request)';
                    $arrActivity['app_id'] = null;
                    $this->activityLogByTrait($activity_type_id, $activity_desc, response()->json(['arrFileData' => $arrFileData, 'uploadData' => $uploadData, 'nachData' => $nachData]), $arrActivity);
                } 
                Session::flash('message',trans('success_messages.uploaded'));
                Session::flash('operation_status', 1);
                return redirect()->route('backend_nach_detail_preview', ['users_nach_id' => $users_nach_id, 'user_id' => $user_id]);
            }

        } catch (\Exception $ex) {
            return Helpers::getExceptionMessage($ex);
        }
    }

	public function repaymentList(Request $request) {
		return view('backend.nach.repayment.list');
	}

	public function createNachRepaymentReq(Request $request)
    {
        try {

            if ($request->get('eod_process')) {
                Session::flash('error', trans('backend_messages.lms_eod_batch_process_msg'));
                return back();
            }
            
            $nachReqIds = $request->get('nachRequest');
            $creatorId = Auth::user()->user_id;
            
            if(empty($nachReqIds)){
                return redirect()->route('nach_repayment_list')->withErrors(trans('backend_messages.noSelectedInvoice'));
            }

            $allrecords = array_unique($nachReqIds);
            $allrecords = array_map('intval', $allrecords);
            $nachData = $this->lmsRepo->getUserNaches($allrecords);


            foreach ($nachData as $nach) {
    		 	$nach->outstanding_amt = $this->lmsRepo->getUnsettledTrans($nach->user_id, ['trans_type_not_in' => [config('lms.TRANS_TYPE.NON_FACTORED_AMT')] ])->sum('outstanding');

	            $nach->transactions = [];
	            $transAr = [];
	            foreach ($this->lmsRepo->getNACHUnsettledTrans($nach->user_id, ['trans_type_not_in' => [config('lms.TRANS_TYPE.NON_FACTORED_AMT')] ]) as $key1 => $value1) {
	                $transArray['trans_id'] = $value1->trans_id;
	                $transArray['amount'] = $value1->outstanding;
	                array_push($transAr, $transArray);

	            }
	            $nach->transactions = $transAr;
    		 	if($nach->outstanding_amt > $nach->amount) {
                	return redirect()->route('nach_repayment_list')->withErrors(trans('backend_messages.noBankAccount'));
    		 	}
            }

            $batchNo= _getRand(12);
            $nachData = $nachData->toArray();
            foreach ($nachData as $nach) {
            		$refNo = _getRand(18);

                    $exportData[$nach['user_id']]['users_nach_id'] = $nach['users_nach_id'];
                    $exportData[$nach['user_id']]['user_id'] = $nach['user_id'];
                    $exportData[$nach['user_id']]['Client_code'] = 'CAPSAVE';
                    $exportData[$nach['user_id']]['Batch_Reference_Number'] = $batchNo ?? '';
                    $exportData[$nach['user_id']]['Settlement_date'] = date('Y-m-d');
                    $exportData[$nach['user_id']]['Amount'] = $nach['outstanding_amt'] ?? 0.00;
                    $exportData[$nach['user_id']]['Customer_Transaction_ref_Number'] = $refNo ?? '';
                    $exportData[$nach['user_id']]['UMRN'] = $nach['umrn'] ?? '';
                    $exportData[$nach['user_id']]['transactions'] = $nach['transactions'] ?? '';

            }

            $result = $this->export($exportData, 'ACH_Debit_Transaction_'.$batchNo);
            $file['file_path'] = $result['file_path'] ?? '';

            if ($file) {
                $createBatchFileData = $this->createBatchFileData($file);
                $createBatchFile = $this->lmsRepo->saveBatchFile($createBatchFileData);
                if ($createBatchFile) {
                    $createDisbursalBatch = $this->lmsRepo->createNachReqBatch($createBatchFile, $batchNo);
                    $nachBatchId = $createDisbursalBatch->req_batch_id;
                    foreach ($exportData as $key => $value) {
                		$createNachReqData = $this->createNachReqData($value, $nachBatchId);
                		$createNachReq = $this->lmsRepo->saveNachReq($createNachReqData);
                		$nachReqId = $createNachReq->nach_repayment_req_id;
                		foreach ($value['transactions'] as $key2 => $trans) {
                			$createNachTransData = $this->createNachTransData($trans, $nachReqId);
                			$createNachTrans = $this->lmsRepo->saveNachTrans($createNachTransData);

                		}
                    }
                }
            }
            $whereActivi['activity_code'] = 'create_nach_repayment_req';
            $activity = $this->masterRepo->getActivity($whereActivi);
            if(!empty($activity)) {
                $activity_type_id = isset($activity[0]) ? $activity[0]->id : 0;
                $activity_desc = 'Generate NACH Request in Manage NACH (NACH Repayment)';
                $arrActivity['app_id'] = null;
                $this->activityLogByTrait($activity_type_id, $activity_desc, response()->json($nachReqIds), $arrActivity);
            }  
            Session::flash('message',trans('backend_messages.disbursed'));
            return redirect()->route('nach_repayment_list');
        } catch (Exception $ex) {
            return redirect()->back()->withErrors(Helpers::getExceptionMessage($ex));
        } 
    }

	public function export($data, $filename = 'nach_demo') {
        $sheet =  new Spreadsheet();
        $sheet->getProperties()
                ->setCreator("Capsave")
                ->setLastModifiedBy("Capsave")
                ->setTitle("NACH Repayment Batch Request")
                ->setSubject("NACH Repayment Batch Request")
                ->setDescription("NACH Repayment Batch Request")
                ->setKeywords("NACH Repayment Batch Request")
                ->setCategory("NACH Repayment Batch Request");
    
        $sheet->setActiveSheetIndex(0)
                ->setCellValue('A1', 'Client code')
                ->setCellValue('B1', 'Batch Reference Number')
                ->setCellValue('C1', 'Settlement date')
                ->setCellValue('D1', 'Transaction Amount')
                ->setCellValue('E1', 'Customer Transaction ref Number')
                ->setCellValue('F1', 'UMRN');
        $rows = 2;

        foreach($data as $rowData){
            $sheet->setActiveSheetIndex(0)
                ->setCellValue('A' . $rows, $rowData['Client_Code'] ?? 'XYZ')
                ->setCellValue('B' . $rows, $rowData['Batch_Reference_Number'] ?? '')
                ->setCellValue('C' . $rows, $rowData['Settlement_date'] ?? '')
                ->setCellValue('D' . $rows, $rowData['Amount'] ?? '')
                ->setCellValue('E' . $rows, $rowData['Customer_Transaction_ref_Number'] ?? '')
                ->setCellValue('F' . $rows, $rowData['UMRN'] ?? '');

            $rows++;
        }
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="'.$filename.'.xlsx"');
        header('Cache-Control: max-age=0');

        if (!Storage::exists('/public/docs/nachRequest')) {
            Storage::makeDirectory('/public/docs/nachRequest');
        }
        $tmpHandle = tmpfile();
		$metaDatas = stream_get_meta_data($tmpHandle);
		$tmpFilename = $metaDatas['uri'];

        $commonUrl = 'public/docs/nachRequest'.'/'.$filename.'.xlsx';
        $storage_path = Storage::path($commonUrl);
        $fileUrl = Storage::url($commonUrl);

        $objWriter = IOFactory::createWriter($sheet, 'Xlsx');
        $objWriter->save($tmpFilename);
        $attributes['temp_file_path'] = $tmpFilename;
		$path = Helper::uploadAwsS3Bucket($storage_path, $attributes, $filename);
		unlink($tmpFilename);
        
        return [ 'status' => 1,
                'file_path' => $path,
                'file_url' => $fileUrl,
                'objWriter' => $objWriter
                ];
    }
    
    /**
     * 
     * @param Request $request
     * @return type
     */
    public function repaymentTransList(Request $request) {
        return view('backend.nach.repayment.transaction_list');
    }
    
    /**
     * Upload excel file for import
     * 
     * @param Request $request
     * @return type
     */
    public function uploadNachTransResponse(Request $request)
    {
        return view('backend.nach.repayment.upload_nach_trans_res');
    }
    
    /**
     * Import NACH Transaction response excel file
     * 
     * @param Request $request
     * @return type
     */
    public function importNachTransResponse(Request $request)
    {
        try {
            
            $uploadedFile = $request->file('doc_file');
            $destinationPath = Storage::path('public/nach/transaction/response');
            $date = new DateTime;
            $currentDate = $date->format('Y-m-d H:i:s');
            $fileName = $currentDate.'_nach.xlsx';
            if ($uploadedFile->isValid()) {

                $sessionId = Session::getId();
                Storage::disk('temp')->put($sessionId.'/'.$fileName,$uploadedFile);
                $filePath = $sessionId.'/'.$fileName;
                $fileContent = Storage::disk('temp')->get($filePath);
                $fileData = $this->fileHelper->uploadFileWithContent($destinationPath, $fileContent);
                Storage::disk('temp')->delete($filePath);
                $file = UserFile::create($fileData);
                $nachBatchData['res_file_id'] = $file->file_id;
                $this->appRepo->saveNachBatch($nachBatchData, null);
                
            }
            $fullFilePath  = $destinationPath . '/' . $fileName;
            $header = [
                0,1,2,3,4,5,6,7,8,9,10,11,12,13,14,15,16,17,18,19,20,21
            ];
            $fileArrayData = $this->fileHelper->excelNcsv_to_array($fullFilePath, $header);
            if($fileArrayData['status'] != 'success'){
                Session::flash('message', 'Please import correct format sheet,');
                return redirect()->route('nach_repayment_trans_list');
            }
            $rowData = $fileArrayData['data'];
            if (empty($rowData)) {
                Session::flash('message', 'File does not contain any record');
                return redirect()->route('nach_repayment_trans_list');
            }
            foreach ($rowData as $key => $value) {
                $customerRefNo = trim($value[4]);
                if (!empty($customerRefNo) && $customerRefNo != null) {
                        $nachReqStatus = '';
                    if (trim($value[7]) == 'S') {
                        $nachReqStatus = 2;
                        $nachTransStatus = 2;
                    } elseif (trim($value[7]) == 'F') {
                        $nachReqStatus = 3;
                        $nachTransStatus = 3;
                    } 
                    $arrUpdateData = [
                        'status' => $nachReqStatus,
                    ];                           
                    $whereCondition[] = ['ref_no', '=',  $customerRefNo];
                    $whereCondition[] = ['status', '!=',  $nachReqStatus];
                    $resUpdate = $this->lmsRepo->updateRepaymentReq($arrUpdateData, $whereCondition);
                    $nachRepaymentReq = $this->lmsRepo->getNachRepaymentReqFirst(['ref_no' => $customerRefNo]);
                    $updateNachTrans = $this->lmsRepo->updateNachTransReq(['status' => $nachTransStatus], ['nach_repayment_req_id' => $nachRepaymentReq->nach_repayment_req_id]);
                    
                    $whereCond[] = ['ref_no', '=',  $customerRefNo];
                    $nachList = $this->lmsRepo->getNachRepaymentReq($whereCond)->first();
                    if ($nachList && $resUpdate != false) {
                        $wherCond['user_id'] = $nachList['user_id'];
                        $lmsData = $this->appRepo->getLmsUsers($wherCond)->first();
                            $paymentData = [
                            'user_id' => $nachList['user_id'],
                            'biz_id' => null,
                            'virtual_acc' => $lmsData ? $lmsData->virtual_acc_id : '',
                            'action_type' => 1,
                            'trans_type' => config('lms.TRANS_TYPE.REPAYMENT'),
                            'parent_trans_id' => '',
                            'amount' => trim($value[3]),
                            'date_of_payment' => trim($value[2]),
                            'gst' => '',
                            'sgst_amt' => 0,
                            'cgst_amt' => 0,
                            'igst_amt' => 0,
                            'payment_type' => 3,
                            'utr_no' => trim($value[5]),
                            'unr_no' => '',
                            'cheque_no' => '',
                            'tds_certificate_no' => '',
                            'file_id' => '',
                            'description' => $value[9] ? $value[9] : '',
                            'is_settled' => 0,
                            'is_manual' => '',
                            'generated_by' => 1,
                            'is_refundable' => 1
                            ];
                        $paymentId = Payment::insertPayments($paymentData);
                    }
                }
            }
            Session::flash('message',trans('Excel Data Imported successfully.'));
            Session::flash('operation_status', 1);
            return redirect()->route('nach_repayment_trans_list');
        } catch (\Exception $ex) {
            return Helpers::getExceptionMessage($ex);
        }
    }
    
}
