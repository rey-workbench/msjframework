<?php

namespace MSJFramework\LaravelGenerator\Services\Templates;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class ModelTemplate
{
    public static function getTemplate(array $config): string
    {
        $modelName = Str::studly(Str::singular($config['table']));
        $table = $config['table'];

        // Get all columns from table
        $columns = self::getTableColumns($table);

        // Get primary key from columns or config
        $primaryKey = self::getPrimaryKey($columns, $config);

        // Generate fillable array - exclude auto-generated fields
        $fillable = self::generateFillable($columns, $primaryKey);

        // Format fillable array
        $fillableString = self::formatFillableArray($fillable);

        // Check if primary key is auto increment
        $primaryColumn = collect($columns)->firstWhere('Field', $primaryKey);
        $isIncrementing = $primaryColumn && str_contains($primaryColumn->Extra ?? '', 'auto_increment');
        $incrementingLine = $isIncrementing ? '' : '    public $incrementing = false;';

        return <<<PHP
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class {$modelName} extends Model
{
    public const TABLE = '{$table}';
    public const FIELD_CREATED_AT = 'created_at';
    public const FIELD_UPDATED_AT = 'updated_at';

    protected \$table = '{$table}';
    protected \$primaryKey = '{$primaryKey}';
    public \$timestamps = true;
{$incrementingLine}
    protected \$fillable = [
{$fillableString}
    ];
}

PHP;
    }

    protected static function getTableColumns(string $table): array
    {
        try {
            return DB::select("SHOW COLUMNS FROM {$table}");
        } catch (\Exception $e) {
            return [];
        }
    }

    protected static function getPrimaryKey(array $columns, array $config): string
    {
        // Try to get from config fields first
        if (isset($config['fields']) && is_array($config['fields'])) {
            $primaryField = collect($config['fields'])->firstWhere('primary', '1');
            if ($primaryField && isset($primaryField['field'])) {
                return $primaryField['field'];
            }
        }

        // Get from database columns
        foreach ($columns as $column) {
            if ($column->Key === 'PRI') {
                return $column->Field;
            }
        }

        // Default fallback
        return 'id';
    }

    protected static function generateFillable(array $columns, string $primaryKey): array
    {
        $fillable = [];
        $excludedFields = [
            'created_at',
            'updated_at',
        ];

        foreach ($columns as $column) {
            $fieldName = $column->Field;

            // Skip excluded fields
            if (in_array($fieldName, $excludedFields)) {
                continue;
            }

            // Skip auto increment primary key (but include non-auto-increment primary keys)
            if ($fieldName === $primaryKey && str_contains($column->Extra ?? '', 'auto_increment')) {
                continue;
            }

            $fillable[] = $fieldName;
        }

        return $fillable;
    }

    protected static function formatFillableArray(array $fillable): string
    {
        if (empty($fillable)) {
            return '        // No fillable fields';
        }

        $formatted = [];
        foreach ($fillable as $field) {
            $formatted[] = "        '{$field}',";
        }

        return implode("\n", $formatted);
    }
}
