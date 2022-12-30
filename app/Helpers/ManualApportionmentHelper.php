<?php

namespace App\Helpers;
use DB;
use Helpers;
use Carbon\Carbon;
use App\Helpers\Helper;
use InvalidArgumentException;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use App\Inv\Repositories\Models\Payment;
use App\Inv\Repositories\Models\Master\Tds;
use App\Inv\Repositories\Models\Lms\Disbursal;
use App\Inv\Repositories\Models\Master\BaseRate;
use App\Inv\Repositories\Models\Lms\Transactions;
use App\Inv\Repositories\Models\Lms\Apportionment;
use App\Inv\Repositories\Models\Lms\InterestAccrual;
use App\Inv\Repositories\Models\Lms\InvoiceDisbursed;
use App\Inv\Repositories\Models\Lms\TransactionsRunning;
use App\Inv\Repositories\Models\Lms\InvoiceDisbursedDetail;

class ManualApportionmentHelper{
    
    public function __construct($lms_repo){
		$this->lmsRepo = $lms_repo;
    }

    private function calInterest($principalAmt, $interestRate, $tenorDays){
        $interest = round(($principalAmt * ($interestRate / config('common.DCC')))/100,2) ;                
        return $tenorDays * $interest;         
    }  
    
    private function addDays($currentDate, $noOfDays){
        $calDate = date('Y-m-d', strtotime($currentDate . "+ $noOfDays days"));
        return $calDate;
    }

    private function subDays($currentDate, $noOfDays){
        $calDate = date('Y-m-d', strtotime($currentDate . "- $noOfDays days"));
        return $calDate;
    }

