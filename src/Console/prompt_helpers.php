<?php

use MSJFramework\LaravelGenerator\Support\CrossPlatformPrompt;

if (!function_exists('prompt_text')) {
    /**
     * Safe text prompt that works on all platforms.
     */
    function prompt_text(string $label, string $default = '', bool $required = false, $command = null): string
    {
        return CrossPlatformPrompt::text($label, $default, $required, $command);
    }
}

if (!function_exists('prompt_select')) {
    /**
     * Safe select prompt that works on all platforms.
     */
    function prompt_select(string $label, array $options, mixed $default = null, int $scroll = 10, $command = null): mixed
    {
        return CrossPlatformPrompt::select($label, $options, $default, $scroll, $command);
    }
}

if (!function_exists('prompt_confirm')) {
    /**
     * Safe confirm prompt that works on all platforms.
     */
    function prompt_confirm(string $label, bool $default = true, $command = null): bool
    {
        return CrossPlatformPrompt::confirm($label, $default, $command);
    }
}

if (!function_exists('prompt_search')) {
    /**
     * Safe search prompt that works on all platforms.
     */
    function prompt_search(string $label, callable $options, string $placeholder = '', $command = null): mixed
    {
        return CrossPlatformPrompt::search($label, $options, $placeholder, $command);
    }
}

if (!function_exists('prompt_multiselect')) {
    /**
     * Safe multiselect prompt that works on all platforms.
     */
    function prompt_multiselect(string $label, array $options, array $default = [], int $scroll = 10, $command = null): array
    {
        if (CrossPlatformPrompt::isWindowsNative() && $command) {
            $helper = $command->getHelper('question');
            $question = new \Symfony\Component\Console\Question\ChoiceQuestion(
                $label . ' (comma-separated) ',
                $options,
                implode(',', $default)
            );
            $question->setMultiselect(true);
            
            $result = $helper->ask($command->input, $command->output, $question);
            return is_array($result) ? $result : [];
        }

        // Use Laravel Prompts on Linux/macOS/WSL
        return \Laravel\Prompts\multiselect($label, $options, $default, $scroll);
    }
}

if (!function_exists('prompt_password')) {
    /**
     * Safe password prompt that works on all platforms.
     */
    function prompt_password(string $label, string $placeholder = '', $validate = null, $command = null): string
    {
        if (CrossPlatformPrompt::isWindowsNative() && $command) {
            $helper = $command->getHelper('question');
            $question = new \Symfony\Component\Console\Question\Question($label . ' ');
            $question->setHidden(true);
            $question->setHiddenFallback(false);
            
            if ($validate) {
                $question->setValidator($validate);
            }
            
            return $helper->ask($command->input, $command->output, $question);
        }

        // Use Laravel Prompts on Linux/macOS/WSL
        return \Laravel\Prompts\password($label, $placeholder, '', false, $validate);
    }
}
