<?php

namespace MSJFramework\LaravelGenerator\Console\Commands\Database;

use MSJFramework\LaravelGenerator\Console\Commands\Concerns\HasConsoleStyling;
use MSJFramework\LaravelGenerator\Console\Commands\Concerns\HasDatabaseOperations;
use MSJFramework\LaravelGenerator\Console\Commands\Concerns\HasValidation;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
// Import safe prompt helpers that work on all platforms

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
            $type = prompt_select(
                'Pilih tipe data yang ingin dibuat:',
                [
                    'role' => 'Role (sys_roles)',
                    'auth' => 'Authorization (sys_auth)', 
                    'user' => 'User (users)',
                    'all' => 'Semua (role, auth, user)'
                ],
                command: $this
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
        $this->displayHeader('Create Role');

        $idroles = prompt_text(
            label: 'ID Role (max 6 karakter):',
            default: 'admin1',
            required: true,
            command: $this
        );
        
        // Validate role ID
        $validation = $this->validateRoleId($idroles, 6);
        if ($validation) {
            $this->badge('error', $validation);
            return;
        }
        
        // Check duplicate
        if ($this->roleExists($idroles)) {
            $this->badge('error', "ID Role '{$idroles}' sudah ada");
            return;
        }

        $name = prompt_text(
            label: 'Nama Role (max 20 karakter):',
            default: 'Administrator',
            required: true,
            command: $this
        );
        
        // Validate role name
        $validation = $this->validateRoleName($name, 20);
        if ($validation) {
            $this->badge('error', $validation);
            return;
        }

        $description = prompt_text(
            label: 'Deskripsi (max 100 karakter):',
            default: 'Role untuk administrator sistem',
            required: false,
            command: $this
        );
        
        // Validate description
        $validation = $this->validateRoleDescription($description, 100);
        if ($validation) {
            $this->badge('error', $validation);
            return;
        }

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

            $this->badge('success', "Role '{$name}' berhasil dibuat dengan ID: {$idroles}");
        } catch (\Exception $e) {
            $this->badge('error', "Gagal membuat role: " . $e->getMessage());
        }
    }

    protected function createAuth()
    {
        $this->displayHeader('Create Authorization');

        // Get available roles
        $roles = DB::table('sys_roles')->where('isactive', '1')->pluck('name', 'idroles')->toArray();
        if (empty($roles)) {
            $this->badge('error', 'Tidak ada role yang tersedia. Buat role terlebih dahulu.');
            return;
        }

        $idroles = prompt_select(
            'Pilih Role:',
            $roles,
            command: $this
        );

        // Get available group menus
        $gmenus = DB::table('sys_gmenu')->where('isactive', '1')->pluck('name', 'gmenu')->toArray();
        if (empty($gmenus)) {
            $this->badge('error', 'Tidak ada group menu yang tersedia.');
            return;
        }

        $gmenu = prompt_select(
            'Pilih Group Menu:',
            $gmenus,
            command: $this
        );

        // Get available detail menus for selected group
        $dmenus = DB::table('sys_dmenu')
            ->where('gmenu', $gmenu)
            ->where('isactive', '1')
            ->pluck('name', 'dmenu')
            ->toArray();

        if (empty($dmenus)) {
            $this->badge('error', 'Tidak ada detail menu yang tersedia untuk group menu ini.');
            return;
        }

        $dmenu = prompt_select(
            'Pilih Detail Menu:',
            $dmenus,
            command: $this
        );

        // Check if auth already exists
        $exists = DB::table('sys_auth')
            ->where('idroles', $idroles)
            ->where('gmenu', $gmenu)
            ->where('dmenu', $dmenu)
            ->exists();

        if ($exists) {
            $this->badge('error', 'Authorization untuk kombinasi ini sudah ada!');
            return;
        }

        // Pilih template permission atau custom
        $template = prompt_select(
            'Pilih template permission:',
            [
                'full' => 'Full Access (semua permission)',
                'readonly' => 'Read Only (hanya view, print, excel, pdf)',
                'editor' => 'Editor (add, edit, view, print, excel, pdf)',
                'custom' => 'Custom (pilih manual)'
            ],
            default: 'full',
            command: $this
        );

        $permissions = match($template) {
            'full' => [
                'add' => 1, 'edit' => 1, 'delete' => 1, 'approval' => 1,
                'value' => 1, 'print' => 1, 'excel' => 1, 'pdf' => 1, 'rules' => 1
            ],
            'readonly' => [
                'add' => 0, 'edit' => 0, 'delete' => 0, 'approval' => 0,
                'value' => 1, 'print' => 1, 'excel' => 1, 'pdf' => 1, 'rules' => 0
            ],
            'editor' => [
                'add' => 1, 'edit' => 1, 'delete' => 0, 'approval' => 0,
                'value' => 1, 'print' => 1, 'excel' => 1, 'pdf' => 1, 'rules' => 0
            ],
            'custom' => [
                'add' => (int) prompt_select('Add/Create:', ['0' => 'Tidak', '1' => 'Ya'], command: $this),
                'edit' => (int) prompt_select('Edit/Update:', ['0' => 'Tidak', '1' => 'Ya'], command: $this),
                'delete' => (int) prompt_select('Delete:', ['0' => 'Tidak', '1' => 'Ya'], command: $this),
                'approval' => (int) prompt_select('Approval:', ['0' => 'Tidak', '1' => 'Ya'], command: $this),
                'value' => (int) prompt_select('View/Value:', ['0' => 'Tidak', '1' => 'Ya'], default: '1', command: $this),
                'print' => (int) prompt_select('Print:', ['0' => 'Tidak', '1' => 'Ya'], default: '1', command: $this),
                'excel' => (int) prompt_select('Excel:', ['0' => 'Tidak', '1' => 'Ya'], default: '1', command: $this),
                'pdf' => (int) prompt_select('PDF:', ['0' => 'Tidak', '1' => 'Ya'], default: '1', command: $this),
                'rules' => (int) prompt_select('Rules:', ['0' => 'Tidak', '1' => 'Ya'], command: $this),
            ]
        };

        try {
            DB::table('sys_auth')->insert([
                'idroles' => $idroles,
                'gmenu' => $gmenu,
                'dmenu' => $dmenu,
                'add' => (string) $permissions['add'],
                'edit' => (string) $permissions['edit'],
                'delete' => (string) $permissions['delete'],
                'approval' => (string) $permissions['approval'],
                'value' => (string) $permissions['value'],
                'print' => (string) $permissions['print'],
                'excel' => (string) $permissions['excel'],
                'pdf' => (string) $permissions['pdf'],
                'rules' => (string) $permissions['rules'],
                'isactive' => '1',
                'user_create' => 'system',
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            $roleName = $roles[$idroles];
            $gmenuName = $gmenus[$gmenu];
            $dmenuName = $dmenus[$dmenu];

            $this->badge('success', "Authorization berhasil dibuat untuk:");
            $this->badge('info', "   Role: {$roleName}");
            $this->badge('info', "   Group Menu: {$gmenuName}");
            $this->badge('info', "   Detail Menu: {$dmenuName}");
        } catch (\Exception $e) {
            $this->badge('error', "Gagal membuat authorization: " . $e->getMessage());
        }
    }

    protected function createUser()
    {
        $this->displayHeader('Create User');

        $name = prompt_text(
            label: 'Nama Depan:',
            default: 'John',
            required: true,
            command: $this
        );
        
        // Validate name
        if (strlen($name) < 2) {
            $this->badge('error', 'Nama minimal 2 karakter');
            return;
        }
        if (strlen($name) > 255) {
            $this->badge('error', 'Nama maksimal 255 karakter');
            return;
        }

        $username = prompt_text(
            label: 'Username:',
            default: 'johndoe',
            required: true,
            command: $this
        );
        
        // Validate username
        if (strlen($username) < 3) {
            $this->badge('error', 'Username minimal 3 karakter');
            return;
        }
        if (strlen($username) > 20) {
            $this->badge('error', 'Username maksimal 20 karakter');
            return;
        }
        if (!preg_match('/^[a-zA-Z0-9_]+$/', $username)) {
            $this->badge('error', 'Username hanya boleh huruf, angka, dan underscore');
            return;
        }
        if (DB::table('users')->where('username', $username)->exists()) {
            $this->badge('error', 'Username sudah ada');
            return;
        }

        $email = prompt_text(
            label: 'Email:',
            default: 'john@example.com',
            required: true,
            command: $this
        );
        
        // Validate email
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $this->badge('error', 'Format email tidak valid');
            return;
        }
        if (DB::table('users')->where('email', $email)->exists()) {
            $this->badge('error', 'Email sudah ada');
            return;
        }

        $password = prompt_password(
            label: 'Password:',
            placeholder: 'Minimal 8 karakter',
            validate: fn (string $value) => strlen($value) < 8 ? 'Password minimal 8 karakter' : null,
            command: $this
        );

        // Get available roles
        $roles = DB::table('sys_roles')->where('isactive', '1')->pluck('name', 'idroles')->toArray();
        if (empty($roles)) {
            $this->badge('error', 'Tidak ada role yang tersedia. Buat role terlebih dahulu.');
            return;
        }

        $selectedRoles = prompt_multiselect(
            'Pilih Role (bisa multiple):',
            $roles,
            command: $this
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

            $this->badge('success', "User '{$name}' berhasil dibuat:");
            $this->badge('info', "   Username: {$username}");
            $this->badge('info', "   Email: {$email}");
            $this->badge('info', "   Roles: " . implode(', ', $roleNames));
        } catch (\Exception $e) {
            $this->badge('error', "Gagal membuat user: " . $e->getMessage());
        }
    }

    protected function createAll()
    {
        $this->displayHeader('Create All');

        if (prompt_confirm('Apakah Anda ingin membuat data auth lengkap?', command: $this)) {
            $this->badge('info', '1. Membuat Role...');
            $this->createRole();

            $this->badge('info', '2. Membuat Authorization...');
            $this->createAuth();

            $this->badge('info', '3. Membuat User...');
            $this->createUser();

            $this->badge('success', 'Semua data auth berhasil dibuat!');
        }
    }
}
