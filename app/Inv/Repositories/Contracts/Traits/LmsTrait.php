<?php
namespace App\Inv\Repositories\Contracts\Traits;

trait LmsTrait
{
    /**
     * Calculate Interest
     * 
     * @param float $principalAmt
     * @param float $interestRate
     * @param integer $tenorDays
     * 
     * @return mixed
     */
    protected function calInterest($principalAmt, $interestRate, $tenorDays)
    {
        $interest = $principalAmt * $tenorDays * ($interestRate / 365) ;                
        return $interest;        
    }   
    

    /**
     * Process Interest Accrual
     *      
     * @return mixed
     */
    protected function calAccrualInterest()
    {
        $currentDate = \Carbon\Carbon::now()->format('Y-m-d');
        
        $disbursalWhereCond = [];
        $disbursalWhereCond['status']  = [0,1,2];
        $disbursalWhereCond['int_accrual_start_dt']  = $currentDate;
        $disbursalData = $this->lmsRepo->getDisbursalRequests($disbursalWhereCond);

        foreach($disbursalData as $disburse) {
            $appId  = $disburse->app_id;
            $invoiceId  = $disburse->invoice_id;
            $principalAmount  = $disburse->principal_amount;
            $totalRepaidAmount  = $disburse->total_repaid_amt;
            $invoiceDueDate  = $disburse->invoice_due_date;
                        
            $balancePrincipalAmt = $principalAmount - $totalRepaidAmount;
            $interestRate = $disburse->interest_rate;
            $gracePeriodDate = $this->addDays($invoiceDueDate, $disburse->grace_period);
            $overDueInterestDate = $this->addDays($invoiceDueDate, 1);
            $reculateInterest = false;
            if ($currentDate > $gracePeriodDate && $balancePrincipalAmt > 0) {
                $whereProgramOffer = [];
                //$whereProgramOffer['app_id'] = $appId;
                //$whereProgramOffer['invoice_id'] = $invoiceId;                
                $whereProgramOffer['disbursal_id'] = $disburse->disbursal_id;
                $prgmOffer = $this->lmsRepo->getProgramOffer($whereProgramOffer);
                $overdueIntRate = $prgmOffer->overdue_interest_rate;
                $interestRate = $interestRate + $overdueIntRate;
                $reculateInterest = true;
            }
            $calInterestRate  = round($interestRate / 100, 2);
            $tenorDays        = 1;

            $interest = $this->calInterest($balancePrincipalAmt, $calInterestRate, $tenorDays);
        
            $intAccrualData = [];
            $intAccrualData['disbursal_id'] = $disburse->disbursal_id;
            $intAccrualData['interest_date'] = $currentDate;
            $intAccrualData['principal_amount'] = $disburse->principal_amount;
            $intAccrualData['accrued_interest'] = $interest;
            $intAccrualData['interest_rate'] = $interestRate;
            
            if ($reculateInterest) {
                $reWhereCond = [];
                $reWhereCond['interest_date'] = $overDueInterestDate;
                $accruedInterestData = $this->lmsRepo->getAccruedInterestData($reWhereCond);
                foreach($accruedInterestData as $accruedInt) {
                    $whereCond = [];
                    $whereCond['interest_accrual_id'] = $accruedInt->interest_accrual_id;
                    $intAccrualData['overdue_interest_rate'] = $overdueIntRate;
                    $this->lmsRepo->saveInterestAccrual($intAccrualData, $whereCond);
                }
            } else {
                $this->lmsRepo->saveInterestAccrual($intAccrualData);
            }            
        }        
    }
    
    /**
     * Add No Of Days to Date
     * 
     * @param Date $currentDate
     * @param integer $noOfDays
     * @return Date
     */
    protected function addDays($currentDate, $noOfDays)
    {
        $calDate = date('Y-m-d', strtotime($currentDate . "+ $noOfDays days"));
        return $calDate;
    }
    
