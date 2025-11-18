<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class example_tabel_rpt_syslog extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('sys_table')->where(['gmenu' => 'exampl', 'dmenu' => 'rpexam'])->delete();

        DB::table('sys_table')->insert([
            'gmenu' => 'exampl',
            'dmenu' => 'rpexam',
            'urut' => '1',
            'field' => 'query',
            'type' => 'exampl',
            'query' => "SELECT A.CREATED_AT, A.DATE, A.USERNAME, C.firstname, A.TIPE, CASE A.tipe WHEN 'V' THEN 'View' WHEN 'C' THEN 'Create' WHEN 'U' THEN 'Update' WHEN 'D' THEN 'Delete' WHEN 'A' THEN 'Approve' WHEN 'L' THEN 'Login' WHEN 'E' THEN 'Error' END AS 'NTIPE', A.DMENU, IFNULL(B.NAME,A.DMENU) AS NAME, A.DESCRIPTION, A.IPADDRESS, A.USERAGENT, CASE A.STATUS WHEN '1' THEN 'Sukses' ELSE 'Gagal' END AS 'NSTATUS', A.STATUS FROM sys_log A LEFT OUTER JOIN sys_dmenu B ON B.DMENU = A.DMENU LEFT OUTER JOIN users C ON C.USERNAME = A.USERNAME WHERE A.DATE BETWEEN :frdate AND :todate AND YEAR(A.DATE) LIKE :nyear AND A.USERNAME LIKE :username AND A.STATUS LIKE :status ORDER BY A.CREATED_AT DESC",
        ]);

        DB::table('sys_table')->insert([
            'gmenu' => 'exampl',
            'dmenu' => 'rpexam',
            'urut' => '2',
            'field' => 'date',
            'alias' => 'Date',
            'type' => 'date2',
            'validate' => 'nullable',
            'filter' => '1',
            'query' => '',
        ]);

        DB::table('sys_table')->insert([
            'gmenu' => 'exampl',
            'dmenu' => 'rpexam',
            'urut' => '3',
            'field' => 'nyear',
            'alias' => 'Tahun',
            'type' => 'enum',
            'length' => '4',
            'validate' => 'max:4',
            'filter' => '1',
            'query' => "select value, name from sys_enum where idenum = 'tahun' and isactive = '1'",
            'class' => 'custom-select',
        ]);

        DB::table('sys_table')->insert([
            'gmenu' => 'exampl',
            'dmenu' => 'rpexam',
            'urut' => '4',
            'field' => 'username',
            'alias' => 'Username',
            'type' => 'search',
            'length' => '20',
            'validate' => 'max:20',
            'filter' => '1',
            'query' => "select username, firstname from users where isactive = '1'",
            'class' => '',
        ]);

        DB::table('sys_table')->insert([
            'gmenu' => 'exampl',
            'dmenu' => 'rpexam',
            'urut' => '5',
            'field' => 'status',
            'alias' => 'Status',
            'type' => 'enum',
            'length' => '1',
            'validate' => 'max:1',
            'filter' => '1',
            'class' => 'filter',
            'query' => "select value, name from sys_enum where idenum = 'status' and isactive = '1'",
        ]);
    }
}
