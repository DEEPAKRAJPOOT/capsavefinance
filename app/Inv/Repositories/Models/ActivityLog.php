<?php

namespace App\Inv\Repositories\Models;

use App\Inv\Repositories\Factory\Models\BaseModel;

class ActivityLog extends BaseModel {
    /* The database table used by the model.
     *
     * @var string
     */

    protected $table = 'activity_log';

    /**
     * Custom primary key is set for the table
     *
     * @var integer
     */
    protected $primaryKey = 'activity_log_id';

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
     * Identifier to set created_by as null
     *
     * @var boolean
     */
    protected static $nullable_user = true;

    /**
     * Maintain Device automatically
     *
     * @var boolean
     */
    public $devicetrack = true;

    const DEVICE = 'device_type';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'session_id',
        'route_name',
        'user_id',
        'app_id',
        'activity_id',
        'activity_desc',
        'data',
        'email',
        'ip_address',
        'browser_info',
        'source',
        'device_type',
        'created_by',
        'created_at',        
    ];

}
