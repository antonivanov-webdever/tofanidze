<?php

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
        $middleware->redirectGuestsTo(fn (Request $request) => route('admin.login'));
        $middleware->redirectUsersTo(fn (Request $request) => route('admin.dashboard'));

        // Safe as '*' only because nginx is the sole proxy PHP-FPM ever talks to
        // (not exposed outside the docker network) and, in production, nginx
        // itself overwrites X-Forwarded-For/-Proto with its own real_ip-resolved
        // values before this ever runs — see docker/nginx/prod.conf. If PHP-FPM
        // is ever reachable from anywhere else, or that header rewrite is
        // removed, this must be scoped to specific trusted proxy IPs instead.
        $middleware->trustProxies(at: '*');
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();
