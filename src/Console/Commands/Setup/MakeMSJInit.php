<?php

namespace MSJFramework\LaravelGenerator\Console\Commands\Setup;

use MSJFramework\LaravelGenerator\Console\Commands\Concerns\HasConsoleStyling;
use MSJFramework\LaravelGenerator\Templates\Controllers\Base\PageControllerTemplate;
use MSJFramework\LaravelGenerator\Templates\Helpers\FormatHelperTemplate;
use MSJFramework\LaravelGenerator\Templates\Helpers\FunctionHelperTemplate;
use MSJFramework\LaravelGenerator\Templates\Helpers\ValidationHelperTemplate;
use MSJFramework\LaravelGenerator\Templates\Helpers\ErrorHelperTemplate;
use MSJFramework\LaravelGenerator\Templates\Helpers\TableExporterTemplate;
use MSJFramework\LaravelGenerator\Templates\Helpers\IdGeneratorTemplate;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

class MakeMSJInit extends Command
{
    use HasConsoleStyling;

    protected $signature = 'msj:init';

    protected $description = 'Initialize MSJ Framework with PageController and default routes for auto layout';

    public function handle(): int
    {
        $this->displayHeader('MSJ Framework Initialization');

        $this->section('Generating Core Files');

        // 1. Generate PageController
        $pageControllerResult = $this->generatePageController();
        $this->displayResult('PageController', $pageControllerResult);

        // 2. Setup default routes for auto layout
        $routesResult = $this->setupAutoLayoutRoutes();
        $this->displayResult('Routes Setup', $routesResult);

        $this->newLine();
        $this->badge('completed', 'MSJ Framework initialization completed successfully!');
        
        $this->newLine();
        $this->section('Next Steps');
        $this->line('  1. Configure your database in .env');
        $this->line('  2. Run migrations: php artisan migrate');
        $this->line('  3. Use: php artisan msj:make to create modules');
        $this->newLine();

        return Command::SUCCESS;
    }

    protected function generatePageController(): array
    {
        $controllerPath = app_path('Http/Controllers/PageController.php');

        if (File::exists($controllerPath)) {
            $this->badge('warning', 'PageController already exists!');
            
            if (!prompt_confirm('Overwrite existing PageController?', false, $this)) {
                return [
                    'status' => 'skipped',
                    'message' => 'Skipped (already exists)',
                    'path' => $controllerPath,
                ];
            }
        }

        try {
            $content = PageControllerTemplate::getTemplate();
            File::put($controllerPath, $content);

            return [
                'status' => 'success',
                'message' => 'Created successfully',
                'path' => $controllerPath,
            ];
        } catch (\Exception $e) {
            return [
                'status' => 'error',
                'message' => 'Failed: ' . $e->getMessage(),
                'path' => $controllerPath,
            ];
        }
    }

