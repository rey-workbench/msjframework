<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class tabel_sys_role extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('sys_table')->where(['gmenu' => 'system', 'dmenu' => 'rolesx'])->delete();

        DB::table('sys_table')->insert([
            'gmenu' => 'system',
            'dmenu' => 'rolesx',
            'urut' => '1',
            'field' => 'idroles',
            'alias' => 'ID Roles',
            'type' => 'char',
            'length' => '6',
            'decimals' => '0',
            'default' => '',
            'validate' => 'required|max:6|min:6|unique:sys_roles,idroles',
            'primary' => '1',
            'filter' => '1',
            'list' => '1',
            'show' => '1',
            'query' => '',
            'class' => 'lower',
        ]);

        DB::table('sys_table')->insert([
            'gmenu' => 'system',
            'dmenu' => 'rolesx',
            'urut' => '2',
            'field' => 'name',
            'alias' => 'Nama',
            'type' => 'string',
            'length' => '20',
            'decimals' => '0',
            'default' => '',
            'validate' => 'required|max:20|min:2',
            'primary' => '0',
            'filter' => '1',
            'list' => '1',
            'show' => '1',
            'query' => '',
        ]);

        DB::table('sys_table')->insert([
            'gmenu' => 'system',
            'dmenu' => 'rolesx',
            'urut' => '3',
            'field' => 'description',
            'alias' => 'Deskripsi',
            'type' => 'string',
            'length' => '100',
            'decimals' => '0',
            'default' => '',
            'validate' => 'required|max:100|min:2',
            'primary' => '0',
            'filter' => '1',
            'list' => '1',
            'show' => '1',
            'query' => '',
        ]);

        DB::table('sys_table')->insert([
            'gmenu' => 'system',
            'dmenu' => 'rolesx',
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
