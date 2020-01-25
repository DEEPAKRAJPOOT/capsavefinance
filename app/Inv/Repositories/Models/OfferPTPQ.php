<?php

namespace App\Inv\Repositories\Models;

use DB;
use App\Inv\Repositories\Factory\Models\BaseModel;
use App\Inv\Repositories\Entities\User\Exceptions\InvalidDataTypeExceptions;
use App\Inv\Repositories\Entities\User\Exceptions\BlankDataExceptions;

class OfferPTPQ extends BaseModel
{
    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'offer_ptpq';

    /**
     * Custom primary key is set for the table
     *
     * @var integer
     */
    protected $primaryKey = 'offer_ptpq_id';

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
        'ptpq_from',
        'ptpq_to',
        'ptpq_rate',
        'created_at',
        'created_by',
        'updated_at',
        'updated_by'
    ];

    
    /**
     * Create offer PTPQ
     * 
     * @param array $data
     * @return boolean
     */

    public static function addOfferPTPQ($data){
        if(!is_array($data)){
            throw new InvalidDataTypeExceptions(trans('error_message.invalid_data_type'));
        }else{
            $offerPtpq =  OfferPTPQ::insert($data);
            return $offerPtpq ? true : false;
        }
    }

}