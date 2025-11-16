<?php

namespace MSJFramework\LaravelGenerator\Templates\Helpers;

use Illuminate\Support\Facades\File;
use function app_path;

class ErrorHelper
{
    public static function getTemplate(): string
    {
        return <<<'PHP'
<?php

namespace App\Helpers\Koperasi;

class ErrorHelper
{
    public static function format(\Exception $exception): string
    {
        $message = $exception->getMessage();
        
        // Handle database errors
        if (str_contains($message, 'SQLSTATE')) {
            return self::formatDatabaseError($message);
        }
        
        // Handle validation errors
        if (str_contains($message, 'validation')) {
            return self::formatValidationError($message);
        }
        
        // Handle file errors
        if (str_contains($message, 'file') || str_contains($message, 'upload')) {
            return self::formatFileError($message);
        }
        
        // Default error message
        return self::formatGenericError($message);
    }
    
    private static function formatDatabaseError(string $message): string
    {
        if (str_contains($message, 'Duplicate entry')) {
            return 'Data sudah ada. Silakan gunakan data yang berbeda.';
        }
        
        if (str_contains($message, 'foreign key constraint')) {
            return 'Data tidak dapat dihapus karena masih digunakan di tempat lain.';
        }
        
        if (str_contains($message, 'Connection refused')) {
            return 'Koneksi database bermasalah. Silakan hubungi administrator.';
        }
        
        return 'Terjadi kesalahan pada database. Silakan coba lagi.';
    }
    
    private static function formatValidationError(string $message): string
    {
        return 'Data yang dimasukkan tidak valid. Silakan periksa kembali.';
    }
    
    private static function formatFileError(string $message): string
    {
        if (str_contains($message, 'size')) {
            return 'Ukuran file terlalu besar. Maksimal 5MB.';
        }
        
        if (str_contains($message, 'type') || str_contains($message, 'extension')) {
            return 'Format file tidak didukung. Gunakan format yang sesuai.';
        }
        
        return 'Terjadi kesalahan saat mengupload file. Silakan coba lagi.';
    }
    
    private static function formatGenericError(string $message): string
    {
        // Remove technical details for user-friendly message
        if (strlen($message) > 100) {
            return 'Terjadi kesalahan sistem. Silakan hubungi administrator jika masalah berlanjut.';
        }
        
        return $message;
    }
    
    public static function log(\Exception $exception): void
    {
        error_log(sprintf(
            "[%s] %s in %s:%d\nStack trace:\n%s",
            date('Y-m-d H:i:s'),
            $exception->getMessage(),
            $exception->getFile(),
            $exception->getLine(),
            $exception->getTraceAsString()
        ));
    }
}

PHP;
    }

    public static function createIfNotExists(): void
    {
        $helperPath = app_path('Helpers/Koperasi/ErrorHelper.php');

        if (! file_exists($helperPath)) {
            // Create Helpers/Koperasi directory if not exists
            $helperDir = dirname($helperPath);
            if (! is_dir($helperDir)) {
                mkdir($helperDir, 0755, true);
            }

            file_put_contents($helperPath, self::getTemplate());
        }
    }
}
