<?php
namespace App\Inv\Repositories\Contracts\Traits;

use Auth;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use App\Inv\Repositories\Models\Lms\Disbursal;
use App\Inv\Repositories\Models\Lms\Transactions;
use App\Inv\Repositories\Models\Lms\InterestAccrual;
use App\Inv\Repositories\Models\Lms\InvoiceRepaymentTrail;
use App\Inv\Repositories\Models\Business;
use App\Inv\Repositories\Models\Application;
use App\Inv\Repositories\Models\BizPanGst;

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
        return $invoice['invoice_approve_amount'] - ((float)($invoice['invoice_approve_amount']*$margin)/100);
    }   

    protected function calAccrualInterest($transDate=null){
        
        $disbursalData = Disbursal::whereIn('status_id',[12,13])->get();
        $currentDate = date('Y-m-d');
        $interest = 0;
        $returnData = [];

        foreach ($disbursalData as $disburse) {
            $intAccrualDt = NULL;
            
            $whereProgramOffer = [];
            $whereProgramOffer['app_id'] = $disburse->app_id;
            $whereProgramOffer['invoice_id'] = $disburse->invoice_id;      
            $whereProgramOffer['disbursal_id'] = $disburse->disbursal_id;
            $prgmOffer = $this->lmsRepo->getProgramOffer($whereProgramOffer);
            
            $int_type_config = $prgmOffer->payment_frequency ? $prgmOffer->payment_frequency : 1;
            
            $gracePeriod = $prgmOffer->grace_period ? $prgmOffer->grace_period : 0;
            $interestRate = $disburse->interest_rate ?? $prgmOffer->interest_rate;
            $overdueIntRate = $disburse->overdue_interest_rate ?? $prgmOffer->overdue_interest_rate;
            
            $invoiceDueDate  = $disburse->inv_due_date;
            $intAccrualStartDt = $disburse->int_accrual_start_dt;
            $gracePeriodDate = $this->addDays($invoiceDueDate, $gracePeriod);
            $overDueInterestDate = $this->addDays($invoiceDueDate, 1);
            $maxAccrualDate = $disburse->interests->max('interest_date');
            
            $principalAmount  = $disburse->principal_amount;
            $settledAmount  = $disburse->settlement_amount;
            $totalInterest = $disburse->total_interest ? $disburse->total_interest :0;
           
            $settledInterest = 0;

            if($int_type_config==2){
                $balancePrincipalAmt = round(($principalAmount + $totalInterest) - ($settledAmount + $settledInterest),5);
            }else{
                $balancePrincipalAmt = round(($principalAmount - $settledAmount),5);
            }
            
            if($transDate && $maxAccrualDate && strtotime($transDate)<= strtotime($maxAccrualDate)){
                $intAccrualDt = $transDate;
            }elseif($transDate && $intAccrualStartDt && strtotime($transDate)<= strtotime($intAccrualStartDt)){
                $intAccrualDt = $transDate;
            }elseif($maxAccrualDate){
                $intAccrualDt = $this->addDays($maxAccrualDate, 1);
            }else{
                $intAccrualDt = $intAccrualStartDt;
            }
            /* 
            dump(['intAccrualDt'=>$intAccrualDt, 
                'intAccrualStartDt'=>$intAccrualStartDt,
                'invoiceDueDate'=>$invoiceDueDate,
                'gracePeriod'=>$gracePeriod, 
                'gracePeriodDate'=>$gracePeriodDate,
                'overDueInterestDate'=>$overDueInterestDate, 
                'maxAccrualDate'=>$maxAccrualDate]);
                continue;
            */

            while (strtotime($intAccrualDt) <= strtotime($currentDate) && $balancePrincipalAmt>0) {
                $maxAccrualDate = Disbursal::find($disburse->disbursal_id)->interests->max('interest_date');
                $interest_accrual_id = null;
                
                $currentInterestRate = null;
                $reculateGracePeriodAccrualInterest = False;
                $interestType = null;

                if(strtotime($intAccrualDt) <= strtotime($gracePeriodDate)){
                    $currentInterestRate = $interestRate;
                    $interestType = 1;
                }else{
                    $currentInterestRate = $overdueIntRate;
                    $reculateGracePeriodAccrualInterest = true;
                    $interestType = 2;

                    if(strtotime($this->addDays($gracePeriodDate, 1)) == strtotime($intAccrualDt)){
                        $recalStartDate = $overDueInterestDate;
                        $recalEndDate = $gracePeriodDate;

                        if($transDate && $overDueInterestDate && strtotime($transDate) > strtotime($overDueInterestDate)){
                            $recalStartDate = $transDate;
                        }else{
                            $recalStartDate = $overDueInterestDate;
                        }

                        while(strtotime($recalStartDate)<=strtotime($recalEndDate)){
                            
                            $recalinterest_accrual_id = InterestAccrual::whereDate('interest_date',$recalStartDate)
                            ->where('disbursal_id','=',$disburse->disbursal_id)
                            ->value('interest_accrual_id');

                            $recalAccuredInterest = InterestAccrual::whereDate('interest_date', '<=', $invoiceDueDate)
                                        ->where('disbursal_id','=',$disburse->disbursal_id)
                                        ->sum('accrued_interest');

                            if($int_type_config==2){
                                $recalbalancePrincipalAmt = round(($principalAmount + $recalAccuredInterest) - ($settledAmount + $settledInterest),5);
                            }else{
                                $recalbalancePrincipalAmt = round(($principalAmount - $settledAmount),5);
                            }
                           

                            $recalInterestRate  = (float)$currentInterestRate/100;
                            $recalinterest = round($this->calInterest($recalbalancePrincipalAmt, $recalInterestRate, 1),5);
                            
                            $intAccrualData = [];
                            $intAccrualData['disbursal_id'] = $disburse->disbursal_id;
                            $intAccrualData['interest_date'] = $recalStartDate;
                            $intAccrualData['principal_amount'] = $recalbalancePrincipalAmt;
                            $intAccrualData['accrued_interest'] = $recalinterest;
                            $intAccrualData['interest_rate'] = null;
                            $intAccrualData['overdue_interest_rate'] = $currentInterestRate;
                            
                            if($recalinterest_accrual_id){
                                $recalwhereCond = [];
                                $recalwhereCond['interest_accrual_id'] = $recalinterest_accrual_id;
                                $this->lmsRepo->saveInterestAccrual($intAccrualData,$recalwhereCond);
                            }else{
                                $this->lmsRepo->saveInterestAccrual($intAccrualData);
                            }
                            $recalStartDate = $this->addDays($recalStartDate, 1);
                        }
                        $balancePrincipalAmt = $recalbalancePrincipalAmt;
                    }
                }

                $startOfMonthDate = Carbon::createFromFormat('Y-m-d', $intAccrualDt)->startOfMonth()->format('Y-m-d');
                
                /* Interest Bookin in case of Monthly on Last day of month
                   Interest will be added in pricipal amount for furture interest calculation  
                */
                if((strtotime($intAccrualDt) == strtotime($startOfMonthDate) 
                || strtotime($intAccrualDt) == strtotime($overDueInterestDate))

                
                && strtotime($intAccrualDt) <= strtotime($overDueInterestDate) 


                && $int_type_config == 2 
                
                
                ){

                    if(strtotime($intAccrualDt) == strtotime($startOfMonthDate)){
                        $lastYear = Carbon::createFromFormat('Y-m-d', $intAccrualDt)
                                    ->subMonth()
                                    ->startOfMonth()
                                    ->format('Y');
                        $lastMonth = Carbon::createFromFormat('Y-m-d', $intAccrualDt)
                                    ->subMonth()
                                    ->startOfMonth()
                                    ->format('m');
                    }elseif(strtotime($intAccrualDt) == strtotime($overDueInterestDate)){
                        $lastYear = Carbon::createFromFormat('Y-m-d', $intAccrualDt)
                                    ->startOfMonth()
                                    ->format('Y');
                        $lastMonth = Carbon::createFromFormat('Y-m-d', $intAccrualDt)
                                    ->startOfMonth()
                                    ->format('m');
                    }

                    $lastInterest  = InterestAccrual::whereYear('interest_date',$lastYear)
                                ->whereMonth('interest_date',$lastMonth)
                                ->whereDate('interest_date','<=',$invoiceDueDate)
                                ->where('disbursal_id','=',$disburse->disbursal_id)
                                ->sum('accrued_interest') ?? 0;

                    $totalInterest += $lastInterest;
                    $balancePrincipalAmt += $lastInterest; 

                    if($lastInterest>0){
                        $interestDue = $this->createTransactionData($disburse->user_id, [
                            'amount' => $lastInterest,
                            'trans_date'=>$intAccrualDt,
                            'disbursal_id'=>$disburse->disbursal_id,
                        ], null, config('lms.TRANS_TYPE.INTEREST'), 0);
                        $this->lmsRepo->saveTransaction($interestDue);
                    }
                }

                $calInterestRate  = (float)$currentInterestRate/100;
                $interest = round($this->calInterest($balancePrincipalAmt, $calInterestRate, 1),5);
                
                $intAccrualData = [];
                $intAccrualData['disbursal_id'] = $disburse->disbursal_id;
                $intAccrualData['interest_date'] = $intAccrualDt;
                $intAccrualData['principal_amount'] = $balancePrincipalAmt;
                $intAccrualData['accrued_interest'] = $interest;
                $intAccrualData['interest_rate'] = ($interestType==1)?$currentInterestRate:null;
                $intAccrualData['overdue_interest_rate'] = ($interestType==2)?$currentInterestRate:null;

                $interest_accrual_id = InterestAccrual::whereDate('interest_date',$intAccrualDt)
                ->where('disbursal_id','=',$disburse->disbursal_id)
                ->value('interest_accrual_id');
          

                if($interest_accrual_id){
                    $whereCond = [];
                    $whereCond['interest_accrual_id'] = $interest_accrual_id;
                    $this->lmsRepo->saveInterestAccrual($intAccrualData,$whereCond);
                }else{
                    $this->lmsRepo->saveInterestAccrual($intAccrualData);
                }
                 
                $intAccrualDt = $this->addDays($intAccrualDt, 1);
            }
            
            if(strtotime($maxAccrualDate)>strtotime($intAccrualDt)){
                InterestAccrual::where('disbursal_id','=',$disburse->disbursal_id)
                                ->whereDate('interest_date','>',$intAccrualDt)
                                ->delete();
            }

            $accuredInterest = $disburse->interests->sum('accrued_interest');
            
            $penalDays = $disburse->interests->where('overdue_interest_rate','!=', NULL)->count();
            
            $penalAmount = $disburse->interests->where('overdue_interest_rate','!=', NULL)->sum('accrued_interest');

            $accuredInterest = $this->lmsRepo->sumAccruedInterest(['disbursal_id' => $disburse->disbursal_id]);
            $overDueDetails = $this->getOverDueInterest($disburse->invoice_id);
           
            $saveDisbursalData = [];
            $saveDisbursalData['accured_interest'] = $accuredInterest;
            $saveDisbursalData['penalty_amount'] = $penalAmount;
            $saveDisbursalData['penal_days'] = $penalDays;
            $saveDisbursalData['total_interest'] = $totalInterest;
            $returnData[$disburse->disbursal_id] = $accuredInterest;
            $this->lmsRepo->saveDisbursalRequest($saveDisbursalData, ['disbursal_id' => $disburse->disbursal_id]);
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
    
    protected function invoiceKnockOff($transId){
        $transDetail = Transactions::whereIn('is_settled',[0,1])->where(['trans_id'=>$transId,'trans_type'=>config('lms.TRANS_TYPE.REPAYMENT')])->get()->first();
       
        if($transDetail->count()>0)
        {
            
            $lastDisbursalId  = null;
            $offset = 0;

            $trans['user_id'] = $transDetail['user_id'];
            $trans['trans_date'] = $transDetail['trans_date'];
            $trans['balance_amount'] = $transDetail['amount']-$transDetail['settled_amount'];

            $disbursalCount = Disbursal::where(['user_id'=>$trans['user_id']])
                ->where('int_accrual_start_dt', '<=', DB::raw(DATE("'".$trans['trans_date']."'")))
                ->whereIn('status_id',[config('lms.STATUS_ID.PARTIALLY_PAYMENT_SETTLED'),config('lms.STATUS_ID.DISBURSED')])
                ->count();
            
            $disbursalData =  Disbursal::where(['user_id'=>$transDetail['user_id']])
                //->whereIn('status_id',[config('lms.STATUS_ID.PARTIALLY_PAYMENT_SETTLED'),config('lms.STATUS_ID.DISBURSED')])
                ->where('int_accrual_start_dt', '<=', DB::raw(DATE("'".$trans['trans_date']."'")))
                ->orderBy('inv_due_date','asc')
                ->orderBy('disbursal_id','asc')
                ->get();

            $transactionData = [];
            foreach ($disbursalData as $key => $disbursalDetail) {
                 
                if($disbursalDetail->count()>0)
                {
                    $offset++;
                    $interestRefund = 0;
                    $interestOverdue = 0;
                    $is_principal_settled = 0;
                    $invoiceRepayment = [];
                    $disbursal = [];
                    $repaidAmount = 0;
                    $settlementLevel = 0;
                    $interestDue = 0;  
                    $interestSettled = 0;
                    $marginPaidAmt = 0;
                    $interestPaidAmt = 0;
                    $principalPaidAmt = 0; 
                    $overduePaidAmt = 0;
                    $restInterestPaidAmt = 0;               

                    $lastDisbursalId = $disbursalDetail->disbursal_id;
              
                    $accured_interest = $disbursalData[$key]
                                        ->interests
                                        ->where('interest_date', '<=', DB::raw(DATE($trans['trans_date'])))
                                        ->sum('accrued_interest');
            
                    $penalDays = $disbursalData[$key]
                                    ->interests
                                    ->where('interest_date', '<=', DB::raw(DATE($trans['trans_date'])))
                                    ->where('overdue_interest_rate','!=', NULL)
                                    ->count();
                    
                    $penalAmount = $disbursalData[$key]
                                    ->interests
                                    ->where('interest_date', '<=', DB::raw(DATE($trans['trans_date'])))
                                    ->where('overdue_interest_rate','!=', NULL)
                                    ->sum('accrued_interest');
                    
                    $marginAmountDetails = $this->userRepo->getDisbursalList()->where('disbursal_id','=',$disbursalDetail->disbursal_id)
                                    ->groupBy('margin')
                                    ->select(DB::raw('(sum(invoice_approve_amount)*margin)/100 as margin_amount,margin'))
                                    ->first();
                    $totalMarginAmount = $marginAmountDetails->margin_amount;

                    $overdueSettled = Transactions::where('disbursal_id','=',$disbursalDetail->disbursal_id)
                                    ->where('trans_type','=',config('lms.TRANS_TYPE.INTEREST_OVERDUE'))
                                    ->where('entry_type','=','0')
                                    ->sum('amount');
                    
                    $marginSettled = Transactions::where('disbursal_id','=',$disbursalDetail->disbursal_id)
                                    ->where('trans_type','=',config('lms.TRANS_TYPE.MARGIN'))
                                    ->where('entry_type','=','1')
                                    ->sum('amount');
                    
                    $interestSettled = Transactions::where('disbursal_id','=',$disbursalDetail->disbursal_id)
                                    ->where('trans_type','=',config('lms.TRANS_TYPE.INTEREST_PAID'))
                                    ->sum('amount');
                    
                    $principalSettled = Transactions::where('disbursal_id','=',$disbursalDetail->disbursal_id)
                                    ->whereIn('trans_type',[config('lms.TRANS_TYPE.INVOICE_KNOCKED_OFF'),config('lms.TRANS_TYPE.INVOICE_PARTIALLY_KNOCKED_OFF')])
                                    ->sum('amount');

                    $marginDueAmt = $totalMarginAmount-$marginSettled;

                    $prgmOffer = $this->lmsRepo->getProgramOffer([
                                    'app_id' => $disbursalDetail->app_id,
                                    'invoice_id' => $disbursalDetail->invoice_id,   
                                    'disbursal_id' => $disbursalDetail->disbursal_id,   
                                ]);
                    
                    $int_type_config = $prgmOffer->payment_frequency ? $prgmOffer->payment_frequency : 1;
                                    
                    $disbursal = [
                        'total_repaid_amt'=>(float)$disbursalDetail->total_repaid_amt,
                        'interest_refund'=>(float)$disbursalDetail->interest_refund,
                        'settlement_amount'=>(float)$disbursalDetail->settlement_amount,
                        'status_id'=>$disbursalDetail->status_id,
                        'surplus_amount'=>(float)$disbursalDetail->surplus_amount,
                        'accured_interest'=> $accured_interest,
                        'penalty_amount'=> $penalAmount,
                        'penal_days'=> $penalDays,
                    ];

                /* Step 0 : Interest Calculation */

                    switch ($int_type_config) {
                        case '1':
                            $interestDue = 0;
                        break;
                        case '2':
                            $interestDue = (float)$disbursalDetail->total_interest - $interestSettled;
                        break;
                        case '3':
                            $interestDue = (float)$accured_interest - $interestSettled;
                        break;
                    }
                    $interestRefund = (float)$disbursalDetail->total_interest-(float)$accured_interest;

                /* Step 1 : Interest Settlement */

                    if($interestDue>0 && $trans['balance_amount']>0)
                    {
                        $interestPaidAmt = ($trans['balance_amount']>=$interestDue)?$interestDue:$trans['balance_amount'];
                        $trans['balance_amount'] -= $interestPaidAmt;
                        $disbursal['total_repaid_amt'] += $interestPaidAmt;

                        $interestPaidData = $this->createTransactionData($transDetail['user_id'], [
                            'amount' => $interestPaidAmt,
                            'trans_date'=>$transDetail['trans_date'],
                            'disbursal_id'=>$disbursalDetail->disbursal_id,
                            'parent_trans_id'=>$transId
                        ], null, config('lms.TRANS_TYPE.INTEREST_PAID'), 0);

                        $transactionData['interestPaid'][] = $interestPaidData;

                        $settlementLevel = 1;
                    }

                /* Step 2 : Principal Settlement */

                    $balancePrincipalAmt = (float)$disbursalDetail->principal_amount-(float)$principalSettled;
                  
                    if($balancePrincipalAmt>0 && $trans['balance_amount']>0)
                    {
                        $principalPaidAmt = ($trans['balance_amount']>=$balancePrincipalAmt)?$balancePrincipalAmt:$trans['balance_amount'];
                        $is_principal_settled = ($trans['balance_amount']>=$balancePrincipalAmt)?2:1;

                        $trans['balance_amount'] -= $principalPaidAmt;
                        $disbursal['total_repaid_amt'] += $principalPaidAmt;
                        $disbursal['settlement_amount'] += $principalPaidAmt;
                        
                        $knockOffData = $this->createTransactionData($transDetail['user_id'], [
                            'amount' =>  $principalPaidAmt,
                            'trans_date'=>$transDetail['trans_date'],
                            'disbursal_id'=>$disbursalDetail->disbursal_id,
                            'parent_trans_id'=>$transId
                        ], null, ($is_principal_settled==2)?config('lms.TRANS_TYPE.INVOICE_KNOCKED_OFF'):config('lms.TRANS_TYPE.INVOICE_PARTIALLY_KNOCKED_OFF'), 0);
                        $transactionData['knockOff'][$disbursalDetail->disbursal_id] = $knockOffData;
                        
                        $settlementLevel = 2;
                    }

                 /* Step 1.1 : Interest Settlement */

                    
                    if(($accured_interest-$interestDue)>0 && $trans['balance_amount']>0 && $is_principal_settled==2 && $int_type_config != '1')
                    {
                        $restInterestPaidAmt = ($trans['balance_amount']>=($accured_interest-$interestDue))?($accured_interest-$interestDue):$trans['balance_amount'];
                        $trans['balance_amount'] -= $restInterestPaidAmt;
                        $disbursal['total_repaid_amt'] += $restInterestPaidAmt;

                        $transactionData['interestPaid'][] = $this->createTransactionData($transDetail['user_id'], [
                            'amount' => $restInterestPaidAmt,
                            'trans_date'=>$transDetail['trans_date'],
                            'disbursal_id'=>$disbursalDetail->disbursal_id,
                            'parent_trans_id'=>$transId
                        ], null, config('lms.TRANS_TYPE.INTEREST'), 0);

                        $interestPaidData = $this->createTransactionData($transDetail['user_id'], [
                            'amount' => $restInterestPaidAmt,
                            'trans_date'=>$transDetail['trans_date'],
                            'disbursal_id'=>$disbursalDetail->disbursal_id,
                            'parent_trans_id'=>$transId
                        ], null, config('lms.TRANS_TYPE.INTEREST_PAID'), 0);

                        $transactionData['interestPaid'][] = $interestPaidData;

                        $settlementLevel = 1;
                    }

                    if($marginDueAmt>0 && $trans['balance_amount']>0)
                    {

                        $marginPaidAmt = ($trans['balance_amount']>=$marginDueAmt)?$marginDueAmt:$trans['balance_amount'];

                        $trans['balance_amount'] -= $marginPaidAmt;
                        $disbursal['total_repaid_amt'] += $marginPaidAmt;
                        
                        $marginData = $this->createTransactionData($transDetail['user_id'], [
                            'amount' =>  $marginPaidAmt,
                            'trans_date'=>$transDetail['trans_date'],
                            'disbursal_id'=>$disbursalDetail->disbursal_id,
                            'parent_trans_id'=>$transId
                        ], null, config('lms.TRANS_TYPE.MARGIN'), 1);

                        $transactionData['margin'][$disbursalDetail->disbursal_id] = $marginData;
                        
                        $settlementLevel = 3;
                    }                

                /* Step 3 : ODI charges Settlement */

                    if($penalAmount > 0 && $penalDays > 0){
                        $interestOverdue = (float)$penalAmount-(float)$overdueSettled;
                    }

                    if($interestOverdue>0 && ($marginPaidAmt>0 || $trans['balance_amount']>0))
                    {
                        if($marginPaidAmt >= $interestOverdue){
                            $overduePaidAmt = $interestOverdue;
                        }
                        elseif($marginPaidAmt+$trans['balance_amount'] >= $interestOverdue ){ 
                            $overduePaidAmt = $marginPaidAmt+$trans['balance_amount']-$interestOverdue;
                            $trans['balance_amount'] -= $marginPaidAmt-$interestOverdue;
                        }else{
                            $overduePaidAmt = $marginPaidAmt+$trans['balance_amount'];
                            $trans['balance_amount'] = 0;
                            $marginPaidAmt = 0;
                        }

                        $marginPaidAmt -= $overduePaidAmt;
                        $disbursal['total_repaid_amt'] += $overduePaidAmt;

                        $overdueData = $this->createTransactionData($transDetail['user_id'], [
                            'amount' => $overduePaidAmt,
                            'trans_date'=>$transDetail['trans_date'],
                            'disbursal_id'=>$disbursalDetail->disbursal_id,
                            'parent_trans_id'=>$transId
                        ], null, config('lms.TRANS_TYPE.INTEREST_OVERDUE'), 0);
                        $transactionData['overdue'][$disbursalDetail->disbursal_id] = $overdueData;

                        $settlementLevel = 3;
                    }
                
                /* Step 5 : Interest Refund  */
                    
                    if($interestRefund>0 && $is_principal_settled == 2)
                    { 
                        $refundData = $this->createTransactionData($transDetail['user_id'], [
                            'amount' => $interestRefund,
                            'trans_date'=>$transDetail['trans_date'],
                            'disbursal_id'=>$disbursalDetail->disbursal_id,
                            'parent_trans_id'=>$transId
                        ], null,config('lms.TRANS_TYPE.INTEREST_REFUND'), 1);
                        $transactionData['interestRefund'][$disbursalDetail->disbursal_id] = $refundData;
                        
                        $disbursal['interest_refund'] = $interestRefund;
                    }   

                    /* 
                    $invoiceRepayment['user_id'] = $transDetail['user_id'];
                    $invoiceRepayment['invoice_id'] = $disbursalDetail->invoice_id;
                    $invoiceRepayment['repaid_amount'] = round($principalPaidAmt,5);
                    $invoiceRepayment['repaid_date'] = $transDetail['trans_date'];
                    $invoiceRepayment['trans_type'] = ($is_inv_settled==2)?config('lms.TRANS_TYPE.INVOICE_KNOCKED_OFF'):config('lms.TRANS_TYPE.INVOICE_PARTIALLY_KNOCKED_OFF');
                    $transactionData['repaymentTrail'][$disbursalDetail->disbursal_id] = $invoiceRepayment;
                    */        
                    
                    $disbursal['status_id'] = ($is_principal_settled == 2)?config('lms.STATUS_ID.PAYMENT_SETTLED'):config('lms.STATUS_ID.PARTIALLY_PAYMENT_SETTLED');
                        
                    $transactionData['disbursal'][$disbursalDetail->disbursal_id] = $disbursal;
                    
                }
                if($trans['balance_amount']<=0) break;
            }

            $getChargesDetails = Transactions::where('user_id','=',$transDetail['user_id'])
                        ->where('entry_type','=',0)
                        ->where('created_at', '<=', DB::raw(DATE("'".$trans['trans_date']."'")))
                        ->whereHas('trans_detail', function($query){ 
                            $query->where('chrg_master_id','!=','0');
                        })
                        ->orderBy('trans_date','asc')->get();
            
            foreach ($getChargesDetails as $key => $chargeDetail) {
                if($trans['balance_amount']>0){
                    $chargePaidAmt = ($chargeDetail->amount<=$trans['balance_amount'])?$chargeDetail->amount:$trans['balance_amount'];
                    $trans['balance_amount'] -= $chargePaidAmt; 
                    $chargesSettledData = $this->createTransactionData($transDetail['user_id'], [
                        'amount' => $chargePaidAmt,
                        'trans_date'=>$transDetail['trans_date'],
                        'parent_trans_id'=>$transId
                    ], null, $chargeDetail->trans_type, 0);
                    $transactionData['charges'][] = $chargesSettledData;
                }
                
                if($trans['balance_amount']<=0) break;
            }

            if($trans['balance_amount']>0)
            { 
                $paymentReverseData = $this->createTransactionData($transDetail['user_id'], [
                    'amount' => $trans['balance_amount'],
                    'trans_date'=>$transDetail['trans_date'],
                    'parent_trans_id'=>$transId
                ], null, config('lms.TRANS_TYPE.PAYMENT_REVERSE'), 0);
                $transactionData['reversePayment'][] = $paymentReverseData;
            }

            if($lastDisbursalId>0 && $trans['balance_amount']>0)
            {
                $nonFactoredAmtData = $this->createTransactionData($transDetail['user_id'], [
                    'amount' => $trans['balance_amount'],
                    'trans_date'=>$transDetail['trans_date'],
                    'parent_trans_id'=>$transId
                ], null,config('lms.TRANS_TYPE.NON_FACTORED_AMT'), 1);
                $transactionData['nonFactoredAmt'][] = $nonFactoredAmtData;
            } 
            
            if(!empty($transactionData['repaymentTrail']))
            foreach ($transactionData['repaymentTrail'] as $rePayTrailValue) {
                $this->lmsRepo->saveRepayment($rePayTrailValue);
            }
                
            if(!empty($transactionData['disbursal']))
            foreach ($transactionData['disbursal'] as $dibursalKey => $dibursalValue) {
                $this->lmsRepo->saveDisbursalRequest($dibursalValue, ['disbursal_id' => $dibursalKey]);
            }
            
            if(!empty($transactionData['knockOff']))
            foreach($transactionData['knockOff'] as $knockOffValue){
                $this->lmsRepo->saveTransaction($knockOffValue);
            }
            
            if(!empty($transactionData['overdue']))
            foreach ($transactionData['overdue'] as $overdueValue) {
                $this->lmsRepo->saveTransaction($overdueValue);
            }
            
            if(!empty($transactionData['interestPaid']))
            foreach($transactionData['interestPaid'] as $interestPaidValue){
                $this->lmsRepo->saveTransaction($interestPaidValue);
            }

            if(!empty($transactionData['interestRefund']))
            foreach ($transactionData['interestRefund'] as $interestRefundValue){
                $this->lmsRepo->saveTransaction($interestRefundValue);
            }
            
            if(!empty($transactionData['nonFactoredAmt']))
            foreach($transactionData['nonFactoredAmt'] as $nonFactoredAmtValue){
                $this->lmsRepo->saveTransaction($nonFactoredAmtValue);
            }

            if(!empty($transactionData['margin']))
            foreach($transactionData['margin'] as $marginReleasedValue){
                $this->lmsRepo->saveTransaction($marginReleasedValue);
            }

            if(!empty($transactionData['charges']))
            foreach($transactionData['charges'] as $chargesValue){
                $this->lmsRepo->saveTransaction($chargesValue);
            }

            // if(!empty($transactionData['reversePayment']))
            // foreach ($transactionData['reversePayment'] as $interestRevPaymentValue){
            //     $this->lmsRepo->saveTransaction($interestRevPaymentValue);
            // }

            $knockedOffDisbursedIds =  Transactions::where('parent_trans_id','=',$transId)
            ->pluck('disbursal_id')->toArray();

            $this->calAccrualInterest($this->addDays($trans['trans_date'], 1),$knockedOffDisbursedIds);
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
        $interest = 0;
        $now = strtotime($invoice['invoice_due_date']); // or your date as well
        $your_date = strtotime($invoice['invoice_date']);
        $datediff = abs($now - $your_date);
        $tenor = round($datediff / (60 * 60 * 24));
        $fundedAmount = $invoice['invoice_approve_amount'] - (($invoice['invoice_approve_amount']*$invoice['program_offer']['margin'])/100);
        $totalinterest = $this->calInterest($fundedAmount, (float)$invoice['program_offer']['interest_rate']/100, $tenor);
        if($invoice['program_offer']['payment_frequency'] == 1 || empty($invoice['program_offer']['payment_frequency'])) {
            $interest = $totalinterest;
        }
        $disburseAmount = round($fundedAmount - $interest, 2);

        $disbursalData['user_id'] = $invoice['supplier_id'] ?? null;
        $disbursalData['app_id'] = $invoice['app_id'] ?? null;
        $disbursalData['invoice_id'] = $invoice['invoice_id'] ?? null;
        $disbursalData['prgm_offer_id'] = $invoice['prgm_offer_id'] ?? null;
        $disbursalData['bank_account_id'] = ($invoice['supplier']['is_buyer'] == 2) ? $invoice['supplier']['anchor_bank_details']['bank_account_id'] : $invoice['supplier_bank_detail']['bank_account_id'];
        $disbursalData['disburse_date'] = (!empty($invoice['disburse_date'])) ? date("Y-m-d h:i:s", strtotime(str_replace('/','-',$invoice['disburse_date']))) : \Carbon\Carbon::now()->format('Y-m-d h:i:s');
        $disbursalData['bank_name'] = ($invoice['supplier']['is_buyer'] == 2) ? $invoice['supplier']['anchor_bank_details']['bank']['bank_name'] : $invoice['supplier_bank_detail']['bank']['bank_name'] ;
        $disbursalData['ifsc_code'] = ($invoice['supplier']['is_buyer'] == 2) ? $invoice['supplier']['anchor_bank_details']['ifsc_code'] : $invoice['supplier_bank_detail']['ifsc_code'];
        $disbursalData['acc_no'] = ($invoice['supplier']['is_buyer'] == 2) ? $invoice['supplier']['anchor_bank_details']['acc_no'] : $invoice['supplier_bank_detail']['acc_no'];            
        $disbursalData['virtual_acc_id'] = $invoice['lms_user']['virtual_acc_id'] ?? null;
        $disbursalData['customer_id'] = $invoice['lms_user']['customer_id'] ?? null;
        $disbursalData['principal_amount'] = $fundedAmount ?? null;
        $disbursalData['inv_due_date'] = $invoice['invoice_due_date'] ?? null;
        $disbursalData['payment_due_date'] = ($invoice['pay_calculation_on'] == 2) ? date('Y-m-d', strtotime(str_replace('/','-',$invoice['disburse_date']). "+ $tenor Days")) : $invoice['invoice_due_date'];
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
        $disbursalData['int_accrual_start_dt'] = ($disburseType == 2 && !empty($invoice['disburse_date'])) ?  date("Y-m-d", strtotime(str_replace('/','-',$invoice['disburse_date']))) : null;
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
        $transactionData['parent_trans_id'] = $data['parent_trans_id'] ?? null;
        $transactionData['gl_flag'] = 1;
        $transactionData['soa_flag'] = in_array($transType,[10,35]) ? 0 : 1;
        $transactionData['user_id'] = $userId ?? null;
        $transactionData['disbursal_id'] = $data['disbursal_id'] ?? null;
        $transactionData['virtual_acc_id'] = $userId ? $this->appRepo->getVirtualAccIdByUserId($userId) : null;
        $transactionData['trans_date'] = (!empty($data['trans_date'])) ? date("Y-m-d h:i:s", strtotime(str_replace('/','-',$data['trans_date']))) : \Carbon\Carbon::now()->format('Y-m-d h:i:s');
        $transactionData['trans_type'] = $transType ?? 0;
        $transactionData['pay_from'] = ($transType == 16) ? 3 : $this->appRepo->getUserTypeByUserId($userId);
        $transactionData['amount'] = $data['amount'] ?? 0;
        $transactionData['settled_amount'] = $data['settled_amount'] ?? 0;
        $transactionData['gst'] = $data['gst'] ?? 0;
        $transactionData['cgst'] = $data['cgst'] ?? 0;            
        $transactionData['sgst'] = $data['sgst'] ?? 0;
        $transactionData['igst'] = $data['igst'] ?? 0;
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
        $monthlyIntCond['overdue_interest_rate_not_null'] = '1';
       // $monthlyIntCond['interest_date_gte'] = $invDueDate;   //date('Y-m-d', strtotime($invDueDate));
        $accuredInterest = $this->lmsRepo->sumAccruedInterest($monthlyIntCond);
        $accuredInterestCount =  $this->lmsRepo->countAccruedInterest($monthlyIntCond);
        return array('penal_amount' => $accuredInterest, 'penal_days'=>$accuredInterestCount);
    }
    
    protected  function businessInformation($attr)
    {
      try
        { 
          $date = Carbon::now();
          $id = Auth::user()->user_id;
          $business = Business::find($attr->biz_id);
          $obj =   $business->replicate();
                $obj->biz_id = "";
                $obj->created_by = $id;
                $obj->created_at = $date;
                $obj->save();
        return $obj;
      } catch (Exception $ex) {
           return false;
      }
       
    }
    protected  function bizPanGst($biz_details)
    {
      try
        { 
          $date = Carbon::now();
          $id = Auth::user()->user_id;
          $business = BizPanGst::find($biz_details->biz_pan_gst_id);
          dd($business);
          $obj =   $business->replicate();
                $obj->biz_pan_gst_id = "";
                $obj->biz_id = $biz_details->biz_id;
                $obj->created_by = $id;
                $obj->created_at = $date;
                $obj->save();
        return $obj->biz_id;
      } catch (Exception $ex) {
           return false;
      }
       
    }
     protected  function applicationSave($app_id,$biz_id)
    {
       try
       {   
            $date = Carbon::now();
            $id = Auth::user()->user_id;
            $app = Application::find($app_id);
            $obj =   $app->replicate();
            $obj->app_id = "";
            $obj->biz_id = $biz_id;
            $obj->created_by = $id;
            $obj->created_at = $date;
            $obj->save(); 
        return $obj->app_id;
       } catch (Exception $ex) {

       }
    }
     protected  function managementInformation($attr)
    {
       dd($attr);
    }
     protected  function document($attr)
    {
       dd($attr);
    } 
}
