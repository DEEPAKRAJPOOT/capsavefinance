<?php

namespace App\Inv\Repositories\Models\Lms;

use DB;
use App\Inv\Repositories\Factory\Models\BaseModel;
use App\Inv\Repositories\Entities\User\Exceptions\BlankDataExceptions;
use App\Inv\Repositories\Entities\User\Exceptions\InvalidDataTypeExceptions;

class ApprovalRequestLog extends BaseModel {
    /* The database table used by the model.
     *
     * @var string
     */

    protected $table = 'lms_approval_request_log';

    /**
     * Custom primary key is set for the table
     *
     * @var integer
     */
    protected $primaryKey = 'request_log_id';

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
        'assigned_user_id',
        'status',
        'wf_stage_id',
        'comment',
        'is_active',
        'created_at',
        'created_by',
        'updated_at',
        'updated_by',
    ];

    /**
     * Save Approval Request Log Data
     * 
     * @param array $reqLogData
     * 
     * @return mixed
     * @throws InvalidDataTypeExceptions
     */
    public static function saveApprRequestLogData($reqLogData)
    {
        //Check $reqLogData is not an array
        if (!is_array($reqLogData)) {
            throw new InvalidDataTypeExceptions(trans('error_messages.invalid_data_type'));
        }
        
        if (isset($reqLogData[0])) {
            return self::insert($reqLogData);
        } else {
            return self::create($reqLogData);
        }
    }
    
    /**
     * Update Approval Request Log Data
     * 
     * @param array $whereCond
     * @param array $reqLogData
     * 
     * @return mixed
     * @throws InvalidDataTypeExceptions
     */
    public static function updateApprRequestLogData($whereCond, $reqLogData)
    {
        //Check $reqLogData is not an array
        if (!is_array($reqLogData)) {
            throw new InvalidDataTypeExceptions(trans('error_messages.invalid_data_type'));
        }
        
        if (!is_array($whereCond)) {
            throw new InvalidDataTypeExceptions(trans('error_messages.invalid_data_type'));
        }        
        
        return self::where($whereCond)->update($reqLogData);        
    }
    
    /**
     * Get Approval Request Log Data
     * 
     * @param array $whereCond
     * 
     * @return mixed
     * @throws InvalidDataTypeExceptions
     */
    public static function getApprRequestLogData($whereCond)
    {
        if (!is_array($whereCond)) {
            throw new InvalidDataTypeExceptions(trans('error_messages.invalid_data_type'));
        }
        
        $result = self::select('*')
                ->where($whereCond)
                ->where('is_active', 1)
                ->orderBy('request_log_id', 'DESC')
                ->get();
        
        return $result ? $result : [];                
    }    
    
}