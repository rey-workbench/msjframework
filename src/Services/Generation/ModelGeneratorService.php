<?php

namespace MSJFramework\LaravelGenerator\Services\Generation;

use MSJFramework\LaravelGenerator\Templates\Models\ModelTemplate;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

class ModelGeneratorService
{
    public function generate(array $config): array
    {
        $modelName = Str::studly(Str::singular($config['table']));
        $modelPath = app_path("Models/{$modelName}.php");

        $exists = File::exists($modelPath);

        // Always regenerate to ensure fillable is up to date
        $content = ModelTemplate::getTemplate($config);
        File::put($modelPath, $content);

        return [
            'status' => $exists ? 'updated' : 'created',
            'message' => $exists ? 'Model diperbarui' : 'Model dibuat',
            'path' => $modelPath,
        ];
    }
}
