<?php

namespace MSJFramework\Services;
class FileGeneratorService
{
    protected DatabaseIntrospectionService $db;

    public function __construct(DatabaseIntrospectionService $db)
    {
        $this->db = $db;
    }

    // =====================================================
    // Public Generation Methods
    // =====================================================

    /**
     * Generate Model with relationships
     */
    public function generateModel(string $tableName): array
    {
        $modelName = $this->getModelName($tableName);
        $columns = $this->db->getTableColumns($tableName)->all();

        // Check if table has deleted_at column for SoftDeletes
        $hasDeletedAt = false;
        foreach ($columns as $column) {
            if ($column->Field === 'deleted_at') {
                $hasDeletedAt = true;
                break;
            }
        }

        $fillable = $this->extractFillableFields($columns);
        $casts = $this->extractCasts($columns);
        $relationships = $this->db->detectRelationships($tableName);

        // Load stub
        $stub = file_get_contents(__DIR__.'/../Framework/Models/models.stub');

        // Build replacements
        $fillableStr = "'" . implode("',\n        '", $fillable) . "'";

        $castsStr = '';
        foreach ($casts as $field => $type) {
            $castsStr .= "        '{$field}' => '{$type}',\n";
        }
        $castsStr = rtrim($castsStr, ",\n");

        $relationshipsStr = '';
        foreach ($relationships as $rel) {
            $relationshipsStr .= $this->buildRelationshipMethod($rel);
        }

        // SoftDeletes conditional
        $softDeletesUse = $hasDeletedAt ? "use Illuminate\\Database\\Eloquent\\SoftDeletes;\n" : '';
        $softDeletesTrait = $hasDeletedAt ? "    use SoftDeletes;\n\n" : '';

        // Replace placeholders
        $content = str_replace([
            '{{modelName}}',
            '{{tableName}}',
            '{{fillable}}',
            '{{casts}}',
            '{{relationships}}',
            '{{softDeletesUse}}',
            '{{softDeletesTrait}}'
        ], [
            $modelName,
            $tableName,
            $fillableStr,
            $castsStr,
            $relationshipsStr,
            $softDeletesUse,
            $softDeletesTrait
        ], $stub);

        $path = $this->getModelPath($modelName);
        $this->ensureDirectoryExists(dirname($path));
        file_put_contents($path, $content);

        return ['name' => $modelName, 'path' => $path];
    }

    /**
     * Generate Controller with CRUD methods
     */
    public function generateController(string $url, string $tableName): array
    {
        $controllerName = $this->getControllerName($url);
        $modelName = $this->getModelName($tableName);
        
        // Load stub
        $stub = file_get_contents(__DIR__.'/../Framework/Controllers/Manual/controller.stub');
        
        // Replace placeholders
        $content = str_replace([
            '{{controllerName}}',
            '{{modelName}}'
        ], [
            $controllerName,
            $modelName
        ], $stub);
        
        $path = $this->getControllerPath($controllerName);
        file_put_contents($path, $content);
        
        return ['name' => $controllerName, 'path' => $path];
    }

    /**
     * Generate all views (list, add, edit, show)
     */
    public function generateViews(string $gmenu, string $url, string $tableName): array
    {
        $viewPath = $this->getViewPath($gmenu, $url);
        $this->ensureDirectoryExists($viewPath);
        
        $fields = $this->db->detectTableFields($tableName);
        
        // Generate list view
        $this->generateListView($viewPath, $fields);
        
        // Generate add view
        $this->generateAddView($viewPath, $fields);
        
        // Generate edit view
        $this->generateEditView($viewPath, $fields);
        
        // Generate show view
        $this->generateShowView($viewPath, $fields);
        
        return [
            'list' => $viewPath . '/list.blade.php',
            'add' => $viewPath . '/add.blade.php',
            'edit' => $viewPath . '/edit.blade.php',
            'show' => $viewPath . '/show.blade.php',
        ];
    }

