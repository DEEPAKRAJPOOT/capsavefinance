<?php

namespace App\Helpers;
use Illuminate\Support\Facades\DB;
use App\Inv\Repositories\Models\Lms\Disbursal;
use App\Inv\Repositories\Models\Lms\Transactions;
use App\Inv\Repositories\Contracts\Traits\LmsTrait;

class ApportionmentHelper{
    
    use LmsTrait;

    private $transId;
    private $transDetails;
    private $disbursalData;
    private $disbursal=[];
    private $transaction=[];

    private $balanceRepayAmount = 0;
    private $totalRefundAmount = 0;

    private $dsbursalId;
    private $penalDays;
    private $penalAmount;
    private $accuredInt;
    private $marginAmount;
    private $penalAmountSettled;
    private $marginSettled;
    private $interestSettled;
    private $principalSettled;
    private $paymentFreq;
    private $isPrincipalSettle;
    private $repayBeforeCharges;
    private $repayAfterCharges;
   
    public function __construct($app_repo, $user_repo, $doc_repo, $lms_repo){
		$this->appRepo = $app_repo;
		$this->userRepo = $user_repo;
		$this->docRepo = $doc_repo;
		$this->lmsRepo = $lms_repo;
    }

    private function getTransaction($transId){
        $this->transDetails = Transactions::whereIn('is_settled',[0,1])->where(['trans_id'=>$transId,'trans_type'=>config('lms.TRANS_TYPE.REPAYMENT')])->get()->first();
        if($this->transDetails->count()>0){
            $this->balanceRepayAmount = $this->transDetails->amount - $this->transDetails->settled_amount;
            self::getDisbursals();
        }
    }

    private function getDisbursals(){
        $this->disbursalData =  Disbursal::where(['user_id'=>$this->transDetails->user_id])
        //->whereIn('status_id',[config('lms.STATUS_ID.PARTIALLY_PAYMENT_SETTLED'),config('lms.STATUS_ID.DISBURSED')])
        //->where('int_accrual_start_dt', '<', DB::raw(DATE("'".$this->transDetails->trans_date."'")))
        ->orderBy('payment_due_date','asc')
        ->orderBy('disbursal_id','asc')
        ->get();
    }

    public function init($transId){
        $this->transId = $transId;
        self::getTransaction($this->transId);
        foreach ($this->disbursalData as $key => $disbursalDetail) {
            if($disbursalDetail->count() > 0)
            self::settleDisbursal($disbursalDetail);
            $this->transaction['disbursal'][$disbursalDetail->disbursal_id] = $this->disbursal;
        }
        self::settleCharges();
        self::settleAllMargin();
        self::saveTransactions();
    }

    private function setDisbursalId(&$disbursal){
        $this->dsbursalId = $disbursal->disbursal_id;
    }

    private function setAccuredInt(&$disbursal){
        $this->accuredInt = $disbursal
        ->interests
        ->where('interest_date', '<', DB::raw(DATE($this->transDetails->trans_date)))
        ->sum('accrued_interest');

    }    

    private function setPenalAmount(&$disbursal){
        $this->penalAmount = $disbursal
        ->interests
        ->where('interest_date', '<', DB::raw(DATE($this->transDetails->trans_date)))
        ->where('overdue_interest_rate','!=', NULL)
        ->sum('accrued_interest');
    }

    private function setPenalAmountSettled(&$disbursal){
        $this->penalAmountSettled = Transactions::where('disbursal_id','=',$disbursal->disbursal_id)
                        ->where('trans_type','=',config('lms.TRANS_TYPE.INTEREST_OVERDUE'))
                        ->where('entry_type','=','0')
                        ->sum('amount');
    }

    private function setInterestSettled(&$disbursal){
        $this->interestSettled = Transactions::where('disbursal_id','=',$disbursal->disbursal_id)
        ->where('trans_type','=',config('lms.TRANS_TYPE.INTEREST_PAID'))
        ->sum('amount');
    }

    private function setPrincipalAmount(&$disbursal){
        $this->principalAmount = $disbursal->principal_amount;
    }

    private function setPrincipalSettled(&$disbursal){
        $this->principalSettled = Transactions::where('disbursal_id','=',$disbursal->disbursal_id)
                        ->whereIn('trans_type',[config('lms.TRANS_TYPE.INVOICE_KNOCKED_OFF'),config('lms.TRANS_TYPE.INVOICE_PARTIALLY_KNOCKED_OFF')])
                        ->sum('amount');
    }

