<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Exceptions\ThrottleRequestsException;
use Illuminate\Support\Facades\Cache;
use Inspector\Laravel\Middleware\WebRequestMonitoring;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
        api: __DIR__.'/../routes/api/v1.php',
    )
    ->withMiddleware(function (Middleware $middleware) {
          $middleware->alias([
            'role' => \Spatie\Permission\Middleware\RoleMiddleware::class,
            'permission' => \Spatie\Permission\Middleware\PermissionMiddleware::class,
            'role_or_permission' => \Spatie\Permission\Middleware\RoleOrPermissionMiddleware::class,
            'jwt.auth' => \App\Http\Middleware\CheckJwtMiddleware::class,
            'check.blocked.ip' => \App\Http\Middleware\CheckBlockedIpRedisMiddleware::class,
            'check.redis.token' => \App\Http\Middleware\CheckRedisTokenMiddleware::class,
        ]);
        $middleware->appendToGroup('web', WebRequestMonitoring::class)
            ->appendToGroup('api', WebRequestMonitoring::class);
    })
    ->withExceptions(function (Exceptions $exceptions) {
         $exceptions->render(function (ThrottleRequestsException $e, $request) {
            $ip = $request->ip();
            Cache::put("blocked_ip_{$ip}", true, now()->addMinutes(30));
            return response(['message' => 'Too Many Attempts. Locked for 30 minutes, try again later'], 429);
        });
    })->create();
