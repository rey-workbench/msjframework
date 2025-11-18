<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

/**
 * Main Data Seeder for MSJ Framework
 * 
 * This seeder imports all system tables data in correct order
 * to ensure foreign key constraints are satisfied.
 * 
 * Usage:
 * php artisan db:seed --class=KoperasiDataSeeder
 */
class KoperasiDataSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->command->info('🚀 Starting MSJ Framework Data Seeding...');
        $this->command->newLine();

        // 1. Application Configuration
        $this->command->info('📱 Seeding application configuration...');
        $this->call(tabel_sys_app::class);

        // 2. Roles
        $this->command->info('👥 Seeding roles...');
        $this->call(tabel_sys_role::class);

        // 3. Group Menu
        $this->command->info('📂 Seeding group menu...');
        $this->call(tabel_sys_gmenu::class);

        // 4. Detail Menu
        $this->command->info('📄 Seeding detail menu...');
        $this->call(tabel_sys_dmenu::class);

        // 5. Authorization
        $this->command->info('🔐 Seeding authorization...');
        $this->call(tabel_sys_auth::class);

        // 6. ID Generation Rules
        $this->command->info('🔢 Seeding ID generation rules...');
        $this->call(tabel_sys_id::class);

        // 7. Counter
        $this->command->info('📊 Seeding counter...');
        $this->call(tabel_sys_counter::class);

        // 8. Number Tracking
        $this->command->info('🔢 Seeding number tracking...');
        $this->call(tabel_sys_number::class);

        // 9. Enum Values
        $this->command->info('📋 Seeding enum values...');
        $this->call(tabel_sys_enum::class);

        // 10. Users
        $this->command->info('👤 Seeding users...');
        $this->call(tabel_users::class);

        $this->command->newLine();
        $this->command->info('✅ MSJ Framework data seeded successfully!');
        $this->command->newLine();
        
        $this->command->warn('📝 Default credentials:');
        $this->command->line('   Email: admin@msj.com');
        $this->command->line('   Password: password');
        $this->command->newLine();
        
        $this->command->info('💡 To seed examples, run:');
        $this->command->line('   php artisan db:seed --class=MSJExamplesSeeder');
    }
}
