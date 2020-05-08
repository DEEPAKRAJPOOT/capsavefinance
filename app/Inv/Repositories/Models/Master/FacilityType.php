<?php

namespace App\Inv\Repositories\Models\Master;

use App\Inv\Repositories\Factory\Models\BaseModel;
use App\Inv\Repositories\Entities\User\Exceptions\BlankDataExceptions;
use App\Inv\Repositories\Entities\User\Exceptions\InvalidDataTypeExceptions;

class FacilityType extends BaseModel
{
    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'mst_facility_type';

    /**
     * Custom primary key is set for the table
     *
     * @var integer
     */
    protected $primaryKey = 'id';

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
        'facility_type',
        'is_active',
        'created_at',
        'created_by',
        'updated_at',
        'updated_by'
    ]; 
    
    
    
    /**
     * get Equipment list
     * 
     * @return mixed
     */    
    public static function getFacilityTypeList()
    {
        $res =   self::where('is_active',1)->pluck('facility_type', 'id');
        return $res ?: [];
    }

    /**
     * Get Facility Type By Id
     * 
     * @param integer $id
     * @return mixed
     */
    public static function getFacilityTypeById($id)
    {
        $res =   self::select('facility_type')
                      ->where('id',$id)
                      ->where('is_active',1)
                      ->first();
        return $res ?: false;
    }
}