<?php

namespace App\Inv\Repositories\Models;

use DB;
use App\Inv\Repositories\Factory\Models\BaseModel;
use App\Inv\Repositories\Entities\User\Exceptions\InvalidDataTypeExceptions;
use App\Inv\Repositories\Entities\User\Exceptions\BlankDataExceptions;

class OfferCharge extends BaseModel
{
    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'offer_chrg';

    /**
     * Custom primary key is set for the table
     *
     * @var integer
     */
    protected $primaryKey = 'offer_chrg_id';

    /**
     * The attributes that should be mutated to dates.
     *
     * @var array
     */
    //protected $dates = ['deleted_at'];

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
        'charge_id',
        'chrg_value',
        'chrg_type',
        'created_at',
        'created_by',
    ];

    /**
     * @param integer $offer_id
     * @return array
     */

    public static function getOfferCharges($offer_id)
    {
        $res =   self::where('prgm_offer_id',$offer_id)->get();
        return $res ?: false;
    }
    
    /**
     * Create offer Charges
     * 
     * @param array $data
     * @return boolean
     */

    public static function addOfferCharges($data){
        if(!is_array($data)){
            throw new InvalidDataTypeExceptions(trans('error_messages.invalid_data_type'));
        }else{
            $offerChrg =  self::insert($data);
            return $offerChrg ? true : false;
        }
    }

    public function chargeName(){
        return $this->belongsTo('App\Inv\Repositories\Models\Master\Charges', 'charge_id', 'id');
    }
}