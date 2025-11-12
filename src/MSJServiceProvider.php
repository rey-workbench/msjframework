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
use Illuminate\Support\ServiceProvider;
use Laravel\Prompts\Prompt;
use function config_path;

class MSJServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        // Configure Laravel Prompts fallback for Windows native
        // Laravel Prompts only supports macOS, Linux, and Windows with WSL
        // See: https://laravel.com/docs/12.x/prompts#unsupported-environments-and-fallbacks
        // 
        // Note: Laravel framework automatically configures fallbacks, but we ensure
        // it's properly set up for this package, especially for Windows native environments
        
        // Use Laravel's windows_os() helper if available, otherwise fallback to PHP_OS_FAMILY
        $isWindows = function_exists('windows_os') 
            ? \windows_os() 
            : (PHP_OS_FAMILY === 'Windows');
        
        // Configure fallback for Windows native or non-interactive environments
        // This matches Laravel's default fallback configuration from the documentation
        Prompt::fallbackWhen(
            ! $this->app->runningInConsole() || 
            $isWindows || 
            $this->app->runningUnitTests()
        );

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
    }
}

