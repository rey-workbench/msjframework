<?php

namespace MSJFramework\LaravelGenerator\Templates\Helpers;

use Illuminate\Support\Facades\File;
use function app_path;

class FunctionHelper
{
    public static function getTemplate(): string
    {
        return <<<'PHP'
<?php

namespace App\Helpers;

use Illuminate\Support\Facades\DB;

class Function_Helper
{
    public function log_insert(string $type, string $dmenu, string $message, string $status = '1'): void
    {
        try {
            DB::table('sys_log')->insert([
                'type' => $type,
                'dmenu' => $dmenu,
                'message' => $message,
                'status' => $status,
                'username' => session('username', 'system'),
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        } catch (\Exception $e) {
            // Silent fail to prevent breaking the application
            error_log("Failed to insert log: " . $e->getMessage());
        }
    }

    public function generateCode(string $prefix, string $table, string $field, int $length = 3): string
    {
        try {
            $lastCode = DB::table($table)
                ->where($field, 'like', $prefix . '%')
                ->orderBy($field, 'desc')
                ->value($field);

            if (!$lastCode) {
                return $prefix . str_pad('1', $length, '0', STR_PAD_LEFT);
            }

            $number = (int) substr($lastCode, strlen($prefix));
            $newNumber = $number + 1;

            return $prefix . str_pad($newNumber, $length, '0', STR_PAD_LEFT);
        } catch (\Exception $e) {
            return $prefix . str_pad('1', $length, '0', STR_PAD_LEFT);
        }
    }

    public function encrypt($value): string
    {
        return encrypt($value);
    }

    public function decrypt($value): mixed
    {
        try {
            return decrypt($value);
        } catch (\Exception $e) {
            return null;
        }
    }

    public function sanitize(string $input): string
    {
        return htmlspecialchars(strip_tags(trim($input)), ENT_QUOTES, 'UTF-8');
    }

    public function validateNIK(string $nik): bool
    {
        return preg_match('/^\d{16}$/', $nik);
    }

    public function validateEmail(string $email): bool
    {
        return filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
    }

    public function validatePhone(string $phone): bool
    {
        $cleanPhone = preg_replace('/\D/', '', $phone);
        return preg_match('/^(\+62|62|0)[0-9]{9,13}$/', $cleanPhone);
    }
}

PHP;
    }

    public static function createIfNotExists(): void
    {
        $helperPath = app_path('Helpers/Function_Helper.php');

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
