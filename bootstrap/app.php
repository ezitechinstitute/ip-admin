<?php

use App\Http\Middleware\ValidUser;
use App\Http\Middleware\ValidManager; // English comments: Import the Manager middleware
use App\Http\Middleware\ValidIntern; // Import the Intern middleware
use Illuminate\Foundation\Application;
use App\Http\Middleware\LocaleMiddleware;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        channels: __DIR__.'/../routes/channels.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->alias([
            'validSupervisor' => \App\Http\Middleware\ValidSupervisor::class,
            'validUser' => \App\Http\Middleware\ValidUser::class,
            'validManager' => \App\Http\Middleware\ValidManager::class,
            'validIntern' => \App\Http\Middleware\ValidIntern::class,
        ]);
    })
    // 🔥 ADD THIS SECTION BELOW
    ->withBroadcasting(
        '/broadcasting/auth',
        ['middleware' => ['web', 'auth:admin,manager,intern']],
    )
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })->create();