<?php

namespace App\Inv\Repositories\Models\Master;

use App\Inv\Repositories\Factory\Models\BaseModel;
use App\Inv\Repositories\Models\User;

class Industry extends BaseModel {

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'mst_industry';

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
    public $userstamps = false;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'cibil_indus_code',
        'is_active',
        'created_by',
        'created_at',
        'updated_at',
        'updated_by'
    ];

    /**
     * Get Drop down list
     *
     * @return type
     */
    public static function getIndustryDropDown()
    {
        $res = self::where('is_active', 1)->pluck('name', 'id');
        return $res ?: [];
    }

    public function userDetail(){
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Check Industry name
     * 
     * @param type $where array
     * @return type mixed
     * @throws BlankDataExceptions
     * @throws InvalidDataTypeExceptions 
     */
    public static function checkIndustryName($industryName, $excludeIndusId=null)
    {

        $query = self::select('id')
                ->where($industryName);
        if (!is_null($excludeIndusId)) {
            $query->where('id', '!=', $excludeIndusId);
        }
        $res = $query->get();        
        return $res ?: [];
    }        

}
