<?php

namespace App\Http\Controllers\Lms;

use Auth;
use File;
use Session;
use Helpers;
use PDF as DPDF;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Requests\DocumentRequest;
use App\Http\Requests\Lms\BankAccountRequest;
use App\Inv\Repositories\Contracts\MasterInterface;
use App\Inv\Repositories\Contracts\UserInterface as InvUserRepoInterface;
use App\Inv\Repositories\Libraries\Storage\Contract\StorageManagerInterface;
use App\Inv\Repositories\Contracts\ApplicationInterface as InvAppRepoInterface;
use App\Inv\Repositories\Contracts\DocumentInterface as InvDocumentRepoInterface;
use App\Helpers\FileHelper;

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
            $nachArr = [];
            foreach ($nachRecords as $nach) {
              $nachArr[] = [
                    'acc_name' => $nach->acc_name ? $nach->acc_name : '',
                    'acc_no' => $nach->acc_no ? $nach->acc_no : '',
                    'ifsc_code' => $nach->ifsc_code ? $nach->ifsc_code : '',
                    'branch_name' => $nach->branch_name ? $nach->branch_name : '',
                    'sponsor_bank_code' => $nach->sponsor_bank_code ? $nach->sponsor_bank_code : '',
                    'utility_code' => $nach->utility_code ? $nach->utility_code : '',
                    'here_by_authorize' => $nach->here_by_authorize ? $nach->here_by_authorize : '',
                    'frequency' => $nach->frequency,
                    'nach_date' => !empty($nach->nach_date) ? date('d-m-Y', strtotime($nach->nach_date)) : '',
                    'debit_tick' => $nach->debit_tick,
                    'amount' => $nach->amount,
                    'debit_type' => $nach->debit_type,
                    'phone_no' => $nach->phone_no,
                    'email_id' => $nach->email_id,
                    'reference_one' => $nach->reference_1,
                    'reference_two' => $nach->reference_2,
                    'period_from' => !empty($nach->period_from) ? date('d-m-Y', strtotime($nach->period_from)) : '',
                    'period_to' => !empty($nach->period_to) ? date('d-m-Y', strtotime($nach->period_to)) : '',
                    'period_until_cancelled' => $nach->period_until_cancelled,
              ];
//                $nachData = ['status' => 5];
//                $this->appRepo->updateNach($nachData, $nach->users_nach_id);
            }
//                $user_id = Auth::user()->user_id;
//                $document_info = $this->docRepo->saveNachDocument($arrFileData, $user_id);
//                if ($document_info) {
//                    $nachData = ['uploaded_file_id' =>  $document_info->file_id];
//                    $this->appRepo->updateNach($nachData, $users_nach_id);
//                }
                $toExportData['Nach Sheet'] = $nachArr;
                return $this->fileHelper->array_to_excel($toExportData, 'nach.xlsx');
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

}


