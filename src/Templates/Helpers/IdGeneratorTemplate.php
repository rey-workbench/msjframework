<?php

namespace MSJFramework\LaravelGenerator\Templates\Helpers;

class IdGeneratorTemplate
{
    public static function getTemplate(): string
    {
        return <<<'PHP'
<?php

namespace App\Helpers\Koperasi\Generator;

use Illuminate\Support\Facades\DB;

class IdGenerator
{
    private const CONFIG = [
        // 'alias' => 'DMENU_CODE', // contoh: 'pinjaman' => 'KOP301'
    ];

    public static function generate(
        string $for,
        ?string $type = null,
        ?int $bulan = null,
        ?int $tahun = null
    ): string {
        if (! isset(self::CONFIG[$for])) {
            throw new \InvalidArgumentException("Unknown generator alias: {$for}");
        }

        $dmenu = self::CONFIG[$for];

        return match ($for) {
            'debitKredit' => self::withPrefix($dmenu, $type ?? 'debit'),
            'periode' => self::forPeriode($dmenu, $bulan, $tahun),
            default => self::standard($dmenu),
        };
    }

    private static function standard(string $dmenu): string
    {
        $config = self::getConfig($dmenu);
        $lookup = self::buildLookup($config);
        $counter = self::getCounter($lookup);
        $id = self::buildId($config, $counter);

        self::saveCounter($lookup, $counter, $id);

        return $id;
    }

    private static function withPrefix(string $dmenu, string $type): string
    {
        $prefix = strtolower($type) === 'debit' ? 'DEB' : 'KRE';
        $config = self::getConfig($dmenu);
        $lookup = $prefix.'-'.self::buildLookup($config);
        $counter = self::getCounter($lookup);
        $id = $prefix.self::buildId($config, $counter);

        self::saveCounter($lookup, $counter, $id);

        return $id;
    }

    private static function forPeriode(string $dmenu, ?int $bulan, ?int $tahun): string
    {
        if ($bulan === null || $tahun === null) {
            throw new \InvalidArgumentException('Periode generator membutuhkan bulan dan tahun.');
        }

        $configs = self::getConfig($dmenu);
        $id = '';

        foreach ($configs as $config) {
            $id .= match ($config->source) {
                'ext' => $config->external,
                'bln' => str_pad($bulan, 2, '0', STR_PAD_LEFT),
                'th4' => $tahun,
                'th2' => substr((string) $tahun, -2),
                default => '',
            };
        }

        return $id;
    }

    private static function getConfig(string $dmenu)
    {
        $config = DB::table('sys_id')
            ->where('dmenu', $dmenu)
            ->where('isactive', '1')
            ->orderBy('urut')
            ->get();

        if ($config->isEmpty()) {
            throw new \RuntimeException("Konfigurasi sys_id tidak ditemukan untuk {$dmenu}");
        }

        return $config;
    }

    private static function buildLookup($config): string
    {
        $lookup = '';
        foreach ($config as $c) {
            $lookup .= self::getValue($c, '-');
        }

        return $lookup;
    }

    private static function buildId($config, int $counter): string
    {
        $id = '';
        foreach ($config as $c) {
            $id .= self::getValue($c, $counter);
        }

        return $id;
    }

    private static function getCounter(string $lookup): int
    {
        $row = DB::table('sys_counter')->where('character', $lookup)->first();

        return $row ? $row->counter + 1 : 1;
    }

    private static function saveCounter(string $lookup, int $counter, string $id): void
    {
        DB::table('sys_counter')->updateOrInsert(
            ['character' => $lookup],
            [
                'counter' => $counter,
                'lastid' => $id,
                'isactive' => '1',
                'updated_at' => now(),
                'user_update' => session('username', 'system'),
            ]
        );
    }

    private static function getValue($config, $counter): string
    {
        return match ($config->source) {
            'int' => substr(request()->{$config->internal} ?? '', 0, $config->length),
            'ext' => substr($config->external, 0, $config->length),
            'th2' => date('y'),
            'th4' => date('Y'),
            'bln' => date('m'),
            'tgl' => date('d'),
            'cnt' => $counter === '-' ? '-' : str_pad($counter, $config->length, '0', STR_PAD_LEFT),
            default => '',
        };
    }
}

PHP;
    }

    public static function createIfNotExists(): void
    {
        $helperPath = app_path('Helpers/Koperasi/Generator/IdGenerator.php');

        if (! file_exists($helperPath)) {
            $helperDir = dirname($helperPath);
            if (! is_dir($helperDir)) {
                mkdir($helperDir, 0755, true);
            }

            file_put_contents($helperPath, self::getTemplate());
        }
    }
}
