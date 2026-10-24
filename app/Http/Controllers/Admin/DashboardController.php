<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Pelanggan;
use App\Models\Pesanan;
use App\Models\Produk;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DashboardController extends Controller
{
    /** Ringkasan toko untuk admin. */
    public function index(Request $request): View
    {
        $perStatus = Pesanan::query()
            ->selectRaw('status, count(*) as total')
            ->groupBy('status')
            ->pluck('total', 'status');

        return view('admin.dashboard', [
            'admin'          => $request->user('admin'),
            'perStatus'      => $perStatus,
            'totalPesanan'   => $perStatus->sum(),
            'totalPelanggan' => Pelanggan::count(),
            'totalProduk'    => Produk::count(),
            'stokHabis'      => Produk::where('stok', '<', 1)->count(),
            'pesananTerbaru' => Pesanan::with('pelanggan')->latest()->limit(5)->get(),
        ]);
    }
}
