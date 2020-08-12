<?php

namespace App\Helpers;
use DB;
use Carbon\Carbon;
use InvalidArgumentException;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use App\Inv\Repositories\Models\Payment;
use App\Inv\Repositories\Models\Lms\Transactions;
use App\Inv\Repositories\Models\Master\RefundType;
use App\Inv\Repositories\Models\Lms\Refund\RefundReq;
use App\Inv\Repositories\Models\Lms\Refund\RefundReqLog;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use App\Inv\Repositories\Models\Lms\Refund\RefundReqType;
use App\Inv\Repositories\Models\Lms\Refund\RefundReqTrans;

class RefundHelper{

    public static function calculateRefund(int $paymentId){
        $repayment = Payment::where('is_settled','=', 1)->where('payment_id','=',$paymentId)->first();
        if (!$repayment) {
            throw new InvalidArgumentException("Payment Detail is not Valid/Settled");
        }

        $repaymentTrails = Transactions::where('payment_id','=',$paymentId)->whereNotIn('trans_type',[config('lms.TRANS_TYPE.REPAYMENT')])->get();
        $interestRefundTotal = 0;
        $interestOverdueTotal = 0;
        $marginTotal = 0;
        $nonFactoredAmount = 0;
        $totalTdsAmount = 0;
        
        foreach ($repaymentTrails as $key => $trans) {
            if($trans->entry_type == '1' && $trans->trans_type == config('lms.TRANS_TYPE.REFUND')){
                $interestRefundTotal +=$trans->refundoutstanding;
            }elseif($trans->entry_type == '1' && $trans->trans_type == config('lms.TRANS_TYPE.INTEREST_OVERDUE')){
                $interestOverdueTotal +=$trans->refundoutstanding;
            }elseif($trans->entry_type == '1' && $trans->trans_type == config('lms.TRANS_TYPE.MARGIN')){
                $marginTotal +=$trans->refundoutstanding;
            }elseif($trans->entry_type == '1' && $trans->trans_type == config('lms.TRANS_TYPE.NON_FACTORED_AMT')){
                $nonFactoredAmount +=$trans->refundoutstanding;
            }elseif($trans->entry_type == '1' && $trans->trans_type == config('lms.TRANS_TYPE.TDS')){
                $totalTdsAmount +=$trans->refundoutstanding;
            }
        }
        
        $refundableAmount = $nonFactoredAmount+$marginTotal+$interestRefundTotal+$totalTdsAmount;

        return [
        'repaymentTrails' => $repaymentTrails, 
        'repayment'=>$repayment,
        'factoredAmount' => $repayment->amount,
        'nonFactoredAmount' => $nonFactoredAmount,
        'interestRefund'=>$interestRefundTotal,
        'interestOverdue'=>$interestOverdueTotal,
        'marginTotal'=>$marginTotal,
        'refundableAmount'=>$refundableAmount,
        'paymentId' => $paymentId
        ]; 
    } 

    public static function createRefundRequest(int $paymentId){
        try{
            $refundReqId = null;
            $data = self::calculateRefund($paymentId);
            $request = self::createRequest($data['repayment'],$data['refundableAmount']);
        
            if($request){
            
                $refundReqId = $request->refund_req_id;
            
                $refundTypeAmt = [
                    'TOTAL_FACTORED'=>$data['factoredAmount'],
                    'NON_FACTORED'=>$data['nonFactoredAmount'],
                    'OVERDUE_INTEREST'=>$data['interestOverdue'],
                    'INTEREST_REFUND'=>$data['interestRefund'],
                    'MARGIN_RELEASED'=>$data['marginTotal'],
                    'TOTAL_REFUNDABLE_AMT'=>$data['refundableAmount'],
                ];
                $logData = [
                    'status'=>'1', 
                    'comment'=>'', 
                    'wf_stage_id'=>'1',
                    'is_active'=>'1',
                ];
                self::createLog($logData, $refundReqId);
                self::createType($refundTypeAmt, $refundReqId);
                self::createTrans($data['repaymentTrails'], $refundReqId);
            }
            return $data;
        } catch (Exception $ex) {
            throw new Error('Something wrong please try later');
        }
    }

