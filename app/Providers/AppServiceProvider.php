<?php

namespace App\Providers;

use App\Services\AttestationPdfService;
use App\Settings\GeneralSettings;
use App\Settings\IdentificationSettings;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->singleton(AttestationPdfService::class, function ($app) {
            return new AttestationPdfService();
        });
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
