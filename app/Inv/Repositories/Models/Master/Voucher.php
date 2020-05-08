<?php

namespace App\Inv\Repositories\Models\Master;

use App\Inv\Repositories\Factory\Models\BaseModel;
use App\Inv\Repositories\Models\Lms\TransType;
use App\Inv\Repositories\Entities\User\Exceptions\BlankDataExceptions;
use App\Inv\Repositories\Entities\User\Exceptions\InvalidDataTypeExceptions;
use App\Inv\Repositories\Models\User;
use Session;
use Auth;
use DB;
 

class Voucher extends BaseModel
{

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'tally_voucher';

    /**
     * Custom primary key is set for the table
     *
     * @var integer
     */
    protected $primaryKey = 'tally_voucher_id';

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
        'trans_type_id',
        'voucher_name',
        'created_at',
        'created_by',
        'updated_at',
        'updated_by',
    ];

    /**
     * get Charge list
     * 
     * @param type $where array
     * @return type mixed
     * @throws BlankDataExceptions
     * @throws InvalidDataTypeExceptions 
     */
    public static function getVoucherList()
    {
        $res = self::where('is_active', '1')->pluck('voucher_name', 'trans_type_id');
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
    public static function getVoucherData($where)
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

    public function transType(){
        return $this->belongsTo(TransType::class, 'trans_type_id');
    }
    
      public static function saveVoucher($attributes) {
        $created_at = \carbon\Carbon::now();
        $uid = Auth::user()->user_id;
        $arr  =  [  
                     "voucher_name" => $attributes['voucher_name'],
                     "trans_type_id" => $attributes['trans_type_id'],
                     "created_at"    =>$created_at,
                     "created_by" => $uid
                 ];
        $resp = [
            'status' => 'success',
            'code' => '000',
            'message' => 'success',
        ];
         try {
            $insertId = self::create($arr)->tally_voucher_id;
            $resp['code'] = $insertId;  
            $resp['message'] = 'Voucher inserted successfuly';  
        } catch (\Exception $e) {
            $errorInfo  = $e->errorInfo;
            $resp['status'] = 'error';  
            $resp['code'] = $errorInfo[1] ?? '444';  
            $resp['message'] = preg_replace('#[^A-Za-z./\s\_]+#', '', $errorInfo[2]) ?? 'Some DB Error occured. Try again.';  
        }
        return $resp;
      }
    
    
}
