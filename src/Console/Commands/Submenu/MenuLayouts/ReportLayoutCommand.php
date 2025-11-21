<?php

namespace MSJFramework\Console\Commands\Submenu\MenuLayouts;

class ReportLayoutCommand extends BaseLayoutCommand
{
    protected $signature = 'msj:make:menu:report';
    protected $description = 'Membuat menu Laporan - Filter dan hasil dengan form otomatis';

    protected function getLayoutType(): string
    {
        return 'report';
    }

    protected function getLayoutDescription(): string
    {
        return 'Laporan - Filter dan hasil (otomatis)';
    }
}
