<?php

namespace App\Inv\Repositories\Models;

use DB;
use App\Inv\Repositories\Factory\Models\BaseModel;
use App\Inv\Repositories\Entities\User\Exceptions\BlankDataExceptions;
use App\Inv\Repositories\Entities\User\Exceptions\InvalidDataTypeExceptions;

class AppProgramOfferSanction extends BaseModel {
    /* The database table used by the model.
     *
     * @var string
     */

    protected $table = 'app_prgm_offer_sanction';

    /**
     * Custom primary key is set for the table
     *
     * @var integer
     */
    protected $primaryKey = 'sanction_id';

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
        'sanction_id',
        'prgm_offer_id',
        'delay_pymt_chrg',
        'insurance',
        'bank_chrg',
        'legal_cost',
        'po',
        'pdp',
        'disburs_guide',
        'other_cond',
        'covenants',
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
    public static function getOfferSancationData($whereCondition=[])
    {
        //Check $whereCondition is not an array
        if (!is_array($whereCondition)) {
            throw new InvalidDataTypeExceptions(trans('error_message.invalid_data_type'));
        }
        
        $whereCondition['is_active'] = isset($whereCondition['is_active']) ? $whereCondition['is_active'] : 1;
        
        $offerData = self::select('app_prgm_offer_sanction.*')
                ->where($whereCondition)
                ->first();      
        return $offerData ? $offerData : null;
    }


    /**
     * Get All Offer Data
     * 
     * @param int AppProgramOfferId
     * @return mixed
     * @throws InvalidDataTypeExceptions
     */
    public static function getOfferSanction($offerId)
    {
        /**
         * Check id is not blank
         */
        if (empty($offerId)) {
            throw new BlankDataExceptions(trans('error_message.no_data_found'));
        }

        /**
         * Check id is not an integer
         */
        if (!is_int($offerId)) {
            throw new InvalidDataTypeExceptions(trans('error_message.invalid_data_type'));
        }

        $sanction = self::where(['prgm_offer_id'=>$offerId, 'is_active'=>1])->first();;      
        return $sanction ? $sanction : null;
    }

    /**
     * Save Offer Sanction Data
     * 
     * @param array $sanctionData
     * @param integer $sanctionId optional
     * 
     * @return mixed
     * @throws InvalidDataTypeExceptions
     */
    public static function saveSanctionData($sanctionData=[], $sanctionId=null)
    {
        //Check $whereCondition is not an array
        if (!is_array($sanctionData)) {
            throw new InvalidDataTypeExceptions(trans('error_message.invalid_data_type'));
        }

        if (!is_null($sanctionId)) {
            $sanctionId =  self::where('sanction_id', $sanctionId)->update($sanctionData);
            if($sanctionId){
                return self::find($sanctionId);
            }else{
                return $sanctionId;
            }
        } else {
            return self::create($sanctionData);
        }
    }    

    /**
     * Update Sanction Data By Offer Id
     * 
     * @param integer $offer_id
     * @param array $arr
     * @return mixed
     * @throws BlankDataExceptions
     * @throws InvalidDataTypeExceptions
     */
    public static function updateSanctionByOfferId($offer_id, $arr = [])
    {
        /**
         * Check id is not blank
         */
        if (empty($offer_id)) {
            throw new BlankDataExceptions(trans('error_message.no_data_found'));
        }

        /**
         * Check id is not an integer
         */
        if (!is_int($offer_id)) {
            throw new InvalidDataTypeExceptions(trans('error_message.invalid_data_type'));
        }

        /**
         * Check Data is Array
         */
        if (!is_array($arr)) {
            throw new InvalidDataTypeExceptions(trans('error_message.send_array'));
        }

        $rowUpdate = self::where('prgm_offer_id',(int) $offer_id)->update($arr);

        return ($rowUpdate ? $rowUpdate : false);
    }

   

    public function offer(){
        return $this->belongsTo('App\Inv\Repositories\Models\AppProgramOffer', 'prgm_offer_id', 'prgm_offer_id');
    }

    
}
