<?php

namespace Url\ProtectUrl\Middleware;

use Closure;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Auth;
use Url\ProtectUrl\UrlParamProtector;

class UrlParamRevealerMiddleware
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
        (Auth::guard()) && App::make(UrlParamProtector::class)->reveal($request);
        
        return $next($request);
    }
}
