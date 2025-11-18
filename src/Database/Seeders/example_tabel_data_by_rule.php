<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class example_tabel_data_by_rule extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('sys_table')->where(['gmenu' => 'exampl', 'dmenu' => 'msdarl'])->delete();

        DB::table('sys_table')->insert([
            'gmenu' => 'exampl',
            'dmenu' => 'msdarl',
            'urut' => '1',
            'field' => 'id',
            'alias' => 'ID',
            'type' => 'hidden',
            'length' => '0',
            'decimals' => '0',
            'default' => '',
            'validate' => 'nullable',
            'primary' => '1',
            'generateid' => '',
            'filter' => '1',
            'list' => '1',
            'show' => '1',
            'query' => '',
        ]);

        DB::table('sys_table')->insert([
            'gmenu' => 'exampl',
            'dmenu' => 'msdarl',
            'urut' => '2',
            'field' => 'nama',
            'alias' => 'Nama',
            'type' => 'string',
            'length' => '100',
            'decimals' => '0',
            'default' => '',
            'validate' => 'required|max:100|min:4',
            'primary' => '0',
            'filter' => '1',
            'list' => '1',
            'show' => '1',
            'query' => '',
            'class' => 'upper',
        ]);

        DB::table('sys_table')->insert([
            'gmenu' => 'exampl',
            'dmenu' => 'msdarl',
            'urut' => '4',
            'field' => 'rules',
            'alias' => 'ID Roles',
            'type' => 'enum',
            'length' => '6',
            'decimals' => '0',
            'default' => '',
            'validate' => 'required',
            'primary' => '0',
            'filter' => '1',
            'list' => '1',
            'show' => '1',
            'query' => "select idroles, name from sys_roles where isactive = '1'",
            'class' => 'select-multiple custom-select',
        ]);

        DB::table('sys_table')->insert([
            'gmenu' => 'exampl',
            'dmenu' => 'msdarl',
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
