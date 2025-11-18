<?php

namespace MSJFramework\LaravelGenerator\Console\Commands;

use Illuminate\Console\Command;
use MSJFramework\LaravelGenerator\Services\PublishService;

class MSJInstallCommand extends Command
{
    protected PublishService $publisher;
    protected $signature = 'msj:install 
                            {--force : Overwrite existing files}
                            {--migrations : Publish migrations only}
                            {--controllers : Publish controllers only}
                            {--helpers : Publish helpers only}
                            {--views : Publish views only}
                            {--seeders : Publish seeders only}
                            {--examples : Publish examples only}
                            {--all : Publish everything}';

    protected $description = 'Install MSJ Framework components';

    public function __construct()
    {
        parent::__construct();
        $this->publisher = new PublishService();
    }

    public function handle(): int
    {
        $this->info('🚀 Installing MSJ Framework...');
        $this->displayEnvironment();
        $this->newLine();

        $force = $this->option('force');
        $publishOptions = $force ? ['--force' => true] : [];

        // Determine what to publish
        $publishAll = $this->option('all') || !$this->hasAnyOption();

        if ($publishAll || $this->option('migrations')) {
            $this->publishMigrations($publishOptions);
        }

        if ($publishAll || $this->option('controllers')) {
            $this->publishControllers($publishOptions);
        }

        if ($publishAll || $this->option('helpers')) {
            $this->publishHelpers($publishOptions);
        }

        if ($publishAll) {
            $this->publishModels($publishOptions);
        }

        if ($publishAll || $this->option('views')) {
            $this->publishViews($publishOptions);
        }

        if ($publishAll || $this->option('seeders')) {
            $this->publishSeeders($publishOptions);
        }

        if ($publishAll) {
            $this->publishMiddleware($publishOptions);
            $this->publishRoutes($publishOptions);
            $this->publishConfig($publishOptions);
        }

        if ($this->option('examples')) {
            $this->publishExamples($publishOptions);
        }

        $this->newLine();
        $this->components->info('✅ MSJ Framework installed successfully!');
        $this->newLine();

        if ($publishAll || $this->option('migrations')) {
            $this->components->warn('⚠️  Next steps:');
            $this->components->bulletList([
                'Run migrations: php artisan migrate',
                'Run seeder: php artisan db:seed --class=MSJSystemSeeder',
                'Check examples: see MSJ-Examples folder (use --examples)',
            ]);
        }

        $this->newLine();
        $this->components->info('📚 Documentation: https://github.com/rey-workbench/msjframework');

        return Command::SUCCESS;
    }

    protected function hasAnyOption(): bool
    {
        return $this->option('migrations')
            || $this->option('controllers')
            || $this->option('helpers')
            || $this->option('views')
            || $this->option('seeders')
            || $this->option('examples');
    }

    protected function displayEnvironment(): void
    {
        $env = $this->publisher->getEnvironmentInfo();
        $this->components->info("{$env['icon']} Detected: {$env['type']}");
        $this->components->info("   Using {$env['executor']}");
    }

    protected function publishMigrations(array $options): void
    {
        $this->components->task('Publishing migrations', function () use ($options) {
            return $this->publisher->executePublish('msj-migrations', $options);
        });
    }

    protected function publishControllers(array $options): void
    {
        $this->components->task('Publishing controllers', function () use ($options) {
            return $this->publisher->executePublish('msj-controllers', $options);
        });
    }

    protected function publishHelpers(array $options): void
    {
        $this->components->task('Publishing helpers', function () use ($options) {
            return $this->publisher->executePublish('msj-helpers', $options);
        });
    }

    protected function publishModels(array $options): void
    {
        $this->components->task('Publishing models', function () use ($options) {
            return $this->publisher->executePublish('msj-models', $options);
        });
    }

    protected function publishMiddleware(array $options): void
    {
        $this->components->task('Publishing middleware', function () use ($options) {
            return $this->publisher->executePublish('msj-middleware', $options);
        });
    }

    protected function publishViews(array $options): void
    {
        $this->components->task('Publishing views', function () use ($options) {
            return $this->publisher->executePublish('msj-views', $options);
        });
    }

    protected function publishRoutes(array $options): void
    {
        $this->components->task('Publishing routes', function () use ($options) {
            return $this->publisher->executePublish('msj-routes', $options);
        });
    }

    protected function publishConfig(array $options): void
    {
        $this->components->task('Publishing config', function () use ($options) {
            return $this->publisher->executePublish('msj-config', $options);
        });
    }

    protected function publishSeeders(array $options): void
    {
        $this->components->task('Publishing seeders', function () use ($options) {
            return $this->publisher->executePublish('msj-seeders', $options);
        });
    }

    protected function publishExamples(array $options): void
    {
        $this->components->task('Publishing examples', function () use ($options) {
            return $this->publisher->executePublish('msj-examples', $options);
        });
    }
}
