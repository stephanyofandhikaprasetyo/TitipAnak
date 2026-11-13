<?php

use App\Http\Controllers\OrangTua\PenjemputanController as OrangTuaPenjemputanController;
use App\Http\Controllers\Pengasuh\PenjemputanController as PengasuhPenjemputanController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect()->route('login');
});

/*
|--------------------------------------------------------------------------
| Rute Autentikasi (RBAC Redirect per Role)
|--------------------------------------------------------------------------
*/
Route::get('/login', function () {
    if (Auth::check()) {
        $isPengasuh = DB::table('model_has_roles')
            ->join('roles', 'model_has_roles.role_id', '=', 'roles.id')
            ->where('model_has_roles.model_id', Auth::id())
            ->where('roles.name', 'pengasuh')
            ->exists();

        return $isPengasuh
            ? redirect()->route('pengasuh.penjemputan.index')
            : redirect()->route('orangtua.penjemputan.index');
    }
    return view('auth.login');
})->name('login');

Route::post('/login', function (Request $request) {
    $credentials = $request->validate([
        'email'    => ['required', 'email'],
        'password' => ['required'],
    ]);

    if (Auth::attempt($credentials)) {
        $request->session()->regenerate();

        // Cek role user dari tabel model_has_roles
        $role = DB::table('model_has_roles')
            ->join('roles', 'model_has_roles.role_id', '=', 'roles.id')
            ->where('model_has_roles.model_id', Auth::id())
            ->value('roles.name');

        if ($role === 'pengasuh') {
            return redirect()->route('pengasuh.penjemputan.index');
        }

        return redirect()->route('orangtua.penjemputan.index');
    }

    return back()->withErrors([
        'email' => 'Email atau password yang Anda masukkan tidak cocok.',
    ])->onlyInput('email');
})->name('login.post');

Route::post('/logout', function (Request $request) {
    Auth::logout();
    $request->session()->invalidate();
    $request->session()->regenerateToken();
    return redirect()->route('login');
})->name('logout');

/*
|--------------------------------------------------------------------------
| Rute Orang Tua — Penjemputan
|--------------------------------------------------------------------------
*/
Route::middleware(['auth'])
    ->prefix('orangtua')
    ->name('orangtua.')
    ->group(function () {
        Route::get('/penjemputan', [OrangTuaPenjemputanController::class, 'index'])
            ->name('penjemputan.index');
        Route::get('/penjemputan/create', [OrangTuaPenjemputanController::class, 'create'])
            ->name('penjemputan.create');
        Route::post('/penjemputan', [OrangTuaPenjemputanController::class, 'store'])
            ->name('penjemputan.store');
        Route::patch('/penjemputan/{id}/cancel', [OrangTuaPenjemputanController::class, 'cancel'])
            ->name('penjemputan.cancel');
    });

/*
|--------------------------------------------------------------------------
| Rute Pengasuh — Validasi Penjemputan (Commit 4: PBI-17d & PBI-17e)
|--------------------------------------------------------------------------
*/
Route::middleware(['auth'])
    ->prefix('pengasuh')
    ->name('pengasuh.')
    ->group(function () {
        Route::get('/penjemputan', [PengasuhPenjemputanController::class, 'index'])
            ->name('penjemputan.index');
        Route::patch('/penjemputan/{id}/validasi', [PengasuhPenjemputanController::class, 'validasi'])
            ->name('penjemputan.validasi');
    });