<?php

namespace App\Http\Controllers\Backend;

use Session;
use Validator;
use Redirect;
use Route;
use Auth;
use Event;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Requests\UpdateProfileFormRequest;
use App\Http\Requests\ChangePasswordFormRequest;
use App\Http\Requests\BackendUserFormRequest;
use App\Inv\Repositories\Models\Master\Country;
use App\Inv\Repositories\Contracts\Traits\ApiAccessTrait;
use App\Inv\Repositories\Contracts\UserInterface as InvUserRepoInterface;

class UserController extends Controller
{

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct(InvUserRepoInterface $user)
    {
        $this->userRepo = $user;
    }
    
     use ApiAccessTrait;

     
    /**
     * User repository
     *
     * @var object
     */
    protected $userRepo;

    /**
     * Show country list.
     *
     * @return Response
     */
    public function viewProfile(Request $request)
    {
        $userId = \Auth::user()->user_id;
        $userDetails = $this->userRepo->getUserDetail($userId);
        return view('backend.view_profile')->with(['userDetails' => $userDetails]);
    }
    
    /**
     * Update user profile
     * 
     * @return type
     */
    public function updateProfile(){
        $request = request();
        $userId  =  \Auth::user()->user_id;
        $userDetails = $this->userRepo->getUserDetail($userId);
        return view('backend.update_profile')->with(['userDetails' => $userDetails]);
    }
    
     /**
     * Update user profile.
     * 
     * Request UpdateProfileFormRequest
     * @return Response
     */
    public function updateUserProfile(UpdateProfileFormRequest $request)
    {
        try {
            $userId     =  \Auth::user()->user_id;
            $email      = request()->get('email');
            $first_name = request()->get('first_name');
            $last_name  = request()->get('last_name');
            $status_comment = request()->get('status_comment');

            $arrData = [
                'f_name' => $first_name,
                'l_name' => $last_name,
                'email' => $email,
            ];
            $updateUserDate = $this->userRepo->save($arrData, $userId);
            Session::flash('message', 'Basic details have been saved successfully.');
            return redirect(route('view_profile'));
        } catch (Exception $e) {
            return redirect()->back()->withErrors(Helpers::getExceptionMessage($e))->withInput();
        }
    }
    
    /**
     * Ajax file upload
     * 
     * @param Request $request
     * @return string
     * 
     */
    public function ajaxImageUpload(Request $request)
    {
        $validator = Validator::make($request->all(),
            [
                'file' => 'mimes:jpeg,jpg,png,gif|required|max:10000',
            ],
            [
                'file.mimes' => 'The file must be an image (jpeg, jpg, png, bmp, gif, or svg)'
            ]);
        if ($validator->fails())
            return array(
                'fail' => true,
                'errors' => $validator->errors()
            );
        
        $extension = $request->file('file')->getClientOriginalExtension();
        $dir = storage_path().'/app/public/';
        $filename = uniqid() . '_' . time() . '.' . $extension;
        $request->file('file')->move($dir, $filename);
        $arrData = ['user_photo' => $filename];
        $userId     =  \Auth::user()->user_id;
        $updateUserDate = $this->userRepo->save($arrData, $userId);
        return $filename;
    }
    
    /**
     * Change password form
     * 
     */
    public function changePassword(){
        return view('backend.change_password_form');
    }
    
    /**
     * Update change password
     * @param Request $request
     * @return string
     */
    public function updateChangePassword(ChangePasswordFormRequest $request)
    {
        try{
            
            $request_data = $request->All(); 
            $current_password = \Auth::User()->password;    
            if(\Hash::check($request_data['old_password'], $current_password))
            {        
              $user_id = \Auth::User()->user_id;
              $obj_user = $this->userRepo->find($user_id);
              $obj_user->password = \Hash::make($request_data['new_password']);
              $obj_user->save(); 
              Session::flash('message', trans('error_messages.admin.password_changed'));
              return Redirect::back();
            }
            else
            {           
              $error = ['old_password' => trans('error_messages.admin.correct_old_password')];

              return Redirect::back()->withErrors($error);
            }
        } catch (Exception $e) {
            return redirect()->back()->withErrors(Helpers::getExceptionMessage($e))->withInput();
        }
    } 
    /*
     * show user list
     * 
     */
    
    public function viewUserList()
    {
        return view('backend.users');
        
    }
    /*
     * show user Details
     * @param Request $request 
     */
    
