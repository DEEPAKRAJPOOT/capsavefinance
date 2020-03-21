<?php

namespace App\Inv\Repositories\Models\Lms;

use DB;
use App\Inv\Repositories\Factory\Models\BaseModel;
use App\Inv\Repositories\Entities\User\Exceptions\BlankDataExceptions;
use App\Inv\Repositories\Entities\User\Exceptions\InvalidDataTypeExceptions;

class RequestAssign extends BaseModel {
    /* The database table used by the model.
     *
     * @var string
     */

    protected $table = 'lms_request_assign';

    /**
     * Custom primary key is set for the table
     *
     * @var integer
     */
    protected $primaryKey = 'req_assign_id';

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
        'from_id',
        'to_id',
        'role_id',        
        'assigned_user_id',
        'req_id',
        'assign_status',
        'assign_type',
        'sharing_comment',
        'is_owner',
        'created_at',
        'created_by',
        'updated_at',
        'updated_by',
    ];

    public static function updateRequestAssignById($reqId, $data)
    {
        $rowUpdate = self::where('req_id', $reqId)->where('is_owner',1)->update($data);
        return ($rowUpdate ? $rowUpdate : false);        
    }
    
    public static function assignRequest($data)
    {
        //Check $reqData is not an array
        if (!is_array($data)) {
            throw new InvalidDataTypeExceptions(trans('error_messages.invalid_data_type'));
        }
        
        if(isset($data[0])) {
            return self::insert($data);
        } else {
            return self::create($data);
        }    
    } 
    
    public static function getAssignedReqData($reqId)
    {
        $result = self::select('*')
                ->where('req_id', $reqId)
                ->where('is_owner',1)
                ->get();
        
        return isset($result[0]) ? $result : [];     
    }
}

