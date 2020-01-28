<?php

namespace App\Http\Middleware;

use Closure;

class SetupSession
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $domain = $request->server('HTTP_HOST');
        $path = $request->path();
        
        switch ($domain) {
            case config('proin.frontend_uri'):
                config(['session.cookie' => config('proin.frontend_cookie_name')]);
                config(['session.domain' => null]);
                break;
            case config('proin.backend_uri'):
                config(['session.cookie' => config('proin.backend_cookie_name')]);
                config(['session.domain' => null]);
                break;
            case config('proin.frontend_api_uri'):
                if (strpos($path, 'api/') === 0) {
                    config(['session.driver' => 'array']);
                }
                break;
            default:
                abort(400);
                // No need to break here
        }
        
        return $next($request);
    }
}
