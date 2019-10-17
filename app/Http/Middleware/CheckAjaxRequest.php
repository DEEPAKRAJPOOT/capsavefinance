<?php

namespace App\Http\Middleware;

use Closure;

class CheckAjaxRequest
{
    /**
     * Create Middleware to Check Ajax Request
     *
     */

    /**
     * Checks the access
     *
     * @param mixed $request
     * @param Closure $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {

        /**
         * First, check if it's an AJAX request or not
         */
        if (!$request->ajax() && strpos(php_sapi_name(), 'cli') === false) {
            abort(403, trans('httperrors.403'));
        }

        /**
         * Allowed routes for unauthenticated AJAX call
         */
        $allowed_open_route = [
        ];

        /**
         * Check itf it's a open call or authenticated call
         */
        $open_call = \in_array($request->segment(2), $allowed_open_route);

        /**
         * If either an open call or authenticated, pass thge request
         */
        if ($open_call || \Auth::check()) {
            return $next($request);
        }

        return response('Unauthorized.', 401);
    }
}