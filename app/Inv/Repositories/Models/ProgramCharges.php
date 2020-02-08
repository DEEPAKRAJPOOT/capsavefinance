<?php

namespace App\Inv\Repositories\Models;

use App\Inv\Repositories\Factory\Models\BaseModel;
use App\Inv\Repositories\Entities\User\Exceptions\BlankDataExceptions;
use App\Inv\Repositories\Entities\User\Exceptions\InvalidDataTypeExceptions;

class ProgramCharges extends BaseModel {

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'prgm_chrg';

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
        'prgm_id', 'charge_id', 'chrg_name', 'chrg_desc', 'chrg_type', 'chrg_calculation_type', 'chrg_calc_min_rate', 'chrg_calc_max_rate', 'chrg_calculation_amt', 'gst_percentage', 'chrg_applicable_id', 'is_gst_applicable', 'chrg_tiger_id', 'is_active', 'created_at', 'created_by'
    ];
    
    
    public static function getProgram()
    {
        return self::with('program')->where(['is_active' => 1])->get();
    }
    
    public function program()
    {
        return $this->belongsTo('App\Inv\Repositories\Models\Program', 'prgm_id', 'prgm_id');
    }


    /**
     * get Charge list
     * 
     * @param type $where array
     * @return type mixed
     * @throws BlankDataExceptions
     * @throws InvalidDataTypeExceptions 
     */
    public static function getCharagesList()
    {
        $res = self::where('is_active', '1')->pluck('chrg_name', 'id');
        return $res ?: false;
    }
    
    

    /**
     * get charge Data
     * 
     * @param type $where array
     * @return type mixed
     * @throws BlankDataExceptions
     * @throws InvalidDataTypeExceptions 
     */
    public static function getChargeData($where)
    {
        if (empty($where)) {
            throw new BlankDataExceptions(trans('error_message.no_data_found'));
        }
        if (!is_array($where)) {
            throw new InvalidDataTypeExceptions(trans('error_message.send_array'));
        }

        $res = self::where($where)->where('is_active', '1')->get();
        return $res ?: false;
    }

    /**
     * save program charge 
     *  
     * @param type $attr Array
     * @return type mixed 
     * @throws BlankDataExceptions
     * @throws InvalidDataTypeExceptions 
     */
    public static function saveProgramChrgData($attr)
    {
        if (empty($attr)) {
            throw new BlankDataExceptions(trans('error_message.no_data_found'));
        }
        if (!is_array($attr)) {
            throw new InvalidDataTypeExceptions(trans('error_message.send_array'));
        }

        $res = self::insert($attr);
        return $res ?: false;
    }

    /**
     * Delete program charge data
     * 
     * @param type $where Array 
     * @return type mixed
     * @throws BlankDataExceptions
     * @throws InvalidDataTypeExceptions
     */
    public static function deleteProgramData($where)
    {

        if (empty($where)) {
            throw new BlankDataExceptions(trans('error_message.no_data_found'));
        }
        if (!is_array($where)) {
            throw new InvalidDataTypeExceptions(trans('error_message.send_array'));
        }
        $res = self::where($where)->delete();
        return $res ?: false;
    }

}
