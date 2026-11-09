<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Pembayaran;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class TransaksiController extends Controller
{
    /** Daftar transaksi (pembayaran) semua pelanggan, dengan pencarian dan saringan status bayar. */
    public function index(Request $request): View
    {
        $cari = trim((string) $request->query('cari', ''));
        $status = (string) $request->query('status', '');

        $query = Pembayaran::query()
            ->when($cari !== '', function (Builder $q) use ($cari) {
                $q->whereHas('pesanan', function (Builder $p) use ($cari) {
                    $p->where('nama', 'like', "%{$cari}%")
                        ->orWhere('whatsapp', 'like', "%{$cari}%");
                });
            })
            ->when(array_key_exists($status, Pembayaran::LABEL_STATUS), fn (Builder $q) => $q->where('status_bayar', $status));

        $jumlahTransaksi = $query->count();
        $totalDibayar = (int) $query->sum('jumlah_bayar');

        // Total tagihan tidak menghitung pesanan yang dibatalkan.
        $totalTagihan = (int) (clone $query)
            ->join('pesanans', 'pesanans.id', '=', 'pembayarans.id_pesanan')
            ->where('pesanans.status', '!=', 'dibatalkan')
            ->sum('pesanans.total');
        $sisaTagihan = max($totalTagihan - $totalDibayar, 0);

        $transaksis = $query
            ->with('pesanan.pelanggan')
            ->latest()
            ->paginate(10)
            ->withQueryString();

        return view('admin.transaksi.index', compact('transaksis', 'cari', 'status', 'jumlahTransaksi', 'totalDibayar', 'totalTagihan', 'sisaTagihan'));
    }

    /** Rincian satu transaksi beserta formulir untuk memperbaruinya. */
    public function show(Pembayaran $pembayaran): View
    {
        $pembayaran->load(['pesanan.pelanggan', 'pesanan.produk', 'pesanan.detailTransaksis.produk']);

        return view('admin.transaksi.show', ['transaksi' => $pembayaran]);
    }

    /**
     * Mengubah status transaksi dan metode pembayaran.
     * Pesanan jahit custom: admin juga menetapkan total (hasil estimasi) dari sini.
     * Lunas: jumlah dibayar otomatis sama dengan total pesanan.
     * Dicicil: jumlah dibayar diisi admin, harus kurang dari total.
     * Menunggu / belum dibayar: belum ada uang masuk, jumlah dibayar menjadi 0.
     */
    public function update(Request $request, Pembayaran $pembayaran): RedirectResponse
    {
        $status = (string) $request->input('status_bayar');

        $data = $request->validate([
            'status_bayar' => ['required', Rule::in(array_keys(Pembayaran::LABEL_STATUS))],
            'metode'       => ['nullable', Rule::in(array_keys(Pembayaran::LABEL_METODE)), Rule::requiredIf(in_array($status, ['dicicil', 'lunas'], true))],
            'jumlah_bayar' => ['nullable', 'integer', 'min:0', 'max:4000000000', Rule::requiredIf($status === 'dicicil')],
            'total'        => ['nullable', 'integer', 'min:0', 'max:4000000000'],
        ], [
            'required' => ':attribute wajib diisi.',
            'in'       => ':attribute tidak valid.',
            'integer'  => ':attribute harus berupa angka.',
        ], [
            'status_bayar' => 'Status transaksi',
            'metode'       => 'Metode pembayaran',
            'jumlah_bayar' => 'Jumlah dibayar',
            'total'        => 'Total pesanan',
        ]);

        $pesanan = $pembayaran->pesanan;

        // Total rental tetap (harga x jumlah). Total pesanan jahit custom ditetapkan admin di sini.
        $total = $pesanan->isRental() ? (int) $pesanan->total : (int) ($data['total'] ?? $pesanan->total);

        if (in_array($data['status_bayar'], ['dicicil', 'lunas'], true) && $total < 1) {
            return back()->withInput()->withErrors([
                'total' => "Transaksi #{$pembayaran->id_bayar}: total pesanan belum ditetapkan. Isi total terlebih dahulu.",
            ]);
        }

        $jumlah = match ($data['status_bayar']) {
            'lunas'   => $total,
            'dicicil' => (int) $data['jumlah_bayar'],
            default   => 0,
        };

        if ($data['status_bayar'] === 'dicicil' && ($jumlah < 1 || $jumlah >= $total)) {
            return back()->withInput()->withErrors([
                'jumlah_bayar' => "Transaksi #{$pembayaran->id_bayar}: jumlah cicilan harus lebih dari 0 dan kurang dari total Rp ".number_format($total, 0, ',', '.').'.',
            ]);
        }

        $sudahAdaUangMasuk = in_array($data['status_bayar'], ['dicicil', 'lunas'], true);

        DB::transaction(function () use ($pembayaran, $pesanan, $data, $total, $jumlah, $sudahAdaUangMasuk) {
            if (! $pesanan->isRental() && $total !== (int) $pesanan->total) {
                $pesanan->update(['total' => $total]);
            }

            $pembayaran->update([
                'status_bayar' => $data['status_bayar'],
                'metode'       => $data['metode'] ?? null,
                'jumlah_bayar' => $jumlah,
                'tgl_bayar'    => $sudahAdaUangMasuk ? ($pembayaran->tgl_bayar ?? now()) : null,
            ]);
        });

        return redirect()
            ->route('admin.transaksi.show', $pembayaran)
            ->with('success', "Transaksi #{$pembayaran->id_bayar} diperbarui menjadi \"{$pembayaran->labelStatus()}\".");
    }
}
