<?php

namespace App\Inv\Repositories\Models;

use DB;
use Carbon\Carbon;
use App\Inv\Repositories\Factory\Models\BaseModel;
use App\Inv\Repositories\Entities\User\Exceptions\BlankDataExceptions;
use App\Inv\Repositories\Entities\User\Exceptions\InvalidDataTypeExceptions;

class UserCkyc extends BaseModel {


    /* The database table used by the model.
     *
     * @var string
     */

    protected $table = 'user_ckyc';

    /**
     * Custom primary key is set for the table
     *
     * @var integer
     */
    protected $primaryKey = 'user_ckyc_id';

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
        'user_id',
        'pan_ckyc_api_log_id',
        'doc_ckyc_api_log_id',
        'user_ucic_id',
        'ucic_code',
        'biz_owner_id',
        'ckyc_no',
        'user_id',
        'request_type',
        'status',
        'created_at',
        'created_by',
        'updated_at',
        'updated_by'
    ];

    public static function userckycCreateOrUpdate($where, $user_ckyc_data){
        
        if (!is_array($where) && !is_array($user_ckyc_data)) {
            throw new InvalidDataTypeExceptions(trans('error_message.invalid_data_type'));
        }
        
        if(isset($where['biz_owner_id']) && !empty($where['biz_owner_id'])){

            if(self::where($where)->count()){

                $update =  self::where($where)->update($user_ckyc_data);
                return self::where($where)->first();

            }else{
                return self::create($user_ckyc_data);
            }
        }else{

            if(self::where($where)->whereNull('biz_owner_id')->count()){

                $update = self::where($where)->whereNull('biz_owner_id')->update($user_ckyc_data);
                return self::where($where)->whereNull('biz_owner_id')->first();

            }else{

                return self::create($user_ckyc_data);
            }
        }

    }
}