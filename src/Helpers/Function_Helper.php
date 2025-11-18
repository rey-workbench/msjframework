<?php

namespace App\Helpers;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;

class Function_Helper
{
    public function log_insert($tipe, $dmenu, $desc, $status)
    {
        // check session
        (Session::has('username')) ? $username = session('username') : ((request()->username) ? $username = request()->username : $username = '-');

        // Truncate description to fit database column (255 chars)
        $desc = strlen($desc) > 250 ? substr($desc, 0, 250).'...' : $desc;

        $data_log = [
            'DATE' => now(),
            'USERNAME' => $username,
            'TIPE' => $tipe,
            'DMENU' => $dmenu,
            'DESCRIPTION' => $desc,
            'STATUS' => $status,
            'IPADDRESS' => request()->ip(),
            'USERAGENT' => request()->header('user-agent'),
        ];
        if (DB::table('sys_log')->insert($data_log)) {
            return true;
        } else {
            return false;
        }
    }

    public function transaction_list_insert($idtrans, $posting, $sloc, $item, $material, $batch, $length, $width, $gsm, $weight, $qty, $uom, $color, $tipe)
    {
        $data_transaction = [
            'idtrans' => $idtrans,
            'posting' => $posting,
            'sloc' => $sloc,
            'item' => $item,
            'material' => $material,
            'batch' => $batch,
            'length' => $length,
            'width' => $width,
            'gsm' => $gsm,
            'weight' => $weight,
            'qty' => $qty,
            'uom' => $uom,
            'color' => $color,
            'tipe' => $tipe,
            'created_at' => now(),
            'user_create' => session('username'),
        ];
        if (DB::table('transaction_list')->insert($data_transaction)) {
            return true;
        } else {
            return false;
        }
        // contoh penggunaan transaction_list
        // $syslog->transaction_list_insert($idtrans->{$data['table_primary']->field}, now(), 'sloc', '10', $idtrans->{$data['table_primary']->field}, 'batch', '10', '20', '30', '40', '50', 'uom', 'warna', 'I');

    }

    public function check_query($gmenu, $dmenu, $data)
    {
        // check query
        $check_query = DB::table('sys_table')->where(['gmenu' => $gmenu, 'dmenu' => $dmenu])->whereNot('query', '')->whereNotNull('query')->where('query', 'not like', '%select%')->first();
        // dd($check_query);
        if ($check_query) {
            // if not exist
            $data['url_menu'] = 'error';
            $data['title_group'] = 'Error';
            $data['title_menu'] = 'Error Query';
            $data['errorpages'] = 'Only "SELECT" Query Is Permitted.!';

            // return error page
            return view('pages.errorpages', $data);
        } else {
            return true;
        }
    }
}
