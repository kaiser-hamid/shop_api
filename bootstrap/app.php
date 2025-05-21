<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use App\Exceptions\ApiExceptionHandler;
use App\Http\Middleware\AuthSourceMiddleware;
use App\Http\Middleware\ApiAuthentication;
return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up' 
    )
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->alias([
            'auth.source' => AuthSourceMiddleware::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        $exceptions->reportable(function (Throwable $e) {
            //
        });
        
        $exceptions->renderable(function (Throwable $e) {
            return app(ApiExceptionHandler::class)->render(request(), $e);
        });
    })->create();
