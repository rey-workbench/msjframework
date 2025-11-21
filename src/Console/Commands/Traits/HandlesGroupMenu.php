<?php

namespace MSJFramework\Console\Commands\Traits;

use Illuminate\Support\Facades\DB;
use function Laravel\Prompts\text;
use function Laravel\Prompts\confirm;
use function Laravel\Prompts\note;

trait HandlesGroupMenu
{
    protected function configureGroupMenu(): void
    {
        note('Konfigurasi Menu Grup');

        $existingGmenus = DB::table('sys_gmenu')
            ->where('isactive', '1')
            ->orderBy('urut')
            ->get(['gmenu', 'name'])
            ->mapWithKeys(fn($item) => [$item->gmenu => "{$item->gmenu} - {$item->name}"])
            ->toArray();

        $useExisting = confirm('Gunakan menu grup yang sudah ada?', false);

        if ($useExisting && !empty($existingGmenus)) {
            $this->menuData['gmenu'] = $this->smartSelect(
                label: 'Pilih menu grup',
                options: $existingGmenus,
                placeholder: 'Mulai ketik untuk mencari...'
            );
        } else {
            $this->menuData['gmenu'] = text(
                label: 'ID Menu Grup (contoh: msj001)',
                placeholder: 'msj001',
                required: true,
                validate: fn($value) => $this->validateGmenuId($value)
            );

            $this->menuData['gmenu_name'] = text(
                label: 'Nama Menu Grup',
                placeholder: 'Master Data',
                required: true
            );

            $this->menuData['gmenu_icon'] = text(
                label: 'Ikon Menu Grup (opsional)',
                placeholder: 'fas fa-database',
                default: 'fas fa-folder'
            );

            $lastUrut = DB::table('sys_gmenu')->max('urut') ?? 0;
            $this->menuData['gmenu_urut'] = (int) text(
                label: 'Urutan Tampilan',
                default: (string)($lastUrut + 1),
                required: true
            );

            $this->menuData['create_new_gmenu'] = true;
        }

        $this->newLine();
    }
}
