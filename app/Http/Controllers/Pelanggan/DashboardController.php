<?php

namespace App\Http\Controllers\Pelanggan;

use App\Http\Controllers\Controller;
use App\Models\Pesanan;
use App\Models\Produk;
use App\Support\DataDashboard as D;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Schema;

class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::guard('pelanggan')->user();

        // Hanya pesanan milik pelanggan ini. Kalau kolom penghubung tidak ditemukan, tampil kosong (aman).
        $tabel = (new Pesanan)->getTable();
        $fk = collect(['pelanggan_id', 'id_pelanggan', 'user_id'])->first(fn ($k) => Schema::hasColumn($tabel, $k));
        $semua = $fk ? Pesanan::where($fk, $user->getKey())->latest()->get() : collect();
        $baris = $semua->map(fn ($p) => D::baris($p));

        $hitung = $baris->countBy('key');
        $aktif = $hitung->get('baru', 0) + $hitung->get('proses', 0);
        $selesai = $hitung->get('selesai', 0);

        $koleksi = Produk::with('kategori')->where('status_produk', 'aktif')->latest()->take(3)->get()
            ->map(fn ($p) => D::produk($p));

        return view('pelanggan.dashboard', [
            'nama' => D::pick($user, ['nama', 'nama_lengkap', 'nama_pelanggan', 'name', 'username'], 'Pelanggan'),
            'tanggal' => D::tanggalIndonesia(),
            'aktif' => $aktif,
            'selesai' => $selesai,
            'persen' => $baris->count() ? (int) round($selesai / $baris->count() * 100) : 0,
            'total' => $baris->count(),
            'bulan' => D::bulanan($semua),
            'terbaru' => $baris->take(4),
            'koleksi' => $koleksi,
        ]);
    }
}
