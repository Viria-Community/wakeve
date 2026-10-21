<?php

use App\Http\Middleware\EnsureAgencyIsOperational;
use App\Http\Middleware\EnsureCompanyIsOperational;
use App\Http\Middleware\EnsureUserIsActive;
use App\Http\Middleware\RequireHttps;
use App\Http\Middleware\SecurityHeaders;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->append(RequireHttps::class);
        $middleware->append(SecurityHeaders::class);

        $middleware->trustProxies(
            at: ['127.0.0.1', '::1'],
            headers: Request::HEADER_X_FORWARDED_PROTO | Request::HEADER_X_FORWARDED_PORT,
        );

        $middleware->alias([
            'active' => EnsureUserIsActive::class,
            'agency.operational' => EnsureAgencyIsOperational::class,
            'company.operational' => EnsureCompanyIsOperational::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        $exceptions->shouldRenderJsonWhen(
            fn (Request $request) => $request->is('api/*'),
        );
    })->create();
