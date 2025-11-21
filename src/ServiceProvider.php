<?php

namespace MSJFramework;

use MSJFramework\Console\Commands\MainCommand;
use MSJFramework\Console\Commands\Submenu\InstallCommand;
use MSJFramework\Console\Commands\Submenu\MenuCommand;
use MSJFramework\Console\Commands\Submenu\EnumCommand;
use MSJFramework\Console\Commands\Submenu\MenuLayouts\MasterLayoutCommand;
use MSJFramework\Console\Commands\Submenu\MenuLayouts\TranscLayoutCommand;
use MSJFramework\Console\Commands\Submenu\MenuLayouts\SystemLayoutCommand;
use MSJFramework\Console\Commands\Submenu\MenuLayouts\StandrLayoutCommand;
use MSJFramework\Console\Commands\Submenu\MenuLayouts\SublnkLayoutCommand;
use MSJFramework\Console\Commands\Submenu\MenuLayouts\ReportLayoutCommand;
use MSJFramework\Console\Commands\Submenu\MenuLayouts\ManualLayoutCommand;
use Illuminate\Support\ServiceProvider as BaseServiceProvider;

class ServiceProvider extends BaseServiceProvider
{
    public function boot(): void
    {
        if ($this->app->runningInConsole()) {
            $this->commands([
                MainCommand::class,
                InstallCommand::class,
                MenuCommand::class,
                EnumCommand::class,
                MasterLayoutCommand::class,
                TranscLayoutCommand::class,
                SystemLayoutCommand::class,
                StandrLayoutCommand::class,
                SublnkLayoutCommand::class,
                ReportLayoutCommand::class,
                ManualLayoutCommand::class,
            ]);

            // Publish migrations
            $this->publishes([
                __DIR__.'/Framework/Database/Migrations' => database_path('migrations'),
            ], 'msj-migrations');

            // Publish controllers
            $this->publishes([
                __DIR__.'/Framework/Controllers' => app_path('Http/Controllers'),
            ], 'msj-controllers');

            // Publish helpers
            $this->publishes([
                __DIR__.'/Framework/Helpers' => app_path('Helpers'),
            ], 'msj-helpers');

            // Publish models
            $this->publishes([
                __DIR__.'/Framework/Models' => app_path('Models'),
            ], 'msj-models');

            // Publish middleware
            $this->publishes([
                __DIR__.'/Framework/Middleware' => app_path('Http/Middleware'),
            ], 'msj-middleware');

            // Publish views
            $this->publishes([
                __DIR__.'/Framework/Views/Auto' => resource_path('views'),
            ], 'msj-views');

            // Publish routes
            $this->publishes([
                __DIR__.'/Framework/Routes/web.php' => base_path('routes/web-framework.php'),
            ], 'msj-routes');

            // Publish config
            $this->publishes([
                __DIR__.'/../config/framework.php' => config_path('framework.php'),
            ], 'msj-config');

            // Publish seeders
            $this->publishes([
                __DIR__.'/Framework/Database/Seeders' => database_path('seeders'),
            ], 'msj-seeders');

            // Publish examples
            $this->publishes([
                __DIR__.'/Examples' => base_path('MSJ-Examples'),
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

