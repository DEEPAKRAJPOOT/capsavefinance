<?php

namespace App\Inv\Repositories\Models\Lms;

use Helpers;
use Illuminate\Database\Eloquent\Model;
use App\Inv\Repositories\Factory\Models\BaseModel;

class CustomerTransactionSOA extends BaseModel
{
    /* The database table used by the model.
    *
    * @var string
    */
    protected $table = 'customer_transaction_soa';

    /**
    * Maintain created_at and updated_at automatically
    *
    * @var boolean
    */
    public $timestamps = true;

    /**
    * Maintain created_by and updated_by automatically
    *
    * @var boolean
    */
    public $userstamps = true;

    /**
    * The attributes that are mass assignable.
    *
    * @var array
    */
    protected $fillable = [
        'user_id',
        'trans_id',
        'trans_date',
        'trans_name',
        'value_date',
        'trans_type',
        'batch_no',
        'invoice_no',
        'narration',
        'currency',
        'debit_amount',
        'credit_amount',
        'balance_amount',
        'created_at',
        'created_by',
        'updated_at',
        'updated_by'
    ];

    public function transaction(){
        return $this->belongsTo('App\Inv\Repositories\Models\Lms\Transactions', 'trans_id', 'trans_id');
    }

    public function user(){
        return $this->belongsTo('App\Inv\Repositories\Models\User','user_id','user_id');
    }

    public function lmsUser(){
        return $this->belongsTo('App\Inv\Repositories\Models\LmsUser','user_id','user_id');
    }

    public static function createTransactionSOADetails($transaction){
        $latestCustomerTranSoa  = self::where('user_id', $transaction->user_id)->orderByDesc('id')->first();
        $balance = $latestCustomerTranSoa->balance_amount ?? 0;
        $customerTranSoa = new CustomerTransactionSOA([
        'user_id'         =>  $transaction->user_id,
        'trans_id'        =>  $transaction->trans_id,
        ]);
        $customerTranSoa->fill(self::makeCustomerTransactionSOA($transaction, $balance));
        $customerTranSoa->save();
    }

    public static function updateTransactionSOADetails($userId){
        $customerAllTranSoa = self::where('user_id', $userId)->get();
        $balance = 0;
        foreach ($customerAllTranSoa as $customerTranSoa) {
            if($customerTranSoa->transaction){
                $data    = self::makeCustomerTransactionSOA($customerTranSoa->transaction, $balance);
                $balance = $data['balance_amount'];
                self::where([
                    'user_id'         =>  $customerTranSoa->user_id,
                    'trans_id'        =>  $customerTranSoa->trans_id,
                    ])->update($data);
            }
        }
    }

    public static function deleteTransactionSOADetails($transaction){
        $tranSoa = self::where([
            'user_id'   => $transaction->user_id,
            'trans_id'  => $transaction->trans_id,
        ])->first();

        if ($tranSoa)
        $tranSoa->delete();
    }

    public static function forceDeletedTransactionSOADetails($transaction){
        //
    }

    private static function makeCustomerTransactionSOA($transaction, $balance){
        $currency = '';
        $debitAmount = 0;
        $creditAmount = 0;
        $transDate = $transaction->created_at;
        if($transaction->payment_id && in_array($transaction->trans_type,[config('lms.TRANS_TYPE.REPAYMENT'),config('lms.TRANS_TYPE.FAILED')])){
            //
        }else{
            $currency = 'INR';
        }
        if($transaction->entry_type == 0 && $transaction->amount > 0){
            $balance += $debitAmount = $transaction->amount;
        }
        if($transaction->entry_type == 1 && $transaction->amount > 0){
            $balance -= $creditAmount = $transaction->amount;
        }
        return [
            'value_date'      =>  $transaction->trans_date,
            'trans_type'      =>  $transaction->trans_type,
            'trans_name'      =>  $transaction->transName,
            'invoice_no'      =>  $transaction->invoiceno,
            'batch_no'        =>  $transaction->batchNo,
            'narration'       =>  $transaction->narration,
            'trans_date'      =>  $transDate,
            'currency'        =>  $currency,
            'debit_amount'    =>  $debitAmount,
            'credit_amount'   =>  $creditAmount,
            'balance_amount'  =>  $balance,
        ];
    }

    public static function getSoaList(){
        return self::whereHas('transaction', function ($q) {
            $q->where('soa_flag', 1);
        })
        ->orderBy('user_id', 'asc')
        #->orderBy('value_date', 'asc')
        ->orderBy('trans_id', 'asc');
    }

    public static function getConsolidatedSoaList(){
        return self::with('transaction.invoiceDisbursed.disbursal','transaction.payment')
        ->orderBy('user_id', 'asc')
        ->orderBy('value_date', 'asc')
        ->orderBy('trans_id', 'asc');
    }
    
    public function getSoaBackgroundColorAttribute(){
        $color = '';
        $trans = $this->transaction;
        switch ($trans->apportionment->apportionment_type ?? null) {
            case '1':
                if($trans->payment_id){
                    if($trans->trans_type == config('lms.TRANS_TYPE.REPAYMENT'))
                    $color = '#f3c714';
                    elseif(!in_array($trans->trans_type, [config('lms.TRANS_TYPE.TDS')]))
                    $color = '#ffe787';
                }else{
                    $color = '#fdeeb3';
                }
                break;
            case '2':
                if($trans->payment_id){
                    if($trans->linkTransactions->trans_type == config('lms.TRANS_TYPE.REPAYMENT'))
                    $color = '#ff6767';
                    elseif(!in_array($trans->linkTransactions->trans_type, [config('lms.TRANS_TYPE.TDS')]))
                    $color = '#ffcccc';
                }else{
                    $color = '#ffdede';
                }
                break;
            
            default:
                    $color = '';
                break;
        }
        return $color;

        $color = '';
        if($this->transaction->payment_id){
            if($this->trans_type == config('lms.TRANS_TYPE.REPAYMENT'))
            $color = '#f3c714';
            elseif(!in_array($this->trans_type, [config('lms.TRANS_TYPE.TDS')]))
            $color = '#ffe787';
        }
        return $color;
    }

    // public function getCustomerSoaBalanceAttribute(){
        
    //     $transaction = self::where('user_id',$this->user_id)->whereDate('value_date','<=',$this->value_date)->where('trans_id','<=',$this->trans_id)->whereHas('transaction', function($query){
    //         $query->where('soa_flag',1);
    //     });

    //     $crTrans = clone $transaction;
    //     $drTrans = clone $transaction;

    //     $dr = $drTrans->sum('debit_amount');
    //     $cr = $crTrans->sum('credit_amount');

    //     return round(($dr - $cr),2);
    // }

    // public function getConsolidatedSoaBalanceAttribute(){
        
    //     $transaction = self::where('user_id',$this->user_id)->whereDate('value_date','<=',$this->value_date)->where('trans_id','<=',$this->trans_id);

    //     $crTrans = clone $transaction;
    //     $drTrans = clone $transaction;

    //     $dr = $drTrans->sum('debit_amount');
    //     $cr = $crTrans->sum('credit_amount');

    //     return round(($dr - $cr),2);
    // }
}