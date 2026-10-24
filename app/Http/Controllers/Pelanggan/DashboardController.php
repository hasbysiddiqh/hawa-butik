<?php

namespace App\Http\Controllers\Pelanggan;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DashboardController extends Controller
{
    /** Ringkasan akun pelanggan: pesanan dan jalan pintas. */
    public function index(Request $request): View
    {
        $pelanggan = $request->user('pelanggan');

        $perStatus = $pelanggan->pesanans()
            ->selectRaw('status, count(*) as total')
            ->groupBy('status')
            ->pluck('total', 'status');

        return view('pelanggan.dashboard', [
            'pelanggan'     => $pelanggan,
            'totalPesanan'  => $perStatus->sum(),
            'pesananBerjalan' => ($perStatus['baru'] ?? 0) + ($perStatus['diproses'] ?? 0),
            'pesananSelesai'  => $perStatus['selesai'] ?? 0,
            'pesananTerbaru' => $pelanggan->pesanans()->latest()->limit(5)->get(),
        ]);
    }
}
