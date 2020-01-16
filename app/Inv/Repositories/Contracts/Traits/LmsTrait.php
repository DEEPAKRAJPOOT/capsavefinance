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
     * Calculate Interest
     * 
     * @param integer $disbursalId
     * @return mixed
     */
    protected function calAccrualInterest($disbursalId, $tenorDays=null)
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
        
        $interest = $this->calInterest($balancePrincipalAmt, $interestRate, $tenorDays);
                
        return $interest;
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
        $userId    = $disbursalData->userId;
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
        $fundedAmount = $invoice['invoice_approve_amount'] - (($invoice['invoice_approve_amount']*$invoice['program_offer']['margin'])/100);
        $interest = (($fundedAmount*$invoice['program_offer']['interest_rate']*$invoice['program_offer']['tenor'])/360);
        $disburseAmount = round($fundedAmount - $interest);


        $disbursalData['user_id'] = $invoice['supplier_id'] ?? null;
        $disbursalData['app_id'] = $invoice['app_id'] ?? null;
        $disbursalData['invoice_id'] = $invoice['invoice_id'] ?? null;
        $disbursalData['prgm_offer_id'] = $invoice['prgm_offer_id'] ?? null;
        $disbursalData['bank_account_id'] = $invoice['supplier_bank_detail']['bank_id'] ?? null;
        $disbursalData['disburse_date'] = \Carbon\Carbon::now()->format('Y-m-d h:i:s');
        $disbursalData['bank_name'] = $invoice['supplier_bank_detail']['bank']['bank_name'] ?? null;
        $disbursalData['ifsc_code'] = $invoice['supplier_bank_detail']['ifsc_code'] ?? null;
        $disbursalData['acc_no'] = $invoice['supplier_bank_detail']['acc_no'] ?? null;            
        $disbursalData['virtual_acc_id'] = $invoice['lms_user']['virtual_acc_id'] ?? null;
        $disbursalData['customer_id'] = $invoice['lms_user']['customer_id'] ?? null;
        $disbursalData['principal_amount'] = $fundedAmount ?? null;
        $disbursalData['inv_due_date'] = $invoice['inv_due_date'] ?? null;
        $disbursalData['tenor_days'] =  $invoice['program_offer']['tenor'] ?? null;
        $disbursalData['interest_rate'] = $invoice['program_offer']['interest_rate'] ?? null;
        $disbursalData['total_interest'] = $interest;
        $disbursalData['margin'] =$invoice['program_offer']['margin'] ?? null;
        $disbursalData['disburse_amount'] = $disburseAmount ?? null;
        $disbursalData['total_repaid_amt'] = 0;
        $disbursalData['status_id'] = ($disburseType == 2) ? 12 : 10;
        $disbursalData['settlement_date'] = null;
        $disbursalData['accured_interest'] = null;
        $disbursalData['interest_refund'] = null;
        $disbursalData['funded_date'] = ($disburseType == 2) ? \Carbon\Carbon::now()->format('Y-m-d h:i:s') : null;
        $disbursalData['int_accrual_start_dt'] = ($disburseType == 2) ? \Carbon\Carbon::now()->format('Y-m-d') : null;
            
        return $disbursalData;
    }    
}
