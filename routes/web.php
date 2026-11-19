<?php

use App\Http\Controllers\Auth\LogoutController;
use App\Http\Controllers\LandingController;
use App\Http\Controllers\SeederDataController;
use App\Livewire\Auth\Login;
use App\Livewire\OrangTua\PenjemputIndex;
use Illuminate\Support\Facades\Route;

Route::get('/data-seeder', SeederDataController::class)->name('data-seeder');

Route::get('/', LandingController::class)->name('landing');

Route::middleware('guest')->group(function () {
    Route::get('login', Login::class)->name('login');
});

Route::post('logout', LogoutController::class)->middleware('auth')->name('logout');

Route::middleware(['auth', 'role:orang_tua'])
    ->prefix('orang-tua')
    ->name('orang-tua.')
    ->group(function () {
        Route::get('/penjemput', PenjemputIndex::class)
            ->name('penjemput.index');

        Route::get('/penjemput/ajukan', fn () => redirect()->route('orang-tua.penjemput.index', ['buat' => 1]))
            ->name('penjemput.create');
    });