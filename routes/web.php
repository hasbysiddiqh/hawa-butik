<?php

use App\Http\Controllers\PesananController;

Route::get('/', fn () => view('home'));
Route::post('/pesanan', [PesananController::class, 'store'])->name('pesanan.store');
