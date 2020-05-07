<?php

namespace App\Helpers;
use DB;
use Carbon\Carbon;
use Dompdf\Helpers;
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

    Private function setMonthlyInterestSoaFlag($invDisbId, $intAccrualDate, $invdueDate, $soaFlag){
        TransactionsRunning::where('invoice_disbursed_id','=',$invDisbId)
        ->where('trans_type','=',config('lms.TRANS_TYPE.INTEREST'))
        ->where('entry_type','=',0)
        ->where('soa_flag','=',0)
        ->whereDate('trans_date','<=',$intAccrualDate)
        ->where(\DB::raw('MONTH(trans_date)'),'<',date('m', strtotime($intAccrualDate)))
        ->update(['soa_flag'=>$soaFlag]);
        
        TransactionsRunning::where('invoice_disbursed_id','=',$invDisbId)
        ->where('trans_type','=',config('lms.TRANS_TYPE.INTEREST'))
        ->where('entry_type','=',0)
        ->where('soa_flag','=',0)
        ->whereDate('trans_date','=',$this->subDays($invdueDate,1))
        ->update(['soa_flag'=>$soaFlag]);
    }

    Private function setRearendInterestSoaFlag($invDisbId, $intAccrualDate, $invdueDate, $soaFlag){
        TransactionsRunning::where('invoice_disbursed_id','=',$invDisbId)
        ->where('trans_type','=',config('lms.TRANS_TYPE.INTEREST'))
        ->where('entry_type','=',0)
        ->where('soa_flag','=',0)
        ->whereDate('trans_date','=',$this->subDays($invdueDate,1))
        ->update(['soa_flag'=>$soaFlag]);
    }

    private function runningToTransPosting($invDisbId, $intAccrualDt, $payFreq, $invdueDate, $odStartDate){
        $intAccrualDate = $this->subDays($intAccrualDt,1);
        $graceStartDate = $this->addDays($invdueDate,1);
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
        if(strtotime($odStartDate) == strtotime($intAccrualDate)){
            $interestList = Transactions::where('invoice_disbursed_id','=',$invDisbId)
            ->where('trans_type','=',config('lms.TRANS_TYPE.INTEREST'))
            ->where('entry_type','=',0)
            ->whereNotNull('trans_running_id')
            ->whereBetween('trans_date',[$graceStartDate,$graceEndDate])
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
                    'trans_date' => $odStartDate,
                    'amount' => $trans->outstanding,
                    'entry_type' => 1,
                    'soa_flag' => 1,
                    'trans_type' => config('lms.TRANS_TYPE.CANCEL')
                    //'created_at' =>$intAccrualDate
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
                'trans_type' => $trans->trans_type,
                //'created_at' =>$intAccrualDate
            ];
        
        }
        if(!empty($transactionList)){
            foreach ($transactionList as $key => $newTrans) {
                $this->lmsRepo->saveTransaction($newTrans);
            }
        }
    }

    Private function setOverdueSoaFlag($invDisbId, $intAccrualDate, $soaFlag){
        TransactionsRunning::where('invoice_disbursed_id','=',$invDisbId)
        ->where('trans_type','=',config('lms.TRANS_TYPE.INTEREST_OVERDUE'))
        ->where('entry_type','=',0)
        ->where('soa_flag','=',0)
        ->whereDate('trans_date','<=',$intAccrualDate)
        ->where(\DB::raw('MONTH(trans_date)'),'<',date('m', strtotime($intAccrualDate)))
        ->update(['soa_flag'=>$soaFlag]);
    }

    private function getpaymentSettled($transDate, $invDisbId, $payFreq){
        $intrest = 0;
        if($payFreq == 2){
            $disbTransIds = Transactions::whereRaw("Date(trans_date) <=?",[$transDate]) 
            ->where('invoice_disbursed_id','=',$invDisbId) 
            ->whereNull('payment_id') 
            ->whereNull('link_trans_id') 
            ->whereNull('parent_trans_id')
            ->whereIn('trans_type',[config('lms.TRANS_TYPE.PAYMENT_DISBURSED')]) 
            ->pluck('trans_id')->toArray();
        
            $intTransIds2 = Transactions::whereRaw("Month(trans_date) <?",[date('m', strtotime($transDate))]) 
            ->where('invoice_disbursed_id','=',$invDisbId) 
            ->whereNull('payment_id') 
            ->whereNull('link_trans_id') 
            ->whereNull('parent_trans_id')
            ->whereIn('trans_type',[config('lms.TRANS_TYPE.INTEREST')]) 
            ->pluck('trans_id')->toArray();

            $transIds = array_merge($disbTransIds,$intTransIds2);
        }
        else{
            $transIds = Transactions::whereRaw("Date(trans_date) <=?",[$transDate]) 
            ->where('invoice_disbursed_id','=',$invDisbId) 
            ->whereNull('payment_id') 
            ->whereNull('link_trans_id') 
            ->whereNull('parent_trans_id')
            ->whereIn('trans_type',[config('lms.TRANS_TYPE.PAYMENT_DISBURSED')]) 
            ->pluck('trans_id')->toArray();
        }
        
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
            ->update(['amount'=>0]);
        

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

    private function interestPosting($invDisbId, $userId, $payFreq, $transDate){
        $interests = new Collection();
        
        //Monthly Case
        if($payFreq == '2'){
            $interests = InterestAccrual::select(\DB::raw("sum(accrued_interest) as totalInt,max(interest_date) as interestDate"))
            ->where('invoice_disbursed_id','=',$invDisbId)
            ->whereNull('overdue_interest_rate')
            ->whereDate('interest_date', '<=', $transDate)
            ->groupByRaw('YEAR(interest_date), MONTH(interest_date)')
            ->get();
        }

        //Rear End Case
        elseif($payFreq == '3'){
            $interests = InterestAccrual::select(\DB::raw("sum(accrued_interest) as totalInt, max(interest_date) as interestDate"))
            ->where('invoice_disbursed_id','=',$invDisbId)
            ->whereNull('overdue_interest_rate')
            ->whereDate('interest_date', '<=', $transDate)
            ->groupBy('invoice_disbursed_id')
            ->get();
        }

        foreach ($interests as $interest) {
           if($payFreq == 3){
               $transId = TransactionsRunning::where('invoice_disbursed_id','=',$invDisbId)
               ->where('trans_type','=',config('lms.TRANS_TYPE.INTEREST'))
               ->where('entry_type','=',0)
               //->where('is_posted','=',0)
               ->whereDate('trans_date',$interest->interestDate)
               ->value('trans_running_id');
            }
            elseif($payFreq == 2){
                $transId = TransactionsRunning::where('invoice_disbursed_id','=',$invDisbId)
                ->where('trans_type','=',config('lms.TRANS_TYPE.INTEREST'))
                ->where('entry_type','=',0)
                //->where('is_posted','=',0)
                ->whereMonth('trans_date', date('m', strtotime($interest->interestDate)))
                ->value('trans_running_id');
                
                TransactionsRunning::where('invoice_disbursed_id','=',$invDisbId)
                ->where('trans_type','=',config('lms.TRANS_TYPE.INTEREST'))
                ->where('entry_type','=',0)
                ->where(\DB::raw('MONTH(trans_date)'),'>',date('m', strtotime($interest->interestDate)))
                ->update(['amount'=>0]);
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
        while(strtotime($gEndDate) >= strtotime($gStartDate)){
            $balancePrincipal = $this->getpaymentSettled($gStartDate, $invDisbId, $payFreq);
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
             
            if (is_null($invDisbDetail->int_accrual_start_dt)) {
                throw new InvalidArgumentException('Interest Accrual Start Date is missing for invoice Disbursed Id: ' . $invDisbId);
            }

            if (is_null($invDisbDetail->payment_due_date)) {
                throw new InvalidArgumentException('Payment Date is missing for invoice Disbursed Id: ' . $invDisbId);
            }
            
            while(strtotime($curdate) > strtotime($loopStratDate)){
                $balancePrincipal = $this->getpaymentSettled($loopStratDate, $invDisbId, $payFreq);
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
                
                if(strtotime($loopStratDate) <= strtotime($odStartDate))
                $this->interestPosting($invDisbId, $userId, $payFreq, $loopStratDate);
                
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
        $curdate = \Carbon\Carbon::now()->setTimezone(config('common.timezone'))->format('Y-m-d');
        $invoiceList = $this->lmsRepo->getUnsettledInvoices([]);
        foreach ($invoiceList as $invId => $trans) {
            $this->intAccrual($invId);
        }
    }
}