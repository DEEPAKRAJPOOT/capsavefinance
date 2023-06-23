<?php

namespace App\Http\Controllers\Backend;

use Helpers;
use Session;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Inv\Repositories\Contracts\FinanceInterface;
use App\Http\Requests\Backend\CreateJeConfigRequest;
use App\Http\Requests\Backend\CreateJiConfigRequest;
use App\Http\Requests\Backend\CreateJournalRequest;
use App\Http\Requests\Backend\CreateAccountRequest;
use App\Helpers\FinanceHelper;
use App\Helpers\FileHelper;
use App\Inv\Repositories\Models\Master\FactTransType;
use App\Inv\Repositories\Models\Master\FactJournalEntry;
use App\Inv\Repositories\Models\Master\FactPaymentEntry;
use App\Helpers\Helper;

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Cell\DataType;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Shared;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\App;

class FinanceController extends Controller {

    private $finRepo;
    private $transType = [];
    private $variables = [];    
    private $journals = [];  
    private $accounts = [];
    private $inputData = [];

    public function __construct(FinanceInterface $finRepo, FileHelper $file_helper) {
        $this->middleware('guest')->except('logout');
        $this->middleware('checkBackendLeadAccess');
        $this->finRepo = $finRepo;
        $this->fileHelper = $file_helper;
    }


    public function getFinTransList() {
        return view('backend.finance.trans_list');
    }    

    public function getFinJournal(Request $request) {
        $journalData = '';
        $journalId = $request->get('journal_id');
        if(isset($journalId) && !empty($journalId)){
            $journalData = $this->finRepo->getJournalByJournalId($journalId); 
        }
        return view('backend.finance.journal_list')
            ->with([
            'journalData'=> $journalData,
            'journalId'=> $journalId
            ]);
    }  

    public function getFinAccount(Request $request) {
        $accountData = '';
        $accountId = $request->get('account_id');
        if(isset($accountId) && !empty($accountId)){
            $accountData = $this->finRepo->getAccountByAccountId($accountId); 
        }
        return view('backend.finance.account_list')
            ->with([
            'accountData'=> $accountData,
            'accountId'=> $accountId
            ]);
    }    

    public function getFinVariable() {
        return view('backend.finance.variable_list');
    }
    
    public function exportTransactions(Request $request) {
        ini_set("memory_limit", "-1");
        $batch_no = $request->get('batch_no') ?? NULL;
        $where = [];
        if (!empty($batch_no)) {
            $where = ['batch_no' => $batch_no];
        }
        $result = $this->finRepo->getTallyTxns($where);
        $records = [];
        $journal = array();
        $payment = array();
        $records['PAYMENT'] = array();
        $records['JOURNAL'] = array();
        $cr_amount_sum = 0;

        $transType = "";
        $voucher_date = "";
        $transDate = "";
        if (!empty($result)) {
           foreach ($result as $key => $value) {
               
                $new[] = $fetchedArr = (array)$value;
                $voucherDate = date('d-m-Y',strtotime($fetchedArr['voucher_date']));
                $trans_date = date('Y-m-d', strtotime($fetchedArr['voucher_date'])); 
                $transaction_date = $fetchedArr['transaction_date']?Helpers::utcToIst($fetchedArr['transaction_date'],'Y-m-d H:i:s', 'Y-m-d'):NULL;
                $entry_type = strtolower($fetchedArr['entry_type']);
                $is_first_n_old = (empty($transType) || empty($transDate) || ($transType == $fetchedArr['trans_type'] && $transDate == $trans_date));
                $j_is_first_or_old = NULL;
                if (strtolower($fetchedArr['voucher_type']) == 'journal') {
                    $jj = $fetchedArr;
                    $j_is_first_or_old  = $is_first_n_old;
                    $j = [
                        "batch_no" => $fetchedArr['batch_no'],
                        "voucher_no" => sprintf('%04d',$fetchedArr['voucher_no']),
                        "fact_voucher_number"=>$fetchedArr['fact_voucher_number'],
                        //"trans_type" => $fetchedArr['trans_type'],
                        "voucher_type" => $fetchedArr['voucher_type'],
                        "voucher_date" => $voucherDate,
                        'transaction_date'=>$transaction_date,
                        "dr_/_cr" => $fetchedArr['entry_type'],
                        "dr_ledger_name" => ($entry_type == 'credit' ? $fetchedArr['trans_type'] : $fetchedArr['ledger_name']),
                        "dr_amount" => $fetchedArr['amount'],
                        "ref_no" => $fetchedArr['ref_no'],
                        "ref_amount" => $fetchedArr['amount'],
                        "cr_ledger_name" => ($entry_type == 'credit' ? $fetchedArr['ledger_name'] : $fetchedArr['trans_type']),
                        "cr_amount" => ($entry_type == 'credit' ? $fetchedArr['amount'] : ''),
                        "cr_ref_no" => $fetchedArr['ref_no'],
                        "cr_ref_amount" => $fetchedArr['amount'],
                        "narration" => $fetchedArr['narration'],
                        "trans_type" => $fetchedArr['trans_type'],
                    ];
                    if (!$is_first_n_old) {
                        if (!empty($journal[0])) {
                           $journal[0]['cr_amount'] = $cr_amount_sum;
                           if (strtolower($journal[0]['dr_/_cr']) == 'debit') {
                              $journal[0]['cr_ledger_name'] = $journal[0]['trans_type'];  
                           }
                           $cr_amount_sum = ($entry_type == 'credit' ? $fetchedArr['amount'] : 0); 
                           $records['JOURNAL'] = array_merge($records['JOURNAL'],$journal);
                        }
                       $journal = array();
                    }
                    $cr_amount_sum += ($entry_type == 'debit' ? $fetchedArr['amount'] : 0);
                    $journal[] = $j; 
                }else{
                    $paymentRow =  [
                        "voucher_no" => $fetchedArr['voucher_no'],
                        "fact_voucher_number"=>$fetchedArr['fact_voucher_number'],
                        "voucher_type" => $fetchedArr['voucher_type'],
                        "voucher_date" =>  !empty($fetchedArr['voucher_date']) ? date('d-m-Y',strtotime($fetchedArr['voucher_date'])) : '',
                        "transaction_date"=>$transaction_date,
                        "ledger_name" => $fetchedArr['ledger_name'],
                        "amount" => $fetchedArr['amount'],
                        "dr_/_cr" => $fetchedArr['entry_type'],
                        "reference_no" => $fetchedArr['ref_no'],
                        "reference_amount" => $fetchedArr['ref_amount'],
                        "transaction_type" => $fetchedArr['mode_of_pay'],
                        "a_/_c_no" => $fetchedArr['acc_no'],
                        "ifsc_code" => $fetchedArr['ifsc_code'],
                        "bank_name" => $fetchedArr['bank_name'],
                        "cheque_amount" => ($fetchedArr['cheque_amount'] != 0 ? $fetchedArr['cheque_amount'] : ''),
                        "cross_using" => $fetchedArr['cross_using'],
                        "utr_no" => $fetchedArr['utr_no'],
                        "inst_no" => $fetchedArr['inst_no'],
                        "inst_date" => $fetchedArr['inst_date'],
                        "favoring_name" => $fetchedArr['favoring_name'],
                        "remarks" => $fetchedArr['remarks'],
                        "narration" => $fetchedArr['narration'],
                        "company_bank_name"=> $fetchedArr['company_bank_name']?$fetchedArr['company_bank_name']:'',
                        "company_bank_acc"=> $fetchedArr['company_bank_acc']?$fetchedArr['company_bank_acc']:'',
                        "trans_type" => $fetchedArr['trans_type']??'',
                    ];
                    $records['PAYMENT'][] = $paymentRow;
                }
                $transType = $fetchedArr['trans_type'];
                $transDate = date('Y-m-d', strtotime($fetchedArr['voucher_date'])); 
            }
        }

        if (!empty($journal[0])) {
            if (isset($j_is_first_or_old) && $j_is_first_or_old) {
               $journal[0]['cr_amount'] = $cr_amount_sum;
               if (strtolower($journal[0]['dr_/_cr']) == 'debit') {
                  $journal[0]['cr_ledger_name'] = $journal[0]['trans_type'];  
               }
               $cr_amount_sum = ($entry_type == 'credit' ? $fetchedArr['amount'] : 0); 
               $records['JOURNAL'] = array_merge($records['JOURNAL'],$journal);
               $journal = array();
            }else{
                $journal[0]['cr_amount'] = $cr_amount_sum;
                $records['JOURNAL'] = array_merge($records['JOURNAL'],$journal);
            }
        }
        if (empty($records['PAYMENT'])) {
            $records['PAYMENT'][] =  [
                "voucher_no" => '',
                "voucher_type" => '',
                "voucher_date" => '',
                "ledger_name" => '',
                "amount" => '',
                "dr_/_cr" => '',
                "reference_no" => '',
                "reference_amount" => '',
                "transaction_type" => '',
                "a_/_c_no" => '',
                "ifsc_code" => '',
                "bank_name" => '',
                "cheque_amount" => '',
                "cross_using" => '',
                "utr_no" => '',
                "inst_no" => '',
                "inst_date" => '',
                "favoring_name" => '',
                "remarks" => '',
                "narration" => '',
                "company_bank_name"=> '',
                "company_bank_acc"=> '',
                "trans_type" => '',
            ];
        }

        if (empty($records['JOURNAL'])) {
            $records['JOURNAL'][] =  [
                "batch_no" => '',
                "voucher_no" => '',
                "voucher_type" => '',
                "voucher_date" => '',
                "dr_/_cr" => '',
                "dr_ledger_name" => '',
                "dr_amount" => '',
                "ref_no" => '',
                "ref_amount" => '',
                "cr_ledger_name" => '',
                "cr_amount" => '',
                "cr_ref_no" => '',
                "cr_ref_amount" => '',
                "narration" => '',
                "trans_type" => '',
            ];
        }
        foreach ($records['JOURNAL'] as $key => $value) {
          //unset($records['JOURNAL'][$key]['trans_type']); //by d
          unset($records['JOURNAL'][$key]['batch_no']);
        }
        $toExportData = $records;
        return $this->fileHelper->array_to_excel($toExportData, "Tally-$batch_no.xlsx");
    }

