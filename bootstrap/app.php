<?php

use App\Http\Middleware\EnsurePortalRole;
use App\Http\Middleware\EnsureStaffTerminalUnlocked;
use App\Http\Middleware\EnsureStaffUser;
use App\Http\Middleware\RecordApiRequest;
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
        $middleware->alias([
            'staff' => EnsureStaffUser::class,
            'staff.unlocked' => EnsureStaffTerminalUnlocked::class,
            'portal' => EnsurePortalRole::class,
            'api.metrics' => RecordApiRequest::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })->create();