    /**
     * Generate JavaScript file for menu
     */
    public function generateJavaScriptFile(string $dmenu): array
    {
        $jsPath = $this->getJavaScriptPath();
        $this->ensureDirectoryExists($jsPath);
        
        // Load stub
        $stub = file_get_contents(__DIR__.'/../Framework/Views/JavaScript/js.blade.stub');
        
        $filePath = $jsPath . '/' . $dmenu . '.blade.php';
        file_put_contents($filePath, $stub);
        
        return [
            'name' => $dmenu . '.blade.php',
            'path' => $filePath,
        ];
    }

    // =====================================================
    // View Generation Helpers
    // =====================================================

    protected function generateListView(string $viewPath, array $fields): void
    {
        $stub = file_get_contents(__DIR__.'/../Framework/Views/Manual/list.blade.stub');
        
        $listFields = array_slice($fields, 0, min(6, count($fields)));
        
        $tableHeaders = '';
        $tableRows = '';
        foreach ($listFields as $field) {
            $tableHeaders .= "                                <th>{$field['label']}</th>\n";
            $tableRows .= "                                    <td>{{ \$item->{$field['field']} }}</td>\n";
        }
        
        $content = str_replace([
            '{{tableHeaders}}',
            '{{tableRows}}'
        ], [
            $tableHeaders,
            $tableRows
        ], $stub);
        
        file_put_contents($viewPath . '/list.blade.php', $content);
    }

    protected function generateAddView(string $viewPath, array $fields): void
    {
        $stub = file_get_contents(__DIR__.'/../Framework/Views/Manual/add.blade.stub');
        
        $formFields = '';
        foreach ($fields as $field) {
            $formFields .= $this->buildFormField($field, 'add');
        }
        
        $content = str_replace('{{formFields}}', $formFields, $stub);
        file_put_contents($viewPath . '/add.blade.php', $content);
    }

    protected function generateEditView(string $viewPath, array $fields): void
    {
        $stub = file_get_contents(__DIR__.'/../Framework/Views/Manual/edit.blade.stub');
        
        $formFields = '';
        foreach ($fields as $field) {
            $formFields .= $this->buildFormField($field, 'edit');
        }
        
        $content = str_replace('{{formFields}}', $formFields, $stub);
        file_put_contents($viewPath . '/edit.blade.php', $content);
    }

    protected function generateShowView(string $viewPath, array $fields): void
    {
        $stub = file_get_contents(__DIR__.'/../Framework/Views/Manual/show.blade.stub');
        
        $detailFields = '';
        foreach ($fields as $field) {
            $detailFields .= <<<BLADE
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold">{$field['label']}</label>
                            <p class="form-control-static">{{ \$item->{$field['field']} ?? '-' }}</p>
                        </div>

BLADE;
        }
        
        $content = str_replace('{{detailFields}}', $detailFields, $stub);
        file_put_contents($viewPath . '/show.blade.php', $content);
    }

    protected function buildFormField(array $field, string $mode): string
    {
        $name = $field['field'];
        $label = $field['label'];
        $type = $field['type'];
        $required = $field['required'] === '1' ? 'required' : '';
        $value = $mode === 'edit' ? "{{ old('{$name}', \$item->{$name}) }}" : "{{ old('{$name}') }}";
        
        $colClass = $field['position'] === 'F' ? 'col-md-12' : 'col-md-6';
        
        $inputHtml = match($type) {
            'text' => "<textarea name=\"{$name}\" class=\"form-control\" rows=\"4\" {$required}>{$value}</textarea>",
            'date' => "<input type=\"date\" name=\"{$name}\" class=\"form-control\" value=\"{$value}\" {$required}>",
            'email' => "<input type=\"email\" name=\"{$name}\" class=\"form-control\" value=\"{$value}\" {$required}>",
            'number', 'currency' => "<input type=\"number\" name=\"{$name}\" class=\"form-control\" value=\"{$value}\" step=\"0.01\" {$required}>",
            default => "<input type=\"text\" name=\"{$name}\" class=\"form-control\" value=\"{$value}\" {$required}>",
        };
        
        return <<<BLADE
                        <div class="{$colClass} mb-3">
                            <label for="{$name}" class="form-label">{$label}</label>
                            {$inputHtml}
                            @error('{$name}')
                                <div class="text-danger text-xs mt-1">{{ \$message }}</div>
                            @enderror
                        </div>

BLADE;
    }

