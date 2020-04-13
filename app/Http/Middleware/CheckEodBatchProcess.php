<?php

namespace App\Http\Middleware;

use Closure;
use App\Http\Middleware\Authorization\BaseAuthorization;

class CheckEodBatchProcess extends BaseAuthorization
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
        $data = ['eod_process' => \Helpers::checkEodBatchProcess()];
        if ($request->ajax()) {
            if (in_array($request->route()->getName(), config('lms.EOD_BATCH_PROCESS_ROUTES'))) {
                $response = $data + ['message' => trans('backend_messages.lms_eod_batch_process_msg')];
                return response()->json($response);
            }
        } else {
            $request->request->add($data);
        }
        return $next($request);
    }
}
