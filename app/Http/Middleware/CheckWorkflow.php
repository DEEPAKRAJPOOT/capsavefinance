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
        $user = \Auth::user();
        if ($user->user_type == '1') {
            $route_name = Helpers::getWfRedirectRoute($user->user_id);
            $appData = Helpers::getLatestAppData($user->user_id);
            if ($appData && !empty($route_name)) {
               //dd($route_name, $appData);
               return redirect()->route($route_name, ['app_id' =>  $appData->app_id, 'biz_id' => $appData->biz_id]);
            }else{
                return $next($request);
            }
        }

        return $next($request);
    }
}
