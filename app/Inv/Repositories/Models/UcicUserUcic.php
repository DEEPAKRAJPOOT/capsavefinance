<?php

namespace App\Inv\Repositories\Models;

use DB;
use Helpers;
use App\Inv\Repositories\Models\User;
use App\Inv\Repositories\Models\Business;
use App\Inv\Repositories\Factory\Models\BaseModel;
use App\Inv\Repositories\Entities\User\Exceptions\BlankDataExceptions;
use App\Inv\Repositories\Entities\User\Exceptions\InvalidDataTypeExceptions;
use Illuminate\Database\Eloquent\SoftDeletes;

class UcicUserUcic extends BaseModel
{
    
    /* The database table used by the model.
     *
     * @var string
     */

    protected $table = 'user_ucic_user';

    /**
     * Custom primary key is set for the table
     *
     * @var integer
     */
    protected $primaryKey = 'ucic_user_id';

    protected $softDelete = false;
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
        'ucic_id',
        'user_id',
        'app_id',
        'group_id',
        'created_at',
        'created_by',
        'updated_at',
        'updated_by'
    ];

    

    public static function create($attributes){
        //Check data is Array
        if (!is_array($attributes)) {
            throw new InvalidDataTypeExceptions(trans('error_messages.send_array'));
        }
        
        //Check data is not blank
        if (empty($attributes)) {
            throw new BlankDataExceptions(trans('error_messages.data_not_found'));
        }
        
        $obj = self::firstOrNew($attributes);
        $obj->fill($attributes)->save();
        return $obj;
    }

    public static function getUcicUserData($where){

        //Check data is Array
        if (!is_array($where)) {
            throw new InvalidDataTypeExceptions(trans('error_messages.send_array'));
        }

        //Check data is not blank
        if (empty($where)) {
            throw new BlankDataExceptions(trans('error_messages.data_not_found'));
        }

        $result = self::where($where)->first();
        return $result ? $result : false;
    }

    public static function updateData($data, $where){
        $ucicDataucic = self::where('user_id', '=', $where['user_id'])
        ->where('ucic_id', '=', $where['ucic_id'])          
        ->update(['app_id' => $data['app_id']]);
        return $ucicDataucic;
    }

    public static function getappByUcicId($where){

         //Check data is Array
         if (!is_array($where)) {
            throw new InvalidDataTypeExceptions(trans('error_messages.send_array'));
        }

        //Check data is not blank
        if (empty($where)) {
            throw new BlankDataExceptions(trans('error_messages.data_not_found'));
        }

        $result =  self::select('user_ckyc_consent.ckyc_consent_id','user_ckyc_consent.status','user_ucic_user.app_id','user_ucic_user.user_id','app.biz_id','biz_owner.first_name','biz_owner.email','biz_owner.biz_owner_id','biz_owner.pan_number','user_ckyc_consent.consent_type','user_ckyc_consent.file_id','user_ckyc_consent.comment','ckyc_report.ckyc_applicable','app.app_id')
                        ->join('app','app.app_id','=','user_ucic_user.app_id')
                        ->join('biz_owner','app.biz_id','=','biz_owner.biz_id')
                        ->leftjoin('user_ckyc_consent', function ($join) {               
                            $join->on('user_ckyc_consent.user_ucic_id', '=', 'user_ucic_user.ucic_id');
                            $join->on('user_ckyc_consent.biz_owner_id', '=', 'biz_owner.biz_owner_id');
                        })
                        ->leftjoin('ckyc_report', function ($join){
                            $join->on('ckyc_report.biz_owner_id', '=', 'biz_owner.biz_owner_id');
                        })
                        ->leftjoin('user_ckyc_api_log', function ($join){
                            $join->on('user_ckyc_api_log.biz_owner_id', '=', 'biz_owner.biz_owner_id');
                        })
                        ->where(function($query) use ($where) {
                            $query->whereNull('biz_owner.deleted_at')
                            ->whereNotIn('app.curr_status_id',[43,51])
                            ->whereNotNull('biz_owner.pan_number')
                            ->where($where);
                        })
                        ->orWhere(function($query) use ($where) {
                            $query->whereNotNull('biz_owner.deleted_at')
                                  ->where('user_ckyc_api_log.api_type',2)
                                  ->where('user_ckyc_api_log.status',1)
                                  ->whereNotIn('app.curr_status_id',[43,51])
                                  ->whereNotNull('biz_owner.pan_number')
                                  ->where($where);
                        })->groupBy('biz_owner.pan_number')->orderBy('biz_owner_id','ASC')->get();   
       return $result ? $result : false;
    }
    
    public function ucicUser(){
        return $this->belongsTo('App\Inv\Repositories\Models\UcicUser','ucic_id', 'user_ucic_id');
    }

    public static function getGroupIdByAppId($appId){  
        return self::where('app_id', $appId)
            ->limit(1)
            ->value('group_id');
    }
}