    protected function setupAutoLayoutRoutes(): array
    {
        $webRoutesPath = base_path('routes/web.php');

        if (!File::exists($webRoutesPath)) {
            return [
                'status' => 'error',
                'message' => 'routes/web.php not found',
                'path' => $webRoutesPath,
            ];
        }

        $existingRoutes = File::get($webRoutesPath);
        $msjRoutesExist = strpos($existingRoutes, '// MSJ Framework Auto Layout Routes') !== false;
        
        // Check if old catch-all PageController routes exist
        $oldRoutesExist = strpos($existingRoutes, "Route::get('/{page}', [PageController::class, 'index'])") !== false;

        // Check if MSJ routes already exist
        if ($msjRoutesExist) {
            $this->badge('warning', 'MSJ Framework routes already exist in web.php!');
            
            if (!prompt_confirm('Overwrite existing MSJ routes?', false, $this)) {
                return [
                    'status' => 'skipped',
                    'message' => 'Skipped (already exists)',
                    'path' => $webRoutesPath,
                ];
            }

            // Remove existing MSJ routes section (including the newlines)
            $existingRoutes = preg_replace(
                '/\n*\/\/ MSJ Framework Auto Layout Routes.*?\/\/ End MSJ Framework Routes\n*/s',
                '',
                $existingRoutes
            );
        }
        
        // Check and remove old catch-all routes to prevent duplication
        if ($oldRoutesExist) {
            $this->badge('warning', 'Old PageController catch-all routes detected!');
            $this->line('   These routes will conflict with MSJ Framework routes.');
            
            if (prompt_confirm('Remove old catch-all routes to prevent duplication?', true, $this)) {
                // Remove comment line
                $existingRoutes = preg_replace('/\n\s*\/\/ Catch-all routes must be at the end\s*\n/', "\n", $existingRoutes);
                
                // Remove all PageController catch-all routes (6 lines)
                $existingRoutes = preg_replace("/\s*Route::get\('\/{page\}',\s*\[PageController::class,\s*'index'\]\).*\n/", '', $existingRoutes);
                $existingRoutes = preg_replace("/\s*Route::post\('\/{page\}',\s*\[PageController::class,\s*'index'\]\).*\n/", '', $existingRoutes);
                $existingRoutes = preg_replace("/\s*Route::get\('\/{page\}\/\{action\}',\s*\[PageController::class,\s*'index'\]\).*\n/", '', $existingRoutes);
                $existingRoutes = preg_replace("/\s*Route::put\('\/{page\}\/\{action\}',\s*\[PageController::class,\s*'index'\]\).*\n/", '', $existingRoutes);
                $existingRoutes = preg_replace("/\s*Route::delete\('\/{page\}\/\{action\}',\s*\[PageController::class,\s*'index'\]\).*\n/", '', $existingRoutes);
                $existingRoutes = preg_replace("/\s*Route::get\('\/{page\}\/\{action\}\/\{id\}',\s*\[PageController::class,\s*'index'\]\).*\n/", '', $existingRoutes);
                
                $this->badge('success', 'Old catch-all routes removed');
            } else {
                $this->badge('warning', 'Routes may conflict! Manual cleanup recommended:');
                $this->line('   Remove lines with: Route::get(\'/{page}\'...');
                $this->line('   Before: // Catch-all routes must be at the end');
            }
        }

        // Default routes template for auto layout
        $routesTemplate = <<<'PHP'

// MSJ Framework Auto Layout Routes
Route::middleware(['auth'])->group(function () {
    // Auto layout routes - handles all CRUD operations dynamically
    // Pattern: /{page}/{action?}/{id?}
    // Examples:
    //   /users               -> index (list)
    //   /users/add           -> add form
    //   /users/edit/xxx      -> edit form
    //   /users/show/xxx      -> show details
    //   POST /users          -> store (create)
    //   PUT /users/xxx       -> update
    //   DELETE /users/xxx    -> destroy
    
    // System layout routes (2 positions: header + detail)
    Route::get('/system/{page}/{action?}/{id?}', [App\Http\Controllers\PageController::class, 'index'])->name('system.auto');
    Route::post('/system/{page}', [App\Http\Controllers\PageController::class, 'index'])->name('system.store');
    Route::put('/system/{page}/{id}', [App\Http\Controllers\PageController::class, 'index'])->name('system.update');
    Route::delete('/system/{page}/{id}', [App\Http\Controllers\PageController::class, 'index'])->name('system.destroy');
    
    // Transaction layout routes (2 positions: header + detail)
    Route::get('/transc/{page}/{action?}/{id?}', [App\Http\Controllers\PageController::class, 'index'])->name('transc.auto');
    Route::post('/transc/{page}', [App\Http\Controllers\PageController::class, 'index'])->name('transc.store');
    Route::put('/transc/{page}/{id}', [App\Http\Controllers\PageController::class, 'index'])->name('transc.update');
    Route::delete('/transc/{page}/{id}', [App\Http\Controllers\PageController::class, 'index'])->name('transc.destroy');
    
    // Standard layout routes (single table with position 3&4)
    Route::get('/standr/{page}/{action?}/{id?}', [App\Http\Controllers\PageController::class, 'index'])->name('standr.auto');
    Route::post('/standr/{page}', [App\Http\Controllers\PageController::class, 'index'])->name('standr.store');
    Route::put('/standr/{page}/{id}', [App\Http\Controllers\PageController::class, 'index'])->name('standr.update');
    Route::delete('/standr/{page}/{id}', [App\Http\Controllers\PageController::class, 'index'])->name('standr.destroy');
    
    // Master layout routes (similar to standard + pagination & export)
    Route::get('/master/{page}/{action?}/{id?}', [App\Http\Controllers\PageController::class, 'index'])->name('master.auto');
    Route::post('/master/{page}', [App\Http\Controllers\PageController::class, 'index'])->name('master.store');
    Route::put('/master/{page}/{id}', [App\Http\Controllers\PageController::class, 'index'])->name('master.update');
    Route::delete('/master/{page}/{id}', [App\Http\Controllers\PageController::class, 'index'])->name('master.destroy');
    
    // Report layout routes (filter + result)
    Route::get('/report/{page}', [App\Http\Controllers\PageController::class, 'index'])->name('report.filter');
    Route::post('/report/{page}', [App\Http\Controllers\PageController::class, 'index'])->name('report.result');
    
    // Sublink layout routes (nested sublink with dynamic table)
    Route::get('/sublnk/{page}/{action?}/{id?}', [App\Http\Controllers\PageController::class, 'index'])->name('sublnk.auto');
    Route::post('/sublnk/{page}', [App\Http\Controllers\PageController::class, 'index'])->name('sublnk.store');
    Route::put('/sublnk/{page}/{id}', [App\Http\Controllers\PageController::class, 'index'])->name('sublnk.update');
    Route::delete('/sublnk/{page}/{id}', [App\Http\Controllers\PageController::class, 'index'])->name('sublnk.destroy');
    
    // Fallback for pages without specific layout (manual layout)
    Route::get('/{page}/{action?}/{id?}', [App\Http\Controllers\PageController::class, 'index'])->name('page.auto');
    Route::post('/{page}', [App\Http\Controllers\PageController::class, 'index'])->name('page.store');
    Route::put('/{page}/{id}', [App\Http\Controllers\PageController::class, 'index'])->name('page.update');
    Route::delete('/{page}/{id}', [App\Http\Controllers\PageController::class, 'index'])->name('page.destroy');
});
// End MSJ Framework Routes

PHP;

        try {
            // Append or replace routes in web.php
            if ($msjRoutesExist) {
                // Write back the modified content (overwrite mode)
                File::put($webRoutesPath, $existingRoutes . $routesTemplate);
                
                return [
                    'status' => 'success',
                    'message' => 'Routes updated successfully',
                    'path' => $webRoutesPath,
                ];
            } else {
                // Append routes to web.php (new installation)
                File::append($webRoutesPath, $routesTemplate);
                
                return [
                    'status' => 'success',
                    'message' => 'Routes added successfully',
                    'path' => $webRoutesPath,
                ];
            }
        } catch (\Exception $e) {
            return [
                'status' => 'error',
                'message' => 'Failed to add routes: ' . $e->getMessage(),
                'path' => $webRoutesPath,
            ];
        }
    }

