<?php

namespace MSJFramework\Console\Commands\Submenu\MenuLayouts;

class SystemLayoutCommand extends BaseLayoutCommand
{
    protected $signature = 'msj:make:menu:system';
    protected $description = 'Membuat menu Sistem - Form konfigurasi dengan form otomatis';

    protected function getLayoutType(): string
    {
        return 'system';
    }

    protected function getLayoutDescription(): string
    {
        return 'Sistem - Form konfigurasi (otomatis)';
    }
}
