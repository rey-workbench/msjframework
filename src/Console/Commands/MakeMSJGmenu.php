<?php

namespace MSJFramework\LaravelGenerator\Console\Commands;

use MSJFramework\LaravelGenerator\Console\Commands\Concerns\HasConsoleStyling;
use MSJFramework\LaravelGenerator\Console\Commands\Concerns\HasValidation;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

use function Laravel\Prompts\text;

class MakeMSJGmenu extends Command
{
    use HasConsoleStyling, HasValidation;

    protected $signature = 'msj:make:gmenu {code?} {name?} {--urut=1}';
    protected $description = 'Create new Group Menu (gmenu)';

    public function handle(): int
    {
        $this->displayHeader('Create Group Menu');

        $gmenuCode = $this->argument('code') ?: text(
            label: 'Kode Group Menu (gmenu)',
            placeholder: 'KOP001 (case insensitive)',
            required: true,
            validate: fn($value) => $this->validateGmenuCode($value, 6) // Max 6 chars
        );

        $gmenuName = $this->argument('name') ?: text(
            label: 'Nama Group Menu',
            placeholder: 'Master Data',
            required: true,
            validate: fn($value) => $this->validateName($value, 2, 25) // Max 25 chars
        );

        $gmenuUrut = (int) ($this->option('urut') ?: text(
            label: 'Urutan',
            default: '1',
            validate: fn($value) => $this->validateNumeric($value)
        ));

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
