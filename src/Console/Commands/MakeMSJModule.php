<?php

namespace MSJFramework\LaravelGenerator\Console\Commands;

use MSJFramework\LaravelGenerator\Console\Commands\Concerns\HasConsoleStyling;
use MSJFramework\LaravelGenerator\Services\MSJModuleGenerator;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

// Import safe prompt helpers that work on all platforms
// These automatically fallback to Symfony Console on Windows

class MakeMSJModule extends Command
{
    use HasConsoleStyling;

    protected $signature = 'msj:make:menu';

    protected $description = 'MSJ Framework Menu Generator CLI';

    protected MSJModuleGenerator $generator;

    protected array $moduleData = [];

    public function handle(): int
    {
        $this->displayWelcomeBox();
        
        $result = $this->collectModuleData();
        if ($result !== Command::SUCCESS) {
            return $result;
        }

        if (! $this->confirmGeneration()) {
            return Command::SUCCESS;
        }

        $this->generateModule();

        return Command::SUCCESS;
    }

    protected function displayWelcomeBox(): void
    {
        $this->displayHeader('MSJ Menu Generator');
    }

    protected function collectModuleData(): int
    {
        $this->collectLayoutType();
        
        $result = $this->collectBasicInfo();
        if ($result !== Command::SUCCESS) {
            return $result;
        }
        
        $this->collectFieldsConfig();
        $this->displaySummary();
        
        return Command::SUCCESS;
    }

    protected function collectLayoutType(): void
    {
        $this->displayStep('Step 1/4', 'Pilih Tipe Layout');

        $layouts = [
            'manual' => 'Manual (kontrol penuh & Views Manual)',
            'standr' => 'Standard (Bawaan MSJ Framework & Views Standard)',
            'transc' => 'Transaksi (Bawaan MSJ Framework & Views Transaksi)',
            'system' => 'System (Bawaan MSJ Framework & Views System)',
            'report' => 'Report (Bawaan MSJ Framework & Views Report)',
        ];

        $this->newLine();
        $this->moduleData['layout'] = prompt_select(
            label: 'Pilih Layout',
            options: $layouts,
            default: 'manual',
            scroll: 10,
            command: $this
        );

        $this->displaySuccess("Layout yang dipilih adalah: {$this->moduleData['layout']}");
        $this->newLine(2);
    }

    protected function collectBasicInfo(): int
    {
        $this->displayStep('Step 2/4', 'Informasi Dasar');

        $this->displayAvailableMenus();

        // GMenu dengan select (radio button style)
        $gmenuList = DB::table('sys_gmenu')
            ->where('isactive', '1')
            ->select('gmenu', 'name')
            ->get()
            ->mapWithKeys(fn ($item) => [$item->gmenu => "{$item->gmenu} - {$item->name}"])
            ->toArray();

        if (! empty($gmenuList)) {
            $gmenuOptions = ['__create_new__' => '+ Buat Group Menu Baru'] + $gmenuList;
            
            $selectedGmenu = prompt_select(
                label: 'Pilih Kode Group Menu (gmenu)',
                options: $gmenuOptions,
                default: '__create_new__',
                scroll: 10,
                command: $this
            );
            
            if ($selectedGmenu === '__create_new__') {
                $gmenu = $this->createNewGmenuViaCommand();
                if ($gmenu === null) {
                    return Command::FAILURE;
                }
                $this->moduleData['gmenu'] = $gmenu;
            } else {
                $this->moduleData['gmenu'] = $selectedGmenu;
            }
        } else {
            $gmenu = $this->createNewGmenuViaCommand();
            if ($gmenu === null) {
                return Command::FAILURE;
            }
            $this->moduleData['gmenu'] = $gmenu;
        }

        // DMenu dengan search untuk autocomplete
        $dmenuList = DB::table('sys_dmenu')
            ->where('isactive', '1')
            ->select('dmenu')
            ->orderBy('dmenu', 'desc')
            ->limit(20)
            ->pluck('dmenu')
            ->toArray();

        if (! empty($dmenuList)) {
            $dmenuOptions = ['__create_new__' => '+ Buat Detail Menu Baru'] + array_combine($dmenuList, $dmenuList);
            
            $selectedDmenu = prompt_select(
                label: 'Pilih Kode Detail Menu (dmenu)',
                options: $dmenuOptions,
                default: '__create_new__',
                scroll: 10,
                command: $this
            );
            
            if ($selectedDmenu === '__create_new__') {
                $dmenu = $this->createNewDmenuViaCommand();
                if ($dmenu === null) {
                    return Command::FAILURE;
                }
                $this->moduleData['dmenu'] = $dmenu;
            } else {
                $this->moduleData['dmenu'] = $selectedDmenu ?: 'KOP999';
            }
        } else {
            $dmenu = $this->createNewDmenuViaCommand();
            if ($dmenu === null) {
                return Command::FAILURE;
            }
            $this->moduleData['dmenu'] = $dmenu;
        }

        // Menu Name
        $this->moduleData['menu_name'] = prompt_text('Masukkan Nama Menu', default: 'Data Example', required: true, command: $this);

        $suggestedUrl = Str::slug($this->moduleData['menu_name']);
        $this->moduleData['url'] = prompt_text(
            label: 'Masukkan URL',
            default: $suggestedUrl,
            required: false,
            command: $this
        );

        // Table dengan search dari database
        $tables = $this->getAvailableTables();
        if (! empty($tables)) {
            $selectedTable = prompt_search(
                label: 'Pilih Nama Tabel Database',
                options: fn ($value) => ! empty($value)
                    ? array_values(array_filter($tables, fn ($table) => stripos($table, $value) !== false))
                    : array_slice($tables, 0, 15),
                placeholder: 'Ketik untuk mencari tabel...',
                command: $this
            );

            // Validasi hasil search
            $this->moduleData['table'] = is_string($selectedTable) && in_array($selectedTable, $tables)
                ? $selectedTable
                : (in_array('mst_example', $tables) ? 'mst_example' : ($tables[0] ?? 'mst_example'));
        } else {
            $this->moduleData['table'] = prompt_text('Masukkan Nama Tabel Database', default: 'mst_example', required: true, command: $this);
        }

        $this->validateTableExists();

        $this->displaySuccess('Informasi dasar telah dikumpulkan dan validasi tabel berhasil');
        $this->newLine(2);
        
        return Command::SUCCESS;
    }

