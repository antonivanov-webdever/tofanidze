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

        // Safe as '*' only because PHP-FPM is never reachable except through
        // this chain: RU Origin's bare-metal edge nginx (docker/nginx/edge.conf.example,
        // resolves the real visitor IP via real_ip_module, scoped to the CDN's
        // ranges) → WireGuard-private link to the EU Exit server (docker/wireguard/*.conf.example)
        // → this Docker nginx there, reachable only on its WireGuard address
        // (docker/nginx/prod.conf, trusts the RU edge as its only possible
        // caller and passes its headers through unmodified) → PHP-FPM, not
        // exposed outside the docker network. If PHP-FPM is ever reachable
        // any other way, or any hop in that chain changes, this must be
        // scoped to specific trusted proxy IPs instead.
        $middleware->trustProxies(at: '*');
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();
