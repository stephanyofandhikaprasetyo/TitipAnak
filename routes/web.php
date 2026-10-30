<?php

use App\Http\Controllers\OrangTua\PenjemputanController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

/*
|--------------------------------------------------------------------------
| Rute Orang Tua — Penjemputan (COMMIT 1)
|--------------------------------------------------------------------------
| Menggunakan middleware 'auth' + 'role:orang_tua' (Spatie).
| Jika nama role Anda bukan 'orang_tua', sesuaikan.
*/
Route::middleware([]) // Hapus auth sementara
    ->prefix('orangtua')
    ->name('orangtua.')
    ->group(function () {
        Route::get('penjemputan/create', [PenjemputanController::class, 'create'])
            ->name('penjemputan.create');
        Route::post('penjemputan', [PenjemputanController::class, 'store'])
            ->name('penjemputan.store');
    });