    public function crateJeConfig(Request $request) {
        $variablesIdArray = [];
        $jeConfigId = $request->get('je_config_id');
        $transConfigId = $request->get('trans_config_id');
        $journalId = $request->get('journal_id');
        $this->transType = $this->finRepo->getAllTransType()->get();
        $this->variables = $this->finRepo->getAllVariable()->get();
        $this->journals = $this->finRepo->getAllJournal()->get();
        if(isset($jeConfigId) && !empty($jeConfigId)){
            $jeVariablesData = $this->finRepo->getVariablesByTransConfigId($transConfigId); 
            if($jeVariablesData[0]->variables->count()>0) {
                foreach($jeVariablesData[0]->variables as $key=>$val){
                    $variablesIdArray[] = $val->variable_id;
                }
            }          
        }
        return view('backend.finance.je_config')
            ->with([
            'transType'=> $this->transType,
            'variables'=> $this->variables,
            'journals'=> $this->journals,
            'jeConfigId'=> $jeConfigId,
            'transConfigId'=> $transConfigId,
            'journalId'=> $journalId,
            'variablesIdArray'=>$variablesIdArray
            ]);
    }  

    public function saveJeConfig(CreateJeConfigRequest $request) {
        try {
            $jeConfigId = $request->get('jeConfigId');
            $transTypeId = $request->get('trans_type');
            $variables = $request->get('variable');
            $journalId = $request->get('journal');

            $this->inputData = [];
            if(isset($jeConfigId) && !empty($jeConfigId)){
                $transConfigId = $request->get('transConfigId');
                foreach($variables as $key=>$val) {
                    $this->inputData[] = $val;
                }
                $outputQryTransVar = $this->finRepo->syncTransVarData($this->inputData, $transConfigId);
            } else {
                $this->inputData = [];
                $this->inputData = [
                    'trans_config_id'=>$transTypeId,
                    'journal_id'=>$journalId
                ];

                $recCount = $this->finRepo->checkTransJeData($transTypeId, $journalId);
                if($recCount >0){
                    Session::flash('error','Journal entry already exist for this transaction type.');
                    return redirect()->back();
                }
                $outputQryJe = $this->finRepo->saveJeData($this->inputData);
                if(isset($outputQryJe->je_config_id)) {
                    $this->inputData = [];
                    // foreach($variables as $key=>$val) {
                    //     $this->inputData[] = [
                    //         'trans_config_id'=>$transTypeId,
                    //         'variable_id'=>$val
                    //     ];
                    // }
                    // $outputQryTransVar = $this->finRepo->saveTransVarData($this->inputData);
                    foreach($variables as $key=>$val) {
                        $this->inputData[] = $val;
                    }
                    $outputQryTransVar = $this->finRepo->syncTransVarData($this->inputData, $transTypeId);                
                }
            }
            Session::flash('message','Journal entry config saved successfully');
            return redirect()->back();
        } catch (Exception $ex) {
            return redirect()->back()->withErrors(Helpers::getExceptionMessage($ex));
        }
    }

