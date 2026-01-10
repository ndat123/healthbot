<?php

use App\Http\Middleware\TrustProxies;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        // Security: Trust Proxies
        // $middleware->trustProxies(at: '*', headers: 
        //     \Illuminate\Http\Request::HEADER_X_FORWARDED_FOR |
        //     \Illuminate\Http\Request::HEADER_X_FORWARDED_HOST |
        //     \Illuminate\Http\Request::HEADER_X_FORWARDED_PORT |
        //     \Illuminate\Http\Request::HEADER_X_FORWARDED_PROTO |
        //     \Illuminate\Http\Request::HEADER_X_FORWARDED_AWS_ELB
        // );

        // Security: CSRF Protection
        // Disable CSRF validation for all routes to avoid 419 errors on Railway
        // (temporary workaround, not recommended for production security).
        // $middleware->validateCsrfTokens(except: ['*']);
        
        // System: Optimize middleware performance
        // $middleware->statefulApi();
        // $middleware->throttleApi();
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();
