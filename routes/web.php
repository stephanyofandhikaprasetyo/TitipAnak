<?php

use App\Http\Controllers\OrangTua\PenjemputanController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect()->route('login');
});

/*
|--------------------------------------------------------------------------
| Rute Autentikasi
|--------------------------------------------------------------------------
*/
Route::get('/login', function () {
    if (Auth::check()) {
        return redirect()->route('orangtua.penjemputan.index');
    }
    return view('auth.login');
})->name('login');

Route::post('/login', function (Request $request) {
    $credentials = $request->validate([
        'email' => ['required', 'email'],
        'password' => ['required'],
    ]);

    if (Auth::attempt($credentials)) {
        $request->session()->regenerate();

        // Redirect langsung ke Halaman Status Izin Penjemputan (Commit 2 & 3)
        return redirect()->intended(route('orangtua.penjemputan.index'))
            ->with('success', 'Selamat datang! Anda berhasil masuk.');
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
        Route::get('/penjemputan', [PenjemputanController::class, 'index'])
            ->name('penjemputan.index');
        Route::get('/penjemputan/create', [PenjemputanController::class, 'create'])
            ->name('penjemputan.create');
        Route::post('/penjemputan', [PenjemputanController::class, 'store'])
            ->name('penjemputan.store');
        Route::patch('/penjemputan/{id}/cancel', [PenjemputanController::class, 'cancel'])
            ->name('penjemputan.cancel');
    });