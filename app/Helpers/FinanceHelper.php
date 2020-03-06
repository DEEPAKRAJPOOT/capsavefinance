<?php

namespace App\Helpers;

class FinanceHelper {

    private $finRepo;
    private $transVariablesData;
    private $resp = [];
    public function __construct($finRepo = null) {
        $this->finRepo = $finRepo;
    }

    public function finExecution($transConfigId = null, $userId = null, $appId = null, $bizId = null){
        try{
            if(isset($transConfigId) && !empty($transConfigId)) {
                $this->transVariablesData = $this->finRepo->getVariablesByTransConfigId($transConfigId); 
                if(isset($this->transVariablesData) && !empty($this->transVariablesData[0]->trans_config_id)) {
                    print($this->transVariablesData);
                    $this->resp['success'] = true;
                    $this->resp['errorMsg'] = '';
                } else {
                    $this->resp['success'] = true;
                    $this->resp['errorMsg'] = 'Transaction config id is not found';
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