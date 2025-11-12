<?php

namespace MSJFramework\LaravelGenerator\Console\Commands;

use MSJFramework\LaravelGenerator\Console\Commands\Concerns\HasConsoleStyling;
use MSJFramework\LaravelGenerator\Console\Commands\Concerns\HasValidation;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

// Import safe prompt helpers that work on all platforms

class MakeMSJGmenu extends Command
{
    use HasConsoleStyling, HasValidation;

    protected $signature = 'msj:make:gmenu {code?} {name?} {--urut=1}';
    protected $description = 'Create new Group Menu (gmenu)';

    public function handle(): int
    {
        $this->displayHeader('Create Group Menu');

        // Note: Laravel Prompts validate callback not supported in fallback mode
        // Manual validation after input
        $gmenuCode = $this->argument('code') ?: prompt_text(
            label: 'Kode Group Menu (gmenu)',
            default: 'KOP001',
            required: true,
            command: $this
        );
        
        // Validate gmenu code
        $validation = $this->validateGmenuCode($gmenuCode, 6);
        if ($validation) {
            $this->badge('error', $validation);
            return Command::FAILURE;
        }
        
        // Check duplicate
        if ($this->gmenuExists($gmenuCode) || $this->gmenuExists(strtoupper($gmenuCode)) || $this->gmenuExists(strtolower($gmenuCode))) {
            $this->badge('error', "Kode gmenu '{$gmenuCode}' sudah ada");
            return Command::FAILURE;
        }

        $gmenuName = $this->argument('name') ?: prompt_text(
            label: 'Nama Group Menu',
            default: 'Master Data',
            required: true,
            command: $this
        );
        
        // Validate name
        $validation = $this->validateName($gmenuName, 2, 25);
        if ($validation) {
            $this->badge('error', $validation);
            return Command::FAILURE;
        }

        $gmenuUrut = (int) ($this->option('urut') ?: prompt_text(
            label: 'Urutan',
            default: '1',
            required: false,
            command: $this
        ));
        
        // Validate numeric
        if (!is_numeric($gmenuUrut)) {
            $this->badge('error', 'Urutan harus berupa angka');
            return Command::FAILURE;
        }

        // Insert ke database
        DB::table('sys_gmenu')->insert([
            'gmenu' => $gmenuCode,
            'urut' => $gmenuUrut,
            'name' => $gmenuName,
            'icon' => null,
            'isactive' => '1',
            'created_at' => now(),
            'updated_at' => now(),
            'user_create' => 'msj:make:gmenu',
        ]);

        $this->badge('success', "Group Menu '{$gmenuCode} - {$gmenuName}' berhasil dibuat");

        return Command::SUCCESS;
    }
}
