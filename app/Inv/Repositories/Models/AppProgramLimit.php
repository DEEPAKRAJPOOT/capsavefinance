<?php

namespace App\Inv\Repositories\Models;

use DB;
use App\Inv\Repositories\Factory\Models\BaseModel;
use App\Inv\Repositories\Entities\User\Exceptions\BlankDataExceptions;
use App\Inv\Repositories\Entities\User\Exceptions\InvalidDataTypeExceptions;

class AppProgramLimit extends BaseModel {
    /* The database table used by the model.
     *
     * @var string
     */

    protected $table = 'app_prgm_limit';

    /**
     * Custom primary key is set for the table
     *
     * @var integer
     */
    protected $primaryKey = 'app_prgm_limit_id';

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
        'app_limit_id',
        'app_id',
        'biz_id',
        'anchor_id',
        'prgm_id',
        'limit_amt',
        'created_at',
        'created_by',
        'updated_at',        
        'updated_by',
    ];

    //     
}
