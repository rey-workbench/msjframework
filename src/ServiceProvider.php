<?php

namespace MSJFramework;

use MSJFramework\Console\Commands\MSJInstallCommand;
use MSJFramework\Console\Commands\MSJMakeMenuCommand;
use Illuminate\Support\ServiceProvider as BaseServiceProvider;

class ServiceProvider extends BaseServiceProvider
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
                __DIR__.'/Framework/Database/Migrations' => database_path('migrations'),
            ], 'framework-migrations');

            // Publish controllers
            $this->publishes([
                __DIR__.'/Framework/Controllers' => app_path('Http/Controllers'),
            ], 'framework-controllers');

            // Publish helpers
            $this->publishes([
                __DIR__.'/Framework/Helpers' => app_path('Helpers'),
            ], 'framework-helpers');

            // Publish models
            $this->publishes([
                __DIR__.'/Framework/Models' => app_path('Models'),
            ], 'framework-models');

            // Publish middleware
            $this->publishes([
                __DIR__.'/Framework/Middleware' => app_path('Http/Middleware'),
            ], 'framework-middleware');

            // Publish views
            $this->publishes([
                __DIR__.'/Framework/Views/Auto' => resource_path('views'),
            ], 'framework-views');

            // Publish routes
            $this->publishes([
                __DIR__.'/Framework/Routes/web.php' => base_path('routes/web-framework.php'),
            ], 'framework-routes');

            // Publish config
            $this->publishes([
                __DIR__.'/../config/framework.php' => config_path('framework.php'),
            ], 'framework-config');

            // Publish seeders
            $this->publishes([
                __DIR__.'/Framework/Database/Seeders' => database_path('seeders'),
            ], 'framework-seeders');
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

