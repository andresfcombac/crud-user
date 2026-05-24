<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        // --- AQUÍ REGISTRAMOS TU MIDDLEWARE DE FORMA GLOBAL ---
        $middleware->web(append: [
            \App\Http\Middleware\NoGuardarCache::class,
            \App\Http\Middleware\TimeoutInactividad::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })->create();