    public function transactionPostingAdjustment($invDisbId, $apportionmentId = NULL){
        $transactionList = [];
        $amount = 0.00;

        $principalTrans = Transactions::where('invoice_disbursed_id','=',$invDisbId)
        ->where('trans_type',16)
        ->where('entry_type',0)
        ->whereNull('link_trans_id')
        ->whereNull('parent_trans_id')
        ->first();

        $invDisb = $principalTrans->invoiceDisbursed;
        $payFreq = $invDisb->invoice->program_offer->payment_frequency;
        $interest_borne_by = $invDisb->invoice->program_offer->program->interest_borne_by;
        $isRepayment = $invDisb->invoice->is_repayment;
        $paymentDueDate = $invDisb->payment_due_date;
        $disbursedDate = $invDisb->int_accrual_start_dt;

        $transactions = Transactions::where('invoice_disbursed_id','=',$invDisbId)
            ->where('entry_type','=',0)
            ->whereNull('link_trans_id')
            ->whereNull('parent_trans_id')
            ->whereIn('trans_type',[config('lms.TRANS_TYPE.INTEREST'),config('lms.TRANS_TYPE.INTEREST_OVERDUE')])
            ->get();
        
        $currentdate =  Helpers::getSysStartDate();
        $curdate = Carbon::parse($currentdate)->format('Y-m-d');
            
        foreach($transactions as $trans){
            $tdsRate = Tds::getActiveTdsBaseRate($trans->trans_date);

            $amount = round($trans->amount,2);
            $outstanding = ($trans->outstanding > 0.00)?$trans->outstanding:0.00;
            $refundFlag = true;
            $actualAmount = $trans->actInterest;
            $actualAmount = ($actualAmount > $amount) ? $amount : $actualAmount;
            if(is_null($trans->trans_running_id) && $payFreq == 1 /*&& strtotime($curdate) < strtotime($paymentDueDate)  && $interest_borne_by == '2'*/ && $principalTrans->outstanding > 0 ){
                if(strtotime($curdate) < strtotime($paymentDueDate)){
                    $actualAmount = $amount;
                    $refundFlag = false;
                }
            }
            
            $cTransactions = Transactions::where('link_trans_id','=',$trans->parent_trans_id ?? $trans->trans_id)
            ->where('invoice_disbursed_id','=',$invDisbId)
            ->where('entry_type','=',1)
            //->where('trans_running_id',$trans->trans_running_id)
            ->where('trans_type','=',config('lms.TRANS_TYPE.CANCEL'))
            ->where('settled_outstanding','>',0)
            ->orderBy('trans_id','desc')
            ->get();

            $cancelledAmount = $cTransactions->sum('settled_outstanding');
            $cancelableAmount = round(($amount - $actualAmount),2);
           
            if($cancelableAmount > $cancelledAmount){   
                $tt = round(($cancelableAmount - $cancelledAmount),2);
                if($tt > 0){
                    $transactionList[] = [
                        'payment_id' => null,
                        'link_trans_id' => $trans->trans_id,
                        'parent_trans_id' => $trans->parent_trans_id ?? $trans->trans_id,
                        'trans_running_id'=> $trans->trans_running_id,
                        'invoice_disbursed_id' => $trans->invoice_disbursed_id,
                        'user_id' => $trans->user_id,
                        'trans_date' => $currentdate,
                        'amount' => $tt,
                        'entry_type' => 1,
                        'soa_flag' => 1,
                        'trans_type' => config('lms.TRANS_TYPE.CANCEL'),
                        'apportionment_id' => $apportionmentId ?? null
                    ];
                }
            }elseif($cancelableAmount < $cancelledAmount){ 
                foreach($cTransactions as $cTranKey => $cTrans){
                    $cancelableAmount = round(($cancelableAmount - $cTrans->settled_outstanding),2);
                    if($cancelableAmount < 0){
                        $cTransactions->forget($cTranKey);
                        $transactionList[] = [
                            'payment_id' => NULL,
                            'link_trans_id' => $cTrans->trans_id,
                            'parent_trans_id' => $cTrans->parent_trans_id ?? $cTrans->trans_id,
                            'trans_running_id'=> $cTrans->trans_running_id,
                            'invoice_disbursed_id' => $cTrans->invoice_disbursed_id,
                            'user_id' => $cTrans->user_id,
                            'trans_date' => $currentdate,
                            'amount' => $cTrans->settled_outstanding,
                            'entry_type' => 0,
                            'soa_flag' => 1,
                            'trans_type' => config('lms.TRANS_TYPE.REVERSE'),
                            'apportionment_id' => $apportionmentId ?? null
                        ];
                        $cancelableAmount = round($cancelableAmount + $cTrans->settled_outstanding,2);
                    }
                }
                if($cancelableAmount < 0){
                    $transactionList[] = [
                        'payment_id' => null,
                        'link_trans_id' => $trans->trans_id,
                        'parent_trans_id' => $trans->parent_trans_id ?? $trans->trans_id,
                        'trans_running_id'=> $trans->trans_running_id,
                        'invoice_disbursed_id' => $trans->invoice_disbursed_id,
                        'user_id' => $trans->user_id,
                        'trans_date' => $currentdate,
                        'amount' => round(abs($cancelableAmount),2),
                        'entry_type' => 1,
                        'soa_flag' => 1,
                        'trans_type' => config('lms.TRANS_TYPE.CANCEL'),
                        'apportionment_id' => $apportionmentId ?? null
                    ];
                }
            }
            $actualAmount2 = $actualAmount;
            $paidTransactions = Transactions::where('parent_trans_id','=',$trans->trans_id)
                ->where('invoice_disbursed_id','=',$invDisbId)
                ->where('entry_type','=',1)
                ->whereIn('trans_type',[$trans->trans_type,7,36,37])
                ->orderBy('trans_id','asc')
                ->get();

            $refundTransactions = Transactions::where('parent_trans_id','=',$trans->trans_id)
                ->where('invoice_disbursed_id','=',$invDisbId)
                ->whereIn('trans_type',[config('lms.TRANS_TYPE.REFUND'),config('lms.TRANS_TYPE.ADJUSTMENT')])
                ->orderBy('trans_id','asc')
                ->get();
            $tdsReceivable = round($actualAmount*$tdsRate/100,2);
            foreach ($paidTransactions as $paidTrans) {
                if($paidTrans->trans_type == 7){
                    $tdsReceivable = round($tdsReceivable - $paidTrans->settled_outstanding,2);
                    if($tdsReceivable < 0){
                        $toBeRefundCnt = $refundTransactions->where('entry_type','=',1)->where('link_trans_id',$paidTrans->trans_id)->filter(function ($query){
                            return !($query->settled_outstanding == $query->amount);
                        })->count();
                        if(abs($tdsReceivable) && !$toBeRefundCnt){
                            $rfAmt =  abs($tdsReceivable); 
                            if($rfAmt > 0){
                                $transactionList[] = [
                                    'payment_id' => NULL,
                                    'link_trans_id' => $paidTrans->trans_id,
                                    'parent_trans_id' => $paidTrans->parent_trans_id ?? $paidTrans->trans_id,
                                    'trans_running_id'=> $paidTrans->trans_running_id,
                                    'invoice_disbursed_id' => $paidTrans->invoice_disbursed_id,
                                    'user_id' => $paidTrans->user_id,
                                    'trans_date' => $currentdate,
                                    'amount' => $rfAmt,
                                    'entry_type' => 0,
                                    'soa_flag' => $paidTrans->soa_flag,
                                    'trans_type' => config('lms.TRANS_TYPE.REVERSE'),
                                    'apportionment_id' => $apportionmentId ?? null
                                ];
                                $actualAmount2 = round(($actualAmount2 + $rfAmt),2);
                                $tdsReceivable = round(($tdsReceivable + $rfAmt),2);
                            }
                        }
                    }
                }
                
                $actualAmount2 = round(($actualAmount2 - $paidTrans->settled_outstanding),2);
                if($actualAmount2 > 0){
                    $rfTrans = $refundTransactions->where('entry_type','=',1)->where('link_trans_id',$paidTrans->trans_id)->where('settled_outstanding','>',0);
                    foreach ($rfTrans as $rtfKey => $rtf) {
                        if($rtf->settled_outstanding > 0){
                            $refundTransactions->forget($rtfKey);
                            $transactionList[] = [
                                'payment_id' => NULL,
                                'link_trans_id' => $rtf->trans_id,
                                'parent_trans_id' => $rtf->parent_trans_id ?? $rtf->trans_id,
                                'trans_running_id'=> $rtf->trans_running_id,
                                'invoice_disbursed_id' => $rtf->invoice_disbursed_id,
                                'user_id' => $rtf->user_id,
                                'trans_date' => $currentdate,
                                'amount' => $rtf->settled_outstanding,
                                'entry_type' => 0,
                                'soa_flag' => $rtf->soa_flag,
                                'trans_type' => config('lms.TRANS_TYPE.REVERSE'),
                                'apportionment_id' => $apportionmentId ?? null
                            ];
                        }else{
                            Log::info("$paidTrans->invoice_disbursed_id  $rtf->amount Overd Refund"); 
                        }
                    }
                }else{
                    $refundAmt = 0;
                    $toBeRefundAmt = $refundTransactions->where('entry_type','=',1)->where('link_trans_id',$paidTrans->trans_id)->sum('settled_outstanding');
                    $toBeRefundIds = $refundTransactions->where('entry_type','=',1)->where('link_trans_id',$paidTrans->trans_id)->pluck('trans_id')->toArray();
                    $refundedAmt = $refundTransactions->where('entry_type','=',0)->whereIn('link_trans_id',$toBeRefundIds)->sum('amount');

                    $refundAmt = $toBeRefundAmt + $refundedAmt;
                    if($refundAmt < abs($actualAmount2)){
                        $refRevAmt = abs($actualAmount2) < $paidTrans->settled_outstanding ? abs($actualAmount2) : $paidTrans->settled_outstanding;
                        if(in_array($paidTrans->trans_type, [$trans->trans_type,7])){
                            if($refundFlag){
                                $transactionList[] = [
                                    'payment_id' => NULL,
                                    'link_trans_id' => $paidTrans->trans_id,
                                    'parent_trans_id' => $paidTrans->parent_trans_id ?? $paidTrans->trans_id,
                                    'trans_running_id'=> $paidTrans->trans_running_id,
                                    'invoice_disbursed_id' => $paidTrans->invoice_disbursed_id,
                                    'user_id' => $paidTrans->user_id,
                                    'trans_date' => $currentdate,
                                    'amount' => $refRevAmt,
                                    'entry_type' => 1,
                                    'soa_flag' => 0,
                                    'trans_type' => config('lms.TRANS_TYPE.REFUND'),
                                    'apportionment_id' => $apportionmentId ?? null
                                ];
                                $actualAmount2 = round($actualAmount2 + $refRevAmt,2);
                            }
                        }elseif(in_array($paidTrans->trans_type, [36,37])){
                            $transactionList[] = [
                                'payment_id' => NULL,
                                'link_trans_id' => $paidTrans->trans_id,
                                'parent_trans_id' => $paidTrans->parent_trans_id ?? $paidTrans->trans_id,
                                'trans_running_id'=> $paidTrans->trans_running_id,
                                'invoice_disbursed_id' => $paidTrans->invoice_disbursed_id,
                                'user_id' => $paidTrans->user_id,
                                'trans_date' => $currentdate,
                                'amount' => $refRevAmt,
                                'entry_type' => 0,
                                'soa_flag' => $paidTrans->soa_flag,
                                'trans_type' => config('lms.TRANS_TYPE.REVERSE'),
                                'apportionment_id' => $apportionmentId ?? null
                            ];
                            $actualAmount2 = round($actualAmount2 + $refRevAmt,2);
                        }
                    }elseif($refundAmt > abs($actualAmount2)){
                        $ndrevamt = round($refundAmt - abs($actualAmount2),2);
                        $rfTrans = $refundTransactions->where('entry_type','=',1)->where('link_trans_id',$paidTrans->trans_id)->where('settled_outstanding','>',0);
                        foreach ($rfTrans as $rtfKey => $rtf) {
                            $refRevAmt =  $ndrevamt < $rtf->settled_outstanding ? $ndrevamt : $rtf->settled_outstanding;
                            if($ndrevamt > $rtf->settled_outstanding){
                                $refundTransactions{$rtfKey}->settled_outstanding = $rtf->settled_outstanding - $refRevAmt;
                                Log::info("$paidTrans->invoice_disbursed_id  $rtf->amount Overd Refund"); 
                            }else{
                                $refundTransactions->forget($rtfKey);
                            }
                            if($refRevAmt > 0){
                                $transactionList[] = [
                                    'payment_id' => NULL,
                                    'link_trans_id' => $rtf->trans_id,
                                    'parent_trans_id' => $rtf->parent_trans_id ?? $rtf->trans_id,
                                    'trans_running_id'=> $rtf->trans_running_id,
                                    'invoice_disbursed_id' => $rtf->invoice_disbursed_id,
                                    'user_id' => $rtf->user_id,
                                    'trans_date' => $currentdate,
                                    'amount' => $refRevAmt,
                                    'entry_type' => 0,
                                    'soa_flag' => $rtf->soa_flag,
                                    'trans_type' => config('lms.TRANS_TYPE.REVERSE'),
                                    'apportionment_id' => $apportionmentId ?? null
                                ];
                            }
                        }
                    }else{
                        $actualAmount2 = round($actualAmount2 + $refundAmt);
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

    public function transactionUserChargePostingAdjustment($transIds, $paymentId, $apportionmentId){
        $transactionList = [];
        $currentdate =  Helpers::getSysStartDate();

        $charges = Transactions::whereHas('transType',function($query){
            $query->where('chrg_master_id','!=','0');
        })->whereNull('parent_trans_id')
        ->whereNull('invoice_disbursed_id')
        ->where('actual_outstanding', '<', 0)
        ->where('entry_type',0)
        ->whereIn('trans_id',$transIds)
        ->get();

        foreach ($charges as $key => $charge) {
            $chargeRefundeTransactions = Transactions::where('parent_trans_id', $charge->trans_id)
            ->where('trans_type',32)
            ->get();
            
            $currentRefundableAmount = $charge->actual_outstanding < 0 ? abs($charge->actual_outstanding) : 0;
            
            $settleTransactions = Transactions::where('parent_trans_id',$charge->trans_id)
            ->where('entry_type',1)
            ->whereNotIn('trans_type',[32,2])
            ->where('settled_outstanding','>',0)
            ->orderBy('trans_id','DESC')
            ->get();

            foreach ($settleTransactions as $settleTrans) {
                $chrgRefunded = $chargeRefundeTransactions->where('link_trans_id',$settleTrans->trans_id)->where('entry_type',0)->sum('amount');
                $chrgToBeRefunded = $chargeRefundeTransactions->where('link_trans_id',$settleTrans->trans_id)->where('entry_type',1)->sum('settled_outstanding');
                $currentRefundableAmount = round($currentRefundableAmount - $chrgRefunded - $chrgToBeRefunded,2);
                $restSettledAmt = round($settleTrans->settled_outstanding - $chrgRefunded - $chrgToBeRefunded,2);
                if($restSettledAmt > 0 && $currentRefundableAmount > 0){
                    $refAmt = $restSettledAmt < $currentRefundableAmount ? $restSettledAmt : $currentRefundableAmount;
                    if($refAmt > 0){
                        $transactionList[] = [
                            'payment_id' => $paymentId,
                            'link_trans_id' => $settleTrans->trans_id,
                            'parent_trans_id' => $charge->trans_id,
                            'user_id' => $charge->user_id,
                            'trans_date' => $currentdate,
                            'amount' => $refAmt,
                            'entry_type' => 1,
                            'soa_flag' => 0,
                            'trans_type' => config('lms.TRANS_TYPE.REFUND'),
                            'apportionment_id' =>  $apportionmentId,
                        ];
                    }
                    $currentRefundableAmount = round($currentRefundableAmount - $refAmt,2);
                }elseif($currentRefundableAmount < 0){
                    $tobeRefundTranscations = $chargeRefundeTransactions->where('link_trans_id',$settleTrans->trans_id)
                    ->where('entry_type',1)
                    ->where('settled_outstanding','>',0);
                    foreach ($tobeRefundTranscations as $toBeRefTrans) {
                        $revAmt = abs($currentRefundableAmount) < $toBeRefTrans->settled_outstanding ? abs($currentRefundableAmount) : $toBeRefTrans->settled_outstanding;
                        $transactionList[] = [
                            'payment_id' => NULL,
                            'link_trans_id' => $toBeRefTrans->trans_id,
                            'parent_trans_id' => $charge->trans_id,
                            'user_id' => $charge->user_id,
                            'trans_date' => $currentdate,
                            'amount' => abs($revAmt),
                            'entry_type' => 0,
                            'soa_flag' => 0,
                            'trans_type' => config('lms.TRANS_TYPE.REVERSE'),
                            'apportionment_id' =>  $apportionmentId
                        ];
                        $currentRefundableAmount = round($currentRefundableAmount + $revAmt,2);
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

    public function runningToTransPosting($invDisbId, $intAccrualDt, $payFreq, $invdueDate, $odStartDate, $checkByPass = false){
        $currDate = Helpers::getSysStartDate();
        $intAccrualDate = $this->subDays($intAccrualDt,1);
        $invdueDate = $this->subDays($invdueDate,1);
        $graceStartDate = $invdueDate;
        $graceEndDate = $odStartDate;
        $endOfMonthDate = Carbon::createFromFormat('Y-m-d', $intAccrualDate)->endOfMonth()->format('Y-m-d');
        $lastDayofPreviousMonth = Carbon::createFromFormat('Y-m-d', $intAccrualDt)->subMonth()->endOfMonth()->format('Y-m-d');
        $intTransactions = new collection();
        $odTransactions = new collection();
        $transactions = new collection();
        $transactionList = [];

        $check1 = $checkByPass;
        if($payFreq == 1){
            if((strtotime($endOfMonthDate) == strtotime($intAccrualDate) ||  $check1 )){

                $intTransactions = TransactionsRunning::where('invoice_disbursed_id','=',$invDisbId)
                ->where('trans_type','=',config('lms.TRANS_TYPE.INTEREST'))
                ->where('entry_type','=',0)
                ->whereDate('trans_date','>=',$invdueDate)
                ->whereDate('trans_date','<=',$intAccrualDate)
                ->where(function($query)use($invdueDate,$intAccrualDt){
                    $firtOfMonth = Carbon::parse($intAccrualDt)->firstOfMonth()->format('Y-m-d');
                    $query->whereDate('trans_date','<',$firtOfMonth);
                })
                ->get()
                ->filter(function($item){
                    return $item->outstanding > 0;
                });
            }
        }
        if($payFreq == 2){

            if((strtotime($endOfMonthDate) == strtotime($intAccrualDate) ||  $check1 )){

                $intTransactions = TransactionsRunning::where('invoice_disbursed_id','=',$invDisbId)
                ->where('trans_type','=',config('lms.TRANS_TYPE.INTEREST'))
                ->where('entry_type','=',0)
                ->whereDate('trans_date','<=',$intAccrualDate)
                ->where(function($query)use($invdueDate,$intAccrualDt){
                    $firtOfMonth = Carbon::parse($intAccrualDt)->firstOfMonth()->format('Y-m-d');
                    $query->whereDate('trans_date','<',$firtOfMonth);
                })
                ->get()
                ->filter(function($item){
                    return $item->outstanding > 0;
                });
            }

        }
        elseif($payFreq == 3){
            
            if( ($check1 || strtotime($invdueDate) == strtotime($intAccrualDate)) && strtotime($intAccrualDate) >= strtotime($invdueDate)){
                $intTransactions = TransactionsRunning::where('invoice_disbursed_id','=',$invDisbId)
                ->where('trans_type','=',config('lms.TRANS_TYPE.INTEREST'))
                ->where('entry_type','=',0)
                ->whereDate('trans_date','<=',"$invdueDate")
                ->get()
                ->filter(function($item){
                    return $item->outstanding > 0;
                });
            }
        }

        //Overdue Posting
        if( ((strtotime($endOfMonthDate) == strtotime($intAccrualDate)) && strtotime($intAccrualDate) >= strtotime($odStartDate)) || $checkByPass){

            $odTransactions = TransactionsRunning::where('invoice_disbursed_id','=',$invDisbId)
            ->whereIn('trans_type',[config('lms.TRANS_TYPE.INTEREST'),config('lms.TRANS_TYPE.INTEREST_OVERDUE')])
            ->where('entry_type','=',0)
            ->whereDate('trans_date','<=',$lastDayofPreviousMonth)
            ->get()
            ->filter(function($item){
                return $item->outstanding > 0;
            });
        }

        $transactions = $intTransactions->merge($odTransactions); 

        foreach ($transactions as $key => $trans) {
            if(round($trans->outstanding,2) > 0.00){

                $transactionList[] = [
                    'payment_id' => null,
                    'link_trans_id' => NULL,
                    'parent_trans_id' => NULL, 
                    'trans_running_id'=> $trans->trans_running_id,
                    'invoice_disbursed_id' => $trans->invoice_disbursed_id,
                    'user_id' => $trans->user_id,
                    'trans_date' => carbon::parse($trans->trans_date)->endOfMonth()->format('Y-m-d'),
                    'from_date' => $trans->from_date,
                    'to_date' => $trans->trans_date,
                    'due_date' => carbon::parse($trans->trans_date)->endOfMonth()->format('Y-m-d'),
                    'amount' => $trans->outstanding,
                    'entry_type' => $trans->entry_type,
                    'soa_flag' => 1,
                    'gst' => 0,
                    'trans_type' => $trans->trans_type
                ];
            }
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
        $Dr = 0;
        $Cr = 0;
        // Disbursement Debit Amount
        $invDisbDetails = InvoiceDisbursed::where('int_accrual_start_dt','<=',$transDate)->where('invoice_disbursed_id',$invDisbId)->first();
        if($invDisbDetails){
            $margin = ($invDisbDetails->invoice->invoice_approve_amount*$invDisbDetails->margin)/100;
            $Dr += $invDisbDetails->invoice->invoice_approve_amount - $margin;
        }
        // Get Disbursement Transaction 
        $disbTransIds = Transactions::where('invoice_disbursed_id','=',$invDisbId) 
        ->whereNull('payment_id') 
        ->whereNull('link_trans_id') 
        ->whereNull('parent_trans_id')
        ->whereIn('trans_type',[config('lms.TRANS_TYPE.PAYMENT_DISBURSED')]) 
        ->pluck('trans_id')->toArray();

        $Cr +=  Transactions::whereDate('trans_date','<=',$transDate) 
        ->where('invoice_disbursed_id','=',$invDisbId)
        ->where('entry_type','=','1')
        ->where(function($query) use($disbTransIds){
            $query->whereIn('trans_id',$disbTransIds);
            $query->OrwhereIn('parent_trans_id',$disbTransIds);
        })
        ->sum('amount');

        $Dr +=  Transactions::whereDate('trans_date','<=',$transDate) 
        ->where('invoice_disbursed_id','=',$invDisbId)
        ->where('entry_type','=','0')
        ->where(function($query) use($disbTransIds){
            $query->whereIn('link_trans_id',$disbTransIds);
            $query->OrwhereIn('parent_trans_id',$disbTransIds);
        })
        ->sum('amount');

        if($payFreq == 2){
            $mIntrest = InterestAccrual::where('invoice_disbursed_id','=',$invDisbId)
            ->whereNotNull('interest_rate')
            ->where(function($query) use($odStartDate,$transDate){
                $firtOfMonth = Carbon::parse($transDate)->firstOfMonth()->format('Y-m-d');
                $query->whereDate('interest_date','<', $firtOfMonth);
                if($odStartDate <= $transDate){
                    $query->orWhere('interest_date','<',$odStartDate);
                }
            })
            ->sum('accrued_interest');
            $Dr += round($mIntrest,2);

            $intTransIds = Transactions::where('invoice_disbursed_id','=',$invDisbId) 
            ->where('entry_type',0)
            ->whereIn('trans_type',[config('lms.TRANS_TYPE.INTEREST')]) 
            ->pluck('trans_id')->toArray();
        }

        if($intTransIds){
  
            $Cr +=  Transactions::whereDate('trans_date','<=',$transDate) 
            ->where('invoice_disbursed_id','=',$invDisbId)
            ->where('entry_type','=','1')
            ->whereNotIn('trans_type',[config('lms.TRANS_TYPE.CANCEL'),config('lms.TRANS_TYPE.REFUND')]) 
            ->where(function($query) use($intTransIds){
                $query->whereIn('trans_id',$intTransIds);
                $query->OrwhereIn('parent_trans_id',$intTransIds);
            })
            ->sum('settled_outstanding');

            // $Dr +=  Transactions::whereDate('trans_date','<=',$transDate) 
            // ->where('invoice_disbursed_id','=',$invDisbId)
            // ->where('entry_type','=','0')
            // ->whereNotIn('trans_type',[config('lms.TRANS_TYPE.REVERSE'),config('lms.TRANS_TYPE.REFUND')]) 
            // ->where(function($query) use($intTransIds){
            //     $query->whereIn('link_trans_id',$intTransIds);
            //     $query->OrwhereIn('parent_trans_id',$intTransIds);
            // })
            // ->sum('amount');
        }
        return $Dr-$Cr;
    }
    
    private function overDuePosting($invDisbId, $userId, $transDate){
        $overdues = InterestAccrual::select(\DB::raw("
        sum(accrued_interest) as totalInt, 
        max(interest_date) as interestDate, 
        min(interest_date) as fromDate"))
        ->where('invoice_disbursed_id','=',$invDisbId)
        ->whereNull('interest_rate')
        ->groupByRaw('YEAR(interest_date), MONTH(interest_date)')
        ->get();
        TransactionsRunning::where('invoice_disbursed_id','=',$invDisbId)
        ->whereDate('trans_date','>=',$transDate)
        ->update(['amount'=>0,'sys_updated_at' => Helpers::getSysStartDate()]);

        if($overdues->count() > 0 ){
            foreach ($overdues as $odue) {
                $transRunningId = TransactionsRunning::where('invoice_disbursed_id','=',$invDisbId)
                ->where('trans_type','=',config('lms.TRANS_TYPE.INTEREST_OVERDUE'))
                ->where('entry_type','=',0)
                ->whereMonth('trans_date', date('n', strtotime($odue->interestDate)))
                ->whereYear('trans_date', date('Y', strtotime($odue->interestDate)))
                ->value('trans_running_id');

                if(isset($transRunningId)){
                    $whereCond = ['trans_running_id' => $transRunningId];
                    $intTransData = [
                        'invoice_disbursed_id' => $invDisbId,
                        'user_id' => $userId,
                        'from_date' => $odue->fromDate,
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
                        'from_date' => $odue->fromDate,
                        'trans_date' => $odue->interestDate,
                        'amount' => $odue->totalInt,
                        'entry_type' => 0,
                        'trans_type' => config('lms.TRANS_TYPE.INTEREST_OVERDUE')
                    ];
                    $this->lmsRepo->saveTransactionRunning($intTransData);
                }
            }
        }
    }

    private function interestPosting($invDisbId, $userId, $payFreq, $transDate, $gStartDate, $gEndDate){
        $interests = new Collection();
    
        $interestData = InterestAccrual::select(\DB::raw("
        sum(accrued_interest) as totalInt,
        max(interest_date) as interestDate, 
        min(interest_date) as fromDate"))
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
        
        TransactionsRunning::where('invoice_disbursed_id','=',$invDisbId)
        //->whereDate('trans_date','>=',$transDate)
        ->update(['amount'=>0,'sys_updated_at' => Helpers::getSysStartDate()]);
        if($interests->count()>0){
            foreach ($interests as $interest) {
                if($payFreq == 2){
                    $transId = TransactionsRunning::where('invoice_disbursed_id','=',$invDisbId)
                    ->where('trans_type','=',config('lms.TRANS_TYPE.INTEREST'))
                    ->where('entry_type','=',0)
                    ->whereMonth('trans_date', date('n', strtotime($interest->interestDate)))
                    ->whereYear('trans_date', date('Y', strtotime($interest->interestDate)))
                    ->value('trans_running_id');
                }

                //Rear End Case
                elseif($payFreq == 3 || $payFreq == 1){
                $transId = TransactionsRunning::where('invoice_disbursed_id','=',$invDisbId)
                ->where('trans_type','=',config('lms.TRANS_TYPE.INTEREST'))
                ->where('entry_type','=',0)
                ->value('trans_running_id');
                }

                if(isset($transId)){
                    $whereCond = ['trans_running_id' => $transId];
                    $intTransData = [
                        'invoice_disbursed_id' => $invDisbId,
                        'user_id' => $userId,
                        'from_date' => $interest->fromDate,
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
                        'from_date' => $interest->fromDate,
                        'trans_date' => $interest->interestDate,
                        'amount' => $interest->totalInt,
                        'entry_type' => 0,
                        'trans_type' => config('lms.TRANS_TYPE.INTEREST')
                    ];
                    $this->lmsRepo->saveTransactionRunning($intTransData);
                }
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

    public function intAccrual(int $invDisbId, $startDate = null, $apportionmentId = NULL){
        try{   
            $invdisbInN = [];
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
            $odStartDate = $gEndDate;
            $maxAccrualDate = $invDisbDetail->interests->max('interest_date');
            if($maxAccrualDate){
                $maxAccrualDate = $this->addDays($maxAccrualDate,1);
            } 
            $intType = 1;
            
            if($startDate && strtotime($gStartDate) <= strtotime($startDate) && strtotime($gEndDate) >= strtotime($startDate)){
                $startDate = $gStartDate;
            }
            
            $loopStratDate = $startDate ?? $maxAccrualDate ?? $intAccrualStartDate;
             
            if (is_null($invDisbDetail->int_accrual_start_dt)) {
                throw new InvalidArgumentException('Interest Accrual Start Date is missing for invoice Disbursed Id: ' . $invDisbId);
            }

            if (is_null($invDisbDetail->payment_due_date)) {
                throw new InvalidArgumentException('Payment Date is missing for invoice Disbursed Id: ' . $invDisbId);
            }
            
            $oldIntRate = $offerDetails->interest_rate - $offerDetails->base_rate;
            $bankRatesArr = $this->getBankBaseRates($offerDetails->bank_id);//if $bankRatesArr value is false then follow the old process. otherwise call the below function to get the actual interest rate based on base rate.

            while(strtotime($curdate) >= strtotime($loopStratDate)){
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
                
                $balancePrincipal = $this->getpaymentSettled($loopStratDate, $invDisbId, $payFreq, $gStartDate);

                // Update grace period interest into overdue interest.
                if(strtotime($loopStratDate) === strtotime($odStartDate) && $balancePrincipal > 0){
                    $this->updateGracePeriodInt($invDisbId, $gStartDate, $gEndDate, $odIntRate, $payFreq, $userId);
                }

                $balancePrincipal = $this->getpaymentSettled($loopStratDate, $invDisbId, $payFreq, $gStartDate);

                if($balancePrincipal > 0){
                    if(strtotime($loopStratDate) >= strtotime($odStartDate)){
                        $currentIntRate = $odIntRate;
                        $intType = 2; 
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
                $this->overDuePosting($invDisbId, $userId, $loopStratDate);
                
                $loopStratDate = $this->addDays($loopStratDate,1);
                if(!in_array($invDisbId, $invdisbInN)){
                    $this->runningToTransPosting($invDisbId, $loopStratDate, $payFreq, $payDueDate, $odStartDate);
                }
                
                if($balancePrincipal > 0){
                    $endOfMonthDate = Carbon::createFromFormat('Y-m-d', $loopStratDate)->endOfMonth()->format('Y-m-d');
                }else{
                    $transrunning = TransactionsRunning::where('user_id',$userId)->where('invoice_disbursed_id',$invDisbId)->get();
                    $unpostedAmt = $transrunning->where('outstanding','>',0)->sum('outstanding');
                    if(!$unpostedAmt)
                    break;
                }
            }
        } catch (Exception $ex) {
            return Helpers::getExceptionMessage($ex);
       } 
    }
    
    public function dailyIntAccrual(){
        
        ini_set("memory_limit", "-1");
        $cLogDetails = Helper::cronLogBegin(1);

        $curdate = Helpers::getSysStartDate();
        $invoiceList = InvoiceDisbursed::whereNotNull('int_accrual_start_dt')
        ->whereNotNull('payment_due_date')
        ->whereHas('invoice',function($query){ 
            $query->where('is_repayment','0'); 
        })
        ->pluck('invoice_disbursed_id','invoice_disbursed_id');

        foreach ($invoiceList as $invId => $trans) {
            echo $invId."\n";
            $this->intAccrual($invId, NULL);
            $this->transactionPostingAdjustment($invId, NULL);
        }
        // Update Invoice Disbursed Accrual Detail
        InvoiceDisbursedDetail::updateDailyInterestAccruedDetails();
        $this->generateDebitNote();
        $this->generateCreditNote();
        
        if($cLogDetails){
            Helper::cronLogEnd('1',$cLogDetails->cron_log_id);
        }
    }

    public function generateDebitNote(){
        $controller = app()->make('App\Http\Controllers\Lms\userInvoiceController');
        $billData = [];
        $curdate = Helpers::getSysStartDate();
        $cDate = Carbon::parse($curdate)->format('Y-m-d');
        $transList = Transactions::whereNull('parent_trans_id')
        ->whereHas('transType', function($query){
            $query->where('chrg_master_id','>','0')
            ->orWhereIn('id',[config('lms.TRANS_TYPE.INTEREST'),config('lms.TRANS_TYPE.INTEREST_OVERDUE')]);
        })
        ->whereDate('created_at','<=',$cDate)
        ->whereDate('created_at','>=','2022-04-01')
        ->where('entry_type','0')
        ->where('is_invoice_generated','0')
        ->get();
        
        $billData = [];
        foreach($transList as $trans){
            $billType = null;
            if($trans->trans_type == config('lms.TRANS_TYPE.INTEREST')){
                $billType = 'I';
            }elseif($trans->trans_type == config('lms.TRANS_TYPE.INTEREST_OVERDUE')){
                $billType = 'I';
            }elseif($trans->trans_type >= 50){
                $billType = 'C';
            }

            $billData[$trans->user_id][$billType][$trans->gst][$trans->trans_id] = $trans->trans_id;
        }

        foreach($billData as $userId => $transTypes){
            foreach($transTypes as $billType => $gstTypes){
                foreach ($gstTypes as $gst => $trans){
                    $transIds = array_keys($trans);
                    if(!empty($transIds)){
                        $controller->generateDebitNote($transIds, $userId, $billType);
                    }
                }
            }
        }
    }

    public function generateCreditNote($userId = NULL, $apportionmentId = NULL){
        $controller = app()->make('App\Http\Controllers\Lms\userInvoiceController');
        $curdate = Helpers::getSysStartDate();
        $cDate = Carbon::parse($curdate)->format('Y-m-d');
        $cancelTransList = Transactions::whereNotNull('parent_trans_id')
        ->whereIn('trans_type',[config('lms.TRANS_TYPE.CANCEL'),config('lms.TRANS_TYPE.WAVED_OFF')])
        ->whereHas('userInvParentTrans.getUserInvoice')
        ->whereDate('created_at','>=','2022-04-01')
        ->whereDate('created_at','<=',$cDate)
        ->where('entry_type','1')
        ->where('is_invoice_generated','0')
        ->with('userInvParentTrans:trans_id,user_invoice_id','userInvParentTrans.getUserInvoice:user_invoice_id,user_invoice_rel_id');

        if($userId){
            $cancelTransList->where('user_id',$userId);
        }
        if($apportionmentId){
            $cancelTransList->where('apportionment_id',$apportionmentId);
        }
        
        $cancelTransList = $cancelTransList->get();

        $creditData = [];
        foreach($cancelTransList as $trans){
            $billType = null;
            if($trans->parentTransactions->trans_type == config('lms.TRANS_TYPE.INTEREST')){
                $billType = 'I';
            }elseif($trans->parentTransactions->trans_type == config('lms.TRANS_TYPE.INTEREST_OVERDUE')){
                $billType = 'I';
            }elseif($trans->parentTransactions->trans_type >= 50){
                $billType = 'C';
            }else{
                $billType = $trans->parentTransactions->trans_type;
            }

            $creditData[$trans->user_id][$billType][$trans->gst.'_'.$trans->userInvParentTrans->getUserInvoice->user_invoice_rel_id][$trans->trans_id] = $trans->trans_id;
        }
        foreach($creditData as $userId => $transTypes){
            foreach($transTypes as $billType => $gstRelation){
                foreach ($gstRelation as $gstRelCode => $trans){
                    $transIds = array_keys($trans);
                    if(!empty($transIds)){
                        $controller->generateCreditNote($transIds, $userId, $billType);
                    }
                }
            }
        }
    }

    public function generateCreditNoteReversal($userId = NULL, $apportionmentId){
        $controller = app()->make('App\Http\Controllers\Lms\userInvoiceController');
        $curdate = Helpers::getSysStartDate();
        $cDate = Carbon::parse($curdate)->format('Y-m-d');
        $cancelTransList = Transactions::whereNotNull('link_trans_id')->whereIn('trans_type',[config('lms.TRANS_TYPE.REVERSE')])->whereHas('userInvLinkTrans.getUserInvoice')->whereDate('created_at','>=','2022-04-01')->where('entry_type','0')->whereDate('created_at','<=',$cDate)->where('is_invoice_generated','0');

        if($userId){
            $cancelTransList->where('user_id',$userId);
        }
        if($apportionmentId){
            $cancelTransList->where('apportionment_id',$apportionmentId);
        }

        $cancelTransList = $cancelTransList->with('userInvLinkTrans:trans_id,user_invoice_id','userInvLinkTrans.getUserInvoice:user_invoice_id,user_invoice_rel_id')->get();

        $creditData = [];
        foreach($cancelTransList as $trans){
            $billType = null;
            if($trans->parentTransactions->trans_type == config('lms.TRANS_TYPE.INTEREST')){
                $billType = 'I';
            }elseif($trans->parentTransactions->trans_type == config('lms.TRANS_TYPE.INTEREST_OVERDUE')){
                $billType = 'I';
            }elseif($trans->parentTransactions->trans_type >= 50){
                $billType = 'C';
            }else{
                $billType = $trans->parentTransactions->trans_type;
            }

            $creditData[$trans->user_id][$billType][$trans->gst.'_'.$trans->userInvLinkTrans->getUserInvoice->user_invoice_rel_id][$trans->trans_id] = $trans->trans_id;
        }
        foreach($creditData as $userId => $transTypes){
            foreach($transTypes as $billType => $gstRelation){
                foreach ($gstRelation as $gstRelCode => $trans){
                    $transIds = array_keys($trans);
                    if(!empty($transIds)){
                        $controller->generateCreditNoteReversal($transIds, $userId, $billType);
                    }
                }
            }
        }
    }

    public function runningIntPosting(){
        $curDate = Helpers::getSysStartDate();
        $curDate = Carbon::parse($curDate)->format('Y-m-d');
        $invDisbursedIds = TransactionsRunning::whereDate('trans_date','<',$curDate)->orderBy('invoice_disbursed_id','ASC')->get()->filter(function($item) {
            return round($item->outstanding,2) > 0;
        })->pluck('invoice_disbursed_id')->toArray();

        $invDisbursedIds = array_unique($invDisbursedIds ?? []);
        sort($invDisbursedIds);
        foreach ($invDisbursedIds as  $invDisbId) {
            echo $invDisbId ."\n";
            $invDisbDetail = InvoiceDisbursed::find($invDisbId);
            $offerDetails = $invDisbDetail->invoice->program_offer;
            $payFreq = $offerDetails->payment_frequency;
            $payDueDate = $invDisbDetail->payment_due_date;
            $gPeriod = $invDisbDetail->grace_period;
            $gEndDate = $this->addDays($payDueDate,$gPeriod);
            $odStartDate = $this->addDays($gEndDate,1);
            $this->runningToTransPosting($invDisbId, $curDate, $payFreq, $payDueDate, $odStartDate, true);
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