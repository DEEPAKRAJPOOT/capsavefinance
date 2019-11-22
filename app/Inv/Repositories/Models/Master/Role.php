<?php namespace App\Inv\Repositories\Models\Master;

use App\Inv\Repositories\Models\Master\Permission;
use App\Inv\Repositories\Factory\Models\BaseModel;
use App\Inv\Repositories\Entities\User\Exceptions\BlankDataExceptions;
use App\Inv\Repositories\Entities\User\Exceptions\InvalidDataTypeExceptions;

class Role extends BaseModel
{
    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'roles';

    /**
     * Custom primary key is set for the table
     *
     * @var integer
     */
    protected $primaryKey = 'id';

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
        'name',
        'display_name',
        'description',
        'is_editable',
        'redirect_path',
        'is_front_login_allowed',
        'is_admin_login_allowed',
        'is_supper_admin',
        'is_default_ml',
        'dashboard_type',
    ];

    /**
     * Scope to get whether a role is allowed to login in the front or not
     *
     * @param Eloquent $query
     * @return Eloquent
     */
    public function scopeFrontLoginAllowed($query)
    {
        return $query->where('is_front_login_allowed', config('Inv_common.YES'));
    }

    /**
     * Scope to get whether a role is allowed to login in the admin or not
     *
     * @param Eloquent $query
     * @return Eloquent
     */
    public function scopeAdminLoginAllowed($query)
    {
        return $query->where('is_admin_login_allowed', config('Inv_common.YES'));
    }

    /**
     * A role may be given various permissions.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function permissions()
    {
        return $this->belongsToMany(Permission::class);
    }
    
    /**
     * Grant the given permission to a role.
     *
     * @param  Permission $permission
     * @return mixed
     */
    public function assignRolePermission($permission)
    {
        
        return $this->permissions()->sync($permission);
    }
    
    /**
     * Grant the given permission to a role.
     *
     * @param  Permission $permission
     * @return mixed
     */
    public function dettachPermissionTo()
    {
        return $this->permissions()->detach();
    }
    
    /**
     * Get All Roles List
     *
     * @param void()
     *
     * @return object roles
     *
     * @since 0.1
     */
    public static function getRoleLists()
    {
        $arrRoles = Role::where('is_editable', config('Inv_common.YES'));
        return ($arrRoles ? : false);
    }
    
    /**
     * Save role data
     *
     * @param array $roleData
     *
     * @return object role
     */
    public static function addRole($roleData, $role_id)
    {
        $roleObj = self::updateOrCreate($role_id, $roleData);
        
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
    public static function getRoleByArray($arr)
    {
        $arrRoles = Role::whereIn('id', $arr)->get();
        return ($arrRoles ? $arrRoles: []);
    }
}
