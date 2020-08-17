<?php

namespace App\Helpers;
use DB;
use Carbon\Carbon;
use Helpers;
use InvalidArgumentException;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use App\Inv\Repositories\Models\Lms\Disbursal;
use App\Inv\Repositories\Models\Lms\Transactions;
use App\Inv\Repositories\Models\Lms\InterestAccrual;
use App\Inv\Repositories\Models\Lms\InvoiceDisbursed;
use App\Inv\Repositories\Models\Lms\TransactionsRunning;

class ManualApportionmentHelper{
    
    public function __construct($lms_repo){
		$this->lmsRepo = $lms_repo;
    }

    private function calInterest($principalAmt, $interestRate, $tenorDays){
        $interest = $principalAmt * $tenorDays * ($interestRate / 360) ;                
        return $interest/100;        
    }  
    
    private function addDays($currentDate, $noOfDays){
        $calDate = date('Y-m-d', strtotime($currentDate . "+ $noOfDays days"));
        return $calDate;
    }

    private function subDays($currentDate, $noOfDays){
        $calDate = date('Y-m-d', strtotime($currentDate . "- $noOfDays days"));
        return $calDate;
    }

    public function transactionPostingAdjustment($invDisbId, $startDate, $payFreq, $paymentId = null){
        $transactionList = [];
        $amount = 0;

        $runningTransactions = TransactionsRunning::where('invoice_disbursed_id','=',$invDisbId)->get();

        foreach($runningTransactions as $rTrans){
            $amount = $rTrans->amount;
            $transactions = Transactions::where('invoice_disbursed_id','=',$invDisbId)
            ->where('trans_running_id','=',$rTrans->trans_running_id)
            ->where('entry_type','=',0)
            ->whereNotNull('trans_running_id')
            ->get();

            foreach($transactions as $trans){
                $amount -= $trans->amount;
                $cTransactions = Transactions::where('link_trans_id','=',$trans->trans_id)
                ->where('invoice_disbursed_id','=',$invDisbId)
                ->where('trans_running_id','=',$rTrans->trans_running_id)
                ->where('entry_type','=',1)
                ->where('trans_type','=',config('lms.TRANS_TYPE.CANCEL'))
                ->whereNotNull('trans_running_id')
                ->get();

                foreach($cTransactions as $cTrans){
                    $amount += $cTrans->amount;
                }
            }

            if($amount > 0){
                /*$ICPTransactions = Transactions::where('invoice_disbursed_id','=',$invDisbId)
                ->where('trans_running_id','=',$rTrans->trans_running_id)
                ->where('entry_type','=',0)
                ->whereNull('payment_id')  
                ->whereNull('link_trans_id')
                ->whereNull('parent_trans_id')
                ->get()
                ->filter(function($item){
                    return $item->outstanding > 0;
                });
                $transactionList[] = [
                    'payment_id' => null,
                    'link_trans_id' => null,
                    'parent_trans_id' => null,
                    'trans_running_id'=> $rTrans->trans_running_id,
                    'invoice_disbursed_id' => $rTrans->invoice_disbursed_id,
                    'user_id' => $rTrans->user_id,
                    'trans_date' => $rTrans->trans_date,
                    'amount' => $amount,
                    'entry_type' => 1,
                    'soa_flag' => 1,
                    'trans_type' => config('lms.TRANS_TYPE.INTEREST')
                ];*/
            }
            
            elseif($amount < 0){
                $amount = abs($amount);
                // Interest cancelation Process
                if($amount > 0){
                    $ICPTransactions = Transactions::where('invoice_disbursed_id','=',$invDisbId)
                    ->where('trans_running_id','=',$rTrans->trans_running_id)
                    ->where('entry_type','=',0)
                    ->whereNull('payment_id')  
                    ->whereNull('link_trans_id')
                    ->whereNull('parent_trans_id')
                    ->get()
                    ->filter(function($item){
                        return $item->outstanding > 0;
                    });
                    
                    foreach ($ICPTransactions as $icpTrans) {
                        if($amount >= $icpTrans->outstanding){
                            $cAmt = $icpTrans->outstanding;
                        }else{
                            $cAmt = $amount;
                        }
                        $amount -= $cAmt;
                        
                        if($amount > 0){
                            $transactionList[] = [
                                'payment_id' => null,
                                'link_trans_id' => $icpTrans->link_trans_id,
                                'parent_trans_id' => $icpTrans->parent_trans_id,
                                'trans_running_id'=> $icpTrans->trans_running_id,
                                'invoice_disbursed_id' => $icpTrans->invoice_disbursed_id,
                                'user_id' => $icpTrans->user_id,
                                'trans_date' => $icpTrans->trans_date,
                                'amount' => $cAmt,
                                'entry_type' => 1,
                                'soa_flag' => 1,
                                'trans_type' => config('lms.TRANS_TYPE.CANCEL')
                            ];
                        }
                    }
                }
                // Interest Refund Process
                
                if($amount > 0){
                    $IRPTransactions = Transactions::where('invoice_disbursed_id','=',$invDisbId)
                    ->where('trans_running_id','=',$rTrans->trans_running_id)
                    ->where('entry_type','=',0)
                    ->whereNull('payment_id')  
                    ->whereNull('link_trans_id')
                    ->whereNull('parent_trans_id')
                    ->get()
                    ->filter(function($item){
                        return ($item->amount - $item->outstanding) > 0;
                    });

                    foreach ($IRPTransactions as $irpTrans) {
                        $irpAmt = $irpTrans->amount - $irpTrans->outstanding;
                        if($amount >= $irpAmt){
                            $rAmt = $irpAmt;
                        }else{
                            $rAmt = $amount;
                        }
                        $amount -= $rAmt;
                        
                        if($amount > 0){
                            $transactionList[] = [
                                'payment_id' => null,
                                'link_trans_id' => $irpTrans->link_trans_id,
                                'parent_trans_id' => $irpTrans->parent_trans_id,
                                'trans_running_id'=> $irpTrans->trans_running_id,
                                'invoice_disbursed_id' => $irpTrans->invoice_disbursed_id,
                                'user_id' => $irpTrans->user_id,
                                'trans_date' => $irpTrans->trans_date,
                                'amount' => $rAmt,
                                'entry_type' => 1,
                                'soa_flag' => 1,
                                'trans_type' => config('lms.TRANS_TYPE.REFUND')
                            ];
                        }
                    }
                }   
            }
        }

        if(!empty($transactionList)){
            foreach ($transactionList as $key => $newTrans) {
                $this->lmsRepo->saveTransaction($newTrans);
            }
        }
    }

