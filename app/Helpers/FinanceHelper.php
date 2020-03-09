<?php

namespace App\Helpers;

use App\Inv\Repositories\Models\Financial\FinancialDisbursal as Disbursal;
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

    public function finExecution($transConfigId = null, $pk_val = null) {
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
                                'entry_type' => $val->trans_type,       //need to update
                                'reference' => $val->trans_type,        //need to update
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
                                    $amount = $this->getAmtByFormulaCal($formula, explode(',',$sysParameterStr), explode(',',$sysFunctionStr), $pk_val);
                                    if($jival->value_type_val==1) {     //credit
                                        $this->inputData['credit_amount'] = $amount;
                                    } else {                            //debit
                                        $this->inputData['debit_amount'] = $amount;
                                    }

                                    if($jival->is_partner_val==1) {     
                                        $this->inputData['biz_id'] = '1'; //need to update
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

    private function getAmtByFormulaCal($formula=null, $sysParameterStr=null, $sysFunctionStr=null, $pk_val=null) {
        $varFuncArr = array_combine($sysParameterStr, $sysFunctionStr);
        foreach ($varFuncArr as $variable => $function) {
           $funcName = '_'.$function;
           $var_val = $this->$funcName($pk_val);
           $varFuncArr[$variable] = $var_val;
        }
        return calculate_formula($formula, $varFuncArr);
    }


    private function _getObject($transConfigId){
        $objects = ['1' => new Disbursal()];
        return $objects[$transConfigId];
    }

    private function _sysFuncPrincipal($pk_val = null){
       $obj = $this->_getObject($this->transConfigId);
       $disbursalData = $obj::find($pk_val);
       return (!empty($disbursalData) ? $disbursalData->principal_amount : 0);
    }

    public function __call($function, $args){
      return 0;
    }

    private function _sysFuncRate($pk_val = null){
       $obj = $this->_getObject($this->transConfigId);
       $disbursalData = $obj::find($pk_val);
       return (!empty($disbursalData) ? $disbursalData->interest_rate : 0);
    }

    private function _sysFuncTenor($pk_val = null){
       $obj = $this->_getObject($this->transConfigId);
       $disbursalData = $obj::find($pk_val);
       return (!empty($disbursalData) ? $disbursalData->tenor_days : 0);
    }

    private function sysFuncOdIntRate($pk_val = null){
       $obj = $this->_getObject($this->transConfigId);
       $disbursalData = $obj::find($pk_val);
       return (!empty($disbursalData) ? $disbursalData->overdue_interest_rate : 0);
    }
}