    public function viewUserDetail(Request $request) {
        
        try {
            
            $user_id = $request->get('user_id');
            $userData = $this->userRepo->getUserDetail($user_id);
            $userPersonalData = $this->userRepo->getUserPersonalData($user_id);

            $APISecret      = config('common.APISecret');
            $atwayurl       = config('common.gatwayurl');
            $contentType    = config('common.contentType');
            $gatwayhost     = config('common.gatwayhost');
            $apiKey         =  config('common.apiKey');
            $groupId        = config('common.groupId');


            //$fitstName = $userData[0]->f_name;
            $content ='{
                "groupId":"sdsdsds",
                "entityType": "INDIVIDUAL",
                "providerTypes": [
                  "WATCHLIST"
                ],
                "name": "putin",
                "secondaryFields":[],
                "customFields":[]
            }';

           

            $dataArray = [];
            $dataArray['groupId'] = $groupId;
            $dataArray['entityType'] = "INDIVIDUAL";
            $dataArray['providerTypes'] = array("WATCHLIST");
            $dataArray['name'] = "putin";
            $dataArray['secondaryFields'] = [];
            $dataArray['customFields'] = [];

            $endodedData = json_encode($dataArray);
           
            return view('backend.user_detail')
                    ->with(['userData' => $userData,'content' => $endodedData, 'userPersonalData' => $userPersonalData])
                    ;
        } catch (Exception $exc) {
            return redirect()->back()->withErrors(Helpers::getExceptionMessage($exc))->withInput();
        }
    }
    /*
     * show user Details form
     * @param Request $request 
     */
    
    public function editUser(Request $request) 
    {
        
        try {
            $user_id  = $request->get('user_id');
            $userData = $this->userRepo->getUserDetail($user_id);


            
            $countryDropDown = Country::getDropDown();
            return view('backend.edit_user')
                    ->with(['countryDropDown' => $countryDropDown])
                    ->with(['userData' => $userData]);
        } catch (Exception $exc) {
            return redirect()->back()->withErrors(Helpers::getExceptionMessage($exc))->withInput();
        }
    }
    
    
    
    /*
     * Update user
     * @param BackendUserFormRequest $request
     */
    public function saveUser(BackendUserFormRequest $request)
    {
        
        try{
            $user_id = $request->get('user_id');
            $fname = $request->get('first_name');
            $lname = $request->get('last_name');
            $occupation = $request->get('occupation');
            $about = $request->get('about');
            $phone = $request->get('phone');
            $at_phone = $request->get('alternate_phone');
            $addr1 = $request->get('addr1');
            $addr2 = $request->get('addr2');
            $area = $request->get('area');
            $country_id = $request->get('country_id');
            $city = $request->get('city');
            $zip_code = $request->get('zip_code');
            $status = $request->get('status');
            $userArr = [
                'first_name' => isset($fname) ? $fname : NULL,
                'last_name' => isset($lname) ? $lname : NULL,
                'occupation' => isset($occupation) ? $occupation : NULL,
                'about' => isset($about) ? $about : NULL,
                'phone' => isset($phone) ? $phone : NULL,
                'at_phone' => isset($at_phone) ? $at_phone : NULL,
                'addr1' => isset($addr1) ? $addr1 : NULL,
                'addr2' => isset($addr2) ? $addr2 : NULL,
                'area' => isset($area) ? $area : NULL,
                'country_id' => isset($country_id) ? $country_id : NULL,
                'city' => isset($city) ? $city : NULL,
                'zip_code' => isset($zip_code) ? $zip_code : NULL,
                'status' => isset($status) ? $status : NULL,
            ];
            $result = $this->userRepo->updateUser($userArr, $user_id);
            Session::flash('message', 'User updated successfully.');
              return Redirect::Route('manage_users');
        } catch (Exception $ex) {
            return redirect()->back()->withErrors(Helpers::getExceptionMessage($exc))->withInput();
        }
    }
    
    /* Soft detele user
     * @param Request $request
     */
    public function deleteUser(Request $request)
    {
        $user_id = $request->get('uid');
        $response = $this->userRepo->deleteUser($user_id);
        return $response;
    }
    
    
    
    
    
    /*
     * show Users list
     * 
     */
    
    public function viewAllUser()
    {

        $usersList = $this->userRepo->getAllUsersPaginate();
        return view('backend.users',['usersList' => $usersList]);
        
    }
    
    
    public function viewUserAjaxPaginate(Request $request)
    {
      if($request->ajax()) {
        $usersList = $this->userRepo->getAllUsersPaginate();
        return view('backend.pagination_data',['usersList' => $usersList])->render();
      }
        
    }
    
    /*
     * update scoute
     * @param Request $request
     */
    
    public function updateUserDetail(Request $request)
    {
       
        $v = Validator::make($request->all(), ['is_approved' => 'required']);
        $status = "";
        if ($v->fails()){
            return redirect()->back()->withErrors($v->errors());
        }
        $user_id = $request->get('user_id');
        
        $email_id = $request->get('email_id');
        $is_approved_stack = $request->get('is_approved_stack');
       
        $userArr = [
                'is_admin_approved' =>  $request->get('is_approved'),
                'reason' =>  $request->get('reason'),
                'user_type' => 2
            ];
        $userData = $this->userRepo->getUserDetail($user_id);
        //call api for tranjection

        $rrt = (int)$request->get('is_approved');
        $dArr = [];
        if($userData->is_admin_approved != 0 || $rrt == 1 ){
            $dArr['address'] =  $userData->address;           
            $dArr['type'] =  2;           
            $dArr['status'] =  $request->get('is_approved')-1;
            $status = $this->onUpdateUser($dArr);
            
                
            }
      /* 
      if($is_approved_stack == 0){
        $status = $this->onApprovedScout($email_id, \Auth::user()->user_type);
      }else{
         $status = $this->onUpdateUser($request->get('is_approved'));
      }*/
     //dd($status);
     if($status == '200'){
            $result = $this->userRepo->updateUser($userArr, $user_id);
            $userMailArr = [];
            $userMailArr['email_owner'] = $email_id;
            $userMailArr['name'] = $userData->first_name;
            $userMailArr['reason'] = $request->get('reason');
            if($userArr['is_admin_approved'] == 1) {
                Event::fire("admin.approved", serialize($userMailArr));
            } else {
                Event::fire("admin.disapproved", serialize($userMailArr));
            }
            
            Session::flash('message', 'User updated successfully.');
            return Redirect::Route('show_scout');
       }else{
            return redirect()->back()->withErrors('Private key for the user already exists!');
       }
        
        
    }
    

}