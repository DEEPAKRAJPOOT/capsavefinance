<?php

namespace App\Inv\Repositories\Models\Master;

use App\Inv\Repositories\Factory\Models\BaseModel;
use App\Inv\Repositories\Entities\User\Exceptions\BlankDataExceptions;
use App\Inv\Repositories\Entities\User\Exceptions\InvalidDataTypeExceptions;

class DoaLevelRole extends BaseModel {

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'doa_level_role';

    /**
     * Custom primary key is set for the table
     *
     * @var integer
     */
    //protected $primaryKey = '';

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
        'doa_level_id',
        'role_id'        
    ];

    /**
     * Get DoA Level Roles
     * 
     * @param array $doa_level_id
     * @return type mixed
     */
    public static function getDoaLevelRoles($doa_level_id)
    {      
        $res = self::select('*')                
                ->where('doa_level_id', $doa_level_id)                
                ->get();
        return $res ? : [];
    }
    
    /**
     * Save DoA Level Roles
     * 
     * @param array $data
     * @return type mixed
     */
    public static function saveDoaLevelRoles($data)
    { 
        return self::insert($data);
    }

    /**
     * Save DoA Level Roles
     * 
     * @param array $data
     * @return type mixed
     */
    public static function deleteDoaLevelRoles($doa_level_id)
    {
        return self::where('doa_level_id', $doa_level_id)->delete();
    }
}
