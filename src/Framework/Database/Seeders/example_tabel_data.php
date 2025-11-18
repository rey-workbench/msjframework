<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class example_tabel_data extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('sys_table')->where(['gmenu' => 'exampl', 'dmenu' => 'msdata'])->delete();

        DB::table('sys_table')->insert([
            'gmenu' => 'exampl',
            'dmenu' => 'msdata',
            'urut' => '1',
            'field' => 'idobat',
            'alias' => 'Kode Obat',
            'type' => 'hidden',
            'length' => '6',
            'decimals' => '0',
            'default' => '',
            'validate' => 'required|max:6|min:6|unique:example_data,idobat',
            'primary' => '1',
            'filter' => '1',
            'list' => '1',
            'show' => '1',
            'query' => '',
        ]);

        DB::table('sys_table')->insert([
            'gmenu' => 'exampl',
            'dmenu' => 'msdata',
            'urut' => '2',
            'field' => 'jenis',
            'alias' => 'Jenis Obat',
            'type' => 'enum',
            'length' => '2',
            'decimals' => '0',
            'default' => '',
            'validate' => 'required|max:2|min:2',
            'primary' => '0',
            'filter' => '1',
            'list' => '1',
            'show' => '1',
            'query' => "select idjenis, nama from example_standard where isactive = '1'",
            // setting 1 hanya jika di kaitkan ke sublink
            'position' => '1',
        ]);

        DB::table('sys_table')->insert([
            'gmenu' => 'exampl',
            'dmenu' => 'msdata',
            'urut' => '3',
            'field' => 'jenis',
            'alias' => 'Warna',
            'type' => 'join',
            'length' => '0',
            'decimals' => '0',
            'default' => 'image',
            'validate' => '',
            'primary' => '0',
            'filter' => '0',
            'list' => '1',
            'show' => '0',
            'query' => "select image from example_standard where isactive = '1' and idjenis = ",
            // setting 2 hanya jika di kaitkan ke sublink dan sebagai detail data yg tampil
            'position' => '2',
        ]);

        DB::table('sys_table')->insert([
            'gmenu' => 'exampl',
            'dmenu' => 'msdata',
            'urut' => '4',
            'field' => 'nama',
            'alias' => 'Nama Obat',
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
            'class' => 'upper',
            // setting 2 hanya jika di kaitkan ke sublink dan sebagai detail data yg tampil
            'position' => '2',
        ]);

        DB::table('sys_table')->insert([
            'gmenu' => 'exampl',
            'dmenu' => 'msdata',
            'urut' => '5',
            'field' => 'kemasan',
            'alias' => 'Kemasan Obat',
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
            'gmenu' => 'exampl',
            'dmenu' => 'msdata',
            'urut' => '6',
            'field' => 'harga',
            'alias' => 'Harga Obat',
            'type' => 'currency',
            'length' => '9999999999',
            'decimals' => '2',
            'default' => '',
            'validate' => 'required|numeric|between:1,9999999999',
            'primary' => '0',
            'filter' => '1',
            'list' => '1',
            'show' => '1',
            'sub' => 'IDR ',
        ]);

        DB::table('sys_table')->insert([
            'gmenu' => 'exampl',
            'dmenu' => 'msdata',
            'urut' => '7',
            'field' => 'image',
            'alias' => 'Foto Obat',
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
            'dmenu' => 'msdata',
            'urut' => '8',
            'field' => 'expired',
            'alias' => 'Expired',
            'type' => 'date',
            'length' => '30',
            'decimals' => '0',
            'default' => '',
            'validate' => 'required',
            'primary' => '0',
            'filter' => '1',
            'list' => '1',
            'show' => '1',
            'query' => '',
            'class' => 'check-date',
        ]);

        DB::table('sys_table')->insert([
            'gmenu' => 'exampl',
            'dmenu' => 'msdata',
            'urut' => '9',
            'field' => 'min_stock',
            'alias' => 'Min Stock',
            'type' => 'number',
            'length' => '10',
            'decimals' => '0',
            'default' => '0',
            'validate' => 'nullable',
            'primary' => '0',
            'filter' => '1',
            'list' => '1',
            'show' => '1',
            'query' => '',
        ]);

        DB::table('sys_table')->insert([
            'gmenu' => 'exampl',
            'dmenu' => 'msdata',
            'urut' => '10',
            'field' => 'stock',
            'alias' => 'Stock',
            'type' => 'number',
            'length' => '10',
            'decimals' => '0',
            'default' => '0',
            'validate' => 'nullable',
            'primary' => '0',
            'filter' => '1',
            'list' => '1',
            'show' => '1',
            'query' => '',
            'class' => 'check-stock',
            'sub' => 'min_stock',
        ]);

        DB::table('sys_table')->insert([
            'gmenu' => 'exampl',
            'dmenu' => 'msdata',
            'urut' => '11',
            'field' => 'rules',
            'alias' => 'Rules',
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
        ]);

        DB::table('sys_table')->insert([
            'gmenu' => 'exampl',
            'dmenu' => 'msdata',
            'urut' => '12',
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
