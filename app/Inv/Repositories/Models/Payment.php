<?php

namespace App\Inv\Repositories\Models;

use DB;
use App\Inv\Repositories\Factory\Models\BaseModel;
use App\Inv\Repositories\Entities\User\Exceptions\BlankDataExceptions;
use App\Inv\Repositories\Entities\User\Exceptions\InvalidDataTypeExceptions;
use App\Inv\Repositories\Models\Business;
use App\Inv\Repositories\Models\User;

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
        'description',
        'is_settled',
        'is_manual',
        'created_at',
        'created_by',
    ];
    
    
    /**
     * get Payment data list
     * 
     * @return type mixed
     */
    public static function getPayments(array $where = [])
    {
        $res = self::where($where)->get();
        return $res->isEmpty() ? [] :  $res;
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

    public function getBusinessName() {
       return $this->hasOne(Business::class, 'biz_id');
    }

    public function getUserName() {
       return $this->hasOne(User::class, 'user_id');
    }
     
}
