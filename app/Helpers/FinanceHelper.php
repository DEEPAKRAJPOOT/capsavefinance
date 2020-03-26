<?php

namespace App\Helpers;

use App\Inv\Repositories\Models\Financial\FinancialDisbursal as Disbursal;
use App\Inv\Repositories\Models\Lms\Transactions as Transaction;
class FinanceHelper {

    private $transConfigId;
    private $finRepo;
    private $jeConfigData;
    private $jiConfigData;
    private $resp = [];
    private $inputData = [];
    public function __construct($finRepo = null) {
        $this->finRepo = $finRepo;
    }

    public function finExecution($transConfigId = null, $invoice_txn_id = null, $appId = null, $userId = null, $bizId = null) {
        try{
            $this->transConfigId = $transConfigId;
            if(isset($transConfigId) && !empty($transConfigId)) {
                $this->jeConfigData = $this->finRepo->getAllJeConfigByTransConfigId($transConfigId); 
                if(isset($this->jeConfigData) && !empty($this->jeConfigData)) {
                    foreach($this->jeConfigData as $key=>$val) {                        
                        $this->jiConfigData = $this->finRepo->getAllJiConfig($val->je_config_id);
                        if(isset($this->jiConfigData) && !empty($this->jiConfigData)) {
                            $this->inputData = [];
                            $this->inputData = [ 
                                'journal_id' => $val->journal_id,
                                'entry_type' => $val->trans_type,
                                'invoice_id' => ($transConfigId == 1 ? $invoice_txn_id : null),       //need to update
                                'trans_id' => ($transConfigId != 1 ? $invoice_txn_id : null),       //need to update
                                'user_id' => (int)$userId ?? 0 ,
                                'reference' => _getRand(15),
                                'date' => \Carbon\Carbon::now()->format('Y-m-d h:i:s')
                            ];
                            $outputQryJE = $this->finRepo->saveJournalEntries($this->inputData);
                            if(isset($outputQryJE->journal_entry_id) && !empty($outputQryJE->journal_entry_id)) {
                                foreach($this->jiConfigData as $jikey=>$jival) {  
                                    $this->inputData = [];
                                    $this->inputData = [ 
                                        'ji_config_id' => $jival->ji_config_id,               
                                        'date' => \Carbon\Carbon::now()->format('Y-m-d h:i:s'),
                                        'account_id' => $jival->account_id,
                                        'label' => $jival->label,
                                        'journal_id' => $val->journal_id,
                                        'journal_entry_id' => $outputQryJE->journal_entry_id 
                                    ]; 

                                    $formula = $jival->config_value;
                                    $sysParameterStr = $val->variable_name;
                                    $sysFunctionStr = $val->sys_func_name;
                                    $amount = 0;
                                    $amount = $this->getAmtByFormulaCal($formula, explode(',',$sysParameterStr), explode(',',$sysFunctionStr), $invoice_txn_id, $appId, $userId, $bizId);
                                    if($jival->value_type_val==1) {     //credit
                                        $this->inputData['credit_amount'] = $amount;
                                    } else {                            //debit
                                        $this->inputData['debit_amount'] = $amount;
                                    }

                                    if($jival->is_partner_val==1) {     
                                        $this->inputData['biz_id'] =  $bizId ?? '1'; //need to update
                                    }
                                   
                                    $outputQryJI = $this->finRepo->saveJournalItems($this->inputData);
                                    if(isset($outputQryJI->journal_item_id) && !empty($outputQryJI->journal_item_id)) {
                                        $this->resp['success'] = true;
                                        $this->resp['errorMsg'] = 'Journal item saved';
                                    } else {
                                        $this->resp['success'] = false;
                                        $this->resp['errorMsg'] = 'Journal item not saved';
                                    }
                                }   
                            } else {
                                $this->resp['success'] = false;
                                $this->resp['errorMsg'] = 'Journal entry not saved';
                            }                      
                        } else {
                            $this->resp['success'] = false;
                            $this->resp['errorMsg'] = 'Ji Configuration not found';
                        }                        
                    }                    
                } else {
                    $this->resp['success'] = false;
                    $this->resp['errorMsg'] = 'JE Configuration not found';
                }                
            } else {
                $this->resp['success'] = false;
                $this->resp['errorMsg'] = 'Transaction config id is blank';
            }
            return $this->resp;
        } catch (Exception $ex) {
            throw new Error('Something wrong please try later');
        }        
    }

