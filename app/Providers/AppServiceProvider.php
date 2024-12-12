<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Services\PrivateRelayService;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->singleton(PrivateRelayService::class, function ($app) {
            return new PrivateRelayService();
        });
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        if ($this->app->runningInConsole()) {
            $this->commands([
                \App\Console\Commands\UpdateRelayIPs::class,
            ]);
        }
    }
}
