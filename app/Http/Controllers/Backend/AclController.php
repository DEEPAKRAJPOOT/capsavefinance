<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use Auth;
use Helpers;
use Session;
use Illuminate\Http\Request;
use App\Inv\Repositories\Contracts\UserInterface as InvUserRepoInterface;
use App\Inv\Repositories\Contracts\AclInterface as AclRepoInterface;


class AclController extends Controller {

    protected $userRepo;
    protected $aclRepo;

    public function __construct(
     InvUserRepoInterface $user_repo,  AclRepoInterface $acl_repo
    ) {
        $this->userRepo = $user_repo;
        $this->aclRepo = $acl_repo;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index() {
        //$business_info = $this->appRepo->getApplicationById($request->biz_id);
        //$OwnerPanApi = $this->userRepo->getOwnerApiDetail($attribute);
        //$userFile = $this->docRepo->saveFile($uploadData);
        
        return view('backend.acl.index');
    }
    
    /**
     * Display a listing of the resource.
     */
    public function addRole(Request $request) {
        try {
            $roleId = ($request->get('role_id'))?$request->get('role_id'):null;
            $roleInfo = [];
            if ($roleId) {
                $roleInfo = $this->userRepo->getRole($roleId);
                
            }
           return view('backend.acl.add_role')
                            ->with('role_id', $roleId)
                            ->with('roleInfo',$roleInfo);
        } catch (Exception $ex) {
            return redirect()->back()->withErrors(Helpers::getExceptionMessage($ex));
        }
        
    }
    
     /**
     * Display a listing of the resource.
     */
    public function saveRole(Request $request) {
        try {
            $arrRoleVal = $request->all();
            $roleId = ($request->post('role_id'))?$request->post('role_id'):null;
            $arrRoleData = [
                'name' => $arrRoleVal['role'],
                'description' => $arrRoleVal['description'],
                'display_name' => 'User',
                'is_active' => $arrRoleVal['is_active'],
                'redirect_path' => 'dashboard',
            ];
            $updateRoelInfo = $this->userRepo->addRole($arrRoleData, $roleId);           
            if ($updateRoelInfo) {
                Session::flash('message', 'Role has been updated/Created');
                return redirect()->route('get_role');
            } 
        } catch (Exception $ex) {
            return redirect()->back()->withErrors(Helpers::getExceptionMessage($ex));
        }
        
    }
   
    /**
    * get role permitions
    * 
    */
    
    public function getRolePermission(Request $request) {
        try {
           
            $roleId = $request->get('role_id');
            $name = $request->get('name');
           
            $getParentData = $this->userRepo->getParaentRoute()->toArray();
            return view('backend.acl.manage_permission')
                    ->with('getParentData' , $getParentData)
                    ->with('role_id', $roleId)
                    ->with('name', $name);
        } catch (Exception $ex) {
            return redirect()->back()->withErrors(Helpers::getExceptionMessage($ex));
        }
        
    }
    
    /**
     * Save role permission 
     * 
     * @param Request $request
     * @return type
     */
    
    public function saveRolePermission(Request $request) {
        try {
         
             $parentPermission = $request->get('parent');
             $Permission = $request->get('child')?$request->get('child'):[];
             $role_id = $request->get('role_id')?$request->get('role_id'):[];
             
             
             $permissionData = [];
            if (count($Permission) > 0 || count($parentPermission) > 0) {
                if (count($Permission) > 0) {
                    foreach ($Permission as $permission_id) {
                        $permissionRows = $this->userRepo->getChildByPermissionId($permission_id);
                        foreach ($permissionRows as $perRow) {
                            if ($perRow->count() > 0) {
                                if ($perRow->is_display == 0) {
                                    $permissionData[] = $perRow->id;
                                }
                            }
                        }
                    }
                } else {
                    $Permission = [];
                }
           $Permission_manul = [];
                $permissionData = array_merge($parentPermission, $permissionData, $Permission, $Permission_manul);
                // dd($permissionData);
                //$collection->pluck('user_id')->all();
                //Attach new permission
                $result = $this->userRepo->givePermissionTo($role_id, $permissionData);
                if ($result) {
                    //Session::flash('message_growl', trans('backend_messages.permission.added.msg'));
                    return redirect(route('get_role'));
                }
            } else {
                return redirect()->back()->withErrors(trans('backend_messages.permission.one.msg'))->withInput();
            }
       
        } catch (Exception $ex) {
            return redirect()->back()->withErrors(Helpers::getExceptionMessage($ex));
        }
        
    }
    

}
