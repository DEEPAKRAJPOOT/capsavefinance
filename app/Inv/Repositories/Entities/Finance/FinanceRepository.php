<?php

namespace App\Inv\Repositories\Entities\Finance;

use App\Inv\Repositories\Contracts\FinanceInterface;
use App\Inv\Repositories\Factory\Repositories\BaseRepositories;
use App\Inv\Repositories\Models\Financial\FinancialTransConfig;
use App\Inv\Repositories\Models\Financial\FinancialJournals;
use App\Inv\Repositories\Models\Financial\FinancialAccount;
use App\Inv\Repositories\Models\Financial\FinancialVariables;
use App\Inv\Repositories\Models\Financial\FinancialJeConfig;
use App\Inv\Repositories\Models\Financial\FinancialVariablesTransConfig;

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
}