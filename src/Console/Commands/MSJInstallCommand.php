<?php

namespace MSJFramework\Console\Commands;

use Illuminate\Console\Command;
use MSJFramework\Services\PublishService;

class MSJInstallCommand extends Command
{
    protected PublishService $publisher;
    protected $signature = 'msj:install 
                            {--force : Timpa file yang sudah ada}
                            {--migrations : Hanya publish migrasi}
                            {--controllers : Hanya publish controller}
                            {--helpers : Hanya publish helper}
                            {--views : Hanya publish view}
                            {--seeders : Hanya publish seeder}
                            {--examples : Hanya publish contoh}
                            {--all : Publish semua komponen}';

    protected $description = 'Instal komponen MSJ Framework';

    public function __construct()
    {
        parent::__construct();
        $this->publisher = new PublishService();
    }

    public function handle(): int
    {
        $this->info('🚀 Menginstal MSJ Framework...');
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
        $this->components->info('✅ MSJ Framework berhasil diinstal!');
        $this->newLine();

        if ($publishAll || $this->option('migrations')) {
            $this->components->warn('⚠️  Langkah selanjutnya:');
            $this->components->bulletList([
                'Jalankan migrasi: php artisan migrate',
                'Jalankan seeder: php artisan db:seed --class=MSJSystemSeeder',
                'Lihat contoh: folder MSJ-Examples (gunakan --examples)',
            ]);
        }

        $this->newLine();
        $this->components->info('📚 Dokumentasi: https://github.com/rey-workbench/msjframework');

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
        $this->components->info("{$env['icon']} Terdeteksi: {$env['type']}");
        $this->components->info("   Menggunakan {$env['executor']}");
    }

    protected function publishMigrations(array $options): void
    {
        $this->components->task('Mempublish migrasi', function () use ($options) {
            return $this->publisher->executePublish('msj-migrations', $options);
        });
    }

    protected function publishControllers(array $options): void
    {
        $this->components->task('Mempublish controller', function () use ($options) {
            return $this->publisher->executePublish('msj-controllers', $options);
        });
    }

    protected function publishHelpers(array $options): void
    {
        $this->components->task('Mempublish helper', function () use ($options) {
            return $this->publisher->executePublish('msj-helpers', $options);
        });
    }

    protected function publishModels(array $options): void
    {
        $this->components->task('Mempublish model', function () use ($options) {
            return $this->publisher->executePublish('msj-models', $options);
        });
    }

    protected function publishMiddleware(array $options): void
    {
        $this->components->task('Mempublish middleware', function () use ($options) {
            return $this->publisher->executePublish('msj-middleware', $options);
        });
    }

    protected function publishViews(array $options): void
    {
        $this->components->task('Mempublish view', function () use ($options) {
            return $this->publisher->executePublish('msj-views', $options);
        });
    }

    protected function publishRoutes(array $options): void
    {
        $this->components->task('Mempublish routes', function () use ($options) {
            return $this->publisher->executePublish('msj-routes', $options);
        });
    }

    protected function publishConfig(array $options): void
    {
        $this->components->task('Mempublish config', function () use ($options) {
            return $this->publisher->executePublish('msj-config', $options);
        });
    }

    protected function publishSeeders(array $options): void
    {
        $this->components->task('Mempublish seeder', function () use ($options) {
            return $this->publisher->executePublish('msj-seeders', $options);
        });
    }

    protected function publishExamples(array $options): void
    {
        $this->components->task('Mempublish contoh', function () use ($options) {
            return $this->publisher->executePublish('msj-examples', $options);
        });
    }
}
