<?php

use MSJFramework\LaravelGenerator\Console\Helpers\PromptHelper;

if (!function_exists('prompt_text')) {
    /**
     * Safe text prompt that works on all platforms.
     */
    function prompt_text(string $label, string $default = '', bool $required = false, $command = null): string
    {
        return PromptHelper::text($label, $default, $required, $command);
    }
}

if (!function_exists('prompt_select')) {
    /**
     * Safe select prompt that works on all platforms.
     */
    function prompt_select(string $label, array $options, mixed $default = null, int $scroll = 10, $command = null): mixed
    {
        return PromptHelper::select($label, $options, $default, $scroll, $command);
    }
}

if (!function_exists('prompt_confirm')) {
    /**
     * Safe confirm prompt that works on all platforms.
     */
    function prompt_confirm(string $label, bool $default = true, $command = null): bool
    {
        return PromptHelper::confirm($label, $default, $command);
    }
}

if (!function_exists('prompt_search')) {
    /**
     * Safe search prompt that works on all platforms.
     */
    function prompt_search(string $label, callable $options, string $placeholder = '', $command = null): mixed
    {
        return PromptHelper::search($label, $options, $placeholder, $command);
    }
}
