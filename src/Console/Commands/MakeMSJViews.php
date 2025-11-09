<?php

namespace MSJFramework\LaravelGenerator\Console\Commands;

use MSJFramework\LaravelGenerator\Console\Commands\Concerns\HasConsoleStyling;
use MSJFramework\LaravelGenerator\Services\MSJModuleGenerator;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

use function Laravel\Prompts\select;
use function Laravel\Prompts\text;

class MakeMSJViews extends Command
{
    use HasConsoleStyling;

    protected $signature = 'msj:make:views {--gmenu= : Group menu code} {--url= : URL slug} {--table= : Table name}';

    protected $description = 'Generate Views MSJ (list, create, edit, show)';

    public function handle(): int
    {
        $this->displayHeader('Generator Views MSJ', '🎨');

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

        $dmenu = text('Masukkan Direktori Menu (dmenu)', default: 'KOP999');

        $generator = new MSJModuleGenerator;
        $columns = $generator->getTableColumns($table);

        if (empty($columns)) {
            $this->badge('warning', "Tabel '{$table}' tidak ditemukan. Generate views dasar...");
            $columns = [];
        }

        $config = [
            'gmenu' => $gmenu,
            'dmenu' => $dmenu,
            'url' => $url,
            'table' => $table,
            'menu_name' => ucwords(str_replace('-', ' ', $url)),
        ];

        $generator->setConfig($config);
        $config['fields'] = empty($columns) ? [] : $generator->mapColumnsToFields($columns);
        $generator->setConfig($config);

        $this->section('Generate Views');

        $result = $generator->generateViews();

        if ($result['status'] === 'success') {
            $this->badge('success', "Views: {$result['message']}");
            $this->line("<fg=gray>📍 Path:</> <fg=white>{$result['path']}</>");
            $this->newLine();
            $this->line('<fg=gray>Generated files:</>');
            $this->line('   <fg=green>•</> list.blade.php');
            $this->line('   <fg=green>•</> add.blade.php');
            $this->line('   <fg=green>•</> edit.blade.php');
            $this->line('   <fg=green>•</> show.blade.php');

            return Command::SUCCESS;
        }

        $this->badge('warning', "Views gagal dibuat: {$result['message']}");

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
