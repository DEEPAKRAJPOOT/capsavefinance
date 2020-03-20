<?php

namespace App\Inv\Repositories\Models\Lms;

use DB;
use App\Inv\Repositories\Factory\Models\BaseModel;
use App\Inv\Repositories\Entities\User\Exceptions\BlankDataExceptions;
use App\Inv\Repositories\Entities\User\Exceptions\InvalidDataTypeExceptions;

class WfStage extends BaseModel {
    /* The database table used by the model.
     *
     * @var string
     */

    protected $table = 'lms_wf_stage';

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
        'req_type',
        'stage_code',
        'order_no',
        'stage_name',  
        'assign_role',
        'route_name',
        'created_at',
        'created_by',
        'updated_at',
        'updated_by',
    ];

    
    /**
     * Get Wf stage Details 
     *
    */
    public static function getWfStages($reqType)
    {
        $arr = self::from('lms_wf_stage as wf')
            ->select('wf.*')
            ->where('wf.req_type', $reqType)
            ->get();
        return ($arr ? $arr : []);
    }
    
    /**
     * Get workflow detail by wf stage code
     * 
     * @param string $req_type 
     * @param string $wf_stage_code
     * @return mixed
     * @throws BlankDataExceptions
     */
    public static function getWfDetailById($req_type, $wf_stage_code)
    {
        if (empty($wf_stage_code)) {
            throw new BlankDataExceptions(trans('error_message.no_data_found'));
        }
        
        $arr = self::from('lms_wf_stage as wf')
            ->select('wf.*')   
            ->where('wf.req_type', $req_type)
            ->where('wf.stage_code', $wf_stage_code)
            ->first();

        return ($arr ? $arr : null);
    }
  
    /**
     * Get next workflow by $wf_order_no
     *
     * @param string $req_type 
     * @param string $wf_order_no
     * 
     * @return mixed
     * @throws BlankDataExceptions
     */
    public static function getNextWfStage($req_type, $wf_order_no)
    {
        $next_wf_order_no = (int) $wf_order_no + 1;
        $arr = self::from('lms_wf_stage as wf')
            ->select('wf.*')
            ->where('wf.req_type', $req_type)
            ->skip($next_wf_order_no)->take(1)->first();
        return ($arr ? $arr : null);
    }        
    
    
    /**
     * Get Workflow Detail By Order No
     *
     * @param string $req_type 
     * @param integer $wf_order_no
     *
     * @return mixed
     */
    public static function getWfDetailByOrderNo($req_type, $wf_order_no)
    {
        $arr = self::from('wf_stage as wf')
            ->select('wf.*')            
            ->where('wf.req_type', $req_type)
            ->where('wf.order_no', $wf_order_no)
            ->first();
        return ($arr ? $arr : null);
    }    
}

