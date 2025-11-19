<?php

namespace MSJFramework\Services;

/**
 * Cross-platform detection service
 */
class PlatformDetectorService
{
    protected bool $isWindows;
    protected bool $isWSL;

    public function __construct()
    {
        $this->isWindows = PHP_OS_FAMILY === 'Windows';
        $this->isWSL = $this->detectWSL();
    }

    /**
     * Detect if running on WSL
     */
    protected function detectWSL(): bool
    {
        if (PHP_OS_FAMILY !== 'Linux') {
            return false;
        }

        // Check WSL environment variables
        if (getenv('WSL_DISTRO_NAME') !== false || getenv('WSL_INTEROP') !== false) {
            return true;
        }

        // Check /proc/version for Microsoft/WSL
        if (file_exists('/proc/version')) {
            $version = file_get_contents('/proc/version');
            return stripos($version, 'microsoft') !== false || stripos($version, 'WSL') !== false;
        }

        return false;
    }

    /**
     * Check if running on Windows native (not WSL)
     */
    public function isWindowsNonWSL(): bool
    {
        return $this->isWindows && !$this->isWSL;
    }

    /**
     * Get environment information
     */
    public function getEnvironmentInfo(): array
    {
        if ($this->isWSL) {
            return ['type' => 'WSL', 'icon' => '🐧'];
        }
        
        if ($this->isWindows) {
            return ['type' => 'Windows Native', 'icon' => '🪟'];
        }
        
        if (PHP_OS_FAMILY === 'Darwin') {
            return ['type' => 'macOS', 'icon' => '🍎'];
        }
        
        return ['type' => 'Linux', 'icon' => '🐧'];
    }
}
