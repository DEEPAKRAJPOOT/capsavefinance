<?php

namespace App\Http\Controllers\Backend\Auth;

use Session;
use Clouser;
use Redirect;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use App\Inv\Repositories\Contracts\UserInterface as InvUserRepoInterface;

class LoginController extends Controller
{
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
     * Where to redirect users after login.
     *
     * @var string
     */
    protected $redirectTo = '/dashboard';

    /**
     * User repository
     *
     * @var object
     */
    protected $userRepo;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct(InvUserRepoInterface $user)
    {
        $this->middleware('guest')->except('logout');
        $this->userRepo = $user;
       // dd($this->userRepo->getBackendUsers(1));
    }

    /**
     * Show the application's login form.
     *
     * @return \Illuminate\Http\Response
     */
    public function showLoginForm()
    {
        return view('backend.auth.login');
    }

    /**
     * Handle a login request to the application.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Http\Response|\Illuminate\Http\JsonResponse
     */
    public function login(Request $request)
    {
        //$this->validateLogin($request);

        // If the class is using the ThrottlesLogins trait, we can automatically throttle
        // the login attempts for this application. We'll key this by the username and
        // the IP address of the client making these requests into this application.
        if ($this->hasTooManyLoginAttempts($request)) {
            $this->fireLockoutEvent($request);

            return $this->sendLockoutResponse($request);
        }
       
        $userEmail    = $request['email'];
        $userInfo = $this->userRepo->getUserByEmail($userEmail);
                
        if (!empty($userInfo)) {            
            // Checking User is frontend user            
            if ($this->isFrontendUser($userInfo)) {                
                Session::flash('messages', trans('error_messages.creadential_not_valid'));                
                return redirect()->route('get_backend_login_open');
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
    }

    /**
     * Validate the user login request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return void
     */
    protected function validateLogin(Request $request)
    {
        $this->validate($request,
            [
            $this->username() => 'required|string|email',
            'password' => 'required|string',
            ],
            [
            $this->username().".required" => trans('error_messages.req_email'),
            $this->username().".email" => trans('error_messages.invalid_email'),
            'password.required' => trans('error_messages.req_password')
            ]
        );
    }
    public function logout(Request $request)
    {
        $this->guard()->logout();

        $request->session()->invalidate();

        return $this->loggedOut($request) ?: redirect('/');
    }
    
    protected function authenticated(Request $request, $user) {
         
         $domain = $request->route()->domain();
          if (\Auth::check()) {
               $user_type = \Auth::user()->user_type;
            if (config('proin.frontend_uri') === $domain && $user_type==1) {
                return redirect('front_dashboard');
            } elseif (config('proin.backend_uri') === $domain && $user_type==2) {
                $user = $this->userRepo->getBackendUser(\Auth::user()->user_id);
                if (isset($user->redirect_path) && $user->redirect_path != '') {
                    return redirect($user->redirect_path);
                } elseif (!empty(request()->get('rtoken'))) {
                    try {
                        $decryptData = Crypt::decrypt(request()->get('rtoken'));
                        $decryptData = explode("#", $decryptData);
                        return redirect(route('case_detail', ['user_id' => $decryptData[0], 'app_id' => $decryptData[1]]));
                    } catch (Exception $ex) {
                        throw new DecryptException($ex->getMessage());
                    }
                }

                return redirect(route('backend_dashboard'));
            }
        }
   
    }
    
    /**
     * Check If user is front-end user or not
     *
     * @param object $user
     * @return boolean     
     */
    protected function isFrontendUser($user) {                
        if (!empty($user) && ($user->user_type == config('common.USER_TYPE.FRONTEND'))) {   // || $user->user_type == 2
            return true;
        }
        return false;
    }    
}