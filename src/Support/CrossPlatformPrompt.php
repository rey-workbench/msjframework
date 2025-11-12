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
            // Display beautiful styled prompt for Windows
            $command->newLine();
            
            // Header with icon and label
            $boxWidth = 64;
            $labelLen = mb_strlen($label);
            $padding = $boxWidth - $labelLen - 4;
            $command->getOutput()->writeln(" <fg=cyan>┌─</> <fg=bright-cyan>{$label}</> <fg=cyan>" . str_repeat('─', max(0, $padding)) . '┐</>');
            
            $helper = $command->getHelper('question');
            
            // Show default value hint if exists
            if ($default) {
                $command->getOutput()->writeln(" <fg=cyan>│</> <fg=gray>Default: {$default}</>");
            }
            
            // Show required indicator
            $requiredMark = $required ? '<fg=red>*</>' : '';
            $question = new Question(" <fg=cyan>│</> {$requiredMark} <fg=bright-white>", $default);
            
            if ($required) {
                $question->setValidator(function ($answer) use ($label) {
                    if (empty($answer)) {
                        throw new \RuntimeException("✗ {$label} is required.");
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
            
            $result = $helper->ask($inputProp->getValue($command), $outputProp->getValue($command), $question);
            
            // Display styled footer with success indicator
            $command->getOutput()->writeln(" <fg=cyan>└" . str_repeat('─', $boxWidth) . '┘</>');
            
            return $result;
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
            // Display Laravel Prompts-style select for Windows
            $command->newLine();
            
            // Label dengan styling
            $labelWidth = 120;
            $labelPadding = str_repeat('.', max(0, $labelWidth - mb_strlen($label) - 1));
            $command->getOutput()->writeln("  <fg=green>{$label}</> <fg=gray>{$labelPadding}</>");
            
            // Display options dengan nomor list
            $optionKeys = array_keys($options);
            
            foreach ($optionKeys as $index => $key) {
                $value = $options[$key];
                $command->getOutput()->writeln("  <fg=cyan>[{$index}]</> <fg=white>{$value}</>");
            }
            
            // Arrow prompt seperti Laravel
            $command->getOutput()->write("  <fg=cyan>❯</> ");
            
            $helper = $command->getHelper('question');
            
            // Simple input tanpa label tambahan
            $defaultDisplay = is_numeric($default) ? $default : array_search($default, $optionKeys);
            $question = new Question('', $defaultDisplay);
            
            // Validator
            $question->setValidator(function ($answer) use ($options) {
                if (!is_numeric($answer)) {
                    throw new \RuntimeException('Please enter a valid number.');
                }
                $index = (int)$answer;
                if ($index < 0 || $index >= count($options)) {
                    throw new \RuntimeException('Number out of range (0-' . (count($options) - 1) . ')');
                }
                return $answer;
            });
            
            // Use reflection to access protected properties
            $reflection = new \ReflectionClass($command);
            $inputProp = $reflection->getProperty('input');
            $outputProp = $reflection->getProperty('output');
            $inputProp->setAccessible(true);
            $outputProp->setAccessible(true);
            
            $selectedIndex = $helper->ask($inputProp->getValue($command), $outputProp->getValue($command), $question);
            
            // Get the actual value by index
            $optionKeys = array_keys($options);
            $result = $optionKeys[(int)$selectedIndex];
            
            return $result;
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
            // Display beautiful styled confirm for Windows
            $command->newLine();
            $defaultText = $default ? '<fg=green>Yes</> / <fg=gray>no</>' : '<fg=gray>yes</> / <fg=red>No</>';
            $icon = '<fg=cyan>?</>';
            
            $command->getOutput()->write(" {$icon} <fg=bright-white>{$label}</> <fg=gray>({$defaultText})</> <fg=cyan>›</> ");
            
            $helper = $command->getHelper('question');
            $question = new ConfirmationQuestion('', $default);
            
            // Use reflection to access protected properties
            $reflection = new \ReflectionClass($command);
            $inputProp = $reflection->getProperty('input');
            $outputProp = $reflection->getProperty('output');
            $inputProp->setAccessible(true);
            $outputProp->setAccessible(true);
            
            $result = $helper->ask($inputProp->getValue($command), $outputProp->getValue($command), $question);
            
            // Show result with icon
            $resultIcon = $result ? '<fg=green>✓</>' : '<fg=red>✗</>';
            $resultText = $result ? '<fg=green>Yes</>' : '<fg=red>No</>';
            $command->getOutput()->write("\r <fg=gray>│</> {$resultIcon} {$resultText}");
            $command->newLine();
            
            return $result;
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
            // Display Laravel Prompts-style multiselect for Windows
            $command->newLine();
            
            // Label dengan styling
            $labelWidth = 120;
            $labelPadding = str_repeat('.', max(0, $labelWidth - mb_strlen($label) - 1));
            $command->getOutput()->writeln("  <fg=green>{$label}</> <fg=gray>{$labelPadding}</>");
            $command->getOutput()->writeln("  <fg=gray>(comma-separated numbers)</>");
            
            // Display options dengan nomor list
            $optionKeys = array_keys($options);
            
            foreach ($optionKeys as $index => $key) {
                $value = $options[$key];
                $command->getOutput()->writeln("  <fg=cyan>[{$index}]</> <fg=white>{$value}</>");
            }
            
            // Arrow prompt seperti Laravel
            $command->getOutput()->write("  <fg=cyan>❯</> ");
            
            $helper = $command->getHelper('question');
            
            // Convert default to comma-separated numbers
            $defaultNumbers = [];
            foreach ($default as $def) {
                if (is_numeric($def)) {
                    $defaultNumbers[] = $def;
                } else {
                    $idx = array_search($def, $optionKeys);
                    if ($idx !== false) {
                        $defaultNumbers[] = $idx;
                    }
                }
            }
            $defaultDisplay = implode(',', $defaultNumbers);
            
            $question = new Question('', $defaultDisplay);
            
            // Validator
            $question->setValidator(function ($answer) use ($options) {
                if (empty($answer)) {
                    return '';
                }
                
                $numbers = array_map('trim', explode(',', $answer));
                foreach ($numbers as $num) {
                    if (!is_numeric($num)) {
                        throw new \RuntimeException('Invalid format. Use comma-separated numbers');
                    }
                    $index = (int)$num;
                    if ($index < 0 || $index >= count($options)) {
                        throw new \RuntimeException('Number out of range (0-' . (count($options) - 1) . ')');
                    }
                }
                return $answer;
            });
            
            // Use reflection to access protected properties
            $reflection = new \ReflectionClass($command);
            $inputProp = $reflection->getProperty('input');
            $outputProp = $reflection->getProperty('output');
            $inputProp->setAccessible(true);
            $outputProp->setAccessible(true);
            
            $selectedNumbers = $helper->ask($inputProp->getValue($command), $outputProp->getValue($command), $question);
            
            // Convert numbers back to keys
            $result = [];
            if (!empty($selectedNumbers)) {
                $numbers = array_map('trim', explode(',', $selectedNumbers));
                $optionKeys = array_keys($options);
                foreach ($numbers as $num) {
                    $index = (int)$num;
                    $result[] = $optionKeys[$index];
                }
            }
            
            return $result;
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
            // Display beautiful styled password for Windows
            $command->newLine();
            
            // Header with icon and label
            $boxWidth = 64;
            $labelLen = mb_strlen($label);
            $padding = $boxWidth - $labelLen - 4;
            $command->getOutput()->writeln(" <fg=cyan>┌─</> <fg=bright-cyan>{$label}</> <fg=cyan>" . str_repeat('─', max(0, $padding)) . '┐</>');
            
            if ($placeholder) {
                $command->getOutput()->writeln(" <fg=cyan>│</> <fg=gray>{$placeholder}</>");
            }
            
            $helper = $command->getHelper('question');
            $question = new Question(" <fg=cyan>│</> <fg=yellow>🔒</> <fg=bright-white>", '');
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
            
            $result = $helper->ask($inputProp->getValue($command), $outputProp->getValue($command), $question);
            
            // Display styled footer with masked preview
            $maskedLength = mb_strlen($result);
            $maskedPreview = $maskedLength > 0 ? str_repeat('•', min($maskedLength, 12)) : '<fg=gray>(empty)</>';
            $command->getOutput()->writeln(" <fg=cyan>│</> <fg=gray>{$maskedPreview}</>");
            $command->getOutput()->writeln(" <fg=cyan>└" . str_repeat('─', $boxWidth) . '┘</>');
            
            return $result;
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
