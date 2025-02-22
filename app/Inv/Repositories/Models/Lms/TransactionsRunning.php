<?php
namespace App\Inv\Repositories\Models\Lms;

use DB;
use Helpers;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use App\Inv\Repositories\Models\Lms\Transactions;
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
        'invoice_disbursed_id',
        'user_id',
        'from_date',
        'trans_date',
        'due_date',
        'trans_type',
        'amount',
        'entry_type',
        'sys_created_at',
        'sys_updated_at',
        'created_at',
        'created_by',
        'updated_at',
        'updated_by'
    ];

    public function transaction(){
        return $this->hasMany(Transactions::class,'trans_running_id','trans_running_id');
    }

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

    public function getTransNameAttribute(){
        $name = ' '; 
       
        if(in_array($this->trans_type,[config('lms.TRANS_TYPE.WAVED_OFF'),config('lms.TRANS_TYPE.TDS'),config('lms.TRANS_TYPE.REVERSE'),config('lms.TRANS_TYPE.REFUND')])){
            if($this->parent_trans_id){
                $parentTrans = self::find($this->parent_trans_id);
                $name .= $parentTrans->transType->trans_name.' ';
                if($this->link_trans_id){
                    $linkTrans = self::find($this->link_trans_id);
                    if(in_array($linkTrans->trans_type,[config('lms.TRANS_TYPE.WAVED_OFF'),config('lms.TRANS_TYPE.TDS'),config('lms.TRANS_TYPE.REVERSE')]))
                        $name .= $linkTrans->transType->trans_name.' ';
                }
            }
        }

        if($this->entry_type == 0){
            $name .= $this->transType->debit_desc;
        }elseif($this->entry_type == 1){
            $name .= $this->transType->credit_desc;
        }
        return $name;
    }

    public function getOutstandingAttribute(){
        $amount = $this->amount;
       
        $dr = $this->transaction
            ->where('entry_type','=','0')
            ->whereNull('payment_id')
            ->whereNull('link_trans_id')
            ->whereNull('parent_trans_id')
            ->sum('amount');

        $cr = $this->transaction
            ->where('entry_type','=','1')
            ->where('trans_type','=',config('lms.TRANS_TYPE.CANCEL'))
            ->sum('settled_outstanding');

        $amount -= round((round($dr,2) - round($cr,2)),2);
        return round($amount,2);
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
        
        $transactions['sys_updated_at'] = Helpers::getSysStartDate();
        if (!empty($whereCondition)) {
            return self::where($whereCondition)->update($transactions);
        } else {
            $transactions['sys_created_at'] = Helpers::getSysStartDate();
            return self::create($transactions);
        } 
    }

    public static function getRunningTrans($userId){
        return self::where('user_id','=',$userId)
        //->where('soa_flag','=',0)
        ->orderBy('trans_date','asc')
        ->get()
        ->filter(function($item) {
            return $item->outstanding > 0;
        });
    }

    public static function getUnsettledRunningTrans(){
        $invDisb = self::whereHas('invoiceDisbursed', function($q){
            $q->whereHas('invoice', function($q2){
                $q2->whereHas('program_offer', function($q3){
                    $q3->where('payment_frequency',2);
                });
            });
        })
        ->get()
        ->filter(function($item) { 
            return round($item->outstanding,2) > 0.00; 
        })
        ->pluck('invoice_disbursed_id')->toArray();
        return array_unique($invDisb);
    }

}
