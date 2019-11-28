<?php

namespace App\Inv\Repositories\Models;

use App\Inv\Repositories\Factory\Models\BaseModel;
use App\Inv\Repositories\Entities\User\Exceptions\InvalidDataTypeExceptions;
use App\Inv\Repositories\Entities\User\Exceptions\BlankDataExceptions;

class WfAppStage extends BaseModel
{

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'app_wf';

    /**
     * Custom primary key is set for the table
     *
     * @var integer
     */
    protected $primaryKey = 'app_wf_id';
    
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
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'wf_stage_id',
        'biz_app_id',
        'user_id',
        'app_wf_status',        
        'is_complete'        
     ];
    
    /**
     * Get Wf stage Details 
     *
    */
    public static function updateWfStage($wf_stage_id, $app_id, $arrData = [])
    {
       $rowUpdate = self::where('wf_stage_id', $wf_stage_id)->where('biz_app_id', $app_id)->update($arrData);

        return ($rowUpdate ? $rowUpdate : false);
    }
    
        
    /**
     * Get Wf stage Details 
     *
     */
    public static function updateWfStageByUserId($wf_stage_id, $user_id, $arrData = [])
    {
        $rowUpdate = self::where('wf_stage_id', $wf_stage_id)->where('user_id', $user_id)->where('biz_app_id', '0')->update($arrData);

        return ($rowUpdate ? $rowUpdate : false);
    }    
    
    /**
     * Save application workflow stage
     * 
     * @param array $arrData
     * @return mixed
     * @throws BlankDataExceptions
     */
    public static function saveWfDetail($arrData)
    {
        $arr = self::create($arrData);
        return ($arr ? $arr : null);
    }
    
    /**
     * Get Current WfStage by app id
     * 
     * @param type $app_id
     * @return type
     */    
    protected static function getCurrentWfStage($app_id) 
    {
        $appData = self::select('wf_stage.stage_code','wf_stage.assign_role','wf_stage.order_no')
                ->join('wf_stage', 'app_wf.wf_stage_id', '=', 'wf_stage.wf_stage_id')                 
                ->where('app_wf.biz_app_id', $app_id)
                ->where('app_wf.app_wf_status', 1)
                ->orderBy('app_wf.wf_stage_id', 'DESC')
                ->limit(1)
                ->first();
        return $appData ? $appData : null;
    }

    /**
     * Get application workflow stage by code
     * 
     * @param string $wf_stage_code
     * @param integer $user_id
     * @param integer $app_id
     * @param integer $prgm_id
     * @return mixed
     */
    protected static function getAppWfStage($wf_stage_code, $user_id, $app_id=0, $prgm_id=1) 
    {
        $appData = self::select('app_wf.wf_stage_id', 'wf_stage.order_no', 'wf_stage.route_name')
                ->join('wf_stage', 'app_wf.wf_stage_id', '=', 'wf_stage.wf_stage_id')
                ->where('wf_stage.stage_code', $wf_stage_code)                
                ->where('wf_stage.prgm_id', $prgm_id)
                ->where('app_wf.user_id', $user_id)
                ->where('app_wf.biz_app_id', $app_id)
                ->first();
        return $appData ? $appData : false;
    }
    
    
}
  
