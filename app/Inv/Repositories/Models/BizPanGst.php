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
    public $userstamps = true;
    public $timestamps = true;

    
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
        return BizPanGst::where(['user_id'=> $user_id, 'type'=> 2, 'status'=> 1])->where('parent_pan_gst_id', '<>', 0)->groupBy(['is_gst_hide', 'pan_gst_hash'])->get(['is_gst_hide', 'pan_gst_hash']);
    }

    public static function updateGstHideAddress($data, $biz_addr_id){
        BizPanGst::where(['user_id'=> $data['user_id'],'biz_addr_id'=> $biz_addr_id])->update(['is_gst_hide'=>0, 'biz_addr_id'=>0]);

        return BizPanGst::where(['user_id'=> $data['user_id'], 'pan_gst_hash'=>$data['pan_gst_hash'], 'type'=>2])->where('parent_pan_gst_id','<>',0)->update(['is_gst_hide'=>1, 'biz_addr_id'=>$biz_addr_id]);
        //return BizPanGst::where('biz_pan_gst_id', $biz_pan_gst_id)->update($data);
    }

    //GST's which are associated with application
    public static function getAppGSTsByUserId($user_id){
        return BizPanGst::where(['user_id'=> $user_id, 'type'=> 2, 'status'=> 1])->where('parent_pan_gst_id', '=', 0)->pluck('pan_gst_hash', 'biz_id')->toArray();
    }
   
    /**
     * Get Biz Pan Gst Data
     * 
     * @param array $whereCond
     * @return mixed
     */
    public static function getBizPanGstData($whereCond=[])
    {
        $query = self::select('*');
        if (count($whereCond) > 0) {
            $query->where($whereCond);
        }
        $result = $query->get();
        return $result;
    }   
    
    /**
     * Save Biz Pan Gst Data
     * 
     * @param array $bizPanGstData
     * @return mixed
     */
    public static function saveBizPanGstData($bizPanGstData)
    {
        return self::create($bizPanGstData);
    }    
}




