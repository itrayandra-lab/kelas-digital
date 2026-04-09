<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Gate;
use App\Models\ArticleCategory;
use App\Http\View\Composers\CategoryComposer;
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
        View::composer('*', function ($view) {
            $view->with('articleCategories', ArticleCategory::all());
        });
        
        // Implicitly grant "Super-Admin" role all permission checks using can()
        Gate::before(function ($user, $ability) {
            if ($user && $user->hasRole('Super-Admin')) {
                return true;
            }
        });
    }
}
