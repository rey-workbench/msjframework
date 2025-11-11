<?php

namespace MSJFramework\LaravelGenerator\Console\Commands;

use MSJFramework\LaravelGenerator\Console\Commands\Concerns\HasConsoleStyling;
use MSJFramework\LaravelGenerator\Console\Commands\Concerns\HasDatabaseOperations;
use MSJFramework\LaravelGenerator\Console\Commands\Concerns\HasValidation;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use function Laravel\Prompts\confirm;
use function Laravel\Prompts\multiselect;
use function Laravel\Prompts\password;
use function Laravel\Prompts\select;
use function Laravel\Prompts\text;

class MakeMSJAuth extends Command
{
    use HasConsoleStyling, HasDatabaseOperations, HasValidation;

    protected $signature = 'msj:make:auth {type?}';
    protected $description = 'Generate authentication data (roles, auth, users)';

    public function handle(): int
    {
        $this->displayHeader('MSJ Auth Generator');
        
        $type = $this->argument('type');

        if (!$type) {
            $type = select(
                'Pilih tipe data yang ingin dibuat:',
                [
                    'role' => 'Role (sys_roles)',
                    'auth' => 'Authorization (sys_auth)', 
                    'user' => 'User (users)',
                    'all' => 'Semua (role, auth, user)'
                ]
            );
        }

        switch ($type) {
            case 'role':
                $this->createRole();
                break;
            case 'auth':
                $this->createAuth();
                break;
            case 'user':
                $this->createUser();
                break;
            case 'all':
                $this->createAll();
                break;
            default:
                $this->error('Tipe tidak valid!');
                return 1;
        }

        return 0;
    }

