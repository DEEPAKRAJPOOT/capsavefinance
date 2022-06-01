<?php

namespace App\Inv\Repositories\Models;

use DB;
use Carbon\Carbon;
use App\Inv\Repositories\Factory\Models\BaseModel;
use App\Inv\Repositories\Models\AppProgramLimit;
use App\Inv\Repositories\Models\AppProgramOffer;
use App\Inv\Repositories\Models\BizInvoice;
use App\Inv\Repositories\Entities\User\Exceptions\BlankDataExceptions;
use App\Inv\Repositories\Entities\User\Exceptions\InvalidDataTypeExceptions;

class AppLimit extends BaseModel {
    /* The database table used by the model.
     *
     * @var string
     */

    protected $table = 'app_limit';

    /**
     * Custom primary key is set for the table
     *
     * @var integer
     */
    protected $primaryKey = 'app_limit_id';

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
        'parent_app_limit_id',
        'user_id',
        'app_id',
        'biz_id',
        'tot_limit_amt',
        'status',
        'start_date',
        'end_date',
        'actual_end_date',
        'created_at',
        'created_by',
        'updated_at',        
        'updated_by',
    ];

    public static function saveAppLimit($data, $limit_id){
        if (!is_array($data)) {
            throw new InvalidDataTypeExceptions(trans('error_message.invalid_data_type'));
        }
        
        if (!is_null($limit_id)) {
            return self::where('app_limit_id', $limit_id)->update($data);
        } else {
            return self::create($data);
        }
    }

    public function app()
    {
        return $this->belongsTo('App\Inv\Repositories\Models\Application', 'app_id', 'app_id');
    }   
    
    
    public static function getUserLimit($user_id)
    {
       return  self::where(['user_id'=>$user_id,'status' => 1])->first();
    }
    

 
    public static function updateAppLimit($data, $whereCond=[]){
        if (!is_array($data)) {
            throw new InvalidDataTypeExceptions(trans('error_message.invalid_data_type'));
        }
        
        if (!is_array($whereCond)) {
            throw new InvalidDataTypeExceptions(trans('error_message.invalid_data_type'));
        }        
        
        if (count($whereCond) > 0) {
            return self::where($whereCond)->update($data);
        }
    }    

    public static function getUserProgramLimit($user_id)
    {
       return  self::where(['user_id'=>$user_id,'status' => 1])->first();
    }
     public function anchor(){
        return $this->belongsTo('App\Inv\Repositories\Models\Anchor','anchor_id','anchor_id');
    }

    public function program(){
        return $this->belongsTo('App\Inv\Repositories\Models\Program','prgm_id','prgm_id');
    }

    public function offer(){
        return $this->hasMany('App\Inv\Repositories\Models\AppProgramOffer','app_prgm_limit_id','app_prgm_limit_id')->where('is_active',1);
    }     
      public function product(){
        return $this->hasOne('App\Inv\Repositories\Models\Master\Product', 'product_id', 'id');
    }  
    
     public function programLimit(){
        return $this->hasMany('App\Inv\Repositories\Models\AppProgramLimit','app_limit_id','app_limit_id');
    }
   
    
    public static  function getUserApproveLimit($user_id)
    {
        return  AppLimit::with(['app','programLimit','programLimit.product','programLimit.offer.program','programLimit.offer.anchor','programLimit.offer.adhoc_limit'])
                        ->whereHas('app', function ($query) {
                            $query->whereNotIn('curr_status_id', [config('common.mst_status_id')['APP_REJECTED'], config('common.mst_status_id')['APP_CANCEL']]);
                        })
                        ->where(['user_id'=>$user_id])
                        ->orderBy('created_at','DESC')
                        ->get();
    }

    public static  function appLimitByUserId($user_id)
    {
        return  AppLimit::where(['user_id'=>$user_id])
                ->where('status', 1)
                ->first();
    }

    public static function getAppLimitData($whereCond)
    {
        $result = self::select('*')
                ->where($whereCond)               
                ->get();
        return $result ? $result : []; 
    }   

}
