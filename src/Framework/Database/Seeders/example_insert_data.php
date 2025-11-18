<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class example_insert_data extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // delete data
        DB::table('example_data_by_rule')->delete();
        DB::table('example_standard')->delete();
        DB::table('example_form')->delete();
        DB::table('example_data')->delete();
        // insert data
        DB::table('example_standard')->insert(
            [
                'idjenis' => 'AA',
                'nama' => 'JENIS AA',
                'image' => 'example_standard/eV3eIAMQtnnJwtAvOfJPnQA8Pq9t1iPmXb1pkqhn.png',
                'isactive' => '1',
                'created_at' => '2025-03-04 02:38:34',
                'updated_at' => '2025-03-04 02:38:34',
                'user_create' => 'msjit',
                'user_update' => '',
            ]
        );
        DB::table('example_standard')->insert(
            [
                'idjenis' => 'BB',
                'nama' => 'JENIS BB',
                'image' => 'example_standard/GiTOadY71k2ROk01nLaxbMDMTneIHC9BZw10hYqB.png',
                'isactive' => '1',
                'created_at' => '2025-03-04 02:39:11',
                'updated_at' => '2025-03-04 02:39:11',
                'user_create' => 'msjit',
                'user_update' => '',
            ]
        );
        DB::table('example_standard')->insert(
            [
                'idjenis' => 'CC',
                'nama' => 'JENIS CC',
                'image' => 'example_standard/Dyj5JbSlrKym50jN6c3fWoe2jCVtxOqUOyBVfiOz.png',
                'isactive' => '1',
                'created_at' => '2025-03-04 02:39:27',
                'updated_at' => '2025-03-04 02:39:27',
                'user_create' => 'msjit',
                'user_update' => '',
            ]
        );
        DB::table('example_form')->insert(
            [
                'id' => '1',
                'upper' => 'HURUF BESAR',
                'lower' => 'huruf kecil',
                'notspace' => 'tanpaspasi',
                'readonly' => 'readonly',
                'char' => 'char',
                'currency' => '35000',
                'date' => '2025-03-04',
                'email' => 'faiz@gmail.com',
                'enum' => 'master',
                'file' => 'example_form/D4VSDlV0z098XE9QIPmsechgIM7L0wyezrT9hppR.pdf',
                'image' => 'example_form/t40QfzewoUMudqmBZGoOYljWO80jrnoq1MFX2CJq.png',
                'number' => '5',
                'search' => 'msjit',
                'string' => 'string',
                'text' => 'text',
                'customs' => 'standr',
                'multiples' => 'master, standr',
                'isactive' => '1',
                'created_at' => '2025-03-04 01:47:59',
                'updated_at' => '2025-03-04 01:48:50',
                'user_create' => 'msjit',
                'user_update' => 'msjit',
            ]
        );
        DB::table('example_form')->insert(
            [
                'id' => '2',
                'upper' => 'BESAR SEMUA',
                'lower' => 'kecil semua',
                'notspace' => 'spasikosong',
                'readonly' => 'readonly',
                'char' => 'char',
                'currency' => '45000',
                'date' => '2025-03-04',
                'email' => 'faiz.m@spunindo.com',
                'enum' => 'report',
                'file' => 'example_form/W3rItwZLbs7LjT8Lihn02jwBNHkfExO3iPtzoGgy.pdf',
                'image' => 'example_form/sQ3WMKlJuXTq67M84dfjg7D2GmIptv2ygL3xnun2.jpg',
                'number' => '455',
                'search' => 'msjit',
                'string' => 'string',
                'text' => 'ok text',
                'customs' => 'manual',
                'multiples' => 'manual, master, standr',
                'isactive' => '1',
                'created_at' => '2025-03-04 02:00:35',
                'updated_at' => '2025-03-04 02:00:35',
                'user_create' => 'msjit',
                'user_update' => '',
            ]
        );
        DB::table('example_data')->insert(
            [
                'idobat' => 'AA001',
                'jenis' => 'AA',
                'nama' => 'OBAT 1',
                'kemasan' => 'box',
                'harga' => '34000',
                'image' => 'example_data/0VzIOri9fxkIxQOE7shg3yahYDXNVUmE5avTP104.jpg',
                'expired' => '2025-03-04',
                'min_stock' => '2',
                'stock' => '6',
                'rules' => 'admins',
                'isactive' => '1',
                'created_at' => '2025-03-04 03:23:10',
                'updated_at' => '2025-03-04 03:23:10',
                'user_create' => 'msjit',
                'user_update' => '',
            ]
        );
        DB::table('example_data')->insert(
            [
                'idobat' => 'AA002',
                'jenis' => 'AA',
                'nama' => 'OBAT 4',
                'kemasan' => 'Sachet',
                'harga' => '44000',
                'image' => 'example_data/khOGNiP9LPTvvgeNFYwBKXVd16BzfPli4Y0IjxVn.png',
                'expired' => '2025-07-10',
                'min_stock' => '3',
                'stock' => '10',
                'rules' => 'admins',
                'isactive' => '1',
                'created_at' => '2025-03-04 03:26:33',
                'updated_at' => '2025-03-04 03:26:33',
                'user_create' => 'msjit',
                'user_update' => '',
            ]
        );
        DB::table('example_data')->insert(
            [
                'idobat' => 'BB001',
                'jenis' => 'BB',
                'nama' => 'OBAT 2',
                'kemasan' => 'Sachet/Box',
                'harga' => '5000',
                'image' => 'example_data/5dh2OitCD2m3hKG2rBvE8v9otH13fTAEuBP3DEng.jpg',
                'expired' => '2025-09-05',
                'min_stock' => '5',
                'stock' => '3',
                'rules' => 'admins',
                'isactive' => '1',
                'created_at' => '2025-03-04 03:24:16',
                'updated_at' => '2025-03-04 03:24:16',
                'user_create' => 'msjit',
                'user_update' => '',
            ]
        );
        DB::table('example_data')->insert(
            [
                'idobat' => 'CC001',
                'jenis' => 'CC',
                'nama' => 'OBAT 3',
                'kemasan' => 'box',
                'harga' => '20000',
                'image' => 'example_data/MzZqDYJZFyt2qdtYrXUVLtiKHw9BLEMMKlHBVX93.png',
                'expired' => '2025-08-01',
                'min_stock' => '4',
                'stock' => '50',
                'rules' => 'admins',
                'isactive' => '1',
                'created_at' => '2025-03-04 03:25:25',
                'updated_at' => '2025-03-04 03:25:25',
                'user_create' => 'msjit',
                'user_update' => '',
            ]
        );
        DB::table('example_data_by_rule')->insert(
            [
                'id' => '1',
                'nama' => 'NAMA 1',
                'rules' => 'admins',
                'isactive' => '1',
                'created_at' => '2025-03-11 01:00:30',
                'updated_at' => '2025-03-11 16:02:12',
                'user_create' => 'msjit',
                'user_update' => 'msjit',
            ]
        );
        DB::table('example_data_by_rule')->insert(
            [
                'id' => '2',
                'nama' => 'NAMA 2',
                'rules' => 'admins',
                'isactive' => '1',
                'created_at' => '2025-03-11 01:01:02',
                'updated_at' => '2025-03-11 16:00:15',
                'user_create' => 'msjit',
                'user_update' => 'msjit',
            ]
        );
        DB::table('example_data_by_rule')->insert(
            [
                'id' => '3',
                'nama' => 'NAMA 3',
                'rules' => 'admins',
                'isactive' => '1',
                'created_at' => '2025-03-11 01:04:29',
                'updated_at' => '2025-03-11 16:02:41',
                'user_create' => 'msjit',
                'user_update' => 'msjit',
            ]
        );
    }
}
