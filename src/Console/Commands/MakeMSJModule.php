<?php

namespace MSJFramework\LaravelGenerator\Console\Commands;

use MSJFramework\LaravelGenerator\Console\Commands\Concerns\HasConsoleStyling;
use MSJFramework\LaravelGenerator\Services\MSJModuleGenerator;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

use function Laravel\Prompts\confirm;
use function Laravel\Prompts\search;
use function Laravel\Prompts\select;
use function Laravel\Prompts\text;

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
        $this->collectModuleData();

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

    protected function collectModuleData(): void
    {
        $this->collectLayoutType();
        $this->collectBasicInfo();
        $this->collectFieldsConfig();
        $this->displaySummary();
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
        $this->moduleData['layout'] = select(
            label: 'Pilih Layout',
            options: $layouts,
            default: 'manual',
            scroll: 10
        );

        $this->displaySuccess("Layout yang dipilih adalah: {$this->moduleData['layout']}");
        $this->newLine(2);
    }

    protected function collectBasicInfo(): void
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
            $this->moduleData['gmenu'] = select(
                label: 'Pilih Kode Group Menu (gmenu)',
                options: $gmenuList,
                default: in_array('KOP001', array_keys($gmenuList)) ? 'KOP001' : array_key_first($gmenuList),
                scroll: 10
            );
        } else {
            $this->moduleData['gmenu'] = text('Masukkan Kode Group Menu (gmenu)', default: 'KOP001');
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
            $this->moduleData['dmenu'] = search(
                label: 'Masukkan Kode Direktori Menu (dmenu)',
                options: fn ($value) => ! empty($value)
                    ? array_filter($dmenuList, fn ($dmenu) => stripos($dmenu, $value) !== false)
                    : array_merge(['KOP999'], array_slice($dmenuList, 0, 9)),
                placeholder: 'Ketik untuk mencari... (default: KOP999)'
            ) ?: 'KOP999';
        } else {
            $this->moduleData['dmenu'] = text('Masukkan Kode Direktori Menu (dmenu)', default: 'KOP999');
        }

        // Menu Name
        $this->moduleData['menu_name'] = text('Masukkan Nama Menu', default: 'Data Example', required: true);

        // URL dengan auto-suggest dari menu_name
        $suggestedUrl = Str::slug($this->moduleData['menu_name']);
        $this->moduleData['url'] = text(
            label: 'Masukkan URL',
            default: $suggestedUrl,
            placeholder: $suggestedUrl
        );

        // Table dengan search dari database
        $tables = $this->getAvailableTables();
        if (! empty($tables)) {
            $selectedTable = search(
                label: 'Pilih Nama Tabel Database',
                options: fn ($value) => ! empty($value)
                    ? array_values(array_filter($tables, fn ($table) => stripos($table, $value) !== false))
                    : array_slice($tables, 0, 15),
                placeholder: 'Ketik untuk mencari tabel...'
            );

            // Validasi hasil search
            $this->moduleData['table'] = is_string($selectedTable) && in_array($selectedTable, $tables)
                ? $selectedTable
                : (in_array('mst_example', $tables) ? 'mst_example' : ($tables[0] ?? 'mst_example'));
        } else {
            $this->moduleData['table'] = text('Masukkan Nama Tabel Database', default: 'mst_example', required: true);
        }

        $this->validateTableExists();

        $this->displaySuccess('Informasi dasar telah dikumpulkan dan validasi tabel berhasil');
        $this->newLine(2);
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

        if (! empty($columns) && confirm('Deteksi field secara otomatis dari database?', default: true)) {
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
                $fieldName = select(
                    label: 'Field name',
                    options: $fieldOptions,
                    scroll: 15
                );
            } elseif (! empty($existingFields)) {
                // Jika banyak field, gunakan search
                $selectedField = search(
                    label: 'Field name',
                    options: fn ($value) => ! empty($value)
                        ? array_values(array_filter($existingFields, fn ($field) => stripos($field, $value) !== false))
                        : array_slice($existingFields, 0, 15),
                    placeholder: 'Ketik untuk mencari...'
                );

                $fieldName = is_string($selectedField) && in_array($selectedField, $existingFields)
                    ? $selectedField
                    : text('Field name', required: true);
            } else {
                $fieldName = text('Field name', required: true);
            }

            $fieldAlias = text(
                label: 'Field label',
                default: ucwords(str_replace('_', ' ', $fieldName)),
                placeholder: ucwords(str_replace('_', ' ', $fieldName))
            );

            $fieldType = select(
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
                scroll: 10
            );

            $validationRules = [
                'nullable' => 'Nullable',
                'required' => 'Required',
                'required|string' => 'Required String',
                'required|numeric' => 'Required Numeric',
                'required|date' => 'Required Date',
                'nullable|string' => 'Nullable String',
            ];

            $validate = select(
                label: 'Aturan Validation',
                options: $validationRules,
                default: 'nullable',
                scroll: 10
            );

            $fields[] = [
                'field' => $fieldName,
                'alias' => $fieldAlias,
                'type' => $fieldType,
                'validate' => $validate,
                'urut' => count($fields) + 1,
            ];
        } while (confirm('Tambahkan field lainnya?', default: true));

        return $fields;
    }

    protected function displaySummary(): void
    {
        $this->displayStep('Step 4/4', 'Ringkasan Pengaturan');

        $this->newLine();
        $this->table(
            ['<fg=cyan;options=bold>Property</>', '<fg=cyan;options=bold>Value</>'],
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

        if (confirm('Tampilkan detail field?', default: false)) {
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
            ['<fg=cyan;options=bold>Field</>', '<fg=cyan;options=bold>Alias</>', '<fg=cyan;options=bold>Type</>', '<fg=cyan;options=bold>Primary</>'],
            $rows
        );
    }

    protected function confirmGeneration(): bool
    {
        $this->newLine();

        if (! confirm('Generate modul dengan pengaturan di atas?', default: true)) {
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
        $this->line('  <fg=bright-cyan>┌─ <fg=white;options=bold>⚡ Validation</>');
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
        $this->line('  <fg=bright-cyan>│</> <fg=yellow>⚠</> <fg=yellow;options=bold>Warnings detected:</>');
        foreach ($warnings as $warning) {
            $this->line("  <fg=bright-cyan>│</> <fg=gray>  •</> {$warning}");
        }
        $this->line('  <fg=bright-cyan>└─</>');
        $this->newLine();

        if (! confirm('Files/data sudah ada. Lanjutkan? (akan diperbarui/ditimpa)', default: true)) {
            $this->displayWarning('Generate dibatalkan oleh pengguna');

            return false;
        }

        return true;
    }

    protected function displayGenerationSection(): void
    {
        $this->newLine();
        $this->line('  <fg=bright-cyan>┌─ <fg=white;options=bold>MenGenerate Module</>');
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
        $this->line("<fg=gray>📍 Akses menu Anda di:</> <fg=cyan;options=bold>/{$this->moduleData['url']}</>");
        $this->newLine();
    }

    protected function displayStep(string $step, string $title): void
    {
        $this->newLine();
        $this->line("  <fg=bright-cyan>┌─ <fg=white;options=bold>{$step}: {$title}</>");
        $this->line('  <fg=bright-cyan>│</>');
    }

    protected function displaySuccess(string $message): void
    {
        $this->line("  <fg=bright-cyan>│</> <fg=green>✓</> {$message}");
    }

    protected function displayWarning(string $message): void
    {
        $this->line("  <fg=bright-cyan>│</> <fg=yellow>⚠</> {$message}");
    }

    protected function displayError(string $message): void
    {
        $this->line("  <fg=red>✗</> {$message}");
    }
}
