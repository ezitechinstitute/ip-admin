<?php

use App\Http\Middleware\ValidUser;
use App\Http\Middleware\ValidManager; // English comments: Import the Manager middleware
use Illuminate\Foundation\Application;
use App\Http\Middleware\LocaleMiddleware;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__ . '/../routes/web.php',
        commands: __DIR__ . '/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
       
        $middleware->alias([
            'validUser' => ValidUser::class,
            'validManager' => ValidManager::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })->create();