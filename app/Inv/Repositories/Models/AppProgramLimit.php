<?php

namespace App\Inv\Repositories\Models;

use DB;
use Auth;
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
        return $this->hasOne('App\Inv\Repositories\Models\AppProgramOffer','app_prgm_limit_id','app_prgm_limit_id')->where('is_active',1);
    }     


       function anchorOne()
     {
          return $this->belongsTo('App\Inv\Repositories\Models\Anchor', 'anchor_id','anchor_id');  
    
     }
     
      public static function getAllAnchor()
    {
         
       return self::whereHas('supplyOffers')->where(['product_id' =>1])->with('anchorOne')->groupBy('anchor_id')->get(['anchor_id']);
    }  
    
   /* public static function getAnchorBehalfStatus($status)
    {
         
       return self::where(['status' => $status])->distinct('anchor_id')->with('anchorOne')->get(['anchor_id']);
    }   */
    
     public static function getBusinessName()
     {
        return self::whereHas('supplyOffers')->where(['product_id' =>1])->with('business')->groupBy('biz_id')->get(['biz_id']);
     }   
     
     function Business()
     {
          return $this->belongsTo('App\Inv\Repositories\Models\Business', 'biz_id','biz_id');  
     
     } 
     
      public static function getLimitProgram($aid)
     {
     
         return AppProgramLimit::whereHas('supplyOffers')->with('program')->where(['product_id' =>1,'anchor_id' =>$aid])->groupBy('prgm_id')->get();
     }
     
    public static function getLimitAnchor($aid){
        return AppProgramLimit::with('anchorList')->where(['app_id' =>$aid])->get();
    }
 
        public static function getUserBehalfAnchor($uid)
    {
       return AppProgramLimit::whereHas('supplyOffers')->with('app.user')->where(['product_id' =>1,'anchor_id' => $uid])->get();
    }   
  
    public static function geAnchortLimitProgram($aid){  
        return Program::where(['anchor_id' =>$aid,'parent_prgm_id' =>0])->first();
    }
     
    public static function getLimitAllAnchor(){
        return AppProgramLimit::whereHas('supplyOffers')->where(['product_id' =>1])->with('anchorList')->groupBy('anchor_id')->get();
    }
     
    public  function anchorList(){   
        return $this->hasOne('App\Inv\Repositories\Models\Anchor','anchor_id','anchor_id');  
    }   
    
    public static function getLimitSupplier($pid){
        return AppProgramLimit::whereHas('supplyOffers')->with('app.user')->where(['product_id' =>1,'prgm_id' => $pid])->get();
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

     public function product(){
        return $this->belongsTo('App\Inv\Repositories\Models\Master\Product', 'product_id', 'id');
    }  

    
     function supplyOffers()
     {
          return $this->hasMany('App\Inv\Repositories\Models\AppProgramOffer', 'app_prgm_limit_id','app_prgm_limit_id')->where(['is_approve' =>1,'is_active' =>1,'status' => 1]);  
     
     } 
    //to do
     /*public function programLimit(){
        return $this->belongsTo('App\Inv\Repositories\Models\AppProgramLimit', 'app_prgm_limit_id', 'app_prgm_limit_id');
    }*/  

    public static function getLimitWithOffer($appId, $bizId, $productId){
        return self::select('app_prgm_limit.limit_amt', 
                'app_prgm_offer.tenor',
                'app_prgm_offer.equipment_type_id', 
                'app_prgm_offer.security_deposit',
                'app_prgm_offer.rental_frequency',
                'app_prgm_offer.ptpq_from',
                'app_prgm_offer.ptpq_to',
                'app_prgm_offer.ptpq_rate',
                'app_prgm_offer.ruby_sheet_xirr',
                'app_prgm_offer.cash_flow_xirr',
                'app_prgm_offer.addl_security',
                'app_prgm_offer.comment'
                )
                ->join('app_prgm_offer', 'app_prgm_offer.app_prgm_limit_id', '=', 'app_prgm_limit.app_prgm_limit_id')
                ->where('app_prgm_limit.app_id',$appId)
                ->where('app_prgm_limit.biz_id',$bizId)
                ->where('app_prgm_limit.product_id',$productId)
                ->where('app_prgm_offer.is_active', config('common.active.yes'))
                ->first();  
    }
}
