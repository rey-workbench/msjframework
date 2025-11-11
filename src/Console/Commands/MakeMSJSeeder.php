<?php

namespace MSJFramework\LaravelGenerator\Console\Commands;

use MSJFramework\LaravelGenerator\Console\Commands\Concerns\HasConsoleStyling;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

use function Laravel\Prompts\confirm;
use function Laravel\Prompts\select;

class MakeMSJSeeder extends Command
{
    use HasConsoleStyling;

    protected $signature = 'msj:make:seeder {type?} {--prefix=} {--gmenu=} {--dmenu=} {--auto}';
    protected $description = 'Generate smart seeders based on menu context';

    public function handle(): int
    {
        $type = $this->argument('type');
        
        if (!$type) {
            $type = select(
                label: 'Pilih tipe seeder',
                options: [
                    'menu' => 'Menu Seeder (gmenu + dmenu)',
                    'table' => 'Table Config Seeder',
                    'auth' => 'Auth Seeder (roles + permissions)',
                    'complete' => 'Complete Seeder (semua)',
                ],
                default: 'menu'
            );
        }

        $prefix = $this->determinePrefix();

        return match ($type) {
            'menu' => $this->generateMenuSeeder($prefix),
            'table' => $this->generateTableSeeder($prefix),
            'auth' => $this->generateAuthSeeder($prefix),
            'complete' => $this->generateCompleteSeeder($prefix),
            default => $this->error("Unknown seeder type: {$type}")
        };
    }

    protected function determinePrefix(): string
    {
        // Priority: --prefix option > --gmenu/--dmenu > smart detection
        if ($this->option('prefix')) {
            return $this->option('prefix');
        }

        $gmenu = $this->option('gmenu');
        $dmenu = $this->option('dmenu');

        if ($dmenu) {
            return $this->generateSmartPrefix($dmenu);
        }

        if ($gmenu) {
            return $this->generateSmartPrefix($gmenu);
        }

        // Auto-detect from recent activity or ask user
        return $this->autoDetectPrefix();
    }

    protected function generateSmartPrefix(string $code): string
    {
        // Clean up and convert to StudlyCase
        $prefix = Str::studly(str_replace(['_', '-'], '', $code));
        
        // Remove common prefixes
        $prefix = preg_replace('/^(Kop|Msj|Sys)/', '', $prefix);
        $prefix = preg_replace('/\d+$/', '', $prefix); // Remove trailing numbers
        
        // Ensure it's not empty
        if (empty($prefix) || strlen($prefix) < 2) {
            $prefix = 'MSJ';
        }
        
        return ucfirst($prefix);
    }

    protected function autoDetectPrefix(): string
    {
        // Try to detect from recent menu activity
        $recentDmenu = DB::table('sys_dmenu')
            ->where('isactive', '1')
            ->orderBy('created_at', 'desc')
            ->first();

        if ($recentDmenu) {
            $this->info("Auto-detected dari menu terbaru: {$recentDmenu->dmenu}");
            return $this->generateSmartPrefix($recentDmenu->dmenu);
        }

        return 'MSJ';
    }

    protected function generateMenuSeeder(string $prefix): int
    {
        $this->displayHeader("Generate {$prefix} Menu Seeder");

        $gmenus = DB::table('sys_gmenu')->where('isactive', '1')->orderBy('urut')->get();
        $dmenus = DB::table('sys_dmenu')->where('isactive', '1')->orderBy('urut')->get();

        if ($gmenus->isEmpty() && $dmenus->isEmpty()) {
            $this->badge('warning', 'Tidak ada data menu untuk di-export');
            return Command::SUCCESS;
        }

        $seederName = "{$prefix}MenuSeeder";
        $path = database_path("seeders/{$seederName}.php");

        $content = $this->generateMenuSeederContent($seederName, $gmenus, $dmenus);

        if (!$this->checkFileOverwrite($path, "{$seederName}.php")) {
            return Command::SUCCESS;
        }

        File::put($path, $content);
        $this->badge('success', "{$seederName}.php berhasil dibuat dengan {$gmenus->count()} group menus dan {$dmenus->count()} detail menus");

        return Command::SUCCESS;
    }

