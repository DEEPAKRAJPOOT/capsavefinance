<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Inv\Repositories\Models\User;

class HttpSecure {

    /**
    * Handle an incoming HTTP request.
    *
    * @param \Illuminate\Http\Request $request
    * @param \Closure $next
    * @return mixed
    */
    public function handle(Request $request, Closure $next) {
        if ($request->getMethod() === "OPTIONS") {
            return response('');
        }
        $session_id = \Session::getId();
//        if (\Auth::check()) {
//            $user = User::find(\Auth::user()->user_id);
//            $lastSessionId = $user->session_id;
//            if ($lastSessionId != $session_id) {
//                \Auth::logout();
//                if (!empty($user) && ($user->user_type == config('common.USER_TYPE.FRONTEND'))) {
//                    return redirect()->route('login_open');
//                }
//               return redirect()->route('get_backend_login_open');
//            }
//        }

        $headers = ['Access-Control-Allow-Origin' => '*',
                    'Access-Control-Allow-Methods' =>  'GET, POST, PUT, DELETE',
                    'Cache-Control', 'no-cache, no-store, must-revalidate'
                   ];

        $response = $next($request);
        $IlluminateResponse = 'Illuminate\Http\Response';
        $SymfonyResopnse = 'Symfony\Component\HttpFoundation\Response';
        if($response instanceof $IlluminateResponse) {
            foreach ($headers as $key => $value) {
                $response->header($key, $value);
            }
            return $response;
        }

        if($response instanceof $SymfonyResopnse) {
            foreach ($headers as $key => $value) {
                $response->headers->set($key, $value);
            }
            return $response;
        }
        return response;
    }

}
