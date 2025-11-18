<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class tabel_sys_counter extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('sys_table')->where(['gmenu' => 'system', 'dmenu' => 'syscnt'])->delete();

        DB::table('sys_table')->insert([
            'gmenu' => 'system',
            'dmenu' => 'syscnt',
            'urut' => '1',
            'field' => 'character',
            'alias' => 'Character',
            'type' => 'string',
            'length' => '255',
            'decimals' => '0',
            'default' => '',
            'validate' => 'required|max:255|min:1|unique:sys_counter,character',
            'primary' => '1',
            'filter' => '1',
            'list' => '1',
            'show' => '1',
            'query' => '',
        ]);

        DB::table('sys_table')->insert([
            'gmenu' => 'system',
            'dmenu' => 'syscnt',
            'urut' => '2',
            'field' => 'counter',
            'alias' => 'Counter',
            'type' => 'number',
            'length' => '100000',
            'decimals' => '0',
            'default' => '',
            'validate' => 'required|max:100000|min:1',
            'primary' => '0',
            'filter' => '1',
            'list' => '1',
            'show' => '1',
            'query' => '',
        ]);

        DB::table('sys_table')->insert([
            'gmenu' => 'system',
            'dmenu' => 'syscnt',
            'urut' => '3',
            'field' => 'lastid',
            'alias' => 'ID Terakhi',
            'type' => 'string',
            'length' => '255',
            'decimals' => '0',
            'default' => '',
            'validate' => 'required|max:255|min:1',
            'primary' => '0',
            'filter' => '1',
            'list' => '1',
            'show' => '1',
            'query' => '',
        ]);

        DB::table('sys_table')->insert([
            'gmenu' => 'system',
            'dmenu' => 'syscnt',
            'urut' => '4',
            'field' => 'isactive',
            'alias' => 'Status',
            'type' => 'enum',
            'length' => '1',
            'decimals' => '0',
            'default' => '',
            'validate' => '',
            'primary' => '0',
            'filter' => '1',
            'list' => '1',
            'show' => '0',
            'query' => "select value, name from sys_enum where idenum = 'isactive' and isactive = '1'",
        ]);
    }
}
