<?php

use App\Http\Middleware\PermissionMiddleware;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;
use App\Http\Middleware\AuditMiddleware;
use App\Http\Middleware\SetLocaleFromHeader;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        channels: __DIR__.'/../routes/channels.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {

        $middleware->alias([

            'permission' => PermissionMiddleware::class,
            'audit'      => AuditMiddleware::class,

        ]);

        // Apply audit logging automatically to all authenticated API requests
        $middleware->appendToGroup('api', AuditMiddleware::class);

        // Set app locale from Accept-Language header on every API request
        $middleware->prependToGroup('api', SetLocaleFromHeader::class);

    })
    ->withExceptions(function (Exceptions $exceptions): void {
        $exceptions->shouldRenderJsonWhen(
            fn (Request $request) => $request->is('api/*'),
        );

        // Prevent the auth guard from redirecting to a 'login' route
        // (which doesn't exist in this API-only app) when a user is unauthenticated.
        $exceptions->renderable(function (\Illuminate\Auth\AuthenticationException $e, Request $request) {
            if ($request->is('api/*') || $request->expectsJson()) {
                return response()->json(['message' => 'Unauthenticated.'], 401);
            }
        });
    })
    ->create();
