<?php

namespace App\Inv\Repositories\Models\Lms;

use DB;
use App\Inv\Repositories\Factory\Models\BaseModel;
use App\Inv\Repositories\Entities\User\Exceptions\BlankDataExceptions;
use App\Inv\Repositories\Entities\User\Exceptions\InvalidDataTypeExceptions;
use App\Inv\Repositories\Models\User;

class ApprovalRequest extends BaseModel {
    /* The database table used by the model.
     *
     * @var string
     */

    protected $table = 'lms_approval_request';

    /**
     * Custom primary key is set for the table
     *
     * @var integer
     */
    protected $primaryKey = 'req_id';

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
        'req_id',
        'ref_code',
        'req_type',
        'trans_id',
        'status',  
        'amount',
        'created_at',
        'created_by',
        'updated_at',
        'updated_by',
    ];

    /**
     * Save Approval Request Data
     * 
     * @param array $reqData
     * @param integer $reqId optional
     * 
     * @return mixed
     * @throws InvalidDataTypeExceptions
     */
    public static function saveApprRequestData($reqData=[], $reqId=null)
    {
        //Check $reqData is not an array
        if (!is_array($reqData)) {
            throw new InvalidDataTypeExceptions(trans('error_messages.invalid_data_type'));
        }        
        
        if (!is_null($reqId)) {
            return self::where('req_id', $reqId)->update($reqData);
        } else {
            if(isset($reqData[0])) {
                return self::insert($reqData);
            } else {
                return self::create($reqData);
            }
        }
    }
    
    public static function getApprRequestData($reqId)
    {
        $result = self::select('*')
                ->where('req_id', $reqId)
                ->first();
        
        return $result ? $result : null;
    }
    
    public static function getAllApprRequests()
    {
        $roleData = User::getBackendUser(\Auth::user()->user_id);
        $curUserId = \Auth::user()->user_id;
        $userArr = \Helpers::getChildUsersWithParent($curUserId);
        $query = self::from('lms_approval_request as req')
                ->select('req.req_id','req.ref_code','req.amount','req.status as req_status','req.req_type',
                'req.created_at',
                DB::raw("CONCAT_WS(' ', rta_assignee_u.f_name, rta_assignee_u.l_name) AS assignee"), 
                DB::raw("CONCAT_WS(' ', rta_from_u.f_name, rta_from_u.l_name) AS assigned_by"),                                 
                'req_assign.to_id',
                'req_assign.sharing_comment', 'assignee_r.name as assignee_role', 'from_r.name as from_role',
                'req_assign.req_assign_id'
                //DB::raw("CASE
                //            WHEN rta_req.req_type = 1 THEN 'Refund'
                //            WHEN rta_req.req_type = 2 THEN 'Adjustment'
                //            WHEN rta_req.req_type = 3 THEN 'Waveoff'
                //            ELSE ''
                //        END AS req_type_name"),
                //'reqlog.status'
                )                                
                /*
                ->join('lms_approval_request_log as reqlog', function ($join) use($roleData, $curUserId, $userArr) {
                        $join->on('reqlog.req_id', '=', 'req.req_id');
                        $join->on('reqlog.is_active', '=', DB::raw("1"));
                        if ($roleData[0]->is_superadmin != 1) {
                            //$join->on('req_assign.to_id', '=', DB::raw($curUserId));
                            $join->whereIn('reqlog.assigned_user_id', $userArr);

                        }                        
                })
                 * 
                 */
                ->join('lms_request_assign as req_assign', function ($join) use($roleData, $curUserId, $userArr) {
                    $join->on('req.req_id', '=', 'req_assign.req_id');
                    if ($roleData[0]->is_superadmin != 1) {
                        //$join->on('req_assign.to_id', '=', DB::raw($curUserId));
                        $join->whereIn('req_assign.to_id', $userArr);
                        
                    } else {
                        $join->on('req_assign.is_owner', '=', DB::raw("1"));
                        $join->whereNotNull('req_assign.to_id');
                    }
                })
                ->join('users as assignee_u', 'req_assign.to_id', '=', 'assignee_u.user_id')
                ->join('users as from_u', 'req_assign.from_id', '=', 'from_u.user_id')
                ->join('role_user as assignee_ru', 'req_assign.to_id', '=', 'assignee_ru.user_id')
                ->join('roles as assignee_r', 'assignee_ru.role_id', '=', 'assignee_r.id')
                ->leftJoin('role_user as from_ru', 'req_assign.from_id', '=', 'from_ru.user_id')
                ->leftJoin('roles as from_r', 'from_ru.role_id', '=', 'from_r.id');

        $query->groupBy('req.req_id');
        $result = $query->orderBy('req.req_id', 'DESC');
        return $result;
    }
}

