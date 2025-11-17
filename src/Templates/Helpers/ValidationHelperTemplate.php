<?php

namespace MSJFramework\LaravelGenerator\Templates\Helpers;

use Illuminate\Support\Facades\File;
use function app_path;

class ValidationHelperTemplate
{
    public static function getTemplate(): string
    {
        return <<<'PHP'
<?php

namespace App\Helpers\Koperasi;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class ValidationHelper
{
    public static function validate(string $dmenu, ?string $id = null): array
    {
        try {
            // Get validation rules from sys_table
            $fields = DB::table('sys_table')
                ->where('dmenu', $dmenu)
                ->where('show', '1')
                ->where('isactive', '1')
                ->get();

            if ($fields->isEmpty()) {
                return [
                    'success' => false,
                    'errors' => collect(['Konfigurasi validasi tidak ditemukan']),
                    'data' => []
                ];
            }

            $rules = [];
            $messages = [];
            $data = request()->all();

            foreach ($fields as $field) {
                if (empty($field->validate) || $field->primary == '1') {
                    continue;
                }

                $fieldRules = explode('|', $field->validate);
                $processedRules = [];

                foreach ($fieldRules as $rule) {
                    // Handle unique rule for updates
                    if (str_starts_with($rule, 'unique:') && $id) {
                        $rule .= ',' . $id;
                    }
                    $processedRules[] = $rule;
                }

                $rules[$field->field] = $processedRules;
                
                // Custom messages
                $messages[$field->field . '.required'] = $field->alias . ' harus diisi';
                $messages[$field->field . '.email'] = $field->alias . ' harus berupa email yang valid';
                $messages[$field->field . '.unique'] = $field->alias . ' sudah digunakan';
                $messages[$field->field . '.min'] = $field->alias . ' minimal :min karakter';
                $messages[$field->field . '.max'] = $field->alias . ' maksimal :max karakter';
                $messages[$field->field . '.numeric'] = $field->alias . ' harus berupa angka';
                $messages[$field->field . '.date'] = $field->alias . ' harus berupa tanggal yang valid';
            }

            $validator = Validator::make($data, $rules, $messages);

            if ($validator->fails()) {
                return [
                    'success' => false,
                    'errors' => $validator->errors(),
                    'data' => $data
                ];
            }

            // Filter only validated data
            $validatedData = [];
            foreach ($fields as $field) {
                if (isset($data[$field->field])) {
                    $validatedData[$field->field] = $data[$field->field];
                }
            }

            return [
                'success' => true,
                'errors' => collect(),
                'data' => $validatedData
            ];

        } catch (\Exception $e) {
            return [
                'success' => false,
                'errors' => collect(['Terjadi kesalahan validasi: ' . $e->getMessage()]),
                'data' => request()->all()
            ];
        }
    }

    public static function validateField(string $field, $value, string $rules): array
    {
        $validator = Validator::make(
            [$field => $value],
            [$field => $rules]
        );

        return [
            'success' => !$validator->fails(),
            'errors' => $validator->errors(),
            'messages' => $validator->errors()->get($field)
        ];
    }

    public static function sanitizeInput(array $data): array
    {
        $sanitized = [];
        
        foreach ($data as $key => $value) {
            if (is_string($value)) {
                $sanitized[$key] = htmlspecialchars(strip_tags(trim($value)), ENT_QUOTES, 'UTF-8');
            } elseif (is_array($value)) {
                $sanitized[$key] = self::sanitizeInput($value);
            } else {
                $sanitized[$key] = $value;
            }
        }
        
        return $sanitized;
    }
}

PHP;
    }

    public static function createIfNotExists(): void
    {
        $helperPath = app_path('Helpers/Koperasi/ValidationHelper.php');

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
