<?php

use App\Http\Middleware\CheckRole;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        // CheckRole adalah satu-satunya middleware guard yang diperlukan.
        // Gunakan di routes sebagai: middleware('role:guru'), middleware('role:siswa'), middleware('role:superadmin')
        $middleware->alias([
            'role' => CheckRole::class,
        ]);

        $middleware->redirectGuestsTo(function (Request $request) {
            return $request->is('portal*') ? route('siswa.login') : route('admin.login');
        });
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        $exceptions->shouldRenderJsonWhen(
            fn (Request $request) => $request->is('api/*') || $request->expectsJson(),
        );
    })->create();
