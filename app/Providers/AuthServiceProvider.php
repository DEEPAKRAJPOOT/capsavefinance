<?php

namespace App\Providers;

use App\Inv\Repositories\Models\User;
use App\Inv\Repositories\Models\Master\Permission;
use Illuminate\Contracts\Auth\Access\Gate as GateContract;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The policy mappings for the application.
     *
     * @var array
     */
    protected $policies = [
        'App\Model' => 'App\Policies\ModelPolicy',
    ];

    /**
     * Register any application authentication / authorization services.
     *
     * @param  \Illuminate\Contracts\Auth\Access\Gate  $gate
     * @return void
     */
    public function boot(GateContract $gate)
    {
        $this->registerPolicies($gate);
           $gate->before(function ($user, $ability) {
            
            //if frontend user control by pass   
            if($user->user_type == 1){
                return true;
            }   
            
            if ($user->roles->first()->is_superadmin == 1) {
                return true;
            }
           });
     
        // Dynamically register permissions with Laravel's Gate.
        foreach ($this->getPermissions() as $permission) {
            if (isset($permission->roles[0])) {
                $gate->define($permission->name, function ($user) use ($permission) {                    
                    //return $user->hasRole($permission->roles);
                    return $this->checkRolePermission($permission->name, $user->user_id);
                });
            }
        }
        
    }
    
    /**
     * Fetch the collection of site permissions.
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    protected function getPermissions()
    {
        return Permission::with('roles')->get();
    }
    
    /**
     * Check Role Permission
     *
     * @param $route_name
     * @param $role_id
     * 
     * @return boolean
     */    
    protected function checkRolePermission($route_name, $user_id)
    {
        $roleData = User::getBackendUser($user_id);
        $role_id = isset($roleData[0]) ? $roleData[0]->id : null;
        return Permission::checkRolePermission($route_name, $role_id);
    }
}