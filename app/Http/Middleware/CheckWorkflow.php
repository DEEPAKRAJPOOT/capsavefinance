<?php

namespace App\Http\Middleware;

use Closure;
use Helpers;
use App\Http\Middleware\Authorization\BaseAuthorization;

class CheckWorkflow extends BaseAuthorization
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
        if (\Auth::user()->user_type == '1' && $request->has('app_id')) {
            $route_name = Helpers::getWfRedirectRoute($request->get('app_id'));
            if ($request) {
               redirect()->route($route_name,['app_id' =>  $request->get('app_id'), 'biz_id' => $request->get('biz_id')]);
            }
        }

        return $next($request);
    }
}
