<?php

namespace App\Exceptions;

use Helpers;
use Response;
use Exception;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Session\TokenMismatchException;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException;

class Handler extends ExceptionHandler
{
    /**
     * A list of the exception types that are not reported.
     *
     * @var array
     */
    protected $dontReport = [
        //
    ];

    /**
     * A list of the inputs that are never flashed for validation exceptions.
     *
     * @var array
     */
    protected $dontFlash = [
        'password',
        'password_confirmation'
    ];

    /**
     * Report or log an exception.
     *
     * This is a great spot to send exceptions to Sentry, Bugsnag, etc.
     *
     * @param  \Exception  $exception
     * @return void
     */
    public function report(Exception $exception)
    {
        parent::report($exception);
    }

    /**
     * Render an exception into an HTTP response.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Exception  $exception
     * @return \Illuminate\Http\Response
     */
    public function render($request, Exception $exception)
    {

        // Check whether site is down for maintenance or not
        $maintenanceMode = (bool) ($exception instanceof HttpException && $exception->getStatusCode() === 503);
        if ($exception instanceof TokenMismatchException) {
            return $this->handleTokenMismatch();
        }

        // create a validator and validate to throw a new ValidationException

        if ($exception instanceof \Symfony\Component\HttpFoundation\File\Exception\FileException) {
            return Validator::make($request->all(), [
                'doc_file' => 'required|file|size:5000000',
            ])->validate();
        }

        if (config('app.debug')) {
            if ($maintenanceMode) {
                return Response::view('errors.503', [], 503);
            } elseif ($exception instanceof TooManyRequestsHttpException) {
                return Response::view('errors.throttle', [], 429);
            } elseif ($exception instanceof NotFoundHttpException) {
                return Response::view('errors.404', [], 404);
            } elseif ($exception instanceof HttpException && $exception->getStatusCode() === 403) {
                return Response::view('errors.403', [], 403);
            } elseif ($exception instanceof HttpException && $exception->getStatusCode() === 400) {
                return redirect('/');
            } elseif ($exception instanceof HttpException && $exception->getStatusCode() === 401) {
                 return redirect('/');
            }elseif ($exception instanceof MethodNotAllowedHttpException) {
                return redirect('/');
            }
        }
        return parent::render($request, $exception);
    }

    /**
     * Convert an authentication exception into an unauthenticated response.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Illuminate\Auth\AuthenticationException  $exception
     * @return \Illuminate\Http\Response
     */
    protected function unauthenticated($request, AuthenticationException $exception)
    {
        if ($request->expectsJson()) {
            return response()->json(['error' => 'Unauthenticated.'], 401);
        }
        return redirect()->guest(route('login'));
    }

    /**
     * Handle Token Mismatch Exception
     *
     * @param void
     * @return \Illuminate\Http\Response
     */
    protected function handleTokenMismatch()
    {
        $isGuest = auth()->guest();
        $message  = $isGuest ? 'Please retry.' : 'Token mismatched. Please retry.';
        $httpCode = $isGuest ? 401 : 400;
        $request = request();
        if ($request->isJson() || $request->ajax()) {
            return Response::json([$message], $httpCode);
        }
        return redirect()->back()->withErrors([$message]);
    }
}