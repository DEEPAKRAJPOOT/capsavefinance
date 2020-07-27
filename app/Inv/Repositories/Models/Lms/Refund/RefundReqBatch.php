<?php
namespace App\Inv\Repositories\Models\Lms\Refund;

use DB;
use App\Inv\Repositories\Factory\Models\BaseModel;
use App\Inv\Repositories\Entities\User\Exceptions\BlankDataExceptions;
use App\Inv\Repositories\Entities\User\Exceptions\InvalidDataTypeExceptions;
use App\Inv\Repositories\Models\User;

class RefundReqBatch extends BaseModel {
    /* The database table used by the model.
     *
     * @var string
     */

    protected $table = 'lms_refund_req_batch';

    /**
     * Custom primary key is set for the table
     *
     * @var integer
     */
    protected $primaryKey = 'refund_req_batch_id';

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
        'batch_no',
        'file_id',
        'disbursal_api_log_id',
        'batch_status',
        'refund_type',
        'created_by',
        'created_at',
        'updated_by',
        'updated_at'
    ];

    public static function saveRefundReqData($reqData=[], $reqId=null)
    {
        if (!is_array($reqData)) {
            throw new InvalidDataTypeExceptions(trans('error_messages.invalid_data_type'));
        }        
        
        if (!is_null($reqId)) {
            return self::where('refund_req_batch_id', $reqId)->update($reqData);
        } else {
            return self::create($reqData);
        }
    }

    public static function lmsGetRefundBatchRequest()
    {
        return self::where('batch_status', 1)
                ->where('refund_type', 1)
                ->orderBy('refund_req_batch_id', 'DESC');
    }

    public function refund() { 
        return $this->hasMany('App\Inv\Repositories\Models\Lms\Refund\RefundReq', 'refund_req_batch_id', 'refund_req_batch_id'); 
    }

    public function disbursal_api_log() { 
        return $this->belongsTo('App\Inv\Repositories\Models\Lms\DisburseApiLog', 'disbursal_api_log_id', 'disbursal_api_log_id'); 
    }
}   

