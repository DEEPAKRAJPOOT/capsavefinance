<?php

namespace App\Helpers;
use DB;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use App\Inv\Repositories\Models\Payment;
use App\Inv\Repositories\Models\Lms\Transactions;
use App\Inv\Repositories\Models\Master\RefundType;
use App\Inv\Repositories\Models\Lms\Refund\RefundReq;
use App\Inv\Repositories\Models\Lms\Refund\RefundReqLog;
use App\Inv\Repositories\Models\Lms\Refund\RefundReqType;
use App\Inv\Repositories\Models\Lms\Refund\RefundReqTrans;


class RefundHelper{

    public static function calculateRefund(int $paymentId){

        $repayment = Payment::where('is_settled','=', 1)->where('payment_id','=',$paymentId)->first();
        
        $repaymentTrails = Transactions::where('payment_id','=',$paymentId)->get();

        $interestRefundTotal = Transactions::where('payment_id','=',$paymentId)
        ->where('entry_type','=','1')
        ->where('trans_type','=',config('lms.TRANS_TYPE.REFUND'))
        ->sum('amount');

        $interestOverdueTotal = Transactions::where('payment_id','=',$paymentId)
        ->where('entry_type','=','1')
        ->where('trans_type','=',config('lms.TRANS_TYPE.INTEREST_OVERDUE'))
        ->sum('amount');
        
        $marginTotal = Transactions::where('payment_id','=',$paymentId)
        ->where('entry_type','=','1')
        ->where('trans_type','=',config('lms.TRANS_TYPE.MARGIN'))
        ->sum('amount');
        
        $nonFactoredAmount = Transactions::where('payment_id','=',$paymentId)
        ->where('entry_type','=','1')
        ->where('trans_type','=',config('lms.TRANS_TYPE.NON_FACTORED_AMT'))
        ->sum('amount');

        $totalTdsAmount = Transactions::where('payment_id','=',$paymentId)
        ->where('entry_type','=','1')
        ->where('trans_type','=',config('lms.TRANS_TYPE.TDS'))
        ->sum('amount');
        
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
        $refundReqId = null;
        $data = self::calculateRefund($paymentId);
        $request = self::createRequest($data['repayment']);
        
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
    }

    public static function createRequest($payment){
        $curData = \Carbon\Carbon::now(config('common.timezone'))->format('Y-m-d h:i:s');
        $curUserId  = \Auth::user()->user_id;
        return RefundReq::saveRefundReqData([
            'ref_code'=>'',  
            'payment_id'=>$payment->payment_id,  
            'refund_date'=>$curData,  
            'refund_amount'=>$payment->amount,  
            'created_at'=>$curData,
            'created_by'=>$curUserId, 
            'updated_at'=>$curData,
            'updated_by'=>$curUserId
        ]);
    }

    public static function createLog(array $data, int $refundReqId){
        $curData = \Carbon\Carbon::now(config('common.timezone'))->format('Y-m-d h:i:s');
        $curUserId  = \Auth::user()->user_id;

        RefundReqLog::saveRefundReqLogData([
            'refund_req_id'=>$refundReqId,
            'assigned_user_id'=>$curUserId, 
            'status'=>$data['status'], 
            'comment'=>$data['comment'], 
            'wf_stage_id'=>$data['wf_stage_id'],
            'is_active'=>$data['is_active'],
            'created_at'=>$curData,
            'created_by'=>$curUserId, 
            'updated_at'=>$curData,
            'updated_by'=>$curUserId
        ]);
    }
   
    public static function createTrans($transactions, int $refundReqId){
        $curData = \Carbon\Carbon::now(config('common.timezone'))->format('Y-m-d h:i:s');
        $curUserId  = \Auth::user()->user_id;

        foreach ($transactions as $key => $value) {
            RefundReqTrans::saveRefundReqTransData([
                'refund_req_id' => $refundReqId,
                'trans_id' => $value->trans_id,
                'req_amount' => $value->amount,
                'created_at' => $curData,
                'created_by' => $curUserId,
                'updated_at' => $curData,
                'updated_by' => $curUserId
            ]);
        }
    }

    public static function createType(array $refundTypeAmt, int $refundReqId){
        
        $refundTypes = RefundType::get();
        $curData = \Carbon\Carbon::now(config('common.timezone'))->format('Y-m-d h:i:s');
        $curUserId  = \Auth::user()->user_id;

        foreach ($refundTypes as $key => $value) {
            if(isset($refundTypeAmt[$value->name])){
                RefundReqType::saveRefundReqTypeData([ 
                    'refund_req_id' => $refundReqId,
                    'refund_type_id' => $value->id, 
                    'amount' => $refundTypeAmt[$value->name], 
                    'created_by' => $curUserId,
                    'created_at' => $curData,
                    'updated_by' => $curUserId,
                    'updated_at' => $curData
                ]);
            }
        }

        
    }


}