    private function runningToTransPosting($invDisbId, $intAccrualDt, $payFreq, $invdueDate, $odStartDate){
        $intAccrualDate = $this->subDays($intAccrualDt,1);
        $invdueDate = $this->subDays($invdueDate,1);
        $graceStartDate = $invdueDate;
        $graceEndDate = $this->subDays($odStartDate,1);
        $endOfMonthDate = Carbon::createFromFormat('Y-m-d', $intAccrualDate)->endOfMonth()->format('Y-m-d');
        $intTransactions = new collection();
        $odTransactions = new collection();
        $transactions = new collection();
        $transactionList = [];
        // Interest Posting
        if($payFreq == 2){

            if( (strtotime($endOfMonthDate) == strtotime($intAccrualDate) || strtotime($invdueDate) == strtotime($intAccrualDate)) 
            && strtotime($intAccrualDate) < strtotime($odStartDate)){

                $intTransactions = TransactionsRunning::where('invoice_disbursed_id','=',$invDisbId)
                ->where('trans_type','=',config('lms.TRANS_TYPE.INTEREST'))
                ->where('entry_type','=',0)
                ->whereDate('trans_date','<=',$intAccrualDate)
                ->where(function($query)use($invdueDate,$intAccrualDt){
                    $query->whereMonth('trans_date','<',date('m', strtotime($intAccrualDt)));
                    $query->OrwhereDate('trans_date','=',$invdueDate);
                })
                ->get()
                ->filter(function($item){
                    return $item->outstanding > 0;
                });
            }

        }
        elseif($payFreq == 3){
            
            if( strtotime($invdueDate) == strtotime($intAccrualDate) && strtotime($intAccrualDate) <= strtotime($invdueDate)){

                $intTransactions = TransactionsRunning::where('invoice_disbursed_id','=',$invDisbId)
                ->where('trans_type','=',config('lms.TRANS_TYPE.INTEREST'))
                ->where('entry_type','=',0)
                ->whereDate('trans_date','<=',"$intAccrualDate")
                ->whereDate('trans_date','=',"$invdueDate")
                ->get()
                ->filter(function($item){
                    return $item->outstanding > 0;
                });
            }
        }
        
        //Roll back interest

        if($payFreq == 2 && strtotime($odStartDate) == strtotime($intAccrualDate)){
            $interestList = Transactions::where('invoice_disbursed_id','=',$invDisbId)
            ->where('trans_type','=',config('lms.TRANS_TYPE.INTEREST'))
            ->where('entry_type','=',0)
            ->whereNotNull('trans_running_id')
            ->whereDate('trans_date','>',$graceStartDate)
            ->whereDate('trans_date','<=',$graceEndDate)
            ->get();
            foreach ($interestList as $trans) {
                $canceledAmt = Transactions::where('parent_trans_id','=',$trans->trans_id)
                ->where('trans_type','=',config('lms.TRANS_TYPE.CANCEL'))
                ->where('entry_type','=',1)
                ->sum('amount');
                if($trans->outstanding == $trans->amount && $canceledAmt == 0)
                $transactionList[] = [
                    'payment_id' => null,
                    'link_trans_id' => $trans->trans_id,
                    'parent_trans_id' => $trans->trans_id,
                    'trans_running_id'=> null,
                    'invoice_disbursed_id' => $trans->invoice_disbursed_id,
                    'user_id' => $trans->user_id,
                    'trans_date' => $graceEndDate,
                    'amount' => $trans->outstanding,
                    'entry_type' => 1,
                    'soa_flag' => 1,
                    'trans_type' => config('lms.TRANS_TYPE.CANCEL')
                ];
            }
        }

        //Overdue Posting
        if( (strtotime($endOfMonthDate) == strtotime($intAccrualDate) || strtotime($intAccrualDate) == strtotime($odStartDate)) && strtotime($intAccrualDate) >= strtotime($odStartDate)){

            $odTransactions = TransactionsRunning::where('invoice_disbursed_id','=',$invDisbId)
            ->where('trans_type','=',config('lms.TRANS_TYPE.INTEREST_OVERDUE'))
            ->where('entry_type','=',0)
            ->whereDate('trans_date','<=',$intAccrualDate)
            ->where(\DB::raw('MONTH(trans_date)'),'<',date('m', strtotime($intAccrualDt)))
            ->get()
            ->filter(function($item){
                return $item->outstanding > 0;
            });
        }

        $transactions = $intTransactions->merge($odTransactions); 

        foreach ($transactions as $key => $trans) {
            $transactionList[] = [
                'payment_id' => null,
                'link_trans_id' => null,
                'parent_trans_id' => null,
                'trans_running_id'=> $trans->trans_running_id,
                'invoice_disbursed_id' => $trans->invoice_disbursed_id,
                'user_id' => $trans->user_id,
                'trans_date' => $trans->trans_date,
                'amount' => $trans->outstanding,
                'entry_type' => $trans->entry_type,
                'soa_flag' => 1,
                'trans_type' => $trans->trans_type
            ];
        
        }
        if(!empty($transactionList)){
            foreach ($transactionList as $key => $newTrans) {
                $this->lmsRepo->saveTransaction($newTrans);
            }
        }
    }

