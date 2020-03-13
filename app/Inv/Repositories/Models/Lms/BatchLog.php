<?php

namespace App\Inv\Repositories\Models\Lms;

use DB;
use App\Inv\Repositories\Factory\Models\BaseModel;
use App\Inv\Repositories\Entities\User\Exceptions\BlankDataExceptions;
use App\Inv\Repositories\Entities\User\Exceptions\InvalidDataTypeExceptions;

class BatchLog extends BaseModel {
    /* The database table used by the model.
     *
     * @var string
     */

    protected $table = 'lms_batch_log';

    /**
     * Custom primary key is set for the table
     *
     * @var integer
     */
    protected $primaryKey = 'batch_log_id';

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
        'batch_log_id',
        'batch_id',
        'trans_id',
        'status',        
        'amount',
        'approve_amount',
        'created_at',
        'created_by',
        'updated_at',
        'updated_by',
    ];

    public function batch()
    {
        return $this->belongsTo('App\Inv\Repositories\Models\Lms\Batch', 'batch_id');
    }

    public function transactions()
    {
        return $this->belongsTo('App\Inv\Repositories\Models\Lms\Transactions', 'trans_id');
    }
}