    private function setMarginAmount(&$disbursal){
        $marginAmountDetails = $this->userRepo->getDisbursalList()->where('disbursal_id','=',$disbursal->disbursal_id)
                        ->groupBy('margin')
                        ->select(DB::raw('(sum(invoice_approve_amount)*margin)/100 as margin_amount,margin'))
                        ->first();
        $this->marginAmount = $marginAmountDetails->margin_amount;
    }

    private function setMarginSettled(&$disbursal){
        $this->marginSettled = Transactions::where('disbursal_id','=',$disbursal->disbursal_id)
                        ->where('trans_type','=',config('lms.TRANS_TYPE.MARGIN'))
                        ->whereNotNull('repay_trans_id')
                        ->where('entry_type','=','1')
                        ->sum('amount');
    }

    private function setPenalDays(&$disbursal){
        $this->penalDays = $disbursal
            ->interests
            ->where('interest_date', '<', DB::raw(DATE($this->transDetails->trans_date)))
            ->where('overdue_interest_rate','!=', NULL)
            ->count();
    }

    private function setPaymentFreq(&$disbursal){
        $prgmOffer = $this->lmsRepo->getProgramOffer([
            'app_id' => $disbursal->app_id,
            'invoice_id' => $disbursal->invoice_id,   
            'disbursal_id' => $disbursal->disbursal_id,   
        ]);

        $this->paymentFreq = $prgmOffer->payment_frequency ? $prgmOffer->payment_frequency : 1;
    }
    
    // private function setRepayPenalCharges(&$disbursal){
    //     $penalAmountDue = $this->penalAmount-$this->penalAmountSettled;
    //     if($penalAmountDue>0 && $this->balanceRepayAmount>0)
    //     {
            
    //         if($this->balanceRepayAmount >= $penalAmountDue){
    //             $overduePaidAmt = $penalAmountDue;
    //         }else{
    //             $overduePaidAmt = $this->balanceRepayAmount;
    //         }

    //         $this->balanceRepayAmount -= $overduePaidAmt;
    //         $this->disbursal['total_repaid_amt'] += $overduePaidAmt;

    //         $overdueData = $this->createTransactionData($this->transDetails->user_id, [
    //             'amount' => $overduePaidAmt,
    //             'trans_date'=>$this->transDetails->trans_date,
    //             'disbursal_id'=>$disbursal->disbursal_id,
    //             'repay_trans_id'=>$this->transDetails->trans_id
    //         ], null, config('lms.TRANS_TYPE.INTEREST_OVERDUE'), 0);
    //         $this->transaction['overdue'][$disbursal->disbursal_id] = $overdueData;
    //     }
    // }

    private function setRepayBeforeCharges($userId){
        $this->repayBeforeCharges = Transactions::where('user_id','=',$userId)
        ->where('entry_type','=',0)
        ->whereNull('repay_trans_id')
        ->where('created_at', '<=', DB::raw(DATE("'".$this->transDetails->trans_date."'")))
        ->whereHas('trans_detail', function($query){ 
            $query->where('chrg_master_id','!=','0');
        })
        ->orderBy('trans_date','asc')->get()->toArray();
    }

    private function setRepayAfterCharges($userId){
        $this->repayAfterCharges = Transactions::where('user_id','=',$userId)
        ->where('entry_type','=',0)
        ->where('created_at', '>', DB::raw(DATE("'".$this->transDetails->trans_date."'")))
        ->whereHas('trans_detail', function($query){ 
            $query->where('chrg_master_id','!=','0');
        })
        ->orderBy('trans_date','asc')->get()->toArray();
    }

    private function getInterestDueAmount(&$disbursal){
        switch ($this->paymentFreq) {
            case '1':
                $interestDue = 0;
            break;
            case '2':
                $interestDue = (float)$disbursal->total_interest - $this->interestSettled;
            break;
            case '3':
                $interestDue = (float)$accured_interest - $this->penalAmount - $this->interestSettled;
            break;
        }
        return $interestDue;
    }

    private function getInterestRefundAmount(&$disbursal){
        if(in_array($disbursal->status_id, [config('lms.STATUS_ID.DISBURSED'), config('lms.STATUS_ID.PARTIALLY_PAYMENT_SETTLED')])){
            return  $disbursal->total_interest-($this->accuredInt-$this->penalAmount);
        }
        return 0;
    }

    private function getChargeSettled($transId){
        return Transactions::where('parent_trans_id','=',$transId)->sum('amount');
    }

