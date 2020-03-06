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
                            foreach($this->jiConfigData as $jikey=>$jival) {  
                                //print_r($jival);
                                $this->inputData = [];
                                $this->inputData = [ 
                                    'ji_config_id' => $jival->ji_config_id,               
                                    'date' => \Carbon\Carbon::now()->format('Y-m-d h:i:s'),
                                    'account_id' => $jival->account_id,
                                    'label' => $jival->label,
                                    'journal_id' => $val->journal_id,
                                    'journal_entry_id' => 1 //need to update
                                ]; 

                                if($jival->value_type_val==1) {     //credit
                                    $this->inputData['credit_amount'] = '0';
                                } else {                            //debit
                                    $this->inputData['debit_amount'] = '0';
                                }

                                if($jival->is_partner_val==1) {     
                                    $this->inputData['biz_id'] = $bizId;
                                }
                                print_r($this->inputData);
                                $outputQry = $this->finRepo->saveJournalItems($this->inputData);
                            }                            
                        } else {
                            $this->resp['success'] = true;
                            $this->resp['errorMsg'] = 'Ji Configuration not found';
                        }                        
                    }
                    //dd($this->jeConfigData);
                    $this->resp['success'] = true;
                    $this->resp['errorMsg'] = '';
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
}