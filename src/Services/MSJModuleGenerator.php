<?php

namespace MSJFramework\LaravelGenerator\Services;

use MSJFramework\LaravelGenerator\Services\Generation\ControllerGeneratorService;
use MSJFramework\LaravelGenerator\Services\Generation\JavascriptGeneratorService;
use MSJFramework\LaravelGenerator\Services\Generation\ModelGeneratorService;
use MSJFramework\LaravelGenerator\Services\Generation\ViewGeneratorService;
use MSJFramework\LaravelGenerator\Services\Validation\ModuleValidationService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class MSJModuleGenerator
{
    protected array $config;
    protected ModuleValidationService $validator;
    protected ModelGeneratorService $modelGenerator;
    protected ControllerGeneratorService $controllerGenerator;
    protected ViewGeneratorService $viewGenerator;
    protected JavascriptGeneratorService $javascriptGenerator;

    public function __construct(array $config = [])
    {
        // Normalize URL for PageController compatibility
        if (isset($config['url']) && str_contains($config['url'], '-')) {
            $config['url'] = str_replace('-', '', $config['url']);
        }

        $this->config = $config;
        $this->validator = new ModuleValidationService();
        $this->modelGenerator = new ModelGeneratorService();
        $this->controllerGenerator = new ControllerGeneratorService();
        $this->viewGenerator = new ViewGeneratorService();
        $this->javascriptGenerator = new JavascriptGeneratorService();
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
        return $this->validator->validateModuleConfig($this->config);
    }

    public function generateModel(): array
    {
        return $this->modelGenerator->generate($this->config);
    }

    public function generateController(): array
    {
        return $this->controllerGenerator->generate($this->config);
    }

    public function generateViews(): array
    {
        return $this->viewGenerator->generate($this->config);
    }

    public function generateJavascript(): array
    {
        return $this->javascriptGenerator->generate($this->config);
    }

    public function registerMenu(): array
    {
        try {
            $exists = DB::table('sys_dmenu')
                ->where('dmenu', $this->config['dmenu'])
                ->exists();

            $urlForMenu = $this->config['url'];

            if ($exists) {
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
            $columns = $this->getTableColumns($table);
            
            if (empty($columns)) {
                return ['status' => 'error', 'message' => 'Tidak ada kolom ditemukan di tabel'];
            }

            $deleted = DB::table('sys_table')
                ->where('gmenu', $this->config['gmenu'])
                ->where('dmenu', $this->config['dmenu'])
                ->delete();

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
            $roles = DB::table('sys_roles')->where('isactive', '1')->pluck('idroles');
            $created = 0;
            $updated = 0;

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

                $exists ? $updated++ : $created++;
            }

            $message = $updated > 0
                ? "Authorization diperbarui ({$updated} role diperbarui, {$created} role baru dibuat)"
                : "Authorization berhasil didaftarkan ({$created} role dibuat)";

            return ['status' => 'success', 'message' => $message];
        } catch (\Exception $e) {
            return ['status' => 'error', 'message' => 'Error: '.substr($e->getMessage(), 0, 100).'...'];
        }
    }

    protected function getTableColumns(string $table): array
    {
        try {
            return DB::select("SHOW COLUMNS FROM {$table}");
        } catch (\Exception $e) {
            return [];
        }
    }

    protected function mapColumnsToFields(array $columns): array
    {
        $fields = [];
        $urut = 1;
        $layoutType = $this->config['layout'] ?? 'manual';

        foreach ($columns as $column) {
            $position = $this->determinePosition($column, $urut, $layoutType);
            $fieldType = $this->detectFieldType($column->Type, $column->Field);
            
            if ($column->Key === 'PRI' && $column->Extra === 'auto_increment') {
                $fieldType = 'hidden';
            }
            
            $fields[] = [
                'gmenu' => $this->config['gmenu'] ?? null,
                'dmenu' => $this->config['dmenu'] ?? null,
                'field' => $column->Field,
                'alias' => Str::title(str_replace('_', ' ', $column->Field)),
                'type' => $fieldType,
                'length' => $this->extractLength($column->Type),
                'decimals' => $this->extractDecimals($column->Type),
                'default' => $column->Default ?? '',
                'validate' => $this->generateValidation($column),
                'primary' => $column->Key === 'PRI' ? '1' : '0',
                'generateid' => '',
                'filter' => $this->shouldBeFilterable($column) ? '1' : '0',
                'list' => $this->shouldBeInList($column) ? '1' : '0',
                'show' => $this->shouldBeShown($column, $fieldType) ? '1' : '0',
                'query' => '',
                'class' => '',
                'note' => '',
                'sub' => '',
                'link' => '',
                'position' => $position,
                'urut' => $urut++,
            ];
        }

        return $fields;
    }

    protected function determinePosition($column, int $urut, string $layoutType): string
    {
        $isPrimary = $column->Key === 'PRI';
        
        return match ($layoutType) {
            'sublnk' => $isPrimary && $urut === 1 ? '1' : '2',
            'system' => $isPrimary ? '1' : '2',
            'transc' => $urut <= 2 ? '1' : '2',
            'master' => $urut <= 7 ? '3' : '4',
            default => '1',
        };
    }

    protected function extractDecimals(string $type): string
    {
        if (preg_match('/\(\d+,(\d+)\)/', $type, $matches)) {
            return $matches[1];
        }

        return '0';
    }

    protected function shouldBeFilterable($column): bool
    {
        $skipFields = ['created_at', 'updated_at', 'user_create', 'user_update', 'deleted_at'];
        
        return ! in_array($column->Field, $skipFields);
    }

    protected function shouldBeInList($column): bool
    {
        $skipFields = ['created_at', 'updated_at', 'user_create', 'user_update', 'deleted_at', 'password'];
        
        return ! in_array($column->Field, $skipFields);
    }

    protected function shouldBeShown($column, string $fieldType): bool
    {
        $skipFields = ['created_at', 'updated_at', 'user_create', 'user_update', 'deleted_at', 'password'];
        
        if ($fieldType === 'hidden' || $column->Extra === 'auto_increment') {
            return false;
        }
        
        return ! in_array($column->Field, $skipFields);
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
}
