<?php

namespace App\Inv\Repositories\Models\Lms;

use DB;
use App\Inv\Repositories\Factory\Models\BaseModel;
use App\Inv\Repositories\Entities\User\Exceptions\BlankDataExceptions;
use App\Inv\Repositories\Entities\User\Exceptions\InvalidDataTypeExceptions;

class Transactions extends BaseModel {
    /* The database table used by the model.
     *
     * @var string
     */

    protected $table = 'transactions';

    /**
     * Custom primary key is set for the table
     *
     * @var integer
     */
    protected $primaryKey = 'trans_id';

    /**
     * Maintain created_at and updated_at automatically
     *
     * @var boolean
     */
    public $timestamps = false;

    /**
     * Maintain created_by and updated_by automatically
     *
     * @var boolean
     */
    public $userstamps = false;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'gl_flag',
        'soa_flag',
        'user_id',
        'virtual_acc_id',
        'trans_date',
        'trans_type',
        'pay_from',
        'amount',
        'gst',
        'cgst',
        'sgst',
        'igst',
        'entry_type',
        'tds_per',
        'mode_of_pay',
        'comment',
        'utr_no',
        'cheque_no',
        'unr_no',
        'txn_id',        
        'created_at',
        'created_by',
    ];

    /**
     * Save Transactions
     * 
     * @param array $transactions
     * @return mixed
     * @throws InvalidDataTypeExceptions
     */
    public static function saveTransaction($transactions,$whereCondition=[])
    {
        if (!is_array($transactions)) {
            throw new InvalidDataTypeExceptions(trans('error_message.invalid_data_type'));
        }
        
        if (!isset($transactions['created_at'])) {
            $transactions['created_at'] = \Carbon\Carbon::now()->format('Y-m-d h:i:s');
        }
        if (!isset($transactions['created_by'])) {
            $transactions['created_at'] = \Auth::user()->user_id;
        }        
        
        if (!empty($whereCondition)) {
            return self::where($whereCondition)->update($transactions);
        } else if (!isset($transactions[0])) {
            return self::create($transactions);
        } else {
            return self::insert($transactions);
        }
    }


    
    
    /**
     * Get Transactions
     *      
     * @param array $whereCondition | optional
     * @return mixed
     * @throws InvalidDataTypeExceptions
     */
    public static function getTransactions($whereCondition=[])
    {
        if (!is_array($whereCondition)) {
            throw new InvalidDataTypeExceptions(trans('error_message.invalid_data_type'));
        }
        
        $query = self::select('*');
                
        if (!empty($whereCondition)) {
            $query->where($whereCondition);
        }
        
        $result = $query->get();
        return $result;
    }
    
    
    /*** save repayment transaction details for invoice  **/
    public static function saveRepaymentTrans($attr)
    {
          return self::create($attr);
    }
    
    
    /*** get all transaction  **/
    public static function getAllManualTransaction()
    {
          return self::with('disburse')->where('trans_by','!=',NULL)->orderBy('trans_id','DESC');
    }
    
    function disburse()
    {
       return $this->hasMany('App\Inv\Repositories\Models\Lms\Disbursal','user_id','user_id');
    }      
   
     
}
