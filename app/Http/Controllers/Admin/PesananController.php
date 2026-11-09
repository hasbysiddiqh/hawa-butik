<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Pesanan;
use App\Models\Produk;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class PesananController extends Controller
{
    /**
     * Pesanan semua pelanggan dalam dua tabel: Rental dan Pesan (Kustom).
     * Pencarian berlaku untuk keduanya; saringan status dan halaman terpisah per tabel.
     */
    public function index(Request $request): View
    {
        $cari = trim((string) $request->query('cari', ''));
        $statusRental = (string) $request->query('status_rental', '');
        $statusKustom = (string) $request->query('status_kustom', '');

        $dasar = fn (): Builder => Pesanan::query()
            ->with('pelanggan')
            ->when($cari !== '', function (Builder $q) use ($cari) {
                $q->where(function (Builder $w) use ($cari) {
                    $w->where('nama', 'like', "%{$cari}%")
                        ->orWhere('whatsapp', 'like', "%{$cari}%")
                        ->orWhere('jenis_pakaian', 'like', "%{$cari}%");
                });
            });

        $rentals = $dasar()
            ->rental()
            ->with('produk')
            ->when(in_array($statusRental, Pesanan::STATUS_RENTAL, true), fn (Builder $q) => $q->where('status', $statusRental))
            ->latest()
            ->paginate(10, ['*'], 'halaman_rental')
            ->withQueryString();

        $kustoms = $dasar()
            ->kustom()
            ->when(in_array($statusKustom, Pesanan::STATUS_KUSTOM, true), fn (Builder $q) => $q->where('status', $statusKustom))
            ->latest()
            ->paginate(10, ['*'], 'halaman_kustom')
            ->withQueryString();

        return view('admin.pesanan.index', compact('rentals', 'kustoms', 'cari', 'statusRental', 'statusKustom'));
    }

    public function show(Pesanan $pesanan): View
    {
        return view('admin.pesanan.show', ['pesanan' => $pesanan->load(['pelanggan', 'pembayaran', 'penyewaan', 'detailTransaksis.produk', 'adminVerifikasi'])]);
    }

    /**
     * Mengganti status pesanan mengikuti alurnya masing-masing.
     * Kustom: baru, dikerjakan, siap diambil, selesai (atau dibatalkan).
     * Rental: dipinjam, belum dikembalikan, dikembalikan.
     */
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

        DB::transaction(function () use ($request, $pesanan, $data) {
            $pesanan->update([
                'status' => $data['status'],
                'id_admin_verifikasi' => $request->user('admin')->id_admin,
            ]);

            if (! $pesanan->isRental()) {
                return;
            }

            // Status rental sama dengan status sewa, jadi keduanya selalu diperbarui bersamaan.
            $pesanan->penyewaan?->update([
                'status_sewa' => $data['status'],
                'tgl_selesai' => $data['status'] === 'dikembalikan' ? now()->toDateString() : null,
            ]);

            // Stok dikembalikan sekali saja, hanya untuk rental yang stoknya memang dikurangi saat dibuat.
            if ($data['status'] === 'dikembalikan' && $pesanan->stok_dipotong && $pesanan->id_produk) {
                Produk::whereKey($pesanan->id_produk)->increment('stok', (int) $pesanan->jumlah);
                $pesanan->update(['stok_dipotong' => false]);
            }
        });

        return redirect()
            ->route('admin.pesanan.show', $pesanan)
            ->with('success', 'Status pesanan diganti menjadi "'.$pesanan->labelStatus().'".');
    }
}
