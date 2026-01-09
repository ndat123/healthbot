<?php

namespace App\Http\Middleware;

use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken as Middleware;
use Closure;

class VerifyCsrfToken extends Middleware
{
    /**
     * The URIs that should be excluded from CSRF verification.
     *
     * @var array<int, string>
     */
    protected $except = [
        '/login',
        '/register',
        '/forgot-password',
        '/reset-password',
        '/health-plans/generate',
        '/nutrition/generate',
    ];

    /**
     * Completely disable CSRF verification (temporary workaround).
     * WARNING: This removes CSRF protection for all routes.
     */
    public function handle($request, Closure $next)
    {
        return $next($request);
    }
}


