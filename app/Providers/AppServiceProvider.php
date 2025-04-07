<?php

namespace App\Providers;

use App\Settings\GeneralSettings;
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
        // Retrieve your settings from the database or config
        $settings = app(GeneralSettings::class);

        // Share the settings globally (e.g., to views)
        View::share(compact('settings'));
    }
}
