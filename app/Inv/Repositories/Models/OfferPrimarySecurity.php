<?php

namespace App\Inv\Repositories\Models;

use DB;
use App\Inv\Repositories\Factory\Models\BaseModel;
use App\Inv\Repositories\Entities\User\Exceptions\InvalidDataTypeExceptions;
use App\Inv\Repositories\Entities\User\Exceptions\BlankDataExceptions;

class OfferPrimarySecurity extends BaseModel
{
    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'offer_primary_security';

    /**
     * Custom primary key is set for the table
     *
     * @var integer
     */
    protected $primaryKey = 'offer_primary_security_id';

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
        'ps_security_id',
        'ps_type_of_security_id',
        'ps_status_of_security_id',
        'ps_time_for_perfecting_security_id',
        'ps_desc_of_security',
        'created_at',
        'created_by'
    ];

    /**
     * @param integer $offer_id
     * @return array
     */

    public static function getOfferPrimarySecurity($offer_id)
    {
        $res =   self::where('prgm_offer_id',$offer_id)->get();
        return $res ?: false;
    }
    
    /**
     * Create offer Primary Security
     * 
     * @param array $data
     * @return boolean
     */

    public static function addOfferPrimarySecurity($data){
        if(!is_array($data)){
            throw new InvalidDataTypeExceptions(trans('error_message.invalid_data_type'));
        }else{
            $offerPs =  self::insert($data);
            return $offerPs ? true : false;
        }
    }

}