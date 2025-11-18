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
     * Report Layout - Query definition + filter fields
     */
    protected function insertReportTableConfig(): void
    {
        if (empty($this->tableFields)) {
            return;
        }

        // First row: query definition
        DB::table('sys_table')->insert([
            'gmenu' => $this->menuData['gmenu'],
            'dmenu' => $this->menuData['dmenu'],
            'urut' => 1,
            'field' => 'query',
            'alias' => 'Report Query',
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
            'query' => $this->generateReportQuery(),
            'class' => '',
            'sub' => '',
            'link' => '',
            'note' => '',
            'position' => '0',
            'isactive' => '1',
            'user_create' => 'system',
            'created_at' => now(),
            'updated_at' => now(),
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
     */
    protected function insertSystemTableConfig(): void
    {
        if (empty($this->tableFields)) {
            return;
        }

        foreach ($this->tableFields as $field) {
            $this->insertFormField($field, [
                'filter' => '1',
                'list' => '1',
                'show' => '1',
                'position' => '2', // System layout typically uses position 2
            ]);
        }
    }

    /**
     * Sublink Layout - Parent-child relationship form
     */
    protected function insertSublinkTableConfig(): void
    {
        if (empty($this->tableFields)) {
            return;
        }

        foreach ($this->tableFields as $field) {
            // Determine position based on field characteristics
            $position = '0';
            if ($field['primary'] === '1') {
                $position = '1'; // Primary key goes to header
            } elseif (!empty($field['link'])) {
                $position = '1'; // Linked field goes to header
            } else {
                $position = '2'; // Detail fields
            }

            $this->insertFormField($field, [
                'filter' => '1',
                'list' => '1',
                'show' => '1',
                'position' => $position,
                'link' => $field['link'] ?? '',
            ]);
        }
    }

    /**
     * Manual Layout - User fully controls structure
     */
    protected function insertManualTableConfig(): void
    {
        // Manual layout typically doesn't insert sys_table
        // Controller and views are generated from stubs
        return;
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
     */
    protected function insertFilterField(array $field, int $urut): void
    {
        $decimals = $this->determineDecimalPlaces($field['db_type'] ?? null);

        DB::table('sys_table')->insert([
            'gmenu' => $this->menuData['gmenu'],
            'dmenu' => $this->menuData['dmenu'],
            'urut' => $urut,
            'field' => $field['field'],
            'alias' => $field['label'],
            'type' => $field['type'],
            'length' => $field['length'] ?? 0,
            'decimals' => $decimals,
            'default' => $field['default'] ?? '',
            'validate' => $field['required'] === '1' ? 'required' : 'nullable',
            'primary' => '0',
            'generateid' => '',
            'filter' => '1',
            'list' => '0',
            'show' => '0',
            'query' => $field['query'] ?? '',
            'class' => $field['class'] ?? '',
            'sub' => '',
            'link' => '',
            'note' => $field['note'] ?? '',
            'position' => '0',
            'isactive' => '1',
            'user_create' => 'system',
            'created_at' => now(),
            'updated_at' => now(),
        ]);
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
