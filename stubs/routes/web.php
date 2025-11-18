<?php

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\MSJBaseController;
use App\Http\Controllers\PageController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| MSJ Framework Routes
|--------------------------------------------------------------------------
|
| Dynamic routing pattern using metadata-driven architecture.
| All routes are handled by PageController which resolves controllers
| based on database configuration (sys_dmenu, sys_gmenu, sys_auth).
|
*/

Route::get('/', function () {
    return redirect('/dashboard');
})->middleware('auth');

Route::get('/auth/{token}', [LoginController::class, 'auth'])->name('auth');
Route::get('/login', [LoginController::class, 'show'])->middleware('guest')->name('login');
Route::post('/login', [LoginController::class, 'login'])->middleware('guest')->name('login.perform');

Route::group(['middleware' => 'auth'], function () {
    // Profile routes
    Route::get('/profile', [PageController::class, 'profile'])->name('profile');
    Route::post('/profile/update', [PageController::class, 'update'])->name('profile.update');
    
    // Change password routes
    Route::get('/changepass', [PageController::class, 'changepass'])->name('changepass');
    Route::post('/changepass/update', [PageController::class, 'changepass_update'])->name('changepass.update');
    
    // Logout
    Route::post('logout', [LoginController::class, 'logout'])->name('logout');
    
    // Export data routes
    Route::get('export-data/{module}/{type}', [MSJBaseController::class, 'exportData'])->name('export.data');

    // Dashboard (optional - customize as needed)
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    /*
    |--------------------------------------------------------------------------
    | Dynamic Catch-All Routes (MUST be at the end)
    |--------------------------------------------------------------------------
    |
    | These routes dynamically resolve to controllers based on sys_dmenu config.
    | Pattern: /{page}/{action}/{id}
    | 
    | - page: URL from sys_dmenu.url
    | - action: index|add|edit|show|destroy|etc
    | - id: encrypted primary key
    |
    */
    
    Route::get('/{page}', [PageController::class, 'index'])->name('page.index');
    Route::post('/{page}', [PageController::class, 'index'])->name('page.store');
    Route::get('/{page}/{action}', [PageController::class, 'index'])->name('page.action');
    Route::put('/{page}/{action}', [PageController::class, 'index'])->name('page.update');
    Route::delete('/{page}/{action}', [PageController::class, 'index'])->name('page.destroy');
    Route::get('/{page}/{action}/{id}', [PageController::class, 'index'])->name('page.action.id');
});
