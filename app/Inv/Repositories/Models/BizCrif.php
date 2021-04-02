<?php

namespace App\Inv\Repositories\Models;

use Carbon\Carbon;
use DateTime;
use App\Inv\Repositories\Factory\Models\BaseModel;

class BizCrif extends BaseModel
{
    /* The database table used by the model.
     *
     * @var string
     */
    protected $table = 'biz_crif';

    /**
     * Custom primary key is set for the table
     *
     * @var integer
     */
    protected $primaryKey = 'biz_crif_id';
    public $userstamps = false;
    public $timestamps = false;

    
    protected $fillable = [
        'app_id',
        'biz_id',
        'biz_owner_ids',
        'api_name',
        'unique_ref',
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
    public static function getBizCrifData($whereCond=[]) {
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
    public static function saveBizCrifData($bizCrifData) {
        return self::create($bizCrifData);
    }

    public static function updateBizCrif($data, $where){
      $is_updated = self::where($where)->update($data);
      return $is_updated;
    }

    public static function getLastCrifRequest($biz_id, array $where = []){
       $result = self::with(['getBizCrifLog'=> function ($query) use ($where) {
            $query->where($where);
       }])->where(['biz_id' => $biz_id])->orderBy('biz_crif_id', 'desc')->first();
       return ($result ?? null);
    } 

    public  function getBizCrifLog(){
       return $this->hasMany('App\Inv\Repositories\Models\BizCrifLog', 'biz_crif_id', 'biz_crif_id');
    }

    public static function getCrifDataByBizId($bizId){
        return self::where('biz_id','=',$bizId)->orderBy('biz_crif_id', 'desc')->first();
    }
}




