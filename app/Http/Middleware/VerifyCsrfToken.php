<?php

namespace App\Http\Middleware;

use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken as Middleware;

class VerifyCsrfToken extends Middleware
{
    /**
     * The URIs that should be excluded from CSRF verification.
     *
     * @var array
     */
    protected $except = [
        'api/perfios/fsa-callback','api/perfios/bsa-callback','api/karza/webhook', 'api/tally/entry', 'login-lenevo', 'logout', 'register-lenevo'
    ];
}
