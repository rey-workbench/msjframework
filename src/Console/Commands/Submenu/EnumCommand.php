<?php

namespace MSJFramework\Console\Commands\Submenu;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use function Laravel\Prompts\text;
use function Laravel\Prompts\confirm;
use function Laravel\Prompts\info;
use function Laravel\Prompts\warning;
use function Laravel\Prompts\error;
use function Laravel\Prompts\note;
use function Laravel\Prompts\table;

class EnumCommand extends Command
{
    protected $signature = 'msj:make:enum';
    protected $description = 'Membuat enum/dropdown options untuk sys_enum';

    public function handle(): int
    {
        $this->displayBanner();

        try {
            // Get idenum
            $idenum = text(
                label: 'ID Enum (contoh: STATUS, TIPE_PEMBAYARAN)',
                placeholder: 'STATUS',
                required: true,
                validate: fn($value) => strlen($value) < 3 ? 'ID Enum minimal 3 karakter' : null
            );

            // Check if exists and handle
            $exists = DB::table('sys_enum')->where('idenum', $idenum)->exists();
            if ($exists) {
                if (!confirm("ID Enum '{$idenum}' sudah ada. Tambah opsi ke enum ini?", true)) {
                    warning('Pembuatan enum dibatalkan.');
                    $this->newLine();
                    return Command::FAILURE;
                }
            }

            $description = text(
                label: 'Deskripsi enum (opsional)',
                placeholder: 'Status aktif/nonaktif',
                default: ''
            );

            info("Membuat enum: {$idenum}");
            $this->newLine();

            $options = [];
            $urut = 1;

            do {
                note("Opsi #{$urut}");
                
                $name = text(
                    label: 'Name (disimpan di database)',
                    placeholder: '1',
                    required: true,
                    hint: 'Nilai yang disimpan di field database'
                );

                $value = text(
                    label: 'Value (ditampilkan ke user)',
                    placeholder: 'Aktif',
                    required: true,
                    hint: 'Label yang ditampilkan di dropdown'
                );

                $isactive = confirm('Aktifkan opsi ini?', true) ? '1' : '0';

                $options[] = [
                    'idenum' => $idenum,
                    'value' => $value,
                    'name' => $name,
                    'isactive' => $isactive,
                    'user_create' => 'system',
                    'created_at' => now(),
                    'updated_at' => now(),
                ];

                info("✓ Opsi ditambahkan: name={$name} (DB) → value={$value} (User)");
                $this->newLine();
                $urut++;

            } while (confirm('Tambah opsi lagi?', true));

            // Review
            $this->newLine();
            note('═══════════════════════════════════════════');
            note("Preview Enum: {$idenum}");
            note('═══════════════════════════════════════════');
            $this->newLine();

            table(
                ['Name (DB)', 'Value (User)', 'Status'],
                collect($options)->map(fn($opt) => [
                    $opt['name'],
                    $opt['value'],
                    $opt['isactive'] === '1' ? 'Aktif' : 'Nonaktif'
                ])->toArray()
            );

            $this->newLine();

            if (!confirm('Simpan enum ke database?', true)) {
                warning('Pembuatan enum dibatalkan.');
                return Command::FAILURE;
            }

            // Insert to database
            DB::beginTransaction();
            try {
                DB::table('sys_enum')->insert($options);
                DB::commit();

                $this->newLine();
                info('╔═══════════════════════════════════════════╗');
                info('║        Enum Berhasil Dibuat!              ║');
                info('╚═══════════════════════════════════════════╝');
                $this->newLine();

                info("✓ ID Enum: {$idenum}");
                info("✓ Jumlah Opsi: " . count($options));
                $this->newLine();

                note('Cara penggunaan:');
                info("1. Di sys_table, set type = 'enum'");
                info("2. Di field 'generateid', masukkan: {$idenum}");
                info("3. Query otomatis: SELECT value, name FROM sys_enum WHERE idenum = '{$idenum}'");
                info("   → value = label ditampilkan, name = nilai disimpan");
                $this->newLine();

                return Command::SUCCESS;

            } catch (\Exception $e) {
                DB::rollBack();
                throw $e;
            }

        } catch (\Exception $e) {
            $this->newLine();
            if (str_contains($e->getMessage(), 'dibatalkan')) {
                warning($e->getMessage());
            } else {
                error('Error: ' . $e->getMessage());
            }
            $this->newLine();
            return Command::FAILURE;
        }
    }

    protected function displayBanner(): void
    {
        $this->newLine();
        info('╔═══════════════════════════════════════════╗');
        info('║   MSJ Framework - Enum Creator           ║');
        info('╚═══════════════════════════════════════════╝');
        $this->newLine();
    }
}
