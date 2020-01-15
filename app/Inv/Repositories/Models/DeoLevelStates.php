<?php

namespace App\Inv\Repositories\Models;

use App\Inv\Repositories\Factory\Models\BaseModel;
use App\Inv\Repositories\Entities\User\Exceptions\BlankDataExceptions;
use App\Inv\Repositories\Entities\User\Exceptions\InvalidDataTypeExceptions;

class DeoLevelStates extends BaseModel {

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'doa_level_states';

    /**
     * Custom primary key is set for the table
     *
     * @var integer
     */
    //  protected $primaryKey = 'cam_hygiene_id';

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
        'doa_level_id', 'state_id', 'city_id'
    ];
    
    
    /**
     * doa level 
     * 
     * @return mixed
     */
    
     public function doaLevel()
    {
        return $this->belongsTo('App\Inv\Repositories\Models\DoaLevel', 'doa_level_id', 'doa_level_id');
    }

    /**
     * Save deoLevel States
     * 
     * @param type $attributes
     * @return type mixed
     * @throws InvalidDataTypeExceptions
     * @throws BlankDataExceptions 
     */
    public static function saveDeoLevelStates($attributes)
    {
        if (!is_array($attributes)) {
            throw new InvalidDataTypeExceptions(trans('error_messages.send_array'));
        }

        //Check data is not blank
        if (empty($attributes)) {
            throw new BlankDataExceptions(trans('error_messages.data_not_found'));
        }

        return self::insert($attributes);
    }

    
    /**
     * Delete deo level
     * 
     * @param Array $where 
     * @return mixed  
     * @throws InvalidDataTypeExceptions
     * @throws BlankDataExceptions 
     */
    public static function deleteDeoLevelStates($where)
    {
        if (!is_array($where)) {
            throw new InvalidDataTypeExceptions(trans('error_messages.send_array'));
        }

        //Check data is not blank
        if (empty($where)) {
            throw new BlankDataExceptions(trans('error_messages.data_not_found'));
        }

        return self::where($where)->delete();
    }
    
    

}
