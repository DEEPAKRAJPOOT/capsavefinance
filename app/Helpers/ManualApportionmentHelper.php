<?php

namespace App\Helpers;
use DB;
use Helpers;
use Carbon\Carbon;
use App\Helpers\Helper;
use InvalidArgumentException;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use App\Inv\Repositories\Models\Payment;
use App\Inv\Repositories\Models\Lms\Disbursal;
use App\Inv\Repositories\Models\Master\BaseRate;
use App\Inv\Repositories\Models\Lms\Transactions;
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

    public function transactionPostingAdjustment($invDisbId, $startDate, $payFreq, $paymentId = null, $useApporCol = false){
        $transactionList = [];
        $amount = 0.00;

        $transactions = Transactions::where('invoice_disbursed_id','=',$invDisbId)
            ->where('entry_type','=',0)
            ->whereNull('link_trans_id')
            ->whereNull('parent_trans_id')
            ->whereIn('trans_type',[config('lms.TRANS_TYPE.INTEREST'),config('lms.TRANS_TYPE.INTEREST_OVERDUE')])
            ->get();
        
        $curdate =  Helpers::getSysStartDate();
        $curdate = Carbon::parse($curdate)->format('Y-m-d');
            
        foreach($transactions as $trans){
            $invDisb = $trans->invoiceDisbursed;
            $payFreq = $invDisb->invoice->program_offer->payment_frequency;
            $isRepayment = $invDisb->invoice->is_repayment;
            $paymentDueDate = $invDisb->payment_due_date;
            $disbursedDate = $invDisb->int_accrual_start_dt;

            $amount = round($trans->amount,2);
            $outstanding = ($trans->outstanding > 0.00)?$trans->outstanding:0.00;
            $toIntDate = $trans->getToIntDateAttribute($payFreq, $disbursedDate, $paymentDueDate);
            $actualAmount = InterestAccrual::where('interest_date', '>=',$trans->getFromIntDateAttribute($payFreq, $disbursedDate, $paymentDueDate))
                    ->where('interest_date','<=',$toIntDate)
                    ->where(function($query) use($trans){
                        if($trans->trans_type == 9){
                            $query->whereNotNull('interest_rate'); 
                        }
                        elseif($trans->trans_type == 33){
                            $query->whereNotNull('overdue_interest_rate');
                        }
                    })
                    ->where('invoice_disbursed_id', $trans->invoice_disbursed_id)
                    ->sum('accrued_interest');
            $actualAmount = round($actualAmount,2);

            if($toIntDate){
                if( $trans->trans_type == config('lms.TRANS_TYPE.INTEREST') && $payFreq == '1' && $isRepayment == '0'){
                    $actualAmount = $amount;
                }
            }
            
            $cTransactions = Transactions::where('parent_trans_id','=',$trans->parent_trans_id ?? $trans->trans_id)
            ->where('invoice_disbursed_id','=',$invDisbId)
            ->where('entry_type','=',1)
            ->where('trans_running_id',$trans->trans_running_id)
            ->where('trans_type','=',config('lms.TRANS_TYPE.CANCEL'))
            ->get();
            
            foreach($cTransactions as $cTrans){
                $amount -= round($cTrans->amount,2);
                $amount = round($amount,2);
            }

            $amount = round(round($actualAmount,2) - round($amount,2),2);
            if($amount < 0.00){
                $amount = abs($amount);
                // Interest cancelation Process
                
                if($amount > 0.00){
                    if($amount >= $outstanding){
                        $cAmt = $outstanding;
                    }else{
                        $cAmt = $amount;
                    }
                    $amount -= round($cAmt,2);
                    $amount = round($amount,2);
                    if(round($cAmt, 2) > 0.00){
                        $refundFlag = True;
                        if($payFreq == 1 && strtotime($curdate) < strtotime($paymentDueDate)){
                            $refundFlag = False;
                        }
                        if($refundFlag){
                            $transactionList[] = [
                                'payment_id' => null,
                                'link_trans_id' => $trans->trans_id,
                                'parent_trans_id' => $trans->parent_trans_id ?? $trans->trans_id,
                                'trans_running_id'=> $trans->trans_running_id,
                                'invoice_disbursed_id' => $trans->invoice_disbursed_id,
                                'user_id' => $trans->user_id,
                                'trans_date' => $trans->trans_date,
                                'amount' => $cAmt,
                                'entry_type' => 1,
                                'soa_flag' => 1,
                                'trans_type' => config('lms.TRANS_TYPE.CANCEL'),
                                'apportionment_id' => $useApporCol ? $paymentId : null,
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

    public function refundProcess($invDisbId, $parentTransId = null, $linkTransId = null){
        $refundTrans = $this->generateRefundTrans($invDisbId, $parentTransId, $linkTransId);
        
        if(!empty($refundTrans)){
            foreach ($refundTrans as $key => $newTrans) {
                $this->lmsRepo->saveTransaction($newTrans);
            }
        }
    }  

    public function generateRefundTrans(int $invDisbId, $parentTransId = null, $linkTransId = null){
        $intBornBy = null;
        $payFrq = null;
        $banchDate = null;
        $isSettled = null;

        $InvDetails = DB::SELECT('SELECT b.invoice_disbursed_id,d.`interest_borne_by`, c.`payment_frequency`, b.payment_due_date, b.int_accrual_start_dt, c.`benchmark_date`, a.`is_repayment` FROM rta_invoice AS a JOIN rta_invoice_disbursed AS b ON a.`invoice_id` = b.`invoice_id` JOIN rta_app_prgm_offer AS c ON c.`prgm_offer_id` = a.`prgm_offer_id` JOIN rta_prgm AS d ON d.`prgm_id` = a.`program_id` WHERE b.`invoice_disbursed_id` = ? limit 1', [$invDisbId]);

        if(count($InvDetails)){
            $intBornBy = $InvDetails[0]->interest_borne_by;
            $payFrq = $InvDetails[0]->payment_frequency;
            $banchDate = $InvDetails[0]->benchmark_date;
            $isSettled = $InvDetails[0]->is_repayment;
            $disbursedDate = $InvDetails[0]->int_accrual_start_dt; 
            $paymentDueDate = $InvDetails[0]->payment_due_date;
        }

        $parentTransactions = Transactions::where('invoice_disbursed_id',$invDisbId)
        ->whereNull('parent_trans_id')
        ->whereNull('link_trans_id')
        ->where('entry_type','0')
        ->whereIn('trans_type',[config('lms.TRANS_TYPE.INTEREST'),config('lms.TRANS_TYPE.INTEREST_OVERDUE')]);
        
        if($parentTransId){
            $parentTransactions->where('parent_trans_id',$parentTransId);
        }
        $parentTransactions = $parentTransactions->get();

        $transactionList = [];
        foreach($parentTransactions as $pTrans){
            $amount = $pTrans->amount;
            $pTToIntDate = $pTrans->getToIntDateAttribute($payFrq, $disbursedDate, $paymentDueDate);
            $actualAmount = InterestAccrual::where('interest_date', '>=',$pTrans->getFromIntDateAttribute($payFrq, $disbursedDate, $paymentDueDate))
                    ->where('interest_date','<=',$pTToIntDate)
                    ->where(function($query) use($pTrans){
                        if($pTrans->trans_type == 9){
                            $query->whereNotNull('interest_rate'); 
                        }
                        elseif($pTrans->trans_type == 33){
                            $query->whereNotNull('overdue_interest_rate');
                        }
                    })
                    ->where('invoice_disbursed_id', $pTrans->invoice_disbursed_id)
                    ->sum('accrued_interest');
            $actualAmount = round($actualAmount,2);

            if($pTToIntDate){
                $curdate =  Helpers::getSysStartDate();
                $curdate = Carbon::parse($curdate)->format('Y-m-d');
                if( $pTrans->trans_type == config('lms.TRANS_TYPE.INTEREST') && $payFrq == '1' && $isSettled == '0'){
                    $actualAmount = $amount;
                }
            }

            $settledOtherAmt = Transactions::where('parent_trans_id',$pTrans->trans_id)
            ->where('entry_type','1')
            ->whereNotIn('trans_type',[$pTrans->trans_type,config('lms.TRANS_TYPE.CANCEL'),config('lms.TRANS_TYPE.TDS'),config('lms.TRANS_TYPE.REFUND')])
            ->sum('amount');

            $actualAmount = round($actualAmount-$settledOtherAmt,2);

            $paidTransactions = Transactions::where('parent_trans_id','=',$pTrans->trans_id)
            ->where('invoice_disbursed_id','=',$invDisbId)
            ->where('entry_type','=',1)
            ->whereIn('trans_type',[$pTrans->trans_type,7]);
            
            if($payFrq == 1 /*&& $intBornBy == 2*/ && !$isSettled){
                $paidTransactions->whereNotIn('invoice_disbursed_id',[$invDisbId]);
            }

            if($linkTransId){
                $paidTransactions->where('link_trans_id',$linkTransId);
            }

            $paidTransactions = $paidTransactions->orderBy('trans_id','ASC')->get();

            foreach($paidTransactions as $paidTrans){
                $pdTransAmount = $paidTrans->settled_outstanding;
                $actualAmount = round($actualAmount - $pdTransAmount,2);
                if($actualAmount < 0){
                    
                    $refundAmt = Transactions::where('parent_trans_id','=',$pTrans->trans_id)
                    ->where('link_trans_id','=',$paidTrans->trans_id)
                    ->where('invoice_disbursed_id','=',$invDisbId)
                    ->where('entry_type','=',1)
                    ->where('trans_type','=',config('lms.TRANS_TYPE.REFUND'))
                    ->sum('amount');
                    
                    $actualAmount += $refundAmt;

                    if($payFrq == 1){
                        $refundTranDetails = Transactions::where('invoice_disbursed_id',$invDisbId)->where('entry_type','1')->whereNotIn('trans_type',[8,10,32])->whereNotNull('payment_id')->orderBy('trans_date', 'DESC')->limit(1)->first();
                        $refundPaymentId = $refundTranDetails->payment_id ?? $paidTrans->payment_id  ?? NULL;
                        $refundTransDate = $refundTranDetails->trans_date ?? $paidTrans->trans_date;
                        $refundCreatedAt = $refundTranDetails->created_at ?? $paidTrans->created_at;
                        $refundCreatedBy = $refundTranDetails->created_by ?? $paidTrans->created_by;
                        $refundTransId = $refundTranDetails->trans_id ?? $paidTrans->trans_id ?? NULL;
                    }else{
                        $refundPaymentId = $paidTrans->payment_id ?? NULL;
                        $refundTransDate = $paidTrans->trans_date;
                        $refundCreatedAt = $paidTrans->created_at;
                        $refundCreatedBy = $paidTrans->created_by;
                        $refundTransId = $paidTrans->trans_id ?? NULL;
                    }
/*                    $emptyTransId = null;
                    $emptyTransDetails = DB::select('SELECT DISTINCT trans_id +1 AS dd FROM rta_transactions_old WHERE trans_id + 1 NOT IN (SELECT DISTINCT trans_id FROM rta_transactions_old) AND trans_id  > ? ORDER BY dd ASC LIMIT 1',[$refundTransId]);
                    if($emptyTransDetails){
                        $emptyTransId = $emptyTransDetails?$emptyTransDetails[0]->dd:null;
                    }
*/
                    if($actualAmount < 0){
                        $soaFlag =  $paidTrans->trans_type == 7 ? 0:1;
                        $transactionList[] = [
                            'payment_id' => $refundPaymentId,
                            'link_trans_id' => $paidTrans->trans_id,
                            'parent_trans_id' => $paidTrans->parent_trans_id ?? $paidTrans->trans_id,
                            'trans_running_id'=> $paidTrans->trans_running_id,
                            'invoice_disbursed_id' => $paidTrans->invoice_disbursed_id,
                            'user_id' => $paidTrans->user_id,
                            'trans_date' => $refundTransDate,
                            'amount' => abs($actualAmount),
                            'entry_type' => 1,
                            'soa_flag' => 1,
                            'trans_type' => config('lms.TRANS_TYPE.REFUND'),
                            'apportionment_id' => $refundPaymentId
                        ];
                        $actualAmount = 0;
                    }
                }
            }
        }

        return $transactionList;
    }

    public function runningToTransPosting($invDisbId, $intAccrualDt, $payFreq, $invdueDate, $odStartDate, $checkByPass = false){
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
        // if($checkByPass){
        //     $check1  = strtotime($intAccrualDate) > strtotime($graceEndDate);
        // }else{
        //     $check1 = $checkByPass;
        // }

        // Interest Posting
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
        $invDisbDetails = InvoiceDisbursed::where('int_accrual_start_dt','<=',$transDate)->where('invoice_disbursed_id',$invDisbId)->first();
        if($invDisbDetails){
            $margin = ($invDisbDetails->invoice->invoice_approve_amount*$invDisbDetails->margin)/100;
            $Dr = $invDisbDetails->invoice->invoice_approve_amount - $margin;
        }

        $disbTransIds = Transactions::where('invoice_disbursed_id','=',$invDisbId) 
        ->whereNull('payment_id') 
        ->whereNull('link_trans_id') 
        ->whereNull('parent_trans_id')
        ->whereIn('trans_type',[config('lms.TRANS_TYPE.PAYMENT_DISBURSED')]) 
        ->pluck('trans_id')->toArray();

        if($disbTransIds){
            $disbIntTransIds = [];
            $disbDetails = Transactions::find($disbTransIds[0]);
            $intBornBy = $disbDetails->invoiceDisbursed->invoice->program->interest_borne_by;
            $disTransDate = $disbDetails->trans_date;
            /*if((int) $intBornBy == 1){
                $disbIntTransIds = Transactions::where('invoice_disbursed_id','=',$invDisbId) 
                ->whereNull('payment_id') 
                ->whereNull('link_trans_id') 
                ->whereNull('parent_trans_id')
                ->where('trans_date',$disTransDate)
                ->whereIn('trans_type',[config('lms.TRANS_TYPE.INTEREST')]) 
                ->pluck('trans_id')->toArray();
            }*/
            $disbTransIds = array_merge($disbIntTransIds,$disbTransIds);
        }

        $Cr =  Transactions::whereDate('trans_date','<=',$transDate) 
        ->where('invoice_disbursed_id','=',$invDisbId)
        ->where('entry_type','=','1')
        ->where(function($query) use($disbTransIds){
            $query->whereIn('trans_id',$disbTransIds);
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
            ->whereNull('payment_id') 
            ->whereNull('link_trans_id') 
            ->whereNull('parent_trans_id')
            ->whereIn('trans_type',[config('lms.TRANS_TYPE.INTEREST')]) 
            ->pluck('trans_id')->toArray();
        }

        if($intTransIds){
  
             $Cr +=  Transactions::whereDate('trans_date','<=',$transDate) 
             ->where('invoice_disbursed_id','=',$invDisbId)
             ->where('entry_type','=','1')
             ->whereNotIn('trans_type',[config('lms.TRANS_TYPE.CANCEL')]) 
             ->where(function($query) use($intTransIds){
                 $query->whereIn('trans_id',$intTransIds);
                 $query->OrwhereIn('parent_trans_id',$intTransIds);
             })
             ->sum('amount');
        }
        return $Dr-$Cr;
    }
    
    private function overDuePosting($invDisbId, $userId, $transDate){
        $overdues = InterestAccrual::select(\DB::raw("sum(accrued_interest) as totalInt,max(interest_date) as interestDate"))
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
                $this->runningToTransPosting($invDisbId, $loopStratDate, $payFreq, $payDueDate, $odStartDate);
                
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
        //$transRunningTrans = $this->lmsRepo->getUnsettledRunningTrans();
        //sort($transRunningTrans);
        //$invoiceList = $this->lmsRepo->getUnsettledInvoices(['noNPAUser'=>true, 'intAccrualStartDateLteSysDate'=>true]);
        $invoiceList = InvoiceDisbursed::whereNotNull('int_accrual_start_dt')->whereNotNull('payment_due_date')->whereHas('invoice',function($query){ $query->where('is_repayment','0'); })->pluck('invoice_disbursed_id','invoice_disbursed_id');
        foreach ($invoiceList as $invId => $trans) {
            echo $invId."\n";
            //$pos = array_search($invId, $transRunningTrans);
            //unset($transRunningTrans[$pos]);
            //unset($pos);
            $this->intAccrual($invId);
            $this->transactionPostingAdjustment($invId, NULL, NULL, NULL);
            $this->refundProcess($invId,NULL,NULL);
        }
        // Update Invoice Disbursed Accrual Detail
        InvoiceDisbursedDetail::updateDailyInterestAccruedDetails();

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
        
        $controller = app()->make('App\Http\Controllers\Lms\userInvoiceController');
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

            $billData[$trans->user_id][$billType][$trans->trans_id] = $trans->trans_id;
        }

        foreach($billData as $userId => $transTypes){
            foreach($transTypes as $billType => $trans){
                $transIds = array_keys($trans);
                if(!empty($transIds)){
                    $controller->generateCapsaveInvoice($transIds, $userId, $billType);
                }
            }
        }
        
        /*foreach($transRunningTrans as $invId){
            $invDisbDetail = InvoiceDisbursed::find($invId);
            $offerDetails = $invDisbDetail->invoice->program_offer;
            $payFreq = $offerDetails->payment_frequency;
            $gPeriod = $invDisbDetail->grace_period;
            $payDueDate = $invDisbDetail->payment_due_date;
            $gStartDate = $payDueDate;
            $gEndDate = $this->addDays($payDueDate,$gPeriod);
            $this->runningToTransPosting($invId, $curdate, $payFreq, $payDueDate, $gEndDate);
            $this->transactionPostingAdjustment($invId, NULL, NULL, NULL);   
        }*/
        
        if($cLogDetails){
            Helper::cronLogEnd('1',$cLogDetails->cron_log_id);
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
            ->whereMonth('trans_date', date('n', strtotime($odue->interestDate)))
            ->value('trans_running_id');
            
            TransactionsRunning::where('invoice_disbursed_id','=',$invDisbId)
            ->where('trans_type','=',config('lms.TRANS_TYPE.INTEREST_OVERDUE'))
            ->where('entry_type','=',0)
            ->where(\DB::raw('MONTH(trans_date)'),'>',date('n', strtotime($odue->interestDate)))
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
                ->whereMonth('trans_date', date('n', strtotime($interest->interestDate)))
                ->value('trans_running_id');
                
                TransactionsRunning::where('invoice_disbursed_id','=',$invDisbId)
                ->where('trans_type','=',config('lms.TRANS_TYPE.INTEREST'))
                ->where('entry_type','=',0)
                ->where(\DB::raw('MONTH(trans_date)'),'>',date('n', strtotime($interest->interestDate)))
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
                    //$this->sugOverDuePosting($invDisbId, $userId);
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
        ->where(\DB::raw('MONTH(trans_date)'),'<',date('n', strtotime($intAccrualDate)))
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
        ->where(\DB::raw('MONTH(trans_date)'),'<',date('n', strtotime($intAccrualDate)))
        ->update(['soa_flag'=>$soaFlag,'sys_updated_at' => Helpers::getSysStartDate()]);
    }
    */
}