    private function getpaymentSettled($transDate, $invDisbId, $payFreq, $odStartDate){
        $intrest = 0;
        $disbTransIds = null;
        $intTransIds = null;

        if($payFreq == 2){
            $disbTransIds = Transactions::whereDate('trans_date','<=',$odStartDate) 
            ->where('invoice_disbursed_id','=',$invDisbId) 
            ->whereNull('payment_id') 
            ->whereNull('link_trans_id') 
            ->whereNull('parent_trans_id')
            ->whereIn('trans_type',[config('lms.TRANS_TYPE.PAYMENT_DISBURSED')]) 
            ->pluck('trans_id')->toArray();
        
            $intTransIds = Transactions::whereDate('trans_date','<=',$odStartDate)
            ->where('invoice_disbursed_id','=',$invDisbId) 
            ->whereNull('payment_id') 
            ->whereNull('link_trans_id') 
            ->whereNull('parent_trans_id')
            ->whereIn('trans_type',[config('lms.TRANS_TYPE.INTEREST')]) 
            ->pluck('trans_id')->toArray();
        }
        else{
            $disbTransIds = Transactions::whereDate('trans_date','<=',$odStartDate) 
            ->where('invoice_disbursed_id','=',$invDisbId) 
            ->whereNull('payment_id') 
            ->whereNull('link_trans_id') 
            ->whereNull('parent_trans_id')
            ->whereIn('trans_type',[config('lms.TRANS_TYPE.PAYMENT_DISBURSED')]) 
            ->pluck('trans_id')->toArray();
        }
        
        $Dr = Transactions::whereDate('trans_date','<=',$transDate)
        ->where('invoice_disbursed_id','=',$invDisbId)
        ->where('entry_type','=','0')
        ->where(function($query) use($disbTransIds){
            $query->whereIn('trans_id',$disbTransIds);
            $query->OrwhereIn('parent_trans_id',$disbTransIds);
        })
        ->sum('amount');

        $Cr =  Transactions::whereDate('trans_date','<=',$transDate) 
        ->where('invoice_disbursed_id','=',$invDisbId)
        ->where('entry_type','=','1')
        ->where(function($query) use($disbTransIds){
            $query->whereIn('trans_id',$disbTransIds);
            $query->OrwhereIn('parent_trans_id',$disbTransIds);
        })
        ->sum('amount');

       if($intTransIds){
           $Dr += Transactions::whereDate('trans_date','<=',$transDate)
           ->where('invoice_disbursed_id','=',$invDisbId)
           ->where('entry_type','=','0')
           ->where(function($query) use($intTransIds){
               $query->whereIn('trans_id',$intTransIds);
               $query->OrwhereIn('parent_trans_id',$intTransIds);
            })
            ->sum('amount');
 
            $Cr +=  Transactions::whereDate('trans_date','<=',$transDate) 
            ->where('invoice_disbursed_id','=',$invDisbId)
            ->where('entry_type','=','1')
            ->where(function($query) use($intTransIds){
                $query->whereIn('trans_id',$intTransIds);
                $query->OrwhereIn('parent_trans_id',$intTransIds);
            })
            ->sum('amount');
        }

        return $Dr-$Cr;
    }
    
