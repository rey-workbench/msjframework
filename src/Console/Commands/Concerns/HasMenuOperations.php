<?php

namespace MSJFramework\LaravelGenerator\Console\Commands\Concerns;

use Illuminate\Support\Str;
// Import safe prompt helpers that work on all platforms

trait HasMenuOperations
{
    use HasDatabaseOperations;

    /**
     * Select or create gmenu interactively
     */
    protected function selectOrCreateGmenu(?string $defaultGmenu = null): ?string
    {
        $gmenus = $this->getActiveGmenus();

        if (empty($gmenus)) {
            $this->warn('Tidak ada group menu yang tersedia. Membuat baru...');
            return $this->createGmenuViaCommand();
        }

        if ($defaultGmenu && isset($gmenus[$defaultGmenu])) {
            return $defaultGmenu;
        }

        $gmenuOptions = ['__create_new__' => '+ Buat Group Menu Baru'] + $gmenus;

        $selectedGmenu = prompt_select(
            label: 'Pilih Group Menu (gmenu)',
            options: $gmenuOptions,
            default: '__create_new__',
            scroll: 10,
            command: $this
        );

        if ($selectedGmenu === '__create_new__') {
            return $this->createGmenuViaCommand();
        }

        return $selectedGmenu;
    }

    /**
     * Select or create dmenu interactively
     */
    protected function selectOrCreateDmenu(string $gmenu, ?string $defaultDmenu = null): ?string
    {
        $dmenus = $this->getActiveDmenus($gmenu);

        if ($defaultDmenu && isset($dmenus[$defaultDmenu])) {
            return $defaultDmenu;
        }

        if (empty($dmenus)) {
            $this->warn('Tidak ada detail menu yang tersedia. Membuat baru...');
            return $this->createDmenuViaCommand($gmenu);
        }

        $dmenuOptions = ['__create_new__' => '+ Buat Detail Menu Baru'] + $dmenus;

        $selectedDmenu = prompt_select(
            label: 'Pilih Detail Menu (dmenu)',
            options: $dmenuOptions,
            default: '__create_new__',
            scroll: 10,
            command: $this
        );

        if ($selectedDmenu === '__create_new__') {
            return $this->createDmenuViaCommand($gmenu);
        }

        return $selectedDmenu;
    }

    /**
     * Search and select table
     */
    protected function searchAndSelectTable(?string $defaultTable = null): string
    {
        $tables = $this->getAvailableTables();

        if (empty($tables)) {
            return prompt_text(
                label: 'Masukkan Nama Tabel Database',
                default: $defaultTable ?? 'mst_example',
                required: true,
                command: $this
            );
        }

        if ($defaultTable && in_array($defaultTable, $tables)) {
            return $defaultTable;
        }

        $selectedTable = prompt_search(
            label: 'Pilih Nama Tabel Database',
            options: fn ($value) => !empty($value)
                ? array_values(array_filter($tables, fn ($table) => stripos($table, $value) !== false))
                : array_slice($tables, 0, 15),
            placeholder: 'Ketik untuk mencari tabel...',
            command: $this
        );

        return is_string($selectedTable) && in_array($selectedTable, $tables)
            ? $selectedTable
            : ($tables[0] ?? 'mst_example');
    }

    /**
     * Create gmenu via command
     */
    protected function createGmenuViaCommand(): ?string
    {
        $this->newLine();
        $this->info('📝 Membuat Group Menu Baru...');

        $exitCode = $this->call('msj:make:gmenu');

        if ($exitCode !== 0) {
            $this->error('Gagal membuat Group Menu');
            return null; // Return null to signal failure
        }

        $latestGmenu = $this->getLatestGmenu();
        return $latestGmenu ? $latestGmenu->gmenu : null;
    }

    /**
     * Create dmenu via command
     */
    protected function createDmenuViaCommand(string $gmenu): ?string
    {
        $this->newLine();
        $this->info('📝 Membuat Detail Menu Baru...');

        $exitCode = $this->call('msj:make:dmenu', ['--gmenu' => $gmenu]);

        if ($exitCode !== 0) {
            $this->error('Gagal membuat Detail Menu');
            return null; // Return null to signal failure
        }

        $latestDmenu = $this->getLatestDmenu($gmenu);
        return $latestDmenu ? $latestDmenu->dmenu : null;
    }

    /**
     * Generate smart prefix for seeders
     */
    protected function generateSeederPrefix(?string $gmenu = null, ?string $dmenu = null): string
    {
        if ($dmenu) {
            $prefix = Str::studly(str_replace(['_', '-'], '', $dmenu));
        } elseif ($gmenu) {
            $prefix = Str::studly(str_replace(['_', '-'], '', $gmenu));
        } else {
            $prefix = 'MSJ';
        }

        // Clean up common prefixes/suffixes
        $prefix = preg_replace('/^(Kop|Msj|Sys)/', '', $prefix);
        $prefix = preg_replace('/\d+$/', '', $prefix);

        // Ensure it's not empty and has reasonable length
        if (empty($prefix) || strlen($prefix) < 2) {
            $prefix = 'MSJ';
        }

        return ucfirst($prefix);
    }
}
