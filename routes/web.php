<?php

use App\Http\Controllers\OrangTua\PenjemputanController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

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
    });