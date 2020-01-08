<?php
namespace App\Inv\Repositories\Contracts\Traits;

trait LmsTrait
{
    /**
     * Calculate Interest
     * 
     * @param integer $disbursalId
     * @return mixed
     */
    protected function calInterest($disbursalId, $tenorDays=null)
    {
        $disbursalWhereCond = [];
        $disbursalWhereCond['disbursal_id']  = $disbursalId;
        $disbursalData = $this->lmsRepo->getDisbursalRequests($disbursalWhereCond);
        if (!isset($disbursalData[0])) return 0;
        
        $disbursalData = $disbursalData[0];
        $userId    = $disbursalData->userId;
        $invoiceId = $disbursalData->invoice_id;
        $principalAmount  = $disbursalData->principal_amount;
        
        $repaymentWhereCond = [];
        $repaymentWhereCond['user_id']  = $userId;
        $repaymentWhereCond['invoice_id']  = $invoiceId;
        $repaymentData = $this->lmsRepo->getRepayments($repaymentWhereCond);
        
        $interestRate     = round($disbursalData->interest_rate / 100, 2);
        $tenorDays        = $tenorDays ? : $disbursalData->tenor_days;
        
        $interest = $principalAmount * $tenorDays * ($interestRate / 365) ;
                
        return $interest;
    }
    
    
}
