<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class tabel_sys_app extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('sys_table')->where(['gmenu' => 'system', 'dmenu' => 'setupx'])->delete();

        DB::table('sys_table')->insert([
            'gmenu' => 'system',
            'dmenu' => 'setupx',
            'urut' => '1',
            'field' => 'appid',
            'alias' => 'ID Aplikasi',
            'type' => 'string',
            'length' => '20',
            'decimals' => '0',
            'default' => '',
            'validate' => 'required|max:20|min:2|unique:sys_app,appid',
            'primary' => '1',
            'filter' => '1',
            'list' => '1',
            'show' => '1',
            'query' => '',
        ]);

        DB::table('sys_table')->insert([
            'gmenu' => 'system',
            'dmenu' => 'setupx',
            'urut' => '2',
            'field' => 'appname',
            'alias' => 'Nama Aplikasi',
            'type' => 'string',
            'length' => '50',
            'decimals' => '0',
            'default' => '',
            'validate' => 'required|max:50|min:2',
            'primary' => '0',
            'filter' => '1',
            'list' => '1',
            'show' => '1',
            'query' => '',
        ]);

        DB::table('sys_table')->insert([
            'gmenu' => 'system',
            'dmenu' => 'setupx',
            'urut' => '3',
            'field' => 'description',
            'alias' => 'Deskripsi',
            'type' => 'text',
            'length' => '255',
            'decimals' => '0',
            'default' => '',
            'validate' => 'required',
            'primary' => '0',
            'filter' => '1',
            'list' => '1',
            'show' => '1',
            'query' => '',
        ]);

        DB::table('sys_table')->insert([
            'gmenu' => 'system',
            'dmenu' => 'setupx',
            'urut' => '4',
            'field' => 'company',
            'alias' => 'Nama Perusahaan',
            'type' => 'string',
            'length' => '100',
            'decimals' => '0',
            'default' => '',
            'validate' => 'required',
            'primary' => '0',
            'filter' => '1',
            'list' => '1',
            'show' => '1',
            'query' => '',
            'class' => 'upper',
        ]);

        DB::table('sys_table')->insert([
            'gmenu' => 'system',
            'dmenu' => 'setupx',
            'urut' => '5',
            'field' => 'address',
            'alias' => 'Alamat',
            'type' => 'text',
            'length' => '255',
            'decimals' => '0',
            'default' => '',
            'validate' => 'required',
            'primary' => '0',
            'filter' => '1',
            'list' => '1',
            'show' => '1',
            'query' => '',
        ]);

        DB::table('sys_table')->insert([
            'gmenu' => 'system',
            'dmenu' => 'setupx',
            'urut' => '6',
            'field' => 'city',
            'alias' => 'Kota',
            'type' => 'string',
            'length' => '50',
            'decimals' => '0',
            'default' => '',
            'validate' => 'required',
            'primary' => '0',
            'filter' => '1',
            'list' => '1',
            'show' => '1',
            'query' => '',
            'class' => 'upper',
        ]);

        DB::table('sys_table')->insert([
            'gmenu' => 'system',
            'dmenu' => 'setupx',
            'urut' => '7',
            'field' => 'province',
            'alias' => 'Provinsi',
            'type' => 'string',
            'length' => '50',
            'decimals' => '0',
            'default' => '',
            'validate' => 'required',
            'primary' => '0',
            'filter' => '1',
            'list' => '1',
            'show' => '1',
            'query' => '',
            'class' => 'upper',
        ]);

        DB::table('sys_table')->insert([
            'gmenu' => 'system',
            'dmenu' => 'setupx',
            'urut' => '8',
            'field' => 'country',
            'alias' => 'Negara',
            'type' => 'string',
            'length' => '50',
            'decimals' => '0',
            'default' => '',
            'validate' => 'required',
            'primary' => '0',
            'filter' => '1',
            'list' => '1',
            'show' => '1',
            'query' => '',
            'class' => 'upper',
        ]);

        DB::table('sys_table')->insert([
            'gmenu' => 'system',
            'dmenu' => 'setupx',
            'urut' => '9',
            'field' => 'telephone',
            'alias' => 'Telpon',
            'type' => 'string',
            'length' => '50',
            'decimals' => '0',
            'default' => '',
            'validate' => 'required',
            'primary' => '0',
            'filter' => '1',
            'list' => '1',
            'show' => '1',
            'query' => '',
        ]);

        DB::table('sys_table')->insert([
            'gmenu' => 'system',
            'dmenu' => 'setupx',
            'urut' => '10',
            'field' => 'fax',
            'alias' => 'Fax',
            'type' => 'string',
            'length' => '50',
            'decimals' => '0',
            'default' => '',
            'validate' => 'required',
            'primary' => '0',
            'filter' => '1',
            'list' => '1',
            'show' => '1',
            'query' => '',
        ]);

        DB::table('sys_table')->insert([
            'gmenu' => 'system',
            'dmenu' => 'setupx',
            'urut' => '11',
            'field' => 'logo_small',
            'alias' => 'Logo Kecil',
            'type' => 'image',
            'length' => '1024',
            'decimals' => '0',
            'default' => 'noimage.png',
            'validate' => 'mimes:png,PNG,jpg,JPG|file|max:1024',
            'primary' => '0',
            'filter' => '1',
            'list' => '1',
            'show' => '1',
            'query' => '',
            'note' => 'Format Image : jpg & png',
        ]);

        DB::table('sys_table')->insert([
            'gmenu' => 'system',
            'dmenu' => 'setupx',
            'urut' => '12',
            'field' => 'logo_large',
            'alias' => 'Logo Besar',
            'type' => 'image',
            'length' => '2048',
            'decimals' => '0',
            'default' => 'noimage.png',
            'validate' => 'mimes:png,PNG,jpg,JPG|file|max:2048',
            'primary' => '0',
            'filter' => '1',
            'list' => '1',
            'show' => '1',
            'query' => '',
            'note' => 'Format Image : jpg & png',
        ]);

        DB::table('sys_table')->insert([
            'gmenu' => 'system',
            'dmenu' => 'setupx',
            'urut' => '13',
            'field' => 'cover_out',
            'alias' => 'Cover Luar',
            'type' => 'image',
            'length' => '2048',
            'decimals' => '0',
            'default' => 'noimage.png',
            'validate' => 'mimes:png,PNG,jpg,JPG|file|max:2048',
            'primary' => '0',
            'filter' => '1',
            'list' => '1',
            'show' => '1',
            'query' => '',
            'note' => 'Format Image : jpg & png',
        ]);

        DB::table('sys_table')->insert([
            'gmenu' => 'system',
            'dmenu' => 'setupx',
            'urut' => '14',
            'field' => 'cover_in',
            'alias' => 'Cover Dalam',
            'type' => 'image',
            'length' => '2048',
            'decimals' => '0',
            'default' => 'noimage.png',
            'validate' => 'mimes:png,PNG,jpg,JPG|file|max:2048',
            'primary' => '0',
            'filter' => '1',
            'list' => '1',
            'show' => '1',
            'query' => '',
            'note' => 'Format Image : jpg & png',
        ]);

        DB::table('sys_table')->insert([
            'gmenu' => 'system',
            'dmenu' => 'setupx',
            'urut' => '15',
            'field' => 'icon',
            'alias' => 'Icon',
            'type' => 'image',
            'length' => '1048',
            'decimals' => '0',
            'default' => 'noimage.png',
            'validate' => 'mimes:png,PNG,ico,ICO|file|max:1048',
            'primary' => '0',
            'filter' => '1',
            'list' => '1',
            'show' => '1',
            'query' => '',
            'note' => 'Format Image : ico & png',
        ]);

        DB::table('sys_table')->insert([
            'gmenu' => 'system',
            'dmenu' => 'setupx',
            'urut' => '16',
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