    private function overDuePosting($invDisbId, $userId){
        $overdues = InterestAccrual::select(\DB::raw("sum(accrued_interest) as totalInt,max(interest_date) as interestDate"))
        ->where('invoice_disbursed_id','=',$invDisbId)
        ->whereNull('interest_rate')
        ->groupByRaw('YEAR(interest_date), MONTH(interest_date)')
        ->get();

        foreach ($overdues as $odue) {
            $transRunningId = TransactionsRunning::where('invoice_disbursed_id','=',$invDisbId)
            ->where('trans_type','=',config('lms.TRANS_TYPE.INTEREST_OVERDUE'))
            ->where('entry_type','=',0)
            //->where('is_posted','=',0)
            ->whereMonth('trans_date', date('m', strtotime($odue->interestDate)))
            ->value('trans_running_id');
            
            TransactionsRunning::where('invoice_disbursed_id','=',$invDisbId)
            ->where('trans_type','=',config('lms.TRANS_TYPE.INTEREST_OVERDUE'))
            ->where('entry_type','=',0)
            ->where(\DB::raw('MONTH(trans_date)'),'>',date('m', strtotime($odue->interestDate)))
            ->update(['amount'=>0,'sys_updated_at' => Helpers::getSysStartDate()]);
        

            if($transRunningId){
                $whereCond = ['trans_running_id' => $transRunningId];
                $intTransData = [
                    'invoice_disbursed_id' => $invDisbId,
                    'user_id' => $userId,
                    'trans_date' => $odue->interestDate,
                    'amount' => $odue->totalInt,
                    'entry_type' => 0,
                    'trans_type' => config('lms.TRANS_TYPE.INTEREST_OVERDUE')
                ];
                $this->lmsRepo->saveTransactionRunning($intTransData,$whereCond);
            }else{
                $intTransData = [
                    'invoice_disbursed_id' => $invDisbId,
                    'user_id' => $userId,
                    'trans_date' => $odue->interestDate,
                    'amount' => $odue->totalInt,
                    'entry_type' => 0,
                    'trans_type' => config('lms.TRANS_TYPE.INTEREST_OVERDUE')
                ];
                $this->lmsRepo->saveTransactionRunning($intTransData);
            }
        }
    }

    private function interestPosting($invDisbId, $userId, $payFreq, $transDate, $gStartDate, $gEndDate){
        $interests = new Collection();
        
        $interestData = InterestAccrual::select(\DB::raw("sum(accrued_interest) as totalInt,max(interest_date) as interestDate"))
                        ->where('invoice_disbursed_id','=',$invDisbId)
                        ->whereNull('overdue_interest_rate')
                        ->whereDate('interest_date', '<=', $transDate);
    
        //Upfront
        if($payFreq == 1){
            $interests = $interestData->whereDate('interest_date', '>=', $gStartDate)->get();
        }
        
        //Monthly
        elseif($payFreq == 2){
            $interests = $interestData->groupByRaw('YEAR(interest_date), MONTH(interest_date)')->get();
        }

        //Rear End Case
        elseif($payFreq == 3){
            $interests = $interestData->get();
        }

        foreach ($interests as $interest) {
            if($payFreq == 2){
                $transId = TransactionsRunning::where('invoice_disbursed_id','=',$invDisbId)
                ->where('trans_type','=',config('lms.TRANS_TYPE.INTEREST'))
                ->where('entry_type','=',0)
                ->whereMonth('trans_date', date('m', strtotime($interest->interestDate)))
                ->value('trans_running_id');
                
                TransactionsRunning::where('invoice_disbursed_id','=',$invDisbId)
                ->where('trans_type','=',config('lms.TRANS_TYPE.INTEREST'))
                ->where('entry_type','=',0)
                ->whereMonth('trans_date','>', date('m', strtotime($interest->interestDate)))
                ->update(['amount'=>0,'sys_updated_at' => Helpers::getSysStartDate()]);
            }

            //Rear End Case
            elseif($payFreq == 3 || $payFreq == 1){
               $transId = TransactionsRunning::where('invoice_disbursed_id','=',$invDisbId)
               ->where('trans_type','=',config('lms.TRANS_TYPE.INTEREST'))
               ->where('entry_type','=',0)
               ->value('trans_running_id');
            }
          
            if($transId){
                $whereCond = ['trans_running_id' => $transId];
                $intTransData = [
                    'invoice_disbursed_id' => $invDisbId,
                    'user_id' => $userId,
                    'trans_date' => $interest->interestDate,
                    'amount' => $interest->totalInt,
                    'entry_type' => 0,
                    'trans_type' => config('lms.TRANS_TYPE.INTEREST')
                ];
                $this->lmsRepo->saveTransactionRunning($intTransData,$whereCond);
            }else{
                $intTransData = [
                    'invoice_disbursed_id' => $invDisbId,
                    'user_id' => $userId,
                    'trans_date' => $interest->interestDate,
                    'amount' => $interest->totalInt,
                    'entry_type' => 0,
                    'trans_type' => config('lms.TRANS_TYPE.INTEREST')
                ];
                $this->lmsRepo->saveTransactionRunning($intTransData);
            }
        }
    }
    
