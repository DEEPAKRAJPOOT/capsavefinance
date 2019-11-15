<?php

namespace App\Http\Middleware;

use Closure;
use Session;
use Illuminate\Contracts\Auth\Guard;
use Illuminate\Support\Facades\Input;
use Auth;
use App\Inv\Repositories\Models\User as UserModel;

class AdminAuthenticate {

    /**
     * The Guard implementation.
     *
     * @var Guard
     */
    protected $auth;

    /**
     * Route name excludes from redirection
     *
     * @var array
     */
    protected $excluded_routes = [
    ];

    /**
     * Create a new filter instance.
     *
     * @param  Guard  $auth
     * @return void
     */
    public function __construct(Guard $auth) {
        $this->auth = $auth;
    }

    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next) {

//        if ($this->auth->guest()) {
//            if ($request->ajax()) {
//                return response('Unauthorized.', 401);
//            } else {
//                return redirect()->route('get_backend_login_open');
//            }
//        }
        //dd(Auth::user());
        //if(Auth::user())
      //  $result = UserModel::where('email', $request->get('email'))->first();
       // dd(Auth::user()->user_type);
        if (Auth::user()) {
            if (Auth::user()->user_type == '2') {
                return $next($request);
            } else {
                Auth::logout();
                return redirect()->route('get_backend_login_open');
            }
        } else {
            Auth::logout();
            return redirect()->route('get_backend_login_open');
        }

        //Auth::logout();
        //session_destroy();
    }

}
