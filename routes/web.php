<?php

use App\Http\Controllers\PesananController;
use Illuminate\Support\Facades\Route;

Route::get('/', fn () => view('home'));

Route::get('/pesanan', [PesananController::class, 'index'])->name('pesanan.index');
Route::get('/pesanan/buat', [PesananController::class, 'create'])->name('pesanan.create');
Route::post('/pesanan', [PesananController::class, 'store'])->name('pesanan.store');
Route::get('/pesanan/{pesanan}', [PesananController::class, 'show'])->name('pesanan.show');
Route::get('/pesanan/{pesanan}/ubah', [PesananController::class, 'edit'])->name('pesanan.edit');
Route::put('/pesanan/{pesanan}', [PesananController::class, 'update'])->name('pesanan.update');
Route::delete('/pesanan/{pesanan}', [PesananController::class, 'destroy'])->name('pesanan.destroy');
