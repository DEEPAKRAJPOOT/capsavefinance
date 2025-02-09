<?php namespace App\Inv\Repositories\Models\Master;

use App\Inv\Repositories\Models\Master\Permission;
use App\Inv\Repositories\Factory\Models\BaseModel;
use App\Inv\Repositories\Entities\User\Exceptions\BlankDataExceptions;
use App\Inv\Repositories\Entities\User\Exceptions\InvalidDataTypeExceptions;
use DB;

class RoleUser extends BaseModel
{
    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'role_user';

    

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
        'user_id',
        'role_id',
        'created_by',
        'created_at',
        'updated_at',
        'updated_by'
    ];

    
    
   
    
    /**
     * Save role data
     *
     * @param array $roleData
     *
     * @return object role
     */
    public static function addNewRoleUser($roleData)
    {
        $roleObj = self::create($roleData);
        
        return ($roleObj ? : false);
    }
    
    /**
     * Check role assign to any user
     *
     * @param integer $role_id
     *
     * @return integer
     */
    public static function checkRoleAssigntoUser($role_id)
    {
        $countRow = self::where('roles.id', $role_id)->join('role_user', 'roles.id', '=', 'role_user.role_id')->count();
         return ($countRow ? : 0);
    }

    
    /**
     * Get Backend Role
     *
     * @param int $role_id
     *
     * @return object roles
     *
     * @since 0.1
     */
    public static function getBackendRole()
    {
        $arrRoles = self::where('is_admin_login_allowed', 1)->get();
        return ($arrRoles ? : false);
    }
    
     /**
     * Get Backend User
     *
     *
     *
     * @since 0.1
     */
    public static function getRole($role_id)
    {
        $arrRoles = Role::where('id', $role_id)->first();
        return ($arrRoles ? : false);
    }
    
    /**
     * Get Backend User
     *
     *
     *
     * @since 0.1
     */
    public static function getAllData()
    {
         $arr = self::select('users.*','users.is_active as u_active','users.created_at as   created_on','users.updated_by as updated','roles.*', 
                 DB::raw("CONCAT_WS(' ', rta_rptmgr.f_name, rta_rptmgr.l_name) AS reporting_mgr"))
                 ->join('users', 'role_user.user_id', '=', 'users.user_id')
                ->join('roles', 'roles.id', '=', 'role_user.role_id')
                 ->leftJoin('users as rptmgr', 'users.parent_id', '=', 'rptmgr.user_id')
                 ->where('roles.role_type',2)
                 ->where('roles.is_editable','!=',0);
                return $arr;
    }
    
     /**
     * Get Backend User
     *
     *
     *
     * @since 0.1
     */
    public static function getRoleDataById($user_id)
    {
        $arrRoles = self::where('user_id', $user_id)->first();
        return ($arrRoles ? : false);
    }
    
     /**
     * Get Backend User
     *
     *
     *
     * @since 0.1
     */
    public static function updateUserRole($userId, $role)
    {
        $arrRoles = self::where('user_id', $userId)->where('is_logged_in_role', 1)->update($role);
        return ($arrRoles ? : false);
    }
    
     /**
     * Get Backend User
     *
     *
     *
     * @since 0.1
     */
    public static function getAllUsersByRoleId($role_id)
    {
         $arr = self::select('users.*')
                 ->join('users', 'role_user.user_id', '=', 'users.user_id')
                 ->where('role_user.role_id',$role_id)
                 ->where('users.is_active', 1)->pluck('f_name','user_id');  //DB::raw("CONCAT_WS(' ', rta_users.f_name, rta_users.l_name) AS f_name")
                return $arr;
    }
    
     /**
     * Get Backend User
     *
     *
     *
     * @since 0.1
     */
    public static function getBackendUsersByRoleId($role_id, $usersNotIn=[], $orUsersIn=[])
    {
        // dump($usersNotIn, $orUsersIn);
         $query = self::select('users.*')
                 ->join('users', 'role_user.user_id', '=', 'users.user_id')
                 ->where('role_user.role_id',$role_id)
                 ->where('users.is_active', 1)
                 ->orderBy('f_name','ASC');
         if (count($usersNotIn) > 0) {
             $query->whereNotIn('users.user_id', $usersNotIn);
         }
         if (count($orUsersIn) > 0) {
             $query->orWhereIn('users.user_id', $orUsersIn);
         }
         $arr =   $query->get();
         return $arr;
    } 
    
    // Check role id behalf of user_id
    public static function getAllRoleDataByUserId($user_id)
    {
        $arrRoles = self::where('user_id', $user_id)
                        ->whereIn('role_id', [5, 6])
                        ->get();
        return ($arrRoles ? : false);
    }

    public static function getRoleActiveUsers($roleIds = [])
    {
        $users = self::select('users.*')
                 ->join('users', 'role_user.user_id', '=', 'users.user_id')
                 ->whereIn('role_user.role_id', $roleIds)
                 ->where('users.is_active', 1)
                 ->get();
        return $users;         
    }

    public static function getAllUsersByRoleIds($role_id)
    {
         $arr = self::select('users.*')
                 ->join('users', 'role_user.user_id', '=', 'users.user_id')
                 ->join('roles', 'role_user.role_id', '=', 'roles.id')
                 ->where('role_user.role_id',$role_id)
                 ->get();  //DB::raw("CONCAT_WS(' ', rta_users.f_name, rta_users.l_name) AS f_name")
                return $arr;
    }
}
