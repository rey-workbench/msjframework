<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

/**
 * Examples Seeder for MSJ Framework
 * 
 * This seeder imports example data demonstrating various layout types:
 * - Master layout
 * - Standard layout
 * - System layout
 * - Sub-linking layout
 * - Transaction layout
 * - Report layout
 * 
 * Usage:
 * php artisan db:seed --class=MSJExamplesSeeder
 */
class MSJExamplesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->command->info('🎨 Seeding MSJ Framework Examples...');
        $this->command->newLine();

        // 1. Example: Master Layout
        $this->command->info('📝 Seeding master layout example...');
        $this->call(example_tabel_form_master::class);

        // 2. Example: Standard Layout
        $this->command->info('📝 Seeding standard layout example...');
        $this->call(example_tabel_form_standard::class);

        // 3. Example: System Layout
        $this->command->info('📝 Seeding system layout example...');
        $this->call(example_tabel_form_system::class);

        // 4. Example: Sub-linking Layout
        $this->command->info('📝 Seeding sub-linking layout example...');
        $this->call(example_tabel_form_sublink::class);
        $this->call(example_tabel_sublink::class);

        // 5. Example: Report
        $this->command->info('📝 Seeding report example...');
        $this->call(example_tabel_rpt_syslog::class);

        // 6. Example: Generate ID
        $this->command->info('📝 Seeding ID generation example...');
        $this->call(example_generate_id::class);

        // 7. Example: Data
        $this->command->info('📝 Seeding example data...');
        $this->call(example_tabel_data::class);
        $this->call(example_tabel_data_by_rule::class);

        // 8. Example: Insert Data
        $this->command->info('📝 Seeding insert data example...');
        $this->call(example_insert_data::class);

        // 9. Example: Group Menu
        $this->command->info('📝 Seeding group menu example...');
        $this->call(example_gmenu::class);

        $this->command->newLine();
        $this->command->info('✅ Examples seeded successfully!');
        $this->command->newLine();
        
        $this->command->info('💡 You can now explore:');
        $this->command->line('   - Master layout at /example-master');
        $this->command->line('   - Standard layout at /example-standard');
        $this->command->line('   - System layout at /example-system');
        $this->command->line('   - Sub-linking at /example-sublink');
        $this->command->line('   - Report at /example-report');
    }
}
