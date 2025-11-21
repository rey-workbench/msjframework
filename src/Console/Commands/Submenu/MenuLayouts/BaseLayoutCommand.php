<?php

namespace MSJFramework\Console\Commands\Submenu\MenuLayouts;

use Illuminate\Console\Command;
use MSJFramework\Console\Commands\Traits\HandlesTableConfiguration;
use MSJFramework\Console\Commands\Traits\HandlesMenuCreation;
use MSJFramework\Console\Commands\Traits\HandlesGroupMenu;
use MSJFramework\Console\Commands\Traits\HandlesDetailMenu;
use MSJFramework\Console\Commands\Traits\HandlesAuthorization;
use MSJFramework\Console\Commands\Traits\HandlesTableMetadata;
use MSJFramework\Console\Commands\Traits\HandlesIDGeneration;
use MSJFramework\Console\Commands\Traits\HandlesValidation;
use MSJFramework\Console\Commands\Traits\HandlesDisplay;
use MSJFramework\Services\DatabaseIntrospectionService;
use MSJFramework\Services\FileGeneratorService;
use MSJFramework\Services\PlatformDetectorService;
use function Laravel\Prompts\select;
use function Laravel\Prompts\confirm;
use function Laravel\Prompts\warning;
use function Laravel\Prompts\error;
use function Laravel\Prompts\search;

abstract class BaseLayoutCommand extends Command
{
    use HandlesTableConfiguration;
    use HandlesMenuCreation;
    use HandlesGroupMenu;
    use HandlesDetailMenu;
    use HandlesAuthorization;
    use HandlesTableMetadata;
    use HandlesIDGeneration;
    use HandlesValidation;
    use HandlesDisplay;

    protected DatabaseIntrospectionService $db;
    protected FileGeneratorService $generator;
    protected PlatformDetectorService $platform;
    
    protected array $menuData = [];
    protected array $tableFields = [];

    public function __construct()
    {
        parent::__construct();
        $this->db = new DatabaseIntrospectionService();
        $this->generator = new FileGeneratorService($this->db);
        $this->platform = new PlatformDetectorService();
    }

    abstract protected function getLayoutType(): string;
    abstract protected function getLayoutDescription(): string;

    public function handle(): int
    {
        $this->displayBanner();

        try {
            $this->menuData['layout'] = $this->getLayoutType();
            
            $this->configureGroupMenu();
            $this->configureDetailMenu();
            $this->configureAuthorization();

            if ($this->menuData['layout'] !== 'manual') {
                $this->configureTableMetadata();
                $this->configureSublinkParent();
            }

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
}
