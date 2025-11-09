<?php

namespace MSJFramework\LaravelGenerator\Console\Commands;

use MSJFramework\LaravelGenerator\Console\Commands\Concerns\HasConsoleStyling;
use MSJFramework\LaravelGenerator\Services\MSJModuleGenerator;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

use function Laravel\Prompts\select;
use function Laravel\Prompts\text;

class MakeMSJCrud extends Command
{
    use HasConsoleStyling;

    protected $signature = 'msj:make:crud {table} {--gmenu= : Group menu code} {--dmenu= : Detail menu code} {--layout=manual : Layout type}';

    protected $description = 'Generator CRUD MSJ (CRUD Minimal)';

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
            $gmenu = select(
                label: 'Pilih Kode Group Menu (gmenu)',
                options: $gmenuList,
                default: in_array('KOP001', array_keys($gmenuList)) ? 'KOP001' : array_key_first($gmenuList),
                scroll: 10
            );
        } else {
            $gmenu = $this->option('gmenu') ?? text('Masukkan Kode Group Menu (gmenu)', default: 'KOP001');
        }

        // DMenu dengan anticipate
        $dmenuList = DB::table('sys_dmenu')
            ->where('isactive', '1')
            ->select('dmenu')
            ->orderBy('dmenu', 'desc')
            ->limit(10)
            ->pluck('dmenu')
            ->toArray();

        if (! $this->option('dmenu')) {
            $dmenu = text(
                label: 'Masukkan Kode Direktori Menu (dmenu)',
                default: 'KOP999',
                placeholder: 'KOP999'
            );
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
}
