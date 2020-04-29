<?php
namespace App\Inv\Repositories\Models\Lms\Refund;

use DB;
use App\Inv\Repositories\Models\User;
use App\Inv\Repositories\Factory\Models\BaseModel;
use App\Inv\Repositories\Models\Master\RefundType;
use App\Inv\Repositories\Models\Lms\Refund\RefundReq;
use App\Inv\Repositories\Entities\User\Exceptions\BlankDataExceptions;
use App\Inv\Repositories\Entities\User\Exceptions\InvalidDataTypeExceptions;

class RefundReqType extends BaseModel {
    /* The database table used by the model.
     *
     * @var string
     */

    protected $table = 'lms_refund_req_type_h';

    /**
     * Custom primary key is set for the table
     *
     * @var integer
     */
    protected $primaryKey = 'refund_req_type_id';

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
        'refund_req_id',
        'refund_type_id',
        'amount',
        'created_by',
        'created_at',
        'updated_by',
        'updated_at',
    ];
    
    public function refundReq(){
        return $this->belongsTo(RefundReq::class,'refund_req_id','refund_req_id');
    } 

    public function refundType(){
        return $this->belongsTo(RefundType::class,'refund_type_id','id');
    }

    public static function saveRefundReqTypeData($reqData=[], $reqId=null)
    {
        if (!is_array($reqData)) {
            throw new InvalidDataTypeExceptions(trans('error_messages.invalid_data_type'));
        }        
        
        if (!is_null($reqId)) {
            return self::where('refund_req_type_id', $reqId)->update($reqData);
        } else {
            return self::create($reqData);
        }
    }
}

