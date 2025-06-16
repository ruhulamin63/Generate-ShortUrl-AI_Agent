<?php

namespace App\Http\Middleware;

use App\Exceptions\InvalidTokenException;
use Closure;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Symfony\Component\HttpFoundation\Response;

class CheckRedisTokenMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
     public function handle(Request $request, Closure $next)
    {
        try {
            $token = $request->attributes->get('token');

            $user = $request->attributes->get('user');

            // Check the token in Redis
            $cachedToken = Cache::store('redis')->get("user:{$user->id}:token");

            if (! $cachedToken || $cachedToken !== $token) {
                throw new InvalidTokenException;
            }

            auth()->setUser($user);

            return $next($request);

        } catch (InvalidTokenException  $e) {
            throw new HttpResponseException(response()->json(['message' => $e->getMessage()], $e->getCode()));
        }

    }
}
