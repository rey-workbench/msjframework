<?php

namespace App\Http\Controllers;

use App\Helpers\Function_Helper;
use Illuminate\Contracts\Encryption\DecryptException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;

class LoginController extends Controller
{
    /**
     * Display login page.
     *
     * @return Renderable
     */
    public function show()
    {
        $data['title_menu'] = 'Login';
        // ambil informasi setup program
        $data['setup_app'] = DB::table('sys_app')->where('isactive', '1')->first();

        return view('auth.login', $data);
    }

    public function login(Request $request)
    {
        // function helper
        $syslog = new Function_Helper;
        $username = $request->username;
        if (Auth::attempt(['username' => $request->username, 'password' => $request->password, 'isactive' => '1'])) {
            // set session
            $request->session()->regenerate();
            $request->session()->put('user', auth()->user());
            $request->session()->put('username', $username);
            // insert sys_log
            $syslog->log_insert('L', 'login', 'Login Sukses', '1');

            // page dashboard
            return redirect()->intended('dashboard');
        } elseif (Auth::attempt(['username' => $request->username, 'password' => $request->password, 'isactive' => '0'])) {
            Auth::logout();
            // set session
            $request->session()->invalidate();
            $request->session()->regenerateToken();
            Session::forget(['user', 'username']);
            // insert sys_log
            $syslog->log_insert('L', 'login', 'Username Tidak Aktif', '0');

            // user not active
            return back()->withErrors([
                'username' => 'Username Tidak Aktif.',
            ]);
        }

        // insert sys_log
        $syslog->log_insert('L', 'login', 'Username Atau Password Salah', '0');

        // wrong password
        return back()->withErrors([
            'username' => 'Username Atau Password Salah.',
        ]);
    }

    public function logout(Request $request)
    {
        Auth::logout();

        // insert sys_log
        $syslog = new Function_Helper;
        $syslog->log_insert('L', 'logout', 'Logout Sukses', '1');
        // set session
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        Session::forget(['user', 'username']);

        // page login
        return redirect('/login');
    }

    public function auth(Request $request, string $token = '-')
    {
        $token = str_replace(['-', '_', '@'], ['+', '/', '='], $token);
        // check decrypt
        try {
            $id = openssl_decrypt($token, env('ALG'), env('KEY'), 0, env('SCR'));
        } catch (DecryptException $e) {
            $id = '';
        }
        // data primary key
        $token = explode(',', $id);
        $data['title_menu'] = 'Auth';
        $syslog = new Function_Helper;
        $username = $token[0];
        if (date('d-m-Y') != $token[2]) {
            // insert sys_log
            $syslog->log_insert('L', 'login', 'Token Tidak Valid', '0');

            // wrong password
            return back()->withErrors([
                'username' => 'Token Tidak Valid.',
            ]);
        } elseif (Auth::attempt(['username' => $token[0], 'password' => openssl_decrypt(str_replace(['-', '_', '@'], ['+', '/', '='], $token[1]), env('ALG'), env('KEY'), 0, env('SCR')), 'isactive' => '1'])) {
            // set session
            $request->session()->regenerate();
            $request->session()->put('user', auth()->user());
            $request->session()->put('username', $username);
            // insert sys_log
            $syslog->log_insert('L', 'login', 'Login Sukses', '1');

            // page dashboard
            return redirect()->intended('dashboard');
        } elseif (Auth::attempt(['username' => $token[0], 'password' => openssl_decrypt(str_replace(['-', '_', '@'], ['+', '/', '='], $token[1]), env('ALG'), env('KEY'), 0, env('SCR')), 'isactive' => '0'])) {
            Auth::logout();
            // set session
            $request->session()->invalidate();
            $request->session()->regenerateToken();
            Session::forget(['user', 'username']);
            // insert sys_log
            $syslog->log_insert('L', 'login', 'Username Tidak Aktif', '0');

            // user not active
            return back()->withErrors([
                'username' => 'Username Tidak Aktif.',
            ]);
        }

        // insert sys_log
        $syslog->log_insert('L', 'login', 'Username Atau Password Salah', '0');

        // wrong password
        return back()->withErrors([
            'username' => 'Username Atau Password Salah.',
        ]);
    }
}
