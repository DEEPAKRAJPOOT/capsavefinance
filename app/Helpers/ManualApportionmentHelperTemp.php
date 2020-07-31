<?php

namespace App\Helpers;
use DB;
use Helpers;
use Carbon\Carbon;
use InvalidArgumentException;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use App\Inv\Repositories\Models\Payment;
use App\Inv\Repositories\Models\Lms\Disbursal;
use App\Inv\Repositories\Models\Master\BaseRate;
use App\Inv\Repositories\Models\Lms\Transactions;
use App\Inv\Repositories\Models\Lms\InvoiceDisbursed;
use App\Inv\Repositories\Models\Lms\InterestAccrualTemp;

class ManualApportionmentHelperTemp{
    
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
        
            $intTransIds = Transactions::whereMonth('trans_date','<=',date('m', strtotime($odStartDate)))
            ->whereYear('trans_date',date('Y', strtotime($odStartDate)))
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

       if($intTransIds){
           $Dr += Transactions::whereDate('trans_date','<=',$odStartDate)
           ->where('invoice_disbursed_id','=',$invDisbId)
           ->where('entry_type','=','0')
           ->where(function($query) use($intTransIds){
               $query->whereIn('trans_id',$intTransIds);
               $query->OrwhereIn('parent_trans_id',$intTransIds);
            })
            ->sum('amount');
        }
            
        $Cr =  Transactions::whereDate('trans_date','<=',$transDate) 
        ->where('invoice_disbursed_id','=',$invDisbId)
        ->where('entry_type','=','1')
        ->where(function($query) use($disbTransIds){
            $query->whereIn('trans_id',$disbTransIds);
            $query->OrwhereIn('parent_trans_id',$disbTransIds);
        })
        ->sum('amount');

        if($intTransIds){
            $Cr +=  Transactions::whereDate('trans_date','<=',$odStartDate) 
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
    
    private function updateGracePeriodInt($invDisbId, $gStartDate, $gEndDate, $odIntRate, $payFreq, $userId, $paymentId){
        $odStartDate = $gStartDate;
        while(strtotime($gEndDate) >= strtotime($gStartDate)){
            $balancePrincipal = $this->getpaymentSettled($gStartDate, $invDisbId, $payFreq, $odStartDate);
            $interestAmt = round($this->calInterest($balancePrincipal, $odIntRate, 1),config('lms.DECIMAL_TYPE.AMOUNT'));
            
            $interest_accrual_temp_id = InterestAccrualTemp::whereDate('interest_date',$gStartDate)
            ->where('invoice_disbursed_id','=',$invDisbId)
            ->value('interest_accrual_temp_id');

            $intAccrualData = [];
            $intAccrualData['payment_id'] = $paymentId;
            $intAccrualData['invoice_disbursed_id'] = $invDisbId;
            $intAccrualData['interest_date'] = $gStartDate;
            $intAccrualData['principal_amount'] = $balancePrincipal;
            $intAccrualData['accrued_interest'] = $interestAmt;
            $intAccrualData['interest_rate'] = null;
            $intAccrualData['overdue_interest_rate'] = $odIntRate;

            if($interest_accrual_temp_id){
                $recalwhereCond = [];
                $recalwhereCond['interest_accrual_temp_id'] = $interest_accrual_temp_id;
                $this->lmsRepo->saveInterestAccrualTemp($intAccrualData,$recalwhereCond);
            }else{
                $this->lmsRepo->saveInterestAccrualTemp($intAccrualData);
            }
            $gStartDate = $this->addDays($gStartDate,1);
        }
    }

    public function intAccrual(int $invDisbId, $startDate = null, $endDate = null, $paymentId){
        try{
            if($endDate){
                $curdate = $endDate;     
            }else{
                $curdate =  Helpers::getSysStartDate();
            }
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
            $maxAccrualDate = $invDisbDetail->interestTemp->max('interest_date');
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
                            $this->updateGracePeriodInt($invDisbId, $gStartDate, $gEndDate, $odIntRate, $payFreq, $userId, $paymentId);
                        }
                    }
                    $interestAmt = round($this->calInterest($balancePrincipal, $currentIntRate, 1),config('lms.DECIMAL_TYPE.AMOUNT'));
                    
                    $interest_accrual_temp_id = InterestAccrualTemp::whereDate('interest_date',$loopStratDate)
                    ->where('invoice_disbursed_id','=',$invDisbId)
                    ->value('interest_accrual_temp_id');
                    
                    $intAccrualData = [];
                    $intAccrualData['payment_id'] = $paymentId;
                    $intAccrualData['invoice_disbursed_id'] = $invDisbId;
                    $intAccrualData['interest_date'] = $loopStratDate;
                    $intAccrualData['principal_amount'] = $balancePrincipal;
                    $intAccrualData['accrued_interest'] = $interestAmt;
                    $intAccrualData['interest_rate'] = ($intType==1)?$intRate:null;
                    $intAccrualData['overdue_interest_rate'] = ($intType==2)?$odIntRate:null;
                    
                    if($interest_accrual_temp_id){
                        $recalwhereCond = [];
                        $recalwhereCond['interest_accrual_temp_id'] = $interest_accrual_temp_id;
                        $this->lmsRepo->saveInterestAccrualTemp($intAccrualData,$recalwhereCond);
                    }else{
                        $this->lmsRepo->saveInterestAccrualTemp($intAccrualData);
                    }
                    
                }else{
                    InterestAccrualTemp::where('invoice_disbursed_id','=',$invDisbId)
                    ->where('interest_date','>=',$loopStratDate)
                    ->delete();
                }
               
                $loopStratDate = $this->addDays($loopStratDate,1);
                
                $endOfMonthDate = Carbon::createFromFormat('Y-m-d', $loopStratDate)->endOfMonth()->format('Y-m-d');

            }
        } catch (Exception $ex) {
            return Helpers::getExceptionMessage($ex);
       } 
    }
    
    public function setTempInterest($paymentId){
        if($paymentId){
            $payment = Payment::find($paymentId);
            if($payment){
                $paymentDate = $payment->date_of_payment;
                $userId = $payment->user_id;
                $invoiceList = $this->lmsRepo->getUnsettledInvoices(['noNPAUser'=>true, 'intAccrualStartDateLteSysDate'=>true, 'user_id'=>$userId]);
                foreach ($invoiceList as $invId => $trans) {
                    $this->intAccrual($invId, null, $paymentDate, $paymentId);
                }
            }
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
}