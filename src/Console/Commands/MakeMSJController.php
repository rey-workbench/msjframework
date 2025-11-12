<?php

namespace MSJFramework\LaravelGenerator\Console\Commands;

use MSJFramework\LaravelGenerator\Console\Commands\Concerns\HasConsoleStyling;
use MSJFramework\LaravelGenerator\Console\Commands\Concerns\HasDatabaseOperations;
use MSJFramework\LaravelGenerator\Console\Commands\Concerns\HasMenuOperations;
use MSJFramework\LaravelGenerator\Services\MSJModuleGenerator;
use Illuminate\Console\Command;

// Import safe prompt helpers that work on all platforms

class MakeMSJController extends Command
{
    use HasConsoleStyling, HasDatabaseOperations, HasMenuOperations;

    protected $signature = 'msj:make:controller {name} {--table= : Database table name} {--gmenu= : Group menu code} {--url= : URL slug}';

    protected $description = 'Generate Controller MSJ dengan metode CRUD dasar';

    public function handle(): int
    {
        $this->displayHeader('Generator Controller MSJ');

        $name = $this->argument('name');

        // Table dengan select dari database
        $table = $this->option('table') ?? $this->searchAndSelectTable('mst_example');

        // GMenu dengan select
        $gmenu = $this->option('gmenu') ?? $this->selectOrCreateGmenu('KOP001');
        if ($gmenu === null) {
            return Command::FAILURE;
        }

        $url = $this->option('url') ?? prompt_text('Masukkan Slug URL', required: true, command: $this);

        $generator = new MSJModuleGenerator;
        $columns = $generator->getTableColumns($table);

        if (empty($columns)) {
            $this->badge('error', "Tabel '{$table}' tidak ditemukan!");

            return Command::FAILURE;
        }

        $config = [
            'gmenu' => $gmenu,
            'dmenu' => str_replace('Controller', '', $name),
            'url' => $url,
            'table' => $table,
        ];

        $generator->setConfig($config);
        $config['fields'] = $generator->mapColumnsToFields($columns);
        $generator->setConfig($config);

        $this->section('Generate Controller');

        $result = $generator->generateController();

        if ($result['status'] === 'success' || $result['status'] === 'created' || $result['status'] === 'updated') {
            $badgeType = $result['status'] === 'updated' ? 'completed' : 'success';
            $this->badge($badgeType, "Controller: {$result['message']}");
            $this->line("<fg=gray>📍 Path:</> <fg=white>{$result['path']}</>");

            return Command::SUCCESS;
        }

        $this->badge('warning', "Controller gagal dibuat: {$result['message']}");

        return Command::SUCCESS;
    }

    // Method moved to HasDatabaseOperations trait
}
