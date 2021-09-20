<?php

namespace App\Inv\Repositories\Models\Master;

use App\Inv\Repositories\Factory\Models\BaseModel;
use App\Inv\Repositories\Models\User;

class LocationType extends BaseModel {

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'mst_location_type';

    /**
     * Custom primary key is set for the table
     *
     * @var integer
     */
    protected $primaryKey = 'location_id';

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
    public $userstamps = false;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'is_active',
        'location_code',
        'created_by',
        'created_at',
        'updated_at',
        'updated_by'
    ];

    public function userDetail(){
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Check Location name
     * 
     * @param type $where array
     * @return type mixed
     * @throws BlankDataExceptions
     * @throws InvalidDataTypeExceptions 
     */
    public static function checkLocationType($locationType, $excludeLocationId=null)
    {

        $query = self::select('location_id')
                ->where($locationType);
        if (!is_null($excludeLocationId)) {
            $query->where('location_id', '!=', $excludeLocationId);
        }
        $res = $query->get();        
        return $res ?: [];
    } 
    
    public static function getLocationDropDown()
    {
        $res = self::where('is_active', 1)->pluck('name', 'location_id');
        return $res ?: [];
    }
    

}
