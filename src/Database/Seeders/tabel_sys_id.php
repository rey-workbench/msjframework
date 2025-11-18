<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class tabel_sys_id extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('sys_table')->where(['gmenu' => 'system', 'dmenu' => 'sysidx'])->delete();

        DB::table('sys_table')->insert([
            'gmenu' => 'system',
            'dmenu' => 'sysidx',
            'urut' => '1',
            'field' => 'dmenu',
            'alias' => 'ID Menu',
            'type' => 'enum',
            'length' => '6',
            'decimals' => '0',
            'default' => '',
            'validate' => 'required|max:6|min:6|unique:sys_id,dmenu',
            'primary' => '1',
            'filter' => '1',
            'list' => '1',
            'show' => '1',
            'query' => "select dmenu, name from sys_dmenu where isactive = '1'",
            'position' => '1',
        ]);

        DB::table('sys_table')->insert([
            'gmenu' => 'system',
            'dmenu' => 'sysidx',
            'urut' => '2',
            'field' => 'source',
            'alias' => 'Source',
            'type' => 'enum',
            'length' => '3',
            'decimals' => '0',
            'default' => '',
            'validate' => 'required|max:3|min:3|unique:sys_id,source',
            'primary' => '1',
            'filter' => '1',
            'list' => '1',
            'show' => '1',
            'query' => "select value, name from sys_enum where idenum = 'source' and isactive = '1'",
            'note' => 'Internal(pilih value pada menu tersebut), External(input string manual)',
            'position' => '2',
        ]);

        DB::table('sys_table')->insert([
            'gmenu' => 'system',
            'dmenu' => 'sysidx',
            'urut' => '3',
            'field' => 'internal',
            'alias' => 'Source Internal',
            'type' => 'enum',
            'length' => '255',
            'decimals' => '0',
            'default' => '-',
            'validate' => 'max:255|min:1|unique:sys_id,internal',
            'primary' => '1',
            'filter' => '1',
            'list' => '1',
            'show' => '1',
            'sub' => 'dmenu',
            'query' => "select field, alias, dmenu from sys_table where isactive = '1'",
            'note' => 'pilih source ixternal',
            'position' => '2',
        ]);

        DB::table('sys_table')->insert([
            'gmenu' => 'system',
            'dmenu' => 'sysidx',
            'urut' => '4',
            'field' => 'external',
            'alias' => 'Value External',
            'type' => 'string',
            'length' => '255',
            'decimals' => '0',
            'default' => '0',
            'validate' => 'max:255|min:1|unique:sys_id,external',
            'primary' => '1',
            'filter' => '1',
            'list' => '1',
            'show' => '1',
            'sub' => 'dmenu',
            'query' => '',
            'note' => 'Input text external',
            'position' => '2',
        ]);

        DB::table('sys_table')->insert([
            'gmenu' => 'system',
            'dmenu' => 'sysidx',
            'urut' => '5',
            'field' => 'urut',
            'alias' => 'Urut',
            'type' => 'number',
            'length' => '20',
            'decimals' => '0',
            'default' => '',
            'validate' => 'required|numeric|between:0,20',
            'primary' => '0',
            'filter' => '1',
            'list' => '1',
            'show' => '1',
            'query' => '',
            'note' => 'Nomor Urut Penomoran',
            'position' => '2',
        ]);

        DB::table('sys_table')->insert([
            'gmenu' => 'system',
            'dmenu' => 'sysidx',
            'urut' => '6',
            'field' => 'length',
            'alias' => 'Length',
            'type' => 'number',
            'length' => '20',
            'decimals' => '0',
            'default' => '',
            'validate' => 'required|numeric|between:0,20',
            'primary' => '0',
            'filter' => '1',
            'list' => '1',
            'show' => '1',
            'query' => '',
            'note' => 'Jumlah Digit String',
            'position' => '2',
        ]);

        DB::table('sys_table')->insert([
            'gmenu' => 'system',
            'dmenu' => 'sysidx',
            'urut' => '7',
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
