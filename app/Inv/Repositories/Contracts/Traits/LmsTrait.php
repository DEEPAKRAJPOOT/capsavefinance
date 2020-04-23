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
use App\Helpers\ApportionmentHelper;
use App\Inv\Repositories\Models\Lms\RefundTransactions;

use App\Inv\Repositories\Models\Lms\InvoiceDisbursed;

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

    protected function calMargin($amt, $val)
    {
        return ($amt*$val)/100;
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

    protected function calAccrualInterest_old($transDate=null, $currentDate=null){
        if(!$currentDate){
            $currentDate = $this->subDays(date('Y-m-d'),1);
        }
        $disbursalData = Disbursal::whereIn('status_id',[12,13])
        ->whereDate('int_accrual_start_dt','<=',$currentDate)->get();
        $interest = 0;
        $returnData = [];
        echo '<br>------------------------------------------------------------<br>'.$currentDate.'<br>';

        foreach ($disbursalData as $disburse) {
            $intAccrualDt = NULL;
            
            $whereProgramOffer = [];
            $whereProgramOffer['app_id'] = $disburse->app_id;
            $whereProgramOffer['invoice_id'] = $disburse->invoice_id;      
            $whereProgramOffer['disbursal_id'] = $disburse->disbursal_id;
            $prgmOffer = $this->lmsRepo->getProgramOffer($whereProgramOffer);
            
            $int_type_config = $prgmOffer->payment_frequency ? $prgmOffer->payment_frequency : 1;
            
            $gracePeriod = $prgmOffer->grace_period ? $prgmOffer->grace_period : 0;
            $gracePeriod = ($gracePeriod <= 1)?1:$gracePeriod;
            $interestRate = $disburse->interest_rate ?? $prgmOffer->interest_rate;
            $overdueIntRate = $disburse->overdue_interest_rate ?? $prgmOffer->overdue_interest_rate;
            
            $invoiceDueDate  = $disburse->payment_due_date;
            $intAccrualStartDt = $disburse->int_accrual_start_dt;
            $gracePeriodDate = $this->addDays($invoiceDueDate, ($gracePeriod-1));
            $overDueInterestDate = $this->addDays($invoiceDueDate, 0);
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
                            $balancePrincipalAmt = $recalbalancePrincipalAmt;
                        }
                    }
                }

                $endOfMonthDate = Carbon::createFromFormat('Y-m-d', $intAccrualDt)->endOfMonth()->format('Y-m-d');
                
                /* Interest Bookin in case of Monthly on Last day of month
                   Interest will be added in pricipal amount for furture interest calculation  
                */
                if((strtotime($intAccrualDt) == strtotime($endOfMonthDate) 
                || strtotime($intAccrualDt) == strtotime($overDueInterestDate))

                
                && strtotime($intAccrualDt) <= strtotime($overDueInterestDate) 


                && $int_type_config == 2 
                
                
                ){

                    if(strtotime($intAccrualDt) == strtotime($endOfMonthDate)){
                        $lastYear = Carbon::createFromFormat('Y-m-d', $intAccrualDt)
                                    //->subMonth()
                                    ->startOfMonth()
                                    ->format('Y');
                        $lastMonth = Carbon::createFromFormat('Y-m-d', $intAccrualDt)
                                    //->subMonth()
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

                    if($lastInterest > 0){
                        $interestDue = $this->createTransactionData($disburse->user_id, [
                            'amount' => $lastInterest,
                            'trans_date'=>$this->subDays($intAccrualDt,1),
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

    protected function calAccrualInterest($transDate=null, $currentDate=null){
        $curdate = \Carbon\Carbon::now()->setTimezone(config('common.timezone'))->format('Y-m-d');
        if(!$currentDate){
            $currentDate = $this->subDays($curdate,1);
        }
        
        $unsettledInvoicesTrans = $this->lmsRepo->getUnsettledInvoices(['int_accrual_start_dt'=>$currentDate]);
        
        echo '<br>------------------------------------------------------------<br>'.$currentDate.'<br>';

        foreach ($unsettledInvoicesTrans as $key => $trans) {
            $intAccrualDt = NULL;
            
            $whereProgramOffer = [];   
            $whereProgramOffer['disbursal_id'] = $trans->invoiceDisbursed->disbursal_id;
            $prgmOffer =    $trans->invoiceDisbursed->invoice->program_offer;
            
            $int_type_config = $prgmOffer->payment_frequency ? $prgmOffer->payment_frequency : 1;
             
            $gracePeriod = $prgmOffer->grace_period ? $prgmOffer->grace_period : 0;
            $interestRate = $trans->invoiceDisbursed->interest_rate ?? $prgmOffer->interest_rate;
            $overdueIntRate = $trans->invoiceDisbursed->overdue_interest_rate ?? $prgmOffer->overdue_interest_rate;
            
            $invoiceDueDate  = $trans->invoiceDisbursed->payment_due_date;
            $intAccrualStartDt = $trans->invoiceDisbursed->int_accrual_start_dt;
            $gracePeriodDate = $this->addDays($invoiceDueDate, ($gracePeriod-1));
            $overDueInterestDate = $this->addDays($invoiceDueDate, 0);
            $maxAccrualDate = $trans->invoiceDisbursed->interests->max('interest_date');

            $principalAmount  = $trans->amount;
            $settledAmount  = $trans->settled_amt;
            $totalInterest = $trans->invoiceDisbursed->total_interest ? $trans->invoiceDisbursed->total_interest :0;
           
            $settledInterest = 0;

            if($int_type_config==2){
                $balancePrincipalAmt = round(($trans->outstanding + $totalInterest),5);
            }else{
                $balancePrincipalAmt = $trans->outstanding;
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

            while (strtotime($intAccrualDt) <= strtotime($currentDate) && $balancePrincipalAmt>0) {
                $maxAccrualDate = InvoiceDisbursed::find($trans->invoice_disbursed_id)->interests->max('interest_date');
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
                            ->where('invoice_disbursed_id','=',$trans->invoice_disbursed_id)
                            ->value('interest_accrual_id');

                            $recalAccuredInterest = InterestAccrual::whereDate('interest_date', '<=', $invoiceDueDate)
                            ->where('invoice_disbursed_id','=',$trans->invoice_disbursed_id)
                            ->sum('accrued_interest');

                            if($int_type_config==2){
                                $recalbalancePrincipalAmt = round(($principalAmount + $recalAccuredInterest) - ($settledAmount + $settledInterest),5);
                            }else{
                                $recalbalancePrincipalAmt = round(($principalAmount - $settledAmount),5);
                            }
                           
                            $recalInterestRate  = (float)$currentInterestRate/100;
                            $recalinterest = round($this->calInterest($recalbalancePrincipalAmt, $recalInterestRate, 1),5);
                            
                            $intAccrualData = [];
                            $intAccrualData['invoice_disbursed_id'] = $trans->invoice_disbursed_id;
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
                            $balancePrincipalAmt = $recalbalancePrincipalAmt;
                        }
                    }
                }

                $endOfMonthDate = Carbon::createFromFormat('Y-m-d', $intAccrualDt)->endOfMonth()->format('Y-m-d');
                
                /* Interest Booking in case of Monthly on Last day of month
                   Interest will be added in pricipal amount for furture interest calculation  
                */
                if((strtotime($intAccrualDt) == strtotime($endOfMonthDate) 
                || strtotime($intAccrualDt) == strtotime($overDueInterestDate))
                && strtotime($intAccrualDt) <= strtotime($overDueInterestDate) 
                && $int_type_config == 2 
                ){

                    if(strtotime($intAccrualDt) == strtotime($endOfMonthDate)){
                        $lastYear = Carbon::createFromFormat('Y-m-d', $intAccrualDt)
                                    ->startOfMonth()
                                    ->format('Y');
                        $lastMonth = Carbon::createFromFormat('Y-m-d', $intAccrualDt)
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
                                ->where('invoice_disbursed_id','=',$trans->invoice_disbursed_id)
                                ->sum('accrued_interest') ?? 0;

                    $totalInterest += $lastInterest;
                    $balancePrincipalAmt += $lastInterest; 

                    if($lastInterest > 0){
                        $transInterestId = Transactions::where('invoice_disbursed_id','=',$trans->invoice_disbursed_id)
                        ->where('trans_type','=',config('lms.TRANS_TYPE.INTEREST'))
                        ->whereMonth('trans_date',$lastMonth)
                        ->where('user_id','=',$trans->user_id)->value('trans_id');

                        $transIntData = [
                            'invoice_disbursed_id'=>$trans->invoice_disbursed_id,
                            'trans_type'=>9,
                            'user_id'=>$trans->user_id,
                            'trans_date'=>$this->subDays($intAccrualDt,1),
                            'amount'=>$lastInterest,
                            'entry_type'=>0
                        ];
                        if($transInterestId){
                            Transactions::saveTransaction($transIntData,['trans_id'=>$transInterestId]);
                        }else{
                            Transactions::saveTransaction($transIntData);
                        }
                    }
                }

                $calInterestRate  = (float)$currentInterestRate/100;
                $interest = round($this->calInterest($balancePrincipalAmt, $calInterestRate, 1),5);
                
                $intAccrualData = [];
                $intAccrualData['invoice_disbursed_id'] = $trans->invoice_disbursed_id;
                $intAccrualData['interest_date'] = $intAccrualDt;
                $intAccrualData['principal_amount'] = $balancePrincipalAmt;
                $intAccrualData['accrued_interest'] = $interest;
                $intAccrualData['interest_rate'] = ($interestType==1)?$currentInterestRate:null;
                $intAccrualData['overdue_interest_rate'] = ($interestType==2)?$currentInterestRate:null;

                $interest_accrual_id = InterestAccrual::whereDate('interest_date',$intAccrualDt)
                ->where('invoice_disbursed_id','=',$trans->invoice_disbursed_id)
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
                InterestAccrual::where('invoice_disbursed_id','=',$trans->invoice_disbursed_id)
                                ->whereDate('interest_date','>',$intAccrualDt)
                                ->delete();
            }
            
            $accuredInterest = $trans->invoiceDisbursed->interests->sum('accrued_interest');
            
            $penalDays = $trans->invoiceDisbursed->interests->where('overdue_interest_rate','!=', NULL)->count();
            
            $penalAmount = $trans->invoiceDisbursed->interests->where('overdue_interest_rate','!=', NULL)->sum('accrued_interest');

            $accuredInterest = $this->lmsRepo->sumAccruedInterest(['invoice_disbursed_id' => $trans->invoice_disbursed_id]);
           
            $saveDisbursalData = [];
            $saveDisbursalData['accured_interest'] = $accuredInterest;
            $saveDisbursalData['penalty_amount'] = $penalAmount;
            $saveDisbursalData['penal_days'] = $penalDays;
            $saveDisbursalData['total_interest'] = $totalInterest;
            
            if($int_type_config==3){
                $transOverdueData = Transactions::firstOrNew(
                    ['invoice_disbursed_id' => $trans->invoice_disbursed_id, 'trans_type'=>config('lms.TRANS_TYPE.INTEREST'), 'user_id'=>$trans->user_id],
                    ['trans_date'=>$currentDate,
                    'amount'=>$accuredInterest,
                    'entry_type'=>0,
                    'created_at'=>$currentDate,
                    'created_by'=>1
                    ]
                );
            }
            
            if($penalAmount>0 && $penalDays>0){
                $transOverdueData = Transactions::firstOrNew(
                    ['invoice_disbursed_id' => $trans->invoice_disbursed_id, 'trans_type'=>config('lms.TRANS_TYPE.INTEREST_OVERDUE'), 'user_id'=>$trans->user_id],
                    ['trans_date'=>$currentDate,
                    'amount'=>$penalAmount,
                    'entry_type'=>0,
                    'created_at'=>$currentDate,
                    'created_by'=>1
                    ]
                );
                $transOverdueData->save();
            }

            $returnData[$trans->invoice_disbursed_id] = $accuredInterest;
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

    protected function subDays($currentDate, $noOfDays)
    {
        $calDate = date('Y-m-d', strtotime($currentDate . "- $noOfDays days"));
        return $calDate;
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
     * Prepare Invoice Disbursed Data
     * 
     * @param array $data
     * @return mixed
     */
    protected function createInvoiceDisbursedData($invoice, $disburseType = 2)
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
        $disburseAmount = round($fundedAmount - $interest, config('lms.DECIMAL_TYPE')['AMOUNT']);

        $disbursalData['disbursal_id'] = $invoice['disbursal_id'] ?? null;
        $disbursalData['invoice_id'] = $invoice['invoice_id'] ?? null;
        $disbursalData['customer_id'] = $invoice['lms_user']['customer_id'] ?? null;
        $disbursalData['disbursal_api_log_id'] = $invoice['disbursal_api_log_id'] ?? null;
        $disbursalData['disburse_amt'] = $disburseAmount ?? null;
        $disbursalData['inv_due_date'] = $invoice['invoice_due_date'] ?? null;
        $disbursalData['payment_due_date'] = ($invoice['pay_calculation_on'] == 2) ? date('Y-m-d', strtotime(str_replace('/','-',$invoice['disburse_date']). "+ $tenor Days")) : $invoice['invoice_due_date'];
        $disbursalData['tenor_days'] =  $invoice['program_offer']['tenor'] ?? null;
        $disbursalData['interest_rate'] = $invoice['program_offer']['interest_rate'] ?? null;
        $disbursalData['total_interest'] = $interest;
        $disbursalData['margin'] = $invoice['program_offer']['margin'] ?? null;
        $disbursalData['status_id'] = ($disburseType == 2) ? 10 : 12;
        
        $disbursalData['funded_date'] = null;
        $disbursalData['int_accrual_start_dt'] = ($disburseType == 2 && !empty($invoice['disburse_date'])) ?  date("Y-m-d", strtotime(str_replace('/','-',$invoice['disburse_date']))) : null;
        $disbursalData['grace_period'] = $invoice['program_offer']['grace_period'] ?? null;
        $disbursalData['overdue_interest_rate'] = $invoice['program_offer']['overdue_interest_rate'] ?? null;
        
        $curData = \Carbon\Carbon::now()->format('Y-m-d h:i:s');
                        
        $dataArr['created_by'] = Auth::user()->user_id;
        $dataArr['created_at'] = $curData;
        
        return $disbursalData;
    }

    /**
     * Prepare Disbursal Data
     * 
     * @param array $data
     * @return mixed
     */
    protected function createDisbursalData($invoice, $disburseAmount, $disburseType = 2)
    {
        /**
        * disburseType = 1 for online and 2 for manually
        */
        $disbursalData = [];
        
        $disbursalData['user_id'] = $invoice['supplier_id'] ?? null;
        $disbursalData['app_id'] = $invoice['app_id'] ?? null;

        $disbursalData['disburse_date'] = (!empty($invoice['disburse_date'])) ? date("Y-m-d h:i:s", strtotime(str_replace('/','-',$invoice['disburse_date']))) : \Carbon\Carbon::now()->format('Y-m-d h:i:s');
        $disbursalData['disburse_amount'] = $disburseAmount ?? null;
        $disbursalData['disbursal_batch_id'] = $invoice['disbursal_batch_id'] ?? null;
        $disbursalData['tran_id'] = $invoice['tran_id'] ?? null;
        
        $disbursalData['bank_account_id'] = ($invoice['supplier']['is_buyer'] == 2) ? $invoice['supplier']['anchor_bank_details']['bank_account_id'] : $invoice['supplier_bank_detail']['bank_account_id'];
        $disbursalData['bank_name'] = ($invoice['supplier']['is_buyer'] == 2) ? $invoice['supplier']['anchor_bank_details']['bank']['bank_name'] : $invoice['supplier_bank_detail']['bank']['bank_name'] ;
        $disbursalData['ifsc_code'] = ($invoice['supplier']['is_buyer'] == 2) ? $invoice['supplier']['anchor_bank_details']['ifsc_code'] : $invoice['supplier_bank_detail']['ifsc_code'];
        $disbursalData['acc_no'] = ($invoice['supplier']['is_buyer'] == 2) ? $invoice['supplier']['anchor_bank_details']['acc_no'] : $invoice['supplier_bank_detail']['acc_no'];            
        $disbursalData['virtual_acc_id'] = $invoice['lms_user']['virtual_acc_id'] ?? null;
       
        $disbursalData['status_id'] = ($disburseType == 2) ? 12 : 10;
        $disbursalData['disburse_type'] = $disburseType;
       
        $disbursalData['funded_date'] = ($disburseType == 2) ? \Carbon\Carbon::now()->format('Y-m-d h:i:s') : null;
        $curData = \Carbon\Carbon::now()->format('Y-m-d h:i:s');
        $disbursalData['status_update_time'] = $curData;
                        
        $disbursalData['created_by'] = Auth::user()->user_id;
        $disbursalData['created_at'] = $curData;
        return $disbursalData;
    }

    /**
     * Prepare Disbursal Data
     * 
     * @param array $data
     * @return mixed
     */
    protected function createTransactionData($userId = null, $data = 0, $transType = 0, $entryType = 0)
    {
        /**
        * disburseType = 1 for online and 2 for manually
        */

        
        $soaFlag = 0;
        if(isset($data['soa_flag'])){
            $soaFlag = $data['soa_flag'];
        }else{
            $soaFlag = in_array($transType,[10,35]) ? 0 : 1;
        }

        $transactionData = [];

        $transactionData['parent_trans_id'] = $data['parent_trans_id'] ?? null;
        $transactionData['invoice_disbursed_id'] = $data['invoice_disbursed_id'] ?? null;
        $transactionData['user_id'] = $userId ?? null;
        $transactionData['trans_date'] = (!empty($data['trans_date'])) ? date("Y-m-d h:i:s", strtotime(str_replace('/','-',$data['trans_date']))) : \Carbon\Carbon::now()->format('Y-m-d h:i:s');
        $transactionData['trans_type'] = $transType ?? 0;
        $transactionData['amount'] = $data['amount'] ?? 0;
        $transactionData['entry_type'] =  $entryType ?? 0;
        $transactionData['gst'] = $data['gst'] ?? 0;
        $transactionData['cgst'] = $data['cgst'] ?? 0;            
        $transactionData['sgst'] = $data['sgst'] ?? 0;
        $transactionData['igst'] = $data['igst'] ?? 0;
        $transactionData['tds_per'] = null;
        $transactionData['gl_flag'] = 1;
        $transactionData['soa_flag'] = $soaFlag;
        $transactionData['trans_by'] = 2;
        $transactionData['pay_from'] = ($transType == 16) ? 3 : $this->appRepo->getUserTypeByUserId($userId);
        $transactionData['is_settled'] = 0;
        $transactionData['is_posted_in_tally'] = 0;

        $curData = \Carbon\Carbon::now()->format('Y-m-d h:i:s');
                        
        $transactionData['created_by'] = Auth::user()->user_id;
        $transactionData['created_at'] = $curData;
        return $transactionData;
    }

    /**
     * Prepare Disbursal Data
     * 
     * @param array $data
     * @return mixed
     */
    protected function createBatchFileData($file = null)
    {
        /**
        * disburseType = 1 for online and 2 for manually
        */
        $data = [];

        $data['file_path'] = $file['file_path'] ?? '';
        $data['file_type'] = $file['file_type'] ?? '';
        $data['file_name'] = $file['file_name'] ?? '';
        $data['file_size'] = $file['file_size'] ?? '';
        $data['file_encp_key'] =  !empty($path) ? md5(basename($path)) : md5('2');
        $data['created_by'] = 1;
        $data['updated_by'] = 1;
        
        return $data;
    }    
    /**
     * Get Overdue Interest
     * 
     * @param integer $invoice_id
     * @return float
     */
    protected function getOverDueInterest($invoice_id)
    {   
        $invDisbData = InvoiceDisbursed::where('invoice_id','=',$invoice_id)->first();
        if (!$invDisbData) return null;
        
        $invoice_disbursed_id = $invDisbData->invoice_disbursed_id;
        
        $monthlyIntCond = [];
        $monthlyIntCond['invoice_disbursed_id'] = $invoice_disbursed_id;
        $monthlyIntCond['overdue_interest_rate_not_null'] = '1';
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
    
    protected function getRefundData($transId, $includeTrans=true)
    {   
        $data = [];
        $result = $this->lmsRepo->getRefundData($transId);        
        foreach($result as $row) {
            if ($row->name == 'MARGIN') {
                $data[$row->name][] = $row;
            } else {
                $data[$row->name] = $row;
            }
        }
        
        if ($includeTrans) {
            $repayment = $this->lmsRepo->getTransactions(['trans_id' => $transId, 'trans_type' => config('lms.TRANS_TYPE.REPAYMENT')])->first();
            $repaymentTrails = $this->lmsRepo->getTransactions(['payment_id' => $transId]);

            $transaction = [];
            $transactions = [];

            $transaction['TRANS_DATE'] = $repayment->trans_date;
            $transaction['VALUE_DATE'] = $repayment->created_at;

            if ($repayment->transType->chrg_master_id != '0') {
                $transaction['TRANS_TYPE'] = $repayment->transType->charge->chrg_name;
            } else {
                $transaction['TRANS_TYPE'] = $repayment->transType->trans_name;
            }

            if ($repayment->disbursal_id && $repayment->disburse && $repayment->disburse->invoice) {
                $transaction['INV_NO'] = $repayment->disburse->invoice->invoice_no;
            } else {
                $transaction['INV_NO'] = '';
            }      

            if ($repayment->entry_type == '0') {
                $transaction['DEBIT'] = $repayment->amount;
            } else {
                $transaction['DEBIT'] = '';
            }

            if ($repayment->entry_type == '1') {
                $transaction['CREDIT'] = $repayment->amount;
            } else {
                $transaction['CREDIT'] = '';
            }

            $transactions[] = $transaction;

            foreach ($repaymentTrails as $repay) {
                $transaction = [];
                
                
                $transaction['TRANS_DATE'] = $repay->trans_date;
                $transaction['VALUE_DATE'] = $repay->created_at;

                if ($repay->transType->chrg_master_id != '0') {
                    $transaction['TRANS_TYPE'] = $repay->transType->charge->chrg_name;
                } else {
                    $transaction['TRANS_TYPE'] = $repay->transType->trans_name;
                }

                if ($repay->disbursal_id && isset($repay->disburse->invoice) && $repay->disburse->invoice->invoice_no) {
                    $transaction['INV_NO'] = $repay->disburse->invoice->invoice_no;
                } else {
                    $transaction['INV_NO'] = '';
                }      

                if ($repay->entry_type == '0') {
                    $transaction['DEBIT'] = $repay->amount;
                } else {
                    $transaction['DEBIT'] = '';
                }

                if ($repay->entry_type == '1') {
                    $transaction['CREDIT'] = $repay->amount;
                } else {
                    $transaction['CREDIT'] = '';
                }

                $transactions[] = $transaction;   
            }

            $data['TRANSACTIONS'] = $transactions;
        }
        return $data;
    }
    
    protected function createApprRequest($requestType, $addlData=[]) 
    {
        $wf_stage_types = config('lms.REQUEST_TYPE');
        
        $assignRequest = false;        
        //$wf_stage_type = isset($wf_stage_types[$requestType]) ? $wf_stage_types[$requestType] : '';
        $wf_stage_type = $requestType;
        
        $reqData=[];
        $reqLogData=[];
        
        //Prepare Request Data        
        $reqData['req_type'] = $wf_stage_type;
        $reqData['status'] = config('lms.REQUEST_STATUS.NEW_REQUEST');        
        
        //Prepare Request Log Data
        $reqLogData['status'] = $reqData['status'];        
            
        if ($requestType == config('lms.REQUEST_TYPE.REFUND') ) {
            $reqData['trans_id'] = $addlData['trans_id'];
            $reqData['amount'] = $addlData['amount'];            
        }
        
        $saveReqData = $this->lmsRepo->saveApprRequestData($reqData);
        $req_id = $saveReqData->req_id;        
        $saveReqData = $this->lmsRepo->saveApprRequestData(['ref_code' => \Helpers::formatIdWithPrefix($req_id, $type='REFUND')], $req_id);
        $reqLogData['req_id'] = $req_id;
        
        if($reqData['trans_id'] && $req_id){
            RefundTransactions::saveRefundTransactions($reqData['trans_id'], $req_id);
        }

        $wf_stages = $this->lmsRepo->getWfStages($wf_stage_type);
        foreach($wf_stages as $wf_stage) {
            $wf_stage_code = $wf_stage->stage_code;
            $wf_order_no = $wf_stage->order_no;
            
            $wfData = $this->lmsRepo->getWfDetailById($wf_stage_code);
            if ($wfData) {
                $wfAppStageData = $this->lmsRepo->getRequestWfStage($wf_stage_code, $req_id);
                if (!$wfAppStageData) {
                    $arrData = [
                        'wf_stage_id' => $wfData->wf_stage_id,
                        'req_id' => $req_id,
                        'wf_status' => config('lms.WF_STAGE_STATUS.PENDING'),
                    ];
                    $this->lmsRepo->saveWfDetail($arrData);
                }
            }
            if ($wf_order_no == '0') {
                $result = $wf_stage;
                $assignRequest = true;
            }
        }
        
        if ($assignRequest) {
            
            $data = $result;
            $this->lmsRepo->updateRequestAssignById((int) $req_id, ['is_owner' => 0]);
            //update assign table
            $assignRequests=[];
            $allReqLogData=[];
            $assignRoles = explode(',', $data->assign_role);
            foreach($assignRoles as $role_id) {
                $assignedUsers = $this->lmsRepo->getBackendUsersByRoleId($role_id);
                if (count($assignedUsers) > 0) {
                    foreach($assignedUsers as $auser) {
                        $dataArr = [];
                        $dataArr['from_id'] = \Auth::user()->user_id;
                        $dataArr['to_id'] = $auser->user_id;
                        $dataArr['role_id'] = null;
                        $dataArr['req_id'] = $req_id;
                        $dataArr['assign_status'] = '0';
                        $dataArr['assign_type'] = '2';
                        $dataArr['sharing_comment'] = isset($addlData['sharing_comment']) ? $addlData['sharing_comment'] : '';
                        $dataArr['is_owner'] = 1;  
                        
                        $curData = \Carbon\Carbon::now()->format('Y-m-d h:i:s');
                        
                        $dataArr['created_by'] = Auth::user()->user_id;
                        $dataArr['created_at'] = $curData;
                        $dataArr['updated_by'] = Auth::user()->user_id;
                        $dataArr['updated_at'] = $curData;
                        
                        $assignRequests[] = $dataArr;
                        
                        //Save Request Log Data
                        $allReqLogData[] = $reqLogData + [
                            'comment' => isset($addlData['sharing_comment']) ? $addlData['sharing_comment'] : '',
                            'assigned_user_id' => $auser->user_id, 
                            'wf_stage_id' => $data->wf_stage_id,
                            'created_by' => \Auth::user()->user_id,
                            'created_at' => $curData,
                            'updated_by' => \Auth::user()->user_id,
                            'updated_at' => $curData               
                        ];
                    }
                } 
                /*else { 
                    $assignRequests['from_id'] = \Auth::user()->user_id;    
                    $assignRequests['req_id'] = $req_id;
                    $assignRequests['assign_status'] = '0';
                    $assignRequests['assign_type'] = '2';
                    $assignRequests['sharing_comment'] = isset($addlData['sharing_comment']) ? $addlData['sharing_comment'] : '';
                    $assignRequests['is_owner'] = 1;                
                    $assignRequests['to_id'] = null;
                    $assignRequests['role_id'] = $role->role_id;
                }*/
            }
            $this->lmsRepo->assignRequest($assignRequests);
            $this->lmsRepo->saveApprRequestLogData($allReqLogData);
            return $data;
        }
        
    }

    protected function finalRefundTransactions(int $trans_id, int $req_id)
    {
        $transactions = RefundTransactions::getRefundTransactions($req_id);
        $curData = \Carbon\Carbon::now()->format('Y-m-d h:i:s');
        foreach ($transactions as $key => $trans) {
            if($trans->req_amount>0){
                $refundData = $this->createTransactionData($trans->user_id, [
                    'amount' => $trans->req_amount,
                    'trans_date'=>$curData,
                    'disbursal_id'=>$trans->disbursal_id,
                    'soa_flag'=>1
                ], null, $trans->trans_type, 0);
                $trans_data =  Transactions::saveTransaction($refundData);
                if($trans_data){
                    $updateData = [
                        'new_trans_id'=> $trans_data->trans_id
                    ];
                    RefundTransactions::saveRefundTransactionData($updateData,['refund_trans_id'=>$trans->refund_trans_id]);
                }
            }
        }
    }

    protected function updateApprRequest($reqId, $addlData=[]) 
    {        
        $apprReqData = $this->lmsRepo->getApprRequestData($reqId);
        if(!$apprReqData) return false;
                
        $wf_stage_type = $apprReqData->req_type;
        $reqStatus = $addlData['status'];
                
        //Get Current workflow stage
        $wfStage = $this->lmsRepo->getCurrentWfStage($reqId);
        $wf_stage_code = $wfStage ? $wfStage->stage_code : '';
        $wf_stage_id = $wfStage ? $wfStage->wf_stage_id : '';
                        
        //Update Request Log Data
        $updateReqLogData = ['is_active' => '0'];
        $whereCond=[];
        $whereCond['req_id'] = $reqId;
        $whereCond['assigned_user_id'] = \Auth::user()->user_id;
        $whereCond['wf_stage_id'] = $wf_stage_id;
        $this->lmsRepo->updateApprRequestLogData($whereCond, $updateReqLogData);
                        
        //Insert Request Log Data
        $reqLogData=[];
        $reqLogData['req_id'] = $reqId;
        $reqLogData['status'] = $reqStatus;
        $reqLogData['comment'] = $addlData['sharing_comment'];
        $reqLogData['assigned_user_id'] = \Auth::user()->user_id;
        $reqLogData['wf_stage_id'] = $wf_stage_id;
        $this->lmsRepo->saveApprRequestLogData($reqLogData);
                             
        if (in_array($wf_stage_code, ['refund_approval', 'adjustment_approval']) && config('lms.REQUEST_STATUS.REFUND_QUEUE') != $reqStatus) {
            
            //Get Assigned Request for Approval
            $whereCond=[];
            $whereCond['req_id'] = $reqId;
            $assignedReqData = $this->lmsRepo->getAssignedReqData($whereCond);            
            $cntAssignedReqStatus = count($assignedReqData);
                                     
            //Get Request Log with Status for Approval
            $whereCond=[];
            $whereCond['req_id'] = $reqId;
            $whereCond['wf_stage_id'] = $wf_stage_id;
            $apprReqLogData = $this->lmsRepo->getApprRequestLogData($whereCond);
            $cntUpdatedReqStatus=count($apprReqLogData);            
            
            $cntApprReqStatus=0;
            foreach($apprReqLogData as $rLog) {
                if ($rLog->status == config('lms.REQUEST_STATUS.APPROVED')) {
                    $cntApprReqStatus++;
                }
            }
            if ($cntAssignedReqStatus == $cntUpdatedReqStatus) {
                $reqStatus = $cntUpdatedReqStatus == $cntApprReqStatus ? config('lms.REQUEST_STATUS.APPROVED') : config('lms.REQUEST_STATUS.REJECTED');

                //$wf_stage_status = config('lms.WF_STAGE_STATUS.COMPLETED');
            }
        }
        
        $updateReqData=[];
        $updateReqData['status'] = $reqStatus;
        $this->lmsRepo->saveApprRequestData($updateReqData, $reqId);
                
        $wf_stage_status = config('lms.REQUEST_STATUS.REFUND_QUEUE') == $reqStatus ? config('lms.WF_STAGE_STATUS.COMPLETED') : config('lms.WF_STAGE_STATUS.IN_PROGRESS');
        $updateWfStage=[];
        $updateWfStage['wf_status'] = $wf_stage_status;        
        $this->lmsRepo->updateWfStage($wf_stage_id, $reqId, $updateWfStage);
                       
        return true;
    }    
    
    protected function assignRequest($reqId, $wfStage, $reqStatus, $addlData)
    {
        
        $data = $wfStage;
        $this->lmsRepo->updateRequestAssignById((int) $reqId, ['is_owner' => 0]);
        //update assign table
        $assignRequests=[];
        $allReqLogData=[];
        $assignRoles = explode(',', $data->assign_role);
        
        $reqLogData=[];
        $reqLogData['req_id'] = $reqId;
        $reqLogData['status'] = $reqStatus;
        
        foreach($assignRoles as $role_id) {
            $assignedUsers = $this->lmsRepo->getBackendUsersByRoleId($role_id);
            if (count($assignedUsers) > 0) {
                foreach($assignedUsers as $auser) {
                    $dataArr = [];
                    $dataArr['from_id'] = \Auth::user()->user_id;
                    $dataArr['to_id'] = $auser->user_id;
                    $dataArr['role_id'] = null;
                    $dataArr['req_id'] = $reqId;
                    $dataArr['assign_status'] = '0';
                    $dataArr['assign_type'] = '2';
                    $dataArr['sharing_comment'] = isset($addlData['sharing_comment']) ? $addlData['sharing_comment'] : '';
                    $dataArr['is_owner'] = 1;
                    
                    $curData = \Carbon\Carbon::now()->format('Y-m-d h:i:s');

                    $dataArr['created_by'] = \Auth::user()->user_id;
                    $dataArr['created_at'] = $curData;
                    $dataArr['updated_by'] = \Auth::user()->user_id;
                    $dataArr['updated_at'] = $curData;
                        
                    $assignRequests[] = $dataArr;

                    //Save Request Log Data
                    $allReqLogData[] = $reqLogData + [
                        'comment' => isset($addlData['sharing_comment']) ? $addlData['sharing_comment'] : '',
                        'assigned_user_id' => $auser->user_id, 
                        'wf_stage_id' => $data->wf_stage_id,
                        'created_by' => \Auth::user()->user_id,
                        'created_at' => $curData,
                        'updated_by' => \Auth::user()->user_id,
                        'updated_at' => $curData                          
                    ];
                }
            } 
            /*else { 
                $assignRequests['from_id'] = \Auth::user()->user_id;    
                $assignRequests['req_id'] = $reqId;
                $assignRequests['assign_status'] = '0';
                $assignRequests['assign_type'] = '2';
                $assignRequests['sharing_comment'] = isset($addlData['sharing_comment']) ? $addlData['sharing_comment'] : '';
                $assignRequests['is_owner'] = 1;                
                $assignRequests['to_id'] = null;
                $assignRequests['role_id'] = $role->role_id;
            }*/
        }
        $this->lmsRepo->assignRequest($assignRequests);
        
        $updateReqLogData = ['is_active' => '0'];
        $whereCond=[];
        $whereCond['req_id'] = $reqId;
        //$whereCond['assigned_user_id'] = \Auth::user()->user_id;
        //$whereCond['wf_stage_id'] = $data->wf_stage_id;          
        $this->lmsRepo->updateApprRequestLogData($whereCond, $updateReqLogData);
        
        $this->lmsRepo->saveApprRequestLogData($allReqLogData);        
    }

    protected function moveRequestToNextStage($reqId, $addlData=[])
    {
        $apprReqData = $this->lmsRepo->getApprRequestData($reqId);
        if(!$apprReqData) return false;
                
        $wf_stage_type = $apprReqData->req_type;
        
        //Get Current workflow stage
        $curWfStage = $this->lmsRepo->getCurrentWfStage($reqId);
        if (!$curWfStage) return false;
                
        $cur_wf_stage_code = $curWfStage ? $curWfStage->stage_code : '';
        $cur_wf_stage_id = $curWfStage ? $curWfStage->wf_stage_id : '';
        $cur_wf_order_no = $curWfStage ? $curWfStage->order_no : '';
        
        $cur_wf_stage_status = config('lms.WF_STAGE_STATUS.COMPLETED');
        $updateWfStage=[];
        $updateWfStage['wf_status'] = $cur_wf_stage_status;        
        $this->lmsRepo->updateWfStage($cur_wf_stage_id, $reqId, $updateWfStage);
        
        //Get Next workflow stage
        $nextWfStage = $this->lmsRepo->getNextWfStage($wf_stage_type, $cur_wf_order_no);
        if (!$nextWfStage) return false;
                       
        $next_wf_stage_code = $nextWfStage ? $nextWfStage->stage_code : '';
        $next_wf_stage_id = $nextWfStage ? $nextWfStage->wf_stage_id : '';
        $next_wf_order_no = $nextWfStage ? $nextWfStage->order_no : '';
        
        $next_wf_stage_status = config('lms.WF_STAGE_STATUS.IN_PROGRESS');
        $updateWfStage=[];
        $updateWfStage['wf_status'] = $next_wf_stage_status;
        $this->lmsRepo->updateWfStage($next_wf_stage_id, $reqId, $updateWfStage);
        
        //Assign Request
        $reqStatus =  config('lms.REQUEST_STATUS.IN_PROCESS');
        $this->assignRequest($reqId, $nextWfStage, $reqStatus, $addlData);
        
        $updateReqData=[];
        $updateReqData['status'] = $reqStatus;
        $this->lmsRepo->saveApprRequestData($updateReqData, $reqId);
        
        return $nextWfStage;
    }
    
    protected function getRequestPrevStage($reqId)
    {
        $apprReqData = $this->lmsRepo->getApprRequestData($reqId);
        if(!$apprReqData) return false;
                
        $wf_stage_type = $apprReqData->req_type;
        
        //Get Current workflow stage
        $curWfStage = $this->lmsRepo->getCurrentWfStage($reqId);
        if (!$curWfStage) return false;
                
        $cur_wf_stage_code = $curWfStage ? $curWfStage->stage_code : '';
        $cur_wf_stage_id = $curWfStage ? $curWfStage->wf_stage_id : '';
        $cur_wf_order_no = $curWfStage ? $curWfStage->order_no : '';        
        
        //Get Previous workflow stage
        $prevWfStage = $this->lmsRepo->getPrevWfStage($wf_stage_type, $cur_wf_order_no);
        if (!$prevWfStage) return false;

        return $prevWfStage;
    }
    
    protected function getRequestNextStage($reqId)
    {
        $apprReqData = $this->lmsRepo->getApprRequestData($reqId);
        if(!$apprReqData) return false;
                
        $wf_stage_type = $apprReqData->req_type;
        
        //Get Current workflow stage
        $curWfStage = $this->lmsRepo->getCurrentWfStage($reqId);
        if (!$curWfStage) return false;
                
        $cur_wf_stage_code = $curWfStage ? $curWfStage->stage_code : '';
        $cur_wf_stage_id = $curWfStage ? $curWfStage->wf_stage_id : '';
        $cur_wf_order_no = $curWfStage ? $curWfStage->order_no : '';        
        
        //Get Previous workflow stage
        $nextWfStage = $this->lmsRepo->getNextWfStage($wf_stage_type, $cur_wf_order_no);
        if (!$nextWfStage) return false;

        return $nextWfStage;
    }    
    
    protected function moveRequestToPrevStage($reqId, $addlData=[])
    {
        $apprReqData = $this->lmsRepo->getApprRequestData($reqId);
        if(!$apprReqData) return false;
                
        $wf_stage_type = $apprReqData->req_type;
        
        //Get Current workflow stage
        $curWfStage = $this->lmsRepo->getCurrentWfStage($reqId);
        if (!$curWfStage) return false;
                
        $cur_wf_stage_code = $curWfStage ? $curWfStage->stage_code : '';
        $cur_wf_stage_id = $curWfStage ? $curWfStage->wf_stage_id : '';
        $cur_wf_order_no = $curWfStage ? $curWfStage->order_no : '';        
        
        //Get Previous workflow stage
        $prevWfStage = $this->lmsRepo->getPrevWfStage($wf_stage_type, $cur_wf_order_no);
        if (!$prevWfStage) return false;
        
        $prev_wf_stage_code = $prevWfStage ? $prevWfStage->stage_code : '';
        $prev_wf_stage_id = $prevWfStage ? $prevWfStage->wf_stage_id : '';
        $prev_wf_order_no = $prevWfStage ? $prevWfStage->order_no : '';
        
        for ($wf_order_no=$prev_wf_order_no;$wf_order_no<=$cur_wf_order_no;$wf_order_no++) {
            $wf_stage_status = config('lms.WF_STAGE_STATUS.IN_PROGRESS');
            
            $wfStage = $this->lmsRepo->getWfDetailByOrderNo($wf_stage_type, $wf_order_no);
            if ($wfStage) {
                $wf_stage_id = $wfStage->wf_stage_id;
                $updateWfStage=[];
                $updateWfStage['wf_status'] = $wf_stage_status;
                $this->lmsRepo->updateWfStage($wf_stage_id, $reqId, $updateWfStage);
            }
        }
        
        //Assign Request
        $reqStatus =  config('lms.REQUEST_STATUS.NEW_REQUEST');
        $this->assignRequest($reqId, $prevWfStage, $reqStatus, $addlData);
        
        $updateReqData=[];
        $updateReqData['status'] = $reqStatus;
        $this->lmsRepo->saveApprRequestData($updateReqData, $reqId);
        
        return $prevWfStage;
    }
    
    protected function calculateRefund($transId)
    {
        $repayment = $this->lmsRepo->getTransactions(['trans_id'=>$transId,'trans_type'=>config('lms.TRANS_TYPE.REPAYMENT')])->first();
        $repaymentTrails = $this->lmsRepo->getTransactions(['payment_id'=>$transId]);

        $repayDebitTotal = Transactions::where('payment_id','=',$transId)
        ->where('entry_type','=','0')
        ->sum('amount');
        
        $repayCreditTotal = Transactions::where('payment_id','=',$transId)
        ->where('entry_type','=','1')
        ->sum('amount');
        
        $interestRefundTotal = Transactions::where('payment_id','=',$transId)
        ->where('trans_type','=',config('lms.TRANS_TYPE.INTEREST_REFUND'))
        ->sum('amount');

        $interestRefundSettledTotal = Transactions::where('payment_id','=',$transId)
        ->where('trans_type','=',config('lms.TRANS_TYPE.INTEREST_REFUND'))
        ->sum('settled_amount');

        $interestOverdueTotal = Transactions::where('payment_id','=',$transId)
        ->where('trans_type','=',config('lms.TRANS_TYPE.INTEREST_OVERDUE'))
        ->sum('amount');
        
        $marginTotal = Transactions::where('payment_id','=',$transId)
        ->where('trans_type','=',config('lms.TRANS_TYPE.MARGIN'))
        ->sum('amount');
        
        $nonFactoredAmount = Transactions::where('payment_id','=',$transId)
        ->where('trans_type','=',config('lms.TRANS_TYPE.NON_FACTORED_AMT'))
        ->sum('amount');
        if($nonFactoredAmount==0){
            $nonFactoredAmount = ($repayment->amount)-($repayDebitTotal+$marginTotal-$interestRefundSettledTotal);
        }
        
        $refundableAmount = $nonFactoredAmount+$marginTotal+$interestRefundTotal-$interestRefundSettledTotal;

        return ['repaymentTrails' => $repaymentTrails, 
        'repayment'=>$repayment,
        'nonFactoredAmount' => $nonFactoredAmount,
        'interestRefund'=>$interestRefundTotal-$interestRefundSettledTotal,
        'interestOverdue'=>$interestOverdueTotal,
        'marginTotal'=>$marginTotal,
        'refundableAmount'=>$refundableAmount,
        'transId' => $transId
        ];
    }
    
    protected function saveRefundData($transId, $refundData=[])
    {
        if (empty($refundData)) {
            $refundData = $this->calculateRefund($transId);
        }
        
        $saveRefundData=[];
        $variables = $this->lmsRepo->getVariables();
        foreach($variables as $variable) {
            if (isset($refundData[$variable->name])) {
                $refund=[];
                if ($variable->name == 'MARGIN') {
                    foreach($refundData[$variable->name] as $key => $item) {
                        $refund['trans_id'] = $transId;
                        $refund['variable_id'] = $variable->id;                        
                        $refund['variable_type'] = $item['MARGIN_TYPE'];
                        $refund['variable_value'] = $item['MARGIN_PER_OR_FIXED'];
                        $refund['amount'] = $item['MARGIN_AMOUNT'];
                        
                        $curData = \Carbon\Carbon::now()->format('Y-m-d h:i:s');

                        $refund['created_by'] = \Auth::user()->user_id;
                        $refund['created_at'] = $curData;
                        $refund['updated_by'] = \Auth::user()->user_id;
                        $refund['updated_at'] = $curData;
                        
                        $saveRefundData[] = $refund;
                    }
                } else {
                    $refund['trans_id'] = $transId;
                    $refund['variable_id'] = $variable->id;                    
                    $refund['variable_type'] = null;
                    $refund['variable_value'] = null;
                    $refund['amount'] = $refundData[$variable->name];
                    
                    $curData = \Carbon\Carbon::now()->format('Y-m-d h:i:s');
                    
                    $refund['created_by'] = \Auth::user()->user_id;
                    $refund['created_at'] = $curData;
                    $refund['updated_by'] = \Auth::user()->user_id;
                    $refund['updated_at'] = $curData;
                    
                    $saveRefundData[] = $refund;                        
                }                
            }
        }
        
        $result = $this->lmsRepo->saveRefundData($saveRefundData);
        return $result;
    }
    
}
