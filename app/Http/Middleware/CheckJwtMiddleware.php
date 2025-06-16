<?php

namespace App\Http\Middleware;

use App\Exceptions\InvalidTokenException;
use App\Exceptions\TokenNotProvidedException;
use App\Exceptions\UserNotFound;
use App\Models\User;
use App\Services\Token\TokenService;
use App\Trait\FindUserTrait;
use Closure;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckJwtMiddleware
{
    use FindUserTrait;

    public function __construct(private TokenService $tokenService)
    {
        // Constructor logic if needed
    }
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
       try {
            $token = $request->bearerToken();

            if (! $token) {
                throw new TokenNotProvidedException;
            }

            $decoded = $this->tokenService->decodeToken($token);

            if (! $decoded) {
                throw new InvalidTokenException;
            }
            $user = $this->findUserById($decoded->sub);
              $request->attributes->set('token', $token);
            $request->attributes->set('user', $user);
        
           // $request->attributes->set('token', $token);
         

            return $next($request);

        } catch (InvalidTokenException|TokenNotProvidedException|UserNotFound  $e) {
            throw new HttpResponseException(response()->json(['message' => $e->getMessage()], $e->getCode()));
        }

    }
}
