<?php

namespace App\Inv\Repositories\Models;

use DB;
use App\Inv\Repositories\Factory\Models\BaseModel;

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

}
