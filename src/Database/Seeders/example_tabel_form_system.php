<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class example_tabel_form_system extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('sys_table')->where(['gmenu' => 'exampl', 'dmenu' => 'msfrms'])->delete();

        DB::table('sys_table')->insert([
            'gmenu' => 'exampl',
            'dmenu' => 'msfrms',
            'urut' => '1',
            'field' => 'idroles',
            'alias' => 'Rule',
            'type' => 'enum',
            'length' => '6',
            'decimals' => '0',
            'default' => '',
            'validate' => 'required|max:6|min:6|unique:sys_auth,idroles',
            'primary' => '1',
            'filter' => '1',
            'list' => '1',
            'show' => '1',
            'query' => "select idroles, name from sys_roles where isactive = '1'",
            'position' => '1',
        ]);

        DB::table('sys_table')->insert([
            'gmenu' => 'exampl',
            'dmenu' => 'msfrms',
            'urut' => '2',
            'field' => 'gmenu',
            'alias' => 'Nama Group Menu',
            'type' => 'enum',
            'length' => '6',
            'decimals' => '0',
            'default' => '',
            'validate' => 'required|max:6|min:6|unique:sys_auth,gmenu',
            'primary' => '1',
            'filter' => '1',
            'list' => '1',
            'show' => '1',
            'query' => "select gmenu, name from sys_gmenu where isactive = '1'",
            'position' => '2',
        ]);

        DB::table('sys_table')->insert([
            'gmenu' => 'exampl',
            'dmenu' => 'msfrms',
            'urut' => '3',
            'field' => 'dmenu',
            'alias' => 'Nama Menu',
            'type' => 'enum',
            'length' => '6',
            'decimals' => '0',
            'default' => '',
            'validate' => 'required|max:6|min:6|unique:sys_auth,dmenu',
            'primary' => '1',
            'filter' => '1',
            'list' => '1',
            'show' => '1',
            'sub' => 'gmenu',
            'query' => "select dmenu, name, gmenu from sys_dmenu where isactive = '1'",
            'position' => '2',
        ]);

        DB::table('sys_table')->insert([
            'gmenu' => 'exampl',
            'dmenu' => 'msfrms',
            'urut' => '4',
            'field' => 'add',
            'alias' => 'Akses Tambah',
            'type' => 'enum',
            'length' => '1',
            'decimals' => '0',
            'default' => '',
            'validate' => 'required',
            'primary' => '0',
            'filter' => '1',
            'list' => '1',
            'show' => '1',
            'query' => "select value, name from sys_enum where idenum = 'questions' and isactive = '1'",
            'position' => '2',
        ]);

        DB::table('sys_table')->insert([
            'gmenu' => 'exampl',
            'dmenu' => 'msfrms',
            'urut' => '5',
            'field' => 'edit',
            'alias' => 'Akses Edit',
            'type' => 'enum',
            'length' => '1',
            'decimals' => '0',
            'default' => '',
            'validate' => 'required',
            'primary' => '0',
            'filter' => '1',
            'list' => '1',
            'show' => '1',
            'query' => "select value, name from sys_enum where idenum = 'questions' and isactive = '1'",
            'position' => '2',
        ]);

        DB::table('sys_table')->insert([
            'gmenu' => 'exampl',
            'dmenu' => 'msfrms',
            'urut' => '6',
            'field' => 'delete',
            'alias' => 'Akses Hapus',
            'type' => 'enum',
            'length' => '1',
            'decimals' => '0',
            'default' => '',
            'validate' => 'required',
            'primary' => '0',
            'filter' => '1',
            'list' => '1',
            'show' => '1',
            'query' => "select value, name from sys_enum where idenum = 'questions' and isactive = '1'",
            'position' => '2',
        ]);

        DB::table('sys_table')->insert([
            'gmenu' => 'exampl',
            'dmenu' => 'msfrms',
            'urut' => '7',
            'field' => 'approval',
            'alias' => 'Akses Approve',
            'type' => 'enum',
            'length' => '1',
            'decimals' => '0',
            'default' => '',
            'validate' => 'required',
            'primary' => '0',
            'filter' => '1',
            'list' => '1',
            'show' => '1',
            'query' => "select value, name from sys_enum where idenum = 'questions' and isactive = '1'",
            'position' => '2',
        ]);

        DB::table('sys_table')->insert([
            'gmenu' => 'exampl',
            'dmenu' => 'msfrms',
            'urut' => '8',
            'field' => 'print',
            'alias' => 'Akses Print',
            'type' => 'enum',
            'length' => '1',
            'decimals' => '0',
            'default' => '',
            'validate' => 'required',
            'primary' => '0',
            'filter' => '1',
            'list' => '1',
            'show' => '1',
            'query' => "select value, name from sys_enum where idenum = 'questions' and isactive = '1'",
            'position' => '2',
        ]);

        DB::table('sys_table')->insert([
            'gmenu' => 'exampl',
            'dmenu' => 'msfrms',
            'urut' => '9',
            'field' => 'excel',
            'alias' => 'Akses Excel',
            'type' => 'enum',
            'length' => '1',
            'decimals' => '0',
            'default' => '',
            'validate' => 'required',
            'primary' => '0',
            'filter' => '1',
            'list' => '1',
            'show' => '1',
            'query' => "select value, name from sys_enum where idenum = 'questions' and isactive = '1'",
            'position' => '2',
        ]);

        DB::table('sys_table')->insert([
            'gmenu' => 'exampl',
            'dmenu' => 'msfrms',
            'urut' => '10',
            'field' => 'pdf',
            'alias' => 'Akses PDF',
            'type' => 'enum',
            'length' => '1',
            'decimals' => '0',
            'default' => '',
            'validate' => 'required',
            'primary' => '0',
            'filter' => '1',
            'list' => '1',
            'show' => '1',
            'query' => "select value, name from sys_enum where idenum = 'questions' and isactive = '1'",
            'position' => '2',
        ]);

        DB::table('sys_table')->insert([
            'gmenu' => 'exampl',
            'dmenu' => 'msfrms',
            'urut' => '11',
            'field' => 'value',
            'alias' => 'Akses Value',
            'type' => 'enum',
            'length' => '1',
            'decimals' => '0',
            'default' => '',
            'validate' => 'required',
            'primary' => '0',
            'filter' => '1',
            'list' => '1',
            'show' => '1',
            'query' => "select value, name from sys_enum where idenum = 'questions' and isactive = '1'",
            'position' => '2',
        ]);

        DB::table('sys_table')->insert([
            'gmenu' => 'exampl',
            'dmenu' => 'msfrms',
            'urut' => '12',
            'field' => 'rules',
            'alias' => 'Akses Rules',
            'type' => 'enum',
            'length' => '1',
            'decimals' => '0',
            'default' => '',
            'validate' => 'required',
            'primary' => '0',
            'filter' => '1',
            'list' => '1',
            'show' => '1',
            'query' => "select value, name from sys_enum where idenum = 'questions' and isactive = '1'",
            'position' => '2',
        ]);

        DB::table('sys_table')->insert([
            'gmenu' => 'exampl',
            'dmenu' => 'msfrms',
            'urut' => '13',
            'field' => 'isactive',
            'alias' => 'Status',
            'type' => 'enum',
            'length' => '1',
            'decimals' => '0',
            'default' => '1',
            'validate' => '',
            'primary' => '0',
            'filter' => '1',
            'list' => '1',
            'show' => '0',
            'query' => "select value, name from sys_enum where idenum = 'isactive' and isactive = '1'",
        ]);
    }
}
