<?php

namespace App\Inv\Repositories\Models\Lms;

use App\Inv\Repositories\Factory\Models\BaseModel;
use App\Inv\Repositories\Entities\User\Exceptions\BlankDataExceptions;
use App\Inv\Repositories\Entities\User\Exceptions\InvalidDataTypeExceptions;



class WriteOffStatusLog extends BaseModel
{

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'lms_wo_status_log';

    /**
     * Custom primary key is set for the table
     *
     * @var integer
     */
    protected $primaryKey = 'wo_status_log_id';

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
        'wo_req_id',
        'status_id',
        'comment_txt',
        'created_at',
        'created_by',
        'updated_at',
        'updated_by'
    ];

    /**
     * Save write off request log
     * 
     * @param array $data
     * @return type
     * @throws InvalidDataTypeExceptions
     */
    public static function saveWriteOffReqLog($data) {
        if (!is_array($data)) {
            throw new InvalidDataTypeExceptions(trans('error_message.invalid_data_type'));
        }     
        $arr = self::create($data);
        return ($arr ? $arr : null);
    } 
   
}
