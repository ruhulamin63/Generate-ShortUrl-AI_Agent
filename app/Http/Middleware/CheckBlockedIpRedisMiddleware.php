<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Symfony\Component\HttpFoundation\Response;

class CheckBlockedIpRedisMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {

        $ip = $request->ip();
        if (Cache::has("blocked_ip_{$ip}")) {
            throw new HttpResponseException(response(['message' => 'You IP is blocked. Please try again later'], 423));
        }
        return $next($request);
    }
}
