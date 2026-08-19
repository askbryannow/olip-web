<?php

use App\Http\Middleware\RequireAdminSession;
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
        $middleware->alias([
            'admin.session' => RequireAdminSession::class,
        ]);

        // Two separate logins live in this app: the shared admin password (routes/web.php's
        // "admin.session" group) and the per-employee BLIP portal (the "employee" guard below).
        // An unauthenticated visit to a /portal route must bounce to the portal login, not the admin one.
        $middleware->redirectGuestsTo(
            fn (Request $request) => $request->is('portal*') ? route('portal.login') : route('login'),
        );
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        $exceptions->shouldRenderJsonWhen(
            fn (Request $request) => $request->is('api/*') || $request->expectsJson(),
        );
    })->create();
