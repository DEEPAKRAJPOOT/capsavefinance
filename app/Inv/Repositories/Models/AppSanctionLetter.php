<?php

namespace App\Inv\Repositories\Models;

use DB;
use App\Inv\Repositories\Factory\Models\BaseModel;
use App\Inv\Repositories\Entities\User\Exceptions\BlankDataExceptions;
use App\Inv\Repositories\Entities\User\Exceptions\InvalidDataTypeExceptions;

class AppSanctionLetter extends BaseModel {
    /* The database table used by the model.
     *
     * @var string
     */

    protected $table = 'app_sanction_letter';

    /**
     * Custom primary key is set for the table
     *
     * @var integer
     */
    protected $primaryKey = 'sanction_letter_id';

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
        'sanction_letter_id',
        'prgm_offer_id',
        'app_id',
        'ref_no',
        'date_of_final_submission',
        'sanction_content', 
        'status', 
        'is_active',
        'is_regenerated',
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
    public static function getOfferNewSancationLetterData($whereCondition=[])
    {
        //Check $whereCondition is not an array
        if (!is_array($whereCondition)) {
            throw new InvalidDataTypeExceptions(trans('error_message.invalid_data_type'));
        }
        
        $whereCondition['is_active'] = isset($whereCondition['is_active']) ? $whereCondition['is_active'] : 1;
        
        $offerData = self::select('app_sanction_letter.*')
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
    public static function getOfferNewSanctionLetter($offerId, $sanctionID)
    {
        // /**
        //  * Check id is not blank
        //  */
        // if (empty($offerId) && empty($sanctionID)) {
        //     throw new BlankDataExceptions(trans('error_message.no_data_found'));
        // }

        // /**
        //  * Check id is not an integer
        //  */
        // if (!is_int($offerId) && !is_int($sanctionID)) {
        //     throw new InvalidDataTypeExceptions(trans('error_message.invalid_data_type'));
        // }

        $sanction = self::where(['is_active'=>1]);
        if(!empty($sanctionID)){
            $sanction = $sanction->where(['sanction_letter_id'=>$sanctionID]);  
        }
        if(!empty($offerId)){
            $sanction = $sanction->where(['prgm_offer_id'=>$offerId]);
        }
        $sanction = $sanction->first();        
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
    public static function saveNewSanctionLetterData($sanctionData=[], $sanctionId=null)
    {
        //Check $whereCondition is not an array
        if (!is_array($sanctionData)) {
            throw new InvalidDataTypeExceptions(trans('error_message.invalid_data_type'));
        }
        if (!is_null($sanctionId)) {
            $sanctionId =  self::where('sanction_letter_id', $sanctionId)->update($sanctionData);
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
    public static function updateNewSanctionLetterByOfferId($offer_id, $arr = [])
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

    /**
     * Get All Offer Data
     * 
     * @param int AppProgramOfferId
     * @return mixed
     * @throws InvalidDataTypeExceptions
     */
    public static function getOfferNewSanctionLetterData($whereCondition=[],$orderBy=false,$onlyFirst='no')
    {
        if($onlyFirst == 'yes'){
            $sanction = self::where($whereCondition);
            if($orderBy){
                $sanction  = $sanction->orderBy($orderBy, 'DESC');
            }
            $sanction  =$sanction->first();
        }else{
            $sanction = self::where($whereCondition);
            if($orderBy){
                $sanction  = $sanction->orderBy($orderBy, 'DESC');
            }
            $sanction  =$sanction->get();
        }      
        return $sanction ? $sanction : null;
    }

   

    public function offer(){
        return $this->belongsTo('App\Inv\Repositories\Models\AppProgramOffer', 'prgm_offer_id', 'prgm_offer_id');
    }

    
}
