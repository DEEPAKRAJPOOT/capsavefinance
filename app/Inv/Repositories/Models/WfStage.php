<?php

namespace App\Inv\Repositories\Models;

use App\Inv\Repositories\Factory\Models\BaseModel;
use App\Inv\Repositories\Entities\User\Exceptions\InvalidDataTypeExceptions;
use App\Inv\Repositories\Entities\User\Exceptions\BlankDataExceptions;

class WfStage extends BaseModel
{

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'wf_stage';

    /**
     * Custom primary key is set for the table
     *
     * @var integer
     */
    protected $primaryKey = 'wf_stage_id';
    
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
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'prgm_id',
        'stage_code',
        'stage_name',        
        'role_id',
        'status',
        'is_doc',
        'route_name',
        'user_journey',
     ];
    
    /**
     * Get Wf stage Details 
     *
    */
    public static function getWfDetail($prgm_id=1)
    {
        $arr = self::from('wf_stage as wf')
            ->select('wf.*')
            ->where('wf.prgm_id', $prgm_id)
            ->get();
        return ($arr ? $arr : []);
    }
    
    /**
     * Get workflow detail by wf stage code
     * 
     * @param string $wf_stage_code
     * @return mixed
     * @throws BlankDataExceptions
     */
    public static function getWfDetailById($wf_stage_code, $prgm_id=1)
    {
        if (empty($wf_stage_code)) {
            throw new BlankDataExceptions(trans('error_message.no_data_found'));
        }
        
        $arr = self::from('wf_stage as wf')
            ->select('wf.*')
            ->where('wf.prgm_id', $prgm_id)
            ->where('wf.stage_code', $wf_stage_code)
            ->first();

        return ($arr ? $arr : null);
    }
  
    /**
     * Get workflow detail by wf stage code
     * 
     * @param string $wf_stage_code
     * @return mixed
     * @throws BlankDataExceptions
     */
    public static function getNextWfStage($wf_order_no, $prgm_id=1)
    {

        $next_wf_order_no = (int) $wf_order_no + 1;
        $arr = self::from('wf_stage as wf')
            ->select('wf.*') 
            ->where('wf.prgm_id', $prgm_id)    
            ->skip($next_wf_order_no)->take(1)->first();
        return ($arr ? $arr : null);
    } 
    
    
/**
     * Get Current WfStage by Role id
     * 
     * @param type $app_id
     * @return type
     */    
    protected static function getCurrentWfStagebyRole($roleId)
    {
        $appData = self::select('wf_stage.*')
                ->where('wf_stage.role_id', $roleId)
                ->orderBy('wf_stage.order_no')
                ->limit(1)
                ->first();
        return $appData ? $appData : null;
    }
    
    
    /**
     * Get Workflow Detail By Order No
     * 
     * @param integer $wf_order_no
     * @param integer $prgm_id
     * @return mixed
     */
    public static function getWfDetailByOrderNo($wf_order_no, $prgm_id=1)
    {
        $arr = self::from('wf_stage as wf')
            ->select('wf.*')
            ->where('wf.prgm_id', $prgm_id)
            ->where('wf.order_no', $wf_order_no)
            ->first();
        return ($arr ? $arr : null);
    }    
}
  

