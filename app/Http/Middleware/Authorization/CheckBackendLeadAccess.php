<?php

namespace App\Http\Middleware\Authorization;

use Closure;
use App\Http\Middleware\Authorization\BaseAuthorization;

class CheckBackendLeadAccess extends BaseAuthorization
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
        if ($request->ajax()) {
            if ($this->gate->denies($request->route()->getName())) {
            //if ($request->route()->getName() == 'update_invoice_approve') {
                $response = ['access_denied' => 1];
                return response()->json($response);
            }
        } else { 
            if ($this->gate->denies($request->route()->getName())) {
                return response()->view('errors.403', [], 403);
            }
        }
        
        if ($request->has('app_id')) {
            $isViewOnly = \Helpers::isAccessViewOnly($request->get('app_id'));
            $request->request->add(['view_only' => $isViewOnly]);
        }
        return $next($request);
    }
}
