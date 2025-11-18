<?php

namespace MSJFramework\Services;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Collection;

class DatabaseIntrospectionService
{
    /**
     * Get all available tables from database
     */
    public function getAvailableTables(): array
    {
        try {
            $tables = DB::select('SHOW TABLES');
            $database = DB::getDatabaseName();
            $key = "Tables_in_{$database}";
            
            return collect($tables)
                ->pluck($key)
                ->filter(fn($table) => !str_starts_with($table, 'sys_'))
                ->mapWithKeys(fn($table) => [$table => $table])
                ->toArray();
        } catch (\Exception $e) {
            return [];
        }
    }

    /**
     * Detect table fields with metadata
     */
    public function detectTableFields(string $tableName): array
    {
        try {
            $columns = DB::select("SHOW COLUMNS FROM {$tableName}");
            $fields = [];
            $urut = 1;
            
            foreach ($columns as $column) {
                if ($this->isSystemField($column->Field)) {
                    continue;
                }
                
                $fields[] = [
                    'field' => $column->Field,
                    'label' => $this->generateLabel($column->Field),
                    'type' => $this->mapDatabaseTypeToMSJType($column->Type),
                    'db_type' => $column->Type,
                    'length' => $this->extractLength($column->Type),
                    'position' => $urut % 2 === 1 ? 'L' : 'R',
                    'required' => $column->Null === 'NO' ? '1' : '0',
                    'readonly' => '0',
                    'nullable' => $column->Null === 'YES',
                    'default' => $column->Default,
                    'idenum' => '',
                    'primary' => $column->Key === 'PRI' ? '1' : '0',
                    'urut' => $urut,
                ];
                
                $urut++;
            }
            
            return $fields;
        } catch (\Exception $e) {
            return [];
        }
    }

    /**
     * Detect relationships from foreign keys
     */
    public function detectRelationships(string $tableName): array
    {
        $relationships = [];
        
        try {
            // belongsTo relationships
            $foreignKeys = DB::select("
                SELECT 
                    COLUMN_NAME,
                    REFERENCED_TABLE_NAME,
                    REFERENCED_COLUMN_NAME
                FROM INFORMATION_SCHEMA.KEY_COLUMN_USAGE
                WHERE TABLE_SCHEMA = DATABASE()
                    AND TABLE_NAME = '{$tableName}'
                    AND REFERENCED_TABLE_NAME IS NOT NULL
            ");
            
            foreach ($foreignKeys as $fk) {
                $relationships[] = [
                    'type' => 'belongsTo',
                    'name' => str($fk->COLUMN_NAME)->replace('_id', '')->camel()->toString(),
                    'model' => str($fk->REFERENCED_TABLE_NAME)->studly()->singular()->toString(),
                    'foreign_key' => $fk->COLUMN_NAME,
                ];
            }
            
            // hasMany relationships
            $reverseKeys = DB::select("
                SELECT 
                    TABLE_NAME,
                    COLUMN_NAME
                FROM INFORMATION_SCHEMA.KEY_COLUMN_USAGE
                WHERE TABLE_SCHEMA = DATABASE()
                    AND REFERENCED_TABLE_NAME = '{$tableName}'
                    AND REFERENCED_COLUMN_NAME = 'id'
            ");
            
            foreach ($reverseKeys as $rk) {
                $relationships[] = [
                    'type' => 'hasMany',
                    'name' => str($rk->TABLE_NAME)->camel()->plural()->toString(),
                    'model' => str($rk->TABLE_NAME)->studly()->singular()->toString(),
                    'foreign_key' => $rk->COLUMN_NAME,
                ];
            }
        } catch (\Exception $e) {
            // Ignore FK detection errors
        }
        
        return $relationships;
    }

    /**
     * Get table columns for model fillable/casts
     */
    public function getTableColumns(string $tableName): Collection
    {
        try {
            $columns = DB::select("SHOW COLUMNS FROM {$tableName}");
            return collect($columns);
        } catch (\Exception $e) {
            return collect();
        }
    }

    /**
     * Detect cast type from database type
     */
    public function detectCastType(string $dbType): ?string
    {
        $type = strtolower($dbType);
        
        return match (true) {
            str_contains($type, 'int') => 'integer',
            str_contains($type, 'decimal') || str_contains($type, 'float') || str_contains($type, 'double') => 'decimal:2',
            str_contains($type, 'bool') => 'boolean',
            str_contains($type, 'date') && !str_contains($type, 'datetime') => 'date',
            str_contains($type, 'datetime') || str_contains($type, 'timestamp') => 'datetime',
            str_contains($type, 'json') => 'array',
            str_contains($type, 'enum') => 'string',
            default => null,
        };
    }

    /**
     * Map database type to MSJ field type
     */
    protected function mapDatabaseTypeToMSJType(string $dbType): string
    {
        $type = strtolower($dbType);
        
        if (preg_match('/^(\w+)/', $type, $matches)) {
            $baseType = $matches[1];
            
            return match ($baseType) {
                'varchar', 'char' => strlen($dbType) > 50 ? 'string' : 'char',
                'text', 'longtext', 'mediumtext' => 'text',
                'int', 'bigint', 'smallint', 'tinyint' => 'number',
                'decimal', 'float', 'double' => 'currency',
                'date', 'datetime', 'timestamp' => 'date',
                'enum' => 'enum',
                default => 'string',
            };
        }
        
        return 'string';
    }

    /**
     * Extract length from database type
     */
    protected function extractLength(string $dbType): int
    {
        if (preg_match('/\((\d+)\)/', $dbType, $matches)) {
            return (int) $matches[1];
        }
        
        $type = strtolower($dbType);
        return match (true) {
            str_contains($type, 'text') => 1000,
            str_contains($type, 'int') => 11,
            str_contains($type, 'decimal') => 10,
            default => 100,
        };
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
     * Check if field is system field
     */
    protected function isSystemField(string $fieldName): bool
    {
        return in_array($fieldName, [
            'id',
            'created_at',
            'updated_at',
            'deleted_at',
            'user_create',
            'user_update'
        ]);
    }

    /**
     * Validate table name
     */
    public function validateTableName(string $value): ?string
    {
        if (!preg_match('/^[a-z_][a-z0-9_]*$/', $value)) {
            return 'Table name must be valid SQL identifier (lowercase, underscores)';
        }

        try {
            if (!DB::getSchemaBuilder()->hasTable($value)) {
                return "Table '{$value}' does not exist in database";
            }
        } catch (\Exception $e) {
            return 'Error checking table: ' . $e->getMessage();
        }

        return null;
    }
}
