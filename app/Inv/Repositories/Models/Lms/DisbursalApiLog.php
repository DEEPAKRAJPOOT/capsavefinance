<?php

namespace App\Inv\Repositories\Models\Lms;

use DB;
use App\Inv\Repositories\Factory\Models\BaseModel;
use App\Inv\Repositories\Entities\User\Exceptions\BlankDataExceptions;
use App\Inv\Repositories\Entities\User\Exceptions\InvalidDataTypeExceptions;

class DisbursalApiLog extends BaseModel {
    /* The database table used by the model.
     *
     * @var string
     */

    protected $table = 'disbursal_api_log';

    /**
     * Custom primary key is set for the table
     *
     * @var integer
     */
    protected $primaryKey = 'disbursal_api_log_id';

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
        'disbursal_batch_id',
        'bank_type',
        'txn_id',
        'enq_txn_id',
        'url',
        'header',
        'req_text',
        'res_text',
        'file_id',
        'status',
        'created_at',
        'created_by',
    ];

    /**
     * Save or Update
     * 
     * @param array $data
     * @param array $whereCondition | optional
     * @return mixed
     * @throws InvalidDataTypeExceptions
     */
    public static function saveUpdateDisbursalApiLog($data, $whereCondition=[])
    {
        if (!is_array($data)) {
            throw new InvalidDataTypeExceptions(trans('error_messages.invalid_data_type'));
        }
        
        if (!empty($whereCondition)) {
            return self::where($whereCondition)->update($data);
        } else if (isset($data[0])) {
            return self::insert($data);
        } else {
            return self::create($data);
        }
    }

}
