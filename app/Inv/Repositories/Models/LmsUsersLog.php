<?php

namespace App\Inv\Repositories\Models;
use DB;
use Auth;
use Carbon\Carbon;
use App\Inv\Repositories\Models\BizInvoice;
use App\Inv\Repositories\Factory\Models\BaseModel;
use App\Inv\Repositories\Models\User;
use App\Inv\Repositories\Models\Master\Status;
class LmsUsersLog extends BaseModel {
    /* The database table used by the model.
     *
     * @var string
     */

    protected $table = 'lms_users_log';

    /**
     * Custom primary key is set for the table
     *
     * @var integer
     */
    protected $primaryKey = 'lms_users_log_id';

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
                'user_id',
                'status_id',
                'created_by',
                'created_at'
          
    ];
    
  
}
