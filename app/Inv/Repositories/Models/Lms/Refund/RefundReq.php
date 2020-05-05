<?php
namespace App\Inv\Repositories\Models\Lms\Refund;

use DB;
use Dompdf\Helpers;
use App\Inv\Repositories\Models\User;
use App\Inv\Repositories\Factory\Models\BaseModel;
use App\Inv\Repositories\Models\Lms\Refund\RefundReqBatch;
use App\Inv\Repositories\Entities\User\Exceptions\BlankDataExceptions;
use App\Inv\Repositories\Entities\User\Exceptions\InvalidDataTypeExceptions;

class RefundReq extends BaseModel {
    /* The database table used by the model.
     *
     * @var string
     */

    protected $table = 'lms_refund_req';

    /**
     * Custom primary key is set for the table
     *
     * @var integer
     */
    protected $primaryKey = 'refund_req_id';

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
        'ref_code',  
        'payment_id',  
        'refund_req_batch_id',
        'refund_date',  
        'refund_amount',
        'bank_account_id',
        'bank_name',
        'ifsc_code',
        'acc_no',
        'tran_no',
        'actual_refund_date',
        'status',  
        'comment',  
        'created_at',  
        'created_by',  
        'updated_at',  
        'updated_by'
    ];

    public function payment(){
        return $this->belongsTo('App\Inv\Repositories\Models\Payment','payment_id','payment_id');
    } 

    public function batch(){
        return $this->belongsTo(RefundReqBatch::class,'refund_req_batch_id','refund_req_batch_id');
    }

    public static function createRefundReq($data){
        return self::insert($data);
    }

    public static function updateRefundReq($data,$where){
        return self::where($where)->update($data);
    }

    public static function saveRefundReqData($reqData=[], $reqId=null)
    {
        if (!is_array($reqData)) {
            throw new InvalidDataTypeExceptions(trans('error_messages.invalid_data_type'));
        }        
        
        if (!is_null($reqId)) {
            return self::where('refund_req_id', $reqId)->update($reqData);
        } else {
            $data = self::create($reqData);
            $data->ref_code = \Helpers::formatIdWithPrefix($data->refund_req_id, 'REFUND');
            $data->save();
            return $data;
        }
    }

}

