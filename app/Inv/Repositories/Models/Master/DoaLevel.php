<?php

namespace App\Inv\Repositories\Models\Master;

use App\Inv\Repositories\Factory\Models\BaseModel;
use App\Inv\Repositories\Entities\User\Exceptions\BlankDataExceptions;
use App\Inv\Repositories\Entities\User\Exceptions\InvalidDataTypeExceptions;

class DoaLevel extends BaseModel {

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'doa_level';

    /**
     * Custom primary key is set for the table
     *
     * @var integer
     */
    protected $primaryKey = 'doa_level_id';

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
        'level_name',
        'level_code',
        'state_id',
        'city_id',
        'min_amount',
        'max_amount',
        'is_active',
        'created_by',
        'created_at',
        'updated_at',
        'updated_by'
    ];

    /**
     * Get DoA Levels
     * 
     * @param array $where
     * @return type mixed
     * @throws BlankDataExceptions
     * @throws InvalidDataTypeExceptions 
     */
    public static function getDoaLevels($where=[])
    {
        //if (empty($where)) {
        //    throw new BlankDataExceptions(trans('error_message.no_data_found'));
        //}
        //if (!is_array($where)) {
        //    throw new InvalidDataTypeExceptions(trans('error_message.send_array'));
        //}
        $res = self::select('*')
                ->where('is_active', 1)
                ->orderBy('doa_level_id', 'DESC')
                ->get();
        return $res ? : [];
    }
    
    /**
     * Get DoA Level Data By doa_level_id
     * 
     * @param integer $doa_level_id
     * @return type mixed
     * @throws BlankDataExceptions
     * @throws InvalidDataTypeExceptions 
     */
    public static function getDoaLevelById($doa_level_id)
    {
        if (empty($doa_level_id)) {
            throw new BlankDataExceptions(trans('error_message.no_data_found'));
        }
        
        if (!is_int($doa_level_id)) {
            throw new InvalidDataTypeExceptions(trans('error_message.send_array'));
        }
        $res = self::select('*')
                ->where('is_active', 1)
                ->where('doa_level_id', $doa_level_id)
                ->first();
        return $res ? : false;
    }
    
    /**
     * Get Latest DoA Data
     * 
     * @return mixed
     */
    public static function getLatestDoaData()
    {
        $res = self::select('*')
                ->where('is_active', 1)
                ->orderBy('doa_level_id', 'DESC')
                ->first();
        return $res ? : false;
    }
    
    /**
     * Save DoA Data
     * 
     * @return mixed
     */
    public static function saveDoaLevelData($data, $doa_level_id=null)
    {
        if (is_null($doa_level_id)) {
            return self::create($data);
        } else {
            return self::update($data)->where('doa_level_id', $doa_level_id);
        }
        
    }    

}
