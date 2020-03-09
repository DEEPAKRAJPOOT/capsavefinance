<?php

namespace App\Helpers;

class FinanceHelper {

    private $finRepo;
    private $jeConfigData;
    private $jiConfigData;
    private $resp = [];
    private $inputData = [];
    public function __construct($finRepo = null) {
        $this->finRepo = $finRepo;
    }

    public function finExecution($transConfigId = null, $userId = null, $appId = null, $bizId = null) {
        try{
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
                                    $amount = $this->getAmtByFormulaCal($formula, explode(',',$sysParameterStr), explode(',',$sysFunctionStr), $userId, $appId, $bizId);
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
                    $this->resp['success'] = true;
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

    private function getAmtByFormulaCal($formula=null, $sysParameterStr=null, $sysFunctionStr=null, $userId = null, $appId = null, $bizId = null) {
        $varFuncArr = array_combine($sysParameterStr, $sysFunctionStr);
        foreach ($varFuncArr as $variable => $function) {
           $funcName = '_'.$function;
           $var_val = $this->$funcName($variable);
           $varFuncArr[$variable] = $var_val;
        }
        dd($varFuncArr);
        return 0;
    }

    private function _sysFuncPrincipal($userId = null, $appId = null, $bizId = null){
       return "1000";
    }
    private function _sysFuncRate($userId = null, $appId = null, $bizId = null){
      return "5.8";
    }
    private function _sysFuncTenor($userId = null, $appId = null, $bizId = null){
       return "3";
    }
}