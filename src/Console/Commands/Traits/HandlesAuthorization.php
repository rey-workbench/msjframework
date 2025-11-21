<?php

namespace MSJFramework\Console\Commands\Traits;

use Illuminate\Support\Facades\DB;
use function Laravel\Prompts\multiselect;
use function Laravel\Prompts\select;
use function Laravel\Prompts\confirm;
use function Laravel\Prompts\warning;
use function Laravel\Prompts\info;
use function Laravel\Prompts\note;

trait HandlesAuthorization
{
    /**
     * Permission presets untuk quick setup
     */
    protected function getPermissionPresets(): array
    {
        return [
            'full' => [
                'name' => 'Full Access - Semua akses diizinkan',
                'permissions' => [
                    'value' => '1',
                    'add' => '1',
                    'edit' => '1',
                    'delete' => '1',
                    'approval' => '1',
                    'print' => '1',
                    'excel' => '1',
                    'pdf' => '1',
                    'rules' => '0',
                ],
            ],
            'readonly' => [
                'name' => 'Read Only - Hanya lihat data',
                'permissions' => [
                    'value' => '1',
                    'add' => '0',
                    'edit' => '0',
                    'delete' => '0',
                    'approval' => '0',
                    'print' => '1',
                    'excel' => '1',
                    'pdf' => '1',
                    'rules' => '0',
                ],
            ],
            'approver' => [
                'name' => 'Approver - Approve + Export (tidak bisa edit data)',
                'permissions' => [
                    'value' => '1',
                    'add' => '0',
                    'edit' => '0',
                    'delete' => '0',
                    'approval' => '1',
                    'print' => '1',
                    'excel' => '1',
                    'pdf' => '1',
                    'rules' => '0',
                ],
            ],
            'editor' => [
                'name' => 'Editor - Add + Edit (tidak bisa delete/approve)',
                'permissions' => [
                    'value' => '1',
                    'add' => '1',
                    'edit' => '1',
                    'delete' => '0',
                    'approval' => '0',
                    'print' => '1',
                    'excel' => '1',
                    'pdf' => '1',
                    'rules' => '0',
                ],
            ],
            'restricted' => [
                'name' => 'Restricted - Hanya add (untuk anggota/user biasa)',
                'permissions' => [
                    'value' => '1',
                    'add' => '1',
                    'edit' => '0',
                    'delete' => '0',
                    'approval' => '0',
                    'print' => '0',
                    'excel' => '0',
                    'pdf' => '0',
                    'rules' => '1',
                ],
            ],
            'custom' => [
                'name' => 'Custom - Atur manual satu per satu',
                'permissions' => null,
            ],
        ];
    }

    protected function configureAuthorization(): void
    {
        note('Konfigurasi Hak Akses');

        $roles = DB::table('sys_roles')
            ->where('isactive', '1')
            ->pluck('name', 'idroles')
            ->toArray();

        if (empty($roles)) {
            warning('Role tidak ditemukan! Silakan buat role terlebih dahulu.');
            $this->menuData['auth_roles'] = [];
            return;
        }

        $selectedRoles = multiselect(
            label: 'Pilih role yang mendapatkan akses',
            options: $roles,
            required: true,
            hint: 'Gunakan spasi untuk memilih, Enter untuk konfirmasi'
        );

        $this->menuData['auth_roles'] = [];
        $presets = $this->getPermissionPresets();

        foreach ($selectedRoles as $roleId) {
            $this->newLine();
            info("Mengatur hak akses untuk: {$roles[$roleId]}");

            // Tampilkan pilihan preset
            $presetOptions = collect($presets)->mapWithKeys(fn($preset, $key) => [
                $key => $preset['name']
            ])->toArray();

            $selectedPreset = select(
                label: 'Pilih preset permission',
                options: $presetOptions,
                default: 'full'
            );

            // Jika custom, tanya satu per satu
            if ($selectedPreset === 'custom') {
                $this->menuData['auth_roles'][$roleId] = [
                    'value' => confirm('Izinkan VALUE? (lihat data)', true) ? '1' : '0',
                    'add' => confirm('Izinkan TAMBAH?', true) ? '1' : '0',
                    'edit' => confirm('Izinkan UBAH?', true) ? '1' : '0',
                    'delete' => confirm('Izinkan HAPUS?', true) ? '1' : '0',
                    'approval' => confirm('Izinkan APPROVAL?', false) ? '1' : '0',
                    'print' => confirm('Izinkan CETAK?', true) ? '1' : '0',
                    'excel' => confirm('Izinkan EXCEL?', true) ? '1' : '0',
                    'pdf' => confirm('Izinkan PDF?', true) ? '1' : '0',
                    'rules' => confirm('Izinkan RULES? (filter data berdasarkan role)', false) ? '1' : '0',
                ];
            } else {
                // Gunakan preset
                $this->menuData['auth_roles'][$roleId] = $presets[$selectedPreset]['permissions'];
                
                // Tanya RULES secara terpisah karena tergantung tabel
                $hasRulesColumn = $this->checkTableHasRulesColumn();
                if ($hasRulesColumn) {
                    $this->menuData['auth_roles'][$roleId]['rules'] = confirm(
                        'Izinkan RULES? (filter data berdasarkan role)',
                        $presets[$selectedPreset]['permissions']['rules'] === '1'
                    ) ? '1' : '0';
                }
                
                info('✓ Permission preset diterapkan: ' . $presets[$selectedPreset]['name']);
            }
        }

        $this->newLine();
    }

    /**
     * Check if table has rules column
     */
    protected function checkTableHasRulesColumn(): bool
    {
        if (!isset($this->menuData['table']) || $this->menuData['table'] === '-') {
            return false;
        }

        try {
            $columns = DB::getSchemaBuilder()->getColumnListing($this->menuData['table']);
            return in_array('rules', $columns);
        } catch (\Exception $e) {
            return false;
        }
    }
}
