<?php

namespace MSJFramework\LaravelGenerator\Console\Commands\Concerns;

use Illuminate\Support\Facades\File;
// Import safe prompt helpers that work on all platforms

trait HasFileOperations
{
    /**
     * Check file overwrite with confirmation
     */
    protected function checkFileOverwrite(string $path, string $filename): bool
    {
        if (File::exists($path)) {
            if ($this->option('auto')) {
                $this->warn("File {$filename} sudah ada, akan ditimpa...");
                return true;
            } else {
                return prompt_confirm("File {$filename} sudah ada. Timpa?", command: $this);
            }
        }
        return true;
    }

    /**
     * Ensure directory exists
     */
    protected function ensureDirectoryExists(string $path): void
    {
        if (!File::exists($path)) {
            File::makeDirectory($path, 0755, true);
        }
    }

    /**
     * Write file with directory creation
     */
    protected function writeFile(string $path, string $content): bool
    {
        $directory = dirname($path);
        $this->ensureDirectoryExists($directory);

        return File::put($path, $content) !== false;
    }

    /**
     * Get database path
     */
    protected function getDatabasePath(string $path = ''): string
    {
        return database_path($path);
    }

    /**
     * Get app path
     */
    protected function getAppPath(string $path = ''): string
    {
        return app_path($path);
    }

    /**
     * Get resource path
     */
    protected function getResourcePath(string $path = ''): string
    {
        return resource_path($path);
    }

    /**
     * Convert array to PHP code string
     */
    protected function arrayToPhpCode(array $array, int $indent = 0): string
    {
        $spaces = str_repeat('    ', $indent);
        $result = "[\n";
        
        foreach ($array as $item) {
            $result .= $spaces . "    [\n";
            foreach ($item as $key => $value) {
                if ($value === 'now()') {
                    $result .= $spaces . "        '{$key}' => now(),\n";
                } elseif (is_string($value) && strpos($value, "'") === 0) {
                    // Already quoted string (like password hash)
                    $result .= $spaces . "        '{$key}' => {$value},\n";
                } else {
                    $result .= $spaces . "        '{$key}' => " . var_export($value, true) . ",\n";
                }
            }
            $result .= $spaces . "    ],\n";
        }
        
        $result .= $spaces . "]";
        return $result;
    }
}