    public function addJiConfig(Request $request) {
        try {
            $jiConfigData = null;
            $jiConfigId = $request->get('ji_config_id');
            if(isset($jiConfigId) && !empty($jiConfigId)){
                $jiConfigData = $this->finRepo->getJiConfigByjiConfigId($jiConfigId); 
            }
            $jeConfigId = $request->get('je_config_id');       
            $this->accounts = $this->finRepo->getAllAccount()->get();      
            $jeConfigData = $this->finRepo->getJeConfigByjeConfigId($jeConfigId);       
            if(isset($jeConfigData->je_config_id) && !empty($jeConfigData->je_config_id)) {
                $this->variables = explode(',', $jeConfigData->variable_name);
            }
            return view('backend.finance.ji_config')
                ->with([
                'jeConfigId'=> $jeConfigId,
                'variables'=> $this->variables,
                'accounts' => $this->accounts,
                'jiConfigData' => $jiConfigData
                ]);
        } catch (Exception $ex) {
            return redirect()->back()->withErrors(Helpers::getExceptionMessage($ex));
        }       
    }

    public function saveJiConfig(CreateJiConfigRequest $request) {
        try {
            $jiConfigId = $request->get('ji_config_id');
            $this->inputData = [];
            $this->inputData = [                
                'account_id'=>$request->get('account'),
                'is_partner'=>$request->get('is_partner'),
                'label'=>$request->get('label'),                
                'value_type'=>$request->get('value_type'),
                'config_value'=>$request->get('config_value'),
                'je_config_id'=>$request->get('je_config_id')
            ];

            if(isset($jiConfigId) && !empty($jiConfigId)){
                $outputQryJi = $this->finRepo->saveJiData($this->inputData, $jiConfigId);
                if(isset($outputQryJi)) {
                    Session::flash('message','Journal item updated successfully');
                } else {
                    Session::flash('error','Journal item not updated, Please try later.');
                }   
            } else {
                $outputQryJi = $this->finRepo->saveJiData($this->inputData, null);
                if(isset($outputQryJi->ji_config_id)) {
                    Session::flash('message','Journal item saved successfully');
                } else {
                    Session::flash('error','Journal item not saved, Please try later.');
                }   
            }
            return redirect()->back();                      
        } catch (Exception $ex) {
            return redirect()->back()->withErrors(Helpers::getExceptionMessage($ex));
        }
    }

    public function saveJournal(CreateJournalRequest $request) {
        try {
            $journalId = $request->get('journalId');
            $this->inputData = [];
            $this->inputData = [                
                'name'=>$request->get('name'),               
                'journal_type'=>$request->get('journal_type'),
                'is_active'=>$request->get('is_active')
            ];

            if(isset($journalId) && !empty($journalId)){
                $outputQry = $this->finRepo->saveJournalData($this->inputData, $journalId);
                if(isset($outputQry)) {
                    Session::flash('message','Journal updated successfully');
                } else {
                    Session::flash('error','Journal not updated, Please try later.');
                }   
            } else {
                $outputQry = $this->finRepo->saveJournalData($this->inputData, null);
                if(isset($outputQry->id)) {
                    Session::flash('message','Journal saved successfully');
                } else {
                    Session::flash('error','Journal not saved, Please try later.');
                }   
            }
            return redirect()->back();                      
        } catch (Exception $ex) {
            return redirect()->back()->withErrors(Helpers::getExceptionMessage($ex));
        }
    }

    public function saveAccount(CreateAccountRequest $request) {
        try {
            $accountId = $request->get('accountId');
            $this->inputData = [];
            $this->inputData = [                
                'account_code'=>$request->get('account_code'),               
                'account_name'=>$request->get('account_name'),
                'is_active'=>$request->get('is_active')
            ];

            if(isset($accountId) && !empty($accountId)){
                $outputQry = $this->finRepo->saveAccountData($this->inputData, $accountId);
                if(isset($outputQry)) {
                    Session::flash('message','Account updated successfully');
                } else {
                    Session::flash('error','Account not updated, Please try later.');
                }   
            } else {
                $outputQry = $this->finRepo->saveAccountData($this->inputData, null);
                if(isset($outputQry->id)) {
                    Session::flash('message','Account saved successfully');
                } else {
                    Session::flash('error','Account not saved, Please try later.');
                }   
            }
            return redirect()->back();                      
        } catch (Exception $ex) {
            return redirect()->back()->withErrors(Helpers::getExceptionMessage($ex));
        }
    }

    public function getFinTransactions() {
        $latestBatchData = $this->finRepo->getLatestBatch();
        $latest_batch_no = NULL;
        if (!empty($latestBatchData)) {
            $latest_batch_no = $latestBatchData->batch_no;
        }
        return view('backend.finance.transactions', compact('latest_batch_no'));
    }

    public function getFinBatches() {
        return view('backend.finance.batches');
    }

    public function exportFactPaymentTransactions(Request $request){
        self::processFactPaymentTransactions($request->get('batch_no'));
    }

