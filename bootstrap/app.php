<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->alias([
            'admin' => \App\Http\Middleware\AdminMiddleware::class,
            'check.domain' => \App\Http\Middleware\CheckDomainMiddleware::class,
            'prevent.hotlinking' => \App\Http\Middleware\PreventHotlinking::class,
            'check.not.installed' => \App\Http\Middleware\CheckIfNotInstalled::class,
        ]);

        $middleware->append(\App\Http\Middleware\CheckIfInstalled::class);

        $middleware->validateCsrfTokens(except: [
            'install/*',
        ]);

        $middleware->throttleApi('60,1');
    })
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })->create();
