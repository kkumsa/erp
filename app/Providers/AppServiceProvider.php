<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\URL;

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
        // HTTPS 강제 (프로덕션 환경 또는 프록시 뒤에서)
        if ($this->app->environment('production') || 
            request()->header('X-Forwarded-Proto') === 'https' ||
            str_starts_with(config('app.url'), 'https://')) {
            URL::forceScheme('https');
        }

        // Super Admin은 모든 권한을 가짐
        Gate::before(function ($user, $ability) {
            return $user->hasRole('Super Admin') ? true : null;
        });
    }
}
