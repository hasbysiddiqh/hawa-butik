<?php

namespace App\Http\Controllers;

use App\Models\Pesanan;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PesananController extends Controller
{
    /** READ (daftar): semua pesanan, terbaru di atas, dengan pencarian sederhana. */
    public function index(Request $request): View
    {
        $cari = trim((string) $request->query('cari', ''));

        $pesanans = Pesanan::query()
            ->when($cari !== '', function ($q) use ($cari) {
                $q->where(function ($w) use ($cari) {
                    $w->where('nama', 'like', "%{$cari}%")
                      ->orWhere('whatsapp', 'like', "%{$cari}%")
                      ->orWhere('jenis_pakaian', 'like', "%{$cari}%");
                });
            })
            ->latest()
            ->paginate(10)
            ->withQueryString();

        return view('pesanan.index', compact('pesanans', 'cari'));
    }

    /** CREATE (form). */
    public function create(): View
    {
        return view('pesanan.create', ['opsi' => Pesanan::OPSI]);
    }

    /** CREATE (simpan). */
    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'nama'                => ['required', 'string', 'max:100'],
            'whatsapp'            => ['required', 'string', 'regex:/^[0-9+\-\s]{9,20}$/'],

            'jenis_pakaian'       => ['required', 'string', 'max:100'],
            'jenis_pakaian_lain'  => ['nullable', 'required_if:jenis_pakaian,lainnya', 'string', 'max:100'],

            'model_lengan'        => ['required', 'string', 'max:100'],
            'model_lengan_lain'   => ['nullable', 'required_if:model_lengan,lainnya', 'string', 'max:100'],

            'ukuran'              => ['required', 'string', 'max:100'],
            'ukuran_lain'         => ['nullable', 'required_if:ukuran,lainnya', 'string', 'max:100'],

            'bahan'               => ['required', 'string', 'max:100'],
            'bahan_lain'          => ['nullable', 'required_if:bahan,lainnya', 'string', 'max:100'],

            'warna'               => ['required', 'string', 'max:50'],
            'jumlah'              => ['required', 'integer', 'min:1', 'max:1000'],
            'detail_pesanan'      => ['nullable', 'string', 'max:2000'],
        ], [
            'required'    => ':attribute wajib diisi.',
            'required_if' => ':attribute wajib diisi jika memilih "Lainnya".',
            'regex'       => 'Nomor WhatsApp tidak valid.',
        ]);

        // Jika memilih "Lainnya", pakai teks yang diketik sendiri.
        foreach (['jenis_pakaian', 'model_lengan', 'ukuran', 'bahan'] as $f) {
            if (($data[$f] ?? null) === 'lainnya') {
                $data[$f] = $data[$f . '_lain'];
            }
            unset($data[$f . '_lain']);
        }

        $data['status'] = 'baru';

        $pesanan = Pesanan::create($data);

        return redirect()
            ->route('pesanan.show', $pesanan)
            ->with('success', 'Pesanan berhasil dikirim. Kami akan menghubungi Anda lewat WhatsApp.');
    }

    /** READ (detail satu pesanan). */
    public function show(Pesanan $pesanan): View
    {
        return view('pesanan.show', compact('pesanan'));
    }
}
