<?php

namespace MSJFramework\LaravelGenerator\Console\Commands;

use MSJFramework\LaravelGenerator\Console\Commands\Concerns\HasConsoleStyling;
use MSJFramework\LaravelGenerator\Services\MSJModuleGenerator;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

use function Laravel\Prompts\confirm;
use function Laravel\Prompts\select;
use function Laravel\Prompts\text;

class MakeMSJCrud extends Command
{
    use HasConsoleStyling;

    protected $signature = 'msj:make:crud {table} {--gmenu= : Group menu code} {--dmenu= : Detail menu code} {--layout=manual : Layout type}';

    protected $description = 'Generator CRUD MSJ (CRUD Minimal)';
    
    protected string $currentGmenu = '';
    protected string $currentDmenu = '';

    public function handle(): int
    {
        $this->displayHeader('Generator CRUD MSJ');

        $table = $this->argument('table');

        // Layout dengan select
        if (! $this->option('layout')) {
            $layout = select(
                label: 'Pilih Layout',
                options: [
                    'manual' => 'Manual (kontrol penuh & Views Manual)',
                    'standr' => 'Standard (Bawaan MSJ Framework & Views Standard)',
                    'transc' => 'Transaksi (Bawaan MSJ Framework & Views Transaksi)',
                    'system' => 'System (Bawaan MSJ Framework & Views System)',
                    'report' => 'Report (Bawaan MSJ Framework & Views Report)',
                ],
                default: 'manual',
                scroll: 10
            );
        } else {
            $layout = $this->option('layout');
        }

        // GMenu dengan select
        $gmenuList = DB::table('sys_gmenu')
            ->where('isactive', '1')
            ->select('gmenu', 'name')
            ->get()
            ->mapWithKeys(fn ($item) => [$item->gmenu => "{$item->gmenu} - {$item->name}"])
            ->toArray();

        if (! empty($gmenuList) && ! $this->option('gmenu')) {
            $gmenuOptions = $gmenuList + ['__create_new__' => '+ Buat Group Menu Baru'];
            
            $selectedGmenu = select(
                label: 'Pilih Kode Group Menu (gmenu)',
                options: $gmenuOptions,
                default: in_array('KOP001', array_keys($gmenuList)) ? 'KOP001' : array_key_first($gmenuList),
                scroll: 10
            );
            
            if ($selectedGmenu === '__create_new__') {
                $gmenu = $this->createNewGmenuViaCommand();
            } else {
                $gmenu = $selectedGmenu;
            }
        } else {
            $gmenu = $this->option('gmenu') ?? $this->createNewGmenuViaCommand();
        }
        
        // Store gmenu untuk digunakan di createNewDmenu
        $this->currentGmenu = $gmenu;

        // DMenu dengan anticipate
        $dmenuList = DB::table('sys_dmenu')
            ->where('isactive', '1')
            ->select('dmenu')
            ->orderBy('dmenu', 'desc')
            ->limit(10)
            ->pluck('dmenu')
            ->toArray();

        if (! $this->option('dmenu')) {
            if (! empty($dmenuList)) {
                $dmenuOptions = ['__create_new__' => '+ Buat Detail Menu Baru'] + array_combine($dmenuList, $dmenuList);
                
                $selectedDmenu = select(
                    label: 'Pilih Kode Direktori Menu (dmenu)',
                    options: $dmenuOptions,
                    default: 'KOP999',
                    scroll: 10
                );
                
                if ($selectedDmenu === '__create_new__') {
                    $dmenu = $this->createNewDmenuViaCommand();
                } else {
                    $dmenu = $selectedDmenu;
                }
            } else {
                $dmenu = $this->createNewDmenuViaCommand();
            }
        } else {
            $dmenu = $this->option('dmenu');
        }

        $generator = new MSJModuleGenerator;
        $columns = $generator->getTableColumns($table);

        if (empty($columns)) {
            $this->badge('error', "Tabel '{$table}' tidak ditemukan!");

            return Command::FAILURE;
        }

        $menuName = Str::title(str_replace('_', ' ', $table));
        $url = Str::slug($menuName);

        $config = [
            'gmenu' => $gmenu,
            'dmenu' => $dmenu,
            'menu_name' => $menuName,
            'url' => $url,
            'table' => $table,
            'layout' => $layout,
        ];
        
        // Store for seeder prefix generation
        $this->currentGmenu = $gmenu;
        $this->currentDmenu = $dmenu;

        $generator->setConfig($config);
        $config['fields'] = $generator->mapColumnsToFields($columns);
        $generator->setConfig($config);

        $this->section('Generate CRUD');
        $this->line("<fg=gray>Table:</> <fg=white>{$table}</>");
        $this->newLine();

        $results = [
            'Model' => $generator->generateModel(),
            'Menu' => $generator->registerMenu(),
            'Table Config' => $generator->registerTableConfig(),
            'Authorization' => $generator->registerAuthorization(),
        ];

        if ($layout === 'manual') {
            $results['Controller'] = $generator->generateController();
            $results['Views'] = $generator->generateViews();
            $results['JavaScript'] = $generator->generateJavascript();
        }

        $this->displayResults($results, $url);
        
        // Auto save to seeder
        $this->autoSaveToSeeder();

        return Command::SUCCESS;
    }

