<?php

namespace App\Inv\Repositories\Models\Lms;

use App\Inv\Repositories\Factory\Models\BaseModel;
use App\Inv\Repositories\Entities\User\Exceptions\BlankDataExceptions;
use App\Inv\Repositories\Entities\User\Exceptions\InvalidDataTypeExceptions;

class EodProcess extends BaseModel
{

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'lms_eod_process';

    /**
     * Custom primary key is set for the table
     *
     * @var integer
     */
    protected $primaryKey = 'eod_process_id';

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
        'sys_start_date',
        'sys_end_date',
        'eod_process_start',
        'eod_process_end',
        'total_hours',
        'status',
        'is_active',
        'created_at',
        'created_by',
        'updated_at',
        'updated_by'        
    ];

    public static function saveEodProcess($data, $eodProcessId=null)
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
    
    public static function getEodProcess($whereCond=[])
    {
        
        $query = self::select('*');
        if (isset($whereCond['sys_start_date_gte'])) {
            $query->where('sys_start_date', '>=', $whereCond['sys_start_date_gte']); 
        }
        
        if (isset($whereCond['eod_process_start_gte'])) {
            $query->where('eod_process_start', '>=', $whereCond['eod_process_start']); 
        }

        if (isset($whereCond['status'])) {
            $query->where('status', $whereCond['status']);
        }
        
        $query->where('is_active', 1);
        $result = $query->first();
        
        return $result ? $result : null;
    }
}