    protected function getAvailableTables(): array
    {
        try {
            $database = DB::connection()->getDatabaseName();
            $tables = DB::select('SHOW TABLES');
            $tableKey = "Tables_in_{$database}";

            return array_map(fn ($table) => $table->$tableKey, $tables);
        } catch (\Exception $e) {
            return [];
        }
    }

    protected function displayAvailableMenus(): void
    {
        $gmenuList = DB::table('sys_gmenu')
            ->where('isactive', '1')
            ->select('gmenu', 'name')
            ->get();

        if ($gmenuList->isNotEmpty()) {
            $this->newLine();
            $this->line('  <fg=gray>Menu Grup yang Tersedia:</>');
            foreach ($gmenuList as $gmenu) {
                $this->line("    <fg=cyan>▸</> {$gmenu->gmenu} <fg=gray>→</> {$gmenu->name}");
            }
            $this->newLine();
        }
    }

    protected function validateTableExists(): void
    {
        $this->generator = new MSJModuleGenerator($this->moduleData);
        $this->moduleData['url'] = $this->generator->getConfig('url');

        $columns = $this->generator->getTableColumns($this->moduleData['table']);

        if (empty($columns)) {
            $this->displayError("Tabel '{$this->moduleData['table']}' tidak ditemukan!");
            $this->line('  <fg=gray>Silakan buat tabel terlebih dahulu.</>');
        } else {
            $this->displaySuccess("Tabel '{$this->moduleData['table']}' ditemukan dengan ".count($columns).' kolom');
        }
    }

    protected function collectFieldsConfig(): void
    {
        $this->displayStep('Step 3/4', 'Pengaturan Field');

        $columns = $this->generator->getTableColumns($this->moduleData['table']);

        if (! empty($columns) && prompt_confirm('Deteksi field secara otomatis dari database?', default: true, command: $this)) {
            $this->moduleData['fields'] = $this->generator->mapColumnsToFields($columns);
            $this->displaySuccess('Diaturkan secara otomatis '.count($this->moduleData['fields']).' field');
        } else {
            $this->moduleData['fields'] = $this->collectFieldsManually();
        }

        $this->newLine();
    }