    private function settleInterestDueAmount(&$disbursal){
        
        $interestDue = self::getInterestDueAmount($disbursal);

        $interestPaidAmt = ($this->balanceRepayAmount>=$interestDue)?$interestDue:$this->balanceRepayAmount;
        $this->balanceRepayAmount -= $interestPaidAmt;
        $this->disbursal['total_repaid_amt'] += $interestPaidAmt;
        if($interestPaidAmt>0){

            $interestPaidData = $this->createTransactionData($this->transDetails->user_id, [
                'amount' => $interestPaidAmt,
                'trans_date'=>$this->transDetails->trans_date,
                'disbursal_id'=>$disbursal->disbursal_id,
                'repay_trans_id'=>$this->transDetails->trans_id
            ], null, config('lms.TRANS_TYPE.INTEREST_PAID'), 0);
            
            $this->transaction['interestPaid'][$disbursal->disbursal_id] = $interestPaidData;
        }
    }

    private function settlePrincipalAmount(&$disbursal){
        $balancePrincipalAmt = (float)$disbursal->principal_amount-(float)$this->principalSettled;
                  
        if($balancePrincipalAmt>0 && $this->balanceRepayAmount > 0)
        {
            $principalPaidAmt = ($this->balanceRepayAmount>=$balancePrincipalAmt)?$balancePrincipalAmt:$this->balanceRepayAmount;
            $this->isPrincipalSettle = ($this->balanceRepayAmount>=$balancePrincipalAmt)?2:1;

            $this->balanceRepayAmount -= $principalPaidAmt;
            $this->disbursal['total_repaid_amt'] += $principalPaidAmt;
            $this->disbursal['settlement_amount'] += $principalPaidAmt;
            $this->disbursal['status_id'] = ($this->isPrincipalSettle == 2)?config('lms.STATUS_ID.PAYMENT_SETTLED'):config('lms.STATUS_ID.PARTIALLY_PAYMENT_SETTLED');
            
            $knockOffData = $this->createTransactionData($this->transDetails->user_id, [
                'amount' =>  $principalPaidAmt,
                'trans_date'=>$this->transDetails->trans_date,
                'disbursal_id'=>$disbursal->disbursal_id,
                'repay_trans_id'=>$this->transDetails->trans_id
            ], null, ($this->isPrincipalSettle==2)?config('lms.TRANS_TYPE.INVOICE_KNOCKED_OFF'):config('lms.TRANS_TYPE.INVOICE_PARTIALLY_KNOCKED_OFF'), 0);
            $this->transaction['knockOff'][$disbursal->disbursal_id] = $knockOffData;
        }
    }

    private function settleInterestRefund(&$disbursal){
        $interestRefund = self::getInterestRefundAmount($disbursal);

        if($interestRefund>0 && $this->isPrincipalSettle == 2)
        { 
            $refundData = $this->createTransactionData($this->transDetails->user_id, [
                'amount' => $interestRefund,
                'trans_date'=>$this->transDetails->trans_date,
                'disbursal_id'=>$disbursal->disbursal_id,
                'repay_trans_id'=>$this->transDetails->trans_id
            ], null,config('lms.TRANS_TYPE.INTEREST_REFUND'), 1);
            $this->transaction['interestRefund'][$disbursal->disbursal_id] = $refundData;
            $this->totalRefundAmount += $interestRefund;
            $this->disbursal['interest_refund'] = $interestRefund;
        }  
    }

    private function settleRepayPenalCharges(&$disbursal){
        $penalAmountDue = $this->penalAmount-$this->penalAmountSettled;

        $pipedAmt = 0;
        if($penalAmountDue>0 && isset($this->transaction['interestRefund'])){
            foreach($this->transaction['interestRefund'] as $disbursalID => $disburs){
                
                $balanceRefundAmt = $disburs['amount']-$disburs['settled_amount'];
                $pipedAmt += $balanceRefundAmt;
                $this->transaction['interestRefund'][$disbursalID]['settled_amount'] += $balanceRefundAmt;
                
                if($pipedAmt > 0 && $penalAmountDue <= $pipedAmt)
                break;
            }
        }
        $principalPenalAmountDue = 0;
        if($pipedAmt < $penalAmountDue && $this->balanceRepayAmount > 0){

            $principalPenalAmountDue = $penalAmountDue-$pipedAmt; 
            $principalPenalAmountDue = ($this->balanceRepayAmount>=$principalPenalAmountDue)?$principalPenalAmountDue:$this->balanceRepayAmount;
            $this->balanceRepayAmount -= $principalPenalAmountDue;
            $pipedAmt += $principalPenalAmountDue;
        }

        if($penalAmountDue>0 && $pipedAmt>0)
        {
            
            if($pipedAmt >= $penalAmountDue){
                $overduePaidAmt = $penalAmountDue;
            }else{
                $overduePaidAmt = $pipedAmt;
            }

            $pipedAmt -= $overduePaidAmt;
            $this->disbursal['total_repaid_amt'] += $overduePaidAmt;

            $overdueData = $this->createTransactionData($this->transDetails->user_id, [
                'amount' => $overduePaidAmt,
                'trans_date'=>$this->transDetails->trans_date,
                'disbursal_id'=>$disbursal->disbursal_id,
                'repay_trans_id'=>$this->transDetails->trans_id
            ], null, config('lms.TRANS_TYPE.INTEREST_OVERDUE'), 0);
            $this->transaction['overdue'][$disbursal->disbursal_id] = $overdueData;
        }
    }
    
