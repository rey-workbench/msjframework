<?php

namespace MSJFramework\LaravelGenerator\Templates\Controllers\Layouts\Master;

class MasterController
{
    public static function getTemplate(array $config): string
    {
        $controllerName = 'MasterController';

        return <<<'PHP'
<?php

namespace App\Http\Controllers;

use App\Helpers\Format_Helper;
use App\Helpers\Function_Helper;
use Illuminate\Contracts\Encryption\DecryptException;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use Illuminate\Validation\Rule;
use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Gd\Driver;
use Illuminate\Support\Str;
use PhpOffice\PhpSpreadsheet\Spreadsheet;

class MasterController extends Controller
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
        // pengecekan file download
        if (request()->has('export') || request()->has('pdf')) {
            $exportType = request()->input('export'); // Mendapatkan tipe ekspor ('excel' atau 'pdf')
            return $this->exportData($data['dmenu'], $exportType, request());
        }
        // function helper
        $data['format'] = new Format_Helper;
        //list data table
        $data['table_header'] = DB::table('sys_table')->where(['gmenu' => $data['gmenuid'], 'dmenu' => $data['dmenu'], 'list' => '1'])->orderBy('urut')->get();
        $data['table_primary'] = DB::table('sys_table')->where(['gmenu' => $data['gmenuid'], 'dmenu' => $data['dmenu'], 'primary' => '1'])->orderBy('urut')->get();
        //check column where in table sys_dmenu
        $getdatawhere = DB::table('sys_dmenu')->select('where')->where('dmenu', $data['dmenu'])->first();
        if ($getdatawhere->where <> '') {
            //check athorization access rules
            $query = DB::table($data['tabel']);
            $sql = $query->toSql();
            if ($data['authorize']->rules == '1') {
                //masukkan query find in set
                $whereRules = implode(' OR ', array_map(function ($role) {
                    return "FIND_IN_SET('$role', REPLACE(rules, ' ', ''))";
                }, array_map('trim', explode(',', $data['user_login']->idroles))));
                //gabungkan query
                $sql = $sql . " " . $getdatawhere->where . "  AND ($whereRules)";
            } else {
                $sql .= ' ' . $getdatawhere->where;
            }
            // get data
            $rawData = DB::select($sql);

            // Mengonversi data mentah ke dalam koleksi
            $collectionData = collect($rawData);
        } else {
            //check athorization access rules
            if ($data['authorize']->rules == '1') {
                $roles = $data['users_rules'];
                $collectionData = DB::table($data['tabel'])
                    ->where(function ($q) use ($roles) {
                        foreach ($roles as $role) {
                            $q->orWhereRaw("FIND_IN_SET(?, REPLACE(rules, ' ', ''))", [$role]);
                        }
                    })
                    ->get();
            } else {
                $collectionData = DB::table($data['tabel'])->get();
            }
        }
        $search = request('search');
        if (!empty($search)) {
            $collectionData = $collectionData->filter(function ($item) use ($search, $data) {
                foreach ($data['table_header'] as $header) {
                    if (stripos($item->{$header->field}, $search) !== false) {
                        return true;
                    }
                }
                return false;
            });
        }

        // Cache the data for future use
        Cache::put('full_table_data_' . $data['dmenu'], $collectionData, 600);
        // Pagination data
        $currentPage = LengthAwarePaginator::resolveCurrentPage();
        $perPage = 10;
        // Slice the data for pagination
        $slicedData = $collectionData->slice(($currentPage - 1) * $perPage, $perPage)->values();
        // Create a paginated instance
        $paginatedData = new LengthAwarePaginator(
            $slicedData,
            $collectionData->count(),
            $perPage,
            $currentPage,
            ['path' => LengthAwarePaginator::resolveCurrentPath()]
        );

        // Add the paginated data to the response
        $data['table_detail'] = $paginatedData;

        // check data table primary
        if ($data['table_primary']) {
            // return page menu
            return view($data['url'], $data);
        } else {
            //if not exist
            $data['url_menu'] = 'error';
            $data['title_group'] = 'Error';
            $data['title_menu'] = 'Error';
            $data['errorpages'] = 'Not Found!';
            //return error page
            return view("pages.errorpages", $data);
        }
    }
    /**
     * Display the specified resource.
     */
    public function add($data)
    {
        // function helper
        $syslog = new Function_Helper;
        //list data table
        $data['table_primary'] = DB::table('sys_table')->where(['gmenu' => $data['gmenuid'], 'dmenu' => $data['dmenu'], 'primary' => '1'])->orderBy('urut')->get();
        $data['table_header'] = DB::table('sys_table')->where(['gmenu' => $data['gmenuid'], 'dmenu' => $data['dmenu'], 'show' => '1'])->orderBy('urut')->get();

        $data['table_header_l'] = DB::table('sys_table')->where(['gmenu' => $data['gmenuid'], 'dmenu' => $data['dmenu'], 'position' => '3', 'show' => '1'])->orderBy('urut')->get();
        $data['table_header_r'] = DB::table('sys_table')->where(['gmenu' => $data['gmenuid'], 'dmenu' => $data['dmenu'], 'position' => '4', 'show' => '1'])->orderBy('urut')->get();
        //check athorization access add
        if ($data['authorize']->add == '1') {
            // return page menu
            return view($data['url'], $data);
        } else {
            //if not athorize
            $data['url_menu'] = $data['url_menu'];
            $data['title_group'] = 'Error';
            $data['title_menu'] = 'Error';
            $data['errorpages'] = 'Not Authorized!';
            //insert log
            $syslog->log_insert('E', $data['url_menu'], 'Not Authorized!' . ' - Add -' . $data['url_menu'], '0');
            //return error page
            return view("pages.errorpages", $data);
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
        //list data table
        $data['table_header'] = DB::table('sys_table')->where(['gmenu' => $data['gmenuid'], 'dmenu' => $data['dmenu'], 'show' => '1'])->orderBy('urut')->get();
        $data['table_primary'] = DB::table('sys_table')->where(['gmenu' => $data['gmenuid'], 'dmenu' => $data['dmenu'], 'primary' => '1'])->orderBy('urut')->get();
        $sys_id = DB::table('sys_id')->where('dmenu', $data['dmenu'])->where('isactive', '1')->orderBy('urut', 'ASC')->first();
        //cek data primary key
        $wherekey = [];
        $multiple = [];
        $idtrans = '';
        foreach ($data['table_primary'] as $key) {
            $wherekey[$key->field] = request()->{$key->field};
            $idtrans = ($idtrans == '') ? $idtrans = request()->{$key->field} : $idtrans . ',' . request()->{$key->field};
        }
        $data_key = DB::table($data['tabel'])->where($wherekey)->first();
        //get data validate
        foreach (
            $data['table_header']->map(function ($item) {
                return (array) $item;
            }) as $item
        ) {
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
            } elseif ($primary && !$data_key) {
                $validate[$item['field']] = '';
            } else {
                $validate[$item['field']] = $item['validate'];
            }
            // check data type multiple
            if (Str::contains($item['class'], 'select-multiple')) {
                // Convert array values ​​to string with comma as separator
                $multiple[$item['field']] = request()->has($item['field']) && !empty(request()->input($item['field']))
                    ? implode(', ', request()->input($item['field']))
                    : null;
            }
        }
        //validasi data
        $attributes = request()->validate(
            $validate,
            [
                'required' => ':attribute tidak boleh kosong',
                'unique' => ':attribute sudah ada',
                'min' => ':attribute minimal :min karakter',
                'max' => ':attribute maksimal :max karakter',
                'email' => 'format :attribute salah',
                'mimes' => ':attribute format harus :values',
                'between' => ':attribute diisi antara :min sampai :max'
            ]
        );
        // check type multiple
        if (isset($multiple)) {
            $keys = array_keys($multiple);
            foreach ($keys as $m) {
                $attributes[$m] = $multiple[$m];
            }
        }
        //check password
        if (isset($attributes['password'])) {
            //encrypt password
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
                    $manager = new ImageManager(new Driver());
                    // read image from file system
                    $image = $manager->read(request()->file($img->field));
                    // resize image proportionally to 35px height
                    $image->scale(height: 35);
                    // save modified image in new format
                    $image->toPng()->save(public_path('storage/' . $filenameimage . 'tumb.png'));
                }
                ;
            }
        }
        //list data
        $data['table_header'] = DB::table('sys_table')->where(['gmenu' => $data['gmenuid'], 'dmenu' => $data['dmenu'], 'list' => '1'])->orderBy('urut')->get();
        $data['table_detail'] = DB::table($data['tabel'])->get();
        $data['table_primary_generate'] = DB::table('sys_table')->where(['gmenu' => $data['gmenuid'], 'dmenu' => $data['dmenu'], 'primary' => '1'])->orderBy('urut')->first();
        //check data Generate ID
        if ($sys_id) {
            //set ID from generate id
            $insert_data = DB::table($data['tabel'])->insert([$data['table_primary_generate']->field => $data['format']->IDFormat($data['dmenu'])] + $attributes + ['user_create' => session('username')]);
        } else {
            //set ID manual
            $insert_data = DB::table($data['tabel'])->insert($attributes + ['user_create' => session('username')]);
        }
        //check insert
        if ($insert_data) {
            //insert sys_log
            $syslog->log_insert('C', $data['dmenu'], 'Created : ' . $idtrans, '1');
            // Set a session message
            Session::flash('message', 'Tambah Data Berhasil!');
            Session::flash('class', 'success');
            // return page menu
            return redirect($data['url_menu'])->with($data);
        } else {
            //insert sys_log
            $syslog->log_insert('E', $data['dmenu'], 'Create Error', '0');
            // Set a session message
            Session::flash('message', 'Tambah Data Gagal!');
            Session::flash('class', 'danger');
            // return page menu
            return redirect($data['url_menu'])->with($data);
        }
        ;
    }
    /**
     * Display the specified resource.
     */
    public function show($data)
    {
        //list data table
        $data['table_header'] = DB::table('sys_table')->where(['gmenu' => $data['gmenuid'], 'dmenu' => $data['dmenu'], 'filter' => '1', 'show' => '1'])->orderBy('urut')->get();
        $data['table_primary'] = DB::table('sys_table')->where(['gmenu' => $data['gmenuid'], 'dmenu' => $data['dmenu'], 'primary' => '1'])->orderBy('urut')->get();

        $data['table_header_l'] = DB::table('sys_table')->where(['gmenu' => $data['gmenuid'], 'dmenu' => $data['dmenu'], 'position' => '3', 'show' => '1', 'filter' => '1'])->orderBy('urut')->get();
        $data['table_header_r'] = DB::table('sys_table')->where(['gmenu' => $data['gmenuid'], 'dmenu' => $data['dmenu'], 'position' => '4', 'show' => '1', 'filter' => '1'])->orderBy('urut')->get();
        //check decrypt
        try {
            $id = decrypt($data['idencrypt']);
        } catch (DecryptException $e) {
            $id = "";
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
            //if not exist
            $data['url_menu'] = 'error';
            $data['title_group'] = 'Error';
            $data['title_menu'] = 'Error';
            $data['errorpages'] = 'Not Found!';
            //return error page
            return view("pages.errorpages", $data);
        }
    }
    /**
     * Show the form for editing the specified resource.
     */
    public function edit($data)
    {
        // function helper
        $syslog = new Function_Helper;
        //list data table
        $data['table_header'] = DB::table('sys_table')->where(['gmenu' => $data['gmenuid'], 'dmenu' => $data['dmenu'], 'filter' => '1', 'show' => '1'])->orderBy('urut')->get();
        $data['table_primary'] = DB::table('sys_table')->where(['gmenu' => $data['gmenuid'], 'dmenu' => $data['dmenu'], 'primary' => '1'])->orderBy('urut')->get();

        $data['table_header_l'] = DB::table('sys_table')->where(['gmenu' => $data['gmenuid'], 'dmenu' => $data['dmenu'], 'position' => '3', 'show' => '1', 'filter' => '1'])->orderBy('urut')->get();
        $data['table_header_r'] = DB::table('sys_table')->where(['gmenu' => $data['gmenuid'], 'dmenu' => $data['dmenu'], 'position' => '4', 'show' => '1', 'filter' => '1'])->orderBy('urut')->get();
        //check decrypt
        try {
            $id = decrypt($data['idencrypt']);
        } catch (DecryptException $e) {
            $id = "";
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
            //check athorization access edit
            if ($data['authorize']->edit == '1') {
                $data['list'] = $list;
                // return page menu
                return view($data['url'], $data);
            } else {
                //if not athorize
                $data['url_menu'] = $data['url_menu'];
                $data['title_group'] = 'Error';
                $data['title_menu'] = 'Error';
                $data['errorpages'] = 'Not Authorized!';
                //insert log
                $syslog->log_insert('E', $data['url_menu'], 'Not Authorized!' . ' - Edit -' . $data['url_menu'], '0');
                //return error page
                return view("pages.errorpages", $data);
            }
        } else {
            //if not exist
            $data['url_menu'] = 'error';
            $data['title_group'] = 'Error';
            $data['title_menu'] = 'Error';
            $data['errorpages'] = 'Not Found!';
            //return error page
            return view("pages.errorpages", $data);
        }
    }
    /**
     * Update the specified resource in storage.
     */
    public function update($data)
    {
        // function helper
        $syslog = new Function_Helper;
        //check decrypt
        try {
            $id = decrypt($data['idencrypt']);
        } catch (DecryptException $e) {
            $id = "";
        }
        //list data table
        $data['table_primary'] = DB::table('sys_table')->where(['gmenu' => $data['gmenuid'], 'dmenu' => $data['dmenu'], 'primary' => '1'])->orderBy('urut')->get();
        // data primary key
        $primaryArray = explode(':', $id);
        $wherekey = [];
        $wherenotkey = [];
        $multiple = [];
        $i = 0;
        foreach ($data['table_primary'] as $key) {
            $wherekey[$key->field] = $primaryArray[$i];
            $wherenotkey[] = $key->field;
            $i++;
        }
        //list data
        $data['table_header'] = DB::table('sys_table')->where(['gmenu' => $data['gmenuid'], 'dmenu' => $data['dmenu'], 'filter' => '1', 'show' => '1'])->whereNotIn('field', $wherenotkey)->orderBy('urut')->get();
        //get data validate
        foreach (
            $data['table_header']->map(function ($item) {
                return (array) $item;
            }) as $item
        ) {
            //If Data Unique
            if ($item['primary'] == '2') {
                $i = 0;
                $rule = [];
                //set Rule Unique
                foreach ($data['table_primary'] as $key) {
                    $rule = array_merge($rule, [Rule::unique($data['tabel'], $item['field'])->ignore($primaryArray[$i], $key->field)]);
                    $i++;
                }
                $primarykey = explode('|', $item['validate']);
                $p = [$primarykey[0]];
                for ($i = 1; $i < count($primarykey); $i++) {
                    (substr($primarykey[$i], 0, 6) != 'unique') ? $p = array_merge($p, [$primarykey[$i]]) : '';
                }
                //set validate
                $validate[$item['field']] = array_merge($p, $rule);
            } else if ($item['field'] == 'password' && request()->email && empty(request()->password)) {
                unset($validate[$item['field']]);
            } else {
                $validate[$item['field']] = $item['validate'];
            }
            // check data type multiple
            if (Str::contains($item['class'], 'select-multiple')) {
                // Convert array values ​​to string with comma as separator
                $multiple[$item['field']] = request()->has($item['field']) && !empty(request()->input($item['field']))
                    ? implode(', ', request()->input($item['field']))
                    : null;
            }
        }
        //validasi data
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
                'regex' => 'Password harus mengandung huruf besar, huruf kecil, angka, dan simbol.'
            ]
        );
        // check type multiple
        if (isset($multiple)) {
            $keys = array_keys($multiple);
            foreach ($keys as $m) {
                $attributes[$m] = $multiple[$m];
            }
        }
        //data password
        if (isset($attributes['password'])) {
            //encryp password
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
                    $manager = new ImageManager(new Driver());
                    // read image from file system
                    $image = $manager->read(request()->file($img->field));
                    // resize image proportionally to 35px height
                    $image->scale(height: 35);
                    // save modified image in new format
                    $image->toPng()->save(public_path('storage/' . $filenameimage . 'tumb.png'));
                }
                ;
            }
        }
        //list data
        $data['table_header'] = DB::table('sys_table')->where(['gmenu' => $data['gmenuid'], 'dmenu' => $data['dmenu'], 'list' => '1'])->orderBy('urut')->get();
        $data['table_detail'] = DB::table($data['tabel'])->get();
        // Update data by id
        $updateData = DB::table($data['tabel'])->where($wherekey)->update($attributes + ['user_update' => session('username')]);
        //check update
        if ($updateData) {
            //insert sys_log
            $syslog->log_insert('U', $data['dmenu'], 'Updated : ' . $id, '1');
            // Set a session message
            Session::flash('message', 'Edit Data Berhasil!');
            Session::flash('class', 'success');
            // return page menu
            return redirect($data['url_menu'])->with($data);
        } else {
            //insert sys_log
            $syslog->log_insert('E', $data['dmenu'], 'Update Error', '0');
            // Set a session message
            Session::flash('message', 'Edit Data Gagal!');
            Session::flash('class', 'danger');
            //return error page
            return redirect($data['url_menu'])->with($data);
        }
        ;
    }
    /**
     * Remove the specified resource from storage.
     */
    public function destroy($data)
    {
        // function helper
        $syslog = new Function_Helper;
        //list data
        $data['table_primary'] = DB::table('sys_table')->where(['gmenu' => $data['gmenuid'], 'dmenu' => $data['dmenu'], 'primary' => '1'])->orderBy('urut')->get();
        $data['table_header'] = DB::table('sys_table')->where(['gmenu' => $data['gmenuid'], 'dmenu' => $data['dmenu'], 'list' => '1'])->orderBy('urut')->get();
        $data['table_detail'] = DB::table($data['tabel'])->get();
        //check decrypt
        try {
            $id = decrypt($data['idencrypt']);
        } catch (DecryptException $e) {
            $id = "";
        }
        // data primary key
        $primaryArray = explode(':', $id);
        $wherekey = [];
        $i = 0;
        foreach ($data['table_primary'] as $key) {
            $wherekey[$key->field] = $primaryArray[$i];
            $i++;
        }
        // list data update
        $list = DB::table($data['tabel'])->where($wherekey)->first();
        // Update data by id
        $updateData = DB::table($data['tabel'])->where($wherekey)->update(['isactive' => ($list->isactive == '1') ? '0' : '1']);
        // check update
        if ($updateData) {
            //insert sys_log
            $syslog->log_insert('D', $data['dmenu'], ($list->isactive == '1') ? 'Deleted : ' . $id : 'UnDeleted : ' . $id, '1');
            // Set a session message
            Session::flash('message', ($list->isactive == '0') ? 'Aktif Data Berhasil!' : 'NonAktif Data Berhasil!');
            Session::flash('class', 'success');
            // return page menu
            return redirect($data['url_menu'])->with($data);
        } else {
            //insert sys_log
            $syslog->log_insert('E', $data['dmenu'], ($list->isactive == '1') ? 'Delete Error : ' . $id : 'UnDelete Error : ' . $id, '0');
            // Set a session message
            Session::flash('message', ($list->isactive == '0') ? 'Aktif Data Gagal!' : 'NonAktif Data Gagal!');
            Session::flash('class', 'danger');
            //return error page
            return redirect($data['url_menu'])->with($data);
        }
    }
    private function exportData($dmenu, $exportType)
    {
        // Ambil metadata tabel dari `sys_dmenu`
        $sysDmenu = DB::table('sys_dmenu')->where('dmenu', $dmenu)->first();
        if (!$sysDmenu || empty($sysDmenu->tabel)) {
            return redirect()->back()->with('error', 'Tabel tidak ditemukan!');
        }
        // Ambil metadata header dari cache atau database
        $headersCacheKey = 'headers_' . $dmenu;
        $headers = Cache::remember($headersCacheKey, 600, function () use ($dmenu) {
            return DB::table('sys_table')
                ->where('dmenu', $dmenu)
                ->where('list', '1')
                ->orderBy('urut')
                ->get(['field', 'alias', 'query']);
        });
        // Ambil data tabel utama dari cache
        $cacheKey = 'full_table_data_' . $dmenu;
        $mainData = Cache::remember($cacheKey, 600, function () use ($sysDmenu) {
            return DB::table($sysDmenu->tabel)->get();
        });
        // Prefetch mapping untuk semua header dengan query
        $mappings = [];
        foreach ($headers as $header) {
            if (!empty($header->query)) {
                $mappingCacheKey = 'mapping_' . $header->field;
                $mappings[$header->field] = Cache::remember($mappingCacheKey, 600, function () use ($header) {
                    $mapping = collect(DB::select($header->query));
                    if ($mapping->isNotEmpty()) {
                        // Ambil kolom pertama sebagai key, kedua sebagai value
                        $columns = array_keys((array) $mapping->first());
                        $keyColumn = $columns[0] ?? null;
                        $valueColumn = $columns[1] ?? null;

                        return $keyColumn && $valueColumn
                            ? $mapping->pluck($valueColumn, $keyColumn)
                            : [];
                    }
                    return [];
                });
            }
        }
        $NoUrut = 0;
        // Transformasi data berdasarkan metadata header
        $transformedData = $mainData->map(function ($row) use ($headers, $mappings, &$NoUrut) {
            $transformedRow = [];
            $NoUrut++;
            $transformedRow = ['No' => $NoUrut]; // Tambahkan ke array hasil
            foreach ($headers as $header) {
                $field = $header->field;
                if (isset($mappings[$field]) && $mappings[$field]->isNotEmpty()) {
                    // Gunakan mapping jika tersedia
                    $transformedRow[$header->alias] = $mappings[$field][$row->$field] ?? $row->$field;
                } else {
                    // Gunakan nilai asli jika mapping tidak tersedia
                    $transformedRow[$header->alias] = $row->$field;
                }
            }
            return $transformedRow;
        });
        // Tambahkan "No" ke dalam daftar headers
        $headersArray = array_merge(['No'], $headers->pluck('alias')->toArray());
        // Ekspor data ke PDF atau Excel
        return $this->exportToSpreadsheet($transformedData, $headersArray, $sysDmenu->name, $exportType);
    }

    private function exportToSpreadsheet($data, $headers, $fileName, $exportType)
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        // Tambahkan header ke baris pertama
        $sheet->fromArray($headers, null, 'A2');
        // Tambahkan data ke baris berikutnya
        $sheet->fromArray($data->toArray(), null, 'A3');
        // Ambil kolom dan baris tertinggi
        $highestColumn = $sheet->getHighestColumn();
        $highestRow = $sheet->getHighestRow();
        // Mengatur judul di baris pertama
        $sheet->mergeCells("A1:{$highestColumn}1"); // Sesuaikan dengan jumlah kolom
        $sheet->setCellValue('A1', $fileName); // Isi judul
        $sheet->getStyle('A1')->applyFromArray([
            'font' => [
                'size' => 14,
                'bold' => true,
            ],
            'alignment' => [
                'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
            ],
        ]);
        // Atur gaya untuk header
        $sheet->getStyle("A2:{$highestColumn}2")->applyFromArray([
            'font' => [
                'bold' => true,
                'size' => 12,
            ],
            'fill' => [
                'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                'startColor' => [
                    'rgb' => '708090',
                ],
            ]
        ]);
        // Atur gaya untuk isi data
        for ($row = 3; $row <= $highestRow; $row++) {
            $fillColor = ($row % 2 == 0) ? 'ECF0F1' : 'FFFFFF';
            $sheet->getStyle("A{$row}:{$highestColumn}{$row}")->applyFromArray([
                'font' => [
                    'size' => 11,
                ],
                'fill' => [
                    'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                    'startColor' => [
                        'rgb' => $fillColor,
                    ],
                ],
                'alignment' => [
                    'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_LEFT,
                    'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
                    'wrapText' => true,
                ],
            ]);
        }
        // total lebar kolom
        $totalWidth = 0;
        // Atur lebar kolom agar lebih lebar
        foreach (range('A', $highestColumn) as $column) {
            $maxLength = 0;
            foreach ($sheet->getRowIterator() as $row) {
                $cell = $sheet->getCell($column . $row->getRowIndex());
                $cellValue = $cell->getValue();
                $cellLength = strlen($cellValue);
                if ($cellLength > $maxLength) {
                    $maxLength = $cellLength;
                }
            }
            ($column == 'A') ? $maxLength = 2 : ''; //set width kolon No urut
            $sheet->getColumnDimension($column)->setWidth($maxLength + 5); // Tambahkan 5 karakter ekstra
            $totalWidth += $sheet->getColumnDimension($column)->getWidth(); // Ambil total lebar kolom
        }
        // Terapkan AutoFilter ke seluruh header (baris ke-2)
        $sheet->setAutoFilter("A2:{$highestColumn}2");
        //check type export
        if ($exportType === 'pdf') {
            $maxWidthForPortrait = 100; // Tetapkan batas lebar untuk A4 potrait dalam unit PhpSpreadsheet (misal 100)
            // Jika jumlah kolom lebih dari batas, ubah orientasi menjadi landscape
            $orientation = ($totalWidth > $maxWidthForPortrait)
                ? \PhpOffice\PhpSpreadsheet\Worksheet\PageSetup::ORIENTATION_LANDSCAPE
                : \PhpOffice\PhpSpreadsheet\Worksheet\PageSetup::ORIENTATION_PORTRAIT;
            // Konfigurasi untuk PDF
            $sheet->getPageSetup()
                ->setOrientation($orientation)
                ->setPaperSize(\PhpOffice\PhpSpreadsheet\Worksheet\PageSetup::PAPERSIZE_A4); // Gunakan kertas lebih besar
            $sheet->getPageMargins()->setTop(0.5)->setRight(0.5)->setLeft(0.5)->setBottom(0.5);
            // Gunakan pengaturan skala
            $sheet->getPageSetup()->setScale(150); // Perbesar skala hingga 150%
            $writer = new \PhpOffice\PhpSpreadsheet\Writer\Pdf\Mpdf($spreadsheet);
            $filePath = public_path("{$fileName}.pdf");
            $writer->save($filePath);
            return response()->download($filePath)->deleteFileAfterSend(true);
        }
        if ($exportType === 'excel') {
            $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
            $filePath = public_path("{$fileName}.xlsx");
            $writer->save($filePath);
            return response()->download($filePath)->deleteFileAfterSend(true);
        }
        //export error
        return redirect()->back()->with('error', 'Tipe ekspor tidak valid!');
    }
}
PHP;
    }
}