    private function updateGracePeriodInt($invDisbId, $gStartDate, $gEndDate, $odIntRate, $payFreq, $userId){
        $odStartDate = $gStartDate;
        while(strtotime($gEndDate) >= strtotime($gStartDate)){
            $balancePrincipal = $this->getpaymentSettled($gStartDate, $invDisbId, $payFreq, $odStartDate);
            $interestAmt = round($this->calInterest($balancePrincipal, $odIntRate, 1),config('lms.DECIMAL_TYPE.AMOUNT'));
            
            $interest_accrual_id = InterestAccrual::whereDate('interest_date',$gStartDate)
            ->where('invoice_disbursed_id','=',$invDisbId)
            ->value('interest_accrual_id');

            $intAccrualData = [];
            $intAccrualData['invoice_disbursed_id'] = $invDisbId;
            $intAccrualData['interest_date'] = $gStartDate;
            $intAccrualData['principal_amount'] = $balancePrincipal;
            $intAccrualData['accrued_interest'] = $interestAmt;
            $intAccrualData['interest_rate'] = null;
            $intAccrualData['overdue_interest_rate'] = $odIntRate;

            if($interest_accrual_id){
                $recalwhereCond = [];
                $recalwhereCond['interest_accrual_id'] = $interest_accrual_id;
                $this->lmsRepo->saveInterestAccrual($intAccrualData,$recalwhereCond);
            }else{
                $this->lmsRepo->saveInterestAccrual($intAccrualData);
            }
            $gStartDate = $this->addDays($gStartDate,1);
        }
    }

