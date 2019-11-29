<?php namespace App\Inv\Repositories\Models\Master;

use App\Inv\Repositories\Models\Master\Permission;
use App\Inv\Repositories\Factory\Models\BaseModel;
use App\Inv\Repositories\Entities\User\Exceptions\BlankDataExceptions;
use App\Inv\Repositories\Entities\User\Exceptions\InvalidDataTypeExceptions;

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
         $arr = self::select('users.*','roles.*')
                 ->join('users', 'role_user.user_id', '=', 'users.user_id')
                ->join('roles', 'roles.id', '=', 'role_user.role_id');
                return $arr;
    }
}
