<?php

namespace App\Inv\Repositories\Models;

use Carbon\Carbon;
use DateTime;
use App\Inv\Repositories\Factory\Models\BaseModel;

class BizPanGstApi extends BaseModel
{
    /* The database table used by the model.
     *
     * @var string
     */
    protected $table = 'biz_pan_gst_api';

    /**
     * Custom primary key is set for the table
     *
     * @var integer
     */
    protected $primaryKey = 'biz_pan_gst_api_id';
    public $userstamps = false;
    public $timestamps = false;

    
    protected $fillable = [
        'file_name',
        'status',
        'created_at',
        'created_by'
    ];

    
    
}


