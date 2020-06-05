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
        
        if (isset($whereCond['sys_start_date_eq'])) {
            $query->where(\DB::raw('DATE(sys_start_date)'), '=', $whereCond['sys_start_date_eq']);
            unset($whereCond['sys_start_date_eq']);
        }

        if (isset($whereCond['sys_start_date_tz_eq'])) {
            $current_date = \Helpers::convertDateTimeFormat($whereCond['sys_start_date_tz_eq'], $fromDateFormat='Y-m-d', $toDateFormat='Y-m-d');
            $query->where(\DB::raw("DATE(CONVERT_TZ(sys_start_date, '+00:00', '+5:30'))"), '=', $current_date);
            unset($whereCond['sys_start_date_tz_eq']);
        }
        
        if (isset($whereCond['sys_start_date_lte'])) {
            $query->where('sys_start_date', '<=', $whereCond['sys_start_date_lte']);
            unset($whereCond['sys_start_date_lte']);
        }

        if (isset($whereCond['sys_start_date_gte'])) {
            $query->where('sys_start_date', '>=', $whereCond['sys_start_date_gte']);
            unset($whereCond['sys_start_date_gte']);
        }
        
        if (isset($whereCond['eod_process_start_date_eq'])) {
            $query->where(\DB::raw('DATE(eod_process_start)'), '=', $whereCond['eod_process_start_date_eq']);
            unset($whereCond['eod_process_start_date_eq']);
        }
        
        if (isset($whereCond['eod_process_start_date_tz_eq'])) {
            $current_date = \Helpers::convertDateTimeFormat($whereCond['eod_process_start_date_tz_eq'], $fromDateFormat='Y-m-d', $toDateFormat='Y-m-d');
            $query->where(\DB::raw("DATE(CONVERT_TZ(eod_process_start, '+00:00', '+5:30'))"), '=', $current_date);
            unset($whereCond['eod_process_start_date_tz_eq']);
        }        
        
        if (isset($whereCond['eod_process_start_date_lte'])) {
            $query->where('eod_process_start', '<=', $whereCond['eod_process_start_date_lte']);
            unset($whereCond['eod_process_start_date_lte']);
        }  
        
        if (isset($whereCond['eod_process_start_date_gte'])) {
            $query->where('eod_process_start', '>=', $whereCond['eod_process_start_date_gte']);
            unset($whereCond['eod_process_start_date_gte']);
        }              

        if (isset($whereCond['status'])) {
            if (is_array($whereCond['status'])){
                $query->whereIn('status', $whereCond['status']);
            } else {
                $query->where('status', $whereCond['status']);
            }
            unset($whereCond['status']);
        }
                                
        unset($whereCond['is_active']);
        
        $query->where($whereCond);
        $query->where('is_active', 1);        
        
        $result = $query->first();
        
        return $result ? $result : null;
    }
    
    public static function updateEodProcess($data, $whereCond)
    {
        //Check $data is not an array
        if (!is_array($data)) {
            throw new InvalidDataTypeExceptions(trans('error_messages.invalid_data_type'));
        }        
        
        //Check $whereCond is not an array
        if (!is_array($whereCond)) {
            throw new InvalidDataTypeExceptions(trans('error_messages.invalid_data_type'));
        }  
                
        return self::where($whereCond)->update($data);            
    } 
    
    /**
     * Get System Start Date
     * 
     * @return timestamp
     */
    public static function getSysStartDate()
    {
        $result = self::select('sys_start_date')->where('is_active', 1)->first();
        return $result ? $result->sys_start_date : null;
    }
    
    /**
     * Get Latest Eod Process
     * 
     * @return mixed
     */
    public static function getLatestEodProcess($whereCond=[])
    {
        $query = self::select('*');
        if (count($whereCond)) {
            $query->where($whereCond);
        }
        $query->orderBy('eod_process_id', 'DESC');
        $result = $query->first();
        return $result ? $result : null;
    }
    
    public static function getEodDataCount()
    {
        return self::count('*');
    }
}

