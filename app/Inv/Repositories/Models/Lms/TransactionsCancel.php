<?php
namespace App\Inv\Repositories\Models\Lms;

use DB;
use Helpers;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use App\Inv\Repositories\Factory\Models\BaseModel;
use App\Inv\Repositories\Entities\User\Exceptions\BlankDataExceptions;
use App\Inv\Repositories\Entities\User\Exceptions\InvalidDataTypeExceptions;

class TransactionsCancel extends BaseModel {
    /* The database table used by the model.
     *
     * @var string
     */

    private static $balacnce = 0;
    protected $table = 'transactions_cancel';

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
        'payment_id',
        'link_trans_id',
        'parent_trans_id',
        'invoice_disbursed_id',
        'trans_running_id',
        'user_id',
        'trans_date',
        'trans_type',
        'trans_mode',
        'amount',
        'entry_type',
        'gst',
        'gst_per',
        'chrg_gst_id',
        'tds_per',
        'gl_flag',
        'soa_flag',
        'trans_by',
        'pay_from',
        'is_settled',
        'is_posted_in_tally',
        'is_invoice_generated',
        'sys_created_at',
        'sys_updated_at',
        'created_at',
        'created_by',
        'updated_at',
        'updated_by'
    ];

    public function payment(){
        return $this->belongsTo('App\Inv\Repositories\Models\Payment','payment_id','payment_id');
    } 

    public function invoiceDisbursed(){
        return $this->belongsTo('App\Inv\Repositories\Models\Lms\InvoiceDisbursed','invoice_disbursed_id','invoice_disbursed_id');
    }

    public function biz() {
       return $this->belongsTo('App\Inv\Repositories\Models\Business', 'biz_id');
    }

    public function disburse() {
       return $this->belongsTo('App\Inv\Repositories\Models\Lms\InvoiceDisbursed', 'invoice_disbursed_id');
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
  
    public function refundTransaction(){
        return $this->hasOne('App\Inv\Repositories\Models\Lms\RefundTransactions', 'new_trans_id', 'trans_id');
    }

    public function accruedInterest(){
        return $this->hasMany('App\Inv\Repositories\Models\Lms\InterestAccrual','invoice_disbursed_id','invoice_disbursed_id');
    }

    public function userInvTrans(){
        return $this->hasOne('App\Inv\Repositories\Models\Lms\UserInvoiceTrans','trans_id','trans_id');
    } 

    public function userInvParentTrans(){
        return $this->hasOne('App\Inv\Repositories\Models\Lms\UserInvoiceTrans','trans_id','parent_trans_id');
    }

    public function refundReqTrans(){
        return $this->hasOne('App\Inv\Repositories\Models\Lms\Refund\RefundReqTrans','trans_id','trans_id');
    }

    public function transRunning(){
        return $this->belongsTo('App\Inv\Repositories\Models\Lms\TransactionsRunning','trans_running_id','trans_running_id');
    }
}
