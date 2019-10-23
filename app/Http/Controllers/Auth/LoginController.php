<?php

namespace App\Http\Controllers\Auth;

use Auth;
use Helpers;
use Session;
use Redirect;
use Socialite;
use Illuminate\Http\Request;
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
    protected $redirectTo = '/dashboard';

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
    public function login(Request $request) {
        try {
            //Validation for request
            // $this->validateLogin($request);
            //echo "debug"; exit;
            // If the class is using the ThrottlesLogins trait, we can automatically throttle
            // the login attempts for this application. We'll key this by the username and
            // the IP address of the client making these requests into this application.
            if ($this->hasTooManyLoginAttempts($request)) {
//                die("we");
                $this->fireLockoutEvent($request);
                dd($this->sendLockoutResponse($request));
                return $this->sendLockoutResponse($request);
            }
            $userEmail = $request['email'];

            $userInfo = $this->userRepo->getUserByEmail($userEmail);

            if (empty($userInfo)) {
                //Checking User is frontend user
                if (!$this->isFrontendUser($userInfo)) {

                    Session::flash('messages', trans('error_messages.creadential_not_valid'));
                    return redirect()->route('login_open');
                }
                //Checking User Active Status
                if ($this->isAccountBlocked($userInfo)) {
                    return Redirect::back()->withErrors(trans('error_messages.account_blocked'));
                }
            }
            if ($this->attemptLogin($request)) {

                return $this->sendLoginResponse($request);
            }
            // If the login attempt was unsuccessful we will increment the number of attempts
            // to login and redirect the user back to the login form. Of course, when this
            // user surpasses their maximum number of attempts they will get locked out.
            $this->incrementLoginAttempts($request);
            return $this->sendFailedLoginResponse($request);

            //return Redirect::route('dashboard');
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
