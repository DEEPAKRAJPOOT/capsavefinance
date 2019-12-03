<?php

namespace App\Inv\Repositories\Models;

use DB;
use App\Inv\Repositories\Factory\Models\BaseModel;
use App\Inv\Repositories\Entities\User\Exceptions\BlankDataExceptions;
use App\Inv\Repositories\Entities\User\Exceptions\InvalidDataTypeExceptions;

class Offer extends BaseModel {
    /* The database table used by the model.
     *
     * @var string
     */

    protected $table = 'offer';

    /**
     * Custom primary key is set for the table
     *
     * @var integer
     */
    protected $primaryKey = 'offer_id';

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
        'offer_id',
        'app_id',
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
        'created_by',
        'created_at',
        'updated_by',
        'updated_at',        
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
        
        $appNote = self::select('offer.*')
                ->where($whereCondition)
                ->first();      
        return $appNote;
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
    public static function saveOfferData($offerData=[], $offerId=null)
    {
        //Check $whereCondition is not an array
        if (!is_array($offerData)) {
            throw new InvalidDataTypeExceptions(trans('error_message.invalid_data_type'));
        }
        
        
        if (!is_null($offerId)) {
            return self::where('offer_id', $offerId)->update($offerData);
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

        /**
         * Check Data is not blank
         */
        if (empty($arr)) {
            throw new BlankDataExceptions(trans('error_message.no_data_found'));
        }

        $rowUpdate = self::where('app_id',(int) $app_id)->update($arr);

        return ($rowUpdate ? $rowUpdate : false);
    }    
}
