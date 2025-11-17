<?php

namespace MSJFramework\LaravelGenerator\Templates\Controllers\Layouts\Transc;

use Illuminate\Support\Str;

class TranscControllerTemplate
{
    public static function getTemplate(array $config): string
    {
        $controllerName = 'TranscController';

        return <<<'PHP'
<?php

namespace App\Http\Controllers;

use App\Helpers\Format_Helper;
use App\Helpers\Function_Helper;
use Illuminate\Contracts\Encryption\DecryptException;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use Illuminate\Validation\Rule;

class TranscController extends Controller
{
    public function index($data)
    {
        $data['format'] = new Format_Helper;
        
        $data['table_header_h'] = DB::table('sys_table')->where(['gmenu' => $data['gmenuid'], 'dmenu' => $data['dmenu'], 'list' => '1', 'position' => '1'])->orderBy('urut')->get();
        $data['table_header_d'] = DB::table('sys_table')->where(['gmenu' => $data['gmenuid'], 'dmenu' => $data['dmenu'], 'list' => '1', 'position' => '2'])->orderBy('urut')->get();
        
        (Session::has('idtrans')) ? $primaryArray = explode(':', Session::get('idtrans')) : $primaryArray = ['-', '-', '-', '-', '-'];
        $i = 0;
        $wherekey_h = [];

        $data['table_detail_h'] = collect();

        foreach ($data['table_header_h'] as $header_h) {
            if ($header_h->query != '') {
                $data['table_detail_h'] = DB::select($header_h->query);
            }
            $wherekey_h[$header_h->field] = $primaryArray[$i];
            $i++;
        }

        if ($data['table_detail_h']->isEmpty()) {
            $data['table_detail_h'] = DB::table($data['tabel'])->get();
        }
        
        $data['colomh'] = $i;
        $data['table_detail_d'] = DB::table($data['tabel'])->where($wherekey_h)->get();
        $data['table_primary_h'] = DB::table('sys_table')->where(['gmenu' => $data['gmenuid'], 'dmenu' => $data['dmenu'], 'position' => '1', 'primary' => '1'])->orderBy('urut')->get();
        $data['table_primary_d'] = DB::table('sys_table')->where(['gmenu' => $data['gmenuid'], 'dmenu' => $data['dmenu'], 'position' => '2', 'primary' => '1'])->orderBy('urut')->get();
        
        if ($data['table_primary_h']) {
            return view($data['url'], $data);
        } else {
            $data['url_menu'] = 'error';
            $data['title_group'] = 'Error';
            $data['title_menu'] = 'Error';
            $data['errorpages'] = 'Not Found!';
            return view('pages.errorpages', $data);
        }
    }

    public function ajax($data)
    {
        try {
            $id = decrypt($_GET['id']);
        } catch (DecryptException $e) {
            $id = '';
        }
        
        $primaryArray = explode(':', $id);
        $data['table_header_h'] = DB::table('sys_table')->where(['gmenu' => $data['gmenuid'], 'dmenu' => $data['dmenu'], 'list' => '1', 'position' => '1'])->orderBy('urut')->get();
        $i = 0;
        $wherekey_h = [];
        foreach ($data['table_header_h'] as $key_h) {
            $wherekey_h[$key_h->field] = $primaryArray[$i];
            $i++;
        }
        
        $data['ajaxid'] = $id;
        $data['table_detail_d_ajax'] = DB::table($data['tabel'])->where($wherekey_h)->get();
        $data['table_primary_d_ajax'] = DB::table('sys_table')->where(['gmenu' => $_GET['gmenu'], 'dmenu' => $_GET['dmenu'], 'primary' => '1'])->orderBy('urut')->get();
        $data['table_header_d'] = DB::table('sys_table')->where(['gmenu' => $data['gmenuid'], 'dmenu' => $data['dmenu'], 'list' => '1', 'position' => '2'])->orderBy('urut')->get();
        $data['table_header_d_ajax'] = DB::table('sys_table')->where(['gmenu' => $_GET['gmenu'], 'dmenu' => $_GET['dmenu'], 'list' => '1', 'position' => '2'])->orderBy('urut')->get();
        
        $data['encrypt_primary'] = [];
        $data['data_join'] = [];
        $query_join = DB::table('sys_table')->where(['gmenu' => $_GET['gmenu'], 'dmenu' => $_GET['dmenu'], 'position' => '2', 'type' => 'join'])->whereNot('query', '')->orderBy('urut')->first();
        foreach ($data['table_detail_d_ajax'] as $detail) {
            $data_primary = '';
            foreach ($data['table_primary_d_ajax'] as $primary) {
                ($data_primary == '') ? $data_primary = $detail->{$primary->field} : $data_primary = $data_primary.':'.$detail->{$primary->field};
            }
            if ($query_join) {
                $val_join = DB::select($query_join->query." '".$detail->{$query_join->field}."'");
                array_push($data['data_join'], $val_join);
            }
            array_push($data['encrypt_primary'], encrypt($data_primary));
        }
        
        $data['table_query_ajax'] = DB::table('sys_table')->where(['gmenu' => $_GET['gmenu'], 'dmenu' => $_GET['dmenu'], 'position' => '2'])->whereNot('query', '')->whereNot('type', 'join')->orderBy('urut')->get();
        foreach ($data['table_query_ajax'] as $query) {
            $data[$query->field] = DB::select($query->query);
        }

        return json_encode($data);
    }

    public function add($data)
    {
        $syslog = new Function_Helper;
        $data['table_primary'] = DB::table('sys_table')->where(['gmenu' => $data['gmenuid'], 'dmenu' => $data['dmenu'], 'primary' => '1', 'position' => '1'])->orderBy('urut')->get();
        $data['table_header'] = DB::table('sys_table')->where(['gmenu' => $data['gmenuid'], 'dmenu' => $data['dmenu'], 'show' => '1'])->orderBy('urut')->get();
        
        try {
            $id = decrypt($data['idencrypt']);
        } catch (DecryptException $e) {
            $id = '';
        }
        
        $primaryArray = explode(':', $id);
        $wherekey = [];
        $i = 0;
        if ($id != '') {
            foreach ($data['table_primary'] as $key) {
                $wherekey[$key->field] = $primaryArray[$i];
                $i++;
            }
        }
        $data['wherekey'] = $wherekey;
        
        if ($data['authorize']->add == '1') {
            return view($data['url'], $data);
        } else {
            $data['url_menu'] = $data['url_menu'];
            $data['title_group'] = 'Error';
            $data['title_menu'] = 'Error';
            $data['errorpages'] = 'Not Authorized!';
            $syslog->log_insert('E', $data['url_menu'], 'Not Authorized!'.' - Add -'.$data['url_menu'], '0');
            return view('pages.errorpages', $data);
        }
    }

    public function store($data)
    {
        $data['format'] = new Format_Helper;
        $syslog = new Function_Helper;
        
        $data['table_header'] = DB::table('sys_table')->where(['gmenu' => $data['gmenuid'], 'dmenu' => $data['dmenu'], 'show' => '1'])->orderBy('urut')->get();
        $data['table_primary'] = DB::table('sys_table')->where(['gmenu' => $data['gmenuid'], 'dmenu' => $data['dmenu'], 'primary' => '1'])->orderBy('urut')->get();
        $data['table_primary_h'] = DB::table('sys_table')->where(['gmenu' => $data['gmenuid'], 'dmenu' => $data['dmenu'], 'primary' => '1', 'position' => '1'])->orderBy('urut')->get();
        $sys_id = DB::table('sys_id')->where('dmenu', $data['dmenu'])->orderBy('urut', 'ASC')->first();
        
        $wherekey = [];
        $idtrans = '';
        foreach ($data['table_primary'] as $key) {
            $wherekey[$key->field] = request()->{$key->field};
            $idtrans = ($idtrans == '') ? $idtrans = request()->{$key->field} : $idtrans.','.request()->{$key->field};
        }
        $idtrans_h = '';
        foreach ($data['table_primary_h'] as $key) {
            $idtrans_h = ($idtrans_h == '') ? $idtrans_h = request()->{$key->field} : $idtrans_h.':'.request()->{$key->field};
        }
        $data_key = DB::table($data['tabel'])->where($wherekey)->first();
        
        foreach ($data['table_header']->map(function ($item) {
            return (array) $item;
        }) as $item) {
            $primary = false;
            $generateid = false;
            foreach ($data['table_primary'] as $p) {
                $primary == false
                    ? ($p->field == $item['field']
                        ? ($primary = true)
                        : ($primary = false))
                    : '';
                $generateid == false
                    ? ($p->generateid != ''
                        ? ($generateid = true)
                        : ($generateid = false))
                    : '';
            }
            if ($primary && $sys_id) {
                $validate[$item['field']] = '';
            } elseif ($primary && ! $data_key) {
                $validate[$item['field']] = '';
            } else {
                $validate[$item['field']] = $item['validate'];
            }
        }
        
        $attributes = request()->validate(
            $validate,
            [
                'required' => ':attribute tidak boleh kosong',
                'unique' => ':attribute sudah ada',
                'min' => ':attribute minimal :min karakter',
                'max' => ':attribute maksimal :max karakter',
                'email' => 'format :attribute salah',
                'mimes' => ':attribute format harus :values',
                'between' => ':attribute diisi antara :min sampai :max',
            ]
        );
        
        if (isset($attributes['password'])) {
            $new_password = bcrypt($attributes['password']);
            $attributes['password'] = $new_password;
        }
        
        $data['image'] = DB::table('sys_table')->where(['gmenu' => $data['gmenuid'], 'dmenu' => $data['dmenu']])->whereIn('type', ['image', 'file'])->get();
        foreach ($data['image'] as $img) {
            if (request()->file($img->field)) {
                $attributes[$img->field] = request()->file($img->field)->store($data['tabel']);
            }
        }
        
        $data['table_header'] = DB::table('sys_table')->where(['gmenu' => $data['gmenuid'], 'dmenu' => $data['dmenu'],  'list' => '1'])->orderBy('urut')->get();
        $data['table_detail'] = DB::table($data['tabel'])->get();
        $data['table_primary_generate'] = DB::table('sys_table')->where(['gmenu' => $data['gmenuid'], 'dmenu' => $data['dmenu'], 'primary' => '1'])->orderBy('urut')->first();
        
        if ($sys_id) {
            $insert_data = DB::table($data['tabel'])->insert([$data['table_primary_generate']->field => $data['format']->IDFormat($data['dmenu'])] + $attributes + ['user_create' => session('username')]);
        } else {
            $insert_data = DB::table($data['tabel'])->insert($attributes + ['user_create' => session('username')]);
        }
        
        if ($insert_data) {
            $syslog->log_insert('C', $data['dmenu'], 'Created : '.$idtrans, '1');
            Session::flash('message', 'Tambah Data Berhasil!');
            Session::flash('class', 'success');
            Session::flash('idtrans', $idtrans_h);
            return redirect($data['url_menu'])->with($data);
        } else {
            $syslog->log_insert('E', $data['dmenu'], 'Create Error', '0');
            Session::flash('message', 'Tambah Data Gagal!');
            Session::flash('class', 'danger');
            Session::flash('idtrans', $idtrans_h);
            return redirect($data['url_menu'])->with($data);
        }
    }

    public function show($data)
    {
        $data['table_header'] = DB::table('sys_table')->where(['gmenu' => $data['gmenuid'], 'dmenu' => $data['dmenu'],  'filter' => '1', 'show' => '1'])->orderBy('urut')->get();
        $data['table_primary'] = DB::table('sys_table')->where(['gmenu' => $data['gmenuid'], 'dmenu' => $data['dmenu'], 'primary' => '1'])->orderBy('urut')->get();
        
        try {
            $id = decrypt($data['idencrypt']);
        } catch (DecryptException $e) {
            $id = '';
        }
        
        $primaryArray = explode(':', $id);
        $wherekey = [];
        $i = 0;
        foreach ($data['table_primary'] as $key) {
            $wherekey[$key->field] = $primaryArray[$i];
            $i++;
        }
        $list = DB::table($data['tabel'])->where($wherekey)->first();
        
        if ($list) {
            $data['list'] = $list;
            return view($data['url'], $data);
        } else {
            $data['url_menu'] = 'error';
            $data['title_group'] = 'Error';
            $data['title_menu'] = 'Error';
            $data['errorpages'] = 'Not Found!';
            return view('pages.errorpages', $data);
        }
    }

    public function edit($data)
    {
        $syslog = new Function_Helper;
        $data['table_header'] = DB::table('sys_table')->where(['gmenu' => $data['gmenuid'], 'dmenu' => $data['dmenu'],  'filter' => '1', 'show' => '1'])->orderBy('urut')->get();
        $data['table_primary'] = DB::table('sys_table')->where(['gmenu' => $data['gmenuid'], 'dmenu' => $data['dmenu'], 'primary' => '1'])->orderBy('urut')->get();
        
        try {
            $id = decrypt($data['idencrypt']);
        } catch (DecryptException $e) {
            $id = '';
        }
        
        $primaryArray = explode(':', $id);
        $wherekey = [];
        $i = 0;
        foreach ($data['table_primary'] as $key) {
            $wherekey[$key->field] = $primaryArray[$i];
            $i++;
        }
        $list = DB::table($data['tabel'])->where($wherekey)->first();
        
        if ($list) {
            if ($data['authorize']->edit == '1') {
                $data['list'] = $list;
                return view($data['url'], $data);
            } else {
                $data['url_menu'] = $data['url_menu'];
                $data['title_group'] = 'Error';
                $data['title_menu'] = 'Error';
                $data['errorpages'] = 'Not Authorized!';
                $syslog->log_insert('E', $data['url_menu'], 'Not Authorized!'.' - Edit -'.$data['url_menu'], '0');
                return view('pages.errorpages', $data);
            }
        } else {
            $data['url_menu'] = 'error';
            $data['title_group'] = 'Error';
            $data['title_menu'] = 'Error';
            $data['errorpages'] = 'Not Found!';
            return view('pages.errorpages', $data);
        }
    }

    public function update($data)
    {
        $syslog = new Function_Helper;
        
        try {
            $id = decrypt($data['idencrypt']);
        } catch (DecryptException $e) {
            $id = '';
        }
        
        $data['table_primary'] = DB::table('sys_table')->where(['gmenu' => $data['gmenuid'], 'dmenu' => $data['dmenu'], 'primary' => '1'])->orderBy('urut')->get();
        $data['table_primary_h'] = DB::table('sys_table')->where(['gmenu' => $data['gmenuid'], 'dmenu' => $data['dmenu'], 'primary' => '1', 'position' => '1'])->orderBy('urut')->get();
        
        $primaryArray = explode(':', $id);
        $wherekey = [];
        $wherenotkey = [];
        $i = 0;
        foreach ($data['table_primary'] as $key) {
            $wherekey[$key->field] = $primaryArray[$i];
            $wherenotkey[] = $key->field;
            $i++;
        }
        $idtrans_h = '';
        $i = 0;
        foreach ($data['table_primary_h'] as $key) {
            $idtrans_h = ($idtrans_h == '') ? $idtrans_h = $primaryArray[$i] : $idtrans_h.':'.$primaryArray[$i];
            $i++;
        }
        
        $data['table_header'] = DB::table('sys_table')->where(['gmenu' => $data['gmenuid'], 'dmenu' => $data['dmenu'], 'filter' => '1', 'show' => '1'])->whereNotIn('field', $wherenotkey)->orderBy('urut')->get();
        
        foreach ($data['table_header']->map(function ($item) {
            return (array) $item;
        }) as $item) {
            if ($item['field'] == 'email') {
                $validate[$item['field']] = ['required', 'email', 'max:255', Rule::unique('users', 'email')->ignore($id, 'username')];
            } elseif ($item['field'] == 'password' && request()->email && empty(request()->password)) {
                unset($validate[$item['field']]);
            } else {
                $validate[$item['field']] = $item['validate'];
            }
        }
        
        $attributes = request()->validate(
            $validate,
            [
                'required' => ':attribute tidak boleh kosong',
                'unique' => ':attribute sudah ada',
                'min' => ':attribute minimal :min karakter',
                'max' => ':attribute maksimal :max karakter',
                'email' => 'format :attribute salah',
                'mimes' => ':attribute rormat harus :values',
                'between' => ':attribute diisi antara :min sampai :max',
            ]
        );
        
        if (isset($attributes['password'])) {
            $new_password = bcrypt($attributes['password']);
            $attributes['password'] = $new_password;
        }
        
        $data['image'] = DB::table('sys_table')->where(['gmenu' => $data['gmenuid'], 'dmenu' => $data['dmenu']])->whereIn('type', ['image', 'file'])->get();
        foreach ($data['image'] as $img) {
            if (request()->file($img->field)) {
                $attributes[$img->field] = request()->file($img->field)->store($data['tabel']);
            }
        }
        
        $data['table_header'] = DB::table('sys_table')->where(['gmenu' => $data['gmenuid'], 'dmenu' => $data['dmenu'],  'list' => '1'])->orderBy('urut')->get();
        $data['table_detail'] = DB::table($data['tabel'])->get();
        
        $updateData = DB::table($data['tabel'])->where($wherekey)->update($attributes + ['user_update' => session('username')]);
        
        if ($updateData) {
            $syslog->log_insert('U', $data['dmenu'], 'Updated : '.$id, '1');
            Session::flash('message', 'Edit User Berhasil!');
            Session::flash('class', 'success');
            Session::flash('idtrans', $idtrans_h);
            return redirect($data['url_menu'])->with($data);
        } else {
            $syslog->log_insert('E', $data['dmenu'], 'Update Error', '0');
            Session::flash('message', 'Edit User Gagal!');
            Session::flash('class', 'danger');
            Session::flash('idtrans', $idtrans_h);
            return redirect($data['url_menu'])->with($data);
        }
    }

    public function destroy($data)
    {
        $syslog = new Function_Helper;
        $data['table_primary'] = DB::table('sys_table')->where(['gmenu' => $data['gmenuid'], 'dmenu' => $data['dmenu'], 'primary' => '1'])->orderBy('urut')->get();
        $data['table_primary_h'] = DB::table('sys_table')->where(['gmenu' => $data['gmenuid'], 'dmenu' => $data['dmenu'], 'primary' => '1', 'position' => '1'])->orderBy('urut')->get();
        
        try {
            $id = decrypt($data['idencrypt']);
        } catch (DecryptException $e) {
            $id = '';
        }
        
        $primaryArray = explode(':', $id);
        $wherekey = [];
        $i = 0;
        foreach ($data['table_primary'] as $key) {
            $wherekey[$key->field] = $primaryArray[$i];
            $i++;
        }
        $idtrans_h = '';
        $i = 0;
        foreach ($data['table_primary_h'] as $key) {
            $idtrans_h = ($idtrans_h == '') ? $idtrans_h = $primaryArray[$i] : $idtrans_h.':'.$primaryArray[$i];
            $i++;
        }
        $deleteData = DB::table($data['tabel'])->where($wherekey)->delete();
        
        if ($deleteData) {
            $syslog->log_insert('D', $data['dmenu'], 'Deleted : '.$id, '1');
            Session::flash('message', 'Hapus Data Berhasil!');
            Session::flash('class', 'success');
            Session::flash('idtrans', $idtrans_h);
            return redirect($data['url_menu'])->with($data);
        } else {
            $syslog->log_insert('D', $data['dmenu'], 'Deleted Error : '.$id, '0');
            Session::flash('message', 'Hapus Data Gagal!');
            Session::flash('class', 'danger');
            Session::flash('idtrans', $idtrans_h);
            return redirect($data['url_menu'])->with($data);
        }
    }
}
PHP;
    }
}