    protected function collectFieldsManually(): array
    {
        $fields = [];
        $this->line('  <fg=gray>Menambahkan field secara manual...</>');
        $this->newLine();

        // Get existing fields from table for suggestions
        $existingFields = [];
        if (! empty($this->moduleData['table'])) {
            try {
                $columns = DB::select("SHOW COLUMNS FROM `{$this->moduleData['table']}`");
                $existingFields = array_map(fn ($col) => $col->Field, $columns);
            } catch (\Exception $e) {
                // Ignore
            }
        }

        do {
            if (! empty($existingFields) && count($existingFields) <= 20) {
                // Jika field sedikit, gunakan select
                $fieldOptions = array_combine($existingFields, $existingFields);
                $fieldName = prompt_select(
                    label: 'Field name',
                    options: $fieldOptions,
                    scroll: 15,
                    command: $this
                );
            } elseif (! empty($existingFields)) {
                // Jika banyak field, gunakan search
                $selectedField = prompt_search(
                    label: 'Field name',
                    options: fn ($value) => ! empty($value)
                        ? array_values(array_filter($existingFields, fn ($field) => stripos($field, $value) !== false))
                        : array_slice($existingFields, 0, 15),
                    placeholder: 'Ketik untuk mencari...',
                    command: $this
                );

                $fieldName = is_string($selectedField) && in_array($selectedField, $existingFields)
                    ? $selectedField
                    : prompt_text('Field name', required: true, command: $this);
            } else {
                $fieldName = prompt_text('Field name', required: true, command: $this);
            }

            $fieldAlias = prompt_text(
                label: 'Field label',
                default: ucwords(str_replace('_', ' ', $fieldName)),
                required: false,
                command: $this
            );

            $fieldType = prompt_select(
                label: 'Field type',
                options: [
                    'string' => 'String',
                    'text' => 'Text',
                    'number' => 'Number',
                    'date' => 'Date',
                    'datetime' => 'DateTime',
                    'boolean' => 'Boolean',
                ],
                default: 'string',
                scroll: 10,
                command: $this
            );

            $validationRules = [
                'nullable' => 'Nullable',
                'required' => 'Required',
                'required|string' => 'Required String',
                'required|numeric' => 'Required Numeric',
                'required|date' => 'Required Date',
                'nullable|string' => 'Nullable String',
            ];

            $validate = prompt_select(
                label: 'Aturan Validation',
                options: $validationRules,
                default: 'nullable',
                scroll: 10,
                command: $this
            );

            $fields[] = [
                'field' => $fieldName,
                'alias' => $fieldAlias,
                'type' => $fieldType,
                'validate' => $validate,
                'urut' => count($fields) + 1,
            ];
        } while (prompt_confirm('Tambahkan field lainnya?', default: true, command: $this));

        return $fields;
    }

    protected function displaySummary(): void
    {
        $this->displayStep('Step 4/4', 'Ringkasan Pengaturan');

        $this->newLine();
        $this->table(
            ['<fg=cyan>Property</>', '<fg=cyan>Value</>'],
            [
                ['<fg=gray>Layout</>', "<fg=white>{$this->moduleData['layout']}</>"],
                ['<fg=gray>Group Menu</>', "<fg=white>{$this->moduleData['gmenu']}</>"],
                ['<fg=gray>Detail Menu</>', "<fg=white>{$this->moduleData['dmenu']}</>"],
                ['<fg=gray>Menu Name</>', "<fg=white>{$this->moduleData['menu_name']}</>"],
                ['<fg=gray>URL</>', "<fg=cyan>{$this->moduleData['url']}</>"],
                ['<fg=gray>Table</>', "<fg=yellow>{$this->moduleData['table']}</>"],
                ['<fg=gray>Fields</>', '<fg=white>'.count($this->moduleData['fields']).' fields</>'],
            ]
        );

        if (prompt_confirm('Tampilkan detail field?', default: false, command: $this)) {
            $this->displayFieldsTable();
        }

        $this->newLine();
    }

    protected function displayFieldsTable(): void
    {
        $this->newLine();

        $rows = array_map(fn ($f) => [
            "<fg=white>{$f['field']}</>",
            "<fg=gray>{$f['alias']}</>",
            "<fg=cyan>{$f['type']}</>",
            $f['primary'] ?? '0' ? '<fg=green>✓</>' : '<fg=gray>-</>',
        ], $this->moduleData['fields']);

        $this->table(
            ['<fg=cyan>Field</>', '<fg=cyan>Alias</>', '<fg=cyan>Type</>', '<fg=cyan>Primary</>'],
            $rows
        );
    }