    protected function generateTableSeeder(string $prefix): int
    {
        $this->displayHeader("Generate {$prefix} Table Seeder");

        $tables = DB::table('sys_table')
            ->where('isactive', '1')
            ->orderBy('gmenu')
            ->orderBy('dmenu')
            ->orderBy('urut')
            ->get();

        if ($tables->isEmpty()) {
            $this->badge('warning', 'Tidak ada konfigurasi tabel untuk di-export');
            return Command::SUCCESS;
        }

        $seederName = "{$prefix}TableSeeder";
        $path = database_path("seeders/{$seederName}.php");

        $content = $this->generateTableSeederContent($seederName, $tables);

        if (!$this->checkFileOverwrite($path, "{$seederName}.php")) {
            return Command::SUCCESS;
        }

        File::put($path, $content);
        $this->badge('success', "{$seederName}.php berhasil dibuat dengan {$tables->count()} table configurations");

        return Command::SUCCESS;
    }

    protected function generateAuthSeeder(string $prefix): int
    {
        $this->displayHeader("Generate {$prefix} Auth Seeder");

        $roles = DB::table('sys_roles')->orderBy('id')->get();
        $auths = DB::table('sys_auth')->orderBy('dmenu')->get();

        if ($roles->isEmpty() && $auths->isEmpty()) {
            $this->badge('warning', 'Tidak ada data auth untuk di-export');
            return Command::SUCCESS;
        }

        $seederName = "{$prefix}AuthSeeder";
        $path = database_path("seeders/{$seederName}.php");

        $content = $this->generateAuthSeederContent($seederName, $roles, $auths);

        if (!$this->checkFileOverwrite($path, "{$seederName}.php")) {
            return Command::SUCCESS;
        }

        File::put($path, $content);
        $this->badge('success', "{$seederName}.php berhasil dibuat dengan {$roles->count()} roles dan {$auths->count()} permissions");

        return Command::SUCCESS;
    }

    protected function generateCompleteSeeder(string $prefix): int
    {
        $this->displayHeader("Generate {$prefix} Complete Seeder");

        // Generate all seeders
        $this->call('msj:make:seeder', ['type' => 'menu', '--prefix' => $prefix, '--auto' => true]);
        $this->call('msj:make:seeder', ['type' => 'table', '--prefix' => $prefix, '--auto' => true]);
        $this->call('msj:make:seeder', ['type' => 'auth', '--prefix' => $prefix, '--auto' => true]);

        // Generate master seeder that calls all
        $seederName = "{$prefix}CompleteSeeder";
        $path = database_path("seeders/{$seederName}.php");

        $content = $this->generateCompleteSeederContent($seederName, $prefix);

        if (!$this->checkFileOverwrite($path, "{$seederName}.php")) {
            return Command::SUCCESS;
        }

        File::put($path, $content);
        $this->badge('success', "{$seederName}.php berhasil dibuat sebagai master seeder");

        return Command::SUCCESS;
    }

    protected function checkFileOverwrite(string $path, string $filename): bool
    {
        if (File::exists($path)) {
            if ($this->option('auto')) {
                $this->warn("File {$filename} sudah ada, akan ditimpa...");
                return true;
            } else {
                return confirm("File {$filename} sudah ada. Timpa?");
            }
        }
        return true;
    }

