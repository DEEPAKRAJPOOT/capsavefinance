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

class FinanceController extends Controller {

    private $finRepo;
    private $transType = [];
    private $variables = [];    
    private $journals = [];  
    private $accounts = [];
    private $inputData = [];

    public function __construct(FinanceInterface $finRepo) {
        $this->middleware('guest')->except('logout');
        $this->middleware('checkBackendLeadAccess');
        $this->finRepo = $finRepo;
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
            ]);;;
    }    

    public function getFinVariable() {
        return view('backend.finance.variable_list');
    }
    
    public function exportTransactions(Request $request) {
        $result = $this->finRepo->getAllTxns();
         $records[] = [
              'invoice_id' => "Invoice No",
              'journal_name' => "Vch Type",
              'date' => "Invoice Date",
              'f_name' => "Ledger Name",
              'debit_amount' => "Dr Amt",
              'dr_ref_no' => "Ref No",
              'dr_ref_amount' => "Ref Amt",
              'credit_amount' => "cr Amt",
              'cr_ref_no' => "Cr Ref No",
              'cr_ref_amount' => "Cr Ref Amt",
              'transtype' => "transtype",
              'is_partner' => "is_partner",
              'entry_type' => "entry_type",
              'naration' => "Narration",
            ];
        foreach ($result as $key => $value) {
           $ledger_name = $value['f_name']. ' '. $value['m_name'].' '. $value['l_name'];
           unset($value['m_name'],$value['l_name']);
           $value['f_name'] =  $ledger_name;
           $records[] = $value->toArray();
        }
        $this->array_to_csv($records, "execl.csv");
    }

    public function crateJeConfig(Request $request) {
         // $finHelperObj->finExecution(config('common.TRANS_CONFIG_TYPE.DISBURSAL'), $inv_arr['invoice_id'], $inv_arr['app_id'], $inv_arr['supplier_id'], $inv_arr['biz_id']); 
        $finHelperObj = new FinanceHelper($this->finRepo);
        $finHelperObj->finExecution(config('common.TRANS_CONFIG_TYPE.DISBURSAL'), 21);
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
        return view('backend.finance.transactions');
    }

    public function array_to_csv($array, $download = "") {
        if ($download != ""){    
            header('Content-Type: application/csv');
            header('Content-Disposition: attachement; filename="' . $download . '"');
        }        
        ob_start();
        $f = fopen('php://output', 'w') or die("Can't open php://output");
        $n = 0;     
        foreach ($array as $line){
            $n++;
            if (!fputcsv($f, $line)){
               continue;
            }
        }
        $str = ob_get_contents();
        ob_end_clean();
        if ($download == ""){
            return $str;    
        }else{    
            echo $str;
        }        
    }
}
