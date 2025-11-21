<?php

namespace MSJFramework\Console\Commands\Submenu\MenuLayouts;

use Illuminate\Console\Command;
use function Laravel\Prompts\confirm;
use function Laravel\Prompts\warning;
use function Laravel\Prompts\error;

class ManualLayoutCommand extends BaseLayoutCommand
{
    protected $signature = 'msj:make:menu:manual';
    protected $description = 'Membuat menu Manual - Implementasi kustom penuh';

    protected function getLayoutType(): string
    {
        return 'manual';
    }

    protected function getLayoutDescription(): string
    {
        return 'Manual - Implementasi kustom (penuh)';
    }

    public function handle(): int
    {
        $this->displayBanner();

        try {
            $this->menuData['layout'] = $this->getLayoutType();
            
            $this->configureGroupMenu();
            $this->configureDetailMenu();
            $this->configureAuthorization();

            // Manual layout tidak memerlukan table metadata dan sublink parent
            
            if (confirm('Konfigurasi penomoran otomatis?', false)) {
                $this->configureIDGeneration();
            }

            $this->reviewConfiguration();

            if (confirm('Lanjutkan proses pembuatan menu?', true)) {
                $this->createMenu();
                $this->displaySuccess();
                return Command::SUCCESS;
            }

            warning('Pembuatan menu dibatalkan.');
            return Command::FAILURE;

        } catch (\Exception $e) {
            error('Error: ' . $e->getMessage());
            return Command::FAILURE;
        }
    }
}
