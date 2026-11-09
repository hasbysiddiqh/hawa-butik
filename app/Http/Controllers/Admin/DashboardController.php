<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Pembayaran;
use App\Models\Pesanan;
use App\Models\Produk;
use App\Support\DataDashboard as D;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Schema;

class DashboardController extends Controller
{
    public function index()
    {
        $rel = array_values(array_filter(['pelanggan', 'produk'], fn ($r) => method_exists(Pesanan::class, $r)));
        $semua = Pesanan::with($rel)->latest()->get();
        $baris = $semua->map(fn ($p) => D::baris($p));

        $hitung = $baris->countBy('key');
        $ringkas = [
            'total' => $baris->count(),
            'baru' => $hitung->get('baru', 0),
            'proses' => $hitung->get('proses', 0),
            'selesai' => $hitung->get('selesai', 0),
            'batal' => $hitung->get('batal', 0),
        ];

        $stokHabis = 0;
        if (Schema::hasColumn((new Produk)->getTable(), 'stok')) {
            $stokHabis = Produk::where('stok', '<=', 0)->count();
        }

        // Ringkasan transaksi untuk kartu angka dan kartu "Transaksi".
        $transaksi = Pembayaran::query()->selectRaw('status_bayar, COUNT(*) as jumlah')->groupBy('status_bayar')->pluck('jumlah', 'status_bayar');
        $transaksiRingkas = [
            'lunas'   => (int) $transaksi->get('lunas', 0),
            'dicicil' => (int) $transaksi->get('dicicil', 0),
            'belum'   => (int) $transaksi->get('menunggu', 0) + (int) $transaksi->get('belum_dibayar', 0),
        ];
        $pendapatan = (int) Pembayaran::sum('jumlah_bayar');

        $admin = Auth::guard('admin')->user();

        return view('admin.dashboard', [
            'nama' => D::pick($admin, ['nama', 'nama_lengkap', 'name', 'username'], 'Admin'),
            'tanggal' => D::tanggalIndonesia(),
            'ringkas' => $ringkas,
            'bulan' => D::bulanan($semua),
            // "Perlu diproses" hanya untuk pesanan jahit custom yang masih baru (rental tidak perlu diproses).
            'perlu' => $baris->first(fn ($b) => $b['kustom'] && $b['key'] === 'baru'),
            'perluN' => $baris->filter(fn ($b) => $b['kustom'] && $b['key'] === 'baru')->count(),
            'transaksi' => $transaksiRingkas,
            'pendapatan' => $pendapatan,
            'terbaru' => $baris->take(5),
            'pelangganN' => class_exists(\App\Models\Pelanggan::class) ? \App\Models\Pelanggan::count() : 0,
            'produkN' => Produk::where('status_produk', 'aktif')->count(),
            'stokHabis' => $stokHabis,
        ]);
    }
}
