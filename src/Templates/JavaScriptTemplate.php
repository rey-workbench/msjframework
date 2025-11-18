<?php

namespace MSJFramework\LaravelGenerator\Templates;

class JavaScriptTemplate
{
    public static function generate(string $dmenu): string
    {
        return <<<'BLADE'
<x-js />

<script>
    // ============================================================================
    // GLOBAL CONSTANTS AND UTILITIES
    // ============================================================================

    // Add your global constants here
    // Example:
    // const MODULE_NAME = '{{ $module ?? "default" }}';

    // ============================================================================
    // UTILITY FUNCTIONS
    // ============================================================================

    function initializeGlobalUtilities() {
        // Add your global utility functions here
        console.log('Global utilities initialized');
    }

    // ============================================================================
    // MAIN INITIALIZATION
    // ============================================================================

    $(document).ready(function() {
        // Initialize global utilities
        initializeGlobalUtilities();

        // Initialize based on current URL
        const currentUrl = window.location.pathname;

        if (currentUrl.includes('/list') || (!currentUrl.includes('/add') && !currentUrl.includes('/edit') && !currentUrl.includes('/show'))) {
            // List page specific initialization
            console.log('List page initialized');
        } else if (currentUrl.includes('/add') || currentUrl.includes('/edit')) {
            // Form page specific initialization
            console.log('Form page initialized');
        } else if (currentUrl.includes('/show')) {
            // Show page specific initialization
            console.log('Show page initialized');
        }
    });
</script>

{{-- Include page-specific JavaScript based on current URL --}}
@php
    $currentUrl = request()->path();
@endphp

{{-- 
@if ($currentUrl == 'your-menu' || str_contains($currentUrl, '/list'))
    @include('js.module.list')
@elseif (str_contains($currentUrl, '/add'))
    @include('js.module.add')
@elseif (str_contains($currentUrl, '/edit'))
    @include('js.module.edit')
@elseif (str_contains($currentUrl, '/show'))
    @include('js.module.show')
@endif
--}}

BLADE;
    }
}
