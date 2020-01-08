<?php

namespace App\Inv\Repositories\Models;

use DB;
use App\Inv\Repositories\Models\Program;
use App\Inv\Repositories\Models\Application;
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


    public static function saveProgramLimit($data, $prgm_limit_id){
        if (!is_array($data)) {
            throw new InvalidDataTypeExceptions(trans('error_message.invalid_data_type'));
        }
        
        if (!is_null($prgm_limit_id)) {
            return self::where('app_prgm_limit_id', $prgm_limit_id)->update(['limit_amt'=>$data['limit_amt']]);
        } else {
            return self::create($data);
        }
    }

    public static function checkduplicateProgram($data){
        if(!is_array($data)){
            throw new InvalidDataTypeExceptions(trans('error_message.invalid_data_type'));
        }else{
            return AppProgramLimit::where($data)->get();
        }
    }

    public static function getProgramLimitData($appId){
        if(empty($appId)){
            throw new BlankDataExceptions(trans('error_messages.data_not_found'));
        }else if(!is_int($appId)){
            throw new InvalidDataTypeExceptions(trans('error_message.invalid_data_type'));
        }else{
            return AppProgramLimit::where('app_id', $appId)->get();
        }
    }

    public static function getLimit($prgm_limit_id){
        if(empty($prgm_limit_id)){
            throw new BlankDataExceptions(trans('error_messages.data_not_found'));
        }else if(!is_int($prgm_limit_id)){
            throw new InvalidDataTypeExceptions(trans('error_message.invalid_data_type'));
        }else{
            return AppProgramLimit::where('app_prgm_limit_id', $prgm_limit_id)->first();
        }
    }

    public function anchor(){
        return $this->belongsTo('App\Inv\Repositories\Models\Anchor','anchor_id','anchor_id');
    }

    public function program(){
        return $this->belongsTo('App\Inv\Repositories\Models\Program','prgm_id','prgm_id');
    }

    public function offer(){
        return $this->hasOne('App\Inv\Repositories\Models\AppProgramOffer','app_prgm_limit_id','app_prgm_limit_id')->where('is_active',1);
    }     

    public static function getLimitAnchor($aid){
        return AppProgramLimit::with('anchorList')->where(['app_id' =>$aid])->get();
    }
     
    public static function getLimitProgram($aid){ 
        return AppProgramLimit::with('program')->where(['anchor_id' =>$aid])->get();
    }
     
    public static function geAnchortLimitProgram($aid){  
        return Program::where(['anchor_id' =>$aid,'parent_prgm_id' =>0])->first();
    }
     
    public static function getLimitAllAnchor(){
        return AppProgramLimit::with('anchorList')->get();
    }
     
    public  function anchorList(){   
        return $this->hasOne('App\Inv\Repositories\Models\Anchor','anchor_id','anchor_id');  
    }   
     
    public static function getLimitSupplier($pid){
        return AppProgramLimit::with('app.user')->where('prgm_id',$pid)->get();
    }  
   
    public function app(){
        return $this->belongsTo('App\Inv\Repositories\Models\Application','app_id','app_id');  
    }
      
    public static function getSingleLimit($aid){
        return self::where('anchor_id',$aid)->first();  
    }

    public static function getProgramBalanceLimit($program_id){
        if(empty($program_id)){
            throw new BlankDataExceptions(trans('error_messages.data_not_found'));
        }
        if(!is_int($program_id)){
            throw new InvalidDataTypeExceptions(trans('error_message.invalid_data_type'));
        }

        $aplids = AppProgramLimit::where('prgm_id', $program_id)->pluck('app_prgm_limit_id');
        if($aplids->count() == 0){
            return 0;
        }else{
            return AppProgramOffer::where('app_prgm_limit_id', $aplids)->sum('prgm_limit_amt');
        }
     }

    public function appLimit(){
        return $this->belongsTo('App\Inv\Repositories\Models\AppLimit', 'app_limit_id', 'app_limit_id');
    }

    //to do
     /*public function programLimit(){
        return $this->belongsTo('App\Inv\Repositories\Models\AppProgramLimit', 'app_prgm_limit_id', 'app_prgm_limit_id');
    }*/  
}
