<?php

use App\Http\Controllers\OrangTua\PenjemputanController as OrangTuaPenjemputanController;
use App\Http\Controllers\Pengasuh\LaporanHarianController;
use App\Http\Controllers\Pengasuh\LaporanInsidenController;
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
            ? redirect()->route('pengasuh.laporan-harian.index')
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

        $role = DB::table('model_has_roles')
            ->join('roles', 'model_has_roles.role_id', '=', 'roles.id')
            ->where('model_has_roles.model_id', Auth::id())
            ->value('roles.name');

        if ($role === 'pengasuh') {
            return redirect()->route('pengasuh.laporan-harian.index');
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
        Route::get('/penjemputan/{id}/foto', [OrangTuaPenjemputanController::class, 'foto'])
            ->name('penjemputan.foto');
    });

/*
|--------------------------------------------------------------------------
| Rute Pengasuh — Operasional Harian (Laporan Harian, Foto Kegiatan, Insiden)
|--------------------------------------------------------------------------
*/
Route::middleware(['auth'])
    ->prefix('pengasuh')
    ->name('pengasuh.')
    ->group(function () {
        // Stage 1, 2, 7: CRUD Laporan Harian Anak
        Route::get('/laporan-harian', [LaporanHarianController::class, 'index'])
            ->name('laporan-harian.index');
        Route::get('/laporan-harian/create', [LaporanHarianController::class, 'create'])
            ->name('laporan-harian.create');
        Route::post('/laporan-harian', [LaporanHarianController::class, 'store'])
            ->name('laporan-harian.store');
        Route::get('/laporan-harian/{id}/edit', [LaporanHarianController::class, 'edit'])
            ->name('laporan-harian.edit');
        Route::put('/laporan-harian/{id}', [LaporanHarianController::class, 'update'])
            ->name('laporan-harian.update');
        Route::delete('/laporan-harian/{id}', [LaporanHarianController::class, 'destroy'])
            ->name('laporan-harian.destroy');

        // Stage 5 & 6: Unggah, Ubah/Ganti, dan Hapus Foto Kegiatan pada Laporan Harian
        Route::post('/laporan-harian/{id}/foto', [LaporanHarianController::class, 'storeFoto'])
            ->name('laporan-harian.foto.store');
        Route::patch('/laporan-harian/{laporanId}/foto/{fotoId}', [LaporanHarianController::class, 'updateFoto'])
            ->name('laporan-harian.foto.update');
        Route::delete('/laporan-harian/{laporanId}/foto/{fotoId}', [LaporanHarianController::class, 'destroyFoto'])
            ->name('laporan-harian.foto.destroy');

        // Stage 3 & 4: Pencatatan & Pembaruan Laporan Insiden
        Route::get('/laporan-insiden', [LaporanInsidenController::class, 'index'])
            ->name('laporan-insiden.index');
        Route::get('/laporan-insiden/create', [LaporanInsidenController::class, 'create'])
            ->name('laporan-insiden.create');
        Route::post('/laporan-insiden', [LaporanInsidenController::class, 'store'])
            ->name('laporan-insiden.store');
        Route::get('/laporan-insiden/{id}/edit', [LaporanInsidenController::class, 'edit'])
            ->name('laporan-insiden.edit');
        Route::put('/laporan-insiden/{id}', [LaporanInsidenController::class, 'update'])
            ->name('laporan-insiden.update');
        Route::delete('/laporan-insiden/{id}', [LaporanInsidenController::class, 'destroy'])
            ->name('laporan-insiden.destroy');

        // Validasi Penjemputan Pengasuh (Existing Route)
        Route::get('/penjemputan', [PengasuhPenjemputanController::class, 'index'])
            ->name('penjemputan.index');
        Route::patch('/penjemputan/{id}/validasi', [PengasuhPenjemputanController::class, 'validasi'])
            ->name('penjemputan.validasi');
    });