<?php

namespace App\Inv\Repositories\Models\Master;

use App\Inv\Repositories\Factory\Models\BaseModel;
use App\Inv\Repositories\Entities\User\Exceptions\BlankDataExceptions;
use App\Inv\Repositories\Entities\User\Exceptions\InvalidDataTypeExceptions;

class City extends BaseModel
{
    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'mst_city';

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
        'name',
        'is_active',
        'state_id',
        'created_at',
        'created_by',
        'updated_at',
        'updated_by'
    ];
    
    /**
     * Get cities by state Id
     * 
     * @param Integer $stateId   
     * @return array
     */
    public static function getCity($stateId)
    {
        $cities = self::select('*')
                ->where('state_id',$stateId)
                ->where('is_active',1)
                ->get();
        return $cities ? : [];
    }

}