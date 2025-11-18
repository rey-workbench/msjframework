<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class MSJSystemSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 1. sys_app - Application configuration
        DB::table('sys_app')->insert([
            'appid' => 'APP01',
            'appname' => 'MSJ Application',
            'description' => 'Metadata-Driven Laravel Application',
            'icon' => '/img/logos/icon.png',
            'cover_in' => '/img/cover-in.jpg',
            'cover_out' => '/img/cover-out.jpg',
            'version' => '1.0.0',
            'isactive' => '1',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // 2. sys_roles - User roles
        DB::table('sys_roles')->insertMany([
            [
                'idroles' => 'admin',
                'name' => 'Administrator',
                'description' => 'System administrator with full access',
                'isactive' => '1',
                'user_create' => 'system',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'idroles' => 'user',
                'name' => 'User',
                'description' => 'Regular user',
                'isactive' => '1',
                'user_create' => 'system',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);

        // 3. sys_gmenu - Group menu
        DB::table('sys_gmenu')->insertMany([
            [
                'gmenu' => 'MSJ001',
                'name' => 'Master Data',
                'icon' => 'fa-database',
                'urut' => 1,
                'isactive' => '1',
                'user_create' => 'system',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'gmenu' => 'MSJ002',
                'name' => 'System',
                'icon' => 'fa-cog',
                'urut' => 99,
                'isactive' => '1',
                'user_create' => 'system',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);

        // 4. sys_dmenu - Detail menu (Example: User management)
        DB::table('sys_dmenu')->insert([
            'dmenu' => 'MSJ001',
            'gmenu' => 'MSJ002',
            'name' => 'Users',
            'url' => 'users',
            'tabel' => 'users',
            'layout' => 'master',
            'where' => null,
            'js' => '0',
            'urut' => 1,
            'isactive' => '1',
            'user_create' => 'system',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // 5. sys_auth - Authorization (Give admin full access to users menu)
        DB::table('sys_auth')->insert([
            'gmenu' => 'MSJ002',
            'dmenu' => 'MSJ001',
            'idroles' => 'admin',
            'value' => '1',
            'add' => '1',
            'edit' => '1',
            'delete' => '1',
            'approval' => '0',
            'rules' => '0',
            'isactive' => '1',
            'user_create' => 'system',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // 6. sys_table - Table configuration for users (Example)
        $userFields = [
            [
                'field' => 'id',
                'alias' => 'ID',
                'type' => 'hidden',
                'primary' => '1',
                'list' => '0',
                'show' => '0',
                'position' => '3',
                'urut' => 1,
            ],
            [
                'field' => 'name',
                'alias' => 'Name',
                'type' => 'string',
                'length' => 100,
                'validate' => 'required|max:100',
                'primary' => '0',
                'list' => '1',
                'show' => '1',
                'position' => '3',
                'urut' => 2,
            ],
            [
                'field' => 'email',
                'alias' => 'Email',
                'type' => 'email',
                'length' => 100,
                'validate' => 'required|email|unique:users,email',
                'primary' => '0',
                'list' => '1',
                'show' => '1',
                'position' => '3',
                'urut' => 3,
            ],
            [
                'field' => 'password',
                'alias' => 'Password',
                'type' => 'password',
                'validate' => 'required|min:6',
                'primary' => '0',
                'list' => '0',
                'show' => '0',
                'position' => '3',
                'urut' => 4,
            ],
            [
                'field' => 'idroles',
                'alias' => 'Role',
                'type' => 'enum',
                'query' => 'SELECT idroles as id, name as text FROM sys_roles WHERE isactive="1"',
                'validate' => 'required',
                'primary' => '0',
                'list' => '1',
                'show' => '1',
                'position' => '3',
                'urut' => 5,
            ],
        ];

        foreach ($userFields as $field) {
            DB::table('sys_table')->insert(array_merge([
                'gmenu' => 'MSJ002',
                'dmenu' => 'MSJ001',
                'decimals' => 0,
                'default' => null,
                'class' => null,
                'note' => null,
                'generateid' => '0',
                'filter' => '0',
                'link' => null,
                'sub' => null,
                'isactive' => '1',
                'created_at' => now(),
                'updated_at' => now(),
            ], $field));
        }

        // 7. Create default admin user (optional)
        DB::table('users')->insert([
            'name' => 'Administrator',
            'email' => 'admin@example.com',
            'password' => Hash::make('password'),
            'idroles' => 'admin',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $this->command->info('✅ MSJ System data seeded successfully!');
        $this->command->warn('📝 Default admin credentials:');
        $this->command->line('   Email: admin@example.com');
        $this->command->line('   Password: password');
    }
}
