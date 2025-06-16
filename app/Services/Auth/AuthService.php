<?php 
namespace App\Services\Auth;

use App\Exceptions\InvalidCredentialsException;
use App\Models\User;
use App\Services\Token\TokenService;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Hash;

class AuthService{

    public function __construct(private TokenService $tokenService){}


    public function login($email, $password): string{
        $user = User::where('email', $email)->first();
        if (!$user || !Hash::check($password, $user->password)) {
            throw new InvalidCredentialsException();
        }
       $token = Cache::store('redis')->remember("user:{$user->id}:token", 3600, function () use ($user) {
            return $this->tokenService->generateToken($user);
        });
        return $token;
    }
}