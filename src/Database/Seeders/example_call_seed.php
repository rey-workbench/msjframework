<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class example_call_seed extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->call([
            example_gmenu::class,
            example_tabel_form_standard::class,
            example_tabel_form_master::class,
            example_tabel_form_system::class,
            example_tabel_form_sublink::class,
            example_tabel_rpt_syslog::class,
            example_tabel_sublink::class,
            example_tabel_data::class,
            example_tabel_data_by_rule::class,
            example_generate_id::class,
            example_insert_data::class,
        ]);
    }
}
