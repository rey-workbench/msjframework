<?php

namespace MSJFramework\Console\Commands\Traits;

use function Laravel\Prompts\text;
use function Laravel\Prompts\select;
use function Laravel\Prompts\confirm;
use function Laravel\Prompts\info;
use function Laravel\Prompts\note;

trait HandlesIDGeneration
{
    protected function configureIDGeneration(): void
    {
        note('Pengaturan ID Otomatis');
        info('Pengaturan pola ID otomatis (misalnya, EMP-2024-0001)');
        $this->newLine();

        $this->menuData['id_rules'] = [];
        $urut = 1;

        do {
            $rule = ['urut' => $urut];
            
            $rule['source'] = select(
                label: "Segmen #{$urut} - Sumber",
                options: [
                    'ext' => 'String eksternal (teks tetap)',
                    'int' => 'ID internal (auto increment)',
                    'dtm' => 'Tanggal (MMYYYY)',
                    'dty' => 'Tanggal (YYYYMM)',
                    'num' => 'Counter (3 digit)',
                    'usr' => 'Username',
                ],
                required: true
            );

            if ($rule['source'] === 'ext') {
                $rule['external'] = text(label: 'Teks tetap', placeholder: 'EMP', required: true);
                $rule['internal'] = '';
            } elseif ($rule['source'] === 'int') {
                $rule['internal'] = text(label: 'Nama field', placeholder: 'dept_code', required: true);
                $rule['external'] = '';
            } else {
                $rule['external'] = '';
                $rule['internal'] = '';
            }

            $rule['length'] = (int) text(
                label: 'Panjang',
                default: $rule['source'] === 'cnt' ? '4' : '2',
                required: true
            );

            $this->menuData['id_rules'][] = $rule;
            info("✓ Segmen ditambahkan: {$rule['source']} (panjang: {$rule['length']})");
            $this->newLine();
            $urut++;
        } while (confirm('Tambah segmen lagi?', $urut < 5));

        $this->newLine();
    }
}
