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
    
    public static function isRequestOwner($reqId, $assignedUserId)
    {
        $result = self::select('*')
                ->where('req_id', $reqId)
                ->where('to_id', $assignedUserId)
                ->where('is_owner',1)
                ->first();
        
        return $result ? true : false;     
    }
    
    /**
     * Get Request current assignee
     * 
     * @param integer $reqId
     * @return mixed
     */
    public static function getReqCurrentAssignee($reqId)
    {
        $assignData = self::select(DB::raw("CONCAT_WS(' ', rta_to_u.f_name, rta_to_u.l_name) AS assignee"),                 
                'to_r.name as assignee_role',
                DB::raw("CONCAT_WS(' ', rta_from_u.f_name, rta_from_u.l_name) AS assigned_by"), 
                'from_r.name as from_role'
                )
                ->leftJoin('users as to_u', 'lms_request_assign.to_id', '=', 'to_u.user_id')
                ->leftJoin('role_user as to_ru', 'lms_request_assign.to_id', '=', 'to_ru.user_id')
                ->leftJoin('roles as to_r', 'to_ru.role_id', '=', 'to_r.id')                
                ->leftJoin('users as from_u', 'lms_request_assign.from_id', '=', 'from_u.user_id') 
                ->leftJoin('role_user as from_ru', 'lms_request_assign.from_id', '=', 'from_ru.user_id')
                ->leftJoin('roles as from_r', 'from_ru.role_id', '=', 'from_r.id')
                ->where('req_id', $reqId)
                ->where('is_owner', '1')                
                ->first();
        return $assignData ? $assignData : false;
    }     
}

