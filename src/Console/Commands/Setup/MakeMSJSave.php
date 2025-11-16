<?php

namespace MSJFramework\LaravelGenerator\Console\Commands\Setup;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
// Import safe prompt helpers that work on all platforms

class MakeMSJSave extends Command
{
    protected $signature = 'msj:make:save {type?} {--auto : Auto confirm file overwrite} {--prefix= : Custom prefix for seeder names}';
    protected $description = 'Generate MSJ data seeders (roles, auth, users, menus, tables)';

    public function handle()
    {
        $type = $this->argument('type');

        if (!$type) {
            $type = prompt_select(
                'Pilih tipe data yang ingin disimpan:',
                [
                    'roles' => 'Roles (sys_roles)',
                    'auth' => 'Authorization (sys_auth)',
                    'users' => 'Users (users)',
                    'menus' => 'Menus (sys_gmenu & sys_dmenu)',
                    'tables' => 'Table Config (sys_table)',
                    'complete' => 'Complete Setup (semua data)',
                ],
                command: $this
            );
        }

        switch ($type) {
            case 'roles':
                $this->saveRoles();
                break;
            case 'auth':
                $this->saveAuth();
                break;
            case 'users':
                $this->saveUsers();
                break;
            case 'menus':
                $this->saveMenus();
                break;
            case 'tables':
                $this->saveTables();
                break;
            case 'complete':
                $this->saveComplete();
                break;
            default:
                $this->error('Tipe tidak valid!');
                return 1;
        }

        return 0;
    }

    protected function saveRoles()
    {
        $this->info('=== Menyimpan Data Roles ===');

        $prefix = prompt_text(
            label: 'Prefix untuk nama seeder:',
            default: 'MSJ',
            command: $this
        );

        $roles = DB::table('sys_roles')->where('isactive', '1')->get();

        if ($roles->isEmpty()) {
            $this->error('Tidak ada data roles yang ditemukan!');
            return;
        }

        $seederName = "{$prefix}RoleSeeder";
        $path = database_path("seeders/{$seederName}.php");

        $content = $this->generateRoleSeeder($seederName, $roles);

        if (File::exists($path)) {
            if (!prompt_confirm("File {$seederName}.php sudah ada. Timpa?", command: $this)) {
                return;
            }
        }

        File::put($path, $content);
        $this->info("✅ {$seederName}.php berhasil dibuat dengan {$roles->count()} roles");
    }

    protected function saveAuth()
    {
        $this->info('=== Menyimpan Data Authorization ===');

        $prefix = prompt_text(
            label: 'Prefix untuk nama seeder:',
            default: 'MSJ',
            command: $this
        );

        $auths = DB::table('sys_auth')
            ->join('sys_roles', 'sys_auth.idroles', '=', 'sys_roles.idroles')
            ->join('sys_dmenu', 'sys_auth.dmenu', '=', 'sys_dmenu.dmenu')
            ->join('sys_gmenu', 'sys_auth.gmenu', '=', 'sys_gmenu.gmenu')
            ->where('sys_auth.isactive', '1')
            ->select('sys_auth.*', 'sys_roles.name as role_name', 'sys_dmenu.name as menu_name', 'sys_gmenu.name as group_name')
            ->get();

        if ($auths->isEmpty()) {
            $this->error('Tidak ada data authorization yang ditemukan!');
            return;
        }

        $seederName = "{$prefix}AuthSeeder";
        $path = database_path("seeders/{$seederName}.php");

        $content = $this->generateAuthSeeder($seederName, $auths);

        if (File::exists($path)) {
            if (!prompt_confirm("File {$seederName}.php sudah ada. Timpa?", command: $this)) {
                return;
            }
        }

        File::put($path, $content);
        $this->info("✅ {$seederName}.php berhasil dibuat dengan {$auths->count()} authorization");
    }

