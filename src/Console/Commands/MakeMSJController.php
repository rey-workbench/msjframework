<?php

namespace MSJFramework\LaravelGenerator\Console\Commands;

use MSJFramework\LaravelGenerator\Console\Commands\Concerns\HasConsoleStyling;
use MSJFramework\LaravelGenerator\Services\MSJModuleGenerator;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

use function Laravel\Prompts\select;
use function Laravel\Prompts\text;

class MakeMSJController extends Command
{
    use HasConsoleStyling;

    protected $signature = 'msj:make:controller {name} {--table= : Database table name} {--gmenu= : Group menu code} {--url= : URL slug}';

    protected $description = 'Generate Controller MSJ dengan metode CRUD dasar';

    public function handle(): int
    {
        $this->displayHeader('Generator Controller MSJ');

        $name = $this->argument('name');

        // Table dengan select dari database
        $tables = $this->getAvailableTables();
        if (! empty($tables) && ! $this->option('table')) {
            $tableOptions = array_combine($tables, $tables);
            $table = select(
                label: 'Pilih Nama Tabel Database',
                options: $tableOptions,
                default: in_array('mst_example', $tables) ? 'mst_example' : ($tables[0] ?? 'mst_example'),
                scroll: 15
            );
        } else {
            $table = $this->option('table') ?? text('Masukkan Nama Tabel', default: 'mst_example');
        }

        // GMenu dengan select
        $gmenuList = DB::table('sys_gmenu')
            ->where('isactive', '1')
            ->select('gmenu', 'name')
            ->get()
            ->mapWithKeys(fn ($item) => [$item->gmenu => "{$item->gmenu} - {$item->name}"])
            ->toArray();

        if (! empty($gmenuList) && ! $this->option('gmenu')) {
            $gmenu = select(
                label: 'Pilih Kode Group Menu (gmenu)',
                options: $gmenuList,
                default: in_array('KOP001', array_keys($gmenuList)) ? 'KOP001' : array_key_first($gmenuList),
                scroll: 10
            );
        } else {
            $gmenu = $this->option('gmenu') ?? text('Masukkan Kode Group Menu (gmenu)', default: 'KOP001');
        }

        $url = $this->option('url') ?? text('Masukkan Slug URL', required: true);

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
}
