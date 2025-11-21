<?php

namespace MSJFramework\Console\Commands\Traits;

use function Laravel\Prompts\info;
use function Laravel\Prompts\warning;
use function Laravel\Prompts\note;
use function Laravel\Prompts\table;

trait HandlesDisplay
{
    protected function displayBanner(): void
    {
        $this->newLine();
        info('╔═══════════════════════════════════════════╗');
        info('║  MSJ Framework - Panduan Pembuatan Menu   ║');
        info('╚═══════════════════════════════════════════╝');
        $this->newLine();
        info("Layout: {$this->getLayoutDescription()}");
        $this->newLine();
    }

    protected function reviewConfiguration(): void
    {
        $this->newLine();
        note('═══════════════════════════════════════════');
        note('Ringkasan Konfigurasi');
        note('═══════════════════════════════════════════');
        $this->newLine();

        table(
            ['Pengaturan', 'Nilai'],
            [
                ['Tipe Layout', $this->menuData['layout']],
                ['Menu Grup', $this->menuData['gmenu']],
                ['Menu Detail', $this->menuData['dmenu']],
                ['Nama Menu', $this->menuData['dmenu_name']],
                ['URL', $this->menuData['url']],
                ['Tabel', $this->menuData['table']],
                ['File JavaScript', $this->menuData['js_menu'] === '1' ? 'Ya' : 'Tidak'],
                ['Jumlah Role', count($this->menuData['auth_roles'])],
                ['Jumlah Field', count($this->tableFields)],
                ['Aturan ID', !empty($this->menuData['id_rules']) ? 'Ya' : 'Tidak'],
            ]
        );

        $this->newLine();
    }

    protected function displaySuccess(): void
    {
        $this->newLine();
        info('╔═══════════════════════════════════════════╗');
        info('║        Menu Berhasil Dibuat!             ║');
        info('╚═══════════════════════════════════════════╝');
        $this->newLine();

        note('Akses menu baru Anda di:');
        info("URL: /{$this->menuData['url']}");
        $this->newLine();

        if ($this->menuData['layout'] === 'manual') {
            $this->displayManualLayoutSuccess();
        } else {
            $this->displayAutoLayoutSuccess();
        }

        if ($this->menuData['js_menu'] === '1') {
            $this->generateJavaScriptFile();
        }

        $this->displayNextSteps();
    }

    protected function displayAutoLayoutSuccess(): void
    {
        info('✓ Controller: Ditangani otomatis oleh ' . ucfirst($this->menuData['layout']) . 'Controller');
        info('✓ View: Dibentuk otomatis dari metadata sys_table');
        info('✓ Operasi CRUD: Siap digunakan!');
    }

    protected function displayManualLayoutSuccess(): void
    {
        info('✨ Layout Manual - Membuat file otomatis...');
        $this->newLine();
        
        try {
            $model = $this->generator->generateModel($this->menuData['table']);
            info("✓ Model: {$model['name']}");
            
            $controller = $this->generator->generateController(
                $this->menuData['url'],
                $this->menuData['table']
            );
            info("✓ Controller: {$controller['name']}");
            
            $this->generator->generateViews(
                $this->menuData['gmenu'],
                $this->menuData['url'],
                $this->menuData['table']
            );
            info('✓ View: list.blade.php, add.blade.php, edit.blade.php, show.blade.php');
            
            $this->newLine();
            info('📁 File yang dibuat:');
            info("   app/Models/{$model['name']}.php");
            info("   app/Http/Controllers/{$controller['name']}.php");
            info("   resources/views/{$this->menuData['gmenu']}/{$this->menuData['url']}/");
        } catch (\Exception $e) {
            warning('⚠️  Pembuatan file otomatis gagal: ' . $e->getMessage());
            info('Anda mungkin perlu membuat file secara manual.');
            info('Lihat: MANUAL_LAYOUT_GUIDE.md untuk contoh.');
        }
    }

    protected function generateJavaScriptFile(): void
    {
        $this->newLine();
        info('✨ Membuat file JavaScript...');
        
        try {
            $js = $this->generator->generateJavaScriptFile($this->menuData['dmenu']);
            info("✓ JavaScript: {$js['name']}");
            info("   {$js['path']}");
        } catch (\Exception $e) {
            warning('⚠️  Pembuatan JavaScript gagal: ' . $e->getMessage());
        }
    }

    protected function displayNextSteps(): void
    {
        $this->newLine();
        note('Langkah selanjutnya:');
        info('1. Login ke http://127.0.0.1/login');
        info('2. Buka menu baru yang dibuat');
        info('3. Uji operasi CRUD');
        $this->newLine();
    }
}
