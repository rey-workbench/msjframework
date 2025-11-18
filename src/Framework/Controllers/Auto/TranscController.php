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
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    // public function __construct()
    // {
    //     $this->middleware('auth');
    // }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\View\View
     */
    public function index($data)
    {
        // function helper
        $data['format'] = new Format_Helper;
        // list data table
        $data['table_header_h'] = DB::table('sys_table')->where(['gmenu' => $data['gmenuid'], 'dmenu' => $data['dmenu'], 'list' => '1', 'position' => '1'])->orderBy('urut')->get();
        $data['table_header_d'] = DB::table('sys_table')->where(['gmenu' => $data['gmenuid'], 'dmenu' => $data['dmenu'], 'list' => '1', 'position' => '2'])->orderBy('urut')->get();
        // set default data
        (Session::has('idtrans')) ? $primaryArray = explode(':', Session::get('idtrans')) : $primaryArray = ['-', '-', '-', '-', '-'];
        $i = 0;
        $wherekey_h = [];

        // Initialize table_detail_h as empty collection
        $data['table_detail_h'] = collect();

        foreach ($data['table_header_h'] as $header_h) {
            if ($header_h->query != '') {
                $data['table_detail_h'] = DB::select($header_h->query);
            }
            $wherekey_h[$header_h->field] = $primaryArray[$i];
            $i++;
        }

        // If no query was found, get data from main table
        if ($data['table_detail_h']->isEmpty()) {
            $data['table_detail_h'] = DB::table($data['tabel'])->get();
        }
        // list data table
        $data['colomh'] = $i;
        $data['table_detail_d'] = DB::table($data['tabel'])->where($wherekey_h)->get();
        $data['table_primary_h'] = DB::table('sys_table')->where(['gmenu' => $data['gmenuid'], 'dmenu' => $data['dmenu'], 'position' => '1', 'primary' => '1'])->orderBy('urut')->get();
        $data['table_primary_d'] = DB::table('sys_table')->where(['gmenu' => $data['gmenuid'], 'dmenu' => $data['dmenu'], 'position' => '2', 'primary' => '1'])->orderBy('urut')->get();
        // check data table primary
        if ($data['table_primary_h']) {
            // return page menu
            return view($data['url'], $data);
        } else {
            // if not exist
            $data['url_menu'] = 'error';
            $data['title_group'] = 'Error';
            $data['title_menu'] = 'Error';
            $data['errorpages'] = 'Not Found!';

            // return error page
            return view('pages.errorpages', $data);
        }
    }

    /**
     * Display the specified resource with ajax.
     */
    public function ajax($data)
    {
        // check decrypt
        try {
            $id = decrypt($_GET['id']);
        } catch (DecryptException $e) {
            $id = '';
        }
        // data primary key
        $primaryArray = explode(':', $id);
        // list data table
        $data['table_header_h'] = DB::table('sys_table')->where(['gmenu' => $data['gmenuid'], 'dmenu' => $data['dmenu'], 'list' => '1', 'position' => '1'])->orderBy('urut')->get();
        $i = 0;
        $wherekey_h = [];
        foreach ($data['table_header_h'] as $key_h) {
            $wherekey_h[$key_h->field] = $primaryArray[$i];
            $i++;
        }
        // ajax id
        $data['ajaxid'] = $id;
        // list data table
        $data['table_detail_d_ajax'] = DB::table($data['tabel'])->where($wherekey_h)->get();
        $data['table_primary_d_ajax'] = DB::table('sys_table')->where(['gmenu' => $_GET['gmenu'], 'dmenu' => $_GET['dmenu'], 'primary' => '1'])->orderBy('urut')->get();
        $data['table_header_d'] = DB::table('sys_table')->where(['gmenu' => $data['gmenuid'], 'dmenu' => $data['dmenu'], 'list' => '1', 'position' => '2'])->orderBy('urut')->get();
        $data['table_header_d_ajax'] = DB::table('sys_table')->where(['gmenu' => $_GET['gmenu'], 'dmenu' => $_GET['dmenu'], 'list' => '1', 'position' => '2'])->orderBy('urut')->get();
        // set encrypt primery key
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
        // data query
        $data['table_query_ajax'] = DB::table('sys_table')->where(['gmenu' => $_GET['gmenu'], 'dmenu' => $_GET['dmenu'], 'position' => '2'])->whereNot('query', '')->whereNot('type', 'join')->orderBy('urut')->get();
        foreach ($data['table_query_ajax'] as $query) {
            $data[$query->field] = DB::select($query->query);
        }

        // }
        return json_encode($data);
    }

    /**
     * Display the specified resource.
     */
    public function add($data)
    {
        // function helper
        $syslog = new Function_Helper;
        // list data table
        $data['table_primary'] = DB::table('sys_table')->where(['gmenu' => $data['gmenuid'], 'dmenu' => $data['dmenu'], 'primary' => '1', 'position' => '1'])->orderBy('urut')->get();
        $data['table_header'] = DB::table('sys_table')->where(['gmenu' => $data['gmenuid'], 'dmenu' => $data['dmenu'], 'show' => '1'])->orderBy('urut')->get();
        // check decrypt
        try {
            $id = decrypt($data['idencrypt']);
        } catch (DecryptException $e) {
            $id = '';
        }
        // data primary key
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
        // check athorization access add
        if ($data['authorize']->add == '1') {
            // return page menu
            return view($data['url'], $data);
        } else {
            // if not athorize
            $data['url_menu'] = $data['url_menu'];
            $data['title_group'] = 'Error';
            $data['title_menu'] = 'Error';
            $data['errorpages'] = 'Not Authorized!';
            // insert log
            $syslog->log_insert('E', $data['url_menu'], 'Not Authorized!'.' - Add -'.$data['url_menu'], '0');

            // return error page
            return view('pages.errorpages', $data);
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store($data)
    {
        // function helper
        $data['format'] = new Format_Helper;
        $syslog = new Function_Helper;
        // list data table
        $data['table_header'] = DB::table('sys_table')->where(['gmenu' => $data['gmenuid'], 'dmenu' => $data['dmenu'], 'show' => '1'])->orderBy('urut')->get();
        $data['table_primary'] = DB::table('sys_table')->where(['gmenu' => $data['gmenuid'], 'dmenu' => $data['dmenu'], 'primary' => '1'])->orderBy('urut')->get();
        $data['table_primary_h'] = DB::table('sys_table')->where(['gmenu' => $data['gmenuid'], 'dmenu' => $data['dmenu'], 'primary' => '1', 'position' => '1'])->orderBy('urut')->get();
        $sys_id = DB::table('sys_id')->where('dmenu', $data['dmenu'])->orderBy('urut', 'ASC')->first();
        // cek data primary key
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
        // get data validate
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
        // validasi data
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
        // check password
        if (isset($attributes['password'])) {
            // encrypt password
            $new_password = bcrypt($attributes['password']);
            $attributes['password'] = $new_password;
        }
        // check data image and file
        $data['image'] = DB::table('sys_table')->where(['gmenu' => $data['gmenuid'], 'dmenu' => $data['dmenu']])->whereIn('type', ['image', 'file'])->get();
        foreach ($data['image'] as $img) {
            if (request()->file($img->field)) {
                $attributes[$img->field] = request()->file($img->field)->store($data['tabel']);
            }
        }
        // list data
        $data['table_header'] = DB::table('sys_table')->where(['gmenu' => $data['gmenuid'], 'dmenu' => $data['dmenu'],  'list' => '1'])->orderBy('urut')->get();
        $data['table_detail'] = DB::table($data['tabel'])->get();
        $data['table_primary_generate'] = DB::table('sys_table')->where(['gmenu' => $data['gmenuid'], 'dmenu' => $data['dmenu'], 'primary' => '1'])->orderBy('urut')->first();
        // check data Generate ID
        if ($sys_id) {
            // set ID from generate id
            $insert_data = DB::table($data['tabel'])->insert([$data['table_primary_generate']->field => $data['format']->IDFormat($data['dmenu'])] + $attributes + ['user_create' => session('username')]);
        } else {
            // set ID manual
            $insert_data = DB::table($data['tabel'])->insert($attributes + ['user_create' => session('username')]);
        }
        // check insert
        if ($insert_data) {
            // insert sys_log
            $syslog->log_insert('C', $data['dmenu'], 'Created : '.$idtrans, '1');
            // Set a session message
            Session::flash('message', 'Tambah Data Berhasil!');
            Session::flash('class', 'success');
            Session::flash('idtrans', $idtrans_h);

            // return page menu
            return redirect($data['url_menu'])->with($data);
        } else {
            // insert sys_log
            $syslog->log_insert('E', $data['dmenu'], 'Create Error', '0');
            // Set a session message
            Session::flash('message', 'Tambah Data Gagal!');
            Session::flash('class', 'danger');
            Session::flash('idtrans', $idtrans_h);

            // return page menu
            return redirect($data['url_menu'])->with($data);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show($data)
    {
        // list data table
        $data['table_header'] = DB::table('sys_table')->where(['gmenu' => $data['gmenuid'], 'dmenu' => $data['dmenu'],  'filter' => '1', 'show' => '1'])->orderBy('urut')->get();
        $data['table_primary'] = DB::table('sys_table')->where(['gmenu' => $data['gmenuid'], 'dmenu' => $data['dmenu'], 'primary' => '1'])->orderBy('urut')->get();
        // check decrypt
        try {
            $id = decrypt($data['idencrypt']);
        } catch (DecryptException $e) {
            $id = '';
        }
        // data primary key
        $primaryArray = explode(':', $id);
        $wherekey = [];
        $i = 0;
        foreach ($data['table_primary'] as $key) {
            $wherekey[$key->field] = $primaryArray[$i];
            $i++;
        }
        $list = DB::table($data['tabel'])->where($wherekey)->first();
        // check data list
        if ($list) {
            $data['list'] = $list;

            // return page menu
            return view($data['url'], $data);
        } else {
            // if not exist
            $data['url_menu'] = 'error';
            $data['title_group'] = 'Error';
            $data['title_menu'] = 'Error';
            $data['errorpages'] = 'Not Found!';

            // return error page
            return view('pages.errorpages', $data);
        }
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($data)
    {
        // function helper
        $syslog = new Function_Helper;
        // list data table
        $data['table_header'] = DB::table('sys_table')->where(['gmenu' => $data['gmenuid'], 'dmenu' => $data['dmenu'],  'filter' => '1', 'show' => '1'])->orderBy('urut')->get();
        $data['table_primary'] = DB::table('sys_table')->where(['gmenu' => $data['gmenuid'], 'dmenu' => $data['dmenu'], 'primary' => '1'])->orderBy('urut')->get();
        // check decrypt
        try {
            $id = decrypt($data['idencrypt']);
        } catch (DecryptException $e) {
            $id = '';
        }
        // data primary key
        $primaryArray = explode(':', $id);
        $wherekey = [];
        $i = 0;
        foreach ($data['table_primary'] as $key) {
            $wherekey[$key->field] = $primaryArray[$i];
            $i++;
        }
        $list = DB::table($data['tabel'])->where($wherekey)->first();
        // check data list
        if ($list) {
            // check athorization access edit
            if ($data['authorize']->edit == '1') {
                $data['list'] = $list;

                // return page menu
                return view($data['url'], $data);
            } else {
                // if not athorize
                $data['url_menu'] = $data['url_menu'];
                $data['title_group'] = 'Error';
                $data['title_menu'] = 'Error';
                $data['errorpages'] = 'Not Authorized!';
                // insert log
                $syslog->log_insert('E', $data['url_menu'], 'Not Authorized!'.' - Edit -'.$data['url_menu'], '0');

                // return error page
                return view('pages.errorpages', $data);
            }
        } else {
            // if not exist
            $data['url_menu'] = 'error';
            $data['title_group'] = 'Error';
            $data['title_menu'] = 'Error';
            $data['errorpages'] = 'Not Found!';

            // return error page
            return view('pages.errorpages', $data);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update($data)
    {
        // function helper
        $syslog = new Function_Helper;
        // check decrypt
        try {
            $id = decrypt($data['idencrypt']);
        } catch (DecryptException $e) {
            $id = '';
        }
        // list data table
        $data['table_primary'] = DB::table('sys_table')->where(['gmenu' => $data['gmenuid'], 'dmenu' => $data['dmenu'], 'primary' => '1'])->orderBy('urut')->get();
        $data['table_primary_h'] = DB::table('sys_table')->where(['gmenu' => $data['gmenuid'], 'dmenu' => $data['dmenu'], 'primary' => '1', 'position' => '1'])->orderBy('urut')->get();
        // data primary key
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
        // list data
        $data['table_header'] = DB::table('sys_table')->where(['gmenu' => $data['gmenuid'], 'dmenu' => $data['dmenu'], 'filter' => '1', 'show' => '1'])->whereNotIn('field', $wherenotkey)->orderBy('urut')->get();
        // get data validate
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
        // validasi data
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
        // data password
        if (isset($attributes['password'])) {
            // encryp password
            $new_password = bcrypt($attributes['password']);
            $attributes['password'] = $new_password;
        }
        // check data image and file
        $data['image'] = DB::table('sys_table')->where(['gmenu' => $data['gmenuid'], 'dmenu' => $data['dmenu']])->whereIn('type', ['image', 'file'])->get();
        foreach ($data['image'] as $img) {
            if (request()->file($img->field)) {
                $attributes[$img->field] = request()->file($img->field)->store($data['tabel']);
            }
        }
        // list data
        $data['table_header'] = DB::table('sys_table')->where(['gmenu' => $data['gmenuid'], 'dmenu' => $data['dmenu'],  'list' => '1'])->orderBy('urut')->get();
        $data['table_detail'] = DB::table($data['tabel'])->get();
        // Update data by id
        $updateData = DB::table($data['tabel'])->where($wherekey)->update($attributes + ['user_update' => session('username')]);
        // check update
        if ($updateData) {
            // insert sys_log
            $syslog->log_insert('U', $data['dmenu'], 'Updated : '.$id, '1');
            // Set a session message
            Session::flash('message', 'Edit User Berhasil!');
            Session::flash('class', 'success');
            Session::flash('idtrans', $idtrans_h);

            // return page menu
            return redirect($data['url_menu'])->with($data);
        } else {
            // insert sys_log
            $syslog->log_insert('E', $data['dmenu'], 'Update Error', '0');
            // Set a session message
            Session::flash('message', 'Edit User Gagal!');
            Session::flash('class', 'danger');
            Session::flash('idtrans', $idtrans_h);

            // return error page
            return redirect($data['url_menu'])->with($data);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($data)
    {
        // function helper
        $syslog = new Function_Helper;
        // get field primary
        $data['table_primary'] = DB::table('sys_table')->where(['gmenu' => $data['gmenuid'], 'dmenu' => $data['dmenu'], 'primary' => '1'])->orderBy('urut')->get();
        // list data
        $data['table_primary_h'] = DB::table('sys_table')->where(['gmenu' => $data['gmenuid'], 'dmenu' => $data['dmenu'], 'primary' => '1', 'position' => '1'])->orderBy('urut')->get();
        // check decrypt
        try {
            $id = decrypt($data['idencrypt']);
        } catch (DecryptException $e) {
            $id = '';
        }
        // data primary key
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
        // check delete
        if ($deleteData) {
            // insert sys_log
            $syslog->log_insert('D', $data['dmenu'], 'Deleted : '.$id, '1');
            // Set a session message
            Session::flash('message', 'Hapus Data Berhasil!');
            Session::flash('class', 'success');
            Session::flash('idtrans', $idtrans_h);

            return redirect($data['url_menu'])->with($data);
        } else {
            // insert sys_log
            $syslog->log_insert('D', $data['dmenu'], 'Deleted Error : '.$id, '0');
            // Set a session message
            Session::flash('message', 'Hapus Data Gagal!');
            Session::flash('class', 'danger');
            Session::flash('idtrans', $idtrans_h);

            return redirect($data['url_menu'])->with($data);
        }
    }
}