    public function intAccrual(int $invDisbId, $startDate = null){
        try{
            $curdate =  Helpers::getSysStartDate();
            $curdate = Carbon::parse($curdate)->format('Y-m-d');
            
            $invDisbDetail = InvoiceDisbursed::find($invDisbId);
            $offerDetails = $invDisbDetail->invoice->program_offer;
            $userId = $invDisbDetail->disbursal->user_id;
            $funded_date = $invDisbDetail->disbursal->funded_date;
            $intRate = $invDisbDetail->interest_rate;
            $odIntRate = $invDisbDetail->overdue_interest_rate;
            $gPeriod = $invDisbDetail->grace_period;
            $tDays = $invDisbDetail->tenor_days;
            $tDays = $this->subDays($tDays,1);
            $payFreq = $offerDetails->payment_frequency;
            
            $intAccrualStartDate = $invDisbDetail->int_accrual_start_dt;
            $invDueDate =  $invDisbDetail->inv_due_date;
            $payDueDate = $invDisbDetail->payment_due_date;
            $gStartDate = $payDueDate;
            $gEndDate = $this->addDays($payDueDate,$gPeriod);
            $odStartDate = $this->addDays($gEndDate,1);
            $maxAccrualDate = $invDisbDetail->interests->max('interest_date');
            if($maxAccrualDate){
                $maxAccrualDate = $this->addDays($maxAccrualDate,1);
            } 
            $intType = 1;
            
            $loopStratDate = $startDate ?? $maxAccrualDate ?? $intAccrualStartDate;
             
            if (is_null($invDisbDetail->int_accrual_start_dt)) {
                throw new InvalidArgumentException('Interest Accrual Start Date is missing for invoice Disbursed Id: ' . $invDisbId);
            }

            if (is_null($invDisbDetail->payment_due_date)) {
                throw new InvalidArgumentException('Payment Date is missing for invoice Disbursed Id: ' . $invDisbId);
            }
            
            $oldIntRate = $offerDetails->interest_rate - $offerDetails->base_rate;
            $bankRatesArr = $this->getBankBaseRates($offerDetails->bank_id);//if $bankRatesArr value is false then follow the old process. otherwise call the below function to get the actual interest rate based on base rate.

            while(strtotime($curdate) > strtotime($loopStratDate)){
                if($bankRatesArr){
                    if(isset($payFreq) && $payFreq == 1){
                        $intRate = $this->getIntRate($oldIntRate, $bankRatesArr, strtotime($funded_date));//$str_to_time_date is the time at that point of time you want to get interest rate
                    }else{
                        $intRate = $this->getIntRate($oldIntRate, $bankRatesArr, strtotime($loopStratDate));//$str_to_time_date is the time at that point of time you want to get interest rate
                    }
                    $currentIntRate = $intRate;
                }else{
                    $currentIntRate = $intRate;
                }
                
                if(strtotime($loopStratDate) < strtotime($odStartDate)){
                    $balancePrincipal = $this->getpaymentSettled($loopStratDate, $invDisbId, $payFreq, $odStartDate);
                }else{
                    $balancePrincipal = $this->getpaymentSettled($loopStratDate, $invDisbId, $payFreq, $gStartDate);
                }

                if($balancePrincipal > 0){
                    if(strtotime($loopStratDate) >= strtotime($odStartDate)){
                        $currentIntRate = $odIntRate;
                        $intType = 2;
                        if(strtotime($loopStratDate) === strtotime($odStartDate)){
                            $this->updateGracePeriodInt($invDisbId, $gStartDate, $gEndDate, $odIntRate, $payFreq, $userId);
                        }
                    }
                    $interestAmt = round($this->calInterest($balancePrincipal, $currentIntRate, 1),config('lms.DECIMAL_TYPE.AMOUNT'));
                    
                    $interest_accrual_id = InterestAccrual::whereDate('interest_date',$loopStratDate)
                    ->where('invoice_disbursed_id','=',$invDisbId)
                    ->value('interest_accrual_id');
                    
                    $intAccrualData = [];
                    $intAccrualData['invoice_disbursed_id'] = $invDisbId;
                    $intAccrualData['interest_date'] = $loopStratDate;
                    $intAccrualData['principal_amount'] = $balancePrincipal;
                    $intAccrualData['accrued_interest'] = $interestAmt;
                    $intAccrualData['interest_rate'] = ($intType==1)?$intRate:null;
                    $intAccrualData['overdue_interest_rate'] = ($intType==2)?$odIntRate:null;
                    
                    if($interest_accrual_id){
                        $recalwhereCond = [];
                        $recalwhereCond['interest_accrual_id'] = $interest_accrual_id;
                        $this->lmsRepo->saveInterestAccrual($intAccrualData,$recalwhereCond);
                    }else{
                        $this->lmsRepo->saveInterestAccrual($intAccrualData);
                    }
                    
                }else{
                    InterestAccrual::where('invoice_disbursed_id','=',$invDisbId)
                    ->where('interest_date','>=',$loopStratDate)
                    ->delete();
                }
                
                if(strtotime($loopStratDate) <= strtotime($odStartDate))
                $this->interestPosting($invDisbId, $userId, $payFreq, $loopStratDate, $gStartDate, $gEndDate);
                
                if(strtotime($loopStratDate) >= strtotime($odStartDate))
                $this->overDuePosting($invDisbId, $userId);
                
                $loopStratDate = $this->addDays($loopStratDate,1);
                
                $endOfMonthDate = Carbon::createFromFormat('Y-m-d', $loopStratDate)->endOfMonth()->format('Y-m-d');

                $this->runningToTransPosting($invDisbId, $loopStratDate, $payFreq, $payDueDate, $odStartDate);
            }
        } catch (Exception $ex) {
            return Helpers::getExceptionMessage($ex);
       } 
    }
    
    public function dailyIntAccrual(){
        $cLogDetails = Helper::cronLogBegin(1);

        $curdate = Helpers::getSysStartDate();
        $invoiceList = $this->lmsRepo->getUnsettledInvoices(['noNPAUser'=>true, 'intAccrualStartDateLteSysDate'=>true]);
        foreach ($invoiceList as $invId => $trans) {
            $this->intAccrual($invId);
        }
        
        if($cLogDetails){
            Helper::cronLogEnd('1',$cLogDetails->cron_log_id);
        }
    }
    
