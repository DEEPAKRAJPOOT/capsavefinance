<?php

namespace App\Inv\Repositories\Models\Lms;

use DB;
use App\Inv\Repositories\Factory\Models\BaseModel;
use App\Inv\Repositories\Entities\User\Exceptions\BlankDataExceptions;
use App\Inv\Repositories\Entities\User\Exceptions\InvalidDataTypeExceptions;

class Refund extends BaseModel {
    /* The database table used by the model.
     *
     * @var string
     */

    protected $table = 'lms_refund';

    /**
     * Custom primary key is set for the table
     *
     * @var integer
     */
    protected $primaryKey = 'refund_id';

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
        'trans_id',
        'variable_id',      
        'variable_type',
        'variable_value',
        'amount',
        'created_at',
        'created_by',
        'updated_at',
        'updated_by',
    ];

    
    public static function getRefundData($transId)
    {
        $result = self::select('*')
                ->join('lms_variables', 'lms_variables.id', '=', 'lms_refund.variable_id')
                ->where('lms_refund.trans_id', $transId)
                ->get();
        return isset($result[0]) ? $result : [];
    }
    
    public static function saveRefundData($refundData)
    {
        //Check $refundData is not an array
        if (!is_array($refundData)) {
            throw new InvalidDataTypeExceptions(trans('error_messages.invalid_data_type'));
        }        
        
        return self::insert($refundData);
    }
}

