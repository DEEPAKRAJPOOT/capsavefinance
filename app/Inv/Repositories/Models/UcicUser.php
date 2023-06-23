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

class UcicUser extends BaseModel
{
    
    /* The database table used by the model.
     *
     * @var string
     */

    protected $table = 'user_ucic';

    /**
     * Custom primary key is set for the table
     *
     * @var integer
     */
    protected $primaryKey = 'user_ucic_id';

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
        'ucic_code',
        'user_id',
        'pan_no',
        'business_info',
        'management_info',
        'app_id',
        'group_id',
        'updated_info_src',
        'is_sync',
        'created_by',
        'created_at',
        'updated_at',
        'updated_by'
    ];

    public function app(){
        return $this->belongsTo('App\Inv\Repositories\Models\Application', 'app_id', 'app_id');
    }
    
    public function user(){
        return $this->belongsTo('App\Inv\Repositories\Models\User', 'user_id', 'user_id');
    }

    public function userApps(){
        return $this->hasMany('App\Inv\Repositories\Models\Application', 'user_id', 'user_id');
    }

    public function appgroupdetails(){
        return $this->belongsTo('App\Inv\Repositories\Models\AppGroupDetail', 'app_id', 'app_id');
    }
    
    public function group(){
        return $this->belongsTo('App\Inv\Repositories\Models\Master\NewGroup', 'group_id', 'group_id');
    }

    public function anchor(){
        return $this->belongsTo('App\Inv\Repositories\Models\AnchorUser','user_id','user_id');
    }

    public function ucicUserUcic(){
        return $this->hasMany('App\Inv\Repositories\Models\UcicUserUcic','ucic_id','user_ucic_id');
    }
    
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

    public static function getUcicData($where){

        //Check data is Array
        if (!is_array($where)) {
            throw new InvalidDataTypeExceptions(trans('error_messages.send_array'));
        }

        //Check data is not blank
        if (empty($where)) {
            throw new BlankDataExceptions(trans('error_messages.data_not_found'));
        }

        $panNo = $where['pan_no'] ?? NULL;
        $userId = $where['user_id'] ?? NULL;
        $appId = $where['app_id'] ?? NULL;
        $userUcicId = $where['user_ucic_id'] ?? NULL; 
        $ucicCode = $where['ucic_code'] ?? NULL;

        $where1 = [];
        $where2 = [];
        if($userUcicId)
            $where1['user_ucic_id'] = $userUcicId;
        
        if($panNo)
            $where1['pan_no'] = $panNo;

        if($userId)
            $where2['user_id'] = $userId;
        
        if($appId)
            $where2['app_id'] = $appId;
        
        if($ucicCode)
            $where1['ucic_code'] = $ucicCode;
        
        if(!empty($where2)){
            $result = self::where($where1)->whereHas('ucicUserUcic', function($query) use($where2){
                $query->where($where2);
            })->first();
        }else{
            $result = self::where($where1)->first();
        }
        return $result?$result:false;
    }

    public static function getUcicUserApp(){
        return UcicUser::select('user_ucic.ucic_code', 
        'user_ucic.pan_no',
        DB::raw('REPLACE(json_extract(business_info,\'$.business_info.entity_name\'),\'"\',"") as biz_name'), 
        'user_ucic_id',
        'app.app_id',
        'app.biz_id',
        'users.email', 
        'app.app_code', 
        'mst_group_new.group_code', 
        'mst_group_new.group_name',
        'users.created_at'
         )
        ->leftJoin('app','user_ucic.app_id','app.app_id')
        ->leftJoin('mst_group_new','user_ucic.group_id','mst_group_new.group_id')
        ->leftJoin('users','user_ucic.user_id','users.user_id')
        ->orderBy('user_ucic.created_at', 'DESC');                                                                    
    }

    public static function updateUcicUserData($whereCondition, $whereInCondition, $data) {
        $query = '';
        $isUpdateData = false;
        if (isset($whereCondition)) {
            $query = self::where($whereCondition);
        }
        if (isset($whereInCondition)) {
            $query = self::whereIn($whereInCondition);
        }
        if ($query) {
            $isUpdateData = $query->update($data);
        }
        return $isUpdateData;
    }

    public static function updateUcic($attributes, $user_ucic_id) {
        if (!is_array($attributes)) {
            throw new InvalidDataTypeExceptions(trans('error_message.invalid_data_type'));
        }

        return self::where('user_ucic_id', $user_ucic_id)->first()->update($attributes);
    }

    public static function updateUcicByAppId($appId, $attributes) {
        return self::where('app_id', $appId)->first()->update($attributes);
    }

    public static function getMaxUcicNumber(){

        return self::max('ucic_code');
    }

    public static function getUcicApp($ucicCode)
    {
        /**
         * Check id is not blank
         */
        if (empty($ucicCode)) {
            throw new BlankDataExceptions(trans('error_message.no_data_found'));
        }

        $appData = self::select('user_ucic.*','app.app_code')  
            ->join('app', 'user_ucic.app_id', '=', 'app.app_id')
            ->where('user_ucic.ucic_code', $ucicCode)
            ->first();
        return ($appData ? $appData : null);     
    }

    public function ucicUserDetail(){
        return $this->hasMany('App\Inv\Repositories\Models\UcicUserDetail','user_ucic_id','user_ucic_id');
    }
}