    public function processFactPaymentTransactions($batch_no) {
        try {
            \DB::beginTransaction();
            ini_set("memory_limit", "-1");
            $batch_no = $batch_no ?? NULL;
            $where = [];
            if (!empty($batch_no)) {
                $where = ['batch_no' => $batch_no];
            }
            $tallyData = \DB::table('tally')->select('is_fact_payment_generated')->where(['batch_no'=> $batch_no])->first();
            if($tallyData->is_fact_payment_generated == "1"){
                \DB::rollback();
                Session::flash('error', 'Fact Payment File is already in process');
                return redirect()->back();
            }
            if($tallyData->is_fact_payment_generated == "0"){
                $tallyBatch = \DB::table('tally')->where('batch_no',$batch_no)->update(['is_fact_payment_generated'=>1]);
            }
            \DB::commit();
            \DB::beginTransaction();
            $tallyData = \DB::table('tally')->select('is_fact_payment_generated')->where(['batch_no'=> $batch_no])->first();
            $result = $this->finRepo->getPaymentFactTxns($where);
            $records = [];
            $payment = array();
            $records['PAYMENT'] = array();
            $cr_amount_sum = 0;

            $transType = "";
            $voucher_date = "";
            $transDate = "";
            if (!empty($result)) {
                $factBankCodes = []; // Initialize an empty array

                $comp_bank_list = Helpers::getAllCompBankAccList(config('lms.COMP_ADDR_ID'));
                foreach($comp_bank_list as $cmp_bank_details){
                    $bankName = $cmp_bank_details->bank_name;
                    $bankAcNo = $cmp_bank_details->acc_no;
                    $factBankCode = $cmp_bank_details->fact_bank_code;
                
                    if (!isset($factBankCodes[$bankName])) {
                        $factBankCodes[$bankName] = [];
                    }
                
                    $factBankCodes[$bankName][$bankAcNo] = $factBankCode;  
                }

                foreach ($result as $key => $value) {
                    $bankName = '';
                    $bankAcNo = '';
                    $new[] = $fetchedArr = (array)$value;
                    $batchNo = $fetchedArr['batch_no'];
                    $voucherDate = date('d-m-Y',strtotime($fetchedArr['voucher_date']));
                    $trans_date = date('Y-m-d', strtotime($fetchedArr['voucher_date'])); 
                    $transaction_date = $fetchedArr['transaction_date']?Helpers::utcToIst($fetchedArr['transaction_date'],'Y-m-d H:i:s', 'd-m-Y'):NULL;
                    $entry_type = strtolower($fetchedArr['entry_type']);
                    $is_first_n_old = (empty($transType) || empty($transDate) || ($transType == $fetchedArr['trans_type'] && $transDate == $trans_date));
                    $code = '2SG00000S';
                    $GLcode = '54000021';
                    $amount = '';
                    $accNo = '';
                    $bankCode = null;
                    
                    if($fetchedArr['voucher_type'] == 'Payment'){
                        $amount = $fetchedArr['amount'];
                        $bankName = $fetchedArr['bank'];
                        $bankAcNo = $fetchedArr['bank_acc_no'];

                        // Check if the bank name and account number combination exists in the factBankCodes array
                        if (isset($factBankCodes[$bankName]) && isset($factBankCodes[$bankName][$bankAcNo])) {
                            $bankCode = $factBankCodes[$bankName][$bankAcNo];
                        }
                    }elseif($fetchedArr['voucher_type'] == 'Receipt'){
                        $amount = $fetchedArr['amount'];
                        $bankName = $fetchedArr['company_bank_name'];
                        $bankAcNo = $fetchedArr['company_bank_acc'];
                        
                        // Check if the bank name and account number combination exists in the factBankCodes array
                        if (isset($factBankCodes[$bankName]) && isset($factBankCodes[$bankName][$bankAcNo])) {
                            $bankCode = $factBankCodes[$bankName][$bankAcNo];
                        }
                    }
                   
                    $paymentRow =  [
                        "voucher" => $fetchedArr['fact_voucher_number'],
                        "sr"=>'',
                        "date" => $transaction_date,
                        "description" => $fetchedArr['trans_type'],
                        "chq_/_ref_number"=> $fetchedArr['utr_no'],
                        "dt_value" => $voucherDate,
                        "fc_amount" => '0',
                        "amount" => $amount,
                        "bank_code" => $bankCode,
                        "bank_name" => $bankName,
                        "account_no" => $bankAcNo,
                        "payment_vendor_name" => $fetchedArr['ledger_name'],
                        "paid_to_client" => $fetchedArr['ledger_name'],
                        "code" => $code,
                        "remarks" => $fetchedArr['narration'],
                        "type" => '',
                        "gL_code" => $GLcode,
                        "remark" => '',
                        "upload_status" => '',
                        "vendor_code_exists" => '',
                    ];
                    $records['PAYMENT'][] = $paymentRow;
                    
                    $transType = $fetchedArr['trans_type'];
                    $transDate = date('Y-m-d', strtotime($fetchedArr['voucher_date'])); 
                }
            }


            $toExportData = $records;
            if($tallyData->is_fact_payment_generated == "1"){
                $payments = $records['PAYMENT'];
                if(!empty($payments)){
                    if(isset($batch_no)){
                        array_walk($payments,function(&$value,$key) use ($batch_no){ $value['batch_no'] = $batch_no; });
                    }
                    foreach($payments as $key => $payment){
                        $payments[$key]['date'] = date('Y-m-d', strtotime($payment['date']));
                        $payments[$key]['dt_value'] = date('Y-m-d', strtotime($payment['dt_value']));
                    }
                    $data = FactPaymentEntry::insert($payments);
                }
                $tallyBatch = \DB::table('tally')->where('batch_no',$batch_no)->update(['is_fact_payment_generated'=>2]);
            }
            \DB::commit();
            $isFileSave = true;
            $dirPath = '/public/factDocument/tally_'.$batch_no;
            return $this->facPaymentFileExcel($toExportData['PAYMENT'], "Fact-Payment-$batch_no.xlsx",$isFileSave,$dirPath);
        }catch (Exception $ex) {
            \DB::rollback();
            return redirect()->back()->withErrors(Helpers::getExceptionMessage($ex));
        }
    }

    public function downloadFactPaymentTransactions(Request $request) {
        try {
            ini_set("memory_limit", "-1");
            $batch_no = $request->get('batch_no') ?? NULL;
            $where = [];
            if (!empty($batch_no)) {
                $where = ['batch_no' => $batch_no];
            }
            $result = $this->finRepo->getPaymentReportData($where);
            $records = [];
            $payment = array();
            $records['PAYMENT'] = array();
            $cr_amount_sum = 0;

            $transType = "";
            $voucher_date = "";
            $transDate = "";
            if (!empty($result)) {
                foreach ($result as $key => $value) {
                    
                        $new[] = $fetchedArr = (array)$value;
                        $voucher_date = $fetchedArr['date']?Helpers::utcToIst($fetchedArr['date'],'Y-m-d H:i:s', 'd-m-Y'):NULL;
                        $transaction_date = $fetchedArr['dt_value']?Helpers::utcToIst($fetchedArr['dt_value'],'Y-m-d H:i:s', 'd-m-Y'):NULL;
                            $paymentRow =  [
                                "voucher" => $fetchedArr['voucher'],
                                "sr"=>$fetchedArr['sr'],
                                "date" => $voucher_date,
                                "description" => $fetchedArr['description'],
                                "chq_/_ref_number"=> $fetchedArr['chq_/_ref_number'],
                                "dt_value" => $transaction_date,
                                "fc_amount" => '0',
                                "amount" => $fetchedArr['amount'],
                                "bank_code" => $fetchedArr['bank_code'],
                                "bank_name" => $fetchedArr['bank_name'],
                                "account_no" => $fetchedArr['account_no'],
                                "payment_vendor_name" => $fetchedArr['payment_vendor_name'],
                                "paid_to_client" => $fetchedArr['paid_to_client'],
                                "code" => $fetchedArr['code'],
                                "remarks" => $fetchedArr['remarks'],
                                "type" => '',
                                "gL_code" => $fetchedArr['gL_code'],
                                "remark" => '',
                                "upload_status" => '',
                                "vendor_code_exists" => '',
                            ];
                            $records['PAYMENT'][] = $paymentRow;
                }
            }

            if (empty($records['PAYMENT'])) {
                $records['PAYMENT'][] =  [
                    "voucher" => '',
                    "sr"=>'',
                    "date" => '',
                    "description" => '',
                    "chq_/_ref_number"=> '',
                    "dt_value" => '',
                    "fc_amount" => '',
                    "amount" => '',
                    "bank_code" => '',
                    "bank_name" => '',
                    "account_no" => '',
                    "payment_vendor_name" => '',
                    "paid_to_client" => '',
                    "code" => '',
                    "remarks" => '',
                    "type" => '',
                    "gL_code" => '',
                    "remark" => '',
                    "upload_status" => '',
                    "vendor_code_exists" => '',
                ];
            }

            $toExportData = $records;
            return $this->facPaymentFileExcel($toExportData['PAYMENT'], "Fact-Payment-$batch_no.xlsx");
        }catch (Exception $ex) {
            return redirect()->back()->withErrors(Helpers::getExceptionMessage($ex));
        }
        
    }

