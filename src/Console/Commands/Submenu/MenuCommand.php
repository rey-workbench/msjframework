<?php

namespace MSJFramework\Console\Commands\Submenu;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use MSJFramework\Console\Commands\Traits\HandlesTableConfiguration;
use MSJFramework\Console\Commands\Traits\HandlesMenuCreation;
use MSJFramework\Services\DatabaseIntrospectionService;
use MSJFramework\Services\FileGeneratorService;
use MSJFramework\Services\PlatformDetectorService;
use function Laravel\Prompts\text;
use function Laravel\Prompts\select;
use function Laravel\Prompts\multiselect;
use function Laravel\Prompts\confirm;
use function Laravel\Prompts\info;
use function Laravel\Prompts\warning;
use function Laravel\Prompts\error;
use function Laravel\Prompts\note;
use function Laravel\Prompts\table;
use function Laravel\Prompts\search;

class MenuCommand extends Command
{
    use HandlesTableConfiguration;
    use HandlesMenuCreation;
    protected $signature = 'msj:make:menu';
    protected $description = 'Membuat menu baru dengan panduan interaktif';

    protected DatabaseIntrospectionService $db;
    protected FileGeneratorService $generator;
    protected PlatformDetectorService $platform;
    
    protected array $menuData = [];
    protected array $tableFields = [];

    public function __construct()
    {
        parent::__construct();
        $this->db = new DatabaseIntrospectionService();
        $this->generator = new FileGeneratorService($this->db);
        $this->platform = new PlatformDetectorService();
    }

    public function handle(): int
    {
        $this->displayBanner();

        try {
            $this->selectMenuType();
            $this->configureGroupMenu();
            $this->configureDetailMenu();
            $this->configureAuthorization();

            if ($this->menuData['layout'] !== 'manual') {
                $this->configureTableMetadata();
                $this->configureSublinkParent();
            }

            if (confirm('Konfigurasi penomoran otomatis?', false)) {
                $this->configureIDGeneration();
            }

            $this->reviewConfiguration();

            if (confirm('Lanjutkan proses pembuatan menu?', true)) {
                $this->createMenu();
                $this->displaySuccess();
                return Command::SUCCESS;
            }

            warning('Pembuatan menu dibatalkan.');
            return Command::FAILURE;

        } catch (\Exception $e) {
            error('Error: ' . $e->getMessage());
            return Command::FAILURE;
        }
    }

    protected function displayBanner(): void
    {
        $this->newLine();
        info('╔═══════════════════════════════════════════╗');
        info('║  MSJ Framework - Panduan Pembuatan Menu   ║');
        info('╚═══════════════════════════════════════════╝');
        $this->newLine();
    }

    /**
     * Smart selection that uses appropriate UI based on OS
     * Uses PlatformDetector to determine the best prompt type
     */
    protected function smartSelect(string $label, array $options, string $placeholder = '', string $hint = ''): string
    {
        // Use select() for Windows native - displays numbered list
        if ($this->platform->isWindowsNonWSL()) {
            return select(
                label: $label,
                options: $options,
                hint: $hint ?: 'Gunakan panah ↑↓ untuk navigasi atau ketik nomor'
            );
        }

        // Use search() for Unix/WSL/Mac - better interactive experience
        return search(
            label: $label,
            options: fn (string $value) => strlen($value) > 0
                ? collect($options)->filter(fn($optLabel, $key) => 
                    str_contains(strtolower($optLabel), strtolower($value)) ||
                    str_contains(strtolower($key), strtolower($value))
                )->all()
                : $options,
            placeholder: $placeholder ?: 'Mulai ketik untuk mencari...',
            hint: $hint
        );
    }

