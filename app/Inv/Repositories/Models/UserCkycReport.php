<?php

namespace App\Inv\Repositories\Models;

use DB;
use Carbon\Carbon;
use App\Inv\Repositories\Factory\Models\BaseModel;
use App\Inv\Repositories\Entities\User\Exceptions\BlankDataExceptions;
use App\Inv\Repositories\Entities\User\Exceptions\InvalidDataTypeExceptions;

class UserCkycReport extends BaseModel {

    /* The database table used by the model.
     *
     * @var string
     */

    protected $table = 'ckyc_report';

    /**
     * Custom primary key is set for the table
     *
     * @var integer
     */
    protected $primaryKey = 'ckyc_report_id';

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
    public $userstamps = false;


    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'ucic_id',
        'user_id',
        'biz_id',
        'biz_owner_id',
        'entity_type',
        'owner_typ',
        'entity_name',
        'pan_no',
        'ckyc_applicable',
        'ckyc_status',
        'ckyc_date',
        'created_at',        
        'updated_at'
    ];

    public static function getCompanyReport($where){
        
        return self::where($where)->whereNull('biz_owner_id')->first();
    }
}