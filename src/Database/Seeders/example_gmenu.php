<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class example_gmenu extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // delete sys_auth
        DB::table('sys_auth')->where(['idroles' => 'admins', 'gmenu' => 'exampl', 'dmenu' => 'msfrmd'])->delete();
        DB::table('sys_auth')->where(['idroles' => 'admins', 'gmenu' => 'exampl', 'dmenu' => 'msfrmm'])->delete();
        DB::table('sys_auth')->where(['idroles' => 'admins', 'gmenu' => 'exampl', 'dmenu' => 'msfrms'])->delete();
        DB::table('sys_auth')->where(['idroles' => 'admins', 'gmenu' => 'exampl', 'dmenu' => 'rpexam'])->delete();
        DB::table('sys_auth')->where(['idroles' => 'admins', 'gmenu' => 'exampl', 'dmenu' => 'mssubl'])->delete();
        DB::table('sys_auth')->where(['idroles' => 'admins', 'gmenu' => 'exampl', 'dmenu' => 'msdata'])->delete();
        DB::table('sys_auth')->where(['idroles' => 'admins', 'gmenu' => 'exampl', 'dmenu' => 'msdarl'])->delete();
        // delete sys_gmenu
        DB::table('sys_dmenu')->where(['gmenu' => 'exampl', 'dmenu' => 'msfrmd'])->delete();
        DB::table('sys_dmenu')->where(['gmenu' => 'exampl', 'dmenu' => 'msfrmm'])->delete();
        DB::table('sys_dmenu')->where(['gmenu' => 'exampl', 'dmenu' => 'msfrms'])->delete();
        DB::table('sys_dmenu')->where(['gmenu' => 'exampl', 'dmenu' => 'rpexam'])->delete();
        DB::table('sys_dmenu')->where(['gmenu' => 'exampl', 'dmenu' => 'mssubl'])->delete();
        DB::table('sys_dmenu')->where(['gmenu' => 'exampl', 'dmenu' => 'msdata'])->delete();
        DB::table('sys_dmenu')->where(['gmenu' => 'exampl', 'dmenu' => 'msdarl'])->delete();
        // delete sys_gmenu
        DB::table('sys_gmenu')->where(['gmenu' => 'exampl'])->delete();

        // insert tabel sys_gmenu
        DB::table('sys_gmenu')->insert([
            'gmenu' => 'exampl',
            'urut' => 6,
            'name' => 'Example',
            'icon' => 'ni-air-baloon',
        ]);
        // insert tabel sys_dmenu
        DB::table('sys_dmenu')->insert([
            'gmenu' => 'exampl',
            'dmenu' => 'msfrmd',
            'urut' => 1,
            'name' => 'Layout - Standard',
            'url' => 'msfrmd',
            'icon' => 'ni-single-copy-04',
            'tabel' => 'example_standard',
            'layout' => 'standr',
            // setting sublink
            'sub' => 'sbexam',
        ]);
        DB::table('sys_dmenu')->insert([
            'gmenu' => 'exampl',
            'dmenu' => 'msfrmm',
            'urut' => 2,
            'name' => 'Layout - Master',
            'url' => 'msfrmm',
            'icon' => 'ni-single-copy-04',
            'tabel' => 'example_form',
            'layout' => 'master',
        ]);
        DB::table('sys_dmenu')->insert([
            'gmenu' => 'exampl',
            'dmenu' => 'msfrms',
            'urut' => 3,
            'name' => 'Layout - System',
            'url' => 'msfrms',
            'icon' => 'ni-single-copy-04',
            'tabel' => 'sys_auth',
            'layout' => 'system',
        ]);
        DB::table('sys_dmenu')->insert([
            'gmenu' => 'exampl',
            'dmenu' => 'rpexam',
            'urut' => 4,
            'name' => 'Layout - Report',
            'url' => 'rpexam',
            'icon' => 'ni-single-copy-04',
            'tabel' => '-',
            'layout' => 'report',
        ]);
        DB::table('sys_dmenu')->insert([
            'gmenu' => 'exampl',
            'dmenu' => 'mssubl',
            'urut' => 5,
            'name' => 'Layout - SubLink',
            'url' => 'mssubl',
            'icon' => 'ni-single-copy-04',
            'tabel' => 'example_standard',
            'layout' => 'standr',
        ]);
        DB::table('sys_dmenu')->insert([
            'gmenu' => 'exampl',
            'dmenu' => 'msdata',
            'urut' => 6,
            'name' => 'List Data',
            'url' => 'msdata',
            'icon' => 'ni-single-copy-04',
            'tabel' => 'example_data',
            'notif' => "select count(*) as 'notif' from example_data",
            'where' => "where isactive = '1'",
            'layout' => 'master',
            // setting sublink
            'sub' => 'sbexam',
        ]);
        // Menu Sublink
        DB::table('sys_dmenu')->insert([
            'gmenu' => 'exampl',
            'dmenu' => 'sbexam',
            'urut' => 7,
            'name' => 'Contoh Sublink',
            'url' => 'sbexam',
            'icon' => 'ni-single-copy-04',
            'tabel' => '-',
            'show' => '0',
            'layout' => 'sublnk',
        ]);
        DB::table('sys_dmenu')->insert([
            'gmenu' => 'exampl',
            'dmenu' => 'msdarl',
            'urut' => 8,
            'name' => 'Data By Rule',
            'url' => 'msdarl',
            'icon' => 'ni-single-copy-04',
            'tabel' => 'example_data_by_rule',
            'notif' => "select count(*) as 'notif' from example_data_by_rule",
            'where' => '',
            'layout' => 'standr',
        ]);

        // insert tabel sys_auth
        DB::table('sys_auth')->insert([
            'idroles' => 'admins',
            'gmenu' => 'exampl',
            'dmenu' => 'msfrmd',
            'add' => '1',
            'edit' => '1',
            'delete' => '1',
        ]);
        DB::table('sys_auth')->insert([
            'idroles' => 'admins',
            'gmenu' => 'exampl',
            'dmenu' => 'msfrmm',
            'add' => '1',
            'edit' => '1',
            'delete' => '1',
        ]);
        DB::table('sys_auth')->insert([
            'idroles' => 'admins',
            'gmenu' => 'exampl',
            'dmenu' => 'msfrms',
            'add' => '1',
            'edit' => '1',
            'delete' => '1',
        ]);
        DB::table('sys_auth')->insert([
            'idroles' => 'admins',
            'gmenu' => 'exampl',
            'dmenu' => 'rpexam',
            'add' => '1',
            'edit' => '0',
            'delete' => '0',
        ]);
        DB::table('sys_auth')->insert([
            'idroles' => 'admins',
            'gmenu' => 'exampl',
            'dmenu' => 'mssubl',
            'add' => '1',
            'edit' => '1',
            'delete' => '1',
        ]);
        DB::table('sys_auth')->insert([
            'idroles' => 'admins',
            'gmenu' => 'exampl',
            'dmenu' => 'msdata',
            'add' => '1',
            'edit' => '1',
            'delete' => '1',
        ]);
        DB::table('sys_auth')->insert([
            'idroles' => 'admins',
            'gmenu' => 'exampl',
            'dmenu' => 'sbexam',
            'add' => '1',
            'edit' => '1',
            'delete' => '1',
        ]);
        DB::table('sys_auth')->insert([
            'idroles' => 'admins',
            'gmenu' => 'exampl',
            'dmenu' => 'msdarl',
            'add' => '1',
            'edit' => '1',
            'delete' => '1',
            'rules' => '1',
        ]);
    }
}