    /**
     * Calculate Interest
     * 
     * @param integer $disbursalId
     * @return mixed
     */
    protected function calRepayment($virtualAccountId, $paidAmount)
    {
        $disbursalWhereCond = [];
        $disbursalWhereCond['status']  = [0,1,2];
        $disbursalWhereCond['virtual_acc_id']  = $virtualAccountId;
        $disbursalData = $this->lmsRepo->getDisbursalRequests($disbursalWhereCond);
        
        if (!isset($disbursalData[0])) return 0;

        $disbursalData = $disbursalData[0];
        $disbursalId    = $disbursalData->disbursal_id;
        $userId    = $disbursalData->user_id;
        $invoiceId = $disbursalData->invoice_id;
        $totalRepaidAmt = $disbursalData->total_repaid_amt;
        $principalAmount  = $disbursalData->principal_amount;
        
        $repaymentData = [];
        $repaymentData['user_id']    = $userId;
        $repaymentData['invoice_id'] = $invoiceId;
        $repaymentData['repaid_amount'] = $paidAmount;
        $repaymentData['repaid_date'] = \Carbon\Carbon::now()->format('Y-m-d h:i:s');
        $repaymentData['trans_type'] = 17;   //Repayment Trans Type
        
        $this->lmsRepo->saveRepayment($repaymentData);
                        
        $saveDisbursalData = [];
        $saveDisbursalData['total_repaid_amt'] = $totalRepaidAmt + $paidAmount;
        $balRepaymentAmt = $principalAmount - $saveDisbursalData['total_repaid_amt'];
        $status = $balRepaymentAmt == 0 ? 3 : 2;
        $saveDisbursalData['status'] = $status;
        $this->lmsRepo->saveDisbursalRequest($saveDisbursalData, ['disbursal_id' => $disbursalId]);
        
    }
    
    /**
     * Save Disbursal Data
     * 
     * @param array $data
     * @return mixed
     */
    protected function saveDisbursalData($data)
    {
        $saveDisbursalData = [];
        $saveDisbursalData['total_repaid_amt'] = $totalRepaidAmt + $paidAmount;
        $balRepaymentAmt = $principalAmount - $saveDisbursalData['total_repaid_amt'];
        $status = $balRepaymentAmt == 0 ? 3 : 2;
        $saveDisbursalData['status'] = $status;
        $this->lmsRepo->saveDisbursalRequest($saveDisbursalData);        
    }
    
    protected function calDisbursalAmount($principalAmount, $deductions)
    {
        $totalDeductions = 0;
        foreach($deductions as $deduction) {
            $totalDeductions += $deduction;
        }
        $balPrincipal = $principalAmount - $totalDeductions;
        return $balPrincipal;
    }

    /**
     * Prepare Disbursal Data
     * 
     * @param array $data
     * @return mixed
     */
    protected function prepareDisbursalData($requestData, $addlData)
    {
        $disbursalData = [];
        foreach($requestData['invoices'] as $invoice) {
            $data = [];
            $data['user_id'] = isset($requestData['user_id']) ? $requestData['user_id'] : null;
            $data['app_id'] = isset($requestData['app_id']) ? $requestData['app_id'] : null;
            $data['invoice_id'] = isset($requestData['invoice_id']) ? $requestData['invoice_id'] : null;
            $data['prgm_offer_id'] = isset($requestData['prgm_offer_id']) ? $requestData['prgm_offer_id'] : null;
            $data['bank_id'] = isset($requestData['bank_id']) ? $requestData['bank_id'] : null;
            $data['disburse_date'] = \Carbon\Carbon::now()->format('Y-m-d h:i:s');
            $data['bank_id'] = isset($requestData['bank_id']) ? $requestData['bank_id'] : null;
            $data['bank_name'] = isset($requestData['bank_name']) ? $requestData['bank_name'] : null;
            $data['ifsc_code'] = isset($requestData['ifsc_code']) ? $requestData['ifsc_code'] : null;
            $data['acc_no'] = isset($requestData['acc_no']) ? $requestData['acc_no'] : null;            
            $data['virtual_acc_id'] = isset($requestData['virtual_acc_id']) ? $requestData['virtual_acc_id'] : null;
            
            $data['customer_id'] = isset($requestData['customer_id']) ? $requestData['customer_id'] : null;
            $data['principal_amount'] = isset($requestData['principal_amount']) ? $requestData['principal_amount'] : null;
            
            $data['inv_due_date'] = isset($requestData['inv_due_date']) ? $requestData['inv_due_date'] : null;
            $data['tenor_days'] = isset($requestData['tenor_days']) ? $requestData['tenor_days'] : null;
            $data['interest_rate'] = isset($requestData['interest_rate']) ? $requestData['interest_rate'] : null;
            
            $data['total_interest'] = $this->calInterest($data['principal_amount'], $data['interest_rate'], $data['tenor_days']);
            
            $data['margin'] = isset($requestData['margin']) ? $requestData['margin'] : null;
            
            $deductions['margin']         = $data['margin'];
            $deductions['total_interest'] = $data['total_interest'];
            $data['disburse_amount'] = $this->calDisbursalAmount($data['principal_amount'], $deductions);
            
            $data['total_repaid_amt'] = 0;
            $data['status'] = 0;
            $data['settlement_date'] = null;
            $data['accured_interest'] = null;
            $data['interest_refund'] = null;
            
            $disbursalData[] = $data;
        }
        return $data;
    }
}
