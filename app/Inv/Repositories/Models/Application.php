<?php

namespace App\Inv\Repositories\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use App\Inv\Repositories\Entities\User\Exceptions\BlankDataExceptions;
use App\Inv\Repositories\Entities\User\Exceptions\InvalidDataTypeExceptions;
use App\Inv\Repositories\Models\User;

class Application extends Model
{
    use Notifiable;
    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'app';

    /**
     * Custom primary key is set for the table
     *
     * @var integer
     */
    protected $primaryKey = 'app_id';

    /**
     * The attributes that should be mutated to dates.
     *
     * @var array
     */
    //protected $dates = ['deleted_at'];

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'user_id',
        'biz_id',
        'loan_amt',
        'status',
        'is_assigned',
        'created_by',
        'created_at',
        'updated_at',
        'updated_by'
    ];

    /**
     * Get Applications for Application list data tables
     */
    protected static function getApplications() 
    {
        $roleData = User::getBackendUser(\Auth::user()->user_id);
        
        $appData = self::distinct()->select('app.user_id','app.app_id', 'biz.biz_entity_name', 'biz.biz_id', 'app.status','app_assign.to_id', 'anchor_user.anchor_id', 'anchor_user.user_type')
                ->join('biz', 'app.biz_id', '=', 'biz.biz_id')
                 ->leftJoin('anchor_user', 'app.user_id', '=', 'anchor_user.user_id')
                ->leftJoin('app_assign', 'app_assign.assigned_user_id', '=', 'app.user_id');
                //->where('app_assign.to_id', \Auth::user()->user_id)
        if ($roleData[0]->is_superadmin != 1) {
                $appData->where('app_assign.to_id', \Auth::user()->user_id);
                $appData->where('app_assign.is_owner', 1);
                //->where('app.is_assigned', 1)
        }
        //$appData->groupBy('app.app_id');
        $appData = $appData->orderBy('app.app_id');
        return $appData;
    }
   
    
    public static function getApplicationsDetail($user_id)
    {
        /**
         * Check id is not blank
         */
        if (empty($user_id)) {
            throw new BlankDataExceptions(trans('error_message.no_data_found'));
        }

        /**
         * Check id is not an integer
         */
        if (!is_int($user_id)) {
            throw new InvalidDataTypeExceptions(trans('error_message.invalid_data_type'));
        }
               
        
        $appData = self::select('app.*')
                ->where('app.user_id', $user_id)->get();
                       
        return ($appData?$appData:null);
        
    }
    
    /**
     * Get Applications for Application list data tables
     */
    public static function getApplicationPoolData() 
    {
        
        $roleData = User::getBackendUser(\Auth::user()->user_id);
        $appData = self::select('app.*')
                 ->leftJoin('app_assign',  'app_assign.app_id','app.app_id');
                 //->join('app_wf', 'app_wf.biz_app_id', '=', 'app.app_id')
                 //->join('wf_stage', 'app_wf.wf_stage_id', '=', 'wf_stage.wf_stage_id')
                //->where('app.is_assigned', 0)
        if ($roleData[0]->is_superadmin != 1) {
            $appData->where('app_assign.role_id', $roleData[0]->id);
            $appData->where('app_assign.is_owner', 1);
            $appData->whereNull('app_assign.to_id');
        }
        $appData->groupBy('app.app_id');
        $appData = $appData->orderBy('app.app_id');
        
        return $appData;
    } 
    /**
     * update application details
     *
     * @param integer $user_id     user id
     * @param array   $arrUserData user data
     *
     * @return boolean
     */

    
     public static function updateAppDetails($app_id, $arrUserData = [])
    {
        /**
         * Check id is not blank
         */
        if (empty($app_id)) {
            throw new BlankDataExceptions(trans('error_message.no_data_found'));
        }

        /**
         * Check id is not an integer
         */
        if (!is_int($app_id)) {
            throw new InvalidDataTypeExceptions(trans('error_message.invalid_data_type'));
        }

        /**
         * Check Data is Array
         */
        if (!is_array($arrUserData)) {
            throw new InvalidDataTypeExceptions(trans('error_message.send_array'));
        }

        /**
         * Check Data is not blank
         */
        if (empty($arrUserData)) {
            throw new BlankDataExceptions(trans('error_message.no_data_found'));
        }

        $rowUpdate = self::find((int) $app_id)->update($arrUserData);

        return ($rowUpdate ? $rowUpdate : false);
    }
    
    /**
     * Get Application Data By Biz Id
     * 
     * @param integer $biz_id
     * @return mixed
     * @throws BlankDataExceptions
     * @throws InvalidDataTypeExceptions
     */
    public static function getAppDataByBizId($biz_id)
    {
        /**
         * Check id is not blank
         */
        if (empty($biz_id)) {
            throw new BlankDataExceptions(trans('error_message.no_data_found'));
        }

        /**
         * Check id is not an integer
         */
        if (!is_int($biz_id)) {
            throw new InvalidDataTypeExceptions(trans('error_message.invalid_data_type'));
        }
               
        
        $appData = self::select('app.app_id')
                ->where('app.biz_id', $biz_id)->first();
                       
        return ($appData ? $appData : null);        
    }

    
    /**
     * Update Application Data By application Id
     * 
     * @param integer $app_id
     * @param array $arrData
     *
     * @return mixed
     * @throws BlankDataExceptions
     * @throws InvalidDataTypeExceptions
     */
    public static function updateAppData($app_id, $arrData=[])
    {
        /**
         * Check id is not blank
         */
        if (empty($app_id)) {
            throw new BlankDataExceptions(trans('error_message.no_data_found'));
        }

        /**
         * Check id is not an integer
         */
        if (!is_int($app_id)) {
            throw new InvalidDataTypeExceptions(trans('error_message.invalid_data_type'));
        }
               
        
        $appData = self::where('app_id', $app_id)->update($arrData);                
                       
        return ($appData ? $appData : false);
    }    
    
    /**
     * Get Application Data By App Id
     * 
     * @param integer $app_id
     * @return mixed
     * @throws BlankDataExceptions
     * @throws InvalidDataTypeExceptions
     */
    public static function getAppData($app_id)
    {
        /**
         * Check id is not blank
         */
        if (empty($app_id)) {
            throw new BlankDataExceptions(trans('error_message.no_data_found'));
        }

        /**
         * Check id is not an integer
         */
        if (!is_int($app_id)) {
            throw new InvalidDataTypeExceptions(trans('error_message.invalid_data_type'));
        }
               
        
        $appData = self::select('app.*')
                ->where('app.app_id', $app_id)->first();
                       
        return ($appData ? $appData : null);        
    }    
    
    
}