    private function getAmtByFormulaCal($formula=null, $sysParameterStr=null, $sysFunctionStr=null, $invoice_txn_id=null, $appId = null, $userId = null, $bizId = null) {
        $varFuncArr = array_combine($sysParameterStr, $sysFunctionStr);
        foreach ($varFuncArr as $variable => $function) {
           $funcName = '_'.$function;
           $var_val = $this->$funcName($variable, $invoice_txn_id, $appId, $userId, $bizId);
           $varFuncArr[$variable] = $var_val;
        }

        $formula_vars = array_values(array_filter(explode(' ',preg_replace("/[^a-zA-Z_]/", " ", $formula))));
        $stored_vars = array_keys($varFuncArr);
        if(!empty(array_diff($formula_vars, $stored_vars))){
          return 0;
        }
        return calculate_formula($formula, $varFuncArr);
    }

    private function _calculateFormula($formula, $variables){
      extract($variables);
      $script = preg_replace('/\s+/', '', $formula);
      foreach ($variables as $key => $value) {
        $var_val = $$key;
        $script = str_replace($key, $var_val , $script);
      }
      return eval("return $script;");
  } 

    private function _sysFuncPrincipal($variable, $invoice_id = null, $appId = null, $userId = null, $bizId = null){
       $disbursalData = Disbursal::where('invoice_id', $invoice_id)->first();
       return (!empty($disbursalData) ? $disbursalData->principal_amount : 0);
    }

    public function __call($function, $args){
        $variable = $args[0] ?? null;
        $invoice_txn_id = $args[1] ?? null;
      switch ($variable) {
          case 'PRINCIPAL':
              $amt = $this->_sysFuncPrincipal($variable, $invoice_txn_id);
              break;
          case 'RATE':
               $amt = $this->_sysFuncRate($variable, $invoice_txn_id);
              break;
          case 'TENOR':
               $amt = $this->_sysFuncTenor($variable, $invoice_txn_id);
              break;
          case 'OD_INTEREST_RATE':
               $amt = $this->sysFuncOdIntRate($variable, $invoice_txn_id);
              break;
          case 'REPAYMENT_AMOUNT':
               $amt = $this->sysFuncRepayAmt($variable, $invoice_txn_id);
              break;
          default:
               $amt = 0;
              break;
      }
      return $amt;
    }

    private function _sysFuncRate($variable, $invoice_id = null, $appId = null, $userId = null, $bizId = null){
       $disbursalData = Disbursal::where('invoice_id', $invoice_id)->first();
       return (!empty($disbursalData) ? $disbursalData->interest_rate : 0);
    }

    private function _sysFuncTenor($variable, $invoice_id = null, $appId = null, $userId = null, $bizId = null){
       $disbursalData = Disbursal::where('invoice_id', $invoice_id)->first();
       return (!empty($disbursalData) ? $disbursalData->tenor_days : 0);
    }

    private function sysFuncOdIntRate($variable, $invoice_id = null, $appId = null, $userId = null, $bizId = null){
       $disbursalData = Disbursal::where('invoice_id', $invoice_id)->first();
       return (!empty($disbursalData) ? $disbursalData->overdue_interest_rate : 0);
    }

    private function sysFuncRepayAmt($variable, $invoice_txn_id = null, $appId = null, $userId = null, $bizId = null){
       $transactionData = Transaction::find($invoice_txn_id);
       return (!empty($transactionData) ? $transactionData->amount : 0);
    }
}
