<?php
namespace App\Inv\Repositories\Models\Lms;

use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use App\Inv\Repositories\Factory\Models\BaseModel;
use App\Inv\Repositories\Entities\User\Exceptions\BlankDataExceptions;
use App\Inv\Repositories\Entities\User\Exceptions\InvalidDataTypeExceptions;

class CibilUserData extends BaseModel {
    /* The database table used by the model.
     *
     * @var string
     */

    protected $table = 'cibil_userdata';

    /**
     * Custom primary key is set for the table
     *
     * @var integer
     */
    protected $primaryKey = 'cibil_userdata_id';

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
        'ac_no',
        'segment_identifier',
        'segment_data',
        'created_at',
        'created_by'
    ];

    public static function getCibilUserDataList(array $where = [], $whereRawCondition = NULL) {
        if (!is_array($where)) {
            throw new InvalidDataTypeExceptions(trans('error_message.send_array'));
        }
        $res = self::where($where);
        if (!empty($whereRawCondition)) {
            $res->whereRaw($whereRawCondition);
        }
        $res = $res->get();
        return $res ?: false;
    }


    public static function getCibilUserData(array $whereCondition = [], $whereRawCondition = NULL) {
        if (!is_array($whereCondition)) {
            throw new InvalidDataTypeExceptions(trans('error_message.send_array'));
        }
        $res = self::where($whereCondition);       
        if (!empty($whereRawCondition)) {
            $res->whereRaw($whereRawCondition);
        }
        return $res;
    }
    
    public static function saveCibilReportsData(array $attributes = []) {
        $created_at = \carbon\Carbon::now();
        $user_id = Auth::user()->user_id;
        if (!is_array($attributes)) {
            throw new InvalidDataTypeExceptions(trans('error_message.invalid_data_type'));
        }
        if (!isset($attributes['created_at'])) {
            $attributes['created_at'] = $created_at;
        }
        if (!isset($attributes['created_by'])) {
            $attributes['created_at'] = $user_id;
        }        
        
        $resp = [
            'status' => 'success',
            'code' => '000',
            'message' => 'success',
        ];
        try {
            $insertId = self::create($attributes)->cibil_report_id;
            $resp['code'] = $insertId;  
            $resp['message'] = 'Record inserted successfully';  
        } catch (\Exception $e) {
            $errorInfo  = $e->errorInfo;
            $resp['status'] = 'error';  
            $resp['code'] = $errorInfo[1] ?? '444';  
            $resp['message'] = preg_replace('#[^A-Za-z./\s\_]+#', '', $errorInfo[2]) ?? 'Some DB Error occured. Try again.';  
        }
        return $resp;
    }

    public function users() {
       return $this->belongsTo('App\Inv\Repositories\Models\User', 'created_by', 'user_id');
    }

    public static function insertBulkData(array $userData = []) {
    	 return self::insert($userData);
    }
}
