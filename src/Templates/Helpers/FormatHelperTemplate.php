<?php

namespace MSJFramework\LaravelGenerator\Templates\Helpers;

use Illuminate\Support\Facades\File;
use function app_path;

class FormatHelperTemplate
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

    public function IDFormat($dmenu)
    {
        $generate_id = '';
        $counter = 1;
        $zero = '';
        $string = '';
        $sys_id = DB::table('sys_id')->where(['dmenu' => $dmenu, 'isactive' => '1'])->orderBy('urut', 'ASC')->get();
        $sys_str = $sys_id;
        foreach ($sys_str as $str) {
            if ($str->source == 'int') {
                $string = $string.substr(request()->{$str->internal}, 0, $str->length);
            } elseif ($str->source == 'ext') {
                $string = $string.substr($str->external, 0, $str->length);
            } elseif ($str->source == 'th2') {
                $string = $string.date_format(now(), 'y');
            } elseif ($str->source == 'th4') {
                $string = $string.date_format(now(), 'Y');
            } elseif ($str->source == 'bln') {
                $string = $string.date_format(now(), 'm');
            } elseif ($str->source == 'tgl') {
                $string = $string.date_format(now(), 'd');
            } elseif ($str->source == 'cnt') {
                $string = $string.'-';
            }
        }
        $sys_counter = DB::table('sys_counter')->where('character', $string)->first();
        if ($sys_counter) {
            $counter = $sys_counter->counter + 1;
        }
        foreach ($sys_id as $id) {
            if ($id->source == 'int') {
                $generate_id = $generate_id.substr(request()->{$id->internal}, 0, $id->length);
            } elseif ($id->source == 'ext') {
                $generate_id = $generate_id.substr($id->external, 0, $id->length);
            } elseif ($id->source == 'th2') {
                $generate_id = $generate_id.date_format(now(), 'y');
            } elseif ($id->source == 'th4') {
                $generate_id = $generate_id.date_format(now(), 'Y');
            } elseif ($id->source == 'bln') {
                $generate_id = $generate_id.date_format(now(), 'm');
            } elseif ($id->source == 'tgl') {
                $generate_id = $generate_id.date_format(now(), 'd');
            } elseif ($id->source == 'cnt') {
                for ($i = 0; $i < $id->length - strlen((string) $counter); $i++) {
                    $zero = $zero.'0';
                }
                $generate_id = $generate_id.$zero.$counter;
            }
        }
        if ($sys_counter) {
            $data = [
                'counter' => $counter,
                'lastid' => $generate_id,
            ];
            $upd_sys_counter = DB::table('sys_counter')->where('character', $string)->update($data);
            if ($upd_sys_counter) {
                return $generate_id;
            }
        } else {
            $data = [
                'character' => $string,
                'counter' => $counter,
                'lastid' => $generate_id,
            ];
            $ins_sys_counter = DB::table('sys_counter')->insert($data);
            if ($ins_sys_counter) {
                return $generate_id;
            }
        }
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
