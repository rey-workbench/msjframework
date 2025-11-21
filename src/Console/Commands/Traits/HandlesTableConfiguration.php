<?php

namespace MSJFramework\Console\Commands\Traits;

use Illuminate\Support\Facades\DB;

trait HandlesTableConfiguration
{
    /**
     * Insert sys_table configuration based on layout type
     */
    protected function insertTableConfiguration(): void
    {
        // Create parent sublink sys_table if needed
        if (isset($this->menuData['create_parent'])) {
            $this->insertSublinkParentQuery();
        }

        $layoutHandlers = [
            'report' => 'insertReportTableConfig',
            'master' => 'insertMasterTableConfig',
            'standard' => 'insertStandardTableConfig',
            'system' => 'insertSystemTableConfig',
            'sublink' => 'insertSublinkTableConfig',
            'sublnk' => 'insertSublinkTableConfig',
            'manual' => 'insertManualTableConfig',
            'transc' => 'insertTranscTableConfig',
        ];

        $layout = $this->menuData['layout'];
        $handler = $layoutHandlers[$layout] ?? 'insertDefaultTableConfig';

        if (method_exists($this, $handler)) {
            $this->$handler();
        }
    }

    /**
     * Insert parent query for sublink container
     */
    protected function insertSublinkParentQuery(): void
    {
        DB::table('sys_table')->insert([
            'gmenu' => $this->menuData['gmenu'],
            'dmenu' => $this->menuData['parent_dmenu'],
            'urut' => 1,
            'field' => 'query',
            'alias' => 'Parent Query',
            'type' => 'report',
            'length' => 0,
            'decimals' => '0',
            'default' => '',
            'validate' => '',
            'primary' => '0',
            'generateid' => '',
            'filter' => '0',
            'list' => '1',
            'show' => '1',
            'query' => "SELECT gmenu, dmenu, icon, tabel, name AS Detail FROM sys_dmenu WHERE sub = '{$this->menuData['parent_link']}'",
            'class' => '',
            'sub' => '',
            'link' => '',
            'note' => '',
            'position' => '1',
            'isactive' => '1',
            'user_create' => 'system',
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    /**
     * Report Layout - Query-based reporting
     * Pattern dari seeder example_tabel_rpt_syslog.php:
     *   - Field 1: query field dengan type = dmenu name
     *   - Field 2+: filter fields (minimal config)
     */
    protected function insertReportTableConfig(): void
    {
        if (empty($this->tableFields)) {
            return;
        }

        // First row: query definition (sesuai seeder - minimal fields only)
        DB::table('sys_table')->insert([
            'gmenu' => $this->menuData['gmenu'],
            'dmenu' => $this->menuData['dmenu'],
            'urut' => 1,
            'field' => 'query',
            'type' => $this->menuData['dmenu'], // Type = dmenu name (e.g., 'rpexam')
            'query' => $this->generateReportQuery(),
        ]);

        // Subsequent rows: filter fields
        $urut = 2;
        foreach ($this->tableFields as $field) {
            $this->insertFilterField($field, $urut++);
        }
    }

    /**
     * Master Layout - Full featured form with all positions
     */
    protected function insertMasterTableConfig(): void
    {
        if (empty($this->tableFields)) {
            return;
        }

        foreach ($this->tableFields as $field) {
            $this->insertFormField($field, [
                'filter' => '1',
                'list' => '1',
                'show' => '1',
                'position' => $this->convertPosition($field['position'] ?? 'L'),
            ]);
        }
    }

    /**
     * Standard Layout - Basic form without complex features
     */
    protected function insertStandardTableConfig(): void
    {
        if (empty($this->tableFields)) {
            return;
        }

        foreach ($this->tableFields as $field) {
            $this->insertFormField($field, [
                'filter' => '1',
                'list' => '1',
                'show' => '1',
                'position' => $this->convertPosition($field['position'] ?? 'S'),
            ]);
        }
    }

    /**
     * System Layout - System tables with composite primary keys
     * Pattern: Primary pertama → List kiri (position 1)
     *          Field lainnya → Detail kanan (position 2)
     */
    protected function insertSystemTableConfig(): void
    {
        if (empty($this->tableFields)) {
            return;
        }

        // Find first primary key for left panel (selector)
        $firstPrimaryField = collect($this->tableFields)
            ->where('primary', '1')
            ->first();

        foreach ($this->tableFields as $field) {
            // First primary key goes to LEFT panel (list/selector)
            if ($firstPrimaryField && $field['field'] === $firstPrimaryField['field']) {
                // Ensure query exists for left panel
                if (empty($field['query'])) {
                    warning("⚠ Field '{$field['field']}' di panel kiri membutuhkan QUERY untuk populate list!");
                    info("Contoh: SELECT {$field['field']}, name FROM {$this->menuData['table']}");
                }
                
                $this->insertFormField($field, [
                    'filter' => '1',  // Enable filter di kiri
                    'list' => '1',    // Tampil di list kiri
                    'show' => '1',    // Juga di detail kanan
                    'position' => '1', // Position 1 = kiri
                ]);
            } else {
                // Other fields (including other primary keys) go to RIGHT panel (detail)
                $this->insertFormField($field, [
                    'filter' => '1',  // Enable filter
                    'list' => '1',    // Muncul di detail kanan
                    'show' => '1',    // Show di detail
                    'position' => '2', // Position 2 = kanan
                ]);
            }
        }
    }

    /**
     * Sublink Layout - Parent-child relationship form
     * Pattern (dari seeder example_tabel_form_sublink):
     *   - Primary keys → position='1', link ke parent
     *   - Other fields → position='2'
     *   - Semua fields → filter='1', list='1', show='1'
     */
    protected function insertSublinkTableConfig(): void
    {
        if (empty($this->tableFields)) {
            return;
        }

        // Insert konfigurasi untuk parent sublink (jika create_parent = true)
        if (isset($this->menuData['create_parent']) && $this->menuData['create_parent']) {
            $this->insertSublinkParentTableConfig();
        }

        // Find all primary keys
        $primaryKeys = collect($this->tableFields)->where('primary', '1')->pluck('field');
        $firstPrimaryKey = $primaryKeys->first();

        // Insert konfigurasi untuk menu DATA (child form)
        foreach ($this->tableFields as $field) {
            // Primary keys di position 1, others di position 2
            $isPrimary = $primaryKeys->contains($field['field']);
            
            $overrides = [
                'filter' => '1',
                'list' => '1',
                'show' => '1',
                'position' => $isPrimary ? '1' : '2',
            ];

            // Primary key pertama gets link to parent
            if ($field['field'] === $firstPrimaryKey) {
                $overrides['link'] = $field['link'] ?? $this->menuData['parent_link'] ?? '';
            }

            $this->insertFormField($field, $overrides);
        }
    }

    /**
     * Insert table config for sublink parent menu
     */
    protected function insertSublinkParentTableConfig(): void
    {
        // Check if config already exists
        $exists = DB::table('sys_table')
            ->where('gmenu', $this->menuData['gmenu'])
            ->where('dmenu', $this->menuData['parent_dmenu'])
            ->where('urut', 1)
            ->exists();
        
        // Only insert if not exists
        if (!$exists) {
            // Konfigurasi query untuk parent sublink
            DB::table('sys_table')->insert([
                'gmenu' => $this->menuData['gmenu'],
                'dmenu' => $this->menuData['parent_dmenu'],
                'urut' => 1,
                'field' => 'query',
                'alias' => 'Sublink Query',
                'type' => 'report',
                'length' => 0,
                'decimals' => '0',
                'default' => '',
                'validate' => '',
                'primary' => '0',
                'generateid' => '',
                'filter' => '0',
                'list' => '1',
                'show' => '1',
                'query' => $this->generateSublinkQuery(),
                'class' => '',
                'sub' => '',
                'link' => '',
                'note' => '',
                'position' => '1',
                'isactive' => '1',
                'user_create' => 'system',
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }

    /**
     * Manual Layout - User fully controls structure
     * View location: resources/views/pages/{dmenu}/
     * 
     * Requirements:
     *   - gmenu must have name = '-' (e.g., 'blankx')
     *   - Creates view files in resources/views/pages/{dmenu}/
     *   - No sys_table entries created
     */
    protected function insertManualTableConfig(): void
    {
        // Manual layout tidak insert sys_table
        // Hanya buat view files di resources/views/pages/{dmenu}/
        
        // Check jika gmenu name = '-' (untuk routing ke pages.{dmenu})
        $gmenu = DB::table('sys_gmenu')
            ->where('gmenu', $this->menuData['gmenu'])
            ->first();
            
        if ($gmenu && $gmenu->name === '-') {
            // Delegate ke FileGeneratorService
            $this->generator->generateManualViews($this->menuData['dmenu']);
            info("✓ View files created at resources/views/pages/{$this->menuData['dmenu']}/");
            info("  - list.blade.php, add.blade.php, edit.blade.php, show.blade.php");
        } else {
            warning("⚠ Manual layout requires gmenu with name = '-' (e.g., 'blankx')");
            info("  View path will be: {$this->menuData['gmenu']}.{$this->menuData['url']}");
        }
    }

    /**
     * Transc Layout - Transaction with header-detail split
     */
    protected function insertTranscTableConfig(): void
    {
        if (empty($this->tableFields)) {
            return;
        }

        foreach ($this->tableFields as $field) {
            // Determine position: header (1) or detail (2)
            $position = $field['position'] === 'H' ? '1' : '2';

            $this->insertFormField($field, [
                'filter' => '0',
                'list' => '1',
                'show' => '1',
                'position' => $position,
            ]);
        }
    }

    /**
     * Default/Fallback handler
     */
    protected function insertDefaultTableConfig(): void
    {
        $this->insertStandardTableConfig();
    }

    /**
     * Insert a filter field (for reports)
     * Pattern dari seeder example_tabel_rpt_syslog.php:
     *   - Minimal essential fields only
     *   - Fields: gmenu, dmenu, urut, field, alias, type, length (optional), validate, filter, query, class (optional)
     */
    protected function insertFilterField(array $field, int $urut): void
    {
        $data = [
            'gmenu' => $this->menuData['gmenu'],
            'dmenu' => $this->menuData['dmenu'],
            'urut' => $urut,
            'field' => $field['field'],
            'alias' => $field['label'],
            'type' => $field['type'],
            'validate' => $this->buildValidationRules($field),
            'filter' => '1',
        ];

        // Add optional fields if present
        if (!empty($field['length'])) {
            $data['length'] = $field['length'];
        }
        
        if (!empty($field['query'])) {
            $data['query'] = $field['query'];
        }
        
        if (!empty($field['class'])) {
            $data['class'] = $field['class'];
        }

        DB::table('sys_table')->insert($data);
    }

    /**
     * Insert a form field with custom overrides
     */
    protected function insertFormField(array $field, array $overrides = []): void
    {
        $decimals = $this->determineDecimalPlaces($field['db_type'] ?? null);

        $data = array_merge([
            'gmenu' => $this->menuData['gmenu'],
            'dmenu' => $this->menuData['dmenu'],
            'urut' => $field['urut'],
            'field' => $field['field'],
            'alias' => $field['label'],
            'type' => $field['type'],
            'length' => $field['length'] ?? 0,
            'decimals' => $decimals,
            'default' => $field['default'] ?? '',
            'validate' => $this->buildValidationRules($field),
            'primary' => $field['primary'] ?? '0',
            'generateid' => $field['idenum'] ?? '',
            'filter' => '1',
            'list' => '1',
            'show' => $field['readonly'] === '0' ? '1' : '0',
            'query' => $field['query'] ?? '',
            'class' => $field['class'] ?? '',
            'sub' => $field['sub'] ?? '',
            'link' => $field['link'] ?? '',
            'note' => $field['note'] ?? '',
            'position' => '0',
            'isactive' => '1',
            'user_create' => 'system',
            'created_at' => now(),
            'updated_at' => now(),
        ], $overrides);

        DB::table('sys_table')->insert($data);
    }

    /**
     * Build validation rules from field metadata
     */
    protected function buildValidationRules(array $field): string
    {
        $rules = [];

        if ($field['required'] === '1') {
            $rules[] = 'required';
        }

        if (!empty($field['length']) && $field['length'] > 0) {
            $rules[] = "max:{$field['length']}";
        }

        return implode('|', $rules);
    }

    /**
     * Convert position letter to enum value
     */
    protected function convertPosition(string $position): string
    {
        $map = [
            'S' => '0', // Standard
            'H' => '1', // Header
            'D' => '2', // Detail
            'L' => '3', // Left
            'R' => '4', // Right
            'F' => '0', // Full (default to standard)
        ];

        return $map[$position] ?? '0';
    }

    /**
     * Generate basic report query
     */
    protected function generateReportQuery(): string
    {
        $table = $this->menuData['table'];
        $fields = collect($this->tableFields)->pluck('field')->implode(', ');
        
        if (empty($fields)) {
            $fields = '*';
        }
        
        return "SELECT {$fields} FROM {$table} WHERE isactive = '1' ORDER BY created_at DESC";
    }

    /**
     * Generate sublink query (list sub-menu from sys_dmenu)
     */
    protected function generateSublinkQuery(): string
    {
        $parentLink = $this->menuData['parent_link'] ?? $this->menuData['dmenu'];
        
        // Query untuk mendapatkan list sub-menu yang terhubung ke parent sublink
        return "SELECT gmenu, dmenu, icon, tabel, name AS Detail FROM sys_dmenu WHERE sub = '{$parentLink}'";
    }

    /**
     * Determine decimal places from database type
     */
    protected function determineDecimalPlaces(?string $dbType): string
    {
        if (!$dbType) {
            return '0';
        }

        $type = strtolower($dbType);

        // Extract decimal places from definition like decimal(10,2)
        if (preg_match('/\(\s*\d+\s*,\s*(\d+)\s*\)/', $type, $matches)) {
            $decimals = (int) $matches[1];
            return (string) min(3, max(0, $decimals));
        }

        // Default decimal places for float/double
        if (str_contains($type, 'decimal') || str_contains($type, 'float') || str_contains($type, 'double')) {
            return '2';
        }

        return '0';
    }
}
