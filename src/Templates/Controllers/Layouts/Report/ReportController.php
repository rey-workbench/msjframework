<?php

namespace MSJFramework\LaravelGenerator\Templates\Controllers\Layouts\Report;

use Illuminate\Support\Str;

class TranscControllerTemplate
{
    public static function getTemplate(array $config): string
    {
        $controllerName = 'ReportController';

        return <<<'PHP'
<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;

class ReportController extends Controller
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
        // list data table
        $data['table_filter'] = DB::table('sys_table')->where(['gmenu' => $data['gmenuid'], 'dmenu' => $data['dmenu'], 'filter' => '1'])->get();
        // check data filter
        if ($data['table_filter']) {
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
     * Store a newly created resource in storage.
     */
    public function store($data)
    {
        // list data
        $data['table_filter'] = DB::table('sys_table')->where(['gmenu' => $data['gmenuid'], 'dmenu' => $data['dmenu'], 'filter' => '1'])->get();
        $data['table_query'] = DB::table('sys_table')->where(['gmenu' => $data['gmenuid'], 'dmenu' => $data['dmenu'], 'field' => 'query'])->first();
        // get data validate
        foreach ($data['table_filter']->map(function ($item) {
            return (array) $item;
        }) as $item) {
            if ($item['type'] == 'date2') {
                if ($item['validate'] == null) {
                    $validate['fr'.$item['field']] = '';
                    $validate['to'.$item['field']] = '';
                } else {
                    $validate['fr'.$item['field']] = $item['validate'];
                    $validate['to'.$item['field']] = $item['validate'];
                }
            } else {
                if ($item['validate'] == null) {
                    $validate[$item['field']] = '';
                } else {
                    $validate[$item['field']] = $item['validate'];
                }
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
                'mimes' => ':attribute file harus format png,jpg,jpeg',
                'between' => ':attribute diisi antara :min sampai :max',
            ]
        );
        $where = [];
        foreach ($attributes as $column => $value) {
            if ($value == null) {
                if (substr($column, 0, 2) == 'fr') {
                    $where[$column] = '2000-01-01';
                } elseif (substr($column, 0, 2) == 'to') {
                    $where[$column] = '2100-01-01';
                } else {
                    $where[$column] = '%';
                }
            } else {
                $where[$column] = "{$value}";
            }
        }
        // check authorization
        if (@$where['rules']) {
            // check athorization access rules
            if ($data['authorize']->rules == '1') {
                // Check if user session exists
                if (session('user') && session('user')->idroles) {
                    $where['rules'] = session('user')->idroles;
                } else {
                    return redirect('/login')->withErrors(['error' => 'Session expired. Please login again.']);
                }
            } else {
                $where['rules'] = '%';
            }
        }
        // list data
        $data['table_result'] = DB::select($data['table_query']->query, $where);
        $data['table_class'] = DB::table('sys_table')->where(['gmenu' => $data['gmenuid'], 'dmenu' => $data['dmenu'], 'class' => 'filter'])->first();
        $data['filter'] = $where;
        // check data result
        if ($data['table_result']) {
            // return page menu
            return view($data['url'], $data);
        } else {
            // return page menu
            return view($data['url'], $data);
        }
    }

    /**
     * Display the specified resource.
     */
    public function result($data)
    {
        if ($data['table_result']) {
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
}
PHP;
    }
}
