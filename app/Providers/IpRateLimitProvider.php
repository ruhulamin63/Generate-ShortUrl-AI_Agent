<?php

namespace App\Providers;

use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\ServiceProvider;

class IpRateLimitProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
            RateLimiter::for('global', function (Request $request){
        return Limit::perMinute(60)->by($request->ip());
    });

    RateLimiter::for('login', function (Request $request){
        return Limit::perMinute(4)->by($request->ip());
    });
    }
}
