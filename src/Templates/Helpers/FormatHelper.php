<?php

namespace MSJFramework\LaravelGenerator\Templates\Helpers;

use Illuminate\Support\Facades\File;
use function app_path;

class FormatHelper
{
    public static function getTemplate(): string
    {
        return <<<'PHP'
<?php

namespace App\Helpers;

class Format_Helper
{
    public function currency($amount): string
    {
        return 'Rp ' . number_format($amount, 0, ',', '.');
    }

    public function date($date, string $format = 'd/m/Y'): string
    {
        if (!$date) return '-';
        
        try {
            return date($format, strtotime($date));
        } catch (\Exception $e) {
            return $date;
        }
    }

    public function datetime($datetime, string $format = 'd/m/Y H:i'): string
    {
        if (!$datetime) return '-';
        
        try {
            return date($format, strtotime($datetime));
        } catch (\Exception $e) {
            return $datetime;
        }
    }

    public function number($number, int $decimals = 0): string
    {
        return number_format($number, $decimals, ',', '.');
    }

    public function percentage($value, int $decimals = 2): string
    {
        return number_format($value, $decimals, ',', '.') . '%';
    }

    public function truncate(string $text, int $length = 50): string
    {
        return strlen($text) > $length ? substr($text, 0, $length) . '...' : $text;
    }

    public function boolean($value): string
    {
        return $value ? 'Ya' : 'Tidak';
    }

    public function status($status): string
    {
        return $status == '1' ? 'Aktif' : 'Tidak Aktif';
    }
}

PHP;
    }

    public static function createIfNotExists(): void
    {
        $helperPath = app_path('Helpers/Format_Helper.php');

        if (! file_exists($helperPath)) {
            // Create Helpers directory if not exists
            $helperDir = dirname($helperPath);
            if (! is_dir($helperDir)) {
                mkdir($helperDir, 0755, true);
            }

            file_put_contents($helperPath, self::getTemplate());
        }
    }
}
