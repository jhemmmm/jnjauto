<?php

namespace App\Providers;

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
        // Share settings with layouts so nav/footer/sidebar always have dynamic data
        View::composer(['layouts.app', 'layouts.panel', 'auth.*'], function ($view) {
            $allSettings = settings();
            $allSettings['business_logo_url'] = ! empty($allSettings['business_logo'])
                ? asset('storage/'.$allSettings['business_logo'])
                : null;
            $view->with('settings', $allSettings);
        });
    }
}
