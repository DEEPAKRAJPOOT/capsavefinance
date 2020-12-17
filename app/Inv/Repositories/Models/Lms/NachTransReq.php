<?php
namespace App\Inv\Repositories\Models\Lms;

use DB;
use App\Inv\Repositories\Factory\Models\BaseModel;
use App\Inv\Repositories\Entities\User\Exceptions\BlankDataExceptions;
use App\Inv\Repositories\Entities\User\Exceptions\InvalidDataTypeExceptions;

class NachTransReq extends BaseModel {
    /* The database table used by the model.
     *
     * @var string
     */

    protected $table = 'lms_nach_trans_req';

    /**
     * Custom primary key is set for the table
     *
     * @var integer
     */
    protected $primaryKey = 'nach_trans_req_id';
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
        'nach_repayment_req_id',  
        'trans_id',  
        'amt',  
        'status',
        'created_at',  
        'created_by',  
        'updated_at',  
        'updated_by'
    ];

    public function transaction(){
        return $this->belongsTo('App\Inv\Repositories\Models\Lms\Transactions','trans_id','trans_id');
    }

    public static function updateNachTransReq($attributes, $whereCond) {
        
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

