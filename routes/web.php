<?php

use App\Http\Controllers\Admin;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\KatalogController;
use App\Http\Controllers\Pelanggan;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    // Admin tidak memakai halaman utama: langsung ke dashboard.
    if (Auth::guard('admin')->check()) {
        return redirect()->route('admin.dashboard');
    }

    return view('home', [
        'kategoris' => \App\Models\Kategori::orderBy('nama_kategori')->get(),
        'produkTerbaru' => \App\Models\Produk::with('kategori')
            ->where('status_produk', 'aktif')->latest()->take(8)->get(),
    ]);
})->name('home');

// Collection (katalog busana, dikelompokkan per jenis pakaian) terbuka untuk umum.
Route::get('/collection', [KatalogController::class, 'index'])->name('katalog.index');
Route::get('/collection/{produk}', [KatalogController::class, 'show'])->name('katalog.show');

// Login bersama untuk pelanggan & admin; registrasi khusus pelanggan.
Route::middleware('guest:pelanggan,admin')->group(function () {
    Route::get('/login', [LoginController::class, 'create'])->name('login');
    Route::post('/login', [LoginController::class, 'store'])->name('login.store');
    Route::get('/registrasi', [RegisterController::class, 'create'])->name('register');
    Route::post('/registrasi', [RegisterController::class, 'store'])->name('register.store');
});

Route::post('/logout', [LoginController::class, 'destroy'])->name('logout');

// Area pelanggan: wajib login sebagai pelanggan.
Route::middleware('auth:pelanggan')->prefix('pelanggan')->name('pelanggan.')->group(function () {
    Route::get('/', [Pelanggan\DashboardController::class, 'index'])->name('dashboard');

    Route::get('/profil', [Pelanggan\ProfilController::class, 'edit'])->name('profil.edit');
    Route::put('/profil', [Pelanggan\ProfilController::class, 'update'])->name('profil.update');
    Route::put('/profil/password', [Pelanggan\ProfilController::class, 'updatePassword'])->name('profil.password');

    // Pemesanan: pilih Rental (sewa dari Collection) atau Pesan (jahit custom).
    Route::view('/pemesanan', 'pelanggan.pemesanan.index')->name('pemesanan');
    Route::get('/rental', [Pelanggan\RentalController::class, 'create'])->name('rental.create');
    Route::post('/rental', [Pelanggan\RentalController::class, 'store'])->name('rental.store');

    Route::get('/pesanan', [Pelanggan\PesananController::class, 'index'])->name('pesanan.index');
    Route::get('/pesanan/buat', [Pelanggan\PesananController::class, 'create'])->name('pesanan.create');
    Route::post('/pesanan', [Pelanggan\PesananController::class, 'store'])->name('pesanan.store');
    Route::get('/pesanan/{pesanan}', [Pelanggan\PesananController::class, 'show'])->name('pesanan.show');
    Route::get('/pesanan/{pesanan}/ubah', [Pelanggan\PesananController::class, 'edit'])->name('pesanan.edit');
    Route::put('/pesanan/{pesanan}', [Pelanggan\PesananController::class, 'update'])->name('pesanan.update');
    Route::patch('/pesanan/{pesanan}/batal', [Pelanggan\PesananController::class, 'batal'])->name('pesanan.batal');
});

// Area admin: wajib login sebagai admin.
Route::middleware('auth:admin')->prefix('admin')->name('admin.')->group(function () {
    Route::get('/', [Admin\DashboardController::class, 'index'])->name('dashboard');

    Route::get('/profil', [Admin\ProfilController::class, 'show'])->name('profil');

    Route::get('/pesanan', [Admin\PesananController::class, 'index'])->name('pesanan.index');
    Route::get('/pesanan/{pesanan}', [Admin\PesananController::class, 'show'])->name('pesanan.show');
    Route::patch('/pesanan/{pesanan}/status', [Admin\PesananController::class, 'updateStatus'])->name('pesanan.status');

    Route::get('/transaksi', [Admin\TransaksiController::class, 'index'])->name('transaksi.index');
    Route::get('/transaksi/{pembayaran}', [Admin\TransaksiController::class, 'show'])->name('transaksi.show');
    Route::patch('/transaksi/{pembayaran}', [Admin\TransaksiController::class, 'update'])->name('transaksi.update');

    Route::resource('kategori', Admin\KategoriController::class)->except(['show', 'create']);
    Route::resource('produk', Admin\ProdukController::class)->except(['show']);
});
