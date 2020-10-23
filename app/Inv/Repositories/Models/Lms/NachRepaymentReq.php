<?php
namespace App\Inv\Repositories\Models\Lms;

use DB;
use App\Inv\Repositories\Factory\Models\BaseModel;
use App\Inv\Repositories\Entities\User\Exceptions\BlankDataExceptions;
use App\Inv\Repositories\Entities\User\Exceptions\InvalidDataTypeExceptions;
use App\Inv\Repositories\Models\User;

class NachRepaymentReq extends BaseModel {
    /* The database table used by the model.
     *
     * @var string
     */

    protected $table = 'lms_nach_repayment_req';

    /**
     * Custom primary key is set for the table
     *
     * @var integer
     */
    protected $primaryKey = 'nach_repayment_req_id';
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
        'users_nach_id',  
        'req_batch_id',  
        'user_id',  
        'ref_no',
        'req_date',
        'amount',  
        'status',
        'comment',
        'created_at',  
        'created_by',  
        'updated_at',  
        'updated_by'
    ];

    public function user(){
        return $this->belongsTo('App\Inv\Repositories\Models\Payment','payment_id','payment_id');
    }
    
    public function  lms_user()
    {
        return $this->belongsTo('App\Inv\Repositories\Models\LmsUser','user_id','user_id');
    }
    
    
    public function  user_nach()
    {
        return $this->belongsTo('App\Inv\Repositories\Models\UserNach','users_nach_id','users_nach_id');
    }
    
    /**
     * Update repayment request
     * 
     * @param arr $attributes
     * @param arr $whereCond
     * @return type
     * @throws InvalidDataTypeExceptions
     * @throws BlankDataExceptions
     */
    public static function updateRepaymentReq($attributes, $whereCond) {
        
        if (!is_array($attributes)) {
            throw new InvalidDataTypeExceptions(trans('error_message.send_array'));
        }
        
        if (!is_array($whereCond)) {
            throw new InvalidDataTypeExceptions(trans('error_message.send_array'));
        }
        
        if (empty($attributes)) {
            throw new BlankDataExceptions(trans('error_message.no_data_found'));
        }
        $result = self::where($whereCond)->update($attributes);
        return $result ? $result : false;
    
    }
}   

