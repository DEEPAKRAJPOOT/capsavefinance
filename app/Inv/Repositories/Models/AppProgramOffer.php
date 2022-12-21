<?php

namespace App\Inv\Repositories\Models;

use DB;
use App\Inv\Repositories\Factory\Models\BaseModel;
use App\Inv\Repositories\Entities\User\Exceptions\BlankDataExceptions;
use App\Inv\Repositories\Entities\User\Exceptions\InvalidDataTypeExceptions;
use App\Inv\Repositories\Models\AppApprover;
use App\Inv\Repositories\Models\Application;
use Illuminate\Database\Eloquent\Builder;
use App\Inv\Repositories\Models\OfferPrimarySecurity;
use App\Inv\Repositories\Models\OfferCollateralSecurity;
use App\Inv\Repositories\Models\OfferPersonalGuarantee;
use App\Inv\Repositories\Models\OfferCorporateGuarantee;
use App\Inv\Repositories\Models\OfferEscrowMechanism;
use App\Inv\Repositories\Models\User;
use App\Inv\Repositories\Models\LmsUsersLog;

class AppProgramOffer extends BaseModel {
    /* The database table used by the model.
     *
     * @var string
     */

    protected $table = 'app_prgm_offer';

    /**
     * Custom primary key is set for the table
     *
     * @var integer
     */
    protected $primaryKey = 'prgm_offer_id';

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
        'prgm_offer_id',
        'app_id',
        'app_prgm_limit_id',
        'prgm_id',
        'anchor_id',
        'prgm_limit_amt',
        'loan_amount',
        'loan_offer',        
        'interest_rate',        
        'base_rate',        
        'bank_id',        
        'tenor',        
        'tenor_old_invoice',        
        'margin',
        'overdue_interest_rate',        
        'adhoc_interest_rate',        
        'grace_period',        
        'processing_fee',
        'document_fee',
        'discounting',
        'check_bounce_fee',
        'payment_frequency',
        'benchmark_date',
        'equipment_type_id',
        'facility_type_id',
        'security_deposit_type',
        'security_deposit',
        'security_deposit_of',
        'rental_frequency',
        'rental_frequency_type',
        'dsa_applicable',
        //'ptpq_from',
        //'ptpq_to',
        //'ptpq_rate',
        'ruby_sheet_xirr',
        'cash_flow_xirr',
        'addl_security',
        'is_invoice_processingfee',
        'invoice_processingfee_type',
        'invoice_processingfee_value',
        'comment',
        'asset_type_id',
        'asset_insurance',
        'asset_name',
        'timelines_for_insurance',
        'asset_comment',
        'irr',
        'is_approve',
        'payment_frequency',
        'status',
        'is_active',
        'created_at',
        'created_by',
        'updated_at',        
        'updated_by',
    ];

    /**
     * Get Offer Data
     * 
     * @param array $whereCondition
     * @return mixed
     * @throws InvalidDataTypeExceptions
     */
    public static function getOfferData($whereCondition=[])
    {
        //Check $whereCondition is not an array
        if (!is_array($whereCondition)) {
            throw new InvalidDataTypeExceptions(trans('error_messages.invalid_data_type'));
        }
        
        $whereCondition['is_active'] = isset($whereCondition['is_active']) ? $whereCondition['is_active'] : 1;
        

        $offerWhereCond = [];
        if (isset($whereCondition['status_is_not_null'])) {
            $offerWhereCond['status_is_not_null'] = $whereCondition['status_is_not_null'];
            unset($whereCondition['status_is_not_null']);
        } else if (isset($whereCondition['status_is_null'])) {
            $offerWhereCond['status_is_null'] = $whereCondition['status_is_null'];
            unset($whereCondition['status_is_null']);
        } else if (isset($whereCondition['status']) && is_null(isset($whereCondition['status']))) {            
            $offerWhereCond['status'] = $whereCondition['status'];
            unset($whereCondition['status']);
        } else if (isset($whereCondition['status_is_null_or_accepted'])) { 
            $offerWhereCond['status_is_null_or_accepted'] = $whereCondition['status_is_null_or_accepted'];
            unset($whereCondition['status_is_null_or_accepted']);            
        }
                                
        $query = self::select('app_prgm_offer.*')
                ->where($whereCondition);
        if (isset($offerWhereCond['status_is_not_null'])) {
            $query->whereNotNull('status');
        } else if (isset($offerWhereCond['status_is_null'])) {
            $query->whereNull('status');
        } else if (isset($offerWhereCond['status']) && is_null($offerWhereCond['status'])) {            
            $query->whereNull('status');            
        } else if (isset($offerWhereCond['status_is_null_or_accepted'])) {            
            $query->where(function ($query) {
                $query->where('status', null)
                      ->orWhere('status', '=', 1);
            });
        }

        $offerData = $query->first();      
        return $offerData ? $offerData : null;
    }

   
     /**
     * Get single Offer Data
     * 
     * @param int AppId
     * @return mixed
     * @throws InvalidDataTypeExceptions
     */
    public static function getOfferForLimit($oid)
    {
       
      return  self::where(['app_prgm_limit_id'=>$oid, 'is_active'=>1,'status' =>1 ])->first();      

    }
     public static function getTenor($res)
    {
       $getAppUser  =    Application::where(['user_id' => $res['user_id'],'status' =>2])->pluck('user_id');  
       $lms_user_id =    LmsUser::whereIn('user_id',$getAppUser)->pluck('user_id');
       $app_ids =    AppLimit::whereIn('user_id',$lms_user_id)->where('status',1)->pluck('app_id');
       return self::whereHas('productHas')->whereIn('app_id',$app_ids)->where(['anchor_id' => $res['anchor_id'],'prgm_id'=> $res['prgm_id'], 'is_active' => 1, 'is_approve' => 1, 'status' => 1])->first();     
    }
       public static function getAmountOfferLimit($res)
    {
      return  self::where(['anchor_id'=>$res['anchor_id'],'prgm_id'=>$res['prgm_id'],'app_id'=>$res['app_id'],'is_active'=>1,'status' =>1 ])->sum('prgm_limit_amt');      

    }

    /**
     * Get All Offer Data
     * 
     * @param int AppId
     * @return mixed
     * @throws InvalidDataTypeExceptions
     */
    public static function getAllOffers($appId, $product_id=null)
    {
        /**
         * Check id is not blank
         */
        if (empty($appId)) {
            throw new BlankDataExceptions(trans('error_messages.no_data_found'));
        }

        /**
         * Check id is not an integer
         */
        // if (!is_int($appId)) {
        //     throw new InvalidDataTypeExceptions(trans('error_message.invalid_data_type'));
        // }
        
        $roleData = User::getBackendUser(\Auth::user()->user_id);            
        $whereCond = [];
        if (isset($roleData[0]) && $roleData[0]->id == 11) {   
            $whereCond = ['anchor_id' => \Auth::user()->anchor_id, 'app_id' => $appId, 'is_active' => 1];
        } else {
            $whereCond = ['app_id' => $appId, 'is_active' => 1];
        }
        
        if(is_null($product_id) || $product_id == ''){            
            $offers = self::where($whereCond)->orderBy('prgm_offer_id', 'DESC')->get();
        }else{
            $offers = self::whereHas('programLimit', function(Builder $query) use($product_id){$query->where('product_id', $product_id);})->where($whereCond)->with('offerCharges.chargeName')->orderBy('prgm_offer_id', 'DESC')->get();
        }
        return $offers ? $offers : null;
    }

    /**
     * Save Offer Data
     * 
     * @param array $offerData
     * @param integer $offerId optional
     * 
     * @return mixed
     * @throws InvalidDataTypeExceptions
     */
    public static function saveOfferData($offerData=[], $prgmOfferId=null)
    {
        //Check $whereCondition is not an array
        if (!is_array($offerData)) {
            throw new InvalidDataTypeExceptions(trans('error_messages.invalid_data_type'));
        }
        
        
        if (!is_null($prgmOfferId)) {
            return self::where('prgm_offer_id', $prgmOfferId)->update($offerData);
        } else {
            return self::create($offerData);
        }
    }    

    /**
     * Update Offer Data By Application Id
     * 
     * @param integer $app_id
     * @param array $arr
     * @return mixed
     * @throws BlankDataExceptions
     * @throws InvalidDataTypeExceptions
     */
    public static function updateOfferByAppId($app_id, $arr = [])
    {
        /**
         * Check id is not blank
         */
        if (empty($app_id)) {
            throw new BlankDataExceptions(trans('error_messages.no_data_found'));
        }

        /**
         * Check id is not an integer
         */
        if (!is_int($app_id)) {
            throw new InvalidDataTypeExceptions(trans('error_messages.invalid_data_type'));
        }

        /**
         * Check Data is Array
         */
        if (!is_array($arr)) {
            throw new InvalidDataTypeExceptions(trans('error_messages.send_array'));
        }

        $rowUpdate = self::where('app_id',(int) $app_id)->update($arr);

        return ($rowUpdate ? $rowUpdate : false);
    }

    public static function updateActiveOfferByAppId($app_id, $arr = [])
    {
        /**
         * Check id is not blank
         */
        if (empty($app_id)) {
            throw new BlankDataExceptions(trans('error_messages.no_data_found'));
        }

        /**
         * Check id is not an integer
         */
        if (!is_int($app_id)) {
            throw new InvalidDataTypeExceptions(trans('error_messages.invalid_data_type'));
        }

        /**
         * Check Data is Array
         */
        if (!is_array($arr)) {
            throw new InvalidDataTypeExceptions(trans('error_messages.send_array'));
        }

        $rowUpdate = self::where(['app_id'=>(int) $app_id, 'is_active'=>1])
                        ->where(function($q) {
                            $q->where('status', NULL)
                                ->orWhere('status', 1);
                        })
                        ->update($arr);

        return ($rowUpdate ? $rowUpdate : false);
    }

    public static function getProgramOffer($app_prgm_limit_id){
        if(empty($app_prgm_limit_id)){
            throw new BlankDataExceptions(trans('error_messages.data_not_found'));
        }else if(!is_int($app_prgm_limit_id)){
            throw new InvalidDataTypeExceptions(trans('error_messages.invalid_data_type'));
        }else{
            return AppProgramOffer::with('programLimit.program')->where('app_prgm_limit_id', $app_prgm_limit_id)->where('is_active', 1)->first();
        }
    }

    public static function addProgramOffer($data, $app_prgm_limit_id, $prgm_offer_id=null){
        if(empty($app_prgm_limit_id)){
            throw new BlankDataExceptions(trans('error_messages.data_not_found'));
        }else if(!is_int($app_prgm_limit_id)){
            throw new InvalidDataTypeExceptions(trans('error_messages.invalid_data_type'));
        }else if(!is_array($data)){
            throw new InvalidDataTypeExceptions(trans('error_messages.invalid_data_type'));
        }else{
            $prgmOffer = AppProgramOffer::where('prgm_offer_id', $prgm_offer_id)->where('is_active', 1)->first();
            $rejectPrgmOffer = AppProgramOffer::where('app_id', $data['app_id'])->where('is_active', 1)->orderBy('prgm_offer_id', 'DESC')->first();
            if($rejectPrgmOffer && $rejectPrgmOffer->status == 2) {
                $prgmOffer = $rejectPrgmOffer;
            }
            if($prgmOffer){
                $prgmOffer->update(['is_active' => 0]);
            }
            //AppProgramLimit::where('app_prgm_limit_id', $app_prgm_limit_id)->update(['limit_amt'=> $data['prgm_limit_amt']]);
            return AppProgramOffer::create($data);
        }
    }

    public function programLimit(){
        return $this->belongsTo('App\Inv\Repositories\Models\AppProgramLimit', 'app_prgm_limit_id', 'app_prgm_limit_id');
    }

    public function invPL(){
        return $this->belongsTo('App\Inv\Repositories\Models\AppProgramLimit', 'app_prgm_limit_id', 'app_prgm_limit_id')->where('product_id', 1);
    }

    public static function getTotalOfferedLimit($app_id){
        if(empty($app_id)){
            throw new BlankDataExceptions(trans('error_messages.data_not_found'));
        }
        if(!is_int($app_id)){
            throw new InvalidDataTypeExceptions(trans('error_messages.invalid_data_type'));
        }

        $tot_offered_limit = AppProgramOffer::where(['app_id' => $app_id, 'is_active'=>1])->where(function($q) {
                            $q->where('status', NULL)->orWhere('status', 1);
                        })->sum('prgm_limit_amt');
        
        return $tot_offered_limit;
    }


    public static function checkduplicateOffer($data){
        if(!is_array($data)){
            throw new InvalidDataTypeExceptions(trans('error_message.invalid_data_type'));
        }else{
            return AppProgramOffer::where($data)->get();
        }
    }

    public static function getOfferStatus($where_condition){
        if(!is_array($where_condition)){
            throw new InvalidDataTypeExceptions(trans('error_message.invalid_data_type'));
        }

        return AppProgramOffer::where($where_condition)->count();
    }

    public static function changeOfferApprove($appId){
        if(empty($appId)){
            throw new BlankDataExceptions(trans('error_messages.data_not_found'));
        }
        if(!is_int($appId)){
            throw new InvalidDataTypeExceptions(trans('error_messages.invalid_data_type'));
        }

        $approverStatus = AppApprover::where(['app_id' => $appId, 'is_active'=>1])->where('status', '<>', 1)->count();

        if($approverStatus == 0){
            return AppProgramOffer::where(['app_id' => $appId, 'is_active'=>1])->update(['is_approve'=>1]);
        }else{
            return false;
        }
    }
   
    public function offerPtpq(){
        return $this->hasMany('App\Inv\Repositories\Models\OfferPTPQ', 'prgm_offer_id', 'prgm_offer_id');
    }
  
    public static function getTotalByPrgmLimitId($appPrgmLimitId){
        if(empty($appPrgmLimitId)){
            throw new BlankDataExceptions(trans('error_messages.data_not_found'));
        }
        if(!is_int($appPrgmLimitId)){
            throw new InvalidDataTypeExceptions(trans('error_messages.invalid_data_type'));
        }

        $tot_offered_limit = AppProgramOffer::where(['app_prgm_limit_id' => $appPrgmLimitId, 'is_active'=>1])->where(function($q) {
                            $q->where('status', NULL)->orWhere('status', 1);
                        })->sum('prgm_limit_amt');
        
        return $tot_offered_limit;
    }

    public function offerPs(){
        return $this->hasMany('App\Inv\Repositories\Models\OfferPrimarySecurity', 'prgm_offer_id', 'prgm_offer_id');
    }

    public function offerCs(){
        return $this->hasMany('App\Inv\Repositories\Models\OfferCollateralSecurity', 'prgm_offer_id', 'prgm_offer_id');
    }

    public function offerPg(){
        return $this->hasMany('App\Inv\Repositories\Models\OfferPersonalGuarantee', 'prgm_offer_id', 'prgm_offer_id');
    }

    public function offerCg(){
        return $this->hasMany('App\Inv\Repositories\Models\OfferCorporateGuarantee', 'prgm_offer_id', 'prgm_offer_id');
    }

    public function offerEm(){
        return $this->hasMany('App\Inv\Repositories\Models\OfferEscrowMechanism', 'prgm_offer_id', 'prgm_offer_id');
    }

    public function offerCharges(){
        return $this->hasMany('App\Inv\Repositories\Models\OfferCharge', 'prgm_offer_id', 'prgm_offer_id');
    }
    
    
    public static function getLimitAmount($arr)
    {
     
         if(empty($arr->app_id)){
            throw new BlankDataExceptions(trans('error_messages.data_not_found'));
        }
       //////* get   app_prgm_limit_id behalf of app_id  ********//////////////
       return AppProgramOffer::where(['app_id' => $arr->app_id,'is_approve'=> 1,'status' =>1,'is_approve' =>1])->sum('prgm_limit_amt');
      
    }

    
    public function anchor(){
        return $this->belongsTo('App\Inv\Repositories\Models\Anchor','anchor_id','anchor_id');
    }


    public static function getSingleLimit($aid)
    {
         return self::where('anchor_id',$aid)->first();  
    }

     function anchorOne()
     {
          return $this->belongsTo('App\Inv\Repositories\Models\Anchor', 'anchor_id','anchor_id');  
    
     }
     public  function anchorList(){   
        return $this->hasOne('App\Inv\Repositories\Models\Anchor','anchor_id','anchor_id');  
    }   
     public function app(){
        return $this->belongsTo('App\Inv\Repositories\Models\Application','app_id','app_id');  
    }
    
     function productHas()
    {
        return $this->hasMany('App\Inv\Repositories\Models\AppProgramLimit', 'app_prgm_limit_id','app_prgm_limit_id')->where(['product_id' =>1]);  
    
    }

    public function program(){
        return $this->belongsTo('App\Inv\Repositories\Models\Program','prgm_id','prgm_id');
    }
    
    public function adhoc_limit(){
        return $this->hasOne('App\Inv\Repositories\Models\AppOfferAdhocLimit','prgm_offer_id','prgm_offer_id');
    }
    
    public static function getBulkProgramOfferByPrgmId($attr)
    {
        $result = self::select('app_prgm_offer.*','app.user_id','users.f_name','users.l_name','biz.biz_entity_name','lms_users.customer_id')
                ->join('app', 'app.app_id', '=', 'app_prgm_offer.app_id')
                ->join('biz', 'app.biz_id', '=', 'biz.biz_id')                
                ->join('app_product', 'app_product.app_id', '=', 'app.app_id')
                ->join('users', 'users.user_id', '=', 'app.user_id')       
                ->join('user_detail', 'user_detail.user_id', '=', 'users.user_id')
                ->join('lms_users', function ($join) {
                    $join->on('lms_users.user_id', '=', 'user_detail.user_id');                    
                    $join->on('lms_users.app_id', '=', 'app.app_id');
                })     
                ->where('lms_users.customer_id', $attr['cusomer_id'])
                ->where('app_product.product_id', 1)
                ->where('app_prgm_offer.prgm_id', $attr['prgm_id'])
                ->where('app_prgm_offer.is_approve', 1)
                ->where('app_prgm_offer.status', 1)
                ->where('user_detail.is_active', 1)            
                ->groupBy('app.user_id')        
                ->get();
        
        return isset($result[0]) ? $result : [];
    } 
    
    public static function getProgramOfferByPrgmId($prgmId)
    {
        $result = self::select('app_prgm_offer.*','app.user_id','users.f_name','users.l_name','biz.biz_entity_name','lms_users.customer_id')
                ->join('app', 'app.app_id', '=', 'app_prgm_offer.app_id')
                ->join('biz', 'app.biz_id', '=', 'biz.biz_id')                
                ->join('app_product', 'app_product.app_id', '=', 'app.app_id')
                ->join('users', 'users.user_id', '=', 'app.user_id')          
                ->join('user_detail', 'user_detail.user_id', '=', 'users.user_id') 
                ->join('lms_users', function ($join) {
                    $join->on('lms_users.user_id', '=', 'user_detail.user_id');                    
                    $join->on('lms_users.app_id', '=', 'app.app_id');
                })                
                ->where('app_product.product_id', 1)
                ->where('app_prgm_offer.prgm_id', $prgmId)
                ->where('app_prgm_offer.is_approve', 1)
                ->where('app_prgm_offer.status', 1)
                ->where('app.status', 2)      
                ->where('user_detail.is_active', 1)          
                ->groupBy('app.user_id')        
                ->get();
        
        return isset($result[0]) ? $result : [];
    }
     public static function getUserProgramOfferByPrgmId($prgmId,$user_id)
    {
        
        $result = self::select('app_prgm_offer.*','app.user_id','users.f_name','users.l_name','biz.biz_entity_name','lms_users.customer_id')
                ->join('app', 'app.app_id', '=', 'app_prgm_offer.app_id')
                ->join('biz', 'app.biz_id', '=', 'biz.biz_id')                
                ->join('app_product', 'app_product.app_id', '=', 'app.app_id')
                ->join('users', 'users.user_id', '=', 'app.user_id') 
                ->join('user_detail', 'user_detail.user_id', '=', 'users.user_id')   
                ->join('lms_users', function ($join) {
                    $join->on('lms_users.user_id', '=', 'user_detail.user_id');                    
                    $join->on('lms_users.app_id', '=', 'app.app_id');
                })                
                ->where('app_product.product_id', 1)
                ->where('app_prgm_offer.prgm_id', $prgmId)
                ->where('users.user_id', $user_id)
                ->where('app_prgm_offer.is_approve', 1)
                ->where('app_prgm_offer.status', 1)
                ->where('app.status', 2)  
                ->where('user_detail.is_active', 1)  
                ->groupBy('app.user_id')        
                ->get();
        
        return isset($result[0]) ? $result : [];
    }

    public static function getProgramOfferByAppId($appId, $prgm_offer_id = null)
    {
        $query = self::select('app_prgm_offer.app_id', 'app_prgm_offer.prgm_offer_id', 'app_prgm_offer.prgm_limit_amt', 'offer_chrg.charge_id', 'offer_chrg.chrg_value', 'offer_chrg.chrg_type', 'mst_chrg.chrg_name', 'mst_chrg.is_gst_applicable', 'mst_chrg.gst_percentage', 'mst_chrg.chrg_applicable_id')
                ->join('offer_chrg', 'app_prgm_offer.prgm_offer_id', '=', 'offer_chrg.prgm_offer_id')
                ->join('mst_chrg', 'offer_chrg.charge_id', '=', 'mst_chrg.id')                
                ->where('app_prgm_offer.is_active', '1')
                ->where('app_prgm_offer.app_id', $appId);
                if (!empty($prgm_offer_id)) {
                    $query->where('app_prgm_offer.prgm_offer_id', $prgm_offer_id);
                }
                $result = $query->get();
        return !$result->isEmpty() ? $result : [];
    }

    public static function getAppPrgmOfferById($prgm_offer_id = null)
    {
        return  self::with(['programLimit.appLimit'])->where(['prgm_offer_id'=>$prgm_offer_id])->orderBy('created_at','DESC')->first();
    }
    
    public function chargeName(){
        return $this->belongsTo('App\Inv\Repositories\Models\Master\Charges', 'charge_id', 'id');
    }   
    
    //Anchor Name
    public function anchorUser(){
        return $this->hasOne('App\Inv\Repositories\Models\User', 'anchor_id', 'anchor_id')->where('user_type', 2);
    }    
    
    public static function getPrgmBalLimit($program_id)
    {
        if(empty($program_id)){
            throw new BlankDataExceptions(trans('error_messages.data_not_found'));
        }
        $curDate = \Carbon\Carbon::now()->format('Y-m-d');
        $app_prgm_limit_ids = self::join('app_prgm_limit', 'app_prgm_limit.app_prgm_limit_id', '=', 'app_prgm_offer.app_prgm_limit_id')
                ->where('app_prgm_offer.prgm_id', $program_id)   
                ->where('app_prgm_limit.status', 1)               
                ->where('app_prgm_limit.end_date', '<', $curDate)
                ->pluck('app_prgm_offer.app_prgm_limit_id')
                ->toArray();
        $account_clousers = LmsUsersLog::where('status_id', 35)->pluck('user_id')->toArray();
        
        $appStatusList=[
            config('common.mst_status_id.APP_REJECTED'),
            config('common.mst_status_id.APP_CANCEL'),
            //config('common.mst_status_id.APP_HOLD'),
            //config('common.mst_status_id.APP_DATA_PENDING'),
            config('common.mst_status_id.APP_CLOSED'),
            config('common.mst_status_id.OFFER_LIMIT_REJECTED')
        ];
        
        $whereCond = [];
        $whereCond[] = ['app_prgm_offer.is_active', '=', 1];
        //$whereCond[] = ['app_prgm_offer.status', '=', 1];
        if (is_array($program_id)) {
            $query = self::join('app', 'app.app_id', '=', 'app_prgm_offer.app_id')                    
                    ->whereNotIn('app.curr_status_id', $appStatusList)                    
                    ->whereIn('app_prgm_offer.prgm_id', $program_id)
                    ->whereNotIn('app.user_id', $account_clousers);
        } else {
            $query = self::join('app', 'app.app_id', '=', 'app_prgm_offer.app_id')
                    ->whereNotIn('app.curr_status_id', $appStatusList)
                    ->where('app_prgm_offer.prgm_id', $program_id)
                    ->whereNotIn('app.user_id', $account_clousers);
        }
        $query->where($whereCond);
        $query->where(function($q) {
            $q->where('app_prgm_offer.status', NULL)->orWhere('app_prgm_offer.status', 1);
        });
        if (count($app_prgm_limit_ids) > 0) {
            $query->whereNotIn('app_prgm_offer.app_prgm_limit_id', $app_prgm_limit_ids);
        }
        return $query->sum('prgm_limit_amt');
    }

    public static function checkProgramOffers($program_id)
    {
        $whereCond = [];
        $whereCond[] = ['is_active', '=', 1];
        if (is_array($program_id)) {
            $query = self::whereIn('prgm_id', $program_id);
        } else {
            $query = self::where('prgm_id', $program_id);
        }
        $query->where($whereCond);
        return $query->count();
    }
    
    public function BizInvoice(){
        return $this->belongsTo('App\Inv\Repositories\Models\BizInvoice', 'prgm_offer_id', 'prgm_offer_id');
    }
    
    public function getFrequencyName() {

        $getData = $this->payment_frequency;

        switch ($getData) {
            case '1':
                $frequencyType = 'Upfront';
                break;
            case '2':
                $frequencyType = 'Monthly Interest';
                break;
            case '1':
                $frequencyType = 'Rare';
                break;
            
            default:
                $frequencyType = '';
                break;
        }
        return $frequencyType;
    }

    public static function getAnchorPrgmUserIdsInArray($anchorId, $prgmId)
    {
        $appStatusList = [
            config('common.mst_status_id.APP_REJECTED'),
            config('common.mst_status_id.APP_CANCEL'),
            config('common.mst_status_id.APP_CLOSED'),
            config('common.mst_status_id.OFFER_LIMIT_REJECTED')
        ];
        return  AppProgramOffer::join('prgm', 'app_prgm_offer.prgm_id', '=', 'prgm.prgm_id')
                ->join('app', 'app.app_id', '=', 'app_prgm_offer.app_id')
                ->join('app_product', 'app.app_id', '=', 'app_product.app_id')
                ->where('app_product.product_id', 1)
                ->where('prgm.prgm_id', $prgmId)
                ->where('app_prgm_offer.anchor_id', $anchorId)
                ->where('app_prgm_offer.is_active', 1)
                ->whereNotIn('app.curr_status_id', $appStatusList)
                ->where(function ($query) {
                    $query->whereIn('app_prgm_offer.status', [1])->orWhereNull('app_prgm_offer.status');
                })
                ->groupBy('app.user_id')
                ->pluck('app.user_id')
                ->toArray();
    }

    public static function getActiveProgramOfferByAppId($anchorId, $appId, $prgmId = null)
    {
        $data = self::where('anchor_id', $anchorId)
                ->where('app_id', $appId)
                ->where('is_active', '1')
                ->where('status',1);
        if($prgmId){
            $data->where('prgm_id',$prgmId); 
        }
        return $data->first();
    }
    public function asset()
    {
        return $this->belongsTo('App\Inv\Repositories\Models\Master\Asset', 'asset_type_id', 'id');
    }

    public static function getPrgmOfferByAppId($whereCondition)
    {
        $query = self::where($whereCondition);
        $result = $query->get();
        return !$result->isEmpty() ? $result : [];
    }

    public static function getPrgmOfferData($app_id ){
        return self::where(['is_approve' => 1,'status' => 1,'is_active' => 1,'app_id' => $app_id])->count();

    }

    public static function getData($appId,$prgmId,$anchorId){
        return self::where(['is_active' => 1,'app_id' => $appId,'anchor_id'=>$anchorId,'prgm_id'=>$prgmId])->get();
    }
}
