<?php

namespace App\Inv\Repositories\Models;

use Carbon\Carbon;
use DateTime;
use App\Inv\Repositories\Factory\Models\BaseModel;

class BizPanGst extends BaseModel
{
    /* The database table used by the model.
     *
     * @var string
     */
    protected $table = 'biz_pan_gst';

    /**
     * Custom primary key is set for the table
     *
     * @var integer
     */
    protected $primaryKey = 'biz_pan_gst_id';
    public $userstamps = false;
    public $timestamps = false;

    
    protected $fillable = [
        'user_id',
        'biz_id',
        'biz_owner_id',
        'type',
        'cin',
        'pan_gst_hash',
        'status',
        'parent_pan_gst_id',
        'biz_pan_gst_api_id',
        'biz_addr_id',
        'is_gst_hide',
        'created_by',
        'created_at',
        'updated_at',
        'updated_by'
    ];

    public static function getGSTsByUserId($user_id){
        return BizPanGst::where(['user_id'=> $user_id, 'type'=> 2, 'status'=> 1])->where('parent_pan_gst_id', '<>', 0)->get();
    }

    public static function updateGstHideAddress($data, $biz_pan_gst_id){
        BizPanGst::where('biz_addr_id', $data['biz_addr_id'])->update(['is_gst_hide'=>0, 'biz_addr_id'=>0]);
        return BizPanGst::where('biz_pan_gst_id', $biz_pan_gst_id)->update($data);
    }
   
}




