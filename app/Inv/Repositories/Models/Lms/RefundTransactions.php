<?php

namespace App\Inv\Repositories\Models\Lms;

use DB;
use Auth;
use App\Inv\Repositories\Factory\Models\BaseModel;
use App\Inv\Repositories\Entities\User\Exceptions\BlankDataExceptions;
use App\Inv\Repositories\Entities\User\Exceptions\InvalidDataTypeExceptions;
use App\Inv\Repositories\Models\Lms\Transactions;

class RefundTransactions extends BaseModel {
    /* The database table used by the model.
     *
     * @var string
     */

    protected $table = 'lms_refund_transactions';

    /**
     * Custom primary key is set for the table
     *
     * @var integer
     */
    protected $primaryKey = 'refund_trans_id';

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
        'refund_trans_id',  
        'req_id',  
        'trans_id',  
        'created_at',  
        'created_by',
    ];
    
    public static function saveRefundTransactionData($data)
    {
        //Check $refundData is not an array
        if (!is_array($data)) {
            throw new InvalidDataTypeExceptions(trans('error_messages.invalid_data_type'));
        }        
        
        return self::insert($data);
    }

    public static function saveRefundTransactions(int $trans_id, int $req_id){

        $transactions = Transactions::select('trans_id')->where('repay_trans_id','=',$trans_id)
                        ->whereIn('trans_type',[config('lms.TRANS_TYPE.INTEREST_REFUND'),config('lms.TRANS_TYPE.MARGIN'),config('lms.TRANS_TYPE.NON_FACTORED_AMT')])
                        ->get();
        $curData = \Carbon\Carbon::now()->format('Y-m-d h:i:s');

        foreach ($transactions as $key => $trans) {
            $data = [  
            'req_id'  => $req_id,
            'trans_id'  =>  $trans->trans_id, 
            'created_by' => Auth::user()->user_id,
            'created_at' => $curData
            ]; 
            self::saveRefundTransactionData($data);
        }
    }

    protected function getRefundTransactions(int $req_id){
        return  self::select('*')
                        ->join('transactions','transactions.trans_id','lms_refund_transactions.trans_id')
                        ->where('req_id','=',$req_id)
                        ->get();   
    }
}
