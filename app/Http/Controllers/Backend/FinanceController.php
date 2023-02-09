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

    public function exportFactPaymentTransactions(Request $request) {
    try {
        \DB::beginTransaction();
        ini_set("memory_limit", "-1");
        $batch_no = $request->get('batch_no') ?? NULL;
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
           foreach ($result as $key => $value) {
               
                $new[] = $fetchedArr = (array)$value;
                $voucherDate = date('d-m-Y',strtotime($fetchedArr['voucher_date']));
                $trans_date = date('Y-m-d', strtotime($fetchedArr['voucher_date'])); 
                $transaction_date = $fetchedArr['transaction_date']?Helpers::utcToIst($fetchedArr['transaction_date'],'Y-m-d H:i:s', 'd-m-Y'):NULL;
                $entry_type = strtolower($fetchedArr['entry_type']);
                $is_first_n_old = (empty($transType) || empty($transDate) || ($transType == $fetchedArr['trans_type'] && $transDate == $trans_date));
                $code = '2SG00000S';
                $GLcode = '54000021';
                $amount = '';
                $accNo = '';
                if($fetchedArr['voucher_type'] == 'Payment'){
                    if($entry_type == 'credit'){
                        $amount = $fetchedArr['amount'];
                    }else{
                        $amount = '-'.$fetchedArr['amount'];
                    }  
                }else{
                    if($entry_type == 'credit'){
                        $amount = $fetchedArr['amount'];
                    }else{
                        $amount = '-'.$fetchedArr['amount'];
                    }  
                }
                $bankCode = null;
                if($fetchedArr['bank'] == 'IDFC Bank' && $fetchedArr['bank_acc_no'] == '10006748999'){
                    $bankCode = 4;
                }elseif($fetchedArr['bank'] == 'IDFC Bank' && $fetchedArr['bank_acc_no'] == '10062193074'){
                    $bankCode = 9;
                }elseif($fetchedArr['bank'] == 'IDFC Bank' && $fetchedArr['bank_acc_no'] == '10047035004'){
                    $bankCode = 3;
                }elseif($fetchedArr['bank'] == 'HDFC Bank' && $fetchedArr['bank_acc_no'] == '50200030310781'){
                    $bankCode = 19;
                }elseif($fetchedArr['bank'] == 'Yes Bank' && $fetchedArr['bank_acc_no'] == '007884600002532'){
                    $bankCode = 11;
                }
                    $paymentRow =  [
                        "voucher" => $fetchedArr['fact_voucher_number'],
                        "sr"=>'',
                        "date" => $voucherDate,
                        "description" => $fetchedArr['trans_type'],
                        "chq_/_ref_number"=> $fetchedArr['utr_no'],
                        "dt_value" => $transaction_date,
                        "fc_amount" => '0',
                        "amount" => $amount,
                        "bank_code" => $bankCode,
                        "bank_name" => $fetchedArr['bank'],
                        "account_no" => $fetchedArr['bank_acc_no'],
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
        if($tallyData->is_fact_payment_generated == "1"){
            $payments = $records['PAYMENT'];
            foreach($payments as $key => $payment){
                $payments[$key]['date'] = date('Y-m-d', strtotime($payment['date']));
            }
            $data = FactPaymentEntry::insert($payments);
            if(!empty($data)){
                $tallyBatch = \DB::table('tally')->where('batch_no',$batch_no)->update(['is_fact_payment_generated'=>2]);
            }
        }
        \DB::commit();
        return $this->fileHelper->array_to_excel($toExportData, "Fact-Payment-$batch_no.xlsx");
    }catch (Exception $ex) {
        \DB::rollback();
        return redirect()->back()->withErrors(Helpers::getExceptionMessage($ex));
    }
        
    }

    public function exportFactJournalTransactions(Request $request) {
        try {
            \DB::beginTransaction();
            ini_set("memory_limit", "-1");
            $batch_no = $request->get('batch_no') ?? NULL;
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
            $tallyData = \DB::table('tally')->select('is_fact_journal_generated')->where(['batch_no'=> $batch_no])->first();
            $result = $this->finRepo->getTallyTxns($where);
            $factTransDebit = $factTransCredit = [];
            $factTransTypeData = FactTransType::get()->toArray();
            foreach($factTransTypeData as $key => $code){
                $factTransDebit[strtolower($code['trans_type'])] = $code['debit_gl_code'];
                $factTransCredit[strtolower($code['trans_type'])] = $code['credit_gl_code'];
            }
            // dd($factTransDebit,$factTransCredit);
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
                    if (strpos($transType, '+') !== false) {
                        $factGstHand[$fetchedArr['fact_voucher_number']] = isset($factGstHand[$fetchedArr['fact_voucher_number']])?$factGstHand[$fetchedArr['fact_voucher_number']]+1:1;
                        if($factGstHand[$fetchedArr['fact_voucher_number']] == '1'){
                            $fetchedArr['trans_type'] = 'SGST';
                            $creditGlCode = $factTransCredit['sgst'];
                            $debitGlCode = $factTransDebit['sgst'];
                        }elseif($factGstHand[$fetchedArr['fact_voucher_number']] == '2'){
                            $fetchedArr['trans_type'] = 'CGST';
                            $creditGlCode = $factTransCredit['cgst'];
                            $debitGlCode = $factTransDebit['cgst'];
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
            if($tallyData->is_fact_journal_generated == "1"){
                $journals = $records['JOURNAL'];
                foreach($journals as $key => $journal){
                    $journals[$key]['voucher_date'] = date('Y-m-d', strtotime($journal['voucher_date']));
                }
                $data = FactJournalEntry::insert($journals);
                if(!empty($data)){
                    $tallyBatch = \DB::table('tally')->where('batch_no',$batch_no)->update(['is_fact_journal_generated'=>2]);
                }
            }
            \DB::commit();
            return $this->fileHelper->array_to_excel($toExportData, "Fact-Journal-$batch_no.xlsx");
        }catch (Exception $ex) {
            \DB::rollback();
            return redirect()->back()->withErrors(Helpers::getExceptionMessage($ex));
        }
    }
}
