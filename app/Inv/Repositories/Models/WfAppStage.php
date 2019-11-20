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
    public $userstamps = false;

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
        'app_wf_status',        
        'is_complete'        
     ];
    
    /**
     * Get Wf stage Details 
     *
    */
    public static function updateWfStage($wf_stage_id, $arrData = [])
    {
       $rowUpdate = self::where('wf_stage_id', $wf_stage_id)->update($arrData);

        return ($rowUpdate ? $rowUpdate : false);
    }
    
    /**
     * Get workflow detail by wf stage code
     * 
     * @param string $wf_stage_code
     * @return mixed
     * @throws BlankDataExceptions
     */
    public static function saveWfDetail($arrData)
    {
        $arr = self::create($arrData);
        return ($arr ? $arr : null);
    }
    
    /**
     * get Current WfStage by app id 
     * @param type $app_id
     * @return type
     */
    
    protected static function getCurrentWfStage($app_id) 
    {
        $appData = self::select('wf_stage.stage_code')
                ->join('wf_stage', 'app_wf.wf_stage_id', '=', 'wf_stage.wf_stage_id')                 
                ->where('app_wf.biz_app_id', $app_id)
                ->orderBy('app_wf.wf_stage_id', 'DESC')
                ->limit(1)
                ->first();
        return $appData ? $appData : null;
    }

    /**
     * Get application workflow stage by code
     * 
     * @param string $wf_stage_code
     * @return mixed
     */    
    protected static function getAppWfStage($wf_stage_code) 
    {
        $appData = self::select('app_wf.wf_stage_id')
                ->join('wf_stage', 'app_wf.wf_stage_id', '=', 'wf_stage.wf_stage_id')                 
                ->where('wf_stage.stage_code', $wf_stage_code)                
                ->first();
        return $appData ? $appData : false;
    }
  
}
  