    protected function publishMigrations(): array
    {
        try {
            $templateMigrationsPath = __DIR__ . '/../../../Templates/Database/Migrations';
            $projectMigrationsPath = database_path('migrations');

            if (!File::isDirectory($projectMigrationsPath)) {
                File::makeDirectory($projectMigrationsPath, 0755, true);
            }

            $migrationFiles = [
                '2024_01_01_000001_create_sys_gmenu_table.php',
                '2024_01_01_000002_create_sys_dmenu_table.php',
                '2024_01_01_000003_create_sys_roles_table.php',
                '2024_01_01_000004_create_sys_auth_table.php',
                '2024_01_01_000005_create_sys_table_table.php',
                '2024_01_01_000006_create_sys_app_table.php',
                '2024_01_01_000007_create_sys_log_table.php',
                '2024_01_01_000008_create_sys_id_table.php',
                '2024_01_01_000009_create_sys_counter_table.php',
                '2024_01_01_000010_create_sys_enum_table.php',
                '2024_01_01_000011_create_sys_number_table.php',
            ];

            $created = 0;
            $skipped = 0;

            foreach ($migrationFiles as $filename) {
                $sourcePath = $templateMigrationsPath . '/' . $filename;
                $destPath = $projectMigrationsPath . '/' . $filename;

                if (!File::exists($destPath)) {
                    if (! File::exists($sourcePath)) {
                        throw new \RuntimeException("Template migration missing: {$filename}");
                    }

                    File::copy($sourcePath, $destPath);
                    $created++;
                } else {
                    $skipped++;
                }
            }

            return [
                'status' => 'success',
                'message' => "Published {$created} migration files" . ($skipped > 0 ? " ({$skipped} skipped)" : ''),
                'path' => $projectMigrationsPath,
            ];
        } catch (\Exception $e) {
            return [
                'status' => 'error',
                'message' => 'Failed: ' . $e->getMessage(),
            ];
        }
    }

    protected function generateHelpers(): array
    {
        try {
            FormatHelperTemplate::createIfNotExists();
            FunctionHelperTemplate::createIfNotExists();
            ValidationHelperTemplate::createIfNotExists();
            ErrorHelperTemplate::createIfNotExists();
            TableExporterTemplate::createIfNotExists();
            IdGeneratorTemplate::createIfNotExists();

            return [
                'status' => 'success',
                'message' => 'Generated 6 helper files',
                'path' => app_path('Helpers'),
            ];
        } catch (\Exception $e) {
            return [
                'status' => 'error',
                'message' => 'Failed: ' . $e->getMessage(),
            ];
        }
    }

    protected function displayResult(string $label, array $result): void
    {
        // Use badge for status
        $badgeType = match ($result['status']) {
            'success' => 'success',
            'skipped' => 'warning',
            'error' => 'error',
            default => 'info',
        };

        $this->badge($badgeType, "{$label}: {$result['message']}");
        
        if (isset($result['path'])) {
            if ($this->isWindowsNative()) {
                $this->line("   Path: {$result['path']}");
            } else {
                $this->line("   Path: <fg=gray>{$result['path']}</>");
            }
        }

        $this->newLine();
    }
}
