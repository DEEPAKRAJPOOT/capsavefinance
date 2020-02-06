<?php namespace App\Inv\Repositories\Models\Master;

use App\Inv\Repositories\Factory\Models\BaseModel;

class PermissionRole extends BaseModel
{
    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'permission_role';
 
    protected $fillable = [
        'permission_id',
        'role_id',
        'created_by',
        'created_at',
        'updated_at',
        'updated_by'
    ];

    
        public $timestamps = true;

    /**
     * Maintain created_by and updated_by automatically
     *
     * @var boolean
     */
    public $userstamps = true;
    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */
    protected $hidden = [];
    
    /**
     * Check permission assign to role
     *
     * @param type $permission_id
     * @param type $role_id
     *
     * @return type
     */
    public static function checkPermissionAssigntoRole($permission_id, $role_id)
    {
        $permission = self::where('permission_id', $permission_id)->where('role_id', $role_id)->count();
        return ($permission ? $permission : 0);
    }
    
    /**
     * get permission assign to role
     *
     * @param type $role_id
     *
     * @return type
     */
    public static function getPermissionByRoleID($role_id)
    {
        $permissions = self::where('role_id', $role_id)->pluck('permission_id');
        return ($permissions ? $permissions : false);
    }
    
    /**
     * delete permission assign to role
     *
     * @param type $role_id
     *
     * @return type
     */
    public static function deleteRecById($role_id)
    {
        $permissions = self::where('role_id', $role_id)->delete();
        return ($permissions ? $permissions : false);
    }
    
    
    
    /**
     * delete permission assign to role
     *
     * @param type $role_id
     *
     * @return type
     */
    public static function addPermissionRole($arr)
    {
        $permissions = self::create($arr);
        return ($permissions ? $permissions : false);
    }
    
    /**
     * get permission assign to role
     *
     * @param type $role_id
     *
     * @return type
     */
    public static function checkRole($parentId,$role_id)
    {
        $permissions = self::where('role_id', $role_id)->where('permission_id', $parentId)->first();
        return ($permissions ? $permissions : false);
    }
    
}