    protected function generateMenuSeederContent(string $seederName, $gmenus, $dmenus): string
    {
        $gmenuData = $gmenus->map(function ($gmenu) {
            return [
                'gmenu' => $gmenu->gmenu,
                'name' => $gmenu->name,
                'urut' => $gmenu->urut,
                'isactive' => $gmenu->isactive,
                'created_at' => now(),
                'updated_at' => now(),
            ];
        })->toArray();

        $dmenuData = $dmenus->map(function ($dmenu) {
            return [
                'dmenu' => $dmenu->dmenu,
                'gmenu' => $dmenu->gmenu,
                'name' => $dmenu->name,
                'urut' => $dmenu->urut,
                'isactive' => $dmenu->isactive,
                'created_at' => now(),
                'updated_at' => now(),
            ];
        })->toArray();

        $gmenuCodes = $gmenus->pluck('gmenu')->toArray();
        $dmenuCodes = $dmenus->pluck('dmenu')->toArray();

        return "<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class {$seederName} extends Seeder
{
    public function run(): void
    {
        // Delete existing data first
        \$dmenuCodes = " . var_export($dmenuCodes, true) . ";
        \$gmenuCodes = " . var_export($gmenuCodes, true) . ";

        DB::table('sys_auth')->whereIn('dmenu', \$dmenuCodes)->delete();
        DB::table('sys_table')->whereIn('dmenu', \$dmenuCodes)->delete();
        DB::table('sys_dmenu')->whereIn('dmenu', \$dmenuCodes)->delete();
        DB::table('sys_gmenu')->whereIn('gmenu', \$gmenuCodes)->delete();

        // Insert Group Menus
        \$gmenus = " . var_export($gmenuData, true) . ";
        
        DB::table('sys_gmenu')->insert(\$gmenus);

        // Insert Detail Menus
        \$dmenus = " . var_export($dmenuData, true) . ";
        
        DB::table('sys_dmenu')->insert(\$dmenus);
    }
}";
    }

    protected function generateTableSeederContent(string $seederName, $tables): string
    {
        $tablesByDmenu = $tables->groupBy('dmenu');
        $dmenuCodes = $tables->pluck('dmenu')->unique()->toArray();

        $content = "<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class {$seederName} extends Seeder
{
    public function run(): void
    {
        // Delete existing data
        \$dmenuCodes = " . var_export($dmenuCodes, true) . ";
        
        foreach (\$dmenuCodes as \$dmenu) {
            DB::table('sys_table')->where('dmenu', \$dmenu)->delete();
        }

        // Insert table configurations
";

        foreach ($tablesByDmenu as $dmenu => $configs) {
            $content .= "        // {$dmenu} configurations\n";
            $configData = $configs->map(function ($config) {
                return [
                    'gmenu' => $config->gmenu,
                    'dmenu' => $config->dmenu,
                    'field' => $config->field,
                    'alias' => $config->alias,
                    'type' => $config->type,
                    'length' => $config->length ?? null,
                    'validate' => $config->validate ?? '',
                    'primary' => $config->primary ?? '0',
                    'position' => $config->position ?? '0',
                    'urut' => $config->urut,
                    'list' => $config->list ?? '1',
                    'show' => $config->show ?? '1',
                    'filter' => $config->filter ?? '1',
                    'isactive' => $config->isactive ?? '1',
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
            })->toArray();
            
            $content .= "        DB::table('sys_table')->insert(" . var_export($configData, true) . ");\n\n";
        }

        $content .= "    }\n}";

        return $content;
    }

    protected function generateAuthSeederContent(string $seederName, $roles, $auths): string
    {
        $roleData = $roles->map(function ($role) {
            return [
                'id' => $role->id,
                'name' => $role->name,
                'description' => $role->description,
                'created_at' => now(),
                'updated_at' => now(),
            ];
        })->toArray();

        $authData = $auths->map(function ($auth) {
            return [
                'dmenu' => $auth->dmenu,
                'idroles' => $auth->idroles,
                'c' => $auth->c,
                'r' => $auth->r,
                'u' => $auth->u,
                'd' => $auth->d,
                'created_at' => now(),
                'updated_at' => now(),
            ];
        })->toArray();

        return "<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class {$seederName} extends Seeder
{
    public function run(): void
    {
        // Delete existing data
        DB::table('sys_auth')->truncate();
        DB::table('sys_roles')->truncate();

        // Insert Roles
        \$roles = " . var_export($roleData, true) . ";
        
        DB::table('sys_roles')->insert(\$roles);

        // Insert Auth Permissions
        \$auths = " . var_export($authData, true) . ";
        
        DB::table('sys_auth')->insert(\$auths);
    }
}";
    }

    protected function generateCompleteSeederContent(string $seederName, string $prefix): string
    {
        return "<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class {$seederName} extends Seeder
{
    public function run(): void
    {
        \$this->call([
            {$prefix}MenuSeeder::class,
            {$prefix}TableSeeder::class,
            {$prefix}AuthSeeder::class,
        ]);
    }
}";
    }
}
