<?php

namespace MSJFramework\LaravelGenerator;

use MSJFramework\LaravelGenerator\Console\Commands\MakeMSJController;
use MSJFramework\LaravelGenerator\Console\Commands\MakeMSJCrud;
use MSJFramework\LaravelGenerator\Console\Commands\MakeMSJModel;
use MSJFramework\LaravelGenerator\Console\Commands\MakeMSJModule;
use MSJFramework\LaravelGenerator\Console\Commands\MakeMSJViews;
use MSJFramework\LaravelGenerator\Console\Commands\MSJMake;
use Illuminate\Support\ServiceProvider;
use function config_path;

class MSJServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        if ($this->app->runningInConsole()) {
            $this->commands([
                MSJMake::class,
                MakeMSJModule::class,
                MakeMSJCrud::class,
                MakeMSJController::class,
                MakeMSJModel::class,
                MakeMSJViews::class,
            ]);
        }

        // Publish configuration file if needed
        $this->publishes([
            __DIR__.'/../config/msj-generator.php' => config_path('msj-generator.php'),
        ], 'msj-generator-config');
    }

    public function register(): void
    {
        $this->mergeConfigFrom(
            __DIR__.'/../config/msj-generator.php',
            'msj-generator'
        );
    }
}

