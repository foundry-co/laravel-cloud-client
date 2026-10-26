<?php

namespace FoundryCo\LaravelCloudClient;

use Illuminate\Support\ServiceProvider;

class CloudServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->mergeConfigFrom(__DIR__.'/../config/cloud.php', 'cloud');

        $this->app->singleton(CloudClient::class, function ($app) {
            return new CloudClient(
                token: $app['config']['cloud.token'] ?? '',
            );
        });

        $this->app->alias(CloudClient::class, 'cloud');
    }

    public function boot(): void
    {
        if ($this->app->runningInConsole()) {
            $this->publishes([
                __DIR__.'/../config/cloud.php' => config_path('cloud.php'),
            ], 'cloud-config');
        }
    }
}
