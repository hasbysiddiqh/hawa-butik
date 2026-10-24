<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Pesanan;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class PesananController extends Controller
{
    /** Semua pesanan dari semua pelanggan, dengan pencarian dan saringan status. */
    public function index(Request $request): View
    {
        $cari = trim((string) $request->query('cari', ''));
        $status = (string) $request->query('status', '');

        $pesanans = Pesanan::query()
            ->with('pelanggan')
            ->when($cari !== '', function ($q) use ($cari) {
                $q->where(function ($w) use ($cari) {
                    $w->where('nama', 'like', "%{$cari}%")
                      ->orWhere('whatsapp', 'like', "%{$cari}%")
                      ->orWhere('jenis_pakaian', 'like', "%{$cari}%");
                });
            })
            ->when(in_array($status, Pesanan::STATUS, true), fn ($q) => $q->where('status', $status))
            ->latest()
            ->paginate(10)
            ->withQueryString();

        return view('admin.pesanan.index', compact('pesanans', 'cari', 'status'));
    }

    public function show(Pesanan $pesanan): View
    {
        return view('admin.pesanan.show', ['pesanan' => $pesanan->load(['pelanggan', 'pembayaran', 'penyewaan', 'detailTransaksis.produk', 'adminVerifikasi'])]);
    }

    /** Mengganti status pesanan mengikuti alur: baru, dikerjakan, selesai (atau dibatalkan). */
    public function updateStatus(Request $request, Pesanan $pesanan): RedirectResponse
    {
        $data = $request->validate([
            'status' => ['required', Rule::in(Pesanan::STATUS)],
        ], [
            'required' => 'Status wajib dipilih.',
            'in'       => 'Status tidak valid.',
        ]);

        if (! $pesanan->bisaDiubahStatusKe($data['status'])) {
            return redirect()
                ->route('admin.pesanan.show', $pesanan)
                ->with('error', "Status \"{$pesanan->labelStatus()}\" tidak dapat diganti ke \"".Pesanan::LABEL_STATUS[$data['status']].'".');
        }

        $pesanan->update([
            'status' => $data['status'],
            'id_admin_verifikasi' => $request->user('admin')->id_admin,
        ]);

        // Status penyewaan mengikuti status pesanan untuk menghindari dua alur status yang berbeda.
        if ($pesanan->isRental() && $pesanan->penyewaan) {
            $statusSewa = match ($data['status']) {
                'diproses' => 'diproses',
                'selesai' => 'selesai',
                'dibatalkan' => 'dibatalkan',
                default => 'dipesan',
            };

            $pesanan->penyewaan->update([
                'status_sewa' => $statusSewa,
                'tgl_selesai' => $statusSewa === 'selesai' ? now()->toDateString() : null,
            ]);
        }

        return redirect()
            ->route('admin.pesanan.show', $pesanan)
            ->with('success', 'Status pesanan diganti menjadi "'.$pesanan->labelStatus().'".');
    }
}