    protected function confirmGeneration(): bool
    {
        $this->newLine();

        if (! prompt_confirm('Generate modul dengan pengaturan di atas?', default: true, command: $this)) {
            $this->displayWarning('Generate dibatalkan');

            return false;
        }

        return true;
    }

    protected function generateModule(): void
    {
        $this->displayValidationSection();

        $this->generator->setConfig($this->moduleData);
        $validation = $this->generator->validateBeforeGenerate();

        if (! empty($validation['errors'])) {
            $this->displayValidationErrors($validation['errors']);

            return;
        }

        if (! empty($validation['warnings'])) {
            if (! $this->displayValidationWarnings($validation['warnings'])) {
                return;
            }
        }

        $this->displayGenerationSection();
        $results = $this->runGeneration();
        $this->displayResults($results);
    }

    protected function displayValidationSection(): void
    {
        $this->newLine();
        $this->line('  <fg=bright-cyan>┌─ <fg=white>⚡ Validation</>');
        $this->line('  <fg=bright-cyan>│</>');
    }

    protected function displayValidationErrors(array $errors): void
    {
        foreach ($errors as $error) {
            $this->line("  <fg=bright-cyan>│</> <fg=red>✗</> {$error}");
        }
        $this->line('  <fg=bright-cyan>└─</>');
        $this->newLine();
        $this->displayError('Generate dibatalkan karena ada error');
    }

    protected function displayValidationWarnings(array $warnings): bool
    {
        $this->line('  <fg=bright-cyan>│</> <fg=yellow>⚠</> <fg=yellow>Warnings detected:</>');
        foreach ($warnings as $warning) {
            $this->line("  <fg=bright-cyan>│</> <fg=gray>  •</> {$warning}");
        }
        $this->line('  <fg=bright-cyan>└─</>');
        $this->newLine();

        if (! prompt_confirm('Files/data sudah ada. Lanjutkan? (akan diperbarui/ditimpa)', default: true, command: $this)) {
            $this->displayWarning('Generate dibatalkan oleh pengguna');

            return false;
        }

        return true;
    }

    protected function displayGenerationSection(): void
    {
        $this->newLine();
        $this->line('  <fg=bright-cyan>┌─ <fg=white>MenGenerate Module</>');
        $this->line('  <fg=bright-cyan>│</>');
    }

    protected function runGeneration(): array
    {
        $results = [
            'Model' => $this->generator->generateModel(),
            'Menu Registration' => $this->generator->registerMenu(),
            'Table Config' => $this->generator->registerTableConfig(),
            'Authorization' => $this->generator->registerAuthorization(),
        ];

        if ($this->moduleData['layout'] === 'manual') {
            $results['Controller'] = $this->generator->generateController();
            $results['Views'] = $this->generator->generateViews();
            $results['JavaScript'] = $this->generator->generateJavascript();
        }

        return $results;
    }

    protected function displayResults(array $results): void
    {
        foreach ($results as $component => $result) {
            $icon = match ($result['status']) {
                'success', 'created' => '<fg=green>✓</>',
                'updated' => '<fg=blue>↻</>',
                'skipped' => '<fg=yellow>⊝</>',
                'error' => '<fg=red>✗</>',
                default => '<fg=gray>•</>',
            };

            $color = match ($result['status']) {
                'success', 'created' => 'green',
                'updated' => 'blue',
                'skipped' => 'yellow',
                'error' => 'red',
                default => 'gray',
            };

            $message = $result['message'] ?? $result['status'];
            $this->line("  <fg=bright-cyan>│</> {$icon} <fg={$color}>{$component}:</> <fg=gray>{$message}</>");
        }

        $this->line('  <fg=bright-cyan>└─</>');
        $this->displayCompletionBox();
    }

    protected function displayCompletionBox(): void
    {
        $this->newLine();
        $this->displayHeader('Generate Selesai', '🎉');
        $this->line("<fg=gray>📍 Akses menu Anda di:</> <fg=cyan>/{$this->moduleData['url']}</>");
        
        // Auto save to seeder
        $this->autoSaveToSeeder();
        
        $this->newLine();
    }

