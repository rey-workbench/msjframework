<?php

namespace MSJFramework\LaravelGenerator\Console\Commands;

use MSJFramework\LaravelGenerator\Console\Commands\Concerns\HasConsoleStyling;
use MSJFramework\LaravelGenerator\Console\Commands\Concerns\HasDatabaseOperations;
use MSJFramework\LaravelGenerator\Console\Commands\Concerns\HasValidation;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

use function Laravel\Prompts\select;
use function Laravel\Prompts\text;

class MakeMSJDmenu extends Command
{
    use HasConsoleStyling, HasDatabaseOperations, HasValidation;

    protected $signature = 'msj:make:dmenu {code?} {name?} {--gmenu=} {--urut=999}';
    protected $description = 'Create new Detail Menu (dmenu)';

    public function handle(): int
    {
        $this->displayHeader('Create Detail Menu');

        $dmenuCode = $this->argument('code') ?: text(
            label: 'Kode Detail Menu (dmenu)',
            placeholder: 'KOP999',
            required: true,
            validate: fn($value) => $this->validateDmenuCode($value)
        );

        $dmenuName = $this->argument('name') ?: text(
            label: 'Nama Detail Menu',
            placeholder: 'Data Example',
            required: true,
            validate: fn($value) => $this->validateName($value)
        );

        // Pilih gmenu
        $gmenuCode = $this->option('gmenu');
        if (!$gmenuCode) {
            $gmenuList = $this->getActiveGmenus();

            if (!empty($gmenuList)) {
                $gmenuCode = select(
                    label: 'Pilih Group Menu (gmenu)',
                    options: $gmenuList,
                    default: array_key_first($gmenuList),
                    scroll: 10
                );
            } else {
                $this->badge('error', 'Tidak ada Group Menu yang tersedia. Buat gmenu terlebih dahulu.');
                return Command::FAILURE;
            }
        }

        $dmenuUrut = (int) ($this->option('urut') ?: text(
            label: 'Urutan',
            default: '999',
            validate: fn($value) => $this->validateNumeric($value)
        ));

        // Insert ke database dengan semua field yang diperlukan
        DB::table('sys_dmenu')->insert([
            'dmenu' => $dmenuCode,
            'gmenu' => $gmenuCode,
            'urut' => $dmenuUrut,
            'name' => $dmenuName,
            'icon' => null,
            'url' => '', // Empty string untuk field yang required tapi bisa kosong
            'tabel' => null,
            'layout' => 'master',
            'sub' => null,
            'show' => '1',
            'js' => '0',
            'isactive' => '1',
            'created_at' => now(),
            'updated_at' => now(),
            'user_create' => 'msj:make:dmenu',
            'user_update' => null,
        ]);

        $this->badge('success', "Detail Menu '{$dmenuCode} - {$dmenuName}' berhasil dibuat");

        return Command::SUCCESS;
    }
}
