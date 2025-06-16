<?php

declare(strict_types=1);

namespace App\Services\Token;

use App\Contracts\TokenServiceInterface;
use App\Models\User;
use Carbon\Carbon;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class TokenService implements TokenServiceInterface
{
    protected $secret;

    public function __construct()
    {
        $this->secret = config('jwt.secret');
    }

    /**
     * Summary of generateToken
     *
     * @param  mixed  $userId
     */
    public function generateToken(User $user): string
    {
        $rolesNames = $user->roles->pluck('name')->toArray();
        $payload = [
            'sub' => $user->id,
            'role' => $rolesNames,
            'iat' => time(),
            'exp' => time() + 3600, // Token válido por 30 minutos
            'jti' => bin2hex(random_bytes(16)), // Genera un ID único para el token
        ];

        return JWT::encode($payload, $this->secret, 'HS256');
    }

    /**
     * Summary of decodeToken
     *
     * @param  mixed  $token
     * @return \stdClass|null
     */
    public function decodeToken($token): ?object
    {
        try {
            $tokenDecoded = JWT::decode($token, new Key($this->secret, 'HS256'));

            return $tokenDecoded;
        } catch (\Exception $e) {
            return null;
        }
    }

    public function getJtiFromToken($token): mixed
    {
        try {
            $decoded = JWT::decode($token, new Key($this->secret, 'HS256'));

            return $decoded->jti;
        } catch (\Exception $e) {
            return null;
        }
    }

    public function generateRefreshToken($userId): string
    {
        $refreshToken = Str::random(60); // Genera un refresh token aleatorio

        // Establece la fecha de expiración para 1 día a partir de ahora
        $expiresAt = Carbon::now()->addDay();

        // Guarda el refresh token en la base de datos
        DB::table('jwt_refresh_tokens')->insert([
            'id' => Str::uuid(),
            'user_id' => $userId,
            'refresh_token' => $refreshToken,
            'expires_at' => $expiresAt,
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ]);

        return $refreshToken;
    }

    // Nueva función para validar el refresh token
    /**
     * Summary of validateRefreshToken
     *
     * @param  mixed  $userId
     * @param  mixed  $refreshToken
     */
    public function validateRefreshToken($userId, $refreshToken): bool
    {
        $record = DB::table('jwt_refresh_tokens')
            ->where('user_id', $userId)
            ->where('refresh_token', $refreshToken)
            ->first();

        if ($record && Carbon::now()->lessThanOrEqualTo($record->expires_at)) {
            return true;
        }

        return false;
    }

    /**
     * Summary of revokeRefreshToken
     *
     * @param  mixed  $userId
     * @param  mixed  $refreshToken
     */
    public function revokeRefreshToken($userId, $refreshToken): void
    {
        DB::table('jwt_refresh_tokens')
            ->where('user_id', $userId)
            ->where('refresh_token', $refreshToken)
            ->delete();
    }

    /**
     * Summary of revokeAllRefreshTokens
     *
     * @param  mixed  $userId
     */
    public function revokeAllRefreshTokens($userId): void
    {
        DB::table('jwt_refresh_tokens')
            ->where('user_id', $userId)
            ->delete();
    }
}
