<?php

namespace App\Inv\Repositories\Models;

use Carbon\Carbon;
use DateTime;
use App\Inv\Repositories\Factory\Models\BaseModel;

class BizApiLog extends BaseModel
{
    /* The database table used by the model.
     *
     * @var string
     */
    protected $table = 'biz_api_log';

    /**
     * Custom primary key is set for the table
     *
     * @var integer
     */
    protected $primaryKey = 'biz_api_log_id';
    public $userstamps = false;
    public $timestamps = false;

    
    protected $fillable = [
        'req_file',
        'res_file',
        'status'
    ];
   
}




