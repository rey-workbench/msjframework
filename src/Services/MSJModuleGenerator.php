<?php

namespace MSJFramework\LaravelGenerator\Services;

use MSJFramework\LaravelGenerator\Services\Templates\AddView;
use MSJFramework\LaravelGenerator\Services\Templates\ControllerTemplate;
use MSJFramework\LaravelGenerator\Services\Templates\EditView;
use MSJFramework\LaravelGenerator\Services\Templates\JavascriptTemplate;
use MSJFramework\LaravelGenerator\Services\Templates\JsComponent;
use MSJFramework\LaravelGenerator\Services\Templates\ListView;
use MSJFramework\LaravelGenerator\Services\Templates\ModelTemplate;
use MSJFramework\LaravelGenerator\Services\Templates\MSJBaseControllerTemplate;
use MSJFramework\LaravelGenerator\Services\Templates\ShowView;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

class MSJModuleGenerator
{
    protected array $config;

    public function __construct(array $config = [])
    {
        // Normalize URL for PageController compatibility
        // PageController uses ucfirst() so we need to remove dashes and keep lowercase
        if (isset($config['url']) && str_contains($config['url'], '-')) {
            $config['url'] = str_replace('-', '', $config['url']);
        }

        $this->config = $config;
    }

    public function setConfig(array $config): self
    {
        $this->config = array_merge($this->config, $config);

        return $this;
    }

    public function getConfig(?string $key = null): mixed
    {
        if ($key === null) {
            return $this->config;
        }

        return $this->config[$key] ?? null;
    }

    public function validateBeforeGenerate(): array
    {
        $errors = [];
        $warnings = [];

        // Validate required config
        $required = ['table', 'dmenu', 'url', 'gmenu'];
        foreach ($required as $key) {
            if (empty($this->config[$key] ?? null)) {
                $errors[] = "Konfigurasi '{$key}' harus diisi";

                return [
                    'valid' => false,
                    'errors' => $errors,
                    'warnings' => $warnings,
                ];
            }
        }

        // Check if table exists
        if (! $this->checkTableExists($this->config['table'])) {
            $errors[] = "Tabel '{$this->config['table']}' tidak ditemukan di database";
        }

        // Check if dmenu already registered
        if ($this->checkMenuExists($this->config['dmenu'])) {
            $warnings[] = "Menu dengan dmenu '{$this->config['dmenu']}' sudah terdaftar";
        }

        // Check if model already exists
        $modelName = Str::studly(Str::singular($this->config['table']));
        if ($this->checkModelExists($modelName)) {
            $warnings[] = "Model '{$modelName}' sudah ada";
        }

        // Check if controller already exists
        // URL already normalized in constructor (dashes removed)
        $controllerName = Str::studly($this->config['url']).'Controller';
        if ($this->checkControllerExists($controllerName)) {
            $warnings[] = "Controller '{$controllerName}' sudah ada";
        }

        // Check if views already exist
        if ($this->checkViewsExist()) {
            $warnings[] = "Views untuk '{$this->config['url']}' sudah ada";
        }

        // Check if JS already exists
        if ($this->checkJsExists()) {
            $warnings[] = "JS file '{$this->config['dmenu']}.blade.php' sudah ada";
        }

        return [
            'valid' => empty($errors),
            'errors' => $errors,
            'warnings' => $warnings,
        ];
    }

    public function checkTableExists(string $table): bool
    {
        try {
            $result = DB::select("SHOW TABLES LIKE '{$table}'");

            return ! empty($result);
        } catch (\Exception $e) {
            return false;
        }
    }

    public function checkMenuExists(string $dmenu): bool
    {
        return DB::table('sys_dmenu')
            ->where('dmenu', $dmenu)
            ->exists();
    }

    public function checkModelExists(string $modelName): bool
    {
        return File::exists(app_path("Models/{$modelName}.php"));
    }

    public function checkControllerExists(string $controllerName): bool
    {
        return File::exists(app_path("Http/Controllers/{$controllerName}.php"));
    }

    public function checkViewsExist(): bool
    {
        $viewsDir = resource_path("views/{$this->config['gmenu']}/{$this->config['url']}");

        return File::exists($viewsDir);
    }

    public function checkJsExists(): bool
    {
        return File::exists(resource_path("views/js/{$this->config['dmenu']}.blade.php"));
    }

    public function generateModel(): array
    {
        $modelName = Str::studly(Str::singular($this->config['table']));
        $modelPath = app_path("Models/{$modelName}.php");

        $exists = File::exists($modelPath);

        // Always regenerate to ensure fillable is up to date
        $content = ModelTemplate::getTemplate($this->config);
        File::put($modelPath, $content);

        return [
            'status' => $exists ? 'updated' : 'created',
            'message' => $exists ? 'Model diperbarui' : 'Model dibuat',
            'path' => $modelPath,
        ];
    }

