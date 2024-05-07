<?php

namespace Fintech\RestApi;

use Fintech\RestApi\Commands\InstallCommand;
use Fintech\RestApi\Commands\RestApiCommand;
use Illuminate\Support\ServiceProvider;

class RestApiServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        $this->mergeConfigFrom(
            __DIR__.'/../config/restapi.php', 'fintech.restapi'
        );

        $this->app->register(RouteServiceProvider::class);
        $this->app->register(RepositoryServiceProvider::class);
    }

    /**
     * Bootstrap any package services.
     */
    public function boot(): void
    {
        $this->publishes([
            __DIR__.'/../config/restapi.php' => config_path('fintech/restapi.php'),
        ]);

        $this->loadMigrationsFrom(__DIR__.'/../database/migrations');

        $this->loadTranslationsFrom(__DIR__.'/../lang', 'restapi');

        $this->publishes([
            __DIR__.'/../lang' => $this->app->langPath('vendor/restapi'),
        ]);

        $this->loadViewsFrom(__DIR__.'/../resources/views', 'restapi');

        $this->publishes([
            __DIR__.'/../resources/views' => resource_path('views/vendor/restapi'),
        ]);

        if ($this->app->runningInConsole()) {
            $this->commands([
                InstallCommand::class,
                RestApiCommand::class,
            ]);
        }
    }
}
