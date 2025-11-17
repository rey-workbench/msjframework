<?php

namespace MSJFramework\LaravelGenerator\Services\Generation;

use MSJFramework\LaravelGenerator\Templates\Controllers\Base\MSJBaseControllerTemplate;
use MSJFramework\LaravelGenerator\Templates\Controllers\Layouts\Master\MasterControllerTemplate;
use MSJFramework\LaravelGenerator\Templates\Controllers\Layouts\Report\ReportControllerTemplate;
use MSJFramework\LaravelGenerator\Templates\Controllers\Layouts\Standr\StandrControllerTemplate;
use MSJFramework\LaravelGenerator\Templates\Controllers\Layouts\Sublnk\SublnkControllerTemplate;
use MSJFramework\LaravelGenerator\Templates\Controllers\Layouts\System\SystemControllerTemplate;
use MSJFramework\LaravelGenerator\Templates\Controllers\Layouts\Transc\TranscControllerTemplate;
use MSJFramework\LaravelGenerator\Templates\Controllers\Manual\ManualControllerTemplate;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

class ControllerGeneratorService
{
    public function generate(array $config): array
    {
        $controllerName = Str::studly($config['url']).'Controller';
        $controllerPath = app_path("Http/Controllers/{$controllerName}.php");

        $exists = File::exists($controllerPath);

        $content = $this->getControllerTemplate($config);
        File::put($controllerPath, $content);

        // Generate MSJBaseController if not exists
        $this->ensureBaseControllerExists();

        return [
            'status' => $exists ? 'updated' : 'created',
            'message' => $exists ? 'Controller diperbarui' : 'Controller dibuat',
            'path' => $controllerPath,
        ];
    }

    protected function getControllerTemplate(array $config): string
    {
        return match ($config['layout']) {
            'manual' => ManualControllerTemplate::getTemplate($config),
            'standr' => StandrControllerTemplate::getTemplate($config),
            'master' => MasterControllerTemplate::getTemplate($config),
            'transc' => TranscControllerTemplate::getTemplate($config),
            'system' => SystemControllerTemplate::getTemplate($config),
            'sublnk' => SublnkControllerTemplate::getTemplate($config),
            'report' => ReportControllerTemplate::getTemplate($config),
            default => ManualControllerTemplate::getTemplate($config),
        };
    }

    protected function ensureBaseControllerExists(): void
    {
        $baseControllerPath = app_path('Http/Controllers/MSJBaseController.php');

        if (! File::exists($baseControllerPath)) {
            $content = MSJBaseControllerTemplate::getTemplate();
            File::put($baseControllerPath, $content);
        }
    }
}
