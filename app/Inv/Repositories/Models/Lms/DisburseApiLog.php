<?php

namespace App\Inv\Repositories\Models\Lms;

use DB;
use App\Inv\Repositories\Factory\Models\BaseModel;
use App\Inv\Repositories\Entities\User\Exceptions\BlankDataExceptions;
use App\Inv\Repositories\Entities\User\Exceptions\InvalidDataTypeExceptions;

class DisburseApiLog extends BaseModel {
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
        'refer_id',
        'tran_id',
        'utr_no',
        'biz_api_log_id',
        'request_file_name',
        'remark',
        'is_active',
        'created_at',
        'created_by',
        'updated_at',
        'updated_by',
    ];

}
