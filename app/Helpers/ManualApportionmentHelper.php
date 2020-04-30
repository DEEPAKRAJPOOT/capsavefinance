<?php

namespace App\Helpers;
use Carbon\Carbon;
use Dompdf\Helpers;
use Illuminate\Support\Collection;
use DB;
use Illuminate\Support\Facades\Auth;
use App\Inv\Repositories\Models\Lms\Disbursal;
use App\Inv\Repositories\Models\Lms\Transactions;
use App\Inv\Repositories\Models\Lms\InterestAccrual;
use App\Inv\Repositories\Models\Lms\InvoiceDisbursed;

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

    private function getpaymentSettled($transDate, $invDisbId){
        $transIds = Transactions::whereRaw("Date(trans_date) <=?",[$transDate]) 
        ->where('invoice_disbursed_id','=',$invDisbId) 
        ->whereNull('payment_id') 
        ->whereNull('link_trans_id') 
        ->whereNull('parent_trans_id')
        ->whereIn('trans_type',[config('lms.TRANS_TYPE.PAYMENT_DISBURSED')]) 
        ->pluck('trans_id')->toArray();
        
        $transIds2 = Transactions::whereRaw("Date(trans_date) <?",[$transDate]) 
        ->where('invoice_disbursed_id','=',$invDisbId) 
        ->whereNull('payment_id') 
        ->whereNull('link_trans_id') 
        ->whereNull('parent_trans_id')
        ->whereIn('trans_type',[config('lms.TRANS_TYPE.INTEREST')]) 
        ->pluck('trans_id')->toArray();
        $transIds = array_merge($transIds,$transIds2);

        $Dr = Transactions::whereRaw("Date(trans_date) <=?",[$transDate])
        ->where('invoice_disbursed_id','=',$invDisbId)
        ->where('entry_type','=','0')
        ->where(function($query) use($transIds){
            $query->whereIn('trans_id',$transIds);
            $query->OrwhereIn('parent_trans_id',$transIds);
        })
        ->sum('amount');
    
        $Cr =  Transactions::whereRaw("Date(trans_date) <=?",[$transDate])
        ->where('invoice_disbursed_id','=',$invDisbId)
        ->where('entry_type','=','1')
        ->where(function($query) use($transIds){
            $query->whereIn('trans_id',$transIds);
            $query->OrwhereIn('parent_trans_id',$transIds);
        })
        ->sum('amount');

        return $Dr-$Cr;
    }

    private function updateGracePeriodInt($invDisbId, $gStartDate, $gEndDate, $odIntRate){
        
        while(strtotime($gEndDate) >= strtotime($gStartDate)){
            $balancePrincipal = $this->getpaymentSettled($gStartDate, $invDisbId);
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
            $intAccrualData['created_at'] = \Carbon\Carbon::now(config('common.timezone')   )->format('Y-m-d h:i:s');
            $intAccrualData['created_by'] = Auth::user()->user_id;

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

    private function monthlyIntPosting($invDisbId, $userId){
        return InterestAccrual:://select('*')
        select(\DB::raw("sum(accrued_interest) as totalInt,max(interest_date) as interestDate"))
        ->where('invoice_disbursed_id','=',$invDisbId)
        ->whereNull('overdue_interest_rate')
        ->groupByRaw('YEAR(interest_date), MONTH(interest_date)')
        ->get();
    }

    private function rearEndIntPosting($invDisbId,$userId){
        return InterestAccrual:://select('*')
        select(\DB::raw("sum(accrued_interest) as totalInt, max(interest_date) as interestDate"))
        ->where('invoice_disbursed_id','=',$invDisbId)
        ->whereNull('overdue_interest_rate')
        ->groupBy('invoice_disbursed_id')
        ->get();
    }

    private function overDueIntPosting($invDisbId,$userId){
        return InterestAccrual:://select('*')
        select(\DB::raw("sum(accrued_interest) as totalInt, max(interest_date) as interestDate"))
        ->where('invoice_disbursed_id','=',$invDisbId)
        ->whereNotNull('overdue_interest_rate')
        ->groupBy('invoice_disbursed_id')
        ->get();
    }

    private function overDuePosting($invDisbId,$userId){
        $interests = $this->overDueIntPosting($invDisbId, $userId);

        foreach ($interests as $interest) {
            $transId = Transactions::where('invoice_disbursed_id','=',$invDisbId)
            ->where('trans_type','=',config('lms.TRANS_TYPE.INTEREST_OVERDUE'))
            ->where('entry_type','=',0)
            ->value('trans_id');
            
            if($transId){
                $whereCond = ['trans_id' => $transId];
                $intTransData = [
                    'invoice_disbursed_id' => $invDisbId,
                    'user_id' => $userId,
                    'trans_date' => $interest->interestDate,
                    'amount' => $interest->totalInt,
                    'entry_type' => 0,
                    'trans_type' => config('lms.TRANS_TYPE.INTEREST_OVERDUE')
                ];
                $this->lmsRepo->saveTransaction($intTransData,$whereCond);
            }else{
                $intTransData = [
                    'invoice_disbursed_id' => $invDisbId,
                    'user_id' => $userId,
                    'trans_date' => $interest->interestDate,
                    'amount' => $interest->totalInt,
                    'entry_type' => 0,
                    'trans_type' => config('lms.TRANS_TYPE.INTEREST_OVERDUE')
                ];
                $this->lmsRepo->saveTransaction($intTransData);
            }
        }
    }

    private function interestPosting($invDisbId, $userId, $payFreq){
        $interests = new Collection();
        
        //Monthly Case
        if($payFreq == '2'){
            $interests = $this->monthlyIntPosting($invDisbId, $userId);
        }

        //Rear End Case
        elseif($payFreq == '3'){
            $interests = $this->rearEndIntPosting($invDisbId, $userId);
        }

        foreach ($interests as $interest) {
            $transId = Transactions::where('invoice_disbursed_id','=',$invDisbId)
            ->where('trans_type','=',config('lms.TRANS_TYPE.INTEREST'))
            ->where('entry_type','=',0)
            ->whereDate('trans_date',$interest->interestDate)
            ->value('trans_id');
            
            if($transId){
                $whereCond = ['trans_id' => $transId];
                $intTransData = [
                    'payment_id' => null,
                    'parent_trans_id' => null,
                    'invoice_disbursed_id' => $invDisbId,
                    'user_id' => $userId,
                    'trans_date' => $interest->interestDate,
                    'amount' => $interest->totalInt,
                    'entry_type' => 0,
                    'trans_type' => config('lms.TRANS_TYPE.INTEREST')
                ];
                $this->lmsRepo->saveTransaction($intTransData,$whereCond);
            }else{
                $intTransData = [
                    'payment_id' => null,
                    'parent_trans_id' => null,
                    'invoice_disbursed_id' => $invDisbId,
                    'user_id' => $userId,
                    'trans_date' => $interest->interestDate,
                    'amount' => $interest->totalInt,
                    'entry_type' => 0,
                    'trans_type' => config('lms.TRANS_TYPE.INTEREST')
                ];
                $this->lmsRepo->saveTransaction($intTransData);
            }
        }
    }

    public function intAccrual(int $invDisbId, $startDate = null){
        try{
            $curdate = \Carbon\Carbon::now()->setTimezone(config('common.timezone'))->format('Y-m-d');
            
            $invDisbDetail = InvoiceDisbursed::find($invDisbId);
            $offerDetails = $invDisbDetail->invoice->program_offer;
            $userId = $invDisbDetail->disbursal->user_id;
            $intRate = $invDisbDetail->interest_rate;
            $odIntRate = $invDisbDetail->overdue_interest_rate;
            $gPeriod = $invDisbDetail->grace_period;
            $tDays = $invDisbDetail->tenor_days;
            $payFreq = $offerDetails->payment_frequency;
            
            $intAccrualStartDate = $invDisbDetail->int_accrual_start_dt;
            $invDueDate =  $invDisbDetail->inv_due_date;
            $payDueDate = $invDisbDetail->payment_due_date;
            $gStartDate = ($gPeriod>0)?$this->addDays($payDueDate,1):$payDueDate;
            $gEndDate = $this->addDays($payDueDate,$gPeriod);
            $odStartDate = $this->addDays($gEndDate,1);
            $maxAccrualDate = $invDisbDetail->interests->max('interest_date');
            
            $currentIntRate = $intRate;
            $intType = 1;
            
            $loopStratDate = $startDate ?? $intAccrualStartDate;
             
            while(strtotime($curdate) > strtotime($loopStratDate)){
                $balancePrincipal = $this->getpaymentSettled($loopStratDate, $invDisbId);
                if($balancePrincipal > 0){
                    if(strtotime($loopStratDate) === strtotime($odStartDate)){
                        $currentIntRate = $odIntRate;
                        $intType = 2;
                        $this->updateGracePeriodInt($invDisbId, $gStartDate, $gEndDate, $odIntRate);
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
                    $intAccrualData['created_at'] = \Carbon\Carbon::now(config('common.timezone'))->format('Y-m-d h:i:s');
                    $intAccrualData['created_by'] = Auth::user()->user_id;
                    
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
                    break;
                }
                $loopStratDate = $this->addDays($loopStratDate,1);
            }
            $this->interestPosting($invDisbId, $userId, $payFreq);
            $this->overDuePosting($invDisbId, $userId);
        } catch (Exception $ex) {
            return Helpers::getExceptionMessage($ex);
       } 
    }
    
    public function dailyIntAccrual(){
        $curdate = \Carbon\Carbon::now()->setTimezone(config('common.timezone'))->format('Y-m-d');
        $invoiceList = $this->lmsRepo->getUnsettledInvoices([]);
        foreach ($invoiceList as $trans) {
            $this->intAccrual($trans->invoice_disbursed_id);
        }
    }
}