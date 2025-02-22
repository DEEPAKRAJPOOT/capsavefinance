<?php

namespace App\Inv\Repositories\Models\Master;

use App\Inv\Repositories\Factory\Models\BaseModel;
use App\Inv\Repositories\Entities\User\Exceptions\BlankDataExceptions;
use App\Inv\Repositories\Entities\User\Exceptions\InvalidDataTypeExceptions;
use App\Inv\Repositories\Models\User;
use Auth;

class ChargeGST extends BaseModel {

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'mst_chrg_gst';

    /**
     * Custom primary key is set for the table
     *
     * @var integer
     */
    protected $primaryKey = 'chrg_gst_id';

    /**
     * Maintain created_at and updated_at automatically
     *
     * @var boolean
     */
    public $timestamps = false;

    /**
     * Maintain created_by and updated_by automatically
     *
     * @var boolean
     */
    public $userstamps = false;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'chrg_id',
        'gst_val',
        'created_at',
        'created_by'
    ];

    public function userDetail() {
        return $this->belongsTo(User::class, 'created_by');
    }

    public static function saveChargesGST($attributes) {
        
        if (empty($attributes)) {
            throw new BlankDataExceptions(trans('error_message.no_data_found'));
        }
        if (!is_array($attributes)) {
            throw new InvalidDataTypeExceptions(trans('error_message.send_array'));
        }
        
        $res = self::create($attributes)->chrg_gst_id;
        return $res ?: false;
        
    }
    
    public static function getLastChargesGSTById($id){
        
        if (empty($id)) {
            throw new BlankDataExceptions(trans('error_message.no_data_found'));
        }
        
        $result = self::where('chrg_id',$id)
                ->orderBy('chrg_gst_id','desc')
                ->first();
        
        return $result ?: false;
        
    }

}