    public function exportFactJournalTransactions(Request $request){
        self::processFactJournalTransactions($request->get('batch_no'));
    }

    public function processFactJournalTransactions($batch_no) {
        try {
            \DB::beginTransaction();
            ini_set("memory_limit", "-1");
            $batch_no = $batch_no ?? NULL;
            $where = [];
            if (!empty($batch_no)) {
                $where = ['batch_no' => $batch_no,'voucher_type' => 'Journal'];
            }
            $tallyData = \DB::table('tally')->select('is_fact_journal_generated')->where(['batch_no'=> $batch_no])->first();
            if($tallyData->is_fact_journal_generated == "1"){
                \DB::rollback();
                Session::flash('error', 'Fact Jornal File is already in process');
                return redirect()->back();
            }
            if($tallyData->is_fact_journal_generated == "0"){
                $tallyBatch = \DB::table('tally')->where('batch_no',$batch_no)->update(['is_fact_journal_generated'=>1]);
            }
            \DB::commit();
            \DB::beginTransaction();
            $tallyData = \DB::table('tally')->select('is_fact_journal_generated')->where(['batch_no'=> $batch_no])->first();
            $result = $this->finRepo->getTallyTxns($where);
            $factTransDebit = $factTransCredit = [];
            $factTransTypeData = FactTransType::get()->toArray();
            foreach($factTransTypeData as $key => $code){
                $factTransDebit[strtolower($code['trans_type'])] = $code['debit_gl_code'];
                $factTransCredit[strtolower($code['trans_type'])] = $code['credit_gl_code'];
            }
            $records = [];
            $records['JOURNAL'] = array();
            $cr_amount_sum = 0;

            $transType = "";
            $voucher_date = "";
            $transDate = "";
            $factGstHand = [];
            if (!empty($result)) {
            foreach ($result as $key => $value) {
                
                    $new[] = $fetchedArr = (array)$value;
                    $batchNo = $fetchedArr['batch_no'];
                    $voucherDate = date('d-m-Y',strtotime($fetchedArr['voucher_date']));
                    $trans_date = date('Y-m-d', strtotime($fetchedArr['voucher_date'])); 
                    $transaction_date = $fetchedArr['transaction_date']?Helpers::utcToIst($fetchedArr['transaction_date'],'Y-m-d H:i:s', 'd-m-Y'):NULL;
                    $entry_type = strtolower($fetchedArr['entry_type']);
                    $documentClass = '2SG00000S';
                    $entryType = '';
                    $amount = '';
                    $ledgerName = '';
                    if($entry_type == 'credit'){
                        $entryType = 'C';
                    }else{
                        $entryType = 'D';
                    }
                    if($entry_type == 'credit'){
                        $amount = $fetchedArr['amount'];
                    }else{
                        $amount = $fetchedArr['amount'];
                    }
                    $debitGlCode = $factTransDebit[strtolower($fetchedArr['trans_type'])] ?? null;
                    $creditGlCode = $factTransCredit[strtolower($fetchedArr['trans_type'])] ?? null;
                    $is_first_n_old = (empty($transType) || empty($transDate) || ($transType == $fetchedArr['trans_type'] && $transDate == $trans_date));
                    $transType = strtolower($fetchedArr['trans_type']);
                    if($fetchedArr['entry_type'] == 0 && $fetchedArr['parent_trans_id'] == null){
                        if (strpos($transType, 'sgst - ') !== false) {
                            $creditGlCode = $factTransCredit['sgst'];
                            $debitGlCode = $factTransDebit['sgst'];
                        }
                        if (strpos($transType, 'cgst - ') !== false) {
                            $creditGlCode = $factTransCredit['cgst'];
                            $debitGlCode = $factTransDebit['cgst'];
                        }
                        if (strpos($transType, 'igst - ') !== false) {
                            $creditGlCode = $factTransCredit['igst'];
                            $debitGlCode = $factTransDebit['igst'];
                        }
                    }elseif($fetchedArr['entry_type'] == 1 && $fetchedArr['trans_type_id'] == 8){
                        if (strpos($transType, 'sgst - ') !== false) {
                            $creditGlCode = $factTransCredit['sgst cancelled'];
                            $debitGlCode = $factTransDebit['sgst cancelled'];
                        }
                        if (strpos($transType, 'cgst - ') !== false) {
                            $creditGlCode = $factTransCredit['cgst cancelled'];
                            $debitGlCode = $factTransDebit['cgst cancelled'];
                        }
                        if (strpos($transType, 'igst - ') !== false) {
                            $creditGlCode = $factTransCredit['igst cancelled'];
                            $debitGlCode = $factTransDebit['igst cancelled'];
                        } 
                    }elseif($fetchedArr['entry_type'] == 1 && $fetchedArr['trans_type_id'] == 36){
                        if (strpos($transType, 'sgst - ') !== false) {
                            $creditGlCode = $factTransCredit['sgst waived off'];
                            $debitGlCode = $factTransDebit['sgst waived off'];
                        }
                        if (strpos($transType, 'cgst - ') !== false) {
                            $creditGlCode = $factTransCredit['cgst waived off'];
                            $debitGlCode = $factTransDebit['cgst waived off'];
                        }
                        if (strpos($transType, 'igst - ') !== false) {
                            $creditGlCode = $factTransCredit['igst waived off'];
                            $debitGlCode = $factTransDebit['igst waived off'];
                        }
                    }
                    

                    $records['JOURNAL'][] = [
                        "voucher_no" => $fetchedArr['fact_voucher_number'],
                        "voucher_date"=> $transaction_date,
                        "voucher_narration" => ($entry_type == 'credit' ? $fetchedArr['trans_type'] : $fetchedArr['ledger_name']),
                        "general_ledger_code" => $debitGlCode,
                        "document_class"=>$documentClass,
                        "d_/_c" => 'D',
                        "amount" => $amount,
                        "description" => $fetchedArr['narration'],
                        "item_serial_number" => $fetchedArr['trans_type'],
                        "tax_code" => '',
                        "name" => '',
                        "gST_hSN_code" => '',
                        "sAC_code" => '',
                        "gST_state_name" => '',
                        "address_line_1" => '',
                        "address_line_2" => '',
                        "address_line_3" => '',
                        "city" => '',
                        "country" => '',
                        "postal_code" => '',
                        "telephone_number" => '',
                        "mobile_phone_number" => '',
                        "fAX" => '',
                        "email" => '',
                        "gST_identification_number_(GSTIN)" => '',
                    ];
                    
                    $records['JOURNAL'][] = [
                        "voucher_no" => $fetchedArr['fact_voucher_number'],
                        "voucher_date"=> $transaction_date,
                        "voucher_narration" => ($entry_type == 'credit' ? $fetchedArr['ledger_name'] : $fetchedArr['trans_type']),
                        "general_ledger_code" => $creditGlCode,
                        "document_class"=>$documentClass,
                        "d_/_c" => 'C',
                        "amount" => $amount,
                        "description" => $fetchedArr['narration'],
                        "item_serial_number" => $fetchedArr['trans_type'],
                        "tax_code" => '',
                        "name" => '',
                        "gST_hSN_code" => '',
                        "sAC_code" => '',
                        "gST_state_name" => '',
                        "address_line_1" => '',
                        "address_line_2" => '',
                        "address_line_3" => '',
                        "city" => '',
                        "country" => '',
                        "postal_code" => '',
                        "telephone_number" => '',
                        "mobile_phone_number" => '',
                        "fAX" => '',
                        "email" => '',
                        "gST_identification_number_(GSTIN)" => '',
                    ];
                }
            }


            $toExportData = $records;
            if($tallyData->is_fact_journal_generated == "1"){
                $journals = $records['JOURNAL'];
                if(!empty($journals)){
                    array_walk($journals,function(&$value,$key) use ($batch_no){ $value['batch_no'] = $batch_no; });
                    foreach($journals as $key => $journal){
                        $journals[$key]['voucher_date'] = date('Y-m-d', strtotime($journal['voucher_date']));
                    }
                    $data = FactJournalEntry::insert($journals);
                }
                $tallyBatch = \DB::table('tally')->where('batch_no',$batch_no)->update(['is_fact_journal_generated'=>2]);
            }
            \DB::commit();
            $isFileSave = true;
            $dirPath = '/public/factDocument/tally_'.$batch_no;
            return $this->facJournalFileExcel($toExportData['JOURNAL'], "Fact-Journal-$batch_no.xlsx",$isFileSave,$dirPath);
        }catch (Exception $ex) {
            \DB::rollback();
            return redirect()->back()->withErrors(Helpers::getExceptionMessage($ex));
        }
    }

