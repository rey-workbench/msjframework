<?php

namespace MSJFramework\LaravelGenerator;

use MSJFramework\LaravelGenerator\Console\Commands\MakeMSJAuth;
use MSJFramework\LaravelGenerator\Console\Commands\MakeMSJController;
use MSJFramework\LaravelGenerator\Console\Commands\MakeMSJCrud;
use MSJFramework\LaravelGenerator\Console\Commands\MakeMSJDmenu;
use MSJFramework\LaravelGenerator\Console\Commands\MakeMSJGmenu;
use MSJFramework\LaravelGenerator\Console\Commands\MakeMSJModel;
use MSJFramework\LaravelGenerator\Console\Commands\MakeMSJModule;
use MSJFramework\LaravelGenerator\Console\Commands\MakeMSJSave;
use MSJFramework\LaravelGenerator\Console\Commands\MakeMSJSeeder;
use MSJFramework\LaravelGenerator\Console\Commands\MakeMSJViews;
use MSJFramework\LaravelGenerator\Console\Commands\MSJMake;
use MSJFramework\LaravelGenerator\Console\WindowsFallbackConfigurator;
use Illuminate\Support\ServiceProvider;
use function config_path;

class MSJServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        // Configure Laravel Prompts fallback for Windows native only
        // Laravel Prompts works natively on macOS, Linux, and Windows with WSL
        (new WindowsFallbackConfigurator($this->app))->configure();

        if ($this->app->runningInConsole()) {
            $this->commands([
                MSJMake::class,
                MakeMSJModule::class,
                MakeMSJCrud::class,
                MakeMSJController::class,
                MakeMSJModel::class,
                MakeMSJViews::class,
                MakeMSJAuth::class,
                MakeMSJSave::class,
                MakeMSJSeeder::class,
                MakeMSJGmenu::class,
                MakeMSJDmenu::class,
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

        // Load helper functions
        if (file_exists(__DIR__.'/Console/helpers.php')) {
            require_once __DIR__.'/Console/helpers.php';
        }
    }
}

