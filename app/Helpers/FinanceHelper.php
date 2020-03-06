<?php

namespace App\Helpers;

class FinanceHelper {

    private $finRepo;
    private $jeConfigData;
    private $resp = [];
    public function __construct($finRepo = null) {
        $this->finRepo = $finRepo;
    }

    public function finExecution($transConfigId = null, $userId = null, $appId = null, $bizId = null){
        try{
            if(isset($transConfigId) && !empty($transConfigId)) {
                $this->jeConfigData = $this->finRepo->getAllJeConfigByTransConfigId($transConfigId); 
                if(isset($this->jeConfigData) && !empty($this->jeConfigData)) {
                    dd($this->jeConfigData);
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