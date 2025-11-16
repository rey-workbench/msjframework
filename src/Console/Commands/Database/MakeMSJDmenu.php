<?php

namespace MSJFramework\LaravelGenerator\Console\Commands\Database;

use MSJFramework\LaravelGenerator\Console\Commands\Concerns\HasConsoleStyling;
use MSJFramework\LaravelGenerator\Console\Commands\Concerns\HasDatabaseOperations;
use MSJFramework\LaravelGenerator\Console\Commands\Concerns\HasValidation;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

// Import safe prompt helpers that work on all platforms

class MakeMSJDmenu extends Command
{
    use HasConsoleStyling, HasDatabaseOperations, HasValidation;

    protected $signature = 'msj:make:dmenu {code?} {name?} {--gmenu=} {--urut=999}';
    protected $description = 'Create new Detail Menu (dmenu)';

    public function handle(): int
    {
        $this->displayHeader('Create Detail Menu');

        $dmenuCode = $this->argument('code') ?: prompt_text(
            label: 'Kode Detail Menu (dmenu)',
            default: 'mygmenu',
            required: true,
            command: $this
        );
        
        // Validate dmenu code
        $validation = $this->validateDmenuCode($dmenuCode, 6);
        if ($validation) {
            $this->badge('error', $validation);
            return Command::FAILURE;
        }
        
        // Check duplicate
        if ($this->dmenuExists($dmenuCode) || $this->dmenuExists(strtoupper($dmenuCode)) || $this->dmenuExists(strtolower($dmenuCode))) {
            $this->badge('error', "Kode dmenu '{$dmenuCode}' sudah ada");
            return Command::FAILURE;
        }

        $dmenuName = $this->argument('name') ?: prompt_text(
            label: 'Nama Detail Menu',
            default: 'Data Example',
            required: true,
            command: $this
        );
        
        // Validate name
        $validation = $this->validateName($dmenuName, 2, 25);
        if ($validation) {
            $this->badge('error', $validation);
            return Command::FAILURE;
        }

        // Pilih gmenu
        $gmenuCode = $this->option('gmenu');
        if (!$gmenuCode) {
            $gmenuList = $this->getActiveGmenus();

            if (!empty($gmenuList)) {
                $gmenuCode = prompt_select(
                    label: 'Pilih Group Menu (gmenu)',
                    options: $gmenuList,
                    default: array_key_first($gmenuList),
                    scroll: 10,
                    command: $this
                );
            } else {
                $this->badge('error', 'Tidak ada Group Menu yang tersedia. Buat gmenu terlebih dahulu.');
                return Command::FAILURE;
            }
        }

        // Auto-generate URL berdasarkan dmenu code
        $dmenuUrl = strtolower($dmenuCode);

        $dmenuUrut = (int) ($this->option('urut') ?: prompt_text(
            label: 'Urutan',
            default: '999',
            required: false,
            command: $this
        ));
        
        // Validate numeric
        if (!is_numeric($dmenuUrut)) {
            $this->badge('error', 'Urutan harus berupa angka');
            return Command::FAILURE;
        }

        // Insert ke database dengan semua field yang diperlukan
        DB::table('sys_dmenu')->insert([
            'dmenu' => $dmenuCode,
            'gmenu' => $gmenuCode,
            'urut' => $dmenuUrut,
            'name' => $dmenuName,
            'icon' => null,
            'url' => $dmenuUrl, // URL yang diinput user
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
