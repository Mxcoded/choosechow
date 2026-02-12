<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__ . '/../routes/web.php',
        commands: __DIR__ . '/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        
        // 1. GLOBAL MIDDLEWARE (Runs on every web request)
        // This ensures blocked users are kicked out immediately
        $middleware->web(append: [
            \App\Http\Middleware\CheckBanned::class,
        ]);

        // 2. MIDDLEWARE ALIASES
        $middleware->alias([
            // --- Custom Middleware ---
            'chef' => \App\Http\Middleware\ChefMiddleware::class,
            'customer' => \App\Http\Middleware\CustomerMiddleware::class,
            'admin' => \App\Http\Middleware\AdminMiddleware::class,

            // --- Spatie Permission Middleware ---
            'role' => \Spatie\Permission\Middleware\RoleMiddleware::class,
            'permission' => \Spatie\Permission\Middleware\PermissionMiddleware::class,
            'role_or_permission' => \Spatie\Permission\Middleware\RoleOrPermissionMiddleware::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        // Handle authorization failures from Spatie middleware
        $exceptions->render(function (\Illuminate\Auth\Access\AuthorizationException $e, $request) {
            if ($request->expectsJson()) {
                return response()->json(['message' => 'Unauthorized'], 403);
            }
            
            // Create a view at resources/views/errors/403.blade.php to show a nice error page
            return response()->view('errors.403', [], 403);
        });
    })->create();