<?php

namespace MSJFramework\LaravelGenerator\Templates;

class ModelTemplate
{
    public static function generate(string $modelName, string $tableName, array $fillable, array $casts, array $relationships): string
    {
        $fillableStr = "'" . implode("',\n        '", $fillable) . "'";
        
        $castsStr = '';
        foreach ($casts as $field => $type) {
            $castsStr .= "        '{$field}' => '{$type}',\n";
        }
        $castsStr = rtrim($castsStr, ",\n");
        
        $relationshipsStr = '';
        foreach ($relationships as $rel) {
            $relationshipsStr .= self::generateRelationshipMethod($rel);
        }
        
        return <<<PHP
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class {$modelName} extends Model
{
    use SoftDeletes;

    protected \$table = '{$tableName}';

    protected \$fillable = [
        {$fillableStr}
    ];

    protected \$casts = [
{$castsStr}
    ];
{$relationshipsStr}
}

PHP;
    }

    protected static function generateRelationshipMethod(array $rel): string
    {
        $methodName = $rel['name'];
        $relatedModel = $rel['model'];
        $foreignKey = $rel['foreign_key'];
        
        if ($rel['type'] === 'belongsTo') {
            return <<<PHP

    public function {$methodName}()
    {
        return \$this->belongsTo({$relatedModel}::class, '{$foreignKey}');
    }

PHP;
        } elseif ($rel['type'] === 'hasMany') {
            return <<<PHP

    public function {$methodName}()
    {
        return \$this->hasMany({$relatedModel}::class, '{$foreignKey}');
    }

PHP;
        }
        
        return '';
    }
}
