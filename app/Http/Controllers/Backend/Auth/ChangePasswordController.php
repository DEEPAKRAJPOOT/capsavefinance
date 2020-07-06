<?php

namespace App\Http\Controllers\Backend\Auth;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\ResetsPasswords;
use Session;
use Hash;
use Auth;
use Event;
use Illuminate\Support\Facades\Validator;

class ChangePasswordController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Password Reset Controller
    |--------------------------------------------------------------------------
    |
    | This controller is responsible for handling password reset requests
    | and uses a simple trait to include this behavior. You're free to
    | explore this trait and override any methods you wish to tweak.
    |
    */

    use ResetsPasswords;

    /**
     * Where to redirect users after resetting their password.
     *
     * @var string
     */
    protected $redirectTo = '/dashboard';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {//dd('eeeeeeeee');
        $this->middleware('auth');
    }

    /**
     * Display the password reset view for the given token.
     *
     * If no token is present, display the link request form.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  string|null  $token
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function showChangePasswordForm(Request $request, $token = null)
    {
        return view('backend.auth.passwords.change_password');
    }
    
    /**
     * 
     * @param Request $request
     * @return type
     */
    public function changePassword(Request $request){

        
        if (!(Hash::check($request->get('current-password'), Auth::user()->password))) {
            // The passwords matches
            return redirect()->back()->with("error","Your current password does not matches with the password you provided. Please try again.");
        }
        if(strcmp($request->get('current-password'), $request->get('new-password')) == 0){
            //Current password and new password are same
            return redirect()->back()->with("error","New Password cannot be same as your current password. Please choose a different password.");
        }
        $message = [
            'new-password.required' => 'Please enter your New Password (minimum 8 characters)',
            'new-password_confirmation.required' => 'Please confirm your Password (minimum 8 characters)',
            'new-password.regex' => 'Passwords must include 1 uppercase, 1 lowercase, 1 number and 1 special character.',
            'new-password_confirmation.same' => 'Please enter the same password as New Password.',
        ];
        $rules = [
            'current-password' => 'required',
            'new-password' => 'required|min:8|regex:/^((?=.*?[A-Z])(?=.*?[a-z])(?=.*?[0-9])(?=.*?[#?!@$%^&*-]).{8,})$/',
            'new-password_confirmation' => 'required|same:new-password|regex: /^(?!.*(.)\1\1)(.+)$/'
            ];
        $validator = Validator::make($request->all(), $rules, $message);
        if ($validator->fails()) {
            Session::flash('error', $validator->messages()->first());
                return redirect()->back()->withInput();
        }
        //Change Password
        $firstTime = '';
        $user = Auth::user();
        $user->password = bcrypt($request->get('new-password'));
        if(Auth::user()->is_pwd_changed == 0) {
            $firstTime = 'Y';
            $user->is_pwd_changed = 1;
        }
        $user->save();
        if($firstTime == 'Y') {
            //return redirect(route('dashboard'));
            return redirect('/');

        } else {
        
        return redirect()->back()->with("success","Password changed successfully !");
        }
    }
}