    protected function buildRelationshipMethod(array $rel): string
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

    // =====================================================
    // Helper Methods
    // =====================================================

    protected function getModelName(string $tableName): string
    {
        return str($tableName)->studly()->singular()->toString();
    }

    protected function getControllerName(string $url): string
    {
        return str($url)->studly()->append('Controller')->toString();
    }

    protected function getModelPath(string $modelName): string
    {
        return base_path("app/Models/{$modelName}.php");
    }

    protected function getControllerPath(string $controllerName): string
    {
        return base_path("app/Http/Controllers/{$controllerName}.php");
    }

    protected function getViewPath(string $gmenu, string $url): string
    {
        return base_path("resources/views/{$gmenu}/{$url}");
    }

    protected function getJavaScriptPath(): string
    {
        return base_path('resources/views/js');
    }

    protected function ensureDirectoryExists(string $path): void
    {
        if (!is_dir($path)) {
            mkdir($path, 0755, true);
        }
    }

    protected function extractFillableFields(array $columns): array
    {
        $fillable = [];
        
        foreach ($columns as $column) {
            // Skip auto-increment and timestamps
            if (in_array($column->Field, ['id', 'created_at', 'updated_at', 'deleted_at'])) {
                continue;
            }
            $fillable[] = $column->Field;
        }
        
        return $fillable;
    }

    protected function extractCasts(array $columns): array
    {
        $casts = [];
        
        foreach ($columns as $column) {
            if (in_array($column->Field, ['id', 'created_at', 'updated_at', 'deleted_at'])) {
                continue;
            }
            
            $castType = $this->db->detectCastType($column->Type);
            if ($castType) {
                $casts[$column->Field] = $castType;
            }
        }
        
        return $casts;
    }

    /**
     * Generate manual view files
     * 
     * Path logic (sesuai PageController):
     * - Jika gmenu name = '-' → resources/views/pages/{url}/
     * - Jika gmenu name != '-' → resources/views/{gmenu}/{url}/
     * 
     * @param string $gmenu Group menu code
     * @param string $url URL/folder name for views
     * @param string $gmenuName Name of gmenu (use '-' for pages folder)
     */
    public function generateManualViews(string $gmenu, string $url, string $gmenuName = '-'): void
    {
        $basePath = base_path();
        
        // Determine view path based on gmenu name
        if ($gmenuName === '-') {
            // gmenu name = '-' → views di pages/{url}/
            $viewPath = $basePath . '/resources/views/pages/' . $url;
        } else {
            // gmenu name != '-' → views di {gmenu}/{url}/
            $viewPath = $basePath . '/resources/views/' . $gmenu . '/' . $url;
        }
        
        // Buat folder jika belum ada
        if (!is_dir($viewPath)) {
            mkdir($viewPath, 0755, true);
        }
        
        // Path ke template stubs di framework
        $stubPath = __DIR__ . '/../Framework/Views/Manual';
        
        // Copy template files
        $templates = ['list', 'add', 'edit', 'show'];
        
        foreach ($templates as $template) {
            $stubFile = $stubPath . '/' . $template . '.blade.stub';
            $targetFile = $viewPath . '/' . $template . '.blade.php';
            
            if (file_exists($stubFile)) {
                $content = file_get_contents($stubFile);
                
                // Replace placeholders dengan empty untuk manual editing
                $content = str_replace('{{tableHeaders}}', '', $content);
                $content = str_replace('{{tableRows}}', '', $content);
                $content = str_replace('{{formFields}}', '', $content);
                $content = str_replace('{{detailFields}}', '', $content);
                
                file_put_contents($targetFile, $content);
            }
        }
    }
}
