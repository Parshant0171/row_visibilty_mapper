<?php

namespace Jgu\RowVisibilityMapper;

use Illuminate\Support\ServiceProvider;

class RowVisibilityMapperServiceProvider extends ServiceProvider
{
    /**
     * Perform post-registration booting of services.
     *
     * @return void
     */
    public function boot(): void
    {
        // $this->loadTranslationsFrom(__DIR__.'/../resources/lang', 'jgu');
        // $this->loadViewsFrom(__DIR__.'/../resources/views', 'jgu');
        $this->loadMigrationsFrom(__DIR__.'/../database/migrations');
        // $this->loadRoutesFrom(__DIR__.'/routes/web.php');

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
    public function register(): void
    {
        $this->mergeConfigFrom(__DIR__.'/../config/row-visibility-mapper.php', 'row-visibility-mapper');

        // Register the service the package provides.
        $this->app->singleton('row-visibility-mapper', function ($app) {
            return new RowVisibilityMapper;
        });
    }

    /**
     * Get the services provided by the provider.
     *
     * @return array
     */
    public function provides()
    {
        return ['row-visibility-mapper'];
    }

    /**
     * Console-specific booting.
     *
     * @return void
     */
    protected function bootForConsole(): void
    {
        // Publishing the configuration file.
        $this->publishes([
            __DIR__.'/../config/row-visibility-mapper.php' => config_path('row-visibility-mapper.php'),
        ], 'row-visibility-mapper.config');

        // Publishing the views.
        /*$this->publishes([
            __DIR__.'/../resources/views' => base_path('resources/views/vendor/jgu'),
        ], 'row-visibility-mapper.views');*/

        // Publishing assets.
        /*$this->publishes([
            __DIR__.'/../resources/assets' => public_path('vendor/jgu'),
        ], 'row-visibility-mapper.views');*/

        // Publishing the translation files.
        /*$this->publishes([
            __DIR__.'/../resources/lang' => resource_path('lang/vendor/jgu'),
        ], 'row-visibility-mapper.views');*/

        // Registering package commands.
        // $this->commands([]);
    }
}