    protected function displayResults(array $results, string $url): void
    {
        $this->newLine();

        foreach ($results as $component => $result) {
            $badgeType = match ($result['status']) {
                'success', 'created' => 'success',
                'updated' => 'completed',
                'skipped' => 'warning',
                'error' => 'error',
                default => 'error',
            };

            $message = $result['message'] ?? $result['status'];
            $this->badge($badgeType, "{$component}: {$message}");
        }

        $this->newLine();
        $this->displayHeader('Generate Selesai', '🎉');
        $this->line("<fg=gray>📍 Akses menu Anda di:</> <fg=cyan;options=bold>/{$url}</>");
        $this->newLine();
    }

    protected function autoSaveToSeeder(): void
    {
        $this->newLine();
        $this->section('💾 Auto Save to Seeder');
        
        if (confirm('Simpan konfigurasi menu ke seeder?', default: true)) {
            $this->line('<fg=yellow>⚡ Menyimpan menu dan table config...</>');
            
            try {
                // Generate prefix from gmenu/dmenu
                $prefix = $this->generateSeederPrefix();
                
                // Save menus using new seeder command
                $this->call('msj:make:seeder', [
                    'type' => 'menu', 
                    '--auto' => true,
                    '--prefix' => $prefix,
                    '--gmenu' => $this->currentGmenu,
                    '--dmenu' => $this->currentDmenu
                ]);
                $this->badge('success', "Menu berhasil disimpan ke {$prefix}MenuSeeder");
                
                // Save table config using new seeder command
                $this->call('msj:make:seeder', [
                    'type' => 'table', 
                    '--auto' => true,
                    '--prefix' => $prefix,
                    '--gmenu' => $this->currentGmenu,
                    '--dmenu' => $this->currentDmenu
                ]);
                $this->badge('success', "Table config berhasil disimpan ke {$prefix}TableSeeder");
                
                $this->newLine();
                $this->line("<fg=gray>💡 Tip: Jalankan</> <fg=cyan>php artisan db:seed --class={$prefix}MenuSeeder</> <fg=gray>untuk restore menu</> ");
                $this->line("<fg=gray>💡 Tip: Jalankan</> <fg=cyan>php artisan db:seed --class={$prefix}TableSeeder</> <fg=gray>untuk restore table config</> ");
                
            } catch (\Exception $e) {
                $this->badge('error', 'Error: ' . $e->getMessage());
            }
        } else {
            $this->badge('warning', 'Auto save dilewati');
        }
    }

    protected function createNewGmenuViaCommand(): string
    {
        $this->newLine();
        $this->section('📝 Membuat Group Menu Baru via Command');
        
        $exitCode = $this->call('msj:make:gmenu');
        
        if ($exitCode !== 0) {
            $this->badge('error', 'Gagal membuat Group Menu');
            return 'KOP001'; // fallback
        }
        
        // Get the latest created gmenu
        $latestGmenu = DB::table('sys_gmenu')
            ->where('isactive', '1')
            ->orderBy('created_at', 'desc')
            ->first();
            
        return $latestGmenu ? $latestGmenu->gmenu : 'KOP001';
    }

    protected function createNewDmenuViaCommand(): string
    {
        $this->newLine();
        $this->section('📝 Membuat Detail Menu Baru via Command');
        
        $gmenuCode = $this->currentGmenu ?: 'KOP001';
        
        $exitCode = $this->call('msj:make:dmenu', ['--gmenu' => $gmenuCode]);
        
        if ($exitCode !== 0) {
            $this->badge('error', 'Gagal membuat Detail Menu');
            return 'KOP999'; // fallback
        }
        
        // Get the latest created dmenu
        $latestDmenu = DB::table('sys_dmenu')
            ->where('isactive', '1')
            ->where('gmenu', $gmenuCode)
            ->orderBy('created_at', 'desc')
            ->first();
            
        return $latestDmenu ? $latestDmenu->dmenu : 'KOP999';
    }

    protected function generateSeederPrefix(): string
    {
        $gmenu = $this->currentGmenu ?: 'MSJ';
        $dmenu = $this->currentDmenu ?: '';
        
        // Generate smart prefix based on gmenu/dmenu
        if (!empty($dmenu)) {
            // Use dmenu as primary identifier
            $prefix = Str::studly(str_replace(['_', '-'], '', $dmenu));
        } else {
            // Fallback to gmenu
            $prefix = Str::studly(str_replace(['_', '-'], '', $gmenu));
        }
        
        // Clean up common prefixes/suffixes
        $prefix = preg_replace('/^(Kop|Msj|Sys)/', '', $prefix);
        $prefix = preg_replace('/\d+$/', '', $prefix); // Remove trailing numbers
        
        // Ensure it's not empty and has reasonable length
        if (empty($prefix) || strlen($prefix) < 2) {
            $prefix = 'MSJ';
        }
        
        // Capitalize first letter
        $prefix = ucfirst($prefix);
        
        return $prefix;
    }
}
