<?php

namespace MSJFramework\Services;

use Illuminate\Support\Facades\Artisan;
use Symfony\Component\Process\Process;

class PublishService
{
    protected PlatformDetectorService $platform;

    public function __construct()
    {
        $this->platform = new PlatformDetectorService();
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
        if ($this->platform->isWindowsNonWSL()) {
            return $this->executeViaSymfonyProcess('vendor:publish', $params);
        }

        // WSL, Linux, Mac: use Artisan directly
        Artisan::call('vendor:publish', $params);
        return true;
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
        $envInfo = $this->platform->getEnvironmentInfo();
        
        // Add executor information
        if ($this->platform->isWindowsNonWSL()) {
            $envInfo['executor'] = 'Symfony Process';
        } else {
            $envInfo['executor'] = 'Laravel Artisan';
        }
        
        return $envInfo;
    }
}
