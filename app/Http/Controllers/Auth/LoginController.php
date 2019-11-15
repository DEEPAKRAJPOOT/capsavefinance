<?php

namespace App\Http\Controllers\Auth;

use Auth;
use Helpers;
use Session;
use Redirect;
use Socialite;
use Illuminate\Http\Request;
use App\Http\Requests\LoginRequest;
use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use App\Inv\Repositories\Contracts\UserInterface as InvUserRepoInterface;

class LoginController extends Controller {
    /*
      |--------------------------------------------------------------------------
      | Login Controller
      |--------------------------------------------------------------------------
      |
      | This controller handles authenticating users for the application and
      | redirecting them to your home screen. The controller uses a trait
      | to conveniently provide its functionality to your applications.
      |
     */

use AuthenticatesUsers;

    /**
     * User repository
     *
     * @var object
     */
    protected $userRepo;

    /**
     * Where to redirect users after login.
     *
     * @var string
     */
    //protected $redirectTo = '/dashboard';
    protected $redirectTo = '/business-information';

    /**
     * Multiple times user login blocking.  
     *
     * @var value
     */
    protected $maxAttempts = 3; // Default is 5
    protected $decayMinutes = 2; // Default is 1

    /**
     * Create a new controller instance.
     *
     * @return void
     */

    public function __construct(InvUserRepoInterface $user) {
        $this->middleware('guest')->except('logout');
        $this->userRepo = $user;
    }

    /**
     * Handle a login request to the application.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Http\Response|\Illuminate\Http\JsonResponse
     */
    public function login(LoginRequest $request) {

        try {
            // Too many attempts blocking user  
            if ($this->hasTooManyLoginAttempts($request)) {
                $this->fireLockoutEvent($request);
                return $this->sendLockoutResponse($request);
            }
            $userEmail = $request['email'];

            $userInfo = $this->userRepo->getUserByEmail($userEmail);

            if (empty($userInfo)) {
                // Checking User is frontend user
                if (!$this->isFrontendUser($userInfo)) {

                    Session::flash('messages', trans('error_messages.creadential_not_valid'));
                    return redirect()->route('login_open');
                }
            }
            
            // Email verification with OTP
            if (!$this->isEmailVerify($userInfo)) {
                Session::flash('messages', trans('error_messages.login_verify_email'));
                return redirect()->route('login_open');
            }

            if ($this->attemptLogin($request)) {
                return $this->sendLoginResponse($request);
            }  
            // validate user OTP verified or not
            if (!$this->isOtpVerify($userInfo)) {
                Session::flash('messages', trans('error_messages.login_verify_otp'));
                return redirect()->route('login_open');
            }

            
            // If the login attempt was unsuccessful we will increment the number of attempts
            // to login and redirect the user back to the login form. Of course, when this
            // user surpasses their maximum number of attempts they will get locked out.

            $this->incrementLoginAttempts($request);

            return $this->sendFailedLoginResponse($request);

        } catch (Exception $ex) {
            return redirect()->back()->withErrors(Helpers::getExceptionMessage($ex));
        }
    }

    /**
     * Validate the user login request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return void
     */
    protected function validateLogin(Request $request) {
        $this->validate($request, [
            $this->username() => 'required|string',
            'password' => 'required|string',
                ], [
            $this->username() . ".required" => trans('error_messages.req_user_name'),
            'password.required' => trans('error_messages.req_password')
                ]
        );
    }

    /**
     * Log the user out of the application.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function logout(Request $request) {
        $this->guard()->logout();

        $request->session()->invalidate();

        return redirect('/login');
    }

    /**
     * Check If retailer account is blocked
     *
     * @param object $request
     * @param object $user
     * @return boolean
     * @auther Harish
     */
    protected function isAccountBlocked($user) {
        if (!empty($user) && $user->status == config('inv_common.USER_STATUS.Block')) {
            return true;
        }
        return false;
    }

    /**
     * Check If Login account is not verify email
     *
     * @param object $request
     * @param object $user
     * @return boolean
     * @auther Gajendra chauhan
     */
    protected function isEmailVerify($user) {
        if (!empty($user) && ($user->is_email_verified == 1)) {
            return true;
        }
        return false;
    }

    /**
     * Check If Login account is not verify Otp
     *
     * @param object $request
     * @param object $user
     * @return boolean
     * @auther Gajendra chauhan
     */
    protected function isOtpVerify($user) {
        if (!empty($user) && ($user->is_otp_verified == 1)) {
            return true;
        }
        return false;
    }

    /**
     * Check If user is front-end user or not
     *
     * @param object $user
     * @return boolean
     * @auther Harish
     */
    protected function isFrontendUser($user) {
        if (!empty($user) && ($user->user_type == config('inv_common.USER_TYPE.FRONTEND') || $user->user_type == 2)) {
            return true;
        }
        return false;
    }

    /**
     * Redirect the user to the google authentication page.
     *
     * @return Response
     */
    public function redirectToProvider($provider) {
        return Socialite::driver($provider)->redirect();
    }

    public function welcomePage() {
        Session::forget('uId');
        Session::forget('rId');
        Session::forget('go_on_right');
        return view('welcome');
    }

}
