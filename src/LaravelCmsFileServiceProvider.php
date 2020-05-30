<?php

namespace chenweibo\LaravelCmsFile;

use Illuminate\Support\ServiceProvider;

class LaravelCmsFileServiceProvider extends ServiceProvider
{
    /**
     * Perform post-registration booting of services.
     *
     * @return void
     */
    public function boot()
    {
        // $this->loadTranslationsFrom(__DIR__.'/../resources/lang', 'chenweibo');
        // $this->loadViewsFrom(__DIR__.'/../resources/views', 'chenweibo');
        // $this->loadMigrationsFrom(__DIR__.'/../database/migrations');
        // $this->loadRoutesFrom(__DIR__.'/routes.php');

        // Publishing is only necessary when using the CLI.
        if ($this->app->runningInConsole()) {
            $this->bootForConsole();
        }
    }

    /**
     * Register any package services.
     *
     * @return void
     */
    public function register()
    {
        $this->mergeConfigFrom(__DIR__.'/../config/laravelcmsfile.php', 'laravelcmsfile');

        // Register the service the package provides.
        $this->app->singleton('laravelcmsfile', function ($app) {
            return new LaravelCmsFile;
        });
    }

    /**
     * Get the services provided by the provider.
     *
     * @return array
     */
    public function provides()
    {
        return ['laravelcmsfile'];
    }
    
    /**
     * Console-specific booting.
     *
     * @return void
     */
    protected function bootForConsole()
    {
        // Publishing the configuration file.
        $this->publishes([
            __DIR__.'/../config/laravelcmsfile.php' => config_path('laravelcmsfile.php'),
        ], 'laravelcmsfile.config');

        // Publishing the views.
        /*$this->publishes([
            __DIR__.'/../resources/views' => base_path('resources/views/vendor/chenweibo'),
        ], 'laravelcmsfile.views');*/

        // Publishing assets.
        /*$this->publishes([
            __DIR__.'/../resources/assets' => public_path('vendor/chenweibo'),
        ], 'laravelcmsfile.views');*/

        // Publishing the translation files.
        /*$this->publishes([
            __DIR__.'/../resources/lang' => resource_path('lang/vendor/chenweibo'),
        ], 'laravelcmsfile.views');*/

        // Registering package commands.
        // $this->commands([]);
    }
}
