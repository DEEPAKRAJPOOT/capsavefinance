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
        $wfData = Helpers::getWfRedirectRoute($user->user_id);            
        if (isset($wfData['redirect_url']) && !empty($wfData['redirect_url']) ) {
            return redirect($wfData['redirect_url']);                
        } else{
            return $next($request);
        }
        

        return $next($request);
    }
}