    public function generateController(): array
    {
        // Ensure MSJBaseController and ValidationHelper exist first
        MSJBaseControllerTemplate::createIfNotExists();
        \App\Services\Templates\Helpers\ValidationHelperTemplate::createIfNotExists();

        // Generate controller name compatible with PageController
        // PageController uses ucfirst() so we need to match that behavior
        $controllerName = Str::studly($this->config['url']).'Controller';
        $controllerPath = app_path("Http/Controllers/{$controllerName}.php");

        $exists = File::exists($controllerPath);

        // Always regenerate to ensure it's up to date
        $content = ControllerTemplate::getTemplate($this->config);
        File::put($controllerPath, $content);

        return [
            'status' => $exists ? 'updated' : 'created',
            'message' => $exists ? 'Controller diperbarui' : 'Controller dibuat',
            'path' => $controllerPath,
        ];
    }

    public function generateViews(): array
    {
        // Check if gmenu name is "-" (use pages folder instead)
        $gmenuName = DB::table('sys_gmenu')
            ->where('gmenu', $this->config['gmenu'])
            ->value('name');

        // Views folder: if gmenu name is "-", use pages/{url}, otherwise use {gmenu}/{url}
        $viewBasePath = ($gmenuName === '-') ? 'pages' : $this->config['gmenu'];
        $viewsDir = resource_path("views/{$viewBasePath}/{$this->config['url']}");

        // Check if all view files exist
        $views = ['list', 'add', 'edit', 'show'];
        $allExist = File::exists($viewsDir);
        $missingFiles = [];

        if ($allExist) {
            foreach ($views as $view) {
                $filePath = "{$viewsDir}/{$view}.blade.php";
                if (! File::exists($filePath)) {
                    $missingFiles[] = $view;
                    $allExist = false;
                }
            }
        }

        // Create directory if not exists
        if (! File::exists($viewsDir)) {
            File::makeDirectory($viewsDir, 0755, true);
        }

        // Regenerate all views to ensure consistency
        $created = 0;
        $updated = 0;

        foreach ($views as $view) {
            $filePath = "{$viewsDir}/{$view}.blade.php";
            $exists = File::exists($filePath);

            $content = $this->buildViewContent($view);
            File::put($filePath, $content);

            $exists ? $updated++ : $created++;
        }

        $message = [];
        if ($created > 0) {
            $message[] = "{$created} view dibuat";
        }
        if ($updated > 0) {
            $message[] = "{$updated} view diperbarui";
        }

        return [
            'status' => 'success',
            'message' => implode(', ', $message),
            'path' => $viewsDir,
        ];
    }

    public function generateJavascript(): array
    {
        // Ensure JS component exists
        JsComponent::createComponentIfNotExists();

        // JS file: /resources/views/js/{dmenu}.blade.php
        $jsFile = resource_path("views/js/{$this->config['dmenu']}.blade.php");

        if (File::exists($jsFile)) {
            return ['status' => 'skipped', 'message' => 'JS file sudah ada', 'path' => $jsFile];
        }

        $content = JavascriptTemplate::getTemplate();
        File::put($jsFile, $content);

        return ['status' => 'success', 'path' => $jsFile];
    }

    public function registerMenu(): array
    {
        try {
            // Check if menu already exists
            $exists = DB::table('sys_dmenu')
                ->where('dmenu', $this->config['dmenu'])
                ->exists();

            // URL is already normalized in constructor for manual layout
            $urlForMenu = $this->config['url'];

            if ($exists) {
                // Update existing menu
                DB::table('sys_dmenu')
                    ->where('dmenu', $this->config['dmenu'])
                    ->update([
                        'gmenu' => $this->config['gmenu'],
                        'name' => $this->config['menu_name'],
                        'url' => $urlForMenu,
                        'tabel' => $this->config['table'],
                        'layout' => $this->config['layout'] ?? 'manual',
                        'isactive' => '1',
                    ]);

                return ['status' => 'updated', 'message' => 'Menu diperbarui (sudah ada sebelumnya)'];
            }

            $maxUrut = DB::table('sys_dmenu')
                ->where('gmenu', $this->config['gmenu'])
                ->max('urut');

            DB::table('sys_dmenu')->insert([
                'gmenu' => $this->config['gmenu'],
                'dmenu' => $this->config['dmenu'],
                'name' => $this->config['menu_name'],
                'url' => $urlForMenu,
                'tabel' => $this->config['table'],
                'layout' => $this->config['layout'] ?? 'manual',
                'urut' => ($maxUrut ?? 0) + 1,
                'isactive' => '1',
            ]);

            return ['status' => 'success'];
        } catch (\Exception $e) {
            return ['status' => 'error', 'message' => 'Error: '.substr($e->getMessage(), 0, 100).'...'];
        }
    }

