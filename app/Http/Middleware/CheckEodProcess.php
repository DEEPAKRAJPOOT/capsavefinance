<?php

namespace App\Http\Middleware;

use Closure;
use App\Http\Middleware\Authorization\BaseAuthorization;

class CheckEodProcess extends BaseAuthorization
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
        $eodStatus = \Helpers::checkEodProcess();
        $data = ['eod_process' => $eodStatus];
        if ($request->ajax()) {
            if (in_array($request->route()->getName(), config('lms.EOD_PROCESS_ROUTES'))) {
                $response = $data + ['message' => trans('backend_messages.lms_eod_process_msg')];
                return response()->json($response);
            }
        } else {
            if (in_array($request->route()->getName(), config('lms.EOD_PROCESS_ROUTES')) && $eodStatus) {
                \Session::flash('message', trans('backend_messages.lms_eod_process_msg'));
            }
            $request->request->add($data);
        }
        return $next($request);
    }
}
