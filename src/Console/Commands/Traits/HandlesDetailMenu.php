<?php

namespace MSJFramework\Console\Commands\Traits;

use Illuminate\Support\Facades\DB;
use function Laravel\Prompts\text;
use function Laravel\Prompts\confirm;
use function Laravel\Prompts\note;

trait HandlesDetailMenu
{
    protected function configureDetailMenu(): void
    {
        note('Konfigurasi Menu Detail');

        $this->menuData['dmenu'] = text(
            label: 'ID Menu Detail (contoh: msj001)',
            placeholder: 'msj001',
            required: true,
            validate: fn($value) => $this->validateDmenuId($value)
        );

        $this->menuData['dmenu_name'] = text(
            label: 'Nama Menu',
            placeholder: 'Master Karyawan',
            required: true
        );

        $this->menuData['url'] = text(
            label: 'URL/Rute (contoh: master-karyawan)',
            placeholder: 'master-karyawan',
            required: true,
            validate: fn($value) => $this->validateUrl($value)
        );

        $this->menuData['icon'] = text(
            label: 'Icon Menu (Font Awesome)',
            placeholder: 'fas fa-database',
            default: 'fas fa-file',
            hint: 'Contoh: fas fa-user, fas fa-chart-bar, fas fa-cog'
        );

        $availableTables = $this->db->getAvailableTables();
        
        if (!empty($availableTables)) {
            $this->menuData['table'] = $this->smartSelect(
                label: 'Pilih tabel database',
                options: $availableTables,
                placeholder: 'Mulai ketik untuk mencari...',
                hint: 'Pilih dari tabel yang tersedia di database'
            );
        } else {
            $this->menuData['table'] = text(
                label: 'Nama tabel database',
                placeholder: 'mst_employee',
                required: true,
                validate: fn($value) => $this->db->validateTableName($value)
            );
        }

        $whereInput = text(
            label: 'Klausa WHERE (opsional)',
            placeholder: "isactive='1'",
            default: '',
            hint: 'Tulis kondisi tanpa keyword WHERE'
        );
        
        // Normalize where clause
        if (!empty($whereInput)) {
            // Remove WHERE keyword if user accidentally includes it
            $whereInput = preg_replace('/^\s*WHERE\s+/i', '', trim($whereInput));
            // Add WHERE keyword
            $this->menuData['where_clause'] = 'WHERE ' . $whereInput;
        } else {
            $this->menuData['where_clause'] = '';
        }

        $generateJs = confirm('Buat file JavaScript kustom?', false);
        $this->menuData['js_menu'] = $generateJs ? '1' : '0';

        $this->menuData['show'] = confirm('Tampilkan menu di sidebar?', true) ? '1' : '0';

        if (confirm('Tambah notifikasi badge di menu?', false)) {
            $this->menuData['notif'] = text(
                label: 'Query notifikasi (SELECT COUNT...)',
                placeholder: "SELECT COUNT(*) FROM tabel WHERE status='pending'",
                default: ''
            );
        } else {
            $this->menuData['notif'] = null;
        }

        $lastUrut = DB::table('sys_dmenu')
            ->where('gmenu', $this->menuData['gmenu'])
            ->max('urut') ?? 0;

        $this->menuData['dmenu_urut'] = (int) text(
            label: 'Urutan Tampilan',
            default: (string)($lastUrut + 1),
            required: true
        );

        $this->newLine();
    }
}
