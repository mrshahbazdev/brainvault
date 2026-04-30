<?php

namespace App\Providers;

use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        RateLimiter::for('api', function (Request $request) {
            $user = $request->user();
            $plan = $user?->plan ?? 'free';

            $maxAttempts = match ($plan) {
                'pro' => 300,
                'team', 'enterprise' => 600,
                default => 60,
            };

            return Limit::perMinute($maxAttempts)
                ->by($user?->id ?: $request->ip())
                ->response(function () use ($maxAttempts) {
                    return response()->json([
                        'message' => 'Too many requests. Upgrade your plan for higher limits.',
                        'limit' => $maxAttempts,
                    ], 429);
                });
        });
    }
}
