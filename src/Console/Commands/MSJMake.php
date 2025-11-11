<?php

namespace MSJFramework\LaravelGenerator\Console\Commands;

use MSJFramework\LaravelGenerator\Console\Commands\Concerns\HasConsoleStyling;
use MSJFramework\LaravelGenerator\Console\Commands\Concerns\HasDatabaseOperations;
use MSJFramework\LaravelGenerator\Console\Commands\Concerns\HasMenuOperations;
use Illuminate\Console\Command;

use function Laravel\Prompts\search;
use function Laravel\Prompts\select;
use function Laravel\Prompts\text;

class MSJMake extends Command
{
    use HasConsoleStyling, HasDatabaseOperations, HasMenuOperations;

    protected $signature = 'msj:make {type?} {name?}';

    protected $description = 'MSJ Make - Hub command for all MSJ generators';

    public function handle(): int
    {
        $type = $this->argument('type');
        $name = $this->argument('name');

        // Jika ada type langsung, jalankan command
        if ($type) {
            return match ($type) {
                'help' => $this->displayDetailedHelp(),
                'module', 'menu' => $this->call('msj:make:menu'),
                'crud' => $this->makeCrud($name),
                'controller' => $this->makeController($name),
                'model' => $this->makeModel($name),
                'views' => $this->call('msj:make:views'),
                'auth' => $this->call('msj:make:auth'),
                'save' => $this->call('msj:make:save'),
                default => $this->handleUnknownType($type),
            };
        }

        // Jika tidak ada type, tampilkan interactive menu
        $this->displayWelcomeMenu();

        $action = select(
            label: 'Apa yang ingin Anda lakukan?',
            options: [
                'menu' => 'Generate Menu Baru',
                'crud' => 'Generate CRUD (Cepat)',
                'controller' => 'Generate Controller',
                'model' => 'Generate Model',
                'views' => 'Generate Views',
                'auth' => 'Generate Auth Data (Role/User/Permission)',
                'save' => 'Save Data to Seeders (Export Current Data)',
                'help' => 'Lihat Bantuan Detail',
                'exit' => 'Keluar',
            ],
            default: 'menu',
            scroll: 10
        );

        if ($action === 'exit') {
            $this->newLine();
            $this->line('<fg=gray>Terima kasih!</>');
            $this->newLine();

            return Command::SUCCESS;
        }

        return match ($action) {
            'help' => $this->displayDetailedHelp(),
            'menu' => $this->call('msj:make:menu'),
            'crud' => $this->makeCrudInteractive(),
            'controller' => $this->makeControllerInteractive(),
            'model' => $this->makeModelInteractive(),
            'views' => $this->call('msj:make:views'),
            'auth' => $this->call('msj:make:auth'),
            'save' => $this->call('msj:make:save'),
            default => Command::SUCCESS,
        };
    }

    protected function displayWelcomeMenu(): void
    {
        $this->displayHeader('MSJ Make - Hub Command');
    }

    protected function makeCrudInteractive(): int
    {
        $table = $this->searchAndSelectTable();
        return $this->call('msj:make:crud', ['table' => $table]);
    }

    protected function makeControllerInteractive(): int
    {
        $name = text('Masukkan Nama Controller', placeholder: 'ExampleController', required: true);

        return $this->call('msj:make:controller', ['name' => $name]);
    }

    protected function makeModelInteractive(): int
    {
        $table = $this->searchAndSelectTable();
        return $this->call('msj:make:model', ['table' => $table]);
    }

    // Method moved to HasDatabaseOperations trait

    protected function makeCrud(?string $table): int
    {
        if (! $table) {
            $this->badge('error', 'Nama tabel diperlukan!');
            $this->line('<fg=gray>Penggunaan:</> <fg=cyan>php artisan msj:make crud <table></>');

            return Command::FAILURE;
        }

        return $this->call('msj:make:crud', ['table' => $table]);
    }

    protected function makeController(?string $name): int
    {
        if (! $name) {
            $this->badge('error', 'Nama controller diperlukan!');
            $this->line('<fg=gray>Penggunaan:</> <fg=cyan>php artisan msj:make controller <name></>');

            return Command::FAILURE;
        }

        return $this->call('msj:make:controller', ['name' => $name]);
    }

    protected function makeModel(?string $table): int
    {
        if (! $table) {
            $this->badge('error', 'Nama tabel diperlukan!');
            $this->line('<fg=gray>Penggunaan:</> <fg=cyan>php artisan msj:make model <table></>');

            return Command::FAILURE;
        }

        return $this->call('msj:make:model', ['table' => $table]);
    }

