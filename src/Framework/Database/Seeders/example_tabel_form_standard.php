<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class example_tabel_form_standard extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('sys_table')->where(['gmenu' => 'exampl', 'dmenu' => 'msfrmd'])->delete();

        DB::table('sys_table')->insert([
            'gmenu' => 'exampl',
            'dmenu' => 'msfrmd',
            'urut' => '1',
            'field' => 'idjenis',
            'alias' => 'ID Jenis',
            'type' => 'char',
            'length' => '2',
            'decimals' => '0',
            'default' => '',
            'validate' => 'required|max:2|min:2|unique:example_standard,idjenis',
            'primary' => '1',
            'generateid' => '',
            'filter' => '1',
            'list' => '1',
            'show' => '1',
            'query' => '',
            'class' => 'upper',
            // setting 1 hanya jika di kaitkan ke sublink dan sebagai primary key
            'position' => '1',
        ]);

        DB::table('sys_table')->insert([
            'gmenu' => 'exampl',
            'dmenu' => 'msfrmd',
            'urut' => '2',
            'field' => 'nama',
            'alias' => 'Nama Jenis',
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
            // setting 2 hanya jika di kaitkan ke sublink dan sebagai detail data yg tampil
            'position' => '2',
        ]);

        DB::table('sys_table')->insert([
            'gmenu' => 'exampl',
            'dmenu' => 'msfrmd',
            'urut' => '3',
            'field' => 'image',
            'alias' => 'Foto',
            'type' => 'image',
            'length' => '2048',
            'decimals' => '0',
            'default' => 'noimage.png',
            'validate' => 'mimes:png,PNG,jpg,JPG,jpeg,JPEG|file|max:2048',
            'primary' => '0',
            'filter' => '1',
            'list' => '1',
            'show' => '1',
            'query' => '',
            // setting 2 hanya jika di kaitkan ke sublink dan sebagai detail data yg tampil
            'position' => '2',
        ]);

        DB::table('sys_table')->insert([
            'gmenu' => 'exampl',
            'dmenu' => 'msfrmd',
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
            // setting 2 hanya jika di kaitkan ke sublink dan sebagai detail data yg tampil
            'position' => '2',
        ]);
    }
}