    protected function selectMenuType(): void
    {
        note('Langkah 1: Pilih Tipe Layout Menu');

        $layoutOptions = [
            'master' => 'Master - CRUD sederhana (otomatis)',
            'transc' => 'Transaksi - Header-Detail (otomatis)',
            'system' => 'Sistem - Form konfigurasi (otomatis)',
            'standr' => 'Standar - CRUD standar (otomatis)',
            'sublnk' => 'Sublink - Relasi antar tabel (otomatis)',
            'report' => 'Laporan - Filter dan hasil (otomatis)',
            'manual' => 'Manual - Implementasi kustom (penuh)',
        ];

        $this->menuData['layout'] = $this->smartSelect(
            label: 'Pilih tipe layout',
            options: $layoutOptions,
            placeholder: 'Mulai ketik untuk mencari...',
            hint: 'Layout otomatis membuat UI dari metadata, Manual memberi kontrol penuh'
        );

        info("Tipe layout dipilih: {$this->menuData['layout']}");
        $this->newLine();
    }

    protected function configureGroupMenu(): void
    {
        note('Langkah 2: Konfigurasi Menu Grup');

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
                label: 'ID Menu Grup (contoh: MSJ001)',
                placeholder: 'MSJ001',
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

    protected function configureDetailMenu(): void
    {
        note('Langkah 3: Konfigurasi Menu Detail');

        $this->menuData['dmenu'] = text(
            label: 'ID Menu Detail (contoh: MSJ001)',
            placeholder: 'MSJ001',
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

        // Get available tables from service
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

        $this->menuData['where_clause'] = text(
            label: 'Klausa WHERE (opsional)',
            placeholder: "isactive='1'",
            default: ''
        );

        $generateJs = confirm('Buat file JavaScript kustom?', false);
        $this->menuData['js_menu'] = $generateJs ? '1' : '0';

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

    protected function configureAuthorization(): void
    {
        note('Langkah 4: Konfigurasi Hak Akses');

        $roles = DB::table('sys_roles')
            ->where('isactive', '1')
            ->pluck('name', 'idroles')
            ->toArray();

        if (empty($roles)) {
            warning('Role tidak ditemukan! Silakan buat role terlebih dahulu.');
            $this->menuData['roles'] = [];
            return;
        }

        $selectedRoles = multiselect(
            label: 'Pilih role yang mendapatkan akses',
            options: $roles,
            required: true,
            hint: 'Gunakan spasi untuk memilih, Enter untuk konfirmasi'
        );

        $this->menuData['auth_roles'] = [];

        foreach ($selectedRoles as $roleId) {
            $this->newLine();
            info("Mengatur hak akses untuk: {$roles[$roleId]}");

            $this->menuData['auth_roles'][$roleId] = [
                'value' => '1',
                'add' => confirm('Izinkan TAMBAH?', true) ? '1' : '0',
                'edit' => confirm('Izinkan UBAH?', true) ? '1' : '0',
                'delete' => confirm('Izinkan HAPUS?', true) ? '1' : '0',
                'approval' => confirm('Izinkan APPROVAL?', false) ? '1' : '0',
                'print' => confirm('Izinkan CETAK?', true) ? '1' : '0',
                'excel' => confirm('Izinkan EXCEL?', true) ? '1' : '0',
                'pdf' => confirm('Izinkan PDF?', true) ? '1' : '0',
                'rules' => confirm('Izinkan RULES?', true) ? '1' : '0',
            ];
        }

        $this->newLine();
    }

    protected function configureSublinkParent(): void
    {
        if ($this->menuData['layout'] !== 'sublnk') {
            return;
        }

        note('Langkah 4b: Konfigurasi Parent Sublink');
        
        $existingSublinks = DB::table('sys_dmenu')
            ->where('isactive', '1')
            ->where('show', '0')
            ->whereNotNull('sub')
            ->get(['dmenu', 'name', 'sub'])
            ->mapWithKeys(fn($item) => [$item->dmenu => "{$item->dmenu} - {$item->name}"])
            ->toArray();

        $useExisting = false;
        if (!empty($existingSublinks)) {
            $useExisting = confirm('Gunakan parent sublink yang sudah ada?', false);
        }

        if ($useExisting && !empty($existingSublinks)) {
            $parentDmenu = $this->smartSelect(
                label: 'Pilih menu parent sublink',
                options: $existingSublinks,
                placeholder: 'Mulai ketik untuk mencari...'
            );
            
            $parent = DB::table('sys_dmenu')->where('dmenu', $parentDmenu)->first();
            $this->menuData['parent_link'] = $parent->sub;
        } else {
            info('Membuat parent sublink baru...');
            
            $parentDmenu = text(
                label: 'ID Menu Parent (contoh: SUBXXX)',
                placeholder: 'SUB001',
                required: true,
            );
            
            $parentName = text(
                label: 'Nama Menu Parent',
                placeholder: 'Daftar Sub Item',
                required: true
            );
            
            $this->menuData['parent_link'] = $parentDmenu;
            $this->menuData['create_parent'] = true;
            $this->menuData['parent_dmenu'] = $parentDmenu;
            $this->menuData['parent_name'] = $parentName;
        }
        
        // Set link attribute for primary key field
        foreach ($this->tableFields as &$field) {
            if ($field['primary'] === '1') {
                $field['link'] = $this->menuData['parent_link'];
                break;
            }
        }
        
        $this->newLine();
    }

    protected function configureTableMetadata(): void
    {
        note('Langkah 5: Konfigurasi Metadata Tabel (untuk form otomatis)');

        // Auto-detect fields using service
        $detectedFields = $this->db->detectTableFields($this->menuData['table']);
        
        if (!empty($detectedFields)) {
            info("✓ Terdeteksi " . count($detectedFields) . " kolom dari tabel '{$this->menuData['table']}'");
            $this->newLine();
            
            $useAutoDetect = confirm('Gunakan field hasil deteksi otomatis?', true);
            
            if ($useAutoDetect) {
                table(
                    ['Kolom', 'Tipe', 'Bisa Null', 'Default'],
                    collect($detectedFields)->map(fn($f) => [
                        $f['field'],
                        $f['db_type'],
                        $f['nullable'] ? 'Ya' : 'Tidak',
                        $f['default'] ?? '-'
                    ])->toArray()
                );
                $this->newLine();
                
                $selectedFields = multiselect(
                    label: 'Pilih field yang digunakan di form',
                    options: collect($detectedFields)->mapWithKeys(fn($f) => [
                        $f['field'] => "{$f['field']} ({$f['type']})"
                    ])->toArray(),
                    required: true,
                    hint: 'Gunakan spasi untuk memilih, Enter untuk konfirmasi'
                );
                
                foreach ($selectedFields as $fieldName) {
                    $detected = collect($detectedFields)->firstWhere('field', $fieldName);
                    if ($detected) {
                        $this->tableFields[] = $detected;
                    }
                }
                
                info("✓ " . count($this->tableFields) . " fields configured");
                $this->newLine();
                
                if (confirm('Ubah pengaturan field?', false)) {
                    $this->customizeFields();
                }
                
                return;
            }
        } else {
            warning("Tidak dapat mendeteksi field dari tabel '{$this->menuData['table']}'");
            info('Anda perlu mengkonfigurasi field secara manual');
            $this->newLine();
        }

        // Manual configuration fallback
        $this->configureFieldsManually();
        $this->newLine();
        
        foreach ($this->tableFields as $index => &$field) {
            info("Kolom: {$field['label']} ({$field['field']})");
            
            if (confirm('Sesuaikan kolom ini?', false)) {
                $field['label'] = text(
                    label: 'Label',
                    default: $field['label'],
                    required: true
                );
                
                $field['type'] = select(
                    label: 'Tipe Field',
                    options: [
                        'char' => 'Teks (pendek)',
                        'string' => 'Teks (panjang)',
                        'text' => 'Textarea',
                        'number' => 'Angka',
                        'currency' => 'Mata uang',
                        'date' => 'Tanggal',
                        'email' => 'Email',
                        'password' => 'Password',
                        'file' => 'Unggah File',
                        'image' => 'Unggah Gambar',
                        'enum' => 'Dropdown',
                        'search' => 'Search Modal',
                        'hidden' => 'Field Tersembunyi',
                    ],
                    default: $field['type']
                );
                
                $field['position'] = select(
                    label: 'Posisi',
                    options: ['L' => 'Kiri', 'R' => 'Kanan', 'F' => 'Lebar Penuh'],
                    default: $field['position']
                );
                
                $field['required'] = confirm('Wajib diisi?', $field['required'] === '1') ? '1' : '0';
                $field['readonly'] = confirm('Hanya baca?', $field['readonly'] === '1') ? '1' : '0';
                
                if ($field['type'] === 'enum') {
                    $field['idenum'] = text(
                        label: 'ID Enum',
                        placeholder: 'STATUS',
                        required: true
                    );
                }
                
                $this->newLine();
            }
        }
        
        info('✓ Penyesuaian field selesai');
        $this->newLine();
    }

    protected function configureIDGeneration(): void
    {
        note('Langkah 6: Pengaturan ID Otomatis');
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
            info('✨ Layout Manual - Membuat file otomatis...');
            $this->newLine();
            
            try {
                // Use generator service
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
            } catch (
\Exception $e) {
                warning('⚠️  Pembuatan file otomatis gagal: ' . $e->getMessage());
                info('Anda mungkin perlu membuat file secara manual.');
                info('Lihat: MANUAL_LAYOUT_GUIDE.md untuk contoh.');
            }
        } else {
            info('✓ Controller: Ditangani otomatis oleh ' . ucfirst($this->menuData['layout']) . 'Controller');
            info('✓ View: Dibentuk otomatis dari metadata sys_table');
            info('✓ Operasi CRUD: Siap digunakan!');
        }

        // Generate JavaScript file if requested
        if ($this->menuData['js_menu'] === '1') {
            $this->newLine();
            info('✨ Membuat file JavaScript...');
            
            try {
                $js = $this->generator->generateJavaScriptFile($this->menuData['dmenu']);
                info("✓ JavaScript: {$js['name']}");
                info("   {$js['path']}");
            } catch (
Exception $e) {
                warning('⚠️  Pembuatan JavaScript gagal: ' . $e->getMessage());
            }
        }

        $this->newLine();
        note('Langkah selanjutnya:');
        info('1. Login ke http://127.0.0.1/login');
        info('2. Buka menu baru yang dibuat');
        info('3. Uji operasi CRUD');
        $this->newLine();
    }

    protected function validateGmenuId($value): ?string
    {
        if (!preg_match('/^[a-z0-9]{6}$/', $value)) {
            return 'ID Menu Grup harus 6 karakter, huruf kecil atau angka';
        }

        if (DB::table('sys_gmenu')->where('gmenu', $value)->exists()) {
            return 'ID Menu Grup sudah digunakan';
        }

        return null;
    }

    protected function validateDmenuId($value): ?string
    {
        if (!preg_match('/^[a-z0-9]{6}$/', $value)) {
            return 'ID Menu Detail harus 6 karakter, huruf kecil atau angka';
        }

        if (DB::table('sys_dmenu')->where('dmenu', $value)->exists()) {
            return 'ID Menu Detail sudah digunakan';
        }

        return null;
    }

    protected function validateUrl($value): ?string
    {
        if (!preg_match('/^[a-z0-9\-]+$/', $value)) {
            return 'URL harus huruf kecil dengan angka dan strip (-)';
        }

        if (DB::table('sys_dmenu')->where('url', $value)->exists()) {
            return 'URL sudah digunakan';
        }

        return null;
    }
}
