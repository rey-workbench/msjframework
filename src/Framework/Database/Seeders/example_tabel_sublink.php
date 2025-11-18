<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class example_tabel_sublink extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // delete data
        DB::table('sys_table')->where(['gmenu' => 'exampl', 'dmenu' => 'sbexam'])->delete();
        // insert data
        DB::table('sys_table')->insert([
            'gmenu' => 'exampl',
            'dmenu' => 'sbexam',
            'urut' => '1',
            'field' => 'query',
            'type' => 'report',
            'query' => "SELECT gmenu, dmenu, icon, tabel, name AS Detail FROM sys_dmenu WHERE sub = 'sbexam'",
            'position' => '1',
        ]);
    }
}
