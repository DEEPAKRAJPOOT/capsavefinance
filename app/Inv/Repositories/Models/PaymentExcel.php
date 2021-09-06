<?php

namespace App\Inv\Repositories\Models;

use DB;
use Helpers;
use App\Inv\Repositories\Models\User;
use App\Inv\Repositories\Models\Business;
use App\Inv\Repositories\Factory\Models\BaseModel;
use App\Inv\Repositories\Entities\User\Exceptions\BlankDataExceptions;
use App\Inv\Repositories\Entities\User\Exceptions\InvalidDataTypeExceptions;
use Illuminate\Database\Eloquent\SoftDeletes;

class PaymentExcel extends BaseModel {
    
    use SoftDeletes;
    
    /* The database table used by the model.
     *
     * @var string
     */

    protected $table = 'payments_excel';

    /**
     * Custom primary key is set for the table
     *
     * @var integer
     */
    protected $primaryKey = 'payment_excel_id';

    protected $softDelete = true;
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
        'bankcode',
        'virtual_acc',
        'instrument_type',
        'trans_type',
        'remitter_account_number',
        'remitter_ifsc_code',
        'remitter_name',
        'contact_no',
        'email',
        'is_status',
        'txn_amount',
        'txn_date',
        'txn_ref_number',
        'client_code',
        'trn_time_stamp',
        'file_id',
        'created_at',
        'created_by',
        'updated_at',
        'updated_by',
        'deleted_at',
    ];
    
    public function biz() {
       return $this->belongsTo('App\Inv\Repositories\Models\Business', 'biz_id');
    }

    public function user(){
        return $this->belongsTo('App\Inv\Repositories\Models\User','user_id','user_id');
    }
    
    public function lmsUser(){
        return $this->belongsTo('App\Inv\Repositories\Models\LmsUser','user_id','user_id');
    }

    public function transaction(){
        return $this->hasOne('App\Inv\Repositories\Models\Lms\Transactions','payment_id','payment_id');
    }
    
    public function creator(){
        return $this->belongsTo('App\Inv\Repositories\Models\User','created_by','user_id');
    }
    
    public function userFile(){
        return $this->hasOne('App\Inv\Repositories\Models\UserFile','file_id','file_id');
    }    

    public function getBusinessName() {
        return $this->belongsTo(Business::class, 'biz_id');
    }

    public function getUserName() {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function getCreatedByName() {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function transType(){
        return $this->belongsTo('App\Inv\Repositories\Models\Lms\TransType', 'trans_type', 'id');
    } 

    public function refundReq(){
        return $this->hasOne('App\Inv\Repositories\Models\Lms\Refund\RefundReq','payment_id','payment_id');
    }
     
    public function getPaymentNameAttribute(){
        $tdsType = ($this->action_type == 3) ? '/TDS' : '';   
        return $this->transType->trans_name . $tdsType;
    }

    public static function getTallyTxns(array $where = array()) {
        return self::with('user', 'lmsUser', 'transType')->where(['is_settled' => 1, 'generated_by' => 0, 'is_refundable' => 1, 'trans_type' => 17, 'action_type' => 1])->where($where)->get();
    }

    


    public function userRelation() {
        return $this->hasOne('App\Inv\Repositories\Models\Lms\UserInvoiceRelation', 'user_id', 'user_id')->where('is_active', 1);
    }

    /**
     * insert Payment data
     * 
     * @return type int payment_id
     */
    public static function insertPaymentsExcel(array $arr = [])
    {
        $resp = [
            'status' => 'success',
            'code' => '000',
            'message' => 'success',
        ];
        try {
            $arr['sys_updated_at'] = Helpers::getSysStartDate();
            $arr['sys_created_at'] = $arr['sys_updated_at'];
            $insertId = self::create($arr)->payment_excel_id;
            $resp['code'] = $insertId;  
            $resp['message'] = 'Payment Excel inserted successfuly';
        } catch (\Exception $e) {
            $errorInfo  = $e->errorInfo;
            $resp['status'] = 'error';  
            $resp['code'] = $errorInfo[1] ?? '444';  
            $resp['message'] = preg_replace('#[^A-Za-z./\s\_]+#', '', $errorInfo[2]) ?? 'Some DB Error occured. Try again.';  
        }
        return $resp['status'] == 'success' ? $resp['code'] : $resp['message'];
    }   

   
}