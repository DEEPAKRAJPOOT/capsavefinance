<?php

namespace App\Inv\Repositories\Models;

use DB;
use File;

use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Storage;
use App\Inv\Repositories\Factory\Models\BaseModel;
use App\Inv\Repositories\Entities\User\Exceptions\InvalidDataTypeExceptions;
use App\Inv\Repositories\Entities\User\Exceptions\BlankDataExceptions;

class AppAssignment extends BaseModel
{

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'app_assign';

    /**
     * Custom primary key is set for the table
     *
     * @var integer
     */
    protected $primaryKey = 'app_assign_id';
    
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
        'from_id',
        'to_id',
        'role_id',
        'assigned_user_id',
        'app_id',
        'assign_status',
        'sharing_comment',
        'is_owner',
     ];
    
    /**
     * Validating and parsing data passed thos this method
     *
     * @param array $attributes
     * @param mixed $user_id
     *
     * @return New record ID that was added
     *
     * @since 0.1
     */
    public static function saveData($attributes = [])
    {
        /**
         * Check Data is Array
         */
        if (!is_array($attributes)) {
            throw new InvalidDataTypeExceptions('Please send an array');
        }

        /**
         * Check Data is not blank
         */
        if (empty($attributes)) {
            throw new BlankDataExceptions('No Data Found');
        }

        $status =  self::create($attributes);
        return true;
    }
    
    /**
     * update application details
     *
     * @param integer $user_id     user id
     * @param array   $arrUserData user data
     *
     * @return boolean
     */

    
     public static function updateAppAssignById($app_id, $arr = [])
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
        if (!is_array($arr)) {
            throw new InvalidDataTypeExceptions(trans('error_message.send_array'));
        }

        /**
         * Check Data is not blank
         */
        if (empty($arr)) {
            throw new BlankDataExceptions(trans('error_message.no_data_found'));
        }

        $rowUpdate = self::where('app_id',(int) $app_id)->where('is_owner',1)->update($arr);

        return ($rowUpdate ? $rowUpdate : false);
    }
    
    /**
     * Get Application assign data
     * 
     * @param array $whereCondition
     * @return mixed
     */
    public static function getAppAssignmentData ($whereCondition=[])
    {
        $assignData = self::where($whereCondition)->first();
        return $assignData ? $assignData : false;
    }
    
    /**
     * Get Application from user
     * 
     * @param integer $app_id
     * @return mixed
     */
    public static function getOrgFromUser($app_id)
    {
        $assignData = self::select(DB::raw("CONCAT_WS(' ', rta_from_u.f_name, rta_from_u.l_name) AS assigned_by"), 
                'from_r.name as from_role')
                ->leftJoin('users as from_u', 'app_assign.from_id', '=', 'from_u.user_id')
                ->leftJoin('role_user as from_ru', 'app_assign.from_id', '=', 'from_ru.user_id')
                ->leftJoin('roles as from_r', 'from_ru.role_id', '=', 'from_r.id')                   
                ->where('app_id', $app_id)                
                ->where('is_owner', '0')
                ->orderBy('app_assign_id', 'DESC')
                ->first();
        return $assignData ? $assignData : false;
    } 
    
    /**
     * Get Application current assignee
     * 
     * @param integer $app_id
     * @return mixed
     */
    public static function getAppCurrentAssignee($app_id)
    {
        $assignData = self::select(DB::raw("CONCAT_WS(' ', rta_to_u.f_name, rta_to_u.l_name) AS assignee"), 
                'to_r.name as assignee_role')
                ->leftJoin('users as to_u', 'app_assign.to_id', '=', 'to_u.user_id')
                ->leftJoin('role_user as to_ru', 'app_assign.to_id', '=', 'to_ru.user_id')
                ->leftJoin('roles as to_r', 'to_ru.role_id', '=', 'to_r.id')                   
                ->where('app_id', $app_id)
                ->where('is_owner', '1')                
                ->first();
        return $assignData ? $assignData : false;
    }     
  
}
  