    protected function saveUsers()
    {
        $this->info('=== Menyimpan Data Users ===');

        $prefix = prompt_text(
            label: 'Prefix untuk nama seeder:',
            default: 'MSJ',
            command: $this
        );

        $users = DB::table('users')->where('isactive', '1')->get();

        if ($users->isEmpty()) {
            $this->error('Tidak ada data users yang ditemukan!');
            return;
        }

        $seederName = "{$prefix}UserSeeder";
        $path = database_path("seeders/{$seederName}.php");

        $content = $this->generateUserSeeder($seederName, $users);

        if (File::exists($path)) {
            if (!prompt_confirm("File {$seederName}.php sudah ada. Timpa?", command: $this)) {
                return;
            }
        }

        File::put($path, $content);
        $this->info("✅ {$seederName}.php berhasil dibuat dengan {$users->count()} users");
    }

    protected function saveMenus()
    {
        $this->info('=== Menyimpan Data Menus ===');

        $prefix = $this->option('prefix') ?: 'MSJ';

        $gmenus = DB::table('sys_gmenu')->where('isactive', '1')->orderBy('urut')->get();
        $dmenus = DB::table('sys_dmenu')->where('isactive', '1')->orderBy('urut')->get();

        if ($gmenus->isEmpty() && $dmenus->isEmpty()) {
            $this->error('Tidak ada data menu yang ditemukan!');
            return;
        }

        $seederName = "{$prefix}MenuSeeder";
        $path = database_path("seeders/{$seederName}.php");

        $content = $this->generateMenuSeeder($seederName, $gmenus, $dmenus);

        if (!$this->checkFileOverwrite($path, "{$seederName}.php")) {
            return;
        }

        File::put($path, $content);
        $this->info("✅ {$seederName}.php berhasil dibuat dengan {$gmenus->count()} group menus dan {$dmenus->count()} detail menus");
    }

    protected function saveTables()
    {
        $this->info('=== Menyimpan Data Table Config ===');

        $prefix = $this->option('prefix') ?: 'MSJ';

        $tables = DB::table('sys_table')
            ->where('isactive', '1')
            ->orderBy('gmenu')
            ->orderBy('dmenu')
            ->orderBy('urut')
            ->get();

        if ($tables->isEmpty()) {
            $this->error('Tidak ada data table config yang ditemukan!');
            return;
        }

        $seederName = "{$prefix}TableSeeder";
        $path = database_path("seeders/{$seederName}.php");

        $content = $this->generateTableSeeder($seederName, $tables);

        if (!$this->checkFileOverwrite($path, "{$seederName}.php")) {
            return;
        }

        File::put($path, $content);
        $this->info("✅ {$seederName}.php berhasil dibuat dengan {$tables->count()} table configurations");
    }

    protected function saveComplete()
    {
        $this->info('=== Menyimpan Complete Setup ===');

        $prefix = prompt_text(
            label: 'Prefix untuk nama seeder:',
            default: 'MSJ',
            command: $this
        );

        if (prompt_confirm('Apakah Anda ingin membuat semua seeder?', command: $this)) {
            $this->saveRoles();
            $this->saveAuth();
            $this->saveUsers();
            $this->saveMenus();
            $this->saveTables();

            // Create complete seeder
            $seederName = "{$prefix}CompleteSeeder";
            $path = database_path("seeders/{$seederName}.php");

            $content = $this->generateCompleteSeeder($seederName, $prefix);

            if (File::exists($path)) {
                if (!prompt_confirm("File {$seederName}.php sudah ada. Timpa?", command: $this)) {
                    return;
                }
            }

            File::put($path, $content);
            $this->info("✅ {$seederName}.php berhasil dibuat");
            $this->info('');
            $this->info('📋 Cara penggunaan:');
            $this->info("   php artisan db:seed --class={$seederName}");
        }
    }

    protected function generateRoleSeeder($seederName, $roles)
    {
        $rolesArray = [];
        foreach ($roles as $role) {
            $rolesArray[] = [
                'idroles' => $role->idroles,
                'name' => $role->name,
                'description' => $role->description,
                'isactive' => $role->isactive,
                'user_create' => $role->user_create ?? 'seeder',
                'created_at' => 'now()',
                'updated_at' => 'now()',
            ];
        }

        $rolesCode = $this->arrayToPhpCode($rolesArray, 3);

        return <<<PHP
<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class {$seederName} extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Clear existing data
        DB::table('sys_roles')->truncate();

        // Insert roles data
        \$roles = {$rolesCode};

        DB::table('sys_roles')->insert(\$roles);

        \$this->command->info('✅ Roles data berhasil di-seed (' . count(\$roles) . ' records)');
    }
}
PHP;
    }

    protected function generateAuthSeeder($seederName, $auths)
    {
        $authsArray = [];
        foreach ($auths as $auth) {
            $authsArray[] = [
                'idroles' => $auth->idroles,
                'gmenu' => $auth->gmenu,
                'dmenu' => $auth->dmenu,
                'add' => $auth->add,
                'edit' => $auth->edit,
                'delete' => $auth->delete,
                'approval' => $auth->approval,
                'value' => $auth->value,
                'print' => $auth->print,
                'excel' => $auth->excel,
                'pdf' => $auth->pdf,
                'rules' => $auth->rules,
                'isactive' => $auth->isactive,
                'user_create' => $auth->user_create ?? 'seeder',
                'created_at' => 'now()',
                'updated_at' => 'now()',
            ];
        }

        $authsCode = $this->arrayToPhpCode($authsArray, 3);

        return <<<PHP
<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class {$seederName} extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Clear existing data
        DB::table('sys_auth')->truncate();

        // Insert authorization data
        \$auths = {$authsCode};

        DB::table('sys_auth')->insert(\$auths);

        \$this->command->info('✅ Authorization data berhasil di-seed (' . count(\$auths) . ' records)');
    }
}
PHP;
    }

    protected function generateUserSeeder($seederName, $users)
    {
        $usersArray = [];
        foreach ($users as $user) {
            $usersArray[] = [
                'username' => $user->username,
                'firstname' => $user->firstname ?? '',
                'lastname' => $user->lastname ?? '',
                'email' => $user->email,
                'password' => "'" . $user->password . "'", // Keep existing hash
                'idroles' => $user->idroles,
                'isactive' => $user->isactive,
                'user_create' => $user->user_create ?? 'seeder',
                'created_at' => 'now()',
                'updated_at' => 'now()',
            ];
        }

        $usersCode = $this->arrayToPhpCode($usersArray, 3);

        return <<<PHP
<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class {$seederName} extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Clear existing users (except id = 1 if exists)
        DB::table('users')->where('id', '>', 1)->delete();

        // Insert users data
        \$users = {$usersCode};

        foreach (\$users as \$user) {
            // Check if user already exists
            \$exists = DB::table('users')
                ->where('username', \$user['username'])
                ->orWhere('email', \$user['email'])
                ->exists();

            if (!\$exists) {
                DB::table('users')->insert(\$user);
                \$this->command->info("✅ User '{\$user['username']}' berhasil dibuat");
            } else {
                \$this->command->warn("⚠️ User '{\$user['username']}' sudah ada, dilewati");
            }
        }
    }
}
PHP;
    }

    protected function generateMenuSeeder($seederName, $gmenus, $dmenus)
    {
        $gmenusArray = [];
        foreach ($gmenus as $gmenu) {
            $gmenusArray[] = [
                'gmenu' => $gmenu->gmenu,
                'name' => $gmenu->name,
                'icon' => $gmenu->icon ?? 'fas fa-folder',
                'urut' => $gmenu->urut,
                'isactive' => $gmenu->isactive,
                'user_create' => $gmenu->user_create ?? 'seeder',
                'created_at' => 'now()',
                'updated_at' => 'now()',
            ];
        }

        $dmenusArray = [];
        foreach ($dmenus as $dmenu) {
            $dmenusArray[] = [
                'gmenu' => $dmenu->gmenu,
                'dmenu' => $dmenu->dmenu,
                'js' => $dmenu->js ?? '1',
                'name' => $dmenu->name,
                'layout' => $dmenu->layout ?? 'manual',
                'url' => $dmenu->url,
                'tabel' => $dmenu->tabel,
                'where' => $dmenu->where,
                'icon' => $dmenu->icon ?? 'fas fa-file',
                'urut' => $dmenu->urut,
                'isactive' => $dmenu->isactive,
                'user_create' => $dmenu->user_create ?? 'seeder',
                'created_at' => 'now()',
                'updated_at' => 'now()',
            ];
        }

        $gmenusCode = $this->arrayToPhpCode($gmenusArray, 3);
        $dmenusCode = $this->arrayToPhpCode($dmenusArray, 3);

        // Get unique gmenu codes for deletion
        $gmenuCodes = $gmenus->pluck('gmenu')->unique()->toArray();
        $dmenuCodes = $dmenus->pluck('dmenu')->unique()->toArray();

        $gmenuCodesStr = "'" . implode("', '", $gmenuCodes) . "'";
        $dmenuCodesStr = "'" . implode("', '", $dmenuCodes) . "'";

        return <<<PHP
<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class {$seederName} extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Delete existing data first (urutan penting karena foreign key)
        \$dmenuCodes = [{$dmenuCodesStr}];
        \$gmenuCodes = [{$gmenuCodesStr}];

        DB::table('sys_auth')->whereIn('dmenu', \$dmenuCodes)->delete();
        DB::table('sys_table')->whereIn('dmenu', \$dmenuCodes)->delete();
        DB::table('sys_dmenu')->whereIn('dmenu', \$dmenuCodes)->delete();
        DB::table('sys_gmenu')->whereIn('gmenu', \$gmenuCodes)->delete();

        // Insert sys_gmenu (Group Menu)
        \$gmenus = {$gmenusCode};

        DB::table('sys_gmenu')->insert(\$gmenus);

        // Insert sys_dmenu (Detail Menu)
        \$dmenus = {$dmenusCode};

        DB::table('sys_dmenu')->insert(\$dmenus);

        \$this->command->info('✅ Menu data berhasil di-seed (' . count(\$gmenus) . ' group menus, ' . count(\$dmenus) . ' detail menus)');
    }
}
PHP;
    }

    protected function generateTableSeeder($seederName, $tables)
    {
        // Group tables by dmenu
        $tablesByMenu = [];
        foreach ($tables as $table) {
            $key = $table->gmenu . '|' . $table->dmenu;
            if (!isset($tablesByMenu[$key])) {
                $tablesByMenu[$key] = [
                    'gmenu' => $table->gmenu,
                    'dmenu' => $table->dmenu,
                    'fields' => []
                ];
            }
            
            $field = [
                'field' => $table->field,
                'alias' => $table->alias,
                'type' => $table->type,
                'length' => $table->length,
                'decimals' => $table->decimals,
                'default' => $table->default,
                'validate' => $table->validate,
                'primary' => $table->primary,
                'generateid' => $table->generateid,
                'filter' => $table->filter,
                'list' => $table->list,
                'show' => $table->show,
                'query' => $table->query,
                'class' => $table->class,
                'sub' => $table->sub,
                'link' => $table->link,
                'note' => $table->note,
                'position' => $table->position,
            ];
            
            // Remove null/empty values
            $field = array_filter($field, function($value) {
                return $value !== null && $value !== '';
            });
            
            $tablesByMenu[$key]['fields'][] = $field;
        }

        $configsCode = $this->generateTableConfigs($tablesByMenu);
        
        // Get unique dmenu codes for deletion
        $dmenuCodes = collect($tables)->pluck('dmenu')->unique()->toArray();
        $dmenuCodesStr = "'" . implode("', '", $dmenuCodes) . "'";

        return <<<PHP
<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class {$seederName} extends Seeder
{
    public function run(): void
    {
        // Delete existing data
        \$dmenuCodes = [{$dmenuCodesStr}];
        
        foreach (\$dmenuCodes as \$dmenu) {
            DB::table('sys_table')->where('dmenu', \$dmenu)->delete();
        }

        // Define table configurations
        \$configs = {$configsCode};

        // Insert all configurations
        foreach (\$configs as \$dmenu => \$config) {
            \$this->insertTableConfig(\$dmenu, \$config);
        }

        \$this->command->info('✅ Table config data berhasil di-seed (' . count(\$configs) . ' menus)');
    }

    private function insertTableConfig(\$dmenu, \$config)
    {
        \$urut = 1;
        foreach (\$config['fields'] as \$field) {
            \$data = array_merge([
                'gmenu' => \$config['gmenu'],
                'dmenu' => \$dmenu,
                'urut' => \$urut++,
                'decimals' => '0',
                'default' => '',
                'validate' => '',
                'primary' => '0',
                'generateid' => null,
                'filter' => '1',
                'list' => '1',
                'show' => '1',
                'query' => '',
                'class' => null,
                'sub' => null,
                'link' => null,
                'note' => null,
                'position' => '0',
                'isactive' => '1',
                'user_create' => 'seeder',
                'created_at' => now(),
                'updated_at' => now(),
            ], \$field);

            DB::table('sys_table')->insert(\$data);
        }
    }
}
PHP;
    }

    protected function generateCompleteSeeder($seederName, $prefix)
    {
        return <<<PHP
<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class {$seederName} extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        \$this->command->info('🚀 Memulai setup lengkap MSJ Framework...');

        // Run all seeders in order
        \$this->call({$prefix}RoleSeeder::class);
        \$this->call({$prefix}MenuSeeder::class);
        \$this->call({$prefix}TableSeeder::class);
        \$this->call({$prefix}AuthSeeder::class);
        \$this->call({$prefix}UserSeeder::class);

        \$this->command->info('✅ Setup lengkap MSJ Framework selesai!');
        \$this->command->info('🔐 Sistem siap digunakan');
    }
}
PHP;
    }

    protected function arrayToPhpCode($array, $indent = 0)
    {
        $spaces = str_repeat('    ', $indent);
        $result = "[\n";
        
        foreach ($array as $item) {
            $result .= $spaces . "    [\n";
            foreach ($item as $key => $value) {
                if ($value === 'now()') {
                    $result .= $spaces . "        '{$key}' => now(),\n";
                } elseif (is_string($value) && strpos($value, "'") === 0) {
                    // Already quoted string (like password hash)
                    $result .= $spaces . "        '{$key}' => {$value},\n";
                } else {
                    $result .= $spaces . "        '{$key}' => " . var_export($value, true) . ",\n";
                }
            }
            $result .= $spaces . "    ],\n";
        }
        
        $result .= $spaces . "]";
        return $result;
    }

    protected function generateTableConfigs($tablesByMenu)
    {
        $result = "[\n";
        
        foreach ($tablesByMenu as $key => $config) {
            $dmenu = $config['dmenu'];
            $result .= "            // {$dmenu}\n";
            $result .= "            '{$dmenu}' => [\n";
            $result .= "                'gmenu' => '{$config['gmenu']}',\n";
            $result .= "                'fields' => [\n";
            
            foreach ($config['fields'] as $field) {
                $result .= "                    [";
                foreach ($field as $key => $value) {
                    if ($value !== null && $value !== '') {
                        $result .= "'{$key}' => " . var_export($value, true) . ", ";
                    }
                }
                $result .= "],\n";
            }
            
            $result .= "                ],\n";
            $result .= "            ],\n\n";
        }
        
        $result .= "        ]";
        return $result;
    }

    protected function checkFileOverwrite(string $path, string $filename): bool
    {
        if (File::exists($path)) {
            if ($this->option('auto')) {
                $this->warn("File {$filename} sudah ada, akan ditimpa...");
                return true;
            } else {
                return prompt_confirm("File {$filename} sudah ada. Timpa?", command: $this);
            }
        }
        return true;
    }
}
