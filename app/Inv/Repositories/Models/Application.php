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
        $appData = self::select('app.user_id','app.app_id', 'biz.biz_entity_name', 'biz.biz_id', 'app.status')
                ->join('biz', 'app.biz_id', '=', 'biz.biz_id')
                 ->join('app_assign', 'app_assign.assigned_user_id', '=', 'app.user_id')
                //->where('app_assign.to_id', \Auth::user()->user_id)
                ->where('app_assign.role_id', $roleData['0']->id)
                ->where('app.is_assigned', 1)
                ->groupBy('app.app_id')
                ->orderBy('app.app_id');        
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
                 ->join('app_assign',  'app_assign.app_id','app.app_id')
                 //->join('app_wf', 'app_wf.biz_app_id', '=', 'app.app_id')
                 //->join('wf_stage', 'app_wf.wf_stage_id', '=', 'wf_stage.wf_stage_id')
                ->where('app.is_assigned', 0)
                ->where('app_assign.role_id', $roleData[0]->id)
                ->where('app_assign.is_owner', 1)
                ->orderBy('app.app_id');        
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
    
}