    public function downloadFactJournalTransactions(Request $request) {
       
        try {
            ini_set("memory_limit", "-1");
            $batch_no = $request->get('batch_no') ?? NULL;
            $where = [];
            if (!empty($batch_no)) {
                $where = ['batch_no' => $batch_no];
            }
            $result = $this->finRepo->getJournalReportData($where);
            $factTransDebit = $factTransCredit = [];
            $factTransTypeData = FactTransType::get()->toArray();
            foreach($factTransTypeData as $key => $code){
                $factTransDebit[strtolower($code['trans_type'])] = $code['debit_gl_code'];
                $factTransCredit[strtolower($code['trans_type'])] = $code['credit_gl_code'];
            }
            $records = [];
            $records['JOURNAL'] = array();
            $cr_amount_sum = 0;

            $transType = "";
            $voucher_date = "";
            $transDate = "";
            if (!empty($result)) {
                foreach ($result as $key => $value) {
                    
                    $new[] = $fetchedArr = (array)$value;
                    $voucherDate = date('d-m-Y',strtotime($fetchedArr['voucher_date']));
                    $trans_date = date('Y-m-d', strtotime($fetchedArr['voucher_date'])); 
                    $records['JOURNAL'][] = [
                                "voucher_no" => $fetchedArr['voucher_no'],
                                "voucher_date"=> $voucherDate,
                                "voucher_narration" => ($fetchedArr['voucher_narration']),
                                "general_ledger_code" => $fetchedArr['general_ledger_code'],
                                "document_class"=>$fetchedArr['document_class'],
                                "d_/_c" => $fetchedArr['d_/_c'],
                                "amount" => $fetchedArr['amount'],
                                "description" => $fetchedArr['description'],
                                "item_serial_number" => $fetchedArr['item_serial_number'],
                                "tax_code" => '',
                                "name" => '',
                                "gST_hSN_code" => '',
                                "sAC_code" => '',
                                "gST_state_name" => '',
                                "address_line_1" => '',
                                "address_line_2" => '',
                                "address_line_3" => '',
                                "city" => '',
                                "country" => '',
                                "postal_code" => '',
                                "telephone_number" => '',
                                "mobile_phone_number" => '',
                                "fAX" => '',
                                "email" => '',
                                "gST_identification_number_(GSTIN)" => '',
                            ];
                            
                }
            }

            if (empty($records['JOURNAL'])) {
                $records['JOURNAL'][] =  [
                    "voucher_no" => '',
                    "voucher_date"=> '',
                    "voucher_narration" => '',
                    "general_ledger_code" => '',
                    "document_class"=> '',
                    "d_/_c" => '',
                    "amount" => '',
                    "description" => '',
                    "item_serial_number" => '',
                    "tax_code" => '',
                    "name" => '',
                    "gST_hSN_code" => '',
                    "sAC_code" => '',
                    "gST_state_name" => '',
                    "address_line_1" => '',
                    "address_line_2" => '',
                    "address_line_3" => '',
                    "city" => '',
                    "country" => '',
                    "postal_code" => '',
                    "telephone_number" => '',
                    "mobile_phone_number" => '',
                    "fAX" => '',
                    "email" => '',
                    "gST_identification_number_(GSTIN)" => '',
                ];
            }

            $toExportData = $records;
            return $this->facJournalFileExcel($toExportData['JOURNAL'], "Fact-Journal-$batch_no.xlsx");
        }catch (Exception $ex) {
            return redirect()->back()->withErrors(Helpers::getExceptionMessage($ex));
        }
    }

