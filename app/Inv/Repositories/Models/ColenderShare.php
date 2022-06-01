<?php

namespace App\Inv\Repositories\Models;

use DB;
use App\Inv\Repositories\Factory\Models\BaseModel;
use App\Inv\Repositories\Entities\User\Exceptions\BlankDataExceptions;
use App\Inv\Repositories\Entities\User\Exceptions\InvalidDataTypeExceptions;

class ColenderShare extends BaseModel {
    /* The database table used by the model.
     *
     * @var string
     */

    protected $table = 'co_lenders_share';

    /**
     * Custom primary key is set for the table
     *
     * @var integer
     */
    protected $primaryKey = 'co_lenders_share_id';

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
        'app_id',
        'user_id',
        'app_prgm_limit_id',
        'co_lender_id',
        'capsave_percent',
        'co_lender_percent',
        'capsave_status',
        'co_lender_status',
        'co_lender_comment',
        'capsave_comment',
        'is_active',
        'start_date',
        'end_date',
        'created_at',
        'created_by',
        'updated_at',        
        'updated_by',
    ];

    public static function saveShareToColender($data, $co_lenders_share_id){
        if (!is_array($data)) {
            throw new InvalidDataTypeExceptions(trans('error_message.invalid_data_type'));
        }
        
        if (!is_null($co_lenders_share_id)) {
            return self::where('co_lenders_share_id', $co_lenders_share_id)->update($data);
        } else {
            return self::create($data);
        }
    }

    public static function getSharedColender($where, $notColenderId = NULL){
        if (!is_array($where)) {
            throw new InvalidDataTypeExceptions(trans('error_message.invalid_data_type'));
        }else{
            return self::where($where)->where('co_lender_id', '!=' ,$notColenderId)->get();
        }        
    }

    public function colender(){
        return $this->belongsTo('App\Inv\Repositories\Models\CoLenderUsers', 'co_lender_id', 'co_lender_id');
    }

    public static function getColenderShareWithUserId($user_id){
         if (!is_int($user_id)) {
            throw new InvalidDataTypeExceptions(trans('error_message.invalid_data_type'));
        }
        return self::where(['user_id' => $user_id, 'is_active' => 1])->first();
    }

    public static function updateColenderData($attributes, $conditions) {
        if (!is_array($attributes) || !is_array($conditions)) {
            throw new InvalidDataTypeExceptions(trans('error_message.send_array'));
        }
        if (empty($conditions)) {
            throw new BlankDataExceptions(trans('error_message.no_data_found'));
        }
        $res = self::where($conditions)->update($attributes);
        return ($res ?: false);
    }

    public static function getSharedColenderData($where){
        if (!is_array($where)) {
            throw new InvalidDataTypeExceptions(trans('error_message.invalid_data_type'));
        }else{
            return self::where($where)->get();

        }
    }
}
