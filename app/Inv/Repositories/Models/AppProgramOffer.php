<?php

namespace App\Inv\Repositories\Models;

use DB;
use App\Inv\Repositories\Factory\Models\BaseModel;
use App\Inv\Repositories\Entities\User\Exceptions\BlankDataExceptions;
use App\Inv\Repositories\Entities\User\Exceptions\InvalidDataTypeExceptions;

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
        'prgm_offer_id',
        'app_id',
        'app_prgm_limit_id',
        'prgm_limit_amt',
        'prgm_id',
        'loan_amount',
        'loan_offer',        
        'interest_rate',        
        'tenor',        
        'tenor_old_invoice',        
        'margin',
        'overdue_interest_rate',        
        'adhoc_interest_rate',        
        'grace_period',        
        'processing_fee',
        'check_bounce_fee',
        'comment',
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
            throw new InvalidDataTypeExceptions(trans('error_message.invalid_data_type'));
        }
        
        $whereCondition['is_active'] = isset($whereCondition['is_active']) ? $whereCondition['is_active'] : 1;
        
        $offerData = self::select('app_prgm_offer.*')
                ->where($whereCondition)
                ->first();      
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
       
      return  self::where(['app_prgm_limit_id'=>$oid,'is_active' =>1, 'is_active'=>1,'status' =>1 ])->first();      

    }

    /**
     * Get All Offer Data
     * 
     * @param int AppId
     * @return mixed
     * @throws InvalidDataTypeExceptions
     */
    public static function getAllOffers($appId)
    {
        /**
         * Check id is not blank
         */
        if (empty($appId)) {
            throw new BlankDataExceptions(trans('error_message.no_data_found'));
        }

        /**
         * Check id is not an integer
         */
        if (!is_int($appId)) {
            throw new InvalidDataTypeExceptions(trans('error_message.invalid_data_type'));
        }

        $offers = self::where(['app_id'=>$appId, 'is_active'=>1])->get();      
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
            throw new InvalidDataTypeExceptions(trans('error_message.invalid_data_type'));
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
            throw new BlankDataExceptions(trans('error_message.no_data_found'));
        }

        /**
         * Check id is not an integer
         */
        if (!is_int($app_id)) {
            throw new InvalidDataTypeExceptions(trans('error_message.invalid_data_type'));
        }

        /**
         * Check Data is Array
         */
        if (!is_array($arr)) {
            throw new InvalidDataTypeExceptions(trans('error_message.send_array'));
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
            throw new BlankDataExceptions(trans('error_message.no_data_found'));
        }

        /**
         * Check id is not an integer
         */
        if (!is_int($app_id)) {
            throw new InvalidDataTypeExceptions(trans('error_message.invalid_data_type'));
        }

        /**
         * Check Data is Array
         */
        if (!is_array($arr)) {
            throw new InvalidDataTypeExceptions(trans('error_message.send_array'));
        }

        $rowUpdate = self::where(['app_id'=>(int) $app_id, 'is_active'=>1])->update($arr);

        return ($rowUpdate ? $rowUpdate : false);
    }

    public static function getProgramOffer($app_prgm_limit_id){
        if(empty($app_prgm_limit_id)){
            throw new BlankDataExceptions(trans('error_messages.data_not_found'));
        }else if(!is_int($app_prgm_limit_id)){
            throw new InvalidDataTypeExceptions(trans('error_message.invalid_data_type'));
        }else{
            return AppProgramOffer::with('programLimit.program')->where('app_prgm_limit_id', $app_prgm_limit_id)->where('is_active', 1)->first();
        }
    }

    public static function addProgramOffer($data, $app_prgm_limit_id){
        if(empty($app_prgm_limit_id)){
            throw new BlankDataExceptions(trans('error_messages.data_not_found'));
        }else if(!is_int($app_prgm_limit_id)){
            throw new InvalidDataTypeExceptions(trans('error_message.invalid_data_type'));
        }else if(!is_array($data)){
            throw new InvalidDataTypeExceptions(trans('error_message.invalid_data_type'));
        }else{
            $prgmOffer = AppProgramOffer::where('app_prgm_limit_id', $app_prgm_limit_id)->where('is_active', 1)->first();
            if($prgmOffer){
                $prgmOffer->update(['is_active'=>0]);
            }

            AppProgramLimit::where('app_prgm_limit_id', $app_prgm_limit_id)->update(['limit_amt'=> $data['prgm_limit_amt']]);
            return AppProgramOffer::create($data);
        }
    }

    public function programLimit(){
        return $this->belongsTo('App\Inv\Repositories\Models\AppProgramLimit', 'app_prgm_limit_id', 'app_prgm_limit_id');
    }
}
