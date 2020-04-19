<?php

namespace App\Inv\Repositories\Models\Lms;

use App\Inv\Repositories\Factory\Models\BaseModel;
use App\Inv\Repositories\Entities\User\Exceptions\BlankDataExceptions;
use App\Inv\Repositories\Entities\User\Exceptions\InvalidDataTypeExceptions;

class EodProcessLog extends BaseModel
{

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'lms_eod_process_log';

    /**
     * Custom primary key is set for the table
     *
     * @var integer
     */
    protected $primaryKey = 'eod_process_log_id';

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
        'eod_process_id',
        'tally_status',
        'int_accrual_status',
        'repayment_status',
        'disbursal_status',
        'charge_post_status',
        'overdue_int_accrual_status',
        'disbursal_block_status',
        'created_at',
        'created_by',
        'updated_at',
        'updated_by'        
    ];

    public static function saveEodProcessLog($data, $eodProcessId=null)
    {
        //Check $data is not an array
        if (!is_array($data)) {
            throw new InvalidDataTypeExceptions(trans('error_messages.invalid_data_type'));
        }        
        
        if (!is_null($eodProcessId)) {
            return self::where('eod_process_id', $eodProcessId)->update($data);
        } else {           
            return self::create($data);            
        }        
    }
    
    public static function getEodProcessLog($whereCond=[])
    {
        $query = self::select('*');
        if (count($whereCond) > 0) {
            $query->where($whereCond); 
        }
                
        $result = $query->first();        
        return $result ? $result : null;
    }
}