    private function settleRepayCharges($charges){
        foreach ($charges as $key => $chargeDetail) {

            $settledChargeAmount = self::getChargeSettled($chargeDetail['trans_id']);

            $balanceChargeAmount = $chargeDetail['amount'] - $settledChargeAmount;

            $pipedAmt = 0;
            if($balanceChargeAmount >0 && isset($this->transaction['interestRefund'])){
                foreach($this->transaction['interestRefund'] as $disbursalID => $disbursal){
                    $balanceRefundAmt = $disbursal['amount']-$disbursal['settled_amount'];
                    $pipedAmt += $balanceRefundAmt;
                    $this->transaction['interestRefund'][$disbursalID]['settled_amount'] += $balanceRefundAmt;
                    if($pipedAmt > 0 && $balanceChargeAmount <= $pipedAmt)
                    break;
                }
            }
            $balanceChargeAmountDue = 0;
            if($pipedAmt < $balanceChargeAmount && $this->balanceRepayAmount > 0){

                $balanceChargeAmountDue = $balanceChargeAmount-$pipedAmt; 
                $balanceChargeAmountDue = ($this->balanceRepayAmount>=$balanceChargeAmountDue)?$balanceChargeAmountDue:$this->balanceRepayAmount;
                $this->balanceRepayAmount -= $balanceChargeAmountDue;
                $pipedAmt += $balanceChargeAmountDue;
            }
            
            if($pipedAmt>0){
                $chargePaidAmt = ($balanceChargeAmount <= $pipedAmt)?$balanceChargeAmount:$pipedAmt;
                $pipedAmt -= $chargePaidAmt; 
                $chargesSettledData = $this->createTransactionData($this->transDetails->user_id, [
                    'amount' => $chargePaidAmt,
                    'trans_date'=>$this->transDetails->trans_date,
                    'repay_trans_id'=>$this->transDetails->trans_id,
                    'parent_trans_id'=>$chargeDetail['trans_id']
                ], null, $chargeDetail['trans_type'], 0);
                $this->transaction['charges'][$chargeDetail['trans_id']] = $chargesSettledData;
            }
        }
    }

    private function settleDisbursal($disbursalDetail){
        self::setDisbursalId($disbursalDetail);
        self::setPenalDays($disbursalDetail);
        self::setPenalAmount($disbursalDetail);
        self::setAccuredInt($disbursalDetail);
        //self::setMarginAmount($disbursalDetail);
        self::setPenalAmountSettled($disbursalDetail);
        self::setMarginSettled($disbursalDetail);
        self::setInterestSettled($disbursalDetail);
        self::setPrincipalSettled($disbursalDetail);
        self::setPaymentFreq($disbursalDetail);

        $this->disbursal = [
            'total_repaid_amt'=>(float)$disbursalDetail->total_repaid_amt,
            'interest_refund'=>(float)$disbursalDetail->interest_refund,
            'settlement_amount'=>(float)$disbursalDetail->settlement_amount,
            'status_id'=>$disbursalDetail->status_id,
            'surplus_amount'=>(float)$disbursalDetail->surplus_amount,
            'accured_interest'=> $this->accuredInt,
            'penalty_amount'=> $this->penalAmount,
            'penal_days'=> $this->penalDays,
        ];
        
        self::settleInterestDueAmount($disbursalDetail);
        self::settlePrincipalAmount($disbursalDetail);
        self::settleInterestRefund($disbursalDetail);
    }

