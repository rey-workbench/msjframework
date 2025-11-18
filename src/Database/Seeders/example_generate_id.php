<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class example_generate_id extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // delete data
        DB::table('sys_id')->delete();
        DB::table('sys_counter')->delete();
        // insert data
        DB::table('sys_id')->insert(
            [
                'dmenu' => 'msdata',
                'source' => 'int',
                'internal' => 'jenis',
                'external' => '0',
                'urut' => '1',
                'length' => '2',
                'isactive' => '1',
                'created_at' => '2025-03-04 03:17:23',
                'updated_at' => '2025-03-04 03:17:23',
                'user_create' => 'msjit',
                'user_update' => '',
            ]
        );
        DB::table('sys_id')->insert(
            [
                'dmenu' => 'msdata',
                'source' => 'cnt',
                'internal' => '-',
                'external' => '0',
                'urut' => '2',
                'length' => '3',
                'isactive' => '1',
                'created_at' => '2025-03-04 03:17:48',
                'updated_at' => '2025-03-04 03:17:48',
                'user_create' => 'msjit',
                'user_update' => '',
            ]
        );
        DB::table('sys_counter')->insert(
            [
                'character' => 'AA-',
                'counter' => '2',
                'lastid' => 'AA002',
                'isactive' => '1',
                'created_at' => '2025-03-04 04:59:56',
                'updated_at' => '2025-03-04 05:01:16',
                'user_create' => '',
                'user_update' => 'msjit',
            ]
        );
        DB::table('sys_counter')->insert(
            [
                'character' => 'BB-',
                'counter' => '1',
                'lastid' => 'BB001',
                'isactive' => '1',
                'created_at' => '2025-03-04 04:59:56',
                'updated_at' => '2025-03-04 05:01:16',
                'user_create' => '',
                'user_update' => 'msjit',
            ]
        );
        DB::table('sys_counter')->insert(
            [
                'character' => 'CC-',
                'counter' => '1',
                'lastid' => 'CC001',
                'isactive' => '1',
                'created_at' => '2025-03-04 04:59:56',
                'updated_at' => '2025-03-04 05:01:16',
                'user_create' => '',
                'user_update' => 'msjit',
            ]
        );
    }
}
