<?php

namespace App\Inv\Repositories\Models\Lms;

use App\Inv\Repositories\Factory\Models\BaseModel;
use App\Inv\Repositories\Entities\User\Exceptions\BlankDataExceptions;
use App\Inv\Repositories\Entities\User\Exceptions\InvalidDataTypeExceptions;
use App\Inv\Repositories\Models\User;
use App\Inv\Repositories\Models\ProgramCharges;



class Charges extends BaseModel
{

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'mst_chrg';

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
        'chrg_name',
        'chrg_desc',
        'credit_desc',
        'debit_desc',
        'chrg_type',
        'chrg_calculation_type',
        'chrg_calculation_amt',
        'chrg_applicable_id',
        'is_gst_applicable',
        'gst_percentage',
        'chrg_tiger_id',
        'based_on',
        'is_active',
        'created_at',
        'created_by'
    ];

    



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

    public static function getTransName($attr)
    {
        $res = self::where('is_active', '1')->where('based_on', $attr->based_on)->get();
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

    public static function getTransData($where)
    {
        if (empty($where)) {
            throw new BlankDataExceptions(trans('error_message.no_data_found'));
        }
        if (!is_array($where)) {
            throw new InvalidDataTypeExceptions(trans('error_message.send_array'));
        }

        $res = self::where($where)->get();
        return $res ?: false;
    }
    
    public function userDetail(){
        return $this->belongsTo(User::class, 'created_by');
    }
    
  
     public static function getSingleChargeAmount($attr)
    {
        $res = self::where('id', $attr['id'])->first();
        return $res ?: false;
    }
    
    
    /**
     * Check Charge Name
     * 
     * @param type $where array
     * @return type mixed
     * @throws BlankDataExceptions
     * @throws InvalidDataTypeExceptions 
     */
    public static function checkChargeName($chargeName, $excludeChargeId=null)
    {

        $query = self::select('id')
                ->where('chrg_name', $chargeName);
        if (!is_null($excludeChargeId)) {
            $query->where('id', '!=', $excludeChargeId);
        }
        $res = $query->get();        
        return $res ?: [];
    }    
   
}
