<?php

namespace MSJFramework\LaravelGenerator\Console\Commands;

use MSJFramework\LaravelGenerator\Console\Commands\Concerns\HasConsoleStyling;
use MSJFramework\LaravelGenerator\Services\MSJModuleGenerator;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

use function Laravel\Prompts\select;

class MakeMSJModel extends Command
{
    use HasConsoleStyling;

    protected $signature = 'msj:make:model {table?} {--force : Menimpa model yang sudah ada}';

    protected $description = 'Generate Model MSJ dari tabel database';

    public function handle(): int
    {
        $this->displayHeader('Generator Model MSJ', '📦');

        // Table dengan select dari database
        $table = $this->argument('table');
        if (! $table) {
            $tables = $this->getAvailableTables();
            if (! empty($tables)) {
                $tableOptions = array_combine($tables, $tables);
                $table = select(
                    label: 'Pilih Nama Tabel Database',
                    options: $tableOptions,
                    default: in_array('mst_example', $tables) ? 'mst_example' : ($tables[0] ?? 'mst_example'),
                    scroll: 15
                );
            } else {
                $this->badge('error', 'Tidak ada tabel yang ditemukan di database!');

                return Command::FAILURE;
            }
        }

        $force = $this->option('force');

        $generator = new MSJModuleGenerator;
        $columns = $generator->getTableColumns($table);

        if (empty($columns)) {
            $this->badge('error', "Tabel '{$table}' tidak ditemukan!");

            return Command::FAILURE;
        }

        $config = [
            'table' => $table,
        ];

        $generator->setConfig($config);
        $config['fields'] = $generator->mapColumnsToFields($columns);
        $generator->setConfig($config);

        $this->section('Generate Model');

        $result = $generator->generateModel();

        if ($result['status'] === 'success' || $result['status'] === 'created' || $result['status'] === 'updated') {
            $badgeType = $result['status'] === 'updated' ? 'completed' : 'success';
            $this->badge($badgeType, "Model: {$result['message']}");
            $this->line("<fg=gray>📍 Path:</> <fg=white>{$result['path']}</>");
            $this->newLine();
            $this->line('<fg=gray>💡</> <fg=white>Model termasuk field fillable yang dihasilkan secara otomatis dari struktur tabel</>');

            return Command::SUCCESS;
        }

        if ($result['status'] === 'skipped' && $force) {
            $this->badge('warning', "Model sudah ada tetapi opsi force tidak diterapkan: {$result['message']}");
        } else {
            $this->badge('warning', "Model gagal dibuat: {$result['message']}");
        }

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
