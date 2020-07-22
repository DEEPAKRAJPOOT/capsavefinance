<?php

namespace App\Inv\Repositories\Models;

use DB;
use Helpers;
use App\Inv\Repositories\Models\User;
use App\Inv\Repositories\Models\Business;
use App\Inv\Repositories\Factory\Models\BaseModel;
use App\Inv\Repositories\Entities\User\Exceptions\BlankDataExceptions;
use App\Inv\Repositories\Entities\User\Exceptions\InvalidDataTypeExceptions;

class Payment extends BaseModel {
    /* The database table used by the model.
     *
     * @var string
     */

    protected $table = 'payments';

    /**
     * Custom primary key is set for the table
     *
     * @var integer
     */
    protected $primaryKey = 'payment_id';

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
        'biz_id',
        'virtual_acc',
        'action_type',
        'trans_type',
        'parent_trans_id',
        'amount',
        'date_of_payment',
        'gst',
        'sgst_amt',
        'cgst_amt',
        'igst_amt',
        'payment_type',
        'utr_no',
        'unr_no',
        'cheque_no',
        'tds_certificate_no',
        'file_id',
        'description',
        'is_settled',
        'is_manual',
        'is_refundable',
        'generated_by',
        'sys_created_at',
        'sys_updated_at',
        'created_at',
        'created_by',
        'updated_at',
        'updated_by',
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

    /**
     * get Payment data list
     * 
     * @return type mixed
     */
    public static function getPayments(array $where = []) {
        $res = self::where($where)->orderBy('payment_id','desc')->get();
        return $res->isEmpty() ? [] :  $res;
    }


    public function userRelation() {
        return $this->hasOne('App\Inv\Repositories\Models\Lms\UserInvoiceRelation', 'user_id', 'user_id')->where('is_active', 1);
    }

    /**
     * insert Payment data
     * 
     * @return type int payment_id
     */
    public static function insertPayments(array $arr = [])
    {
        $resp = [
            'status' => 'success',
            'code' => '000',
            'message' => 'success',
        ];
        try {
            $arr['sys_updated_at'] = Helpers::getSysStartDate();
            $arr['sys_created_at'] = $arr['sys_updated_at'];
            $insertId = self::create($arr)->payment_id;
            $resp['code'] = $insertId;  
            $resp['message'] = 'Payment inserted successfuly';  
        } catch (\Exception $e) {
            $errorInfo  = $e->errorInfo;
            $resp['status'] = 'error';  
            $resp['code'] = $errorInfo[1] ?? '444';  
            $resp['message'] = preg_replace('#[^A-Za-z./\s\_]+#', '', $errorInfo[2]) ?? 'Some DB Error occured. Try again.';  
        }
        return $resp['status'] == 'success' ? $resp['code'] : $resp['message'];
    }   

    public function getPaymentModeAttribute() {
        if($this->action_type == 1){
            $payment_type = $this->payment_type;
            $payModes = config('payment.type') ?? [];
            $mode_of_pay = $payModes[$payment_type] ?? NULL;
        }else{
            $mode_of_pay = $this->paymentname;
        }
        return $mode_of_pay;
    }

    public function getTransactionNoAttribute() {
        $payment_type = $this->payment_type;
        switch ($payment_type) {
            case '1':
                $attr = $this->utr_no;
                break;
            case '2':
                $attr = $this->cheque_no;
                break;
            case '3':
               $attr = $this->unr_no;
                break;
            case '4':
                $attr = $this->unr_no;
                    break;
            default:
               $attr = '';
                break;
        }
        return $attr;
    }

    public static function getPaymentReceipt(array $where = []) {
        return self::with('userRelation')->where(['is_settled' => 1, 'generated_by' => 0, 'is_refundable' => 1, 'trans_type' => config('lms.TRANS_TYPE.REPAYMENT'), 'action_type' => 1])
        ->where($where)
        ->get();
    }

    public function getSettledTxns() {
       return $this->hasMany('App\Inv\Repositories\Models\Lms\Transactions','payment_id','payment_id')->whereNotIn('trans_type',[config('lms.TRANS_TYPE.REPAYMENT'), config('lms.TRANS_TYPE.REFUND')]);
    }
    
    /*** get all transaction  **/
    public static function getAllManualTransaction()
    {
          return self::with(['biz','user', 'transType', 'transaction'])->where('trans_type','!=',NULL)->orderBy('payment_id','DESC');
    }
}
