<?php

namespace MSJFramework\LaravelGenerator;

use MSJFramework\LaravelGenerator\Console\Commands\Database\MakeMSJAuth;
use MSJFramework\LaravelGenerator\Console\Commands\Database\MakeMSJDmenu;
use MSJFramework\LaravelGenerator\Console\Commands\Database\MakeMSJGmenu;
use MSJFramework\LaravelGenerator\Console\Commands\Generate\MakeMSJController;
use MSJFramework\LaravelGenerator\Console\Commands\Generate\MakeMSJCrud;
use MSJFramework\LaravelGenerator\Console\Commands\Generate\MakeMSJModel;
use MSJFramework\LaravelGenerator\Console\Commands\Generate\MakeMSJModule;
use MSJFramework\LaravelGenerator\Console\Commands\Generate\MakeMSJSeeder;
use MSJFramework\LaravelGenerator\Console\Commands\Generate\MakeMSJViews;
use MSJFramework\LaravelGenerator\Console\Commands\MSJMake;
use MSJFramework\LaravelGenerator\Console\Commands\Setup\MakeMSJInit;
use MSJFramework\LaravelGenerator\Console\Commands\Setup\MakeMSJSave;
use MSJFramework\LaravelGenerator\Support\WindowsPromptFallback;
use Illuminate\Support\ServiceProvider;
use function config_path;

class MSJServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        // Configure Laravel Prompts fallback for Windows native only
        (new WindowsPromptFallback($this->app))->configure();

        if ($this->app->runningInConsole()) {
            $this->commands([
                MSJMake::class,
                MakeMSJInit::class,
                MakeMSJSave::class,
                MakeMSJModule::class,
                MakeMSJCrud::class,
                MakeMSJController::class,
                MakeMSJModel::class,
                MakeMSJViews::class,
                MakeMSJSeeder::class,
                MakeMSJAuth::class,
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

        // Load prompt helper functions
        require_once __DIR__.'/Console/prompt_helpers.php';
    }
}

