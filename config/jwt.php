<?php 

return [
    'secret' => env('JWT_SECRET', '12345678'),
    'algorithm' => env('JWT_ALGORITHM', 'HS256'),
    'ttl' => env('JWT_TTL', 60), // Time to live in minutes
    'refresh_ttl' => env('JWT_REFRESH_TTL', 20160), // Refresh token time to live in minutes
    'blacklist_grace_period' => env('JWT_BLACKLIST_GRACE_PERIOD', 0), // Grace period for blacklisted tokens in minutes
    'blacklist_enabled' => env('JWT_BLACKLIST_ENABLED', true), // Enable or disable token blacklist
];