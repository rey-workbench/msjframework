<?php

namespace MSJFramework\LaravelGenerator\Templates\Javascript;

class JsTemplate
{
    public static function getTemplate(): string
    {
        return <<<'BLADE'
<x-js />

@push('js')
    <script>
        $(document).ready(function() {
            // Add your custom JavaScript here
            console.log('JS loaded for module');
        });
    </script>
@endpush
BLADE;
    }
}
