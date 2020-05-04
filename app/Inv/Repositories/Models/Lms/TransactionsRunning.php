<?php
namespace App\Inv\Repositories\Models\Lms;

use DB;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use App\Inv\Repositories\Factory\Models\BaseModel;
use App\Inv\Repositories\Entities\User\Exceptions\BlankDataExceptions;
use App\Inv\Repositories\Entities\User\Exceptions\InvalidDataTypeExceptions;

class TransactionsRunning extends BaseModel {
    /* The database table used by the model.
     *
     * @var string
     */

    private static $balacnce = 0;
    protected $table = 'transactions_running';

    /**
     * Custom primary key is set for the table
     *
     * @var integer
     */
    protected $primaryKey = 'trans_running_id';

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
        'invoice_disbursed_id',
        'user_id',
        'trans_date',
        'trans_type',
        'amount',
        'entry_type',
        'is_posted',
        'created_at',
        'created_by'
    ];


    public function invoiceDisbursed(){
        return $this->belongsTo('App\Inv\Repositories\Models\Lms\InvoiceDisbursed','invoice_disbursed_id','invoice_disbursed_id');
    }
        
    public function user(){
        return $this->belongsTo('App\Inv\Repositories\Models\User','user_id','user_id');
    }
    
    public function lmsUser(){
        return $this->belongsTo('App\Inv\Repositories\Models\LmsUser','user_id','user_id');
    }

    public function transType(){
       return $this->belongsTo('App\Inv\Repositories\Models\Lms\TransType', 'trans_type', 'id');
    }   

    public function accruedInterest(){
        return $this->hasMany('App\Inv\Repositories\Models\Lms\InterestAccrual','invoice_disbursed_id','invoice_disbursed_id');
    }

    public function setAmountAttribute(){
        return round(($this->amount - $this->getsettledAmtAttribute()),2);
    }


    /**
     * Save Transactions
     * 
     * @param array $transactions
     * @return mixed
     * @throws InvalidDataTypeExceptions
     */
    public static function saveTransactionRunning($transactions,$whereCondition=[])
    {
        if (!is_array($transactions)) {
            throw new InvalidDataTypeExceptions(trans('error_message.invalid_data_type'));
        }
        
        if (!isset($transactions['created_at'])) {
            $transactions['created_at'] = \Carbon\Carbon::now()->setTimezone(config('common.timezone'))->format('Y-m-d h:i:s');
        }
        if (!isset($transactions['created_by'])) {
            $transactions['created_by'] = \Auth::user()->user_id;
        }        
        
        if (!empty($whereCondition)) {
            return self::where($whereCondition)->update($transactions);
        } else if (!isset($transactions[0])) {
            return self::create($transactions);
        } else {            
            return self::insert($transactions);
        }
    }

}
