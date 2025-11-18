<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class tabel_sys_dmenu extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('sys_table')->where(['gmenu' => 'system', 'dmenu' => 'dmenux'])->delete();

        DB::table('sys_table')->insert([
            'gmenu' => 'system',
            'dmenu' => 'dmenux',
            'urut' => '1',
            'field' => 'dmenu',
            'alias' => 'ID Menu',
            'type' => 'char',
            'length' => '6',
            'decimals' => '0',
            'default' => '',
            'validate' => 'required|max:6|min:6|unique:sys_dmenu,dmenu',
            'primary' => '1',
            'filter' => '1',
            'list' => '1',
            'show' => '1',
            'query' => '',
            'class' => 'lower',
            'note' => 'ID Menu',
        ]);

        DB::table('sys_table')->insert([
            'gmenu' => 'system',
            'dmenu' => 'dmenux',
            'urut' => '2',
            'field' => 'gmenu',
            'alias' => 'Group Menu',
            'type' => 'enum',
            'length' => '6',
            'decimals' => '0',
            'default' => '',
            'validate' => 'required|max:6|min:6',
            'primary' => '0',
            'filter' => '1',
            'list' => '1',
            'show' => '1',
            'query' => "select gmenu, name from sys_gmenu where isactive = '1'",
            'note' => 'Pilih Group Menu',
        ]);

        DB::table('sys_table')->insert([
            'gmenu' => 'system',
            'dmenu' => 'dmenux',
            'urut' => '3',
            'field' => 'urut',
            'alias' => 'Urut',
            'type' => 'number',
            'length' => '10',
            'decimals' => '0',
            'default' => '',
            'validate' => 'required|numeric|between:1,10',
            'primary' => '0',
            'filter' => '1',
            'list' => '1',
            'show' => '1',
            'query' => '',
            'note' => 'No. Urut Menu',
        ]);

        DB::table('sys_table')->insert([
            'gmenu' => 'system',
            'dmenu' => 'dmenux',
            'urut' => '4',
            'field' => 'name',
            'alias' => 'Nama',
            'type' => 'string',
            'length' => '25',
            'decimals' => '0',
            'default' => '',
            'validate' => 'required|max:25|min:2',
            'primary' => '0',
            'filter' => '1',
            'list' => '1',
            'show' => '1',
            'query' => '',
            'note' => 'Nama Menu',
        ]);

        DB::table('sys_table')->insert([
            'gmenu' => 'system',
            'dmenu' => 'dmenux',
            'urut' => '5',
            'field' => 'icon',
            'alias' => 'Icon',
            'type' => 'string',
            'length' => '50',
            'decimals' => '0',
            'default' => '',
            'validate' => 'required|max:50|min:5',
            'primary' => '0',
            'filter' => '1',
            'list' => '1',
            'show' => '1',
            'query' => '',
            'note' => 'Icon Menu',
        ]);

        DB::table('sys_table')->insert([
            'gmenu' => 'system',
            'dmenu' => 'dmenux',
            'urut' => '6',
            'field' => 'url',
            'alias' => 'URL',
            'type' => 'string',
            'length' => '50',
            'decimals' => '0',
            'default' => '',
            'validate' => 'required|max:50|min:4',
            'primary' => '0',
            'filter' => '1',
            'list' => '1',
            'show' => '1',
            'query' => '',
            'note' => 'Link Menu',
        ]);

        DB::table('sys_table')->insert([
            'gmenu' => 'system',
            'dmenu' => 'dmenux',
            'urut' => '7',
            'field' => 'tabel',
            'alias' => 'Tabel',
            'type' => 'string',
            'length' => '50',
            'decimals' => '0',
            'default' => '',
            'validate' => 'required|max:50|min:1',
            'primary' => '0',
            'filter' => '1',
            'list' => '1',
            'show' => '1',
            'query' => '',
            'note' => 'Nama Tabel Menu',
        ]);

        DB::table('sys_table')->insert([
            'gmenu' => 'system',
            'dmenu' => 'dmenux',
            'urut' => '8',
            'field' => 'where',
            'alias' => 'Where',
            'type' => 'text',
            'length' => '255',
            'decimals' => '0',
            'default' => '',
            'validate' => 'nullable|max:255',
            'primary' => '0',
            'filter' => '1',
            'list' => '0',
            'show' => '1',
            'query' => '',
            'note' => 'Digunakan Untuk Memfilter Data, Exp: Where status = 1',
        ]);

        DB::table('sys_table')->insert([
            'gmenu' => 'system',
            'dmenu' => 'dmenux',
            'urut' => '9',
            'field' => 'layout',
            'alias' => 'Layout',
            'type' => 'enum',
            'length' => '6',
            'decimals' => '0',
            'default' => '',
            'validate' => 'required|max:6|min:6',
            'primary' => '0',
            'filter' => '1',
            'list' => '1',
            'show' => '1',
            'query' => "select value, name from sys_enum where idenum = 'layout' and isactive = '1'",
            'note' => 'Pilih Manual Untuk Custom Manual',
        ]);

        DB::table('sys_table')->insert([
            'gmenu' => 'system',
            'dmenu' => 'dmenux',
            'urut' => '10',
            'field' => 'sub',
            'alias' => 'SubMenu',
            'type' => 'enum',
            'length' => '6',
            'decimals' => '0',
            'default' => '',
            'validate' => 'nullable|max:6|min:6',
            'primary' => '0',
            'filter' => '1',
            'list' => '1',
            'show' => '1',
            'query' => "select dmenu, name from sys_dmenu where isactive = '1'",
            'note' => 'Pilih Menu',
        ]);

        DB::table('sys_table')->insert([
            'gmenu' => 'system',
            'dmenu' => 'dmenux',
            'urut' => '11',
            'field' => 'show',
            'alias' => 'Show',
            'type' => 'enum',
            'length' => '1',
            'decimals' => '0',
            'default' => '1',
            'validate' => 'required|max:1|min:1',
            'primary' => '0',
            'filter' => '1',
            'list' => '1',
            'show' => '1',
            'query' => "select value, name from sys_enum where idenum = 'questions' and isactive = '1'",
            'note' => 'Menu Tampil Atau Tidak',
        ]);

        DB::table('sys_table')->insert([
            'gmenu' => 'system',
            'dmenu' => 'dmenux',
            'urut' => '12',
            'field' => 'js',
            'alias' => 'Javascript',
            'type' => 'enum',
            'length' => '1',
            'decimals' => '0',
            'default' => '1',
            'validate' => 'required|max:1|min:1',
            'primary' => '0',
            'filter' => '1',
            'list' => '1',
            'show' => '1',
            'query' => "select value, name from sys_enum where idenum = 'questions' and isactive = '1'",
            'note' => 'Menggunakan JS Tambahan Atau Tidak',
        ]);

        DB::table('sys_table')->insert([
            'gmenu' => 'system',
            'dmenu' => 'dmenux',
            'urut' => '13',
            'field' => 'notif',
            'alias' => 'Notifikasi(Query)',
            'type' => 'text',
            'length' => '9999',
            'decimals' => '0',
            'default' => '',
            'validate' => 'nullable',
            'primary' => '0',
            'filter' => '1',
            'list' => '0',
            'show' => '1',
            'query' => '',
            'note' => 'Query Ini Akan Muncul Di Samping List Menu',
        ]);

        DB::table('sys_table')->insert([
            'gmenu' => 'system',
            'dmenu' => 'dmenux',
            'urut' => '14',
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
