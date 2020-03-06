<?php

namespace App\Helpers;

class FinanceHelper {

    private $finRepo;
    private $resp = [];
    public function __construct($finRepo = null) {
        $this->finRepo = $finRepo;
    }

    public function finExecution($transTypeId = null, $userId = null, $appId = null, $bizId = null){
        try{
            if(isset($transTypeId) && !empty($transTypeId)) {
                $this->resp['success'] = true;
                $this->resp['errorMsg'] = 'Transaction type id is found';
            } else {
                $this->resp['success'] = false;
                $this->resp['errorMsg'] = 'Transaction type id is blank';
            }
            return $this->resp;
        } catch (Exception $ex) {
            throw new Error('Something wrong please try later');
        }        
    }
}