<?php

namespace App\Inv\Repositories\Models\Lms;

use App\Inv\Repositories\Factory\Models\BaseModel;
use App\Inv\Repositories\Entities\User\Exceptions\BlankDataExceptions;
use App\Inv\Repositories\Entities\User\Exceptions\InvalidDataTypeExceptions;



class WriteOffRequest extends BaseModel
{

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'lms_wo_req';

    /**
     * Custom primary key is set for the table
     *
     * @var integer
     */
    protected $primaryKey = 'wo_req_id';

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
        'user_id',
        'wo_status_log_id',
        'amount',
        'created_at',
        'created_by',
        'updated_at',
        'updated_by'
    ];

    /**
     * Save write off request
     * 
     * @param array $data
     * @return type
     * @throws InvalidDataTypeExceptions
     */
    public static function saveWriteOffReq($data) {
        if (!is_array($data)) {
            throw new InvalidDataTypeExceptions(trans('error_message.invalid_data_type'));
        }     
        $arr = self::create($data);
        return ($arr ? $arr : null);
    }
    
    public static function updateWriteOffReqById($woReqId, $data)
    {
        $rowUpdate = self::where('wo_req_id', $woReqId)->update($data);
        return ($rowUpdate ? $rowUpdate : false);        
    }
    
    /**
     * Get write off by user id
     * 
     * @param integer $userId
     * @return array
     * @throws InvalidDataTypeExceptions
     */
    public static function getWriteOff($userId) {
        /**
         * Check id is not an integer
         */
        if (!is_int($userId)) {
            throw new InvalidDataTypeExceptions(trans('error_message.invalid_data_type'));
        }
        
        $result = self::SELECT('lms_wo_req.*', 'lms_wo_status_log.status_id', 'mst_status.status_name')
                ->leftJoin('lms_wo_status_log', 'lms_wo_status_log.wo_status_log_id', '=', 'lms_wo_req.wo_status_log_id')
                ->leftJoin('mst_status', 'mst_status.id', '=', 'lms_wo_status_log.status_id')
                ->where('user_id', (int) $userId)
                ->get();
        return ($result ? $result : []);
    }
   
}