    public function getBankBaseRates($bank_id, $date=null){
        if($bank_id){
            $base_rates = \App\Inv\Repositories\Models\Master\BaseRate::where(['bank_id'=> $bank_id, 'is_active'=> 1])->orderBy('id', 'DESC')->get();
            $br_array = [];
            foreach($base_rates as $key=>$base_rate){
                $temp = [
                            'start_date'=>strtotime(\Carbon\Carbon::createFromFormat('Y-m-d H:i:s', $base_rate['start_date'])->setTimezone(config('common.timezone'))->format('Y-m-d')),
                            'end_date'=>($base_rate['end_date'])? strtotime(\Carbon\Carbon::createFromFormat('Y-m-d H:i:s', $base_rate['end_date'])->setTimezone(config('common.timezone'))->format('Y-m-d')): '',
                            'base_rate'=>$base_rate['base_rate']
                        ];
                array_push($br_array, $temp);
            }
            return $br_array;
        }else{
            return false;
        }
    }

    public function getIntRate($oldIntRate, $bankRatesArr, $stt_date){
        $actIntRate = 0;
        foreach($bankRatesArr as $key=>$bankRateArr){
            if($stt_date >= $bankRateArr['start_date']){
                $actIntRate = $bankRateArr['base_rate'] + $oldIntRate;
                break;
            }else{
                continue;
            }
        }
        return $actIntRate;
    }

    private function sugOverDuePosting($invDisbId, $userId, $transDate){
        $overdues = InterestAccrual::select(\DB::raw("sum(accrued_interest) as totalInt,max(interest_date) as interestDate"))
        ->where('invoice_disbursed_id','=',$invDisbId)
        ->whereNull('interest_rate')
        ->whereDate('interest_date','<=',$transDate)
        ->groupByRaw('YEAR(interest_date), MONTH(interest_date)')
        ->get();

        foreach ($overdues as $odue) {
            $transRunningId = TransactionsRunning::where('invoice_disbursed_id','=',$invDisbId)
            ->where('trans_type','=',config('lms.TRANS_TYPE.INTEREST_OVERDUE'))
            ->where('entry_type','=',0)
            //->where('is_posted','=',0)
            ->whereMonth('trans_date', date('m', strtotime($odue->interestDate)))
            ->value('trans_running_id');
            
            TransactionsRunning::where('invoice_disbursed_id','=',$invDisbId)
            ->where('trans_type','=',config('lms.TRANS_TYPE.INTEREST_OVERDUE'))
            ->where('entry_type','=',0)
            ->where(\DB::raw('MONTH(trans_date)'),'>',date('m', strtotime($odue->interestDate)))
            ->update(['amount'=>0,'sys_updated_at' => Helpers::getSysStartDate()]);
        

            if($transRunningId){
                $whereCond = ['trans_running_id' => $transRunningId];
                $intTransData = [
                    'invoice_disbursed_id' => $invDisbId,
                    'user_id' => $userId,
                    'trans_date' => $odue->interestDate,
                    'amount' => $odue->totalInt,
                    'entry_type' => 0,
                    'trans_type' => config('lms.TRANS_TYPE.INTEREST_OVERDUE')
                ];
                $this->lmsRepo->saveTransactionRunning($intTransData,$whereCond);
            }else{
                $intTransData = [
                    'invoice_disbursed_id' => $invDisbId,
                    'user_id' => $userId,
                    'trans_date' => $odue->interestDate,
                    'amount' => $odue->totalInt,
                    'entry_type' => 0,
                    'trans_type' => config('lms.TRANS_TYPE.INTEREST_OVERDUE')
                ];
                $this->lmsRepo->saveTransactionRunning($intTransData);
            }
        }
    }

