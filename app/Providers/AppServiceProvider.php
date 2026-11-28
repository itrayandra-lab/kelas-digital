<?php

namespace App\Providers;

use App\Http\View\Composers\CategoryComposer;
use App\Http\View\Composers\SettingsComposer;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Daftarkan composer untuk layout app
        View::composer('layouts.app', CategoryComposer::class);
        View::composer('layouts.app', SettingsComposer::class);

        // Implicitly grant "Super-Admin" role all permission checks using can()
        Gate::before(function ($user, $ability) {
            if ($user && $user->hasRole('Super-Admin')) {
                return true;
            }
        });

        RateLimiter::for('login', function (Request $request) {
            $key = strtolower($request->input('login')).'|'.$request->ip();

            return Limit::perMinute(5)->by($key);
        });

        RateLimiter::for('register', function (Request $request) {
            return Limit::perHour(2)->by($request->ip());
        });

        // RAYA chat: behind Cloudflare every visitor shares the edge IP, so key on the real client IP header when present.
        RateLimiter::for('raya', function (Request $request) {
            $ip = $request->header('CF-Connecting-IP') ?: $request->ip();

            return [
                Limit::perMinute(config('raya.limits.per_minute'))->by('m'.$ip),
                Limit::perDay(config('raya.limits.per_day'))->by('d'.$ip),
            ];
        });
    }
}
