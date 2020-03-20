<?php

namespace App\Inv\Repositories\Models\Lms;

use DB;
use App\Inv\Repositories\Factory\Models\BaseModel;
use App\Inv\Repositories\Entities\User\Exceptions\BlankDataExceptions;
use App\Inv\Repositories\Entities\User\Exceptions\InvalidDataTypeExceptions;

class RequestWfStage extends BaseModel {
    /* The database table used by the model.
     *
     * @var string
     */

    protected $table = 'lms_wf';

    /**
     * Custom primary key is set for the table
     *
     * @var integer
     */
    protected $primaryKey = 'req_wf_id';

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
    protected $fillable = [
        'wf_stage_id',
        'req_id',
        'wf_status',              
        'created_at',
        'created_by',
        'updated_at',
        'updated_by',
    ];

    /**
     * Get Wf stage Details 
     *
    */
    public static function updateWfStage($wf_stage_id, $req_id, $arrData = [])
    {
       $rowUpdate = self::where('wf_stage_id', $wf_stage_id)->where('req_id', $req_id)->update($arrData);

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
     * Get Current WfStage by req id
     * 
     * @param integer $req_id
     * @return mixed
     */    
    protected static function getCurrentWfStage($req_id) 
    {
        $appData = self::select('lms_wf_stage.stage_code','lms_wf_stage.assign_role','lms_wf_stage.order_no')
                ->join('lms_wf_stage', 'lms_wf.wf_stage_id', '=', 'lms_wf_stage.wf_stage_id')                 
                ->where('lms_wf.req_id', $req_id)
                ->where('lms_wf.wf_status', '!=', 1)
                ->orderBy('lms_wf_stage.order_no', 'ASC')
                ->limit(1)
                ->first();
        return $appData ? $appData : null;
    }

    /**
     * Get request workflow stage by $wf_stage_code and $req_id
     * 
     * @param string $wf_stage_code
     * @param integer $req_id
     * 
     * @return mixed
     */
    protected static function getRequestWfStage($wf_stage_code, $req_id) 
    {
        $wfData = self::select('lms_wf_stage.wf_stage_id', 'lms_wf_stage.order_no', 'lms_wf_stage.route_name')
                ->join('lms_wf_stage', 'lms_wf.wf_stage_id', '=', 'lms_wf_stage.wf_stage_id')
                ->where('lms_wf_stage.stage_code', $wf_stage_code)
                ->where('lms_wf.req_id', $req_id)
                ->first();
        
        return $wfData ? $wfData : false;
    }    
}

