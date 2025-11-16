<?php

namespace MSJFramework\LaravelGenerator\Services\Templates\Controllers\Auto;

use Illuminate\Support\Str;

class StandrControllerTemplate
{
    public static function getTemplate(array $config): string
    {
        $controllerName = 'StandrController';

        return <<<'PHP'
<?php

namespace App\Http\Controllers;

use App\Helpers\Format_Helper;
use App\Helpers\Function_Helper;
use Illuminate\Contracts\Encryption\DecryptException;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Intervention\Image\Drivers\Gd\Driver;
use Intervention\Image\ImageManager;

class StandrController extends Controller
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
        $data['table_header'] = DB::table('sys_table')->where(['gmenu' => $data['gmenuid'], 'dmenu' => $data['dmenu'], 'list' => '1'])->get();
        $data['table_primary'] = DB::table('sys_table')->where(['gmenu' => $data['gmenuid'], 'dmenu' => $data['dmenu'], 'primary' => '1'])->first();
        // check column where in table sys_dmenu
        $getdatawhere = DB::table('sys_dmenu')->select('where')->where('dmenu', $data['dmenu'])->first();
        if ($getdatawhere->where != '') {
            // check athorization access rules
            $query = DB::table($data['tabel']);
            $sql = $query->toSql();
            if ($data['authorize']->rules == '1') {
                // masukkan query find in set
                $whereRules = implode(' OR ', array_map(function ($role) {
                    return "FIND_IN_SET('$role', REPLACE(rules, ' ', ''))";
                }, array_map('trim', explode(',', $data['user_login']->idroles))));
                // gabungkan query
                $sql = $sql.' '.$getdatawhere->where."  AND ($whereRules)";
            } else {
                $sql .= ' '.$getdatawhere->where;
            }
            // get data
            $data['table_detail'] = DB::select($sql);
        } else {
            // check athorization access rules
            if ($data['authorize']->rules == '1') {
                $roles = $data['users_rules'];
                $data['table_detail'] = DB::table($data['tabel'])
                    ->where(function ($q) use ($roles) {
                        foreach ($roles as $role) {
                            $q->orWhereRaw("FIND_IN_SET(?, REPLACE(rules, ' ', ''))", [$role]);
                        }
                    })
                    ->get();
            } else {
                $data['table_detail'] = DB::table($data['tabel'])->get();
            }
        }
        // check data table primary
        if ($data['table_primary']) {
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
     * Display the specified resource.
     */
    public function add($data)
    {
        // function helper
        $syslog = new Function_Helper;
        // list data table
        $data['table_primary'] = DB::table('sys_table')->where(['gmenu' => $data['gmenuid'], 'dmenu' => $data['dmenu'], 'primary' => '1'])->first();
        $data['table_header'] = DB::table('sys_table')->where(['gmenu' => $data['gmenuid'], 'dmenu' => $data['dmenu'], 'show' => '1'])->get();

        $data['table_header_l'] = DB::table('sys_table')->where(['gmenu' => $data['gmenuid'], 'dmenu' => $data['dmenu'], 'position' => '3', 'show' => '1'])->orderBy('urut')->get();
        $data['table_header_r'] = DB::table('sys_table')->where(['gmenu' => $data['gmenuid'], 'dmenu' => $data['dmenu'], 'position' => '4', 'show' => '1'])->orderBy('urut')->get();

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
        $data['table_header'] = DB::table('sys_table')->where(['gmenu' => $data['gmenuid'], 'dmenu' => $data['dmenu'], 'show' => '1'])->get();
        $data['table_primary'] = DB::table('sys_table')->where(['gmenu' => $data['gmenuid'], 'dmenu' => $data['dmenu'], 'primary' => '1'])->first();
        $sys_id = DB::table('sys_id')->where('dmenu', $data['dmenu'])->where('isactive', '1')->orderBy('urut', 'ASC')->first();
        $multiple = [];
        // get data validate
        foreach (
            $data['table_header']->map(function ($item) {
                return (array) $item;
            }) as $item
        ) {
            if ($item['field'] == $data['table_primary']->field && $sys_id) {
                $validate[$item['field']] = '';
            } else {
                $validate[$item['field']] = $item['validate'];
            }
            // check data type multiple
            if (Str::contains($item['class'], 'select-multiple')) {
                // Convert array values ​​to string with comma as separator
                $multiple[$item['field']] = request()->has($item['field']) && ! empty(request()->input($item['field']))
                    ? implode(', ', request()->input($item['field']))
                    : null;
            }
        }
        // validation data
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
        // check type multiple
        if (isset($multiple)) {
            $keys = array_keys($multiple);
            foreach ($keys as $m) {
                $attributes[$m] = $multiple[$m];
            }
        }
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
                $filenameimage = request()->file($img->field)->store($data['tabel']);
                $attributes[$img->field] = $filenameimage;
                if ($img->type == 'image') {
                    // create image manager with desired driver
                    $manager = new ImageManager(new Driver);
                    // read image from file system
                    $image = $manager->read(request()->file($img->field));
                    // resize image proportionally to 35px height
                    $image->scale(height: 35);
                    // save modified image in new format
                    $image->toPng()->save(public_path('storage/'.$filenameimage.'tumb.png'));
                }
            }
        }
        // list data
        $data['table_header'] = DB::table('sys_table')->where(['gmenu' => $data['gmenuid'], 'dmenu' => $data['dmenu'],  'list' => '1'])->get();
        $data['table_detail'] = DB::table($data['tabel'])->get();
        // check data Generate ID
        if ($sys_id) {
            // set ID from generate id
            $insert_data = DB::table($data['tabel'])->insert([$data['table_primary']->field => $data['format']->IDFormat($data['dmenu'])] + $attributes + ['user_create' => session('username')]);
        } else {
            // set ID manual
            $insert_data = DB::table($data['tabel'])->insert($attributes + ['user_create' => session('username')]);
        }
        // check insert
        if ($insert_data) {
            // insert sys_log
            $idtrans = DB::table($data['tabel'])->where(['user_create' => session('username')])->orderByDesc('created_at')->first();
            $syslog->log_insert('C', $data['dmenu'], 'Created : '.$idtrans->{$data['table_primary']->field}, '1');
            // Set a session message
            Session::flash('message', 'Tambah Berhasil!');
            Session::flash('class', 'success');

            // return page menu
            return redirect($data['url_menu'])->with($data);
        } else {
            // insert sys_log
            $syslog = new Function_Helper;
            $syslog->log_insert('E', $data['dmenu'], 'Create Error', '0');
            // Set a session message
            Session::flash('message', 'Tambah Gagal!');
            Session::flash('class', 'danger');

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
        $data['table_header'] = DB::table('sys_table')->where(['gmenu' => $data['gmenuid'], 'dmenu' => $data['dmenu'],  'filter' => '1', 'show' => '1'])->get();
        $data['table_primary'] = DB::table('sys_table')->where(['gmenu' => $data['gmenuid'], 'dmenu' => $data['dmenu'], 'primary' => '1'])->first();

        $data['table_header_l'] = DB::table('sys_table')->where(['gmenu' => $data['gmenuid'], 'dmenu' => $data['dmenu'], 'position' => '3', 'show' => '1', 'filter' => '1'])->orderBy('urut')->get();
        $data['table_header_r'] = DB::table('sys_table')->where(['gmenu' => $data['gmenuid'], 'dmenu' => $data['dmenu'], 'position' => '4', 'show' => '1', 'filter' => '1'])->orderBy('urut')->get();

        // check decrypt
        try {
            $id = decrypt($data['idencrypt']);
        } catch (DecryptException $e) {
            $id = '';
        }
        // data list where primary key
        $list = DB::table($data['tabel'])->where($data['table_primary']->field, $id)->first();
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
        $data['table_header'] = DB::table('sys_table')->where(['gmenu' => $data['gmenuid'], 'dmenu' => $data['dmenu'],  'filter' => '1', 'show' => '1'])->get();
        $data['table_primary'] = DB::table('sys_table')->where(['gmenu' => $data['gmenuid'], 'dmenu' => $data['dmenu'], 'primary' => '1'])->first();

        $data['table_header_l'] = DB::table('sys_table')->where(['gmenu' => $data['gmenuid'], 'dmenu' => $data['dmenu'], 'position' => '3', 'show' => '1', 'filter' => '1'])->orderBy('urut')->get();
        $data['table_header_r'] = DB::table('sys_table')->where(['gmenu' => $data['gmenuid'], 'dmenu' => $data['dmenu'], 'position' => '4', 'show' => '1', 'filter' => '1'])->orderBy('urut')->get();

        // check decrypt
        try {
            $id = decrypt($data['idencrypt']);
        } catch (DecryptException $e) {
            $id = '';
        }
        // data list where primary key
        $list = DB::table($data['tabel'])->where($data['table_primary']->field, $id)->first();
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
        // list data table
        $data['table_primary'] = DB::table('sys_table')->where(['gmenu' => $data['gmenuid'], 'dmenu' => $data['dmenu'], 'primary' => '1'])->first();
        $data['table_header'] = DB::table('sys_table')->where(['gmenu' => $data['gmenuid'], 'dmenu' => $data['dmenu'], ['field', '<>', $data['table_primary']->field],  'filter' => '1', 'show' => '1'])->get();
        // check decrypt
        try {
            $id = decrypt($data['idencrypt']);
        } catch (DecryptException $e) {
            $id = '';
        }
        $multiple = [];
        // get data validate
        foreach (
            $data['table_header']->map(function ($item) {
                return (array) $item;
            }) as $item
        ) {
            if ($item['primary'] == '2') {
                $primarykey = explode('|', $item['validate']);
                $p = [$primarykey[0]];
                for ($i = 1; $i < count($primarykey); $i++) {
                    (substr($primarykey[$i], 0, 6) != 'unique') ? $p = array_merge($p, [$primarykey[$i]]) : '';
                }
                $validate[$item['field']] = array_merge($p, [Rule::unique($data['tabel'], $item['field'])->ignore($id, $data['table_primary']->field)]);
            } elseif ($item['field'] == 'password' && request()->email && empty(request()->password)) {
                unset($validate[$item['field']]);
            } else {
                $validate[$item['field']] = $item['validate'];
            }
            // check data type multiple
            if (Str::contains($item['class'], 'select-multiple')) {
                // Convert array values ​​to string with comma as separator
                $multiple[$item['field']] = request()->has($item['field']) && ! empty(request()->input($item['field']))
                    ? implode(', ', request()->input($item['field']))
                    : null;
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
                'regex' => 'Password harus mengandung huruf besar, huruf kecil, angka, dan simbol.',
            ]
        );
        // check type multiple
        if (isset($multiple)) {
            $keys = array_keys($multiple);
            foreach ($keys as $m) {
                $attributes[$m] = $multiple[$m];
            }
        }
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
                $filenameimage = request()->file($img->field)->store($data['tabel']);
                $attributes[$img->field] = $filenameimage;
                if ($img->type == 'image') {
                    // create image manager with desired driver
                    $manager = new ImageManager(new Driver);
                    // read image from file system
                    $image = $manager->read(request()->file($img->field));
                    // resize image proportionally to 35px height
                    $image->scale(height: 35);
                    // save modified image in new format
                    $image->toPng()->save(public_path('storage/'.$filenameimage.'tumb.png'));
                }
            }
        }
        // list data
        $data['table_header'] = DB::table('sys_table')->where(['gmenu' => $data['gmenuid'], 'dmenu' => $data['dmenu'],  'list' => '1'])->get();
        $data['table_detail'] = DB::table($data['tabel'])->get();
        // Update data by id
        $updateData = DB::table($data['tabel'])->where($data['table_primary']->field, $id)->update($attributes + ['user_update' => session('username')]);
        // check update
        if ($updateData) {
            // insert sys_log
            $syslog->log_insert('U', $data['dmenu'], 'Updated : '.$id, '1');
            // Set a session message
            Session::flash('message', 'Edit Data Berhasil!');
            Session::flash('class', 'success');

            // return page menu
            return redirect($data['url_menu'])->with($data);
        } else {
            // insert sys_log
            $syslog->log_insert('E', $data['dmenu'], 'Update Error', '0');
            // Set a session message
            Session::flash('message', 'Edit Data Gagal!');
            Session::flash('class', 'danger');

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
        // list data
        $data['table_primary'] = DB::table('sys_table')->where(['gmenu' => $data['gmenuid'], 'dmenu' => $data['dmenu'], 'primary' => '1'])->first();
        $data['table_header'] = DB::table('sys_table')->where(['gmenu' => $data['gmenuid'], 'dmenu' => $data['dmenu'],  'list' => '1'])->get();
        $data['table_detail'] = DB::table($data['tabel'])->get();
        // check decrypt
        try {
            $id = decrypt($data['idencrypt']);
        } catch (DecryptException $e) {
            $id = '';
        }
        // list data update
        $list = DB::table($data['tabel'])->where($data['table_primary']->field, $id)->first();
        // Update data by id
        $updateData = DB::table($data['tabel'])->where($data['table_primary']->field, $id)->update(['isactive' => ($list->isactive == '1') ? '0' : '1']);
        // check update
        if ($updateData) {
            // insert sys_log
            $syslog->log_insert('D', $data['dmenu'], ($list->isactive == '1') ? 'Deleted : '.$id : 'UnDelete : '.$id, '1');
            // Set a session message
            Session::flash('message', ($list->isactive == '0') ? 'Aktif Data Berhasil!' : 'NonAktif Data Berhasil!');
            Session::flash('class', 'success');

            // return page menu
            return redirect($data['url_menu'])->with($data);
        } else {
            // insert sys_log
            $syslog->log_insert('E', $data['dmenu'], ($list->isactive == '1') ? 'Delete Error : '.$id : 'UnDelete Error : '.$id, '0');
            // Set a session message
            Session::flash('message', ($list->isactive == '0') ? 'Aktif Data Gagal!' : 'NonAktif Data Gagal!');
            Session::flash('class', 'danger');

            // return error page
            return redirect($data['url_menu'])->with($data);
        }
    }
}
PHP;
    }
}
