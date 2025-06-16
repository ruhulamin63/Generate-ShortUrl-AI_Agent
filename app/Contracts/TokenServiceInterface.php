<?php 
namespace App\Contracts;

use App\Models\User;

interface TokenServiceInterface
{
    public function generateToken(User $user): string;
    public function decodeToken($token);
    public function getJtiFromToken($token);
    public function generateRefreshToken($userId);
    public function validateRefreshToken($userId, $refreshToken);
    public function revokeRefreshToken($userId, $refreshToken);
    public function revokeAllRefreshTokens($userId);
}