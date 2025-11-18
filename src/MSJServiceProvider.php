<?php

namespace MSJFramework\LaravelGenerator;

use MSJFramework\LaravelGenerator\Console\Commands\MSJInstallCommand;
use MSJFramework\LaravelGenerator\Console\Commands\MSJMakeMenuCommand;
use Illuminate\Support\ServiceProvider;

class MSJServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        if ($this->app->runningInConsole()) {
            $this->commands([
                MSJInstallCommand::class,
                MSJMakeMenuCommand::class,
            ]);

            // Publish migrations
            $this->publishes([
                __DIR__.'/Database/Migrations' => database_path('migrations'),
            ], 'msj-migrations');

            // Publish controllers
            $this->publishes([
                __DIR__.'/Controllers' => app_path('Http/Controllers'),
            ], 'msj-controllers');

            // Publish helpers
            $this->publishes([
                __DIR__.'/Helpers' => app_path('Helpers'),
            ], 'msj-helpers');

            // Publish models
            $this->publishes([
                __DIR__.'/Models' => app_path('Models'),
            ], 'msj-models');

            // Publish middleware
            $this->publishes([
                __DIR__.'/Middleware' => app_path('Http/Middleware'),
            ], 'msj-middleware');

            // Publish views
            $this->publishes([
                __DIR__.'/../stubs/views' => resource_path('views'),
            ], 'msj-views');

            // Publish routes
            $this->publishes([
                __DIR__.'/../stubs/routes/web.php' => base_path('routes/web.php'),
            ], 'msj-routes');

            // Publish config
            $this->publishes([
                __DIR__.'/../config/msj.php' => config_path('msj.php'),
            ], 'msj-config');

            // Publish seeders
            $this->publishes([
                __DIR__.'/Database/Seeders' => database_path('seeders'),
            ], 'msj-seeders');

            // Publish examples
            $this->publishes([
                __DIR__.'/../stubs/examples' => base_path('MSJ-Examples'),
            ], 'msj-examples');
        }
    }

    public function register(): void
    {
        $this->mergeConfigFrom(
            __DIR__.'/../config/msj.php',
            'msj'
        );
    }
}

