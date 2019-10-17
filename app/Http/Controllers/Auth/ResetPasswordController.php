<?php

namespace App\Http\Controllers\Auth;

use App\Inv\Repositories\Models\User;
use App\Inv\Repositories\Models\ResetPasswordToken;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\ResetsPasswords;
use Illuminate\Support\Facades\Password;
use Session;
use Illuminate\Support\Facades\Lang;
use Illuminate\Support\Str;
use Event;

class ResetPasswordController extends Controller
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

    const INVALID_TOKEN = 'passwords.token';
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest');
    }

    /**
     * 
     * @return string
     */
    protected function redirectTo()
    {
        return '/';
    }
    /**
     * Display the password reset view for the given token.
     *
     * If no token is present, display the link request form.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function showResetForm(Request $request, $token = null)
    {
       // $token = $request;
       $token = key($request->query()); 
       $token_res = ResetPasswordToken::getUserTokenDetails($token)->toArray();
       
     
       if(count($token_res) > 0) {
           $token_res =(object) $token_res[0];
         //  dd($token_res);
           
        if(isset($token_res->is_reset) && $token_res->is_reset == 0) {
            return view('auth.passwords.reset')->with(
                ['token' => $token, 'email' => Session::get('token-email')]
            );
        } else {
             die('Token Expired');
        }
       }else if(count($token_res) == 0) {
        return view('auth.passwords.reset')->with(
            ['token' => $token, 'email' => Session::get('token-email')]
        );
       } else {         
         die('Token Expired');  
       }
    }
    
        /**
     * 
     * @param Request $request
     * @return type
     */
    public function reset(Request $request)
    {
        $this->validate($request,
            [
            'token' => 'required',
            'password' => 'required|min:8|max:15',
            'password_confirmation' => 'required|same:password',
            ],
            [
            'password.required' => Lang::get('error_messages.req_password').'.',
            'password.min' => 'Password should be minimum 8 characters.',
            'password.max' => 'Password should be maximum 15 characters.',
            'password_confirmation.same' => Lang::get('error_messages.admin.same_confirm_password').'.',
            'password_confirmation.required' => Lang::get('error_messages.admin.req_confirm_password').'.',
            ]
        );

        /**
         * Checking Google Captcha is valid or not
         */
//        if ($this->captchaCheck($request) == false) {
//            return redirect()->back()->withErrors(['captcha' => 'Invalid captcha'])->withInput();
//        }

        $response = $this->broker()->reset(
            $this->credentials($request),
            function ($user, $password) {
            //dd($password);
            $this->resetPassword($user, $password);
        }
        );
        
       $response == Password::PASSWORD_RESET ? $this->sendResetResponse($response)
                    : $this->sendResetFailedResponse($request, $response);
        if ($response == "passwords.token") {
            $request->session()->flash('alert-danger',
                Lang::get('success_messages.ResetLinkExpired').'.');
            return redirect()->back()->withErrors(['resetError' => Lang::get('success_messages.ResetLinkExpired').'.'])->withInput();
        }
        if ($response == "passwords.reset") {

            //Mail to User
            $user    = User::getUserByemail($request->email);
            $arrData['user_id'] = $user->user_id;
            $arrData['token'] = $request->token;
            $arrData['is_reset'] = 1;
            $insert_res = ResetPasswordToken::saveUserTokenDetails($arrData);
            
            //template data
            if($insert_res)  {
                $userMailArr = [];
                $userMailArr['email'] = $user->email;
                $userMailArr['name'] = $user->f_name;
                Event::dispatch("RESET_PASSWORD_SUCCESSS", serialize($userMailArr));
                $request->session()->flash('alert-success',
                    Lang::get('success_messages.PasswordResetSuccessfully').'.');
                return view('auth.reset_success');
            }
        }
       
    }

    /**
     * 
     * @param type $user
     * @param type $password
     */
    protected function resetPassword($user, $password)
    {
        $user->forceFill([
            'password' => bcrypt($password),
            'remember_token' => Str::random(60),
        ])->save();

        //$this->guard()->login($user);
    }

    /**
     * 
     * @param type $response
     * @return type
     */
    protected function sendResetResponse($response)
    {
        return redirect($this->redirectPath())
                ->with('status', trans($response));
    }

    /**
     * Get the response for a failed password reset.
     *
     * @param  \Illuminate\Http\Request
     * @param  string  $response
     * @return \Illuminate\Http\RedirectResponse
     */
    protected function sendResetFailedResponse(Request $request, $response)
    {
        return redirect()->back()
                ->withInput($request->only('email'))
                ->withErrors(['email' => trans($response)]);
    }
}
