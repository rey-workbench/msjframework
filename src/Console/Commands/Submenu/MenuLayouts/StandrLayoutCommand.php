<?php

namespace MSJFramework\Console\Commands\Submenu\MenuLayouts;

class StandrLayoutCommand extends BaseLayoutCommand
{
    protected $signature = 'msj:make:menu:standr';
    protected $description = 'Membuat menu Standar - CRUD standar dengan form otomatis';

    protected function getLayoutType(): string
    {
        return 'standr';
    }

    protected function getLayoutDescription(): string
    {
        return 'Standar - CRUD standar (otomatis)';
    }
}
