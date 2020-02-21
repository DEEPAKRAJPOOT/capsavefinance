<?php
namespace App\Inv\Repositories\Contracts\Traits;

use Auth;
use App\Inv\Repositories\Models\Lms\InvoiceRepaymentTrail;
use App\Inv\Repositories\Models\Lms\Disbursal;
use App\Inv\Repositories\Models\Lms\Transactions;
use App\Inv\Repositories\Models\Lms\InterestAccrual;

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
    
    protected function calculateTenorDays($invoice = [])
    {
        $now = strtotime((isset($invoice['invoice_due_date'])) ? $invoice['invoice_due_date'] : ''); // or your date as well
        $your_date = strtotime((isset($invoice['invoice_date'])) ? $invoice['invoice_date'] : '');
        $datediff = abs($now - $your_date);

        $tenor = round($datediff / (60 * 60 * 24));
        return $tenor;        
    } 

    protected function calculateFundedAmount($invoice = [], $margin)
    {
        return $invoice['invoice_approve_amount'] - (($invoice['invoice_approve_amount']*$margin)/100);
    }   
        

    /**
     * Process Interest Accrual
     *      
     * @return mixed
     */
    protected function calAccrualInterest()
    {        
        $int_type_config = 1; //1=>Daily Interest Accrual, 2=>Monthly Interest Accrual, 3=>Rear Ended
        //$currentDate = \Carbon\Carbon::now()->format('Y-m-d');
        $currentDate = date('Y-m-d');
                
        $disbursalWhereCond = [];
        $disbursalWhereCond['status_id']  = [12];
        //$disbursalWhereCond['int_accrual_start_dt']  = $currentDate;
        $disbursalData = $this->lmsRepo->getDisbursalRequests($disbursalWhereCond);
        $returnData = [];
        $allTrans = [];
        foreach($disbursalData as $disburse) {
            $disbursalId = $disburse->disbursal_id;
            $appId  = $disburse->app_id;            
            $userId  = $disburse->user_id;
            $virAccId  = $disburse->virtual_acc_id;
            $invoiceId  = $disburse->invoice_id;
            $principalAmount  = $disburse->principal_amount;
            $totalRepaidAmount  = $disburse->total_repaid_amt;
            $invoiceDueDate  = $disburse->inv_due_date;
            $intAccrualStartDt = $disburse->int_accrual_start_dt;
            $intAccrualDt = $intAccrualStartDt;
            //$calAccruedInterest = $disburse->accured_interest ? $disburse->accured_interest : 0;
            $calAccruedInterest = 0;
            
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
                    $interestRate = $overdueIntRate;
                    $reculateInterest = true;
                }
                $calInterestRate  = round($interestRate / 100, 2);
                $tenorDays        = 1;
                
                $usedPrincipalAmt = $int_type_config == 2 ? $balancePrincipalAmt + $calAccruedInterest : $balancePrincipalAmt;
                $interest = $this->calInterest($usedPrincipalAmt, $calInterestRate, $tenorDays);

                $intAccrualData = [];
                $intAccrualData['disbursal_id'] = $disbursalId;
                $intAccrualData['interest_date'] = $intAccrualDt;
                $intAccrualData['principal_amount'] = $balancePrincipalAmt;
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
                
                if ($int_type_config == 2 && date("Y-m-t", strtotime($intAccrualDt)) == $intAccrualDt) {
                    $monthlyIntCond = [];
                    $monthlyIntCond['disbursal_id'] = $disbursalId;
                    $monthlyIntCond['interest_date_lte'] = date("Y-m-t", strtotime($intAccrualDt));
                    $monthlyIntCond['interest_date_gte'] = date('Y-m-01', strtotime($intAccrualDt));
                    $accuredInterest = $this->lmsRepo->sumAccruedInterest($monthlyIntCond);
                    $calAccruedInterest += $accuredInterest;
                    $saveDisbursalData = [];
                    $saveDisbursalData['accured_interest'] = $calAccruedInterest;
                    $this->lmsRepo->saveDisbursalRequest($saveDisbursalData, ['disbursal_id' => $disbursalId]);                    
                }
                                                                   
                $intAccrualDt = date ("Y-m-d", strtotime("+1 day", strtotime($intAccrualDt)));
            }
            
            $accuredInterest = $this->lmsRepo->sumAccruedInterest(['disbursal_id' => $disbursalId]);
            if ($int_type_config == 1) {
                $saveDisbursalData = [];
                $saveDisbursalData['accured_interest'] = $accuredInterest;
                $this->lmsRepo->saveDisbursalRequest($saveDisbursalData, ['disbursal_id' => $disbursalId]);
            }
            $returnData[$disbursalId] = $accuredInterest;
            $overDueInterest = $this->getOverDueInterest($invoiceId);
            if ($overDueInterest) {
                $transactions = [];
                $transactions['user_id'] = $userId;
                $transactions['virtual_acc_id'] = $virAccId;
                $transactions['trans_date'] = date("Y-m-d");
                $transactions['virtual_acc_id'] = $virAccId;     
                $transactions['amount'] = 0;
                $transactions['trans_type'] = 19;
                $transactions['entry_type'] = 0;   //0 - Debit and 1 - Credit    
                $transactions['mode_of_pay'] = 1;  //1 - Online
                $transactions['created_at'] = \Carbon\Carbon::now()->format('Y-m-d h:i:s');
                $transactions['created_by'] = \Auth::user() ? \Auth::user()->user_id : 1; 
                $allTrans[$userId] = $transactions;
                $allTrans[$userId]['amount'] = isset($allTrans[$userId]) ? $allTrans[$userId]['amount'] + $overDueInterest : $overDueInterest;
            }
        }
        //$insertTrans = [];
        foreach($allTrans as $key => $trans) {
            //$insertTrans[] = $trans;
            $transData = $this->lmsRepo->getTransactions(['user_id' => $trans['user_id'], 'trans_type' => 19]);
            if (!isset($transData[0])) {
                $this->lmsRepo->saveTransaction($trans);
            } else {
                $data = [
                    'trans_date' => date("Y-m-d"),
                    'amount' => $trans['amount']
                ];
                $this->lmsRepo->updateTransaction(['user_id' => $trans['user_id'], 'trans_type' => 19], $data);
            }
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

    protected function getTransactions($userId, &$trans, &$offset, $pipedAmt, $settlementAmt, &$lastTransId){
        
        $transactions = Transactions::where(['user_id'=>$userId,'trans_type'=>17 ])->whereIn('is_settled',[0,1]);

        if($lastTransId && (($pipedAmt>=$settlementAmt) || ($transactions->count()-1 < $offset)) ){
            $transaction =  Transactions::find($lastTransId);
            $trans[$transaction->trans_id] =[
                'trans_id' => $transaction->trans_id,
                'trans_date' => $transaction->trans_date,
                'amount' => $transaction->amount,
                'pipedAmt' => $pipedAmt,
                'settledAmount' => ($settlementAmt>=$pipedAmt)?$pipedAmt:$settlementAmt
            ];
        }

        if($pipedAmt>=$settlementAmt){
            return $pipedAmt;
        }
        
        if($transactions->count()-1 < $offset){
            return $pipedAmt;
        }

        $transaction = $transactions->orderBy('trans_date','asc')->offset($offset)->limit(1)->first();
        
        $pipedAmt = ($lastTransId != $transaction->trans_id)?$pipedAmt+$transaction->amount:$pipedAmt;
        $lastTransId = $transaction->trans_id;
        $trans[$transaction->trans_id] =[
            'trans_id' => $transaction->trans_id,
            'trans_date' => $transaction->trans_date,
            'amount' => $transaction->amount,
            'pipedAmt' => $pipedAmt,
            'settledAmount' => ($settlementAmt>=$pipedAmt)?$pipedAmt:$settlementAmt
        ];
        
        if($pipedAmt >= $settlementAmt){
            return  $pipedAmt;
        }else{
            $offset++;
            return $this->getTransactions($userId, $trans, $offset,$pipedAmt, $settlementAmt, $lastTransId);
        }
        
    }

    protected function paySettlement($userId){
        $currentDate = date('Y-m-d');
        
        if($userId){
            $settledInvoice = [];
            
            $userInvoiceDetails = Disbursal::where(['user_id'=>$userId])
                ->whereIn('status_id',[13,12])
                ->orderBy('inv_due_date','asc')
                ->get();

            $noOfTransactions = Transactions::where(['user_id'=>$userId,'trans_type'=>17])->whereIn('is_settled',[0,1])->count();
            
            if($noOfTransactions==0){
                return [];
            }
 
            $invoice = array();
            foreach ($userInvoiceDetails as $key => $UIDetail) {
                $invoice[] = [
                    'disbursal_id' => $UIDetail->disbursal_id,
                    'user_id' => $UIDetail->user_id,
                    'invoice_id' => $UIDetail->invoice_id,
                    'principal_amount' => $UIDetail->principal_amount,
                    'disburse_amount' => $UIDetail->disburse_amount,
                    'total_interest' => $UIDetail->total_interest,
                    'total_repaid_amt' => $UIDetail->total_repaid_amt,
                    'interest_refund' => $UIDetail->interest_refund,
                    'accrued_interest' => $UIDetail->interests->sum('accrued_interest'),
                    'disburse_date' => \Carbon\Carbon::createFromFormat('Y-m-d h:i:s', $UIDetail->disburse_date)->format('Y-m-d'),
                    'int_accrual_type'=>$UIDetail->offer->int_accrual_type,
                    'inv_due_date' => $UIDetail->inv_due_date,
                ]; 
            }

            $invoiceLoop = 0;
            $totalRepaidAmount = 0;
            $lastTransId=NULL;
            $totalInterestRrefund = 0;
            foreach ($invoice as $key => $inv) {
            
                $trans = array();
                $overDueInterest= 0; 
                $is_inv_settled = 0;
                $is_settled = 0;
                // switch ($inv['int_accrual_type']) {
                //     case '1': //1=> upfrond
                    
                        // Interest Calculation Init 

                        if($inv['accrued_interest']<=$inv['total_interest']){
                            $interestRrefund = $inv['total_interest']-$inv['accrued_interest'];
                        }else{
                            $interestRrefund = 0;
                            $overDueInterest = ($inv['accrued_interest']-$inv['total_interest']);
                        }

                        $totalRepaidAmount = $this->getTransactions($userId, $trans, $invoiceLoop, $totalRepaidAmount,$overDueInterest,$lastTransId);
                        
                        // Over Due Interest Settlement Step 1
                        
                        if($totalRepaidAmount >= $overDueInterest){
                            $invoice[$key]['disbursal']['total_repaid_amt'] = $overDueInterest;
                        }else{
                            $invoice[$key]['disbursal']['total_repaid_amt'] = $totalRepaidAmount;
                        }
                        
                        $invoice[$key]['disbursal']['status_id'] = 13;
                        $is_inv_settled = 1;

                        $totalRepaidAmount -= $invoice[$key]['disbursal']['total_repaid_amt'];
                        

                        // Principal Settlement Step 2

                        $balancePrincipalAmt = $inv['principal_amount'] - ($inv['total_repaid_amt']+$inv['interest_refund']);
                        
                        $totalRepaidAmount = $this->getTransactions($userId, $trans, $invoiceLoop, $totalRepaidAmount, $balancePrincipalAmt, $lastTransId);
                        
                        if($totalRepaidAmount+$interestRrefund >= $balancePrincipalAmt){
                            $invoice[$key]['disbursal']['total_repaid_amt'] += $inv['principal_amount'];
                            $invoice[$key]['disbursal']['status_id'] = 15;
                            $is_inv_settled = 2;
                        }else{
                            $invoice[$key]['disbursal']['total_repaid_amt'] += $totalRepaidAmount;
                            $invoice[$key]['disbursal']['status_id'] = 13;
                            $is_inv_settled = 1;
                        }

                        $totalRepaidAmount -= $invoice[$key]['disbursal']['total_repaid_amt'];


                        // Interest Refund Step 3

                        if($is_inv_settled == 2){
                            $totalRepaidAmount += $interestRrefund;
                            $invoice[$key]['disbursal']['interest_refund'] = $interestRrefund;
                        }else{
                            $invoice[$key]['disbursal']['interest_refund'] = 0;
                        }

                        $invoice[$key]['disbursal']['settlement_amount'] = $invoice[$key]['disbursal']['total_repaid_amt']-$invoice[$key]['disbursal']['interest_refund'];

                        $invoice[$key]['invoiceRepayment'] = [
                            'user_id'=> $inv['user_id'],
                            'invoice_id'=> $inv['invoice_id'],
                            'repaid_amount'=> round($invoice[$key]['disbursal']['total_repaid_amt'],2),
                            'repaid_date'=> \Carbon\Carbon::now()->format('Y-m-d h:i:s'),
                            'trans_type'=> ($is_inv_settled == 1)?13:17,
                        ];

                        $invoice[$key]['trans']= $trans;

                        $lastTransaction = end($invoice[$key]['trans']);

                        $invoice[$key]['disbursal']['settlement_date'] = $lastTransaction['trans_date'];

                        $is_settled = ($lastTransaction['pipedAmt']>=$balancePrincipalAmt)?1:2;

                //         break;
                //     case '2': //2 => monthly
                //         # code...
                //         break;
                //     case '3': //3 => rear end
                //         # code...
                //         break;
                // }

                
                    // Transaction Settlement Setp 4
                    foreach ($invoice[$key]['trans'] as $transkey => $transVal) {
                        
                        if($is_inv_settled == 1 && count($invoice[$key]['trans'])-1==$transkey){
                            $this->lmsRepo->saveTransaction(['is_settled'=> 1],['trans_id'=>$transVal['trans_id']]);
                        }else{
                            $invoiceLoop--;
                            $this->lmsRepo->saveTransaction(['is_settled'=> 2],['trans_id'=>$transVal['trans_id']]);
                        }
                    }

                $totalInterestRrefund += $invoice[$key]['disbursal']['interest_refund'];
                $this->lmsRepo->saveRepayment($invoice[$key]['invoiceRepayment']);
                $this->lmsRepo->saveDisbursalRequest($invoice[$key]['disbursal'], ['disbursal_id' => $inv['disbursal_id']]);
             
                if($is_inv_settled==1 && $totalRepaidAmount == 0) break;
            }
            
             $unUsedTrnsactions = Transactions::where(['user_id'=>$userId,'trans_type'=>17])
                                    ->whereIn('is_settled',[0])
                                    ->orderBy('trans_date','asc')
                                    ->offset($invoiceLoop+1)
                                    ->limit($noOfTransactions)
                                    ->pluck('amount','trans_id');
            
            foreach ($unUsedTrnsactions as $trans_id => $amt) {
                $totalRepaidAmount += $amt;
                $this->lmsRepo->saveTransaction(['is_settled'=> '2'],['trans_id'=>$trans_id]);
            }

            if(!empty($invoice)){
                $this->lmsRepo->saveDisbursalRequest(['surplus_amount'=>($totalRepaidAmount>0)?$totalRepaidAmount:NULL], ['disbursal_id' => $invoice[$key]['disbursal_id']]);
            } 

            if($totalRepaidAmount>0 && $totalInterestRrefund>0){ 
                $intrstTrnsData = $this->createTransactionData($userId, ['amount' => $totalInterestRrefund], null, 9, 1);
                $createTransaction = $this->lmsRepo->saveTransaction($intrstTrnsData);
            }

            //dd($invoice, 'Surplus='.$totalRepaidAmount);
           
        }
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
        //$interest = (($fundedAmount * ($invoice['program_offer']['interest_rate']/100) * $tenor)/360);
        $interest = $this->calInterest($fundedAmount, $invoice['program_offer']['interest_rate']/100, $tenor);
        $disburseAmount = round($fundedAmount - $interest, 2);
        // dd($invoice);
        $disbursalData['user_id'] = $invoice['supplier_id'] ?? null;
        $disbursalData['app_id'] = $invoice['app_id'] ?? null;
        $disbursalData['invoice_id'] = $invoice['invoice_id'] ?? null;
        $disbursalData['prgm_offer_id'] = $invoice['prgm_offer_id'] ?? null;
        $disbursalData['bank_account_id'] = ($invoice['supplier']['is_buyer'] == 2) ? $invoice['supplier']['anchor_bank_details']['bank_account_id'] : $invoice['supplier_bank_detail']['bank_account_id'];
        $disbursalData['disburse_date'] = \Carbon\Carbon::now()->format('Y-m-d h:i:s');
        $disbursalData['bank_name'] = ($invoice['supplier']['is_buyer'] == 2) ? $invoice['supplier']['anchor_bank_details']['bank']['bank_name'] : $invoice['supplier_bank_detail']['bank']['bank_name'] ;
        $disbursalData['ifsc_code'] = ($invoice['supplier']['is_buyer'] == 2) ? $invoice['supplier']['anchor_bank_details']['ifsc_code'] : $invoice['supplier_bank_detail']['ifsc_code'];
        $disbursalData['acc_no'] = ($invoice['supplier']['is_buyer'] == 2) ? $invoice['supplier']['anchor_bank_details']['acc_no'] : $invoice['supplier_bank_detail']['acc_no'];            
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
        $disbursalData['processing_fee'] = $invoice['program_offer']['processing_fee'] ?? null;
        $disbursalData['grace_period'] = $invoice['program_offer']['grace_period'] ?? null;
        $disbursalData['overdue_interest_rate'] = $invoice['program_offer']['overdue_interest_rate'] ?? null;
        $disbursalData['repayment_amount'] = null;
        $disbursalData['penalty_amount'] = 0;
        
        return $disbursalData;
    }

    /**
     * Prepare Disbursal Data
     * 
     * @param array $data
     * @return mixed
     */
    protected function createTransactionData($userId = null, $data = 0, $transId = null, $transType = 0, $entryType = 0)
    {
        /**
        * disburseType = 1 for online and 2 for manually
        */
        $transactionData = [];
        // dd($data);
        $transactionData['gl_flag'] = 1;
        $transactionData['soa_flag'] = 1;
        $transactionData['user_id'] = $userId ?? null;
        $transactionData['virtual_acc_id'] = $userId ? $this->appRepo->getVirtualAccIdByUserId($userId) : null;
        $transactionData['trans_date'] = \Carbon\Carbon::now()->format('Y-m-d h:i:s');
        $transactionData['trans_type'] = $transType ?? 0;
        $transactionData['pay_from'] = ($transType == 16) ? 3 : $this->appRepo->getUserTypeByUserId($userId);
        $transactionData['amount'] = $data['amount'] ?? 0;
        $transactionData['gst'] = $data['gst'] ?? 0;
        $transactionData['cgst'] = 0;            
        $transactionData['sgst'] = 0;
        $transactionData['igst'] = 0;
        $transactionData['entry_type'] =  $entryType ?? 0;
        $transactionData['tds_per'] = null;
        $transactionData['mode_of_pay'] =  1;
        $transactionData['comment'] = null;
        $transactionData['utr_no'] =null;
        $transactionData['cheque_no'] = null;
        $transactionData['unr_no'] = null;
        $transactionData['txn_id'] = $transId;

        $transactionData['created_by'] = Auth::user()->user_id ?? null;
        
        return $transactionData;
    }    
    /**
     * Get Overdue Interest
     * 
     * @param integer $invoice_id
     * @return float
     */
    protected function getOverDueInterest($invoice_id)
    {   
        $disbData = $this->lmsRepo->getDisbursalRequests(['invoice_id' => $invoice_id]);        
        if (!isset($disbData[0])) return null;
        
        $disbursalId = $disbData[0]->disbursal_id;
        $invDueDate  = $disbData[0]->inv_due_date;
        
        $monthlyIntCond = [];
        $monthlyIntCond['disbursal_id'] = $disbursalId;
        $monthlyIntCond['interest_date_gte'] = $invDueDate;   //date('Y-m-d', strtotime($invDueDate));
        $accuredInterest = $this->lmsRepo->sumAccruedInterest($monthlyIntCond);
        return $accuredInterest;
    }
}
