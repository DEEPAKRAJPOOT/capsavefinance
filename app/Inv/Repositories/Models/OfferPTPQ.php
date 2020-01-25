<?php

namespace App\Inv\Repositories\Models;

use DB;
use App\Inv\Repositories\Factory\Models\BaseModel;
use App\Inv\Repositories\Entities\User\Exceptions\InvalidDataTypeExceptions;
use App\Inv\Repositories\Entities\User\Exceptions\BlankDataExceptions;

class OfferPTPQ extends BaseModel
{
    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'offer_ptpq';

    /**
     * Custom primary key is set for the table
     *
     * @var integer
     */
    protected $primaryKey = 'offer_ptpq_id';

    /**
     * The attributes that should be mutated to dates.
     *
     * @var array
     */
    //protected $dates = ['deleted_at'];

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
        'prgm_offer_id',
        'ptpq_from',
        'ptpq_to',
        'ptpq_rate',
        'is_active',
        'created_at',
        'created_by',
        'updated_at',
        'updated_by'
    ];

    
    /**
     * Get all Application Approvers
     * 
     * @param integer $app_id
     * @return array
     */
    /*public static function getAppApprovers($app_id){
        $apprUsers = self::select('*')
            ->where('app_id', '=', $app_id)
            ->where('is_active', '=', 1)
            ->get();
        return ($apprUsers ? $apprUsers : []);
    }*/
    
    
    /**
     * Get all Application Approvers
     * 
     * @param array $data
     * @return array
     */
    /*public static function saveAppApprovers($data)
    {
        $apprUsers = self::where('app_id', '=', $data['app_id'])
            ->where('approver_user_id', '=', $data['approver_user_id'])
            ->where('is_active', '=', 1)
            ->update(['status' => $data['status']]);
        return ($apprUsers ? $apprUsers : []);
    }*/    
    
    
    /**
     * Get all Application Approvers
     * 
     * @param array $data
     * @return array
     */
    /*public static function updateAppApprActiveFlag($app_id)
    {
        $apprUsers = self::where('app_id', '=', $app_id)            
            ->update(['is_active' => 0]);
        return $apprUsers;
    }*/ 
}