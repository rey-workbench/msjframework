<?php

namespace MSJFramework\LaravelGenerator\Services\Validation;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

class ModuleValidationService
{
    public function validateModuleConfig(array $config): array
    {
        $errors = [];
        $warnings = [];

        // Validate required config
        $required = ['table', 'dmenu', 'url', 'gmenu'];
        foreach ($required as $key) {
            if (empty($config[$key] ?? null)) {
                $errors[] = "Konfigurasi '{$key}' harus diisi";

                return [
                    'valid' => false,
                    'errors' => $errors,
                    'warnings' => $warnings,
                ];
            }
        }

        // Check if table exists
        if (! $this->checkTableExists($config['table'])) {
            $errors[] = "Tabel '{$config['table']}' tidak ditemukan di database";
        }

        // Check if dmenu already registered
        if ($this->checkMenuExists($config['dmenu'])) {
            $warnings[] = "Menu dengan dmenu '{$config['dmenu']}' sudah terdaftar";
        }

        // Check if model already exists
        $modelName = Str::studly(Str::singular($config['table']));
        if ($this->checkModelExists($modelName)) {
            $warnings[] = "Model '{$modelName}' sudah ada";
        }

        // Check if controller already exists
        $controllerName = Str::studly($config['url']).'Controller';
        if ($this->checkControllerExists($controllerName)) {
            $warnings[] = "Controller '{$controllerName}' sudah ada";
        }

        // Check if views already exist
        if ($this->checkViewsExist($config)) {
            $warnings[] = "Views untuk '{$config['url']}' sudah ada";
        }

        // Check if JS already exists
        if ($this->checkJsExists($config)) {
            $warnings[] = "JS file '{$config['dmenu']}.blade.php' sudah ada";
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

    public function checkViewsExist(array $config): bool
    {
        $viewsDir = resource_path("views/{$config['gmenu']}/{$config['url']}");

        return File::exists($viewsDir);
    }

    public function checkJsExists(array $config): bool
    {
        return File::exists(resource_path("views/js/{$config['dmenu']}.blade.php"));
    }
}
