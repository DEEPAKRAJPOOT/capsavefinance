<?php

namespace App\Inv\Repositories\Models\Master;

use App\Inv\Repositories\Factory\Models\BaseModel;
use App\Inv\Repositories\Entities\User\Exceptions\BlankDataExceptions;
use App\Inv\Repositories\Entities\User\Exceptions\InvalidDataTypeExceptions;

class Activity extends BaseModel {

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'mst_activity';

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
        'activity_code',
        'activity_name',
        'is_active',
        'created_by',
        'created_at'
    ];

    /**
     * Get Activity Data
     * 
     * @param array $whereCond
     * @return type mixed     
     * @throws InvalidDataTypeExceptions 
     */
    public static function getActivity($whereCond=[])
    {
        if (!is_array($whereCond)) {
            throw new InvalidDataTypeExceptions(trans('error_messages.invalid_data_type'));
        }
        
        $query = self::where('is_active', '1');
        if (count($whereCond) > 0) {
            $query->where($whereCond);
        }
        $res = $query->get();
        
        return isset($res[0]) ? $res : [];
    }

   

}