    protected function displayDetailedHelp(): int
    {
        $this->displayHeader('MSJ Make - Bantuan Detail');

        // menu Command
        $this->section('msj:make menu');
        $this->line('<fg=gray>Deskripsi:</> <fg=white>Wizard interaktif dengan panduan langkah-demi-langkah</>');
        $this->line('<fg=gray>Penggunaan:</> <fg=cyan>php artisan msj:make menu</>');
        $this->line('<fg=gray>Proses:</> <fg=white>4 langkah - Layout → Info Dasar → Fields → Ringkasan</>');
        $this->newLine();

        // CRUD Command
        $this->section('msj:make crud <table>');
        $this->line('<fg=gray>Deskripsi:</> <fg=white>Cara tercepat untuk generate CRUD (deteksi otomatis dari tabel)</>');
        $this->line('<fg=gray>Penggunaan:</> <fg=cyan>php artisan msj:make crud <table> [--gmenu=] [--dmenu=] [--layout=]</>');
        $this->line('<fg=gray>Contoh:</> <fg=cyan>php artisan msj:make crud mst_example --gmenu=KOP001 --dmenu=KOP999</>');
        $this->line('<fg=gray>Opsi:</>');
        $this->line('  <fg=gray>--gmenu</>    Kode group menu (default: KOP001)');
        $this->line('  <fg=gray>--dmenu</>    Kode detail menu (default: KOP999)');
        $this->line('  <fg=gray>--layout</>   Tipe layout: manual|standr|transc|system|report (default: manual)');
        $this->newLine();

        // Controller Command
        $this->section('msj:make controller <name>');
        $this->line('<fg=gray>Deskripsi:</> <fg=white>Generate controller MSJ dengan method CRUD</>');
        $this->line('<fg=gray>Penggunaan:</> <fg=cyan>php artisan msj:make controller <name> [--table=] [--gmenu=] [--url=]</>');
        $this->line('<fg=gray>Contoh:</> <fg=cyan>php artisan msj:make controller ExampleController --table=mst_example</>');
        $this->line('<fg=gray>Menghasilkan:</> <fg=white>Controller dengan method list, create, store, edit, update, delete</>');
        $this->newLine();

        // Model Command
        $this->section('msj:make model <table>');
        $this->line('<fg=gray>Deskripsi:</> <fg=white>Generate model dari struktur tabel database</>');
        $this->line('<fg=gray>Penggunaan:</> <fg=cyan>php artisan msj:make model <table> [--force]</>');
        $this->line('<fg=gray>Contoh:</> <fg=cyan>php artisan msj:make model mst_example</>');
        $this->line('<fg=gray>Deteksi otomatis:</> <fg=white>Primary key, fillable fields, timestamps</>');
        $this->newLine();

        // Views Command
        $this->section('msj:make views');
        $this->line('<fg=gray>Deskripsi:</> <fg=white>Generate blade views dan file JavaScript</>');
        $this->line('<fg=gray>Penggunaan:</> <fg=cyan>php artisan msj:make views [--gmenu=] [--url=] [--table=]</>');
        $this->line('<fg=gray>Menghasilkan:</> <fg=white>list.blade.php, create.blade.php, edit.blade.php, show.blade.php</>');
        $this->line('<fg=gray>Juga membuat:</> <fg=white>list.js, form.js di resources/js/{gmenu}/{url}/</>');
        $this->newLine();

        // Layout Types
        $this->section('📋 Tipe Layout yang Tersedia');
        $this->line('  <fg=green>manual</>   - Controller & views custom (kontrol penuh)');
        $this->line('  <fg=green>standr</>   - CRUD standard (form sederhana, 1 primary key)');
        $this->line('  <fg=green>transc</>   - Transaksi (struktur header-detail)');
        $this->line('  <fg=green>system</>   - Konfigurasi sistem (master-detail)');
        $this->line('  <fg=green>report</>   - menu laporan (halaman filter & hasil)');
        $this->newLine();

        // Workflow
        $this->section('🔄 Alur Kerja yang Disarankan');
        $this->line('  1. Buat tabel database terlebih dahulu');
        $this->line('  2. Jalankan <fg=cyan>msj:make crud <table></> untuk setup cepat');
        $this->line('  3. Atau jalankan <fg=cyan>msj:make menu</> untuk wizard langkah-demi-langkah');
        $this->line('  4. menu akan terdaftar di sys_dmenu, sys_table, sys_auth');
        $this->line('  5. Akses menu Anda di /{url}');
        $this->newLine();

        // Tips
        $this->section('💡 Tips');
        $this->line('  • Gunakan <fg=cyan>msj:make crud</> untuk prototyping cepat');
        $this->line('  • Gunakan <fg=cyan>msj:make menu</> ketika butuh kontrol lebih');
        $this->line('  • Tabel harus sudah ada sebelum menjalankan generator');
        $this->line('  • Deteksi otomatis bekerja optimal dengan konvensi penamaan standar');
        $this->line('  • Untuk perintah detail, gunakan versi msj:make:*');
        $this->newLine();

        return Command::SUCCESS;
    }

    protected function handleUnknownType(string $type): int
    {
        $this->badge('error', "Tipe tidak dikenal: {$type}");
        $this->newLine();
        $this->line('<fg=gray>Tipe yang tersedia:</> <fg=cyan>help, menu, crud, controller, model, views, auth, save</>');
        $this->line('<fg=gray>Jalankan</> <fg=cyan>"php artisan msj:make"</> <fg=gray>untuk melihat semua perintah.</>');
        $this->line('<fg=gray>Jalankan</> <fg=cyan>"php artisan msj:make help"</> <fg=gray>untuk informasi lengkap.</>');
        $this->newLine();

        return Command::FAILURE;
    }
}
