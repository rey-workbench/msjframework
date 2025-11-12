<?php

namespace MSJFramework\LaravelGenerator\Support;

use Illuminate\Console\Command;
use Symfony\Component\Console\Question\ChoiceQuestion;
use Symfony\Component\Console\Question\ConfirmationQuestion;
use Symfony\Component\Console\Question\Question;

class CrossPlatformPrompt
{
    /**
     * Detect if running on Windows native (not WSL).
     */
    public static function isWindowsNative(): bool
    {
        // Check if running on Windows OS family
        if (PHP_OS_FAMILY !== 'Windows') {
            return false;
        }

        // Check if running in WSL (Windows Subsystem for Linux)
        if (getenv('WSL_DISTRO_NAME') !== false || getenv('WSL_INTEROP') !== false) {
            return false;
        }

        // Additional check: WSL typically has '/proc/version' containing 'microsoft'
        if (is_readable('/proc/version')) {
            $version = file_get_contents('/proc/version');
            if (stripos($version, 'microsoft') !== false || stripos($version, 'WSL') !== false) {
                return false;
            }
        }

        return true;
    }

    /**
     * Safe text prompt that works on all platforms.
     */
    public static function text(string $label, string $default = '', bool $required = false, ?Command $command = null): string
    {
        if (self::isWindowsNative() && $command) {
            $helper = $command->getHelper('question');
            $question = new Question($label . ' ', $default);
            
            if ($required) {
                $question->setValidator(function ($answer) {
                    if (empty($answer)) {
                        throw new \RuntimeException('This field is required.');
                    }
                    return $answer;
                });
            }
            
            // Use reflection to access protected properties
            $reflection = new \ReflectionClass($command);
            $inputProp = $reflection->getProperty('input');
            $outputProp = $reflection->getProperty('output');
            $inputProp->setAccessible(true);
            $outputProp->setAccessible(true);
            
            return $helper->ask($inputProp->getValue($command), $outputProp->getValue($command), $question);
        }

        // Use Laravel Prompts on Linux/macOS/WSL
        return \Laravel\Prompts\text($label, $default, '', $required);
    }

    /**
     * Safe select prompt that works on all platforms.
     */
    public static function select(string $label, array $options, mixed $default = null, int $scroll = 10, ?Command $command = null): mixed
    {
        if (self::isWindowsNative() && $command) {
            $helper = $command->getHelper('question');
            $question = new ChoiceQuestion($label . ' ', $options, $default);
            $question->setErrorMessage('Selection %s is invalid.');
            
            // Use reflection to access protected properties
            $reflection = new \ReflectionClass($command);
            $inputProp = $reflection->getProperty('input');
            $outputProp = $reflection->getProperty('output');
            $inputProp->setAccessible(true);
            $outputProp->setAccessible(true);
            
            return $helper->ask($inputProp->getValue($command), $outputProp->getValue($command), $question);
        }

        // Use Laravel Prompts on Linux/macOS/WSL
        return \Laravel\Prompts\select($label, $options, $default, $scroll);
    }

    /**
     * Safe confirm prompt that works on all platforms.
     */
    public static function confirm(string $label, bool $default = true, ?Command $command = null): bool
    {
        if (self::isWindowsNative() && $command) {
            $helper = $command->getHelper('question');
            $question = new ConfirmationQuestion($label . ' (yes/no) ', $default);
            
            // Use reflection to access protected properties
            $reflection = new \ReflectionClass($command);
            $inputProp = $reflection->getProperty('input');
            $outputProp = $reflection->getProperty('output');
            $inputProp->setAccessible(true);
            $outputProp->setAccessible(true);
            
            return $helper->ask($inputProp->getValue($command), $outputProp->getValue($command), $question);
        }

        // Use Laravel Prompts on Linux/macOS/WSL
        return \Laravel\Prompts\confirm($label, $default);
    }

    /**
     * Safe search prompt that works on all platforms.
     */
    public static function search(string $label, callable $options, string $placeholder = '', ?Command $command = null): mixed
    {
        if (self::isWindowsNative() && $command) {
            // Fallback to select with all options on Windows
            $allOptions = $options('');
            return self::select($label, $allOptions, null, 15, $command);
        }

        // Use Laravel Prompts on Linux/macOS/WSL
        return \Laravel\Prompts\search($label, $options, $placeholder);
    }

    /**
     * Safe multiselect prompt that works on all platforms.
     */
    public static function multiselect(string $label, array $options, array $default = [], int $scroll = 10, ?Command $command = null): array
    {
        if (self::isWindowsNative() && $command) {
            $helper = $command->getHelper('question');
            $question = new ChoiceQuestion(
                $label . ' (comma-separated) ',
                $options,
                implode(',', $default)
            );
            $question->setMultiselect(true);
            
            // Use reflection to access protected properties
            $reflection = new \ReflectionClass($command);
            $inputProp = $reflection->getProperty('input');
            $outputProp = $reflection->getProperty('output');
            $inputProp->setAccessible(true);
            $outputProp->setAccessible(true);
            
            $result = $helper->ask($inputProp->getValue($command), $outputProp->getValue($command), $question);
            return is_array($result) ? $result : [];
        }

        // Use Laravel Prompts on Linux/macOS/WSL
        return \Laravel\Prompts\multiselect($label, $options, $default, $scroll);
    }

    /**
     * Safe password prompt that works on all platforms.
     */
    public static function password(string $label, string $placeholder = '', $validate = null, ?Command $command = null): string
    {
        if (self::isWindowsNative() && $command) {
            $helper = $command->getHelper('question');
            $question = new Question($label . ' ');
            $question->setHidden(true);
            $question->setHiddenFallback(false);
            
            if ($validate) {
                $question->setValidator($validate);
            }
            
            // Use reflection to access protected properties
            $reflection = new \ReflectionClass($command);
            $inputProp = $reflection->getProperty('input');
            $outputProp = $reflection->getProperty('output');
            $inputProp->setAccessible(true);
            $outputProp->setAccessible(true);
            
            return $helper->ask($inputProp->getValue($command), $outputProp->getValue($command), $question);
        }

        // Use Laravel Prompts on Linux/macOS/WSL
        return \Laravel\Prompts\password($label, $placeholder, '', false, $validate);
    }

    /**
     * Strip style tags from string for Windows output.
     */
    public static function stripStyleTags(string $text): string
    {
        if (!self::isWindowsNative()) {
            return $text;
        }

        // Remove all style tags like <fg=color>, <bg=color>, <options=bold>, etc.
        return preg_replace('/<[^>]+>/', '', $text);
    }

    /**
     * Safe line output that strips style tags on Windows.
     */
    public static function line(Command $command, string $text): void
    {
        if (self::isWindowsNative()) {
            $command->line(self::stripStyleTags($text));
        } else {
            $command->line($text);
        }
    }
}
