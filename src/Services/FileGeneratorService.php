<?php

namespace MSJFramework\LaravelGenerator\Services;

use MSJFramework\LaravelGenerator\Templates\ModelTemplate;
use MSJFramework\LaravelGenerator\Templates\ControllerTemplate;
use MSJFramework\LaravelGenerator\Templates\ViewTemplate;
use MSJFramework\LaravelGenerator\Templates\JavaScriptTemplate;

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
        $columns = $this->db->getTableColumns($tableName);
        
        $fillable = $this->extractFillableFields($columns);
        $casts = $this->extractCasts($columns);
        $relationships = $this->db->detectRelationships($tableName);
        
        $content = ModelTemplate::generate($modelName, $tableName, $fillable, $casts, $relationships);
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
        
        $content = ControllerTemplate::generate($controllerName, $modelName);
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
        
        // Generate each view using templates
        file_put_contents($viewPath . '/list.blade.php', ViewTemplate::generateList($fields));
        file_put_contents($viewPath . '/add.blade.php', ViewTemplate::generateAdd($fields));
        file_put_contents($viewPath . '/edit.blade.php', ViewTemplate::generateEdit($fields));
        file_put_contents($viewPath . '/show.blade.php', ViewTemplate::generateShow($fields));
        
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
        
        $content = JavaScriptTemplate::generate($dmenu);
        $filePath = $jsPath . '/' . $dmenu . '.blade.php';
        
        file_put_contents($filePath, $content);
        
        return [
            'name' => $dmenu . '.blade.php',
            'path' => $filePath,
        ];
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
}
