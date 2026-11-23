<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use App\Http\Middleware\EnsureAdmin;
use App\Http\Middleware\RequireKioskSite;
use App\Http\Middleware\RestoreKioskSite;


return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->alias([
            'admin' => EnsureAdmin::class,
            'kiosk.site' => RequireKioskSite::class,
        ]);

        $middleware->web(append: [RestoreKioskSite::class]);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })->create();

    
