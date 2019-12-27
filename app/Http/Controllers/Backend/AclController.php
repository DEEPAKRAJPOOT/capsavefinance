<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use Auth;
use Helpers;
use Session;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use App\Inv\Repositories\Contracts\UserInterface as InvUserRepoInterface;
use App\Inv\Repositories\Contracts\AclInterface as AclRepoInterface;
use Event;

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

    /*
     * get role wise user
     */

    public function getUserRole() {
        return view('backend.acl.user_role');
    }

    /*
     * add role user popup
     */

    public function addUserRole(Request $request) {
        try {             
            $roles = $this->userRepo->getRolesByType(2);
            $rolesDataArray = [];
            foreach($roles as $role) {
                $rolesDataArray[$role->id] = $role->name;
            }
            return view('backend.acl.add_user_role')
                    ->with('rolesList', $rolesDataArray);
        } catch (Exception $ex) {
            
        }
    }

    /*
     * add role user
     */

    public function saveUserRole(Request $request) {
        try {

            $data = $request->all();

            $arrData = [];
            $arrAnchUser = [];
            $arrDetailData = [];
            $arrLeadAssingData = [];
            $arrData['f_name'] = $data['f_name'];
            $arrData['m_name'] = '';
            $arrData['l_name'] = $data['l_name'];
            $arrData['biz_name'] = 'xyz';
            $arrData['email'] = $data['email'];
            $arrData['password'] = bcrypt($data['password']);
            $arrData['mobile_no'] = $data['mobile_no'];
            $arrData['user_type'] = 2;
            $arrData['is_email_verified'] = 1;
            $arrData['is_pwd_changed'] = 1;
            $arrData['is_email_verified'] = 1;
            $arrData['is_otp_verified'] = 1;
            $arrData['parent_id'] = !empty($data['parent_id']) ? $data['parent_id'] : 0;
            $arrData['is_appr_required'] = isset($data['is_appr_required']) && !empty($data['is_appr_required']) ? $data['is_appr_required'] : null;
            $arrData['is_active'] = (int)$data['is_active'];
            $userId = null;
            $existData = $this->userRepo->getUserByemail($data['email']);
            if ($existData) {
                Session::flash('error', 'Email has been allredy Exist!');
                return redirect()->route('get_role_user');
            } else {
                $userDataArray = $this->userRepo->save($arrData, $userId);
                if ($userDataArray) {
                    $role = [];
                    $role['user_id'] = $userDataArray->user_id;
                    $role['role_id'] = (int) $data['role_id'];
                    $rr = $this->userRepo->addNewRoleUser($role);
                    //send mail to user
                    $anchUserMailArr = [];
                    $anchUserMailArr['email'] = $data['email'];
                    $anchUserMailArr['name'] = $data['f_name'];
                    $anchUserMailArr['password'] = $data['password'];
                    Event::dispatch("CREATE_BACKEND_USER_MAIL", serialize($anchUserMailArr));
                    Session::flash('message', 'User added successfully!');
                    return redirect()->route('get_role_user');
                } else {
                    Session::flash('message', 'SomeThings went wrong!!!!');
                    return redirect()->route('get_role_user');
                }
            }
        } catch (Exception $ex) {
            
        }
    }

    /*
     * edit role user popup
     */

    public function editUserRole(Request $request) {
        $data = $request->all();
        $userDataArray = $this->userRepo->find($data['user_id']);
        $roleData = $this->userRepo->getRoleDataById($data['user_id']);
        $parentUserData = $this->userRepo->getBackendUsersByRoleId($roleData->role_id, [$data['user_id']]);
        $parentUserDataArr = [];
        foreach($parentUserData as $user) {
            $parentUserDataArr[$user->user_id] = $user->f_name . ' ' . $user->l_name;
        }
        
        $roles = $this->userRepo->getRolesByType(2);
        $rolesDataArray = [];
        foreach($roles as $role) {
            $rolesDataArray[$role->id] = $role->name;
        }
            
        return view('backend.acl.edit_user_role')
                ->with('userData', $userDataArray)
                ->with('roleData', $roleData)
                ->with('parentUserData', $parentUserDataArr)
                ->with('rolesList', $rolesDataArray);
    }

    /*
     * update role user
     */

    public function updateUserRole(Request $request) {
        try {

            $data = $request->all();
            
            $arrData = [];
            $arrAnchUser = [];
            $arrDetailData = [];
            $arrLeadAssingData = [];
            $arrData['f_name'] = $data['f_name'];
            $arrData['m_name'] = '';
            $arrData['l_name'] = $data['l_name'];
            $arrData['biz_name'] = 'xyz';
            $arrData['email'] = $data['email'];
            //$arrData['password'] = bcrypt($data['password']);
            $arrData['mobile_no'] = $data['mobile_no'];
//            $arrData['user_type'] = 2;
//            $arrData['is_email_verified'] = 1;
//            $arrData['is_pwd_changed'] = 1;
//            $arrData['is_email_verified'] = 1;
//            $arrData['is_otp_verified'] = 1;
            $arrData['parent_id'] = !empty($data['parent_id']) ? $data['parent_id'] : 0;
            $arrData['is_active'] = (int)$data['is_active'];
            $arrData['is_appr_required'] = isset($data['is_appr_required']) && !empty($data['is_appr_required']) ? $data['is_appr_required'] : null;
            
            //dd('oooooooooooo', $arrData);
            
            $userId = $data['user_id'];
            $existData = $this->userRepo->getUserByemail($data['email']);
            
            if ($existData && $existData->user_id != $data['user_id']) {
                
                Session::flash('error', 'Email has been allredy Exist!');
                return redirect()->route('get_role_user');
            } else {
                $userDataArray = $this->userRepo->save($arrData, $userId);
                if ($userDataArray) {
                    $role = [];
                    $role['role_id'] = (int) $data['role_id'];
                    $rr = $this->userRepo->updateUserRole( $userId, $role);
//                    //send mail to user
//                    $anchUserMailArr = [];
//                    $anchUserMailArr['email'] = $data['email'];
//                    $anchUserMailArr['name'] = $data['f_name'];
//                    $anchUserMailArr['password'] = $data['password'];
                   // Event::dispatch("CREATE_BACKEND_USER_MAIL", serialize($anchUserMailArr));
                    Session::flash('message', 'User update successfully!');
                    return redirect()->route('get_role_user');
                } else {
                    Session::flash('message', 'SomeThings went wrong!!!!');
                    return redirect()->route('get_role_user');
                }
            }
        } catch (Exception $ex) {
            
        }
    }

}