    public function registerTableConfig(): array
    {
        try {
            $table = $this->config['table'];

            // Get columns from database table
            $columns = $this->getTableColumns($table);
            if (empty($columns)) {
                return ['status' => 'error', 'message' => 'Tidak ada kolom ditemukan di tabel'];
            }

            // Delete existing configuration first to ensure clean state
            $deleted = DB::table('sys_table')
                ->where('gmenu', $this->config['gmenu'])
                ->where('dmenu', $this->config['dmenu'])
                ->delete();

            // Convert columns to field configuration
            $fields = $this->mapColumnsToFields($columns);
            $inserted = 0;

            foreach ($fields as $field) {
                DB::table('sys_table')->insert($field);
                $inserted++;
            }

            $message = $deleted > 0
                ? "Konfigurasi diperbarui ({$deleted} field lama dihapus, {$inserted} field baru dibuat)"
                : "Konfigurasi tabel berhasil didaftarkan ({$inserted} field dibuat)";

            return ['status' => 'success', 'message' => $message];
        } catch (\Exception $e) {
            return ['status' => 'error', 'message' => 'Error: '.substr($e->getMessage(), 0, 100).'...'];
        }
    }

    public function registerAuthorization(): array
    {
        try {
            // Get all active roles
            $roles = DB::table('sys_roles')->where('isactive', '1')->pluck('idroles');
            $created = 0;
            $updated = 0;

            // Use updateOrInsert to avoid duplicate entry errors
            // Primary key is composite: ['dmenu', 'idroles']
            foreach ($roles as $role) {
                $exists = DB::table('sys_auth')
                    ->where('dmenu', $this->config['dmenu'])
                    ->where('idroles', $role)
                    ->exists();

                DB::table('sys_auth')->updateOrInsert(
                    [
                    'dmenu' => $this->config['dmenu'],
                    'idroles' => $role,
                    ],
                    [
                        'gmenu' => $this->config['gmenu'],
                    'add' => '1',
                    'edit' => '1',
                    'delete' => '1',
                    'approval' => '0',
                    'value' => '1',
                    'print' => '1',
                    'excel' => '1',
                    'pdf' => '1',
                    'rules' => '0',
                    'isactive' => '1',
                    ]
                );

                if ($exists) {
                    $updated++;
                } else {
                $created++;
                }
            }

            $message = $updated > 0
                ? "Authorization diperbarui ({$updated} role diperbarui, {$created} role baru dibuat)"
                : "Authorization berhasil didaftarkan ({$created} role dibuat)";

            return ['status' => 'success', 'message' => $message];
        } catch (\Exception $e) {
            return ['status' => 'error', 'message' => 'Error: '.substr($e->getMessage(), 0, 100).'...'];
        }
    }

    public function getTableColumns(string $table): array
    {
        try {
            return DB::select("SHOW COLUMNS FROM {$table}");
        } catch (\Exception $e) {
            return [];
        }
    }

    public function mapColumnsToFields(array $columns): array
    {
        $fields = [];
        $urut = 1;

        foreach ($columns as $column) {
            $fields[] = [
                'gmenu' => $this->config['gmenu'] ?? null,
                'dmenu' => $this->config['dmenu'] ?? null,
                'field' => $column->Field,
                'alias' => Str::title(str_replace('_', ' ', $column->Field)),
                'type' => $this->detectFieldType($column->Type, $column->Field),
                'length' => $this->extractLength($column->Type),
                'validate' => $this->generateValidation($column),
                'primary' => $column->Key === 'PRI' ? '1' : '0',
                'position' => $urut <= 2 ? '3' : '4',
                'urut' => $urut++,
                'list' => '1',
                'show' => '1',
                'filter' => '1',
            ];
        }

        return $fields;
    }

    protected function detectFieldType(string $type, string $field): string
    {
        $typeMap = [
            'email' => 'string',
            'password' => 'password',
            'foto' => 'image',
            'gambar' => 'image',
            'image' => 'image',
            'file' => 'file',
            'dokumen' => 'file',
        ];

        foreach ($typeMap as $keyword => $fieldType) {
            if (str_contains($field, $keyword)) {
                return $fieldType;
            }
        }

        if (str_contains($field, 'tanggal') || str_contains($field, 'date')) {
            return str_contains($field, 'jam') || str_contains($type, 'datetime') ? 'datetime' : 'date';
        }

        if (str_contains($type, 'text')) {
            return 'text';
        }

        if (str_contains($type, 'int') || str_contains($type, 'decimal') || str_contains($type, 'float')) {
            return 'number';
        }

        return 'string';
    }

    protected function extractLength(string $type): string
    {
        if (preg_match('/\((\d+)\)/', $type, $matches)) {
            return $matches[1];
        }

        return '255';
    }

    protected function generateValidation($column): string
    {
        if ($column->Key === 'PRI') {
            return "unique:{$this->config['table']},{$column->Field}";
        }

        $rules = [];
        $skipFields = ['created_at', 'updated_at', 'user_create', 'user_update', 'isactive'];

        if ($column->Null === 'NO' && ! in_array($column->Field, $skipFields)) {
            $rules[] = 'required';
        }

        if (str_contains($column->Field, 'email')) {
            $rules[] = 'email';
        }

        return implode('|', $rules);
    }

    protected function buildViewContent(string $view): string
    {
        return match ($view) {
            'list' => ListView::getTemplate($this->config['dmenu']),
            'add' => AddView::getTemplate($this->config['dmenu']),
            'edit' => EditView::getTemplate($this->config['dmenu']),
            'show' => ShowView::getTemplate(),
            default => '',
        };
    }
}
