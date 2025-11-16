<?php

namespace MSJFramework\LaravelGenerator\Templates\Controllers\Base;

class PageController
{
    public static function getTemplate(): string
    {
        return <<<'PHP'
<?php

namespace App\Http\Controllers;

use App\Helpers\Function_Helper;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use Illuminate\Validation\Rule;

class PageController extends Controller
{
    public function index(string $page, string $action = 'index', string $id = '-')
    {
        $syslog = new Function_Helper;
        $data['user_login'] = User::find(session('username'));

        if (! $data['user_login']) {
            auth()->logout();
            session()->invalidate();
            session()->regenerateToken();
            return redirect('/login')->withErrors(['error' => 'Session expired. Please login again.']);
        }

        $users_rules = array_map('trim', explode(',', $data['user_login']->idroles));
        $data['users_rules'] = array_map('trim', explode(',', $data['user_login']->idroles));
        $data['setup_app'] = DB::table('sys_app')->where('isactive', '1')->first();
        
        $pages = 'pages';
        $filename = 'list';
        $data['idencrypt'] = $action;
        ($id != '-') ? $data['idencrypt'] = $id : '';
        ($action != 'index') ? ((request()->isMethod('DELETE')) ? $action = 'destroy' : ((request()->isMethod('PUT')) ? $action = 'update' : $filename = $action)) : ((request()->isMethod('POST')) ? $action = 'store' : '');
        
        $data['gmenu'] = DB::table('sys_gmenu')
            ->join('sys_auth', 'sys_gmenu.gmenu', '=', 'sys_auth.gmenu')
            ->whereIn('sys_auth.idroles', $users_rules)
            ->where('sys_gmenu.isactive', '1')
            ->select('sys_gmenu.*')
            ->distinct()
            ->orderBy('urut')
            ->get();
        
        $menu = DB::table('sys_gmenu')
            ->join('sys_auth', 'sys_gmenu.gmenu', '=', 'sys_auth.gmenu')
            ->join('sys_dmenu', 'sys_gmenu.gmenu', '=', 'sys_dmenu.gmenu')
            ->where('sys_dmenu.url', $page)
            ->select('sys_gmenu.gmenu as gmenu', 'sys_gmenu.name as gname', 'sys_dmenu.name as dname', 'sys_dmenu.layout as layout', 'sys_dmenu.url as url', 'sys_dmenu.dmenu as dmenu', 'sys_dmenu.tabel as tabel', 'sys_dmenu.js as jsmenu')
            ->first();
        
        if ($menu) {
            if ($menu->layout != 'manual') {
                $pages = $menu->layout . '.auto';
            } else {
                if ($menu->gname == '-') {
                    $pages = $pages . '.' . $menu->url;
                } else {
                    $pages = $menu->gmenu . '.' . $menu->url;
                }
            }
            if ($menu->layout == 'report' || $menu->gmenu == 'report') {
                if ($action == 'store') {
                    $filename = 'result';
                } else {
                    $filename = 'filter';
                }
            }
            if ($action == 'ajax' || $action == 'api') {
                $filename = 'list';
            }
        }
        
        if (view()->exists("{$pages}.{$filename}")) {
            $auth = DB::table('sys_dmenu')
                ->join('sys_auth', 'sys_auth.dmenu', '=', 'sys_dmenu.dmenu')
                ->where('sys_auth.isactive', '1')
                ->where('sys_dmenu.url', $page)
                ->whereIn('sys_auth.idroles', $users_rules)
                ->first();
            
            if ($auth) {
                $data['url'] = $pages . '.' . $filename;
                $data['url_menu'] = $page;
                $data['title_group'] = $menu->gname;
                $data['title_menu'] = $menu->dname;
                $data['dmenu'] = $menu->dmenu;
                $data['tabel'] = $menu->tabel;
                $data['gmenuid'] = $menu->gmenu;
                $data['jsmenu'] = $menu->jsmenu;
                
                $data['authorize'] = DB::table('sys_auth')->where(['gmenu' => $data['gmenuid'], 'dmenu' => $data['dmenu']])->whereIn('idroles', $users_rules)->first();
                ($menu->layout != 'manual') ? $page = $menu->layout : '';
                
                if ($action == 'api') {
                    $page = $menu->url;
                    $action = $id;
                }
                $vcontroller = "App\Http\Controllers\\" . ucfirst($page) . 'Controller';
                
                if ($action == 'index' && ! Session::has('message')) {
                    $syslog->log_insert('V', $data['dmenu'], 'Akses Menu' . ' - ' . $menu->gname . ' -> ' . $menu->dname, '1');
                }
                
                $chec_query = new Function_Helper;
                $result = $chec_query->check_query($data['gmenuid'], $data['dmenu'], $data);
                if ($result !== true) {
                    $syslog->log_insert('E', $data['dmenu'], 'Error Query Akses Menu' . ' - ' . $menu->gname . ' -> ' . $menu->dname, '0');
                    return $result;
                }

                return app($vcontroller)->$action($data);
            } else {
                $data['url_menu'] = $page;
                $data['title_group'] = 'Error';
                $data['title_menu'] = 'Error';
                $data['errorpages'] = 'Not Authorized!';
                $syslog->log_insert('E', $page, 'Not Authorized!' . ' - ' . $page, '0');
                return view('pages.errorpages', $data);
            }
        } else {
            $data['url_menu'] = $page;
            $data['title_group'] = 'Error';
            $data['title_menu'] = 'Error';
            $data['errorpages'] = 'Not Found!';
            ($page != 'assets') ? $syslog->log_insert('E', $page, 'Not Found!' . ' - ' . $page, '0') : '';
            return view('pages.errorpages', $data);
        }
    }

    public function profile()
    {
        $syslog = new Function_Helper;
        $data['user_login'] = User::find(session('username'));
        $users_rules = array_map('trim', explode(',', $data['user_login']->idroles));
        $data['users_rules'] = array_map('trim', explode(',', $data['user_login']->idroles));
        $data['setup_app'] = DB::table('sys_app')->where('isactive', '1')->first();
        
        $data['gmenu'] = DB::table('sys_gmenu')
            ->join('sys_auth', 'sys_gmenu.gmenu', '=', 'sys_auth.gmenu')
            ->whereIn('sys_auth.idroles', $users_rules)
            ->where('sys_gmenu.isactive', '1')
            ->select('sys_gmenu.*')
            ->distinct()
            ->get();
        
        $data['url_menu'] = '';
        $data['title_group'] = 'Akun';
        $data['title_menu'] = 'Akun Saya';
        
        if (! Session::has('message')) {
            $syslog->log_insert('V', 'Profile', 'Akses Menu -> Profile', '1');
        }

        return view('pages.profile', $data);
    }

    public function update()
    {
        $syslog = new Function_Helper;
        $attributes = request()->validate(
            [
                'profile_email' => [
                    'required',
                    'email',
                    'max:255',
                    Rule::unique('users', 'email')->ignore(session('username'), 'username'),
                ],
                'profile_firstname' => 'required|max:255|min:2',
                'profile_lastname' => 'required|max:255|min:2',
                'profile_image' => 'mimes:png,PNG,jpg,JPG,jpeg,JPEG|file|max:2048',
            ],
            [
                'required' => ':attribute tidak boleh kosong',
                'unique' => ':attribute sudah ada',
                'min' => ':attribute minimal :min karakter',
                'max' => ':attribute maksimal :max karakter',
                'mimes' => ':attribute file harus format png,jpg,jpeg',
            ]
        );
        
        if (request()->file('profile_image')) {
            $attributes['profile_image'] = request()->file('profile_image')->store('profile', 'public');
        } else {
            unset($attributes['profile_image']);
        }
        
        $user = User::find(session('username'));
        if ($user) {
            $user->email = $attributes['profile_email'];
            $user->firstname = $attributes['profile_firstname'];
            $user->lastname = $attributes['profile_lastname'];
            if (request()->file('profile_image')) {
                $user->image = $attributes['profile_image'];
            }
            if ($user->save()) {
                $syslog->log_insert('U', 'Profile', 'Edit User Berhasil!', '1');
                Session::flash('message', 'Edit User Berhasil!');
                Session::flash('class', 'success');
                return redirect('/profile');
            } else {
                $syslog->log_insert('E', 'Profile', 'Edit User Gagal!', '0');
                Session::flash('message', 'Edit User Gagal!');
                Session::flash('class', 'danger');
                return redirect('/profile');
            }
        } else {
            $syslog->log_insert('E', 'Profile', 'Edit User Gagal Diproses!', '0');
            Session::flash('message', 'Edit User Gagal Diproses!');
            Session::flash('class', 'danger');
            return redirect('/profile');
        }
    }

    public function changepass()
    {
        $syslog = new Function_Helper;
        $data['user_login'] = User::find(session('username'));
        $users_rules = array_map('trim', explode(',', $data['user_login']->idroles));
        $data['users_rules'] = array_map('trim', explode(',', $data['user_login']->idroles));
        $data['setup_app'] = DB::table('sys_app')->where('isactive', '1')->first();
        
        $data['gmenu'] = DB::table('sys_gmenu')
            ->join('sys_auth', 'sys_gmenu.gmenu', '=', 'sys_auth.gmenu')
            ->whereIn('sys_auth.idroles', $users_rules)
            ->where('sys_gmenu.isactive', '1')
            ->select('sys_gmenu.*')
            ->distinct()
            ->get();
        
        $data['url_menu'] = '';
        $data['title_group'] = 'Akun';
        $data['title_menu'] = 'Ganti Password';
        
        if (! Session::has('message')) {
            $syslog->log_insert('V', 'Changepass', 'Akses Menu -> Change Password', '1');
        }

        return view('pages.changepass', $data);
    }

    public function changepass_update()
    {
        $syslog = new Function_Helper;
        $attributes = request()->validate(
            [
                'new_password' => 'required|max:255|min:6|regex:/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[\W_]).+$/',
                'confirm_password' => 'required|same:new_password',
            ],
            [
                'required' => ':attribute tidak boleh kosong',
                'min' => ':attribute minimal :min karakter',
                'max' => ':attribute maksimal :max karakter',
                'same' => ':attribute harus sama dengan password baru',
                'regex' => 'Password harus mengandung huruf besar, huruf kecil, angka, dan simbol.',
            ]
        );
        
        $user = User::find(session('username'));
        $password = $attributes['new_password'];
        
        if ($user) {
            $user->password = $password;
            if ($user->update()) {
                $syslog->log_insert('U', 'Changepass', 'Edit Password Berhasil!', '1');
                Session::flash('message', 'Edit Password Berhasil!');
                Session::flash('class', 'success');
                return redirect('/changepass');
            } else {
                $syslog->log_insert('E', 'Changepass', 'Edit Password Gagal!', '0');
                Session::flash('message', 'Edit Password Gagal!');
                Session::flash('class', 'danger');
                return redirect('/changepass');
            }
        } else {
            $syslog->log_insert('E', 'Changepass', 'Edit Password Gagal Diproses!', '0');
            Session::flash('message', 'Edit Password Diproses!');
            Session::flash('class', 'danger');
            return redirect('/changepass');
        }
    }
}
PHP;
    }
}
