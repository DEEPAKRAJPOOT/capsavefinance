<?php

namespace App\Inv\Repositories\Models;

use DB;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use App\Inv\Repositories\Entities\User\Exceptions\BlankDataExceptions;
use App\Inv\Repositories\Entities\User\Exceptions\InvalidDataTypeExceptions;
use App\Inv\Repositories\Models\User;
use App\Inv\Repositories\Factory\Models\BaseModel;

class Application extends BaseModel
{
    
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
        $curUserId = \Auth::user()->user_id;
        $appData = self::select('app.user_id','app.app_id', 'biz.biz_entity_name', 'biz.biz_id', 
                'app.status','app_assign.to_id', 'users.anchor_id', 'users.is_buyer as user_type',
                DB::raw("CONCAT_WS(' ', rta_users.f_name, rta_users.l_name) AS assoc_anchor"),
                DB::raw("CONCAT_WS(' ', rta_assignee_u.f_name, rta_assignee_u.l_name) AS assignee"), 
                DB::raw("CONCAT_WS(' ', rta_from_u.f_name, rta_from_u.l_name) AS assigned_by"),                
                DB::raw("CONCAT_WS(' ', rta_users.f_name, rta_users.l_name) AS name"),
                'users.email',
                'users.mobile_no',                
                'app_assign.sharing_comment', 'assignee_r.name as assignee_role', 'from_r.name as from_role',
                'app_assign.app_assign_id')
                ->join('users', 'users.user_id', '=', 'app.user_id')  
                ->join('biz', 'app.biz_id', '=', 'biz.biz_id')                                               
                ->join('app_assign', function ($join) use($roleData, $curUserId) {
                    $join->on('app.app_id', '=', 'app_assign.app_id');
                    if ($roleData[0]->is_superadmin != 1) {
                        $join->on('app_assign.to_id', '=', DB::raw($curUserId));
                    } else {
                        $join->on('app_assign.is_owner', '=', DB::raw("1"));
                        $join->whereNotNull('app_assign.to_id');
                    }
                })
                ->join('users as assignee_u', 'app_assign.to_id', '=', 'assignee_u.user_id')             
                ->join('users as from_u', 'app_assign.from_id', '=', 'from_u.user_id')
                ->join('role_user as assignee_ru', 'app_assign.to_id', '=', 'assignee_ru.user_id')
                ->join('roles as assignee_r', 'assignee_ru.role_id', '=', 'assignee_r.id')
                ->leftJoin('role_user as from_ru', 'app_assign.from_id', '=', 'from_ru.user_id')
                ->leftJoin('roles as from_r', 'from_ru.role_id', '=', 'from_r.id');    
        if ($roleData[0]->id == 11) {            
                //$appData->where('users.anchor_user_id', \Auth::user()->user_id);            
                $appData->where('users.anchor_id', \Auth::user()->anchor_id);            
        }
//        else if ($roleData[0]->is_superadmin != 1) {
//                $appData->where('app_assign.to_id', \Auth::user()->user_id);            
//        } else {
//           $appData->whereNotNull('app_assign.to_id'); 
//        }
        $appData->groupBy('app.app_id');
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
                'users.is_buyer as user_type', DB::raw("CONCAT_WS(' ', rta_users.f_name, rta_users.l_name) AS assoc_anchor"),
                'assignee_r.name AS assignee', 
                DB::raw("CONCAT_WS(' ', rta_from_u.f_name, rta_from_u.l_name) AS assigned_by"),
                DB::raw("CONCAT_WS(' ', rta_users.f_name, rta_users.l_name) AS name"),
                'users.email',
                'users.mobile_no',                   
                'app_assign.sharing_comment')                 
                ->join('users', 'users.user_id', '=', 'app.user_id')  
                    ->join('biz', 'app.biz_id', '=', 'biz.biz_id')
                //->leftJoin('anchor_user', 'app.user_id', '=', 'anchor_user.user_id')
                
                ->leftJoin('app_assign', function ($join) {
                    $join->on('app.app_id', '=', 'app_assign.app_id');
                    $join->on('app_assign.is_owner', '=', DB::raw("1"));                    
                })                
                ->leftJoin('roles as assignee_r', 'app_assign.role_id', '=', 'assignee_r.id')
                ->leftJoin('users as from_u', 'app_assign.from_id', '=', 'from_u.user_id');
        if ($roleData[0]->is_superadmin != 1) {
            $appData->where('app_assign.role_id', $roleData[0]->id);
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
         $app_id = (int)$app_id;
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
        $appData = self::distinct()->select('app.user_id','app.app_id','app.loan_amt', 'users.f_name', 'users.m_name', 'users.l_name', 'users.email', 'users.mobile_no', 'biz.biz_entity_name', 'biz.biz_id', 'app.status', 'users.anchor_id', 'users.is_buyer as user_type', 'app.created_at')
                ->join('biz', 'app.biz_id', '=', 'biz.biz_id')
                ->join('users', 'app.user_id', '=', 'users.user_id')
                ->where('app.user_id', \Auth::user()->user_id);
        //$appData->groupBy('app.app_id');
        $appData = $appData->orderBy('app.app_id', 'DESC')->get();
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
    
    /**
     * Get Anchor Data By Application Id
     * 
     * @param integer $app_id
     * @return mixed
     * @throws BlankDataExceptions
     * @throws InvalidDataTypeExceptions
     */
    public static function getAnchorDataByAppId($app_id)
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
        
        $appData = self::select('app.*','users.*')  //,'anchor_user.*', 'anchor.*'
                //->join('anchor_user', 'anchor_user.user_id', '=', 'app.user_id')
                //->join('anchor', 'anchor.anchor_id', '=', 'anchor_user.anchor_id')
                ->join('users', 'users.user_id', '=', 'app.user_id')
                ->where('app.app_id', $app_id)                
                ->first();
                       
        return ($appData ? $appData : null);             
    }    
}