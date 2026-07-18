<?php

namespace App\Http\Middleware;

use Illuminate\Foundation\Http\Middleware\ValidateCsrfToken as Middleware;

class VerifyCsrfToken extends Middleware
{
    /**
     * Routes exempt from CSRF (stateless public endpoints only).
     *
     * @var array<int, string>
     */
    protected $except = [
        // Free tryout is a public anonymous flow.
        'api/free-tryout/*',
    ];
}
