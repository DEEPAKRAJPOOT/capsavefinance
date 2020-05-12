<?php

namespace App\Inv\Repositories\Models;
use Illuminate\Database\Eloquent\Model;
use DB;
use Session;
use Auth;
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
        'created_by',
        'created_at'
    ];

 public static function getPromoterCibilData($biz_owner_id)
  {
     $arrData = self::select('biz_api_log.res_file')
        ->join('biz_api_log', 'biz_api_log.biz_api_log_id', '=', 'biz_api.biz_api_log_id')
        ->where('biz_api.biz_owner_id', $biz_owner_id)
        ->where('biz_api.type', '1')
        ->orderBy('biz_api_log.biz_api_log_id', 'DESC')
        ->first();
        return $arrData;
  }
  

 public static function getCommercialCibilData($biz_id)
  {
     $arrData = self::select('biz_api_log.res_file')
        ->join('biz_api_log', 'biz_api_log.biz_api_log_id', '=', 'biz_api.biz_api_log_id')
        ->where('biz_api.biz_owner_id', NULL)
        ->where('biz_api.biz_id', $biz_id)
        ->where('biz_api.type', '1')
        ->orderBy('biz_api_log.biz_api_log_id', 'DESC')
        ->first();
        return $arrData;
  }


    /* GET karaza api response   */
     public static function getKarzaRes($attribute)
     {
       
         return self::with('karza')->where(['biz_owner_id' => $attribute['ownerid'],'type' => $attribute['type'],'status' => 1])->first();
     }
 /* GET karaza api response mapping  */
   public  function karza()
   {
      return $this->belongsTo('App\Inv\Repositories\Models\BizApiLog', 'biz_api_log_id','biz_api_log_id');  
       
   }
   
    /**
     * Get Business Api Data
     * 
     * @param array $whereCond
     * @return type
     */
    public static function getBizApiData($whereCond=[])
    {
        $query = self::select('*');
        if (count($whereCond) > 0) {
            $query->where($whereCond);
        }
        $result = $query->get();
        return $result;
    }   
    
    /**
     * Save Biz Api Data
     * 
     * @param array $bizApiData
     * @return mixed
     */
    public static function saveBizApiData($bizApiData)
    {
        return self::create($bizApiData);
    }    
   
}




