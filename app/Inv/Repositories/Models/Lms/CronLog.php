<?php
namespace App\Inv\Repositories\Models\Lms;

use DB;
use Helpers;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use App\Inv\Repositories\Factory\Models\BaseModel;
use App\Inv\Repositories\Entities\User\Exceptions\BlankDataExceptions;
use App\Inv\Repositories\Entities\User\Exceptions\InvalidDataTypeExceptions;

class CronLog extends BaseModel {
    /* The database table used by the model.
     *
     * @var string
     */

    private static $balacnce = 0;
    protected $table = 'lms_cron_log';

    /**
     * Custom primary key is set for the table
     *
     * @var integer
     */
    protected $primaryKey = 'cron_log_id';

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
        'cron_id',  
        'status',  
        'exec_start_at',
        'exec_end_at',  
        'created_at',  
        'created_by',  
        'updated_at',  
        'updated_by'
    ];

    public function payment(){
        return $this->belongsTo('App\Inv\Repositories\Models\Payment','payment_id','payment_id');
    }
    
    public static function createCronLog($data)
    {
        if (!is_array($data)) {
            throw new InvalidDataTypeExceptions(trans('error_message.invalid_data_type'));
        }
        
        if(!isset($data['cron_id'])){
            throw new BlankDataExceptions('Cron Log: Cron id missing');
        }

        if(!isset($data['exec_start_at'])){
            throw new BlankDataExceptions('Cron Log: Excution start timestamp missing');
        }

        return self::create($data);
    }

    public static function updateCronLog($data,$cronLogId)
    {
        if (!is_array($data)) {
            throw new InvalidDataTypeExceptions(trans('error_message.invalid_data_type'));
        }

        if(!isset($data['status'])){
            throw new BlankDataExceptions('Cron Log: Status missing');
        }

        if(!isset($data['exec_end_at'])){
            throw new BlankDataExceptions('Cron Log: Excution end timestamp missing');
        }

        if ($cronLogId) {
            return self::where('cron_log_id',$cronLogId)->update($data);
        }
    }
}
