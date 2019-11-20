<?php

namespace App\Inv\Repositories\Models;

use Carbon\Carbon;
use DateTime;
use App\Inv\Repositories\Factory\Models\BaseModel;

class BizPanGst extends BaseModel
{
    /* The database table used by the model.
     *
     * @var string
     */
    protected $table = 'biz_pan_gst';

    /**
     * Custom primary key is set for the table
     *
     * @var integer
     */
    protected $primaryKey = 'biz_pan_gst_id';
    public $userstamps = false;
    public $timestamps = false;

    
    protected $fillable = [
        'user_id',
        'biz_id',
        'biz_owner_id',
        'type',
        'cin',
        'pan_gst_hash',
        'status',
        'parent_pan_gst_id',
        'biz_pan_gst_api_id',
        'created_by',
        'created_at',
        'updated_at',
        'updated_by'
    ];
   
}