    private function settleCharges(){
        self::setRepayBeforeCharges($this->transDetails->user_id);
        self::setRepayAfterCharges($this->transDetails->user_id);

        self::settleRepayCharges($this->repayBeforeCharges);
        foreach ($this->disbursalData as $key => $disbursalDetail) {
            if($disbursalDetail->count() > 0)
            self::setAccuredInt($disbursalDetail);
            self::setPenalAmount($disbursalDetail);
            self::setPenalAmountSettled($disbursalDetail);
            self::settleRepayPenalCharges($disbursalDetail);
        }
        self::settleRepayCharges($this->repayAfterCharges);
    }

    private function settleMargin(&$disbursal){
        self::setMarginAmount($disbursal);
        self::setMarginSettled($disbursal);
        $marginAmount = $this->marginAmount-$this->marginSettled;

        $pipedAmt = 0;
        if($marginAmount > 0 && isset($this->transaction['interestRefund'])){
            foreach($this->transaction['interestRefund'] as $disbursalID => $disburs){
                
                $balanceRefundAmt = $disburs['amount']-$disburs['settled_amount'];
                $pipedAmt += $balanceRefundAmt;
                $this->transaction['interestRefund'][$disbursalID]['settled_amount'] += $balanceRefundAmt;
                
                if($pipedAmt > 0 && $marginAmount <= $pipedAmt)
                break;
            }
        }
        $marginAmountDue=0;
        if($pipedAmt < $marginAmount && $this->balanceRepayAmount > 0){

            $marginAmountDue = $marginAmount-$pipedAmt; 
            $marginAmountDue = ($this->balanceRepayAmount>=$marginAmountDue)?$marginAmountDue:$this->balanceRepayAmount;
            $this->balanceRepayAmount -= $marginAmountDue;
            $pipedAmt += $marginAmountDue;
        }

        if($marginAmountDue>0 && $pipedAmt>0)
        {
            
            if($pipedAmt >= $marginAmount){
                $overduePaidAmt = $marginAmount;
            }else{
                $overduePaidAmt = $pipedAmt;
            }

            $pipedAmt -= $overduePaidAmt;
            $this->disbursal['total_repaid_amt'] += $overduePaidAmt;

            $overdueData = $this->createTransactionData($this->transDetails->user_id, [
                'amount' => $overduePaidAmt,
                'trans_date'=>$this->transDetails->trans_date,
                'disbursal_id'=>$disbursal->disbursal_id,
                'repay_trans_id'=>$this->transDetails->trans_id
            ], null, config('lms.TRANS_TYPE.MARGIN'), 1);
            $this->transaction['margin'][$disbursal->disbursal_id] = $overdueData;
        }
    }

    private function settleAllMargin(){
        foreach ($this->disbursalData as $key => $disbursalDetail) {
            if($disbursalDetail->count() > 0)
            self::settleMargin($disbursalDetail);
        }
    }

    private function saveTransactions(){
        if(!empty($this->transaction['disbursal']))
        foreach ($this->transaction['disbursal'] as $dibursalKey => $dibursalValue) {
            $this->lmsRepo->saveDisbursalRequest($dibursalValue, ['disbursal_id' => $dibursalKey]);
        }

        if(!empty($this->transaction['interestPaid']))
        foreach($this->transaction['interestPaid'] as $interestPaidValue){
            $this->lmsRepo->saveTransaction($interestPaidValue);
        }

        if(!empty($this->transaction['knockOff']))
        foreach($this->transaction['knockOff'] as $knockOffValue){
            $this->lmsRepo->saveTransaction($knockOffValue);
        }

        if(!empty($this->transaction['interestRefund']))
        foreach ($this->transaction['interestRefund'] as $interestRefundValue){
            $this->lmsRepo->saveTransaction($interestRefundValue);
        }

        if(!empty($this->transaction['overdue']))
        foreach ($this->transaction['overdue'] as $overdueValue) {
            $this->lmsRepo->saveTransaction($overdueValue);
        }

        if(!empty($this->transaction['margin']))
        foreach ($this->transaction['margin'] as $overdueValue) {
            $this->lmsRepo->saveTransaction($overdueValue);
        }
        
        if(!empty($this->transaction['charges']))
        foreach($this->transaction['charges'] as $chargesValue){
            $this->lmsRepo->saveTransaction($chargesValue);
        }
        
        // if(!empty($this->transaction['nonFactoredAmt']))
        // foreach($this->transaction['nonFactoredAmt'] as $nonFactoredAmtValue){
        //     $this->lmsRepo->saveTransaction($nonFactoredAmtValue);
        // }
    }
}