    protected function autoSaveToSeeder(): void
    {
        $this->newLine();
        $this->line('  <fg=bright-cyan>┌─ <fg=white>💾 Auto Save to Seeder</>');
        $this->line('  <fg=bright-cyan>│</>');
        
        if (prompt_confirm('Simpan konfigurasi menu ke seeder?', default: true, command: $this)) {
            $this->line('  <fg=bright-cyan>│</> <fg=yellow>⚡</> Menyimpan menu dan table config...');
            
            try {
                // Generate prefix from gmenu/dmenu
                $prefix = $this->generateSeederPrefix();
                
                // Save menus using new seeder command
                $this->call('msj:make:seeder', [
                    'type' => 'menu', 
                    '--auto' => true,
                    '--prefix' => $prefix,
                    '--gmenu' => $this->moduleData['gmenu'],
                    '--dmenu' => $this->moduleData['dmenu']
                ]);
                $this->line("  <fg=bright-cyan>│</> <fg=green>✓</> Menu berhasil disimpan ke {$prefix}MenuSeeder");
                
                // Save table config using new seeder command
                $this->call('msj:make:seeder', [
                    'type' => 'table', 
                    '--auto' => true,
                    '--prefix' => $prefix,
                    '--gmenu' => $this->moduleData['gmenu'],
                    '--dmenu' => $this->moduleData['dmenu']
                ]);
                $this->line("  <fg=bright-cyan>│</> <fg=green>✓</> Table config berhasil disimpan ke {$prefix}TableSeeder");
                
            } catch (\Exception $e) {
                $this->line('  <fg=bright-cyan>│</> <fg=red>✗</> Error: ' . $e->getMessage());
            }
        } else {
            $this->line('  <fg=bright-cyan>│</> <fg=gray>⊝</> Auto save dilewati');
        }
        
        $this->line('  <fg=bright-cyan>└─</>');
    }

    protected function displayStep(string $step, string $title): void
    {
        $this->newLine();
        
        if ($this->isWindowsNative()) {
            $this->line("  {$step}: {$title}");
            $this->line('  ');
        } else {
            $this->line("  <fg=bright-cyan>┌─ <fg=white>{$step}: {$title}</>");
            $this->line('  <fg=bright-cyan>│</>');
        }
    }

    protected function displaySuccess(string $message): void
    {
        if ($this->isWindowsNative()) {
            $this->line("  [OK] {$message}");
        } else {
            $this->line("  <fg=bright-cyan>│</> <fg=green>✓</> {$message}");
        }
    }

    protected function displayWarning(string $message): void
    {
        if ($this->isWindowsNative()) {
            $this->line("  [WARNING] {$message}");
        } else {
            $this->line("  <fg=bright-cyan>│</> <fg=yellow>⚠</> {$message}");
        }
    }

    protected function displayError(string $message): void
    {
        if ($this->isWindowsNative()) {
            $this->line("  [ERROR] {$message}");
        } else {
            $this->line("  <fg=red>✗</> {$message}");
        }
    }

    protected function createNewGmenuViaCommand(): ?string
    {
        $this->newLine();
        $this->line('  <fg=bright-cyan>│</> <fg=yellow>📝</> Membuat Group Menu Baru via Command');
        
        $exitCode = $this->call('msj:make:gmenu');
        
        if ($exitCode !== 0) {
            $this->line('  <fg=bright-cyan>│</> <fg=red>✗</> Gagal membuat Group Menu');
            return null; // Return null to signal failure
        }
        
        // Get the latest created gmenu
        $latestGmenu = DB::table('sys_gmenu')
            ->where('isactive', '1')
            ->orderBy('created_at', 'desc')
            ->first();
            
        return $latestGmenu ? $latestGmenu->gmenu : null;
    }

    protected function createNewDmenuViaCommand(): ?string
    {
        $this->newLine();
        $this->line('  <fg=bright-cyan>│</> <fg=yellow>📝</> Membuat Detail Menu Baru via Command');
        
        $gmenuCode = $this->moduleData['gmenu'] ?? 'KOP001';
        
        $exitCode = $this->call('msj:make:dmenu', ['--gmenu' => $gmenuCode]);
        
        if ($exitCode !== 0) {
            $this->line('  <fg=bright-cyan>│</> <fg=red>✗</> Gagal membuat Detail Menu');
            return null; // Return null to signal failure
        }
        
        // Get the latest created dmenu
        $latestDmenu = DB::table('sys_dmenu')
            ->where('isactive', '1')
            ->where('gmenu', $gmenuCode)
            ->orderBy('created_at', 'desc')
            ->first();
            
        return $latestDmenu ? $latestDmenu->dmenu : null;
    }

    protected function generateSeederPrefix(): string
    {
        $gmenu = $this->moduleData['gmenu'] ?? 'MSJ';
        $dmenu = $this->moduleData['dmenu'] ?? '';
        
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
