<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class tabel_sys_number extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('sys_table')->where(['gmenu' => 'system', 'dmenu' => 'number'])->delete();

        DB::table('sys_table')->insert([
            'gmenu' => 'system',
            'dmenu' => 'number',
            'urut' => '1',
            'field' => 'periode',
            'alias' => 'Periode',
            'type' => 'char',
            'length' => '4',
            'decimals' => '0',
            'default' => '',
            'validate' => 'required|max:4|min:4|unique:sys_number,periode',
            'primary' => '1',
            'filter' => '1',
            'list' => '1',
            'show' => '1',
            'query' => '',
        ]);

        DB::table('sys_table')->insert([
            'gmenu' => 'system',
            'dmenu' => 'number',
            'urut' => '2',
            'field' => 'tipe',
            'alias' => 'Tipe',
            'type' => 'char',
            'length' => '3',
            'decimals' => '0',
            'default' => '',
            'validate' => 'required|max:3|min:1|unique:sys_number,tipe',
            'primary' => '1',
            'filter' => '1',
            'list' => '1',
            'show' => '1',
            'query' => '',
            'class' => 'upper',
        ]);

        DB::table('sys_table')->insert([
            'gmenu' => 'system',
            'dmenu' => 'number',
            'urut' => '3',
            'field' => 'lastid',
            'alias' => 'ID Terakhi',
            'type' => 'string',
            'length' => '10',
            'decimals' => '0',
            'default' => '',
            'validate' => 'required|max:10|min:4',
            'primary' => '0',
            'filter' => '1',
            'list' => '1',
            'show' => '1',
            'query' => '',
        ]);

        DB::table('sys_table')->insert([
            'gmenu' => 'system',
            'dmenu' => 'number',
            'urut' => '4',
            'field' => 'lastx',
            'alias' => 'Urut Terakhir',
            'type' => 'number',
            'length' => '1000',
            'decimals' => '0',
            'default' => '',
            'validate' => 'required|max:1000|min:1',
            'primary' => '0',
            'filter' => '1',
            'list' => '1',
            'show' => '1',
            'query' => '',
        ]);

        DB::table('sys_table')->insert([
            'gmenu' => 'system',
            'dmenu' => 'number',
            'urut' => '5',
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