    public static function createRequest($payment, $amount){
        $curData = \Carbon\Carbon::now(config('common.timezone'))->format('Y-m-d h:i:s');
        return RefundReq::saveRefundReqData([
            'ref_code'=>'',  
            'payment_id'=>$payment->payment_id,  
            'refund_date'=>$curData,  
            'refund_amount'=>$amount
        ]);
    }

    public static function createLog(array $data, int $refundReqId){
        $curUserId  = \Auth::user()->user_id;
        return RefundReqLog::saveRefundReqLogData([
            'refund_req_id'=>$refundReqId,
            'assigned_user_id'=>$curUserId, 
            'status'=>$data['status'], 
            'comment'=>$data['comment'], 
            'wf_stage_id'=>$data['wf_stage_id'],
            'is_active'=>$data['is_active']
        ]);
    }
   
    public static function createTrans($transactions, int $refundReqId){
        $response = new collection(); 
        foreach ($transactions as $key => $value) {
            $resRefReqTran = RefundReqTrans::saveRefundReqTransData([
                'refund_req_id' => $refundReqId,
                'trans_id' => $value->trans_id,
                'req_amount' => $value->amount
            ]);
            $response->push($resRefReqTran);
        }
        return $response;
    }

    public static function createType(array $refundTypeAmt, int $refundReqId){
        $refundTypes = RefundType::get();
        $response = new collection();
        foreach ($refundTypes as $key => $value) {
            if(isset($refundTypeAmt[$value->name])){
                $resRefReqType = RefundReqType::saveRefundReqTypeData([ 
                    'refund_req_id' => $refundReqId,
                    'refund_type_id' => $value->id, 
                    'amount' => $refundTypeAmt[$value->name]
                ]);
                $response->push($resRefReqType);
            }
        }
        return $response;
    }

    public static function getRequest(int $refundReqId){
        $refundReq = RefundReq::find($refundReqId);
        $payment = Payment::find($refundReq->payment_id);
        //$refundTrans = Transactions::where('payment_id',$refundReq->payment_id)->get();
        $refundTrans = Transactions::whereHas('refundReqTrans', function($query)use($refundReqId){
            $query->where('refund_req_id',$refundReqId);
        })->get();
        $refundTypes = RefundReqType::where('refund_req_id',$refundReq->refund_req_id)->with('refundType')->get();
        $refundTypeAmt = [];
        foreach ($refundTypes as $key => $value) {
            $refundTypeAmt[$value->refundType->name] = $value->amount; 
        }

        return [
            'repaymentTrails' => $refundTrans, 
            'repayment'=>$payment,
            'factoredAmount' =>$refundTypeAmt['TOTAL_FACTORED'] ?? 0,
            'nonFactoredAmount' =>$refundTypeAmt['NON_FACTORED'] ?? 0,
            'interestRefund'=>$refundTypeAmt['INTEREST_REFUND'] ?? 0,
            'interestOverdue'=>$refundTypeAmt['OVERDUE_INTEREST'] ?? 0,
            'marginTotal'=>$refundTypeAmt['MARGIN_RELEASED'] ?? 0,
            'refundableAmount'=>$refundTypeAmt['TOTAL_REFUNDABLE_AMT'] ?? 0,
            'paymentId' => $refundReq->payment_id
        ]; 
    }

    public static function updateRequest(int $refundReqId, int $curStatus, int $newStatus){
        $updatedRecord = null;
        $refundReq = RefundReq::where('refund_req_id','=',$refundReqId)
        ->where('status','=',$curStatus)
        ->first();
        if($refundReq && $newStatus){
            $refundReq->status = $newStatus;
            $updatedRecord = $refundReq->save();
        }
        return $updatedRecord;
    }

}