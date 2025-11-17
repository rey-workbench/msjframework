<?php

namespace MSJFramework\LaravelGenerator\Templates\Javascript;

use MSJFramework\LaravelGenerator\Templates\Components\FileComponent;
use MSJFramework\LaravelGenerator\Templates\Components\FormatComponent;
use MSJFramework\LaravelGenerator\Templates\Components\FormComponent;
use MSJFramework\LaravelGenerator\Templates\Components\InputComponent;
use MSJFramework\LaravelGenerator\Templates\Components\SwalComponent;
use Illuminate\Support\Facades\File;
use function resource_path;

class JsComponent
{
    public static function getTemplate(): string
    {
        return <<<'BLADE'
@include('components.swal')
@include('components.form')
@include('components.file')
@include('components.format')
@include('components.input')

<script>
    window.deleteData = function(event, name, msg, hasUser = false) {
        event.preventDefault();

        let message = `Apakah Anda Yakin ${msg} Data ${name} ini?`;
        let warningText = '';

        if (msg === 'Non Aktifkan' && hasUser) {
            warningText = '\n\n⚠️ PERHATIAN: Akses login anggota juga akan ikut dinonaktifkan!';
        } else if (msg === 'Aktifkan' && hasUser) {
            warningText = '\n\nℹ️ INFO: Akses login anggota juga akan ikut diaktifkan.';
        }

        window.Swal.confirm('Konfirmasi', message + warningText, `Ya, ${msg}`, 'Batal').then(result => {
            if (result.isConfirmed) {
                event.target.closest('form').submit();
            }
        });
    };

    window.showSuccessMessage = function(title, message, details = null) {
        return window.Swal.success(title, message);
    };

    window.showErrorMessage = function(title, message, details = null) {
        return window.Swal.error(title, message);
    };

    window.showWarningMessage = function(title, message, details = null) {
        return window.Swal.warning(title, message);
    };

    window.showImageModal = function(imageSrc, title) {
        return window.Swal.showImage(imageSrc, title);
    };
</script>
BLADE;
    }

    public static function createComponentIfNotExists(): void
    {
        $componentsDir = resource_path('views/components');

        // Create components directory if not exists
        if (! is_dir($componentsDir)) {
            mkdir($componentsDir, 0755, true);
        }

        // Create all required components
        self::createComponent('js.blade.php', self::getTemplate());
        self::createComponent('swal.blade.php', SwalComponent::getTemplate());
        self::createComponent('form.blade.php', FormComponent::getTemplate());
        self::createComponent('file.blade.php', FileComponent::getTemplate());
        self::createComponent('format.blade.php', FormatComponent::getTemplate());
        self::createComponent('input.blade.php', InputComponent::getTemplate());
    }

    private static function createComponent(string $filename, string $content): void
    {
        $componentPath = resource_path("views/components/{$filename}");

        if (! file_exists($componentPath)) {
            file_put_contents($componentPath, $content);
        }
    }
}
