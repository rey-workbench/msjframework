<?php

namespace MSJFramework\Console\Commands\Submenu\MenuLayouts;

class SublnkLayoutCommand extends BaseLayoutCommand
{
    protected $signature = 'msj:make:menu:sublnk';
    protected $description = 'Membuat menu Sublink - Relasi antar tabel dengan form otomatis';

    protected function getLayoutType(): string
    {
        return 'sublnk';
    }

    protected function getLayoutDescription(): string
    {
        return 'Sublink - Relasi antar tabel (otomatis)';
    }
}
