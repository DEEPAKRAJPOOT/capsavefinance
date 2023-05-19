<?php

namespace App\Inv\Repositories\Models;

use DB;
use Carbon\Carbon;
use App\Inv\Repositories\Factory\Models\BaseModel;
use App\Inv\Repositories\Entities\User\Exceptions\BlankDataExceptions;
use App\Inv\Repositories\Entities\User\Exceptions\InvalidDataTypeExceptions;

class UserCkycApiLog extends BaseModel {

    /* The database table used by the model.
     *
     * @var string
     */

    protected $table = 'user_ckyc_api_log';

    /**
     * Custom primary key is set for the table
     *
     * @var integer
     */
    protected $primaryKey = 'ckyc_api_log_id';

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
        'api_type',
        'biz_owner_id',
        'request_type',
        'req_data',
        'res_file_id',
        'res_data',
        'user_ucic_id',
        'status',
        'created_at',
        'created_by',
        'updated_at',
        'updated_by'
    ];

    public function getCKYCdownloadData(){

        return $this->hasOne('App\Inv\Repositories\Models\UserCkyc','pan_ckyc_api_log_id','ckyc_api_log_id');
    }

    public function collectAllCkycDoc(){

        return $this->hasMany('App\Inv\Repositories\Models\UserCkycDoc','ckyc_api_log_id','ckyc_api_log_id');
    }

    public static function getindividualckycsearchLog($biz_owner_id,$api_type){
        return self::where(['biz_owner_id'=>$biz_owner_id,'api_type'=>$api_type,'status'=>1])->orderBy('ckyc_api_log_id','DESC')->first();
    }

    public static function getLegalEntityckycsearchLog($user_id,$api_type){
        return self::where(['user_id'=>$user_id,'api_type'=>$api_type,'status'=>1])->whereNull('biz_owner_id')->orderBy('ckyc_api_log_id','DESC')->first();
    }

    public static function userckycApilogUpdate($api_log_id, $user_ckyc_api_log_data){

        if (!is_array($user_ckyc_api_log_data)) {
            throw new InvalidDataTypeExceptions(trans('error_message.invalid_data_type'));
        }

        return self::where(['ckyc_api_log_id'=>$api_log_id])
                    ->update($user_ckyc_api_log_data);

    }

    public static function checkCkycdocumentPulled($where){
        DB::enableQueryLog();
        if (!is_array($where)) {
            throw new InvalidDataTypeExceptions(trans('error_message.invalid_data_type'));
        }
        
        if(isset($where['biz_owner_id'])){

            $data = self::where(function($query) use($where){
                $query->where(['user_id'=>(int)$where['user_id'],'api_type'=>1,'status'=>0,'biz_owner_id'=>$where['biz_owner_id']]);
            })->orWhere(function($query) use($where){
                $query->where(['user_id'=>(int)$where['user_id'],'api_type'=>2,'status'=>0,'biz_owner_id'=>$where['biz_owner_id']]);
            })->orderBy('ckyc_api_log_id','DESC')->first();

        }else{

            $data = self::where(function($query) use($where){
                $query->where(['user_id'=>(int)$where['user_id'],'api_type'=>1,'status'=>0])->whereNull('biz_owner_id');
            })->orWhere(function($query) use($where){
                $query->where(['user_id'=>(int)$where['user_id'],'api_type'=>2,'status'=>0])->whereNull('biz_owner_id');
            })->orderBy('ckyc_api_log_id','DESC')->first();

        }
        
        if ($data){
            return $data;
        }else {
            return false;
        }
        
    }
}