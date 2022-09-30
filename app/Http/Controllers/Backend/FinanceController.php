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
                $transaction_date = date('Y-m-d', strtotime($fetchedArr['transaction_date']));
                $entry_type = strtolower($fetchedArr['entry_type']);
                $is_first_n_old = (empty($transType) || empty($transDate) || ($transType == $fetchedArr['trans_type'] && $transDate == $trans_date));
                $j_is_first_or_old = NULL;
                if (strtolower($fetchedArr['voucher_type']) == 'journal') {
                    $jj = $fetchedArr;
                    $j_is_first_or_old  = $is_first_n_old;
                    $j = [
                        "batch_no" => $fetchedArr['batch_no'],
                        "voucher_no" => sprintf('%04d',$fetchedArr['voucher_no']),
                        "trans_type" => $fetchedArr['trans_type'],
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
                        "narration" => $fetchedArr['narration'] 
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
                        "voucher_type" => $fetchedArr['voucher_type'],
                        "voucher_date" =>  !empty($fetchedArr['voucher_date']) ? date('d-m-Y',strtotime($fetchedArr['voucher_date'])) : '',
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
                        "inst_no" => $fetchedArr['inst_no'],
                        "inst_date" => $fetchedArr['inst_date'],
                        "favoring_name" => $fetchedArr['favoring_name'],
                        "remarks" => $fetchedArr['remarks'],
                        "narration" => $fetchedArr['narration'],
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
                "inst_no" => '',
                "inst_date" => '',
                "favoring_name" => '',
                "remarks" => '',
                "narration" => '',
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
                "narration" => '' 
            ];
        }
        foreach ($records['JOURNAL'] as $key => $value) {
          unset($records['JOURNAL'][$key]['trans_type']);
          unset($records['JOURNAL'][$key]['batch_no']);
        }
        $toExportData = $records;
        return $this->fileHelper->array_to_excel($toExportData, "execl.xlsx");
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
}
