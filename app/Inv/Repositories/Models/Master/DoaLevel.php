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
        'product_id',
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
     * doa level stage
     * 
     * @return mixed
     */
    public function doaLevelStates()
    {
        return $this->hasMany('App\Inv\Repositories\Models\DeoLevelStates', 'doa_level_id', 'doa_level_id')
                  ->join('mst_city', 'doa_level_states.city_id','mst_city.id')->select('doa_level_states.*','mst_city.name'); 
    }

    /**
     * Get DoA Levels for Data Render
     *      
     * @return type mixed
     */
    public static function getDoaLevels()
    {
        $groupBy = ['doa_level.product_id','doa_level.city_id', 'doa_level.min_amount', 'doa_level.max_amount'];
        $res = self::with('product')->select('doa_level.*')
               // ->join('mst_city', 'mst_city.id', '=', 'doa_level.city_id')
                //->where('doa_level.is_active', 1)
                ->groupBy($groupBy)
                ->orderBy('doa_level.doa_level_id', 'DESC');
       /// ->get();
        return $res ?: [];
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
            throw new InvalidDataTypeExceptions(trans('error_message.invalid_data_type'));
        }
        $res = self::select('doa_level.*')
                //->join('mst_city', 'mst_city.id', '=', 'doa_level.city_id')
                ->where('doa_level.is_active', 1)
                ->where('doa_level.doa_level_id', $doa_level_id)
                ->first();
        return $res ?: false;
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
        return $res ?: false;
    }

    /**
     * Save DoA Data
     * 
     * @return mixed
     */
    public static function saveDoaLevelData($data, $doa_level_id = null)
    {
        if (is_null($doa_level_id)) {
            return self::create($data);
        } else {
            return self::where('doa_level_id', $doa_level_id)->update($data);
        }
    }

     /**
     * Update DoA 
     * 
     * @param type $attributes
     * @param type $conditions
     * @return type
     * @throws InvalidDataTypeExceptions
     * @throws BlankDataExceptions 
     */
    public static function updateDoaLevelData($attributes = [], $conditions = [])
    {
        /**
         * Check Data is Array
         */
        if (!is_array($attributes)) {
            throw new InvalidDataTypeExceptions(trans('error_message.send_array'));
        }

       
        /**
         * Check Data is Array
         */
        if (!is_array($conditions)) {
            throw new InvalidDataTypeExceptions(trans('error_message.send_array'));
        }

        /**
         * Check Data is not blank
         */
        if (empty($conditions)) {
            throw new BlankDataExceptions(trans('error_message.no_data_found'));
        }
        $res = self::where($conditions)->update($attributes);
     
        return ($res ?: false);
    }

    /**
     * Get DoA Levels
     * 
     * @param array $where
     * @return type mixed
     * @throws BlankDataExceptions
     * @throws InvalidDataTypeExceptions 
     */
    public static function getDoaLevelData($where)
    {
        if (empty($where)) {
            throw new BlankDataExceptions(trans('error_message.no_data_found'));
        }
        if (!is_array($where)) {
            throw new InvalidDataTypeExceptions(trans('error_message.send_array'));
        }
        $res = self::select('*')
                ->where('is_active', 1)
                ->where($where)
                ->first();
        return $res ?: null;
    }

    /**
     * get DOA Level list
     * 
     * @return type mixed
     */
    public static function getDoaLevelList()
    {
        $res = self::where('is_active', 1)->pluck('level_name', 'doa_level_id');
        return $res ?: false;
    }
    
      function product()
     {
          return $this->belongsTo('App\Inv\Repositories\Models\Product', 'product_id','id');  
    
     }
    public static function getProIdByDoaLevel($doid)
    {
        $res = self::where('doa_level_id',$doid)->first();
        return $res ?: false;
    } 
     

    /**
     * Get DoA Data
     * 
     * @return mixed
     */
    public static function getLastDoaLevelId()
    {
        $res = self::select('*')               
                ->orderBy('doa_level_id', 'DESC')
                ->first();
        return $res ?: false;
    }  
    
    /**
     * Get DoA Name exists
     * 
     * @param array $where
     * @return type mixed
     * @throws BlankDataExceptions
     * @throws InvalidDataTypeExceptions 
     */
    public static function getDoaNameExists($where)
    {
        if (empty($where)) {
            throw new BlankDataExceptions(trans('error_message.no_data_found'));
        }
        if (!is_array($where)) {
            throw new InvalidDataTypeExceptions(trans('error_message.send_array'));
        }
        $res = self::where('is_active', 1)
                ->where($where)
                ->count();
        return $res ?: null;
    } 
    
    public static function getDoaNameEditCaseExists($where, $doa_id) {
        return self::where($where)
                    ->whereNotIn('doa_level_id', $doa_id)
                    ->count();
    }    
}
