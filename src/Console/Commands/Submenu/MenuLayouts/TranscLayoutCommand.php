<?php

namespace MSJFramework\Console\Commands\Submenu\MenuLayouts;

class TranscLayoutCommand extends BaseLayoutCommand
{
    protected $signature = 'msj:make:menu:transc';
    protected $description = 'Membuat menu Transaksi - Header-Detail dengan form otomatis';

    protected function getLayoutType(): string
    {
        return 'transc';
    }

    protected function getLayoutDescription(): string
    {
        return 'Transaksi - Header-Detail (otomatis)';
    }
}