    private function sugInterestPosting($invDisbId, $userId, $payFreq, $transDate, $gStartDate, $gEndDate){
        $interests = new Collection();
        
        $interestData = InterestAccrual::select(\DB::raw("sum(accrued_interest) as totalInt,max(interest_date) as interestDate"))
                        ->where('invoice_disbursed_id','=',$invDisbId)
                        ->whereNull('overdue_interest_rate')
                        ->whereDate('interest_date', '<=', $transDate);
    
        //Upfront
        if($payFreq == 1){
            $interests = $interestData->whereDate('interest_date', '>=', $gStartDate)->get();
        }
        
        //Monthly
        elseif($payFreq == 2){
            $interests = $interestData->groupByRaw('YEAR(interest_date), MONTH(interest_date)')->get();
        }

        //Rear End Case
        elseif($payFreq == 3){
            $interests = $interestData->get();
        }

        foreach ($interests as $interest) {
            if($payFreq == 2){
                $transId = TransactionsRunning::where('invoice_disbursed_id','=',$invDisbId)
                ->where('trans_type','=',config('lms.TRANS_TYPE.INTEREST'))
                ->where('entry_type','=',0)
                ->whereMonth('trans_date', date('m', strtotime($interest->interestDate)))
                ->value('trans_running_id');
                
                TransactionsRunning::where('invoice_disbursed_id','=',$invDisbId)
                ->where('trans_type','=',config('lms.TRANS_TYPE.INTEREST'))
                ->where('entry_type','=',0)
                ->where(\DB::raw('MONTH(trans_date)'),'>',date('m', strtotime($interest->interestDate)))
                ->update(['amount'=>0,'sys_updated_at' => Helpers::getSysStartDate()]);
            }

            //Rear End Case
            elseif($payFreq == 3 || $payFreq == 1){
               $transId = TransactionsRunning::where('invoice_disbursed_id','=',$invDisbId)
               ->where('trans_type','=',config('lms.TRANS_TYPE.INTEREST'))
               ->where('entry_type','=',0)
               ->value('trans_running_id');
            }
          
            if($transId){
                $whereCond = ['trans_running_id' => $transId];
                $intTransData = [
                    'invoice_disbursed_id' => $invDisbId,
                    'user_id' => $userId,
                    'trans_date' => $interest->interestDate,
                    'amount' => $interest->totalInt,
                    'entry_type' => 0,
                    'trans_type' => config('lms.TRANS_TYPE.INTEREST')
                ];
                $this->lmsRepo->saveTransactionRunning($intTransData,$whereCond);
            }else{
                $intTransData = [
                    'invoice_disbursed_id' => $invDisbId,
                    'user_id' => $userId,
                    'trans_date' => $interest->interestDate,
                    'amount' => $interest->totalInt,
                    'entry_type' => 0,
                    'trans_type' => config('lms.TRANS_TYPE.INTEREST')
                ];
                $this->lmsRepo->saveTransactionRunning($intTransData);
            }
        }
    }

    public function getSugTransactions($payment_id){
        if($payment_id){
            $payment = Payment::find($payment_id);
            if($payment){
                $paymentDate = $payment->date_of_payment;
                $user_id = $payment->user_id;
                $invoiceList = InvoiceDisbursed::where('user_id',$user_id)->get();
                foreach ($invoiceList as $invId => $trans) {
                    $this->sugOverDuePosting($invDisbId, $userId);
                    $this->sugInterestPosting($invDisbId, $userId, $payFreq, $transDate, $gStartDate, $gEndDate);
                }
            }
        }
    }

    /*
    // Not in use
    Private function setMonthlyInterestSoaFlag($invDisbId, $intAccrualDate, $invdueDate, $soaFlag){
        TransactionsRunning::where('invoice_disbursed_id','=',$invDisbId)
        ->where('trans_type','=',config('lms.TRANS_TYPE.INTEREST'))
        ->where('entry_type','=',0)
        ->where('soa_flag','=',0)
        ->whereDate('trans_date','<=',$intAccrualDate)
        ->where(\DB::raw('MONTH(trans_date)'),'<',date('m', strtotime($intAccrualDate)))
        ->update(['soa_flag'=>$soaFlag,'sys_updated_at' => Helpers::getSysStartDate()]);
        
        TransactionsRunning::where('invoice_disbursed_id','=',$invDisbId)
        ->where('trans_type','=',config('lms.TRANS_TYPE.INTEREST'))
        ->where('entry_type','=',0)
        ->where('soa_flag','=',0)
        ->whereDate('trans_date','=',$this->subDays($invdueDate,1))
        ->update(['soa_flag'=>$soaFlag,'sys_updated_at' => Helpers::getSysStartDate()]);
    }

    //Not in use
    Private function setRearendInterestSoaFlag($invDisbId, $intAccrualDate, $invdueDate, $soaFlag){
        TransactionsRunning::where('invoice_disbursed_id','=',$invDisbId)
        ->where('trans_type','=',config('lms.TRANS_TYPE.INTEREST'))
        ->where('entry_type','=',0)
        ->where('soa_flag','=',0)
        ->whereDate('trans_date','=',$this->subDays($invdueDate,1))
        ->update(['soa_flag'=>$soaFlag,'sys_updated_at' => Helpers::getSysStartDate()]);
    }

    //Not in use
    Private function setOverdueSoaFlag($invDisbId, $intAccrualDate, $soaFlag){
        TransactionsRunning::where('invoice_disbursed_id','=',$invDisbId)
        ->where('trans_type','=',config('lms.TRANS_TYPE.INTEREST_OVERDUE'))
        ->where('entry_type','=',0)
        ->where('soa_flag','=',0)
        ->whereDate('trans_date','<=',$intAccrualDate)
        ->where(\DB::raw('MONTH(trans_date)'),'<',date('m', strtotime($intAccrualDate)))
        ->update(['soa_flag'=>$soaFlag,'sys_updated_at' => Helpers::getSysStartDate()]);
    }
    */
}