    public function facJournalFileExcel($data, $file_name = "", $isFileSave = false, $dirPath = null){
      $objSpreadsheet = new Spreadsheet();

      // Set document properties
      $objSpreadsheet->getProperties()->setCreator("Capsave Finance")
      ->setLastModifiedBy("Capsave Finance")
      ->setTitle($file_name)
      ->setSubject($file_name)
      ->setDescription($file_name)
      ->setKeywords($file_name)
      ->setCategory("Fact file Report");
      
      $headerStyle = array(
        'font' => array(
          'bold' => true,
        ),
        'alignment' => array(
          'horizontal' => Alignment::HORIZONTAL_CENTER,
        ),
        'borders' => array(
            'top' => array(
              'borderStyle' => Border::BORDER_THIN,
            ),
        ),
        'fill' => array(
          'fillType' => Fill::FILL_SOLID,
          'rotation' => 90,
          'startColor' => array(
              'argb' => 'FFA0A0A0',
          ),
          'endColor' => array(
              'argb' => 'FFFFFFFF',
          ),
        ),
      );

      // Set header 
      $objSpreadsheet->getActiveSheet()
        ->setCellValue('A1', 'Voucher No')
        ->setCellValue('B1', 'Voucher Date')
        ->setCellValue('C1', 'Voucher Narration')
        ->setCellValue('D1', 'General Ledger Code')
        ->setCellValue('E1', 'Document Class')
        ->setCellValue('F1', 'D / C')
        ->setCellValue('G1', 'Amount')
        ->setCellValue('H1', 'Description')
        ->setCellValue('I1', 'Item Serial Number')
        ->setCellValue('J1', 'Tax Code')
        ->setCellValue('K1', 'Name')
        ->setCellValue('L1', 'GST HSN Code')
        ->setCellValue('M1', 'SAC Code')
        ->setCellValue('N1', 'GST State Name')
        ->setCellValue('O1', 'Address Line 1')
        ->setCellValue('P1', 'Address Line 2')
        ->setCellValue('Q1', 'Address Line 3')
        ->setCellValue('R1', 'City')
        ->setCellValue('S1', 'Country')
        ->setCellValue('T1', 'Postal Code')
        ->setCellValue('U1', 'Telephone Number')
        ->setCellValue('V1', 'Mobile Phone Number')
        ->setCellValue('W1', 'FAX')
        ->setCellValue('X1', 'Email')
        ->setCellValue('Y1', 'GST Identification Number (GSTIN)')
        ->getStyle('A1:Y1')->applyFromArray($headerStyle);

      foreach(range('A','Y') as $columnID){
        $objSpreadsheet->getActiveSheet()->getColumnDimension($columnID)->setAutoSize(true);
      }

      // Set Data
      if(isset($data)){
        $dataStyle = array(
          'alignment' => array(
            'horizontal' => Alignment::HORIZONTAL_JUSTIFY,
          ),
          'borders' => array(
            'allborders' => array(
              'borderStyle' => Border::BORDER_THIN,
            ),
          ),
        );

        foreach($data as $key => $item){
          $excel_row = $key+2;
          $objSpreadsheet->getActiveSheet()
            ->setCellValue('A' . $excel_row, $item['voucher_no'])
            ->setCellValue('B' . $excel_row, Shared\Date::PHPToExcel($item['voucher_date']))
            ->setCellValue('C' . $excel_row, $item['voucher_narration'])
            ->setCellValue('D' . $excel_row, $item['general_ledger_code'])
            ->setCellValue('E' . $excel_row, $item['document_class'])
            ->setCellValue('F' . $excel_row, $item['d_/_c'])
            ->setCellValue('G' . $excel_row, $item['amount'])
            ->setCellValue('H' . $excel_row, $item['description'])
            ->setCellValue('I' . $excel_row, $item['item_serial_number'])
            ->setCellValue('J' . $excel_row, $item['tax_code'])
            ->setCellValue('K' . $excel_row, $item['name'])
            ->setCellValue('L' . $excel_row, $item['gST_hSN_code'])
            ->setCellValue('M' . $excel_row, $item['sAC_code'])
            ->setCellValue('N' . $excel_row, $item['gST_state_name'])
            ->setCellValue('O' . $excel_row, $item['address_line_1'])
            ->setCellValue('P' . $excel_row, $item['address_line_2'])
            ->setCellValue('Q' . $excel_row, $item['address_line_3'])
            ->setCellValue('R' . $excel_row, $item['city'])
            ->setCellValue('S' . $excel_row, $item['country'])
            ->setCellValue('T' . $excel_row, $item['postal_code'])
            ->setCellValue('U' . $excel_row, $item['telephone_number'])
            ->setCellValue('V' . $excel_row, $item['mobile_phone_number'])
            ->setCellValue('W' . $excel_row, $item['fAX'])
            ->setCellValue('X' . $excel_row, $item['email'])
            ->setCellValue('Y' . $excel_row, $item['gST_identification_number_(GSTIN)'])
            ->getStyle('B'.$excel_row)
            ->getNumberFormat()
            ->setFormatCode('dd-mm-yyyy');
        }
        $objSpreadsheet->getActiveSheet()->getStyle('A2:Y'.($key+2))->applyFromArray($dataStyle);
      }
      
      header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
      header('Content-Disposition: attachment;filename="' . $file_name . '"');
      header('Cache-Control: max-age=0');
      // If you're serving to IE 9, then the following may be needed
      header('Cache-Control: max-age=1');

      // If you're serving to IE over SSL, then the following may be needed
      header ('Expires: Mon, 26 Jul 1997 05:00:00 GMT'); // Date in the past
      header ('Last-Modified: '.gmdate('D, d M Y H:i:s').' GMT'); // always modified
      header ('Cache-Control: cache, must-revalidate'); // HTTP/1.1
      header ('Pragma: public'); // HTTP/1.0

      if ($isFileSave == true) {
        if (!Storage::exists($dirPath)) {
            Storage::makeDirectory($dirPath);
        }
        $filePath = '';
        $storage_path = Storage::path($dirPath);
        $filePath = $storage_path.'/'.$file_name;
        $tmpHandle = tmpfile();
        $metaDatas = stream_get_meta_data($tmpHandle);
        $tmpFilename = $metaDatas['uri'];
        $objWriter = IOFactory::createWriter($objSpreadsheet, 'Xlsx');
        $objWriter->save($tmpFilename); 
        $attributes['temp_file_path'] = $tmpFilename;
        $path = Helper::uploadAwsS3Bucket($storage_path, $attributes, $file_name);
        unlink($tmpFilename);
      }
      if(!App::runningInConsole()){
        $objWriter = IOFactory::createWriter($objSpreadsheet, 'Xlsx');
        $objWriter->save('php://output');
        exit;
      }
    }

