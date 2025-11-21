<?php

namespace MSJFramework\Console\Commands\Submenu;

use Illuminate\Console\Command;
use MSJFramework\Services\PlatformDetectorService;
use function Laravel\Prompts\info;
use function Laravel\Prompts\note;
use function Laravel\Prompts\select;
use function Laravel\Prompts\search;
use function Laravel\Prompts\error;

class MenuCommand extends Command
{
    protected $signature = 'msj:make:menu';
    protected $description = 'Membuat menu baru dengan panduan interaktif';

    protected PlatformDetectorService $platform;

    public function __construct()
    {
        parent::__construct();
        $this->platform = new PlatformDetectorService();
    }

    public function handle(): int
    {
        $this->displayBanner();
        
        $layoutType = $this->selectMenuType();
        
        return $this->runLayoutCommand($layoutType);
    }

    protected function displayBanner(): void
    {
        $this->newLine();
        info('╔═══════════════════════════════════════════╗');
        info('║  MSJ Framework - Panduan Pembuatan Menu   ║');
        info('╚═══════════════════════════════════════════╝');
        $this->newLine();
    }

    /**
     * Smart selection that uses appropriate UI based on OS
     */
    protected function smartSelect(string $label, array $options, string $placeholder = '', string $hint = ''): string
    {
        if ($this->platform->isWindowsNonWSL()) {
            return select(
                label: $label,
                options: $options,
                hint: $hint ?: 'Gunakan panah ↑↓ untuk navigasi atau ketik nomor'
            );
        }

        return search(
            label: $label,
            options: fn (string $value) => strlen($value) > 0
                ? collect($options)->filter(fn($optLabel, $key) => 
                    str_contains(strtolower($optLabel), strtolower($value)) ||
                    str_contains(strtolower($key), strtolower($value))
                )->all()
                : $options,
            placeholder: $placeholder ?: 'Mulai ketik untuk mencari...',
            hint: $hint
        );
    }

    protected function selectMenuType(): string
    {
        note('Langkah 1: Pilih Tipe Layout Menu');

        $layoutOptions = [
            'master' => 'Master - CRUD sederhana (otomatis)',
            'transc' => 'Transaksi - Header-Detail (otomatis)',
            'system' => 'Sistem - Form konfigurasi (otomatis)',
            'standr' => 'Standar - CRUD standar (otomatis)',
            'sublnk' => 'Sublink - Relasi antar tabel (otomatis)',
            'report' => 'Laporan - Filter dan hasil (otomatis)',
            'manual' => 'Manual - Implementasi kustom (penuh)',
        ];

        $layout = $this->smartSelect(
            label: 'Pilih tipe layout',
            options: $layoutOptions,
            placeholder: 'Mulai ketik untuk mencari...',
            hint: 'Layout otomatis membuat UI dari metadata, Manual memberi kontrol penuh'
        );

        info("Tipe layout dipilih: {$layout}");
        $this->newLine();
        
        return $layout;
    }

    protected function runLayoutCommand(string $layoutType): int
    {
        $commandMap = [
            'master' => 'msj:make:menu:master',
            'transc' => 'msj:make:menu:transc',
            'system' => 'msj:make:menu:system',
            'standr' => 'msj:make:menu:standr',
            'sublnk' => 'msj:make:menu:sublnk',
            'report' => 'msj:make:menu:report',
            'manual' => 'msj:make:menu:manual',
        ];

        if (!isset($commandMap[$layoutType])) {
            error("Layout type '{$layoutType}' tidak dikenali.");
            return Command::FAILURE;
        }

        return $this->call($commandMap[$layoutType]);
    }
}
