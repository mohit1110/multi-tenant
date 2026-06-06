<?php

namespace App\Providers;

use App\Services\TenantManager;
use App\Services\TenantService;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        // Register TenantManager as a singleton
        $this->app->singleton(TenantManager::class, function () {
            return new TenantManager();
        });

        // Register TenantService
        $this->app->bind(TenantService::class, function ($app) {
            return new TenantService();
        });
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
