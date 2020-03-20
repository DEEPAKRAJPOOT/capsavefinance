<?php

namespace App\Inv\Repositories\Models\Lms;

use DB;
use App\Inv\Repositories\Factory\Models\BaseModel;
use App\Inv\Repositories\Entities\User\Exceptions\BlankDataExceptions;
use App\Inv\Repositories\Entities\User\Exceptions\InvalidDataTypeExceptions;

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
}

