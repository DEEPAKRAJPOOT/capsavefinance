<?php

namespace App\Inv\Repositories\Entities\Finance;

use App\Inv\Repositories\Contracts\FinanceInterface;
use App\Inv\Repositories\Factory\Repositories\BaseRepositories;
use App\Inv\Repositories\Models\Financial\FinancialTransConfig;
use App\Inv\Repositories\Models\Financial\FinancialJournals;
use App\Inv\Repositories\Models\Financial\FinancialAccount;
use App\Inv\Repositories\Models\Financial\FinancialVariables;
use App\Inv\Repositories\Models\Financial\FinancialJeConfig;
use App\Inv\Repositories\Models\Financial\FinancialJiConfig;
use App\Inv\Repositories\Models\Financial\FinancialVariablesTransConfig;
use App\Inv\Repositories\Models\Financial\FinancialJournalItems;
use App\Inv\Repositories\Models\Financial\FinancialJournalEntries;
use App\Inv\Repositories\Models\BankWorkCapitalFacility;
use App\Inv\Repositories\Models\BankTermBusiLoan;
use App\Inv\Repositories\Models\BankAnalysis;
use App\Inv\Repositories\Models\FinanceModel;

class FinanceRepository extends BaseRepositories implements FinanceInterface
{

    private $result;

    function __construct()
    {
        parent::__construct();
    }

    public function create(array $attributes)
    {
        //
    }

    public function update(array $attributes, $id)
    {
        //
    }

    public function destroy($ids)
    {
        //
    }

    public function getAllTransType()
    {
        $this->result = FinancialTransConfig::getAllTransType();
        return $this->result;
    }

    public function getAllJournal()
    {
        $this->result = FinancialJournals::getAllJournal();
        return $this->result;
    }
    
    public function getAllAccount()
    {
        $this->result = FinancialAccount::getAllAccount();
        return $this->result;
    }

    public function getAllVariable()
    {
        $this->result = FinancialVariables::getAllVariable();
        return $this->result;
    }

    public function saveJeData($arrData){
        return FinancialJeConfig::saveJeData($arrData);
    }

    public function saveTransVarData($arrData){
        return FinancialVariablesTransConfig::saveTransVarData($arrData);
    }

    public function getAllJeConfig()
    {
        return FinancialJeConfig::getAllJeConfig();         
    }

    public function getJeConfigByjeConfigId($jeConfigId)
    {
        return FinancialJeConfig::getJeConfigByjeConfigId($jeConfigId);         
    }

    public function saveJiData($arrData, $jiConfigId){
        return FinancialJiConfig::saveJiData($arrData, $jiConfigId);
    }

    public function getAllJiConfig($jeConfigId)
    {
        return FinancialJiConfig::getAllJiConfig($jeConfigId);         
    }

    public function getJiConfigByjiConfigId($jiConfigId)
    {
        return FinancialJiConfig::getJiConfigByjiConfigId($jiConfigId);         
    }    

    public function getVariablesByTransConfigId($transConfigId)
    {
        return FinancialTransConfig::where('trans_config_id', $transConfigId)->with('variables')->get(); 
    }

    public function syncTransVarData($arrData, $transConfigId)
    {
        $trans = FinancialTransConfig::find($transConfigId);
        return $trans->variablesMany()->sync($arrData);
    }
    
    public function saveJournalData($arrData, $journalId)
    {
        return FinancialJournals::saveJournalData($arrData, $journalId);
    }

    public function getJournalByJournalId($journalId)    
    {
        return FinancialJournals::where('id', $journalId)->first();         
    } 
    
    public function checkTransJeData($transTypeId, $journalId) 
    {
        return FinancialJeConfig::where(['trans_config_id'=> $transTypeId, 'journal_id'=> $journalId])->count();
    }

    public function saveAccountData($arrData, $accountId)
    {
        return FinancialAccount::saveAccountData($arrData, $accountId);
    }

    public function getAccountByAccountId($accountId)    
    {
        return FinancialAccount::where('id', $accountId)->first();         
    } 

    public function getAllJeConfigByTransConfigId($transConfigId)
    {
        return FinancialJeConfig::getAllJeConfigByTransConfigId($transConfigId);         
    }

    public function saveJournalItems($arrData){
        return FinancialJournalItems::saveJournalItems($arrData);
    }    

    public function saveJournalEntries($arrData){
        return FinancialJournalEntries::saveJournalEntries($arrData);
    }   

    public function getTransactions()
    {
        $this->result = FinancialJournalItems::getTransactions();
        return $this->result;
    }

    public function getTallyTxns(array $where = array()) {
        $this->result = FinancialJournalItems::getTallyTxns($where);
        return $this->result;
    }
    
    public function getAllBatches(array $where = array()) {
        $this->result = FinancialJournalItems::getAllBatches($where);
        return $this->result;
    }

    public function getLatestBatch(array $where = array()) {
        $this->result = FinancialJournalItems::getLatestBatch($where);
        return $this->result;
    }
    
    public function saveBankWcFacility($arrData) {
        return BankWorkCapitalFacility::saveBankWcFacility($arrData);
    }
    
    public function updateBankWcFacility($bankDetailId, $dataArr) {
        return BankWorkCapitalFacility::updateBankWcFacility((int) $bankDetailId, $dataArr);
    }
    
    public function getBankWcFacility($bankDetailId) {
        return BankWorkCapitalFacility::getBankWcFacility((int) $bankDetailId);
    }
    
    public function saveBankTermBusiLoan($arrData) {
        return BankTermBusiLoan::saveBankTermBusiLoan($arrData);
    }
    
    public function updateBankTermBusiLoan($bankDetailId, $dataArr) {
        return BankTermBusiLoan::updateBankTermBusiLoan((int) $bankDetailId, $dataArr);
    }
    
    public function getBankTermBusiLoan($bankDetailId) {
        return BankTermBusiLoan::getBankTermBusiLoan((int) $bankDetailId);
    }
    
    public function saveBankAnalysis($arrData) {
        return BankAnalysis::saveBankAnalysis($arrData);
    }
    
    public function updateBankAnalysis($bankDetailId, $dataArr) {
        return BankAnalysis::updateBankAnalysis((int) $bankDetailId, $dataArr);
    }
    
    public function getBankAnalysis($bankDetailId) {
        return BankAnalysis::getBankAnalysis((int) $bankDetailId);
    }
    
    public function getDebtPosition($appID) {
        return FinanceModel::getDebtPosition((int) $appID);
    }

    public function getPaymentFactTxns(array $where = array()) {
        $this->result = FinancialJournalItems::getPaymentFactTxns($where);
        return $this->result;
    }
}