    protected function createRole()
    {
        $this->info('=== Membuat Role Baru ===');

        $idroles = text(
            label: 'ID Role (max 6 karakter):',
            placeholder: 'admin1',
            validate: function(string $value) {
                $validation = $this->validateRoleId($value, 6);
                if ($validation) return $validation;
                
                // Check duplicate
                if ($this->roleExists($value)) {
                    return "ID Role '{$value}' sudah ada";
                }
                
                return null;
            }
        );

        $name = text(
            label: 'Nama Role (max 20 karakter):',
            placeholder: 'Administrator',
            validate: fn(string $value) => $this->validateRoleName($value, 20)
        );

        $description = text(
            label: 'Deskripsi (max 100 karakter):',
            placeholder: 'Role untuk administrator sistem',
            validate: fn(string $value) => $this->validateRoleDescription($value, 100)
        );

        try {
            DB::table('sys_roles')->insert([
                'idroles' => $idroles,
                'name' => $name,
                'description' => $description,
                'isactive' => '1',
                'user_create' => 'system',
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            $this->info("✅ Role '{$name}' berhasil dibuat dengan ID: {$idroles}");
        } catch (\Exception $e) {
            $this->error("❌ Gagal membuat role: " . $e->getMessage());
        }
    }

    protected function createAuth()
    {
        $this->info('=== Membuat Authorization Baru ===');

        // Get available roles
        $roles = DB::table('sys_roles')->where('isactive', '1')->pluck('name', 'idroles')->toArray();
        if (empty($roles)) {
            $this->error('Tidak ada role yang tersedia. Buat role terlebih dahulu.');
            return;
        }

        $idroles = select(
            'Pilih Role:',
            $roles
        );

        // Get available group menus
        $gmenus = DB::table('sys_gmenu')->where('isactive', '1')->pluck('name', 'gmenu')->toArray();
        if (empty($gmenus)) {
            $this->error('Tidak ada group menu yang tersedia.');
            return;
        }

        $gmenu = select(
            'Pilih Group Menu:',
            $gmenus
        );

        // Get available detail menus for selected group
        $dmenus = DB::table('sys_dmenu')
            ->where('gmenu', $gmenu)
            ->where('isactive', '1')
            ->pluck('name', 'dmenu')
            ->toArray();

        if (empty($dmenus)) {
            $this->error('Tidak ada detail menu yang tersedia untuk group menu ini.');
            return;
        }

        $dmenu = select(
            'Pilih Detail Menu:',
            $dmenus
        );

        // Check if auth already exists
        $exists = DB::table('sys_auth')
            ->where('idroles', $idroles)
            ->where('gmenu', $gmenu)
            ->where('dmenu', $dmenu)
            ->exists();

        if ($exists) {
            $this->error('Authorization untuk kombinasi ini sudah ada!');
            return;
        }

        $this->info('Pilih permissions (1 = Ya, 0 = Tidak):');

        $permissions = [
            'add' => select('Add/Create:', ['0' => 'Tidak', '1' => 'Ya']),
            'edit' => select('Edit/Update:', ['0' => 'Tidak', '1' => 'Ya']),
            'delete' => select('Delete:', ['0' => 'Tidak', '1' => 'Ya']),
            'approval' => select('Approval:', ['0' => 'Tidak', '1' => 'Ya']),
            'value' => select('View/Value:', ['0' => 'Tidak', '1' => 'Ya'], default: '1'),
            'print' => select('Print:', ['0' => 'Tidak', '1' => 'Ya'], default: '1'),
            'excel' => select('Excel:', ['0' => 'Tidak', '1' => 'Ya'], default: '1'),
            'pdf' => select('PDF:', ['0' => 'Tidak', '1' => 'Ya'], default: '1'),
            'rules' => select('Rules:', ['0' => 'Tidak', '1' => 'Ya']),
        ];

        try {
            DB::table('sys_auth')->insert([
                'idroles' => $idroles,
                'gmenu' => $gmenu,
                'dmenu' => $dmenu,
                'add' => $permissions['add'],
                'edit' => $permissions['edit'],
                'delete' => $permissions['delete'],
                'approval' => $permissions['approval'],
                'value' => $permissions['value'],
                'print' => $permissions['print'],
                'excel' => $permissions['excel'],
                'pdf' => $permissions['pdf'],
                'rules' => $permissions['rules'],
                'isactive' => '1',
                'user_create' => 'system',
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            $roleName = $roles[$idroles];
            $gmenuName = $gmenus[$gmenu];
            $dmenuName = $dmenus[$dmenu];

            $this->info("✅ Authorization berhasil dibuat untuk:");
            $this->info("   Role: {$roleName}");
            $this->info("   Group Menu: {$gmenuName}");
            $this->info("   Detail Menu: {$dmenuName}");
        } catch (\Exception $e) {
            $this->error("❌ Gagal membuat authorization: " . $e->getMessage());
        }
    }

    protected function createUser()
    {
        $this->info('=== Membuat User Baru ===');

        $name = text(
            label: 'Nama Depan:',
            placeholder: 'John',
            validate: fn (string $value) => match (true) {
                strlen($value) < 2 => 'Nama minimal 2 karakter',
                strlen($value) > 255 => 'Nama maksimal 255 karakter',
                default => null
            }
        );

        $username = text(
            label: 'Username:',
            placeholder: 'johndoe',
            validate: fn (string $value) => match (true) {
                strlen($value) < 3 => 'Username minimal 3 karakter',
                strlen($value) > 20 => 'Username maksimal 20 karakter',
                !preg_match('/^[a-zA-Z0-9_]+$/', $value) => 'Username hanya boleh huruf, angka, dan underscore',
                DB::table('users')->where('username', $value)->exists() => 'Username sudah ada',
                default => null
            }
        );

        $email = text(
            label: 'Email:',
            placeholder: 'john@example.com',
            validate: fn (string $value) => match (true) {
                !filter_var($value, FILTER_VALIDATE_EMAIL) => 'Format email tidak valid',
                DB::table('users')->where('email', $value)->exists() => 'Email sudah ada',
                default => null
            }
        );

        $password = password(
            label: 'Password:',
            placeholder: 'Minimal 8 karakter',
            validate: fn (string $value) => strlen($value) < 8 ? 'Password minimal 8 karakter' : null
        );

        // Get available roles
        $roles = DB::table('sys_roles')->where('isactive', '1')->pluck('name', 'idroles')->toArray();
        if (empty($roles)) {
            $this->error('Tidak ada role yang tersedia. Buat role terlebih dahulu.');
            return;
        }

        $selectedRoles = multiselect(
            'Pilih Role (bisa multiple):',
            $roles
        );

        try {
            DB::table('users')->insert([
                'firstname' => $name,
                'lastname' => '',
                'username' => $username,
                'email' => $email,
                'password' => Hash::make($password),
                'idroles' => implode(',', $selectedRoles),
                'isactive' => '1',
                'user_create' => 'system',
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            $roleNames = array_intersect_key($roles, array_flip($selectedRoles));

            $this->info("✅ User '{$name}' berhasil dibuat:");
            $this->info("   Username: {$username}");
            $this->info("   Email: {$email}");
            $this->info("   Roles: " . implode(', ', $roleNames));
        } catch (\Exception $e) {
            $this->error("❌ Gagal membuat user: " . $e->getMessage());
        }
    }

    protected function createAll()
    {
        $this->info('=== Membuat Role, Auth, dan User Lengkap ===');

        if (confirm('Apakah Anda ingin membuat data auth lengkap?')) {
            $this->info('1. Membuat Role...');
            $this->createRole();

            $this->info('2. Membuat Authorization...');
            $this->createAuth();

            $this->info('3. Membuat User...');
            $this->createUser();

            $this->info('✅ Semua data auth berhasil dibuat!');
        }
    }
}
