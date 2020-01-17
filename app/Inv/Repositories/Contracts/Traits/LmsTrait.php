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
        $interest = $principalAmt * $tenorDays * ($interestRate / 360) ;                
        return $interest;        
    }   
    

    /**
     * Process Interest Accrual
     *      
     * @return mixed
     */
    protected function calAccrualInterest()
    {        
        //$currentDate = \Carbon\Carbon::now()->format('Y-m-d');
        $currentDate = date('Y-m-d');
                
        $disbursalWhereCond = [];
        $disbursalWhereCond['status_id']  = [12];
        //$disbursalWhereCond['int_accrual_start_dt']  = $currentDate;
        $disbursalData = $this->lmsRepo->getDisbursalRequests($disbursalWhereCond);
        $returnData = [];
        foreach($disbursalData as $disburse) {
            $disbursalId = $disburse->disbursal_id;
            $appId  = $disburse->app_id;
            $invoiceId  = $disburse->invoice_id;
            $principalAmount  = $disburse->principal_amount;
            $totalRepaidAmount  = $disburse->total_repaid_amt;
            $invoiceDueDate  = $disburse->inv_due_date;
            $intAccrualStartDt = $disburse->int_accrual_start_dt;
            $intAccrualDt = $intAccrualStartDt;
            
            $balancePrincipalAmt = $principalAmount - $totalRepaidAmount;
                        
            $whereProgramOffer = [];
            $whereProgramOffer['app_id'] = $appId;
            $whereProgramOffer['invoice_id'] = $invoiceId;                
            $whereProgramOffer['disbursal_id'] = $disbursalId;
            $prgmOffer = $this->lmsRepo->getProgramOffer($whereProgramOffer);
            $overdueIntRate = $prgmOffer->overdue_interest_rate;
            $gracePeriod = $prgmOffer->grace_period ? $prgmOffer->grace_period : 0;
            
            $gracePeriodDate = $this->addDays($invoiceDueDate, $gracePeriod);
            $overDueInterestDate = $this->addDays($invoiceDueDate, 1);
            $reculateInterest = false;
            while (strtotime($intAccrualDt) <= strtotime($currentDate)) {
                $interestRate = $disburse->interest_rate;
                
                if ($intAccrualDt > $gracePeriodDate && $balancePrincipalAmt > 0) {
                    $interestRate = $interestRate + $overdueIntRate;
                    $reculateInterest = true;
                }
                $calInterestRate  = round($interestRate / 100, 2);
                $tenorDays        = 1;

                $interest = $this->calInterest($balancePrincipalAmt, $calInterestRate, $tenorDays);

                $intAccrualData = [];
                $intAccrualData['disbursal_id'] = $disbursalId;
                $intAccrualData['interest_date'] = $intAccrualDt;
                $intAccrualData['principal_amount'] = $disburse->principal_amount;
                $intAccrualData['accrued_interest'] = round($interest, 2);
                $intAccrualData['interest_rate'] = $disburse->interest_rate;
                
                if ($reculateInterest) {
                    $reWhereCond = [];
                    $reWhereCond['disbursal_id'] = $disbursalId;
                    $reWhereCond['interest_date_gte'] = $overDueInterestDate;
                    $accruedInterestData = $this->lmsRepo->getAccruedInterestData($reWhereCond);
                    
                    foreach($accruedInterestData as $accruedInt) {
                        $whereCond = [];
                        $whereCond['interest_accrual_id'] = $accruedInt->interest_accrual_id;  
                        $updateIntAccrualData = [];
                        $updateIntAccrualData['accrued_interest'] = $intAccrualData['accrued_interest'];                        
                        $updateIntAccrualData['overdue_interest_rate'] = $overdueIntRate;
                        $this->lmsRepo->saveInterestAccrual($updateIntAccrualData, $whereCond);
                    }
                }

                $exWhereCond = [];
                $exWhereCond['disbursal_id'] = $disbursalId;
                $exWhereCond['interest_date_eq'] = $intAccrualDt;                
                $exInterestData = $this->lmsRepo->getAccruedInterestData($exWhereCond);
                if (!isset($exInterestData[0])) {
                    if ($reculateInterest) {
                        $intAccrualData['overdue_interest_rate'] = $overdueIntRate;
                    }
                    //echo "\n"; print_r($intAccrualData);
                    $this->lmsRepo->saveInterestAccrual($intAccrualData);
                } else {
                    //$rewhereCond = [];
                    //$reIntAccrualData = [];
                    //$rewhereCond['interest_accrual_id'] = $exInterestData[0]->interest_accrual_id;
                    //$reIntAccrualData['interest_rate'] = $disburse->interest_rate;
                    //$this->lmsRepo->saveInterestAccrual($reIntAccrualData, $rewhereCond);
                }
                                                                   
                $intAccrualDt = date ("Y-m-d", strtotime("+1 day", strtotime($intAccrualDt)));
            }
                                
            $accuredInterest = $this->lmsRepo->sumAccruedInterest($disbursalId);
            $saveDisbursalData['accured_interest'] = $accuredInterest;
            $this->lmsRepo->saveDisbursalRequest($saveDisbursalData, ['disbursal_id' => $disbursalId]);
            
            $returnData[$disbursalId] = $accuredInterest;
        }
        return $returnData;
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
        $disbursalWhereCond['status']  = [12,13];   //12 => Disbursed, 13 => Repaid
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
        $status = $balRepaymentAmt == 0 ? 15 : 13;   //13 => Repaid Status, 15 => Payment Settled
        $saveDisbursalData['status'] = $status;
        $this->lmsRepo->saveDisbursalRequest($saveDisbursalData, ['disbursal_id' => $disbursalId]);
        
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

    /**
     * Prepare Disbursal Data
     * 
     * @param array $data
     * @return mixed
     */
    protected function createInvoiceDisbursalData($invoice, $disburseType = 2)
    {
        /**
        * disburseType = 1 for online and 2 for manually
        */
        $disbursalData = [];
        $now = strtotime($invoice['invoice_due_date']); // or your date as well
        $your_date = strtotime($invoice['invoice_date']);
        $datediff = abs($now - $your_date);

        $tenor = round($datediff / (60 * 60 * 24));
        $fundedAmount = $invoice['invoice_approve_amount'] - (($invoice['invoice_approve_amount']*$invoice['program_offer']['margin'])/100);
        $interest = (($fundedAmount*$invoice['program_offer']['interest_rate']*$tenor)/360);
        $disburseAmount = round($fundedAmount - $interest);
        // dd($disburseAmount);
        $disbursalData['user_id'] = $invoice['supplier_id'] ?? null;
        $disbursalData['app_id'] = $invoice['app_id'] ?? null;
        $disbursalData['invoice_id'] = $invoice['invoice_id'] ?? null;
        $disbursalData['prgm_offer_id'] = $invoice['prgm_offer_id'] ?? null;
        $disbursalData['bank_account_id'] = $invoice['supplier_bank_detail']['bank_account_id'] ?? 0;
        $disbursalData['disburse_date'] = \Carbon\Carbon::now()->format('Y-m-d h:i:s');
        $disbursalData['bank_name'] = $invoice['supplier_bank_detail']['bank']['bank_name'] ?? null;
        $disbursalData['ifsc_code'] = $invoice['supplier_bank_detail']['ifsc_code'] ?? null;
        $disbursalData['acc_no'] = $invoice['supplier_bank_detail']['acc_no'] ?? null;            
        $disbursalData['virtual_acc_id'] = $invoice['lms_user']['virtual_acc_id'] ?? null;
        $disbursalData['customer_id'] = $invoice['lms_user']['customer_id'] ?? null;
        $disbursalData['principal_amount'] = $fundedAmount ?? null;
        $disbursalData['inv_due_date'] = $invoice['invoice_due_date'] ?? null;
        $disbursalData['tenor_days'] =  $invoice['program_offer']['tenor'] ?? null;
        $disbursalData['interest_rate'] = $invoice['program_offer']['interest_rate'] ?? null;
        $disbursalData['total_interest'] = $interest;
        $disbursalData['margin'] =$invoice['program_offer']['margin'] ?? null;
        $disbursalData['disburse_amount'] = $disburseAmount ?? null;
        $disbursalData['total_repaid_amt'] = 0;
        $disbursalData['status_id'] = ($disburseType == 2) ? 12 : 10;
        $disbursalData['disburse_type'] = $disburseType;
        $disbursalData['settlement_date'] = null;
        $disbursalData['accured_interest'] = null;
        $disbursalData['interest_refund'] = null;
        $disbursalData['funded_date'] = ($disburseType == 2) ? \Carbon\Carbon::now()->format('Y-m-d h:i:s') : null;
        $disbursalData['int_accrual_start_dt'] = ($disburseType == 2) ? \Carbon\Carbon::now()->format('Y-m-d') : null;
            
        return $disbursalData;
    }    
}
