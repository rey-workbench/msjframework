<?php

namespace MSJFramework\Console\Commands\Traits;

use Illuminate\Support\Facades\DB;
use function Laravel\Prompts\text;
use function Laravel\Prompts\select;
use function Laravel\Prompts\multiselect;
use function Laravel\Prompts\confirm;
use function Laravel\Prompts\info;
use function Laravel\Prompts\warning;
use function Laravel\Prompts\note;
use function Laravel\Prompts\table;

trait HandlesTableMetadata
{
    protected function configureTableMetadata(): void
    {
        note('Konfigurasi Metadata Tabel (untuk form otomatis)');

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
                        // Ensure primary key info is preserved
                        if (!isset($detected['primary'])) {
                            $detected['primary'] = '0';
                        }
                        $this->tableFields[] = $detected;
                    }
                }
                
                info("✓ " . count($this->tableFields) . " fields configured");
                
                // Show primary key info
                $primaryKeys = collect($this->tableFields)->where('primary', '1');
                if ($primaryKeys->count() > 0) {
                    $keyList = $primaryKeys->pluck('field')->implode(', ');
                    if ($primaryKeys->count() > 1) {
                        info("✓ Composite Primary Key: {$keyList}");
                        warning("⚠ Tabel ini punya composite key. Gunakan layout SYSTEM untuk hasil terbaik!");
                    } else {
                        info("✓ Primary Key: {$keyList}");
                    }
                } else {
                    warning("⚠ Primary key tidak terdeteksi!");
                }
                
                $this->newLine();
                
                $this->configureFieldsManually();
                
                if (confirm('Ubah pengaturan field?', false)) {
                    $this->tableFields = $this->db->detectTableFields($this->menuData['table']);

                    if (empty($this->tableFields)) {
                        warning('Tidak dapat mendeteksi field dari tabel');
                        info('Anda perlu mengkonfigurasi field secara manual');
                        $this->newLine();
                    }

                    // Verify primary key detection
                    $hasPrimary = collect($this->tableFields)->where('primary', '1')->isNotEmpty();
                    if (!$hasPrimary) {
                        warning('⚠ Primary key tidak terdeteksi! Form mungkin tidak berfungsi dengan baik.');
                        $this->newLine();
                    }
                    
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
                            
                            // Advanced configuration
                            if (confirm('Konfigurasi advanced (class, prefix/suffix, note)?', false)) {
                                $this->configureAdvancedFieldSettings($field);
                            }
                            
                            // Type-specific configuration
                            if ($field['type'] === 'enum' || $field['type'] === 'search') {
                                $this->configureEnumOrSearchField($field);
                            } elseif ($field['type'] === 'currency') {
                                $field['sub'] = text(
                                    label: 'Prefix mata uang',
                                    default: 'IDR ',
                                    hint: 'Contoh: IDR, USD, Rp'
                                );
                            }
                            
                            $this->newLine();
                        }
                    }
                    
                    info('✓ Penyesuaian field selesai');
                    $this->newLine();
                }
                
                return;
            }
        } else {
            warning("Tidak dapat mendeteksi field dari tabel '{$this->menuData['table']}'");
            info('Anda perlu mengkonfigurasi field secara manual');
            $this->newLine();
            
            // Force manual configuration jika auto-detection gagal
            $this->tableFields = [];
            $this->configureFieldsManually();
        }
        
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
                
                // Advanced configuration
                if (confirm('Konfigurasi advanced (class, prefix/suffix, note)?', false)) {
                    $this->configureAdvancedFieldSettings($field);
                }
                
                // Type-specific configuration
                if ($field['type'] === 'enum' || $field['type'] === 'search') {
                    $this->configureEnumOrSearchField($field);
                } elseif ($field['type'] === 'currency') {
                    $field['sub'] = text(
                        label: 'Prefix mata uang',
                        default: 'IDR ',
                        hint: 'Contoh: IDR, USD, Rp'
                    );
                }
                
                $this->newLine();
            }
        }
        
        info('✓ Penyesuaian field selesai');
        $this->newLine();
    }

    /**
     * Manual field configuration from scratch
     */
    protected function configureFieldsManually(): void
    {
        if (!confirm('Tambah/ubah field secara manual?', false)) {
            return;
        }

        do {
            $this->newLine();
            note('Konfigurasi Field Manual');

            $fieldName = text(
                label: 'Nama field',
                placeholder: 'nama_field',
                required: true,
                validate: fn($v) => preg_match('/^[a-z_][a-z0-9_]*$/', $v) ? null : 'Gunakan lowercase dan underscore'
            );

            // Check if field already exists
            $existingIndex = collect($this->tableFields)->search(fn($f) => $f['field'] === $fieldName);
            
            if ($existingIndex !== false) {
                if (!confirm("Field '{$fieldName}' sudah ada. Edit field ini?", true)) {
                    continue;
                }
                $field = &$this->tableFields[$existingIndex];
            } else {
                $this->tableFields[] = [];
                $field = &$this->tableFields[count($this->tableFields) - 1];
                $field['field'] = $fieldName;
                $field['urut'] = count($this->tableFields);
            }

            $field['label'] = text(
                label: 'Label (ditampilkan ke user)',
                default: $field['label'] ?? $this->generateLabel($fieldName),
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
                default: $field['type'] ?? 'string'
            );

            $field['length'] = (int) text(
                label: 'Panjang maksimal',
                default: (string)($field['length'] ?? 255),
                required: true
            );

            $field['position'] = select(
                label: 'Posisi',
                options: ['L' => 'Kiri', 'R' => 'Kanan', 'F' => 'Lebar Penuh'],
                default: $field['position'] ?? 'L'
            );

            $field['required'] = confirm('Wajib diisi?', ($field['required'] ?? '1') === '1') ? '1' : '0';
            $field['readonly'] = confirm('Hanya baca?', ($field['readonly'] ?? '0') === '1') ? '1' : '0';
            $field['primary'] = confirm('Primary key?', ($field['primary'] ?? '0') === '1') ? '1' : '0';

            $field['default'] = text(
                label: 'Nilai default (opsional)',
                default: $field['default'] ?? '',
                required: false
            );

            // Advanced configuration
            if (confirm('Konfigurasi advanced (class, prefix/suffix, note)?', false)) {
                $this->configureAdvancedFieldSettings($field);
            }

            // Type-specific configuration
            if ($field['type'] === 'enum' || $field['type'] === 'search') {
                $this->configureEnumOrSearchField($field);
            } elseif ($field['type'] === 'currency') {
                $field['sub'] = text(
                    label: 'Prefix mata uang',
                    default: $field['sub'] ?? 'IDR ',
                    hint: 'Contoh: IDR, USD, Rp'
                );
            }

            // Set defaults for optional fields
            $field['db_type'] = $field['db_type'] ?? 'varchar(255)';
            $field['nullable'] = $field['nullable'] ?? ($field['required'] === '0');
            $field['idenum'] = $field['idenum'] ?? '';
            $field['query'] = $field['query'] ?? '';
            $field['class'] = $field['class'] ?? '';
            $field['sub'] = $field['sub'] ?? '';
            $field['link'] = $field['link'] ?? '';
            $field['note'] = $field['note'] ?? '';

            info("✓ Field '{$fieldName}' dikonfigurasi");

        } while (confirm('Tambah/edit field lagi?', false));

        $this->newLine();
    }

    /**
     * Generate label from field name
     */
    protected function generateLabel(string $fieldName): string
    {
        return str($fieldName)
            ->replace('_', ' ')
            ->replace('-', ' ')
            ->title()
            ->toString();
    }

    /**
     * Configure advanced field settings (CLASS, SUB, NOTE)
     */
    protected function configureAdvancedFieldSettings(array &$field): void
    {
        // CSS Class
        $classOptions = [
            '' => 'Tidak ada',
            'upper' => 'Uppercase otomatis',
            'lower' => 'Lowercase otomatis',
            'notspace' => 'Hilangkan spasi',
            'readonly' => 'Read only',
            'custom-select' => 'Custom select styling',
            'select-multiple' => 'Multiple select',
        ];
        
        $field['class'] = select(
            label: 'CSS Class behavior',
            options: $classOptions,
            default: $field['class'] ?? ''
        );
        
        // Prefix/Suffix (SUB)
        $field['sub'] = text(
            label: 'Prefix/Suffix (opsional)',
            placeholder: 'Contoh: IDR, %, kg',
            default: $field['sub'] ?? '',
            hint: 'Teks yang ditampilkan sebelum/sesudah field'
        );
        
        // Note/Help text
        $field['note'] = text(
            label: 'Note/Help text (opsional)',
            placeholder: 'Contoh: Maksimal 100 karakter',
            default: $field['note'] ?? '',
            hint: 'Keterangan bantuan untuk user'
        );
    }

    /**
     * Configure ENUM or SEARCH field with query
     */
    protected function configureEnumOrSearchField(array &$field): void
    {
        $field['idenum'] = text(
            label: 'ID Enum atau Query',
            placeholder: 'Contoh: STATUS atau SELECT value, name FROM...',
            required: true,
            hint: 'Masukkan idenum atau SQL query langsung'
        );
        
        // Check if it's a query or idenum
        if (str_starts_with(strtoupper($field['idenum']), 'SELECT')) {
            // It's a query
            $field['query'] = $field['idenum'];
            $field['idenum'] = '';
        } else {
            // It's an idenum, generate query
            $field['query'] = "SELECT value, name FROM sys_enum WHERE idenum = '{$field['idenum']}' AND isactive = '1'";
        }
    }

    protected function configureSublinkParent(): void
    {
        if ($this->menuData['layout'] !== 'sublnk') {
            return;
        }

        note('Konfigurasi Parent Sublink');
        
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
            $this->menuData['parent_dmenu'] = $parentDmenu;
            $this->menuData['add_parent_auth'] = true;
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
        
        foreach ($this->tableFields as &$field) {
            if ($field['primary'] === '1') {
                $field['link'] = $this->menuData['parent_link'];
                break;
            }
        }
        
        $this->newLine();
    }
}
