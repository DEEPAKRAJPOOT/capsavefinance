<?php

namespace App\Inv\Repositories\Models;

use DB;
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
        
        $appData = self::distinct()->select('app.user_id','app.app_id', 'biz.biz_entity_name', 'biz.biz_id', 
                'app.status','app_assign.to_id', 'anchor_user.anchor_id', 'anchor_user.user_type',
                DB::raw("CONCAT_WS(' ', rta_users.f_name, rta_users.l_name) AS assoc_anchor"),
                DB::raw("CONCAT_WS(' ', rta_assignee_u.f_name, rta_assignee_u.l_name) AS assignee"), 
                DB::raw("CONCAT_WS(' ', rta_from_u.f_name, rta_from_u.l_name) AS assigned_by"),
                'app_assign.sharing_comment', 'assignee_r.name as assignee_role', 'from_r.name as from_role')
                ->join('biz', 'app.biz_id', '=', 'biz.biz_id')
                 ->leftJoin('anchor_user', 'app.user_id', '=', 'anchor_user.user_id')
                ->leftJoin('users', 'users.anchor_id', '=', 'anchor_user.anchor_id')
                //->leftJoin('app_assign', 'app.app_id', '=', 'app_assign.app_id')
                ->leftJoin('app_assign', function ($join) use($roleData) {
                    $join->on('app.app_id', '=', 'app_assign.app_id');
                    $join->on('app_assign.is_owner', '=', DB::raw("1"));
                    //if ($roleData[0]->is_superadmin != 1) {
                    //    $join->on('app_assign.to_id', \Auth::user()->user_id);
                    //}
                })
                ->leftJoin('users as assignee_u', 'app_assign.to_id', '=', 'assignee_u.user_id')             
                ->leftJoin('users as from_u', 'app_assign.from_id', '=', 'from_u.user_id')
                ->leftJoin('role_user as assignee_ru', 'app_assign.to_id', '=', 'assignee_ru.user_id')
                ->leftJoin('roles as assignee_r', 'assignee_ru.role_id', '=', 'assignee_r.id')
                ->leftJoin('role_user as from_ru', 'app_assign.from_id', '=', 'from_ru.user_id')
                ->leftJoin('roles as from_r', 'from_ru.role_id', '=', 'from_r.id');            
                //->where('app_assign.to_id', \Auth::user()->user_id)
        if ($roleData[0]->is_superadmin != 1) {
                $appData->where('app_assign.to_id', \Auth::user()->user_id);
        //        $appData->where('app_assign.is_owner', 1);        
        } else {
           $appData->whereNotNull('app_assign.to_id'); 
        }
        //$appData->groupBy('app.app_id');
        $appData = $appData->orderBy('app.app_id', 'DESC');
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
        $appData = self::distinct()->select('app.app_id','app.biz_id','app.user_id','biz.biz_entity_name',
                'anchor_user.user_type', DB::raw("CONCAT_WS(' ', rta_users.f_name, rta_users.l_name) AS assoc_anchor"),
                'assignee_r.name AS assignee', 
                DB::raw("CONCAT_WS(' ', rta_from_u.f_name, rta_from_u.l_name) AS assigned_by"),
                'app_assign.sharing_comment')                 
                    ->join('biz', 'app.biz_id', '=', 'biz.biz_id')
                 ->leftJoin('anchor_user', 'app.user_id', '=', 'anchor_user.user_id')
                ->leftJoin('users', 'users.anchor_id', '=', 'anchor_user.anchor_id')
                //->leftJoin('app_assign', 'app_assign.app_id', '=', 'app.app_id')
                ->leftJoin('app_assign', function ($join) use($roleData) {
                    $join->on('app.app_id', '=', 'app_assign.app_id');
                    $join->on('app_assign.is_owner', '=', DB::raw("1"));                    
                    //if ($roleData[0]->is_superadmin != 1) {
                    //    $join->on('app_assign.to_id', \Auth::user()->user_id);
                    //}
                })                
                ->leftJoin('roles as assignee_r', 'app_assign.role_id', '=', 'assignee_r.id')
                ->leftJoin('users as from_u', 'app_assign.from_id', '=', 'from_u.user_id');
                 //->join('app_wf', 'app_wf.biz_app_id', '=', 'app.app_id')
                 //->join('wf_stage', 'app_wf.wf_stage_id', '=', 'wf_stage.wf_stage_id')
                //->where('app.is_assigned', 0)
        if ($roleData[0]->is_superadmin != 1) {
            $appData->where('app_assign.role_id', $roleData[0]->id);
            
            //$appData->where('app_assign.is_owner', 1);
            
        }
        $appData->whereNull('app_assign.to_id');
        //$appData->groupBy('app.app_id');
        $appData = $appData->orderBy('app.app_id', 'DESC');
        
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
    
    /**
     * Get Latest application
     * 
     * @param integer $user_id
     * @return mixed
     * @throws BlankDataExceptions
     * @throws InvalidDataTypeExceptions
     */
    public static function getLatestApp($user_id)
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
                ->where('app.user_id', $user_id)
                ->where('app.status', '0')
                ->orderBy('app.app_id', 'DESC')
                ->first();
                       
        return ($appData ? $appData : null);        
    }

    /**
     * Get User Applications for Application list data tables
     */
    protected static function getUserApplications() 
    {  
        $appData = self::distinct()->select('app.user_id','app.app_id','app.loan_amt', 'biz.biz_entity_name', 'biz.biz_id', 'app.status','app_assign.to_id', 'anchor_user.anchor_id', 'anchor_user.user_type', 'app.created_at')
                ->join('biz', 'app.biz_id', '=', 'biz.biz_id')
                ->leftJoin('anchor_user', 'app.user_id', '=', 'anchor_user.user_id')
                ->leftJoin('app_assign', 'app_assign.assigned_user_id', '=', 'app.user_id')
                ->where('app.user_id', \Auth::user()->user_id);
        //$appData->groupBy('app.app_id');
        $appData = $appData->orderBy('app.app_id', 'DESC');
        return $appData;
    }

    /**
     * Count total numbers of applications
     * 
     * @param integer $user_id
     * @return integer
     */
    public static function getAllAppsByUserId($user_id)
    {
        $appData = self::select('app.*')
                ->where('app.user_id', $user_id)
                ->get();
        return $appData ? $appData : [];
    }
    
}