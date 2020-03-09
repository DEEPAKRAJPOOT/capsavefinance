<?php

namespace App\Inv\Repositories\Models;

use DB;
use Auth;
use App\Inv\Repositories\Models\Program;
use App\Inv\Repositories\Models\Application;
use App\Inv\Repositories\Factory\Models\BaseModel;
use Illuminate\Database\Eloquent\Builder;
use App\Inv\Repositories\Entities\User\Exceptions\BlankDataExceptions;
use App\Inv\Repositories\Entities\User\Exceptions\InvalidDataTypeExceptions;
use App\Inv\Repositories\Models\AppProgramOffer;
use App\Inv\Repositories\Models\LmsUser;
use App\Inv\Repositories\Models\User;

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
        'product_id',
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

    public static function getProgramLimitData($appId, $type=null){
        if(empty($appId)){
            throw new BlankDataExceptions(trans('error_messages.data_not_found'));
        }else if(!is_int($appId)){
            throw new InvalidDataTypeExceptions(trans('error_message.invalid_data_type'));
        }else{
            if($type != null)
                return AppProgramLimit::where('app_id', $appId)->where('product_id', $type)->get();
            else
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
        return $this->hasMany('App\Inv\Repositories\Models\AppProgramOffer','app_prgm_limit_id','app_prgm_limit_id')->where('is_active',1);
    }     

   public static function getAllAnchor()
    {
         
       return AppProgramOffer::where(['is_active' =>1,'is_approve' =>1,'status' =>1])->where('prgm_id','<>', null)->with('anchorOne')->groupBy('anchor_id')->get(['anchor_id']);
    }  
    
   /* public static function getAnchorBehalfStatus($status)
    {
         
       return self::where(['status' => $status])->distinct('anchor_id')->with('anchorOne')->get(['anchor_id']);
    }   */
    
     public static function getBusinessName()
     {
        return self::whereHas('supplyOffers')->where(['product_id' =>1])->with('business')->groupBy('biz_id')->get(['biz_id']);
       /// $biz_id =  BizInvoice::pluck('biz_id');
        
     }   
     
     function Business()
     {
          return $this->belongsTo('App\Inv\Repositories\Models\Business', 'biz_id','biz_id');  
     
     } 
     
      public static function getLimitProgram($aid)
     {     
            $user_id =   User::where(['anchor_id' => $aid])->where('anchor_id','<>', null)->pluck('user_id');  //'is_active' => 1,
            $app_id =    LmsUser::whereIn('user_id',$user_id)->pluck('app_id');
          return AppProgramOffer::whereHas('productHas')->whereIn('app_id',$app_id)->where(['anchor_id' => $aid,'is_active' =>1,'is_approve' =>1,'status' =>1])->where('prgm_id','<>', null)->with('program')->groupBy('prgm_id')->get();
     }
      
    public static function getLimitAnchor($aid){
        return AppProgramLimit::with('anchorList')->where(['app_id' =>$aid])->get();
    }
 
        public static function getUserBehalfAnchor($uid)
    {
       return AppProgramLimit::whereHas('supplyOffers')->with('app.user')->where(['product_id' =>1,'anchor_id' => $uid])->get();
    }   
  
    
         public static function getUserBehalfApplication($uid)
    {
       return Application::whereHas('supplyOffers')->with('app.user')->where(['product_id' =>1,'anchor_id' => $uid])->get();
    }   
    
    
    public static function geAnchortLimitProgram($aid){  
        return Program::where(['anchor_id' =>$aid,'parent_prgm_id' =>0])->first();
    }
     
    public static function getLimitAllAnchor(){
            $user_id =    LmsUser::pluck('user_id');
            $achor_id =   User::whereIn('user_id',$user_id)->where('anchor_id','<>', null)->pluck('anchor_id');  
            return AppProgramOffer::whereHas('productHas')->whereIn('anchor_id',$achor_id)->where(['is_active' =>1,'is_approve' =>1,'status' =>1])->where('prgm_id','<>', null)->with('anchorList')->groupBy('anchor_id')->get();
    }
     
    public  function anchorList(){   
        return $this->hasOne('App\Inv\Repositories\Models\Anchor','anchor_id','anchor_id');  
    }   
    
    public static function getLimitSupplier($pid){
        $appID =  AppProgramOffer::whereHas('productHas')->where(['is_active' =>1,'is_approve' =>1,'status' =>1])->where(['prgm_id' => $pid])->pluck('app_id');
        $user_id =    LmsUser::whereIn('app_id',$appID)->pluck('user_id');
        return User::with('app.Business')->whereIn('user_id',$user_id)->get();
        
      }  

   
    public function app(){
        return $this->belongsTo('App\Inv\Repositories\Models\Application','app_id','app_id');  
    }
      
    public static function getSingleLimit($aid){
        return self::where('anchor_id',$aid)->first();  
    }

   public static function getSingleApp($uid){
        return Application::where(['user_id' => $uid,'status' =>1])->first();  
    } 
    
    
    public static function getProgramBalanceLimit($program_id){
        if(empty($program_id)){
            throw new BlankDataExceptions(trans('error_messages.data_not_found'));
        }
        if(!is_int($program_id)){
            throw new InvalidDataTypeExceptions(trans('error_message.invalid_data_type'));
        }

        return AppProgramOffer::where('prgm_id', $program_id)->sum('prgm_limit_amt');
     }

    public function appLimit(){
        return $this->belongsTo('App\Inv\Repositories\Models\AppLimit', 'app_limit_id', 'app_limit_id');
    }

     public function product(){
        return $this->belongsTo('App\Inv\Repositories\Models\Master\Product', 'product_id', 'id');
    }  

    function supplyOffers()
    {
        return $this->hasMany('App\Inv\Repositories\Models\AppProgramOffer', 'app_prgm_limit_id','app_prgm_limit_id')->where(['is_approve' =>1,'is_active' =>1,'status' => 1]);  
    
    }
   

    public static function getLimitWithOffer($appId, $bizId, $productId){
        return self::select('app_prgm_limit.limit_amt', 
                'app_prgm_offer.prgm_offer_id',
                'app_prgm_offer.tenor',
                'app_prgm_offer.equipment_type_id', 
                'app_prgm_offer.security_deposit',
                'app_prgm_offer.rental_frequency',
                'app_prgm_offer.ruby_sheet_xirr',
                'app_prgm_offer.cash_flow_xirr',
                'app_prgm_offer.addl_security',
                'app_prgm_offer.comment',
                'app_prgm_offer.facility_type_id'
                )
                ->join('app_prgm_offer', 'app_prgm_offer.app_prgm_limit_id', '=', 'app_prgm_limit.app_prgm_limit_id')
                ->where('app_prgm_limit.app_id',$appId)
                ->where('app_prgm_limit.biz_id',$bizId)
                ->where('app_prgm_limit.product_id',$productId)
                ->where('app_prgm_offer.is_active', config('common.active.yes'))
                ->first();  
    }

    public function getTotalByPrgmLimitId(){
        return $this->hasMany('App\Inv\Repositories\Models\AppProgramOffer', 'app_prgm_limit_id', 'app_prgm_limit_id')->where(['is_active'=>1])->sum('prgm_limit_amt');
    }

    public static function getTotalPrgmLimitByAppId($appId){
        return AppProgramLimit::where(['app_id'=>$appId])->sum('limit_amt');
    }        
}
