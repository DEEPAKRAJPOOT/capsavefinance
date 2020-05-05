<?php

namespace App\Inv\Repositories\Models;
use Illuminate\Database\Eloquent\Model;
use DB;
use Session;
use Auth;
use Carbon\Carbon;
use DateTime;
use App\Inv\Repositories\Factory\Models\BaseModel;

class BizGstLog extends BaseModel
{
    /* The database table used by the model.
     *
     * @var string
     */
    protected $table = 'biz_gst_log';

    /**
     * Custom primary key is set for the table
     *
     * @var integer
     */
    protected $primaryKey = 'id';
    
    public $userstamps = false;
    
    public $timestamps = false;

    
    protected $fillable = [
        'app_id',
        'gstin',
        'request_id',
        'req_file',
        'res_file',
        'url',
        'status',
        'created_at',
        'updated_at'
    ];
   
    /**
     * Get Biz Gst Log Data
     * 
     * @param array $whereCond
     * @return mixed
     */
    public static function getBizGstLogData($whereCond=[])
    {
        $query = self::select('*');
        if (count($whereCond) > 0) {
            $query->where($whereCond);
        }
        $result = $query->get();
        return $result;
    }   
    
    /**
     * Save Biz Gst Log Data
     * 
     * @param array $bizGstLogData
     * @return mixed
     */
    public static function saveBizGstLogData($bizGstLogData)
    {
        return self::create($bizGstLogData);
    }    
   
}




