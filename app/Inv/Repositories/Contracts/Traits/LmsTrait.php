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
        $totalRepaidAmount  = $disbursalData->total_repaid_amt;
        
        //$repaymentWhereCond = [];
        //$repaymentWhereCond['user_id']  = $userId;
        //$repaymentWhereCond['invoice_id']  = $invoiceId;
        //$repaymentWhereCond['trans_type']  = 17;   //Repayment Trans Type
        //$repaymentData = $this->lmsRepo->getRepayments($repaymentWhereCond);
        
        $balancePrincipalAmt = $principalAmount - $totalRepaidAmount;
        $interestRate     = round($disbursalData->interest_rate / 100, 2);
        $tenorDays        = $tenorDays ? : $disbursalData->tenor_days;
        
        $interest = $balancePrincipalAmt * $tenorDays * ($interestRate / 365) ;
                
        return $interest;
    }
    
   
}
