<?php

namespace App\Inv\Repositories\Models;

use Carbon\Carbon;
use DateTime;
use App\Inv\Repositories\Factory\Models\BaseModel;

class BizApi extends BaseModel
{
    /* The database table used by the model.
     *
     * @var string
     */
    protected $table = 'biz_api';

    /**
     * Custom primary key is set for the table
     *
     * @var integer
     */
    protected $primaryKey = 'biz_api_id';
    public $userstamps = false;
    public $timestamps = false;

    
    protected $fillable = [
        'user_id',
        'biz_id',
        'biz_owner_id',
        'type',
        'verify_doc_no',
        'biz_api_log_id',
        'status',
        'created_by'
    ];

 public static function getPromoterCibilData($biz_owner_id)
  {
     $arrData = self::select('biz_api_log.res_file')
        ->join('biz_api_log', 'biz_api_log.biz_api_log_id', '=', 'biz_api.biz_api_log_id')
        ->where('biz_api.biz_owner_id', $biz_owner_id)
        ->orderBy('biz_api_log.biz_api_log_id', 'DESC')
        ->first();
        return $arrData;
  }
   
}




