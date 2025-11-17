<?php

namespace MSJFramework\LaravelGenerator\Services\Generation;

use MSJFramework\LaravelGenerator\Templates\Javascript\JsComponent;
use MSJFramework\LaravelGenerator\Templates\Javascript\JsTemplate;
use Illuminate\Support\Facades\File;

class JavascriptGeneratorService
{
    public function generate(array $config): array
    {
        $jsPath = resource_path("views/js/{$config['dmenu']}.blade.php");
        $exists = File::exists($jsPath);

        $content = JsTemplate::getTemplate($config);
        File::put($jsPath, $content);

        // Generate JS Component if needed
        $this->ensureJsComponentExists();

        return [
            'status' => $exists ? 'updated' : 'created',
            'message' => $exists ? 'JS diperbarui' : 'JS dibuat',
            'path' => $jsPath,
        ];
    }

    protected function ensureJsComponentExists(): void
    {
        $componentPath = resource_path('views/js/components.blade.php');

        if (! File::exists($componentPath)) {
            $content = JsComponent::getTemplate();
            File::put($componentPath, $content);
        }
    }
}
