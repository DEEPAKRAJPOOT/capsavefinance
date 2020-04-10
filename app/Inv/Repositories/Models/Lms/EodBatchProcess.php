<?php

namespace App\Inv\Repositories\Models\Lms;

use App\Inv\Repositories\Factory\Models\BaseModel;
use App\Inv\Repositories\Entities\User\Exceptions\BlankDataExceptions;
use App\Inv\Repositories\Entities\User\Exceptions\InvalidDataTypeExceptions;

class EodBatchProcess extends BaseModel
{

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'lms_eod_batch_process';

    /**
     * Custom primary key is set for the table
     *
     * @var integer
     */
    protected $primaryKey = 'eod_batch_process_id';

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
        'end_datetime',
        'status',
        'created_at',
        'created_by',
        'updated_at',
        'updated_by'        
    ];

    public static function saveEodBatchProcess($data, $eodBatchProcessId=null)
    {
        //Check $data is not an array
        if (!is_array($data)) {
            throw new InvalidDataTypeExceptions(trans('error_messages.invalid_data_type'));
        }        
        
        if (!is_null($eodBatchProcessId)) {
            return self::where('eod_batch_process_id', $eodBatchProcessId)->update($data);
        } else {           
            return self::create($data);            
        }        
    }
    
    public static function getEodBatchProcess($whereCond=[])
    {
        $query = self::select('*');
        if (isset($whereCond['end_datetime'])) {
            $query->where(\DB::raw('DATE(end_datetime)'), $whereCond['end_datetime']); 
        }
                
        $result = $query->first();        
        return $result ? $result : null;
    }
}

