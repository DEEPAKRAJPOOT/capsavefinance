<?php

namespace App\Http\Middleware;

use Auth;
use Closure;
use App\B2c\Libraries\CmLogger;
use Illuminate\Contracts\Auth\Guard;

class Authenticate
{

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
        'backend_logout',
        'login_password_reset',
        'login_password_update',
    ];

    /**
     * Create a new filter instance.
     *
     * @param  Guard  $auth
     * @return void
     */
    public function __construct(Guard $auth)
    {
        $this->auth = $auth;
    }

    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        if ($this->auth->guest()) {
            if ($request->ajax()) {
                return response('Unauthorized.', 401);
            } else {
                return redirect()->guest('/');
            }
        }

        // Add our Case Manager Activity tracking here
        $domain = $request->server('HTTP_HOST');
        $route = $request->route()->getName();

        if ($domain == config('b2cin.backend_uri')) {
            if ($this->passwordResetOnFirstLogin() && !in_array($route, $this->excluded_routes)) {
                return redirect(route('login_password_reset'));
            }
            (new CmLogger())->handle($request);
        }

        return $next($request);
    }

    /**
     * Returns whether a use is required to update his/her password
     *
     * @return boolean
     */
    protected function passwordResetOnFirstLogin()
    {
        return (Auth::user()->is_password_set_onlogin === null);
    }
}
