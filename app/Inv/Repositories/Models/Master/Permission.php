<?php

namespace App\Inv\Repositories\Models\Master;

use App\Inv\Repositories\Models\Master\Role;
use App\Inv\Repositories\Factory\Models\BaseModel;

class Permission extends BaseModel
{

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'permissions';

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
    protected $fillable = ['name', 'display_name', 'description', 'parent_permission_id'];

    /**
     * A permission can be applied to roles.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function roles()
    {
        return $this->belongsToMany(Role::class);
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
    public static function getRoute()
    {
        $arrPermission = Permission::all();
        return ($arrPermission ? : false);
    }

    /**
     * Save route data
     *
     * @param array $routeData
     * @param array $route_id route id
     *
     * @return object route
     */
    public static function addRoute($routeData, $route_id)
    {
        $routeObj = self::updateOrCreate($route_id, $routeData);
        return ($routeObj ? : false);
    }

    /**
     * A permission can be applied to roles.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public static function getPermissionRoles($id)
    {
        $permission = Permission::find($id);
        return $permission->roles;
    }

    /**
     * Get All Permission List
     *
     * @param void()
     *
     * @return object permissions
     *
     * @since 0.1
     */
    public static function getPermissionList()
    {

        $arrPermissions = self::join('permission_role', 'permissions.id', '=', 'permission_role.permission_id')
                ->join('roles', 'roles.id', '=', 'permission_role.role_id')
                ->select('roles.id', 'roles.name', 'permissions.display_name', 'permission_role.permission_id')
                ->groupBY('roles.name');

        return $arrPermissions;
    }

    public function children()
    {
        return $this->hasMany(Permission::class, 'parent_permission_id', 'id')->where('is_display', 1);
    }

    public static function getChildByPermissionId($parent_permission_id)
    {
        $permission = Permission::find($parent_permission_id);
        return $permission->hasMany(Permission::class, 'parent_permission_id', 'id')
                        ->where('parent_permission_id', $parent_permission_id)->get();
    }

    /**
     * Get All Permission
     *
     * @param void()
     *
     * @return object permissions
     *
     * @since 0.1
     */
    public static function getAllPermissions()
    {
        $arrPermissions = self::select('id', 'display_name', 'parent_permission_id', 'name')
                        ->where('parent_permission_id', 0)
                        ->where('type', 2)
                        ->where('is_display', 1)
                        ->orderBy('order', 'Desc')->get();
        $i = 0;
        foreach ($arrPermissions as $arrPermission) {
            $arrPermissions[$i]['children'] = $arrPermission->children()->get();
            $i++;
        }


        return ($arrPermissions ? : false);
    }
    
    /**
     * get permission name
     *
     * @param type $role_id
     *
     * @return type
     */
    public static function getPermissionByID($permission_id)
    {
        $permission_name = self::where('id', $permission_id)->first();
        return ($permission_name ? $permission_name : false);
    }
    
    /**
     *
     * Get Route Name
     * @return String
     */
    public static function getParentRouteName($route_name)
    {
        $arrPermissions = self::select('id', 'parent_permission_id', 'name')
                        ->where('name', $route_name)
                        ->first();
        if ($arrPermissions === null) {
            return true;
        }

        if ($arrPermissions->parent_permission_id > 0) {
            $arrPermissions = self::select('id', 'parent_permission_id', 'name')
                        ->where('id', $arrPermissions->parent_permission_id)
                        ->first();

            if ($arrPermissions === null) {
                return true;
            }
            return $arrPermissions->name;
        }
       
         return $arrPermissions->name;
    }
    
    
     /**
     *
     * Get Route Name
     * @return String
     */
    public static function getParentRoute()
    {
        $permission = self::whereNull('parent_permission_id')->get();
        return ($permission ? $permission : []);
    }
    
    /**
     * get permission name
     *
     * @param type $role_id
     *
     * @return type
     */
    public static function getByParent($parentId, $isDisplay)
    {
        $permission_name = self::where('parent_permission_id', $parentId)
                ->where('is_display', $isDisplay)->get();
        return ($permission_name ? $permission_name : []);
    }

    /**
     * get permission name by Array
     *
     * @param type $role_id
     *
     * @return type
     */
    public static function getPermissionByArr($permission_id)
    {
        $permission_name = self::whereIn('id', $permission_id)->pluck('name');
        return ($permission_name ? $permission_name : []);
    }
    
}
