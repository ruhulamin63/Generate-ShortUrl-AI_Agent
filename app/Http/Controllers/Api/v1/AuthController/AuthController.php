<?php

namespace App\Http\Controllers\Api\v1\AuthController;

use App\Exceptions\InvalidCredentialsException;
use App\Http\Controllers\Controller;
use App\Http\Requests\LoginFormRequest;
use App\Services\Auth\AuthService;
use Illuminate\Http\Exceptions\HttpResponseException;

class AuthController  extends Controller
{
    public function __construct(private AuthService $authService)
    {
        // Constructor injection of AuthService
    }


    public function __invoke(LoginFormRequest $request)
    {
        $email = $request->input('email');
        $password = $request->input('password');
        try {
            $token = $this->authService->login($email, $password);
            return response()->json(['token' => $token]);
        } catch (InvalidCredentialsException $e) {
            throw new HttpResponseException(response()->json(['message' => $e->getMessage()], $e->getCode()));
        }
    }
}
