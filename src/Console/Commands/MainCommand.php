<?php

namespace MSJFramework\Console\Commands;

use Illuminate\Console\Command;
use MSJFramework\Services\PlatformDetectorService;
use function Laravel\Prompts\select;
use function Laravel\Prompts\search;
use function Laravel\Prompts\info;
use function Laravel\Prompts\note;

class MainCommand extends Command
{
    protected $signature = 'msj';
    protected $description = 'MSJ Framework - Command Hub';
    
    protected PlatformDetectorService $platform;

    public function __construct()
    {
        parent::__construct();
        $this->platform = new PlatformDetectorService();
    }

    public function handle(): int
    {
        $this->displayBanner();
        $this->displayEnvironment();
        
        $choice = $this->showMenu();
        
        $this->newLine();
        
        return $this->executeChoice($choice);
    }

    protected function displayBanner(): void
    {
        $this->newLine();
        info('╔═══════════════════════════════════════════╗');
        info('║         MSJ Framework - Hub Menu         ║');
        info('╚═══════════════════════════════════════════╝');
        $this->newLine();
    }

    protected function displayEnvironment(): void
    {
        $env = $this->platform->getEnvironmentInfo();
        info("{$env['icon']} Platform: {$env['type']}");
        $this->newLine();
    }

    protected function showMenu(): string
    {
        $options = [
            'install' => '📦 Install - Instalasi komponen MSJ Framework',
            'menu' => '🎯 Make Menu - Buat menu baru dengan wizard',
            'exit' => '❌ Exit - Keluar',
        ];

        note('Pilih perintah yang ingin dijalankan:');
        
        // Use select() for Windows, search() for Unix-like
        if ($this->platform->isWindowsNonWSL()) {
            return select(
                label: 'Pilih perintah',
                options: $options,
                hint: 'Gunakan panah ↑↓ untuk navigasi atau ketik nomor'
            );
        }

        return search(
            label: 'Pilih perintah',
            options: fn (string $value) => strlen($value) > 0
                ? collect($options)->filter(fn($label, $key) => 
                    str_contains(strtolower($label), strtolower($value)) ||
                    str_contains(strtolower($key), strtolower($value))
                )->all()
                : $options,
            placeholder: 'Mulai ketik untuk mencari...',
            hint: 'Ketik untuk mencari atau gunakan panah ↑↓'
        );
    }

    protected function executeChoice(string $choice): int
    {
        return match($choice) {
            'install' => $this->call('msj:install'),
            'menu' => $this->call('msj:make:menu'),
            'exit' => $this->exitGracefully(),
            default => Command::FAILURE,
        };
    }

    protected function exitGracefully(): int
    {
        $this->newLine();
        info('👋 Terima kasih telah menggunakan MSJ Framework!');
        $this->newLine();
        return Command::SUCCESS;
    }
}
