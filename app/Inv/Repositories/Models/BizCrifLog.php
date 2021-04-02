<?php

namespace App\Inv\Repositories\Models;

use Carbon\Carbon;
use DateTime;
use App\Inv\Repositories\Factory\Models\BaseModel;

class BizCrifLog extends BaseModel
{
    /* The database table used by the model.
     *
     * @var string
     */
    protected $table = 'biz_crif_log';

    /**
     * Custom primary key is set for the table
     *
     * @var integer
     */
    protected $primaryKey = 'biz_crif_log_id';
    public $userstamps = false;
    public $timestamps = false;

    
    protected $fillable = [
        'biz_crif_id',
        'api_name',
        'report_id',
        'inquiry_ref',
        'req_file',
        'res_file',
        'url',
        'status',
        'created_by',
        'created_at',
        'updated_at',
        'updated_by'
    ];
   
    /**
     * Get Biz Perfios Data
     * 
     * @param array $whereCond
     * @return mixed
     */
    public static function getBizCrifLogData($whereCond=[]) {
        $query = self::select('*');
        if (count($whereCond) > 0) {
            $query->where($whereCond);
        }
        $result = $query->get();
        return $result;
    }   
    
    /**
     * Save Biz Perfios Data
     * 
     * @param array $bizPerfiosData
     * @return mixed
     */
    public static function saveBizCrifLogData($bizCrifLogData) {
        return self::create($bizCrifLogData);
    }

    public static function updateBizCrifLog($data, $where){
      $is_updated = self::where($where)->update($data);
      return $is_updated;
    }     
}




