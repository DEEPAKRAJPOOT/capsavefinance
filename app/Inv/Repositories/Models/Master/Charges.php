<?php

namespace App\Inv\Repositories\Models\Master;

use App\Inv\Repositories\Factory\Models\BaseModel;
use App\Inv\Repositories\Entities\User\Exceptions\BlankDataExceptions;
use App\Inv\Repositories\Entities\User\Exceptions\InvalidDataTypeExceptions;
use App\Inv\Repositories\Models\User;
use Session;
use Auth;
use DB;
 

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
        'sac_code',
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
        'level_charges',
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
        $res = self::where('is_active', '1')->where('based_on', '1')->pluck('chrg_name', 'id');
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

    public function userDetail(){
        return $this->belongsTo(User::class, 'created_by');
    }
    
      public static function saveCharge($attributes)
     {
        $created_at = \carbon\Carbon::now();
        $uid = Auth::user()->user_id;
        $arr  =  [   "chrg_name" => $attributes['chrg_name'],
                     "sac_code" => $attributes['sac_code'],
                     "chrg_desc" => $attributes['chrg_desc'],
                     "credit_desc" => $attributes['credit_desc'],
                     "debit_desc" => $attributes['debit_desc'],
                     "chrg_calculation_type" => $attributes['chrg_calculation_type'],
                     "chrg_calculation_amt" => $attributes['chrg_calculation_amt'],
                     "chrg_applicable_id" => $attributes['chrg_applicable_id'],
                     "chrg_type" => $attributes['chrg_type'],
                     "is_gst_applicable" => $attributes['is_gst_applicable'],
                     "gst_percentage" => $attributes['gst_percentage'],
                     "chrg_tiger_id" => $attributes['chrg_tiger_id'],
                     "based_on" => $attributes['based_on'],
                     "is_active" => $attributes['is_active'],
                     "created_at"    =>$created_at,
                     "created_by" => $uid ];
          return  Charges::create($arr)->id;

      }
    
      public static function getChargeLevel($chrgMstId)
    {
        $res = self::where(['id' => $chrgMstId])->first('level_charges');
        return $res ?: false;
    }
    
}