    public function facPaymentFileExcel($data, $file_name = "", $isFileSave = false, $dirPath = null){
      $objSpreadsheet = new Spreadsheet();

      // Set document properties
      $objSpreadsheet->getProperties()->setCreator("Capsave Finance")
      ->setLastModifiedBy("Capsave Finance")
      ->setTitle($file_name)
      ->setSubject($file_name)
      ->setDescription($file_name)
      ->setKeywords($file_name)
      ->setCategory("Fact file Report");

      $headerStyle = array(
        'font' => array(
          'bold' => true,
        ),
        'alignment' => array(
          'horizontal' => Alignment::HORIZONTAL_CENTER,
        ),
        'borders' => array(
            'top' => array(
              'borderStyle' => Border::BORDER_THIN,
            ),
        ),
        'fill' => array(
          'fillType' => Fill::FILL_SOLID,
          'rotation' => 90,
          'startColor' => array(
              'argb' => 'FFA0A0A0',
          ),
          'endColor' => array(
              'argb' => 'FFFFFFFF',
          ),
        ),
      );

      // Set header 
      $objSpreadsheet->getActiveSheet()
        ->setCellValue('A1','Voucher')
        ->setCellValue('B1','Sr')
        ->setCellValue('C1','Date')
        ->setCellValue('D1','Description')
        ->setCellValue('E1','Chq / Ref Number')
        ->setCellValue('F1','Dt Value')
        ->setCellValue('G1','Fc Amount')
        ->setCellValue('H1','Amount')
        ->setCellValue('I1','Bank Code')
        ->setCellValue('J1','Bank Name')
        ->setCellValue('K1','Account No')
        ->setCellValue('L1','Payment Vendor Name')
        ->setCellValue('M1','Paid To Client')
        ->setCellValue('N1','Code')
        ->setCellValue('O1','Remarks')
        ->setCellValue('P1','Type')
        ->setCellValue('Q1','GL Code')
        ->setCellValue('R1','Remark')
        ->setCellValue('S1','Upload Status')
        ->setCellValue('T1','Vendor Code Exists')
        ->getStyle('A1:T1')->applyFromArray($headerStyle);

      // Set Header Style
      foreach(range('A','T') as $columnID){
        $objSpreadsheet->getActiveSheet()->getColumnDimension($columnID)->setAutoSize(true);
      }

      // Set Data
      if(isset($data)){
        $dataStyle = array(
          'alignment' => array(
            'horizontal' => Alignment::HORIZONTAL_JUSTIFY,
          ),
          'borders' => array(
            'allBorders' => array(
              'borderStyle' => Border::BORDER_THIN,
            ),
          ),
        );

        foreach($data as $key => $item) {
          $excel_row = $key + 2;
          $objSpreadsheet->getActiveSheet()
            ->setCellValue('A' . $excel_row, $item['voucher'])
            ->setCellValue('B' . $excel_row, $item['sr'])
            ->setCellValue('C' . $excel_row, Shared\Date::PHPToExcel($item['date']))
            ->setCellValue('D' . $excel_row, $item['description'])
            ->setCellValue('E' . $excel_row, $item['chq_/_ref_number'])
            ->setCellValue('F' . $excel_row, Shared\Date::PHPToExcel($item['dt_value']))
            ->setCellValue('G' . $excel_row, $item['fc_amount'])
            ->setCellValue('H' . $excel_row, $item['amount'])
            ->setCellValue('I' . $excel_row, $item['bank_code'])
            ->setCellValue('J' . $excel_row, $item['bank_name'])
            ->setCellValue('K' . $excel_row, $item['account_no'])
            ->setCellValue('L' . $excel_row, $item['payment_vendor_name'])
            ->setCellValue('M' . $excel_row, $item['paid_to_client'])
            ->setCellValue('N' . $excel_row, $item['code'])
            ->setCellValue('O' . $excel_row, $item['remarks'])
            ->setCellValue('P' . $excel_row, $item['type'])
            ->setCellValue('Q' . $excel_row, $item['gL_code'])
            ->setCellValue('R' . $excel_row, $item['remark'])
            ->setCellValue('S' . $excel_row, $item['upload_status'])
            ->setCellValue('T' . $excel_row, $item['vendor_code_exists']);
            
            $objSpreadsheet->getActiveSheet()
            ->getStyle('C'.$excel_row)
            ->getNumberFormat()
            ->setFormatCode('dd-mm-yyyy');

            $objSpreadsheet->getActiveSheet()
            ->getStyle('F'.$excel_row)
            ->getNumberFormat()
            ->setFormatCode('dd-mm-yyyy');
        }
      
        $objSpreadsheet->getActiveSheet()->getStyle('A2:T'.($key+2))->applyFromArray($dataStyle);
      }
    
      header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
      header('Content-Disposition: attachment;filename="' . $file_name . '"');
      header('Cache-Control: max-age=0');
      // If you're serving to IE 9, then the following may be needed
      header('Cache-Control: max-age=1');

      // If you're serving to IE over SSL, then the following may be needed
      header ('Expires: Mon, 26 Jul 1997 05:00:00 GMT'); // Date in the past
      header ('Last-Modified: '.gmdate('D, d M Y H:i:s').' GMT'); // always modified
      header ('Cache-Control: cache, must-revalidate'); // HTTP/1.1
      header ('Pragma: public'); // HTTP/1.0

      if ($isFileSave == true) {
        if (!Storage::exists($dirPath)) {
            Storage::makeDirectory($dirPath);
        }
        $filePath = '';
        $fileData = [];
        $storage_path = Storage::path($dirPath);
        $filePath = $storage_path.'/'.$file_name;
        $tmpHandle = tmpfile();
        $metaDatas = stream_get_meta_data($tmpHandle);
        $tmpFilename = $metaDatas['uri'];
        $objWriter = IOFactory::createWriter($objSpreadsheet, 'Xlsx');
        $objWriter->save($tmpFilename); 
        $attributes['temp_file_path'] = $tmpFilename;
        $path = Helper::uploadAwsS3Bucket($storage_path, $attributes, $file_name);
        unlink($tmpFilename);
        
      }
      if(!App::runningInConsole()){
        $objWriter = IOFactory::createWriter($objSpreadsheet, 'Xlsx');
        $objWriter->save('php://output');
        exit;
      }
    }
    
}
