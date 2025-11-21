<?php

namespace MSJFramework\Console\Commands\Submenu\MenuLayouts;

class MasterLayoutCommand extends BaseLayoutCommand
{
    protected $signature = 'msj:make:menu:master';
    protected $description = 'Membuat menu Master - CRUD sederhana dengan form otomatis';

    protected function getLayoutType(): string
    {
        return 'master';
    }

    protected function getLayoutDescription(): string
    {
        return 'Master - CRUD sederhana (otomatis)';
    }
}
