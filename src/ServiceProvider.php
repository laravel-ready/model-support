<?php

namespace LaravelReady\ModelSupport;

use Illuminate\Routing\Router;
use Illuminate\Support\ServiceProvider as BaseServiceProvider;

final class ServiceProvider extends BaseServiceProvider
{
    /**
     * Bootstrap of package services
     *
     * @return  void
     */
    public function boot(Router $router): void
    {
        $this->bootPublishes();
    }

    /**
     * Register any application services
     *
     * @return  void
     */
    public function register(): void
    {        // package config file
        $this->mergeConfigFrom(__DIR__ . '/../config/model-support.php', 'model-support');
    }

    /**
     * Publishes resources on boot
     *
     * @return  void
     */
    private function bootPublishes(): void
    {        // package configs
        $this->publishes([
            __DIR__ . '/../config/model-support.php' => $this->app->configPath('model-support.php'),
        ], 'model-support-config');
    }
}
