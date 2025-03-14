<?php

namespace Fintech\RestApi;

use Fintech\Core\Traits\Packages\RegisterPackageTrait;
use Fintech\RestApi\Commands\InstallCommand;
use Fintech\RestApi\Commands\RestApiCommand;
use Fintech\RestApi\Providers\RepositoryServiceProvider;
use Fintech\RestApi\Providers\RouteServiceProvider;
use Illuminate\Support\ServiceProvider;

class RestApiServiceProvider extends ServiceProvider
{
    use RegisterPackageTrait;

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        $this->packageCode = 'restapi';

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
        $this->injectOnConfig(null, 'Rest API');

        $this->loadMigrationsFrom(__DIR__.'/../database/migrations');

        $this->loadTranslationsFrom(__DIR__.'/../lang', 'restapi');

        $this->loadViewsFrom(__DIR__.'/../resources/views', 'restapi');

        if ($this->app->runningInConsole()) {
            $this->commands([
                InstallCommand::class,
                RestApiCommand::class,
            ]);
        }

        $this->loadPublishableOptions();
    }

    private function loadPublishableOptions(): void
    {
        $this->publishes([
            __DIR__.'/../config/restapi.php' => config_path('fintech/restapi.php'),
        ], 'restapi-config');

        $this->publishes([
            __DIR__.'/../lang' => $this->app->langPath('vendor/restapi'),
        ], 'restapi-lang');

        $this->publishes([
            __DIR__.'/../resources/views' => resource_path('views/vendor/restapi'),
        ]);
    }
}
