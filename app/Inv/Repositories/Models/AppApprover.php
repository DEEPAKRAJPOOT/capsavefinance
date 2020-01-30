<?php

namespace App\Inv\Repositories\Models;

use DB;
use App\Inv\Repositories\Factory\Models\BaseModel;
use App\Inv\Repositories\Entities\User\Exceptions\InvalidDataTypeExceptions;
use App\Inv\Repositories\Entities\User\Exceptions\BlankDataExceptions;

class AppApprover extends BaseModel
{
    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'app_approval_status';

    /**
     * Custom primary key is set for the table
     *
     * @var integer
     */
    protected $primaryKey = 'app_appr_status_id';

    /**
     * The attributes that should be mutated to dates.
     *
     * @var array
     */
    //protected $dates = ['deleted_at'];

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
        'app_id',
        'approver_user_id',
        'status',  
        'is_active',
        'created_by',
        'created_at',
        'updated_at',
        'updated_by'
    ];

    
    /**
     * Get all Application Approvers
     * 
     * @param integer $app_id
     * @return array
     */
    public static function getAppApprovers($app_id){
        $apprUsers = self::select('*')
            ->where('app_id', '=', $app_id)
            ->where('is_active', '=', 1)
            ->get();
        return ($apprUsers ? $apprUsers : []);
    }
    
    
    /**
     * Get all Application Approvers
     * 
     * @param array $data
     * @return array
     */
    public static function saveAppApprovers($data)
    {
        $apprUsers = self::where('app_id', '=', $data['app_id'])
            ->where('approver_user_id', '=', $data['approver_user_id'])
            ->where('is_active', '=', 1)
            ->update(['status' => $data['status']]);
        return ($apprUsers ? $apprUsers : []);
    }    
    
    
    /**
     * Get all Application Approvers
     * 
     * @param array $data
     * @return array
     */
    public static function updateAppApprActiveFlag($app_id)
    {
        $apprUsers = self::where('app_id', '=', $app_id)            
            ->update(['is_active' => 0]);
        return $apprUsers;
    } 
    
    /**
     * Get Approvers Details of Application
     * @param int $app_id
     * @return type
     */
    public static function getAppApproversDetails($app_id){
        if (empty($app_id)) {
            throw new BlankDataExceptions(trans('error_message.no_data_found'));
        }

        if (!is_int($app_id)) {
            throw new InvalidDataTypeExceptions(trans('error_message.invalid_data_type'));
        }
       
        $appApprovers =  self::select(DB::raw("CONCAT_WS(' ',rta_u.f_name,rta_u.l_name) AS approver"), 
        'u.email as approver_email', 'r.name as approver_role', 'app_approval_status.status', 
        'app_approval_status.updated_at')
        ->join('users as u', 'app_approval_status.approver_user_id', '=', 'u.user_id')
        ->join('role_user as ru', 'ru.user_id', '=', 'u.user_id')
        ->join('roles as r', 'r.id', '=','ru.role_id')
        ->where('app_approval_status.app_id', (int) $app_id)
        ->get();

        return $appApprovers;
    }
}