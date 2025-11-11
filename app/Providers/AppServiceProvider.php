<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View; // Tambahkan ini
use Illuminate\Support\Facades\Gate; // Tambahkan untuk Super-Admin
use App\Http\View\Composers\CategoryComposer; // Tambahkan ini
use App\Http\View\Composers\SettingsComposer;

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
    }
}
