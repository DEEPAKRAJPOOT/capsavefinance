<?php

namespace App\Inv\Repositories\Models;

use DB;
use App\Inv\Repositories\Factory\Models\BaseModel;
use App\Inv\Repositories\Models\AppProgramLimit;
use App\Inv\Repositories\Models\AppProgramOffer;
use App\Inv\Repositories\Models\BizInvoice;
use App\Inv\Repositories\Entities\User\Exceptions\BlankDataExceptions;
use App\Inv\Repositories\Entities\User\Exceptions\InvalidDataTypeExceptions;

class AppLimit extends BaseModel {
    /* The database table used by the model.
     *
     * @var string
     */

    protected $table = 'app_limit';

    /**
     * Custom primary key is set for the table
     *
     * @var integer
     */
    protected $primaryKey = 'app_limit_id';

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
        'app_id',
        'biz_id',
        'tot_limit_amt',
        'start_date',
        'end_date',
        'actual_end_date',
        'created_at',
        'created_by',
        'updated_at',        
        'updated_by',
    ];

    public static function saveAppLimit($data, $limit_id){
        if (!is_array($data)) {
            throw new InvalidDataTypeExceptions(trans('error_message.invalid_data_type'));
        }
        
        if (!is_null($limit_id)) {
            return self::where('app_limit_id', $limit_id)->update($data);
        } else {
            return self::create($data);
        }
    }

    public function app()
    {
        return $this->belongsTo('App\Inv\Repositories\Models\Application', 'app_id', 'app_id');
    }   
    
    
    public static function getUserLimit($user_id)
    {
       return  self::where(['user_id'=>$user_id,'status' => 1])->first();
    }
    
 
    public static function updateAppLimit($data, $whereCond=[]){
        if (!is_array($data)) {
            throw new InvalidDataTypeExceptions(trans('error_message.invalid_data_type'));
        }
        
        if (!is_array($whereCond)) {
            throw new InvalidDataTypeExceptions(trans('error_message.invalid_data_type'));
        }        
        
        if (count($whereCond) > 0) {
            return self::where($whereCond)->update($data);
        }
    }    
}
