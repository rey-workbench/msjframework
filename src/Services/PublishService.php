<?php

namespace MSJFramework\Services;

use Illuminate\Support\Facades\Artisan;
use Symfony\Component\Process\Process;

class PublishService
{
    protected bool $isWindows;
    protected bool $isWSL;

    public function __construct()
    {
        $this->isWindows = PHP_OS_FAMILY === 'Windows';
        $this->isWSL = $this->detectWSL();
    }

    /**
     * Execute vendor:publish command
     */
    public function executePublish(string $tag, array $options = []): bool
    {
        $params = array_merge([
            '--tag' => $tag,
            '--provider' => 'MSJFramework\MSJServiceProvider',
        ], $options);

        // Windows native: use Symfony Process
        if ($this->isWindows && !$this->isWSL) {
            return $this->executeViaSymfonyProcess('vendor:publish', $params);
        }

        // WSL, Linux, Mac: use Artisan directly
        Artisan::call('vendor:publish', $params);
        return true;
    }

    /**
     * Detect if running on WSL
     */
    protected function detectWSL(): bool
    {
        if (PHP_OS_FAMILY !== 'Linux') {
            return false;
        }

        if (file_exists('/proc/version')) {
            $version = file_get_contents('/proc/version');
            return stripos($version, 'microsoft') !== false || stripos($version, 'WSL') !== false;
        }

        return false;
    }

    /**
     * Execute command via Symfony Process (for Windows)
     */
    protected function executeViaSymfonyProcess(string $command, array $params = []): bool
    {
        try {
            $args = [PHP_BINARY, 'artisan', $command];
            
            foreach ($params as $key => $value) {
                if (is_bool($value) && $value) {
                    $args[] = $key;
                } elseif (!is_bool($value)) {
                    $args[] = $key . '=' . $value;
                }
            }

            $process = new Process($args, base_path(), null, null, 300);
            $process->run();

            return $process->isSuccessful();
        } catch (\Exception $e) {
            return false;
        }
    }

    /**
     * Get environment info
     */
    public function getEnvironmentInfo(): array
    {
        if ($this->isWSL) {
            return [
                'type' => 'WSL',
                'icon' => '🐧',
                'executor' => 'Laravel Artisan',
            ];
        } elseif ($this->isWindows) {
            return [
                'type' => 'Windows Native',
                'icon' => '🪟',
                'executor' => 'Symfony Process',
            ];
        } else {
            return [
                'type' => 'Linux/Mac',
                'icon' => '🐧',
                'executor' => 'Laravel Artisan',
            ];
        }
    }
}
