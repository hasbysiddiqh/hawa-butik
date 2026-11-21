<?php

namespace App\Http\Controllers\Pelanggan;

use App\Http\Controllers\Controller;
use App\Models\Pembayaran;
use App\Models\Pesanan;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class PesananController extends Controller
{
    /** Daftar pesanan milik pelanggan yang login saja. */
    public function index(Request $request): View
    {
        $pesanans = $request->user('pelanggan')
            ->pesanans()
            ->latest()
            ->paginate(10);

        return view('pelanggan.pesanan.index', compact('pesanans'));
    }

    /** Form pesanan baru; nama, WhatsApp, dan alamat diisi dari profil. */
    public function create(Request $request): View
    {
        $pelanggan = $request->user('pelanggan');

        $awal = new Pesanan([
            'nama'              => $pelanggan->nama_pelanggan,
            'whatsapp'          => $pelanggan->no_telp,
            'alamat_pengiriman' => $pelanggan->alamat,
        ]);

        return view('pelanggan.pesanan.create', ['opsi' => Pesanan::OPSI, 'pesanan' => $awal]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $this->normalisasi($request->validate($this->aturan(), $this->pesan(), $this->atribut()));
        $metode = $data['metode'] ?? null;
        unset($data['metode'], $data['lampiran']);

        // Total pesanan jahit baru ditetapkan admin setelah estimasi, jadi transaksi dimulai dari nol.
        $pesanan = DB::transaction(function () use ($request, $data, $metode) {
            $pesanan = $request->user('pelanggan')->pesanans()->create([
                ...$data,
                'status'   => 'baru',
                'lampiran' => $request->file('lampiran')?->store('', 'lampiran'),
            ]);

            $pesanan->pembayaran()->create([
                'metode'       => $metode,
                'jumlah_bayar' => 0,
                'status_bayar' => 'menunggu',
            ]);

            return $pesanan;
        });

        return redirect()
            ->route('pelanggan.pesanan.show', $pesanan)
            ->with('success', 'Pesanan berhasil dikirim. Kami akan menghubungi Anda lewat WhatsApp.');
    }

    public function show(Request $request, string $pesanan): View
    {
        return view('pelanggan.pesanan.show', ['pesanan' => $this->milikSaya($request, $pesanan)]);
    }

    /** Hanya untuk pesanan berstatus "baru". */
    public function edit(Request $request, string $pesanan): View|RedirectResponse
    {
        $pesanan = $this->milikSaya($request, $pesanan);

        if ($pesanan->isRental()) {
            return $this->tolakUbahRental($pesanan);
        }

        if (! $pesanan->bisaDiubah()) {
            return $this->tolakKarenaSudahDikerjakan($pesanan);
        }

        return view('pelanggan.pesanan.edit', ['pesanan' => $pesanan, 'opsi' => Pesanan::OPSI]);
    }

    /** Status tidak bisa diubah dari sini; hanya admin yang mengganti status. */
    public function update(Request $request, string $pesanan): RedirectResponse
    {
        $pesanan = $this->milikSaya($request, $pesanan);

        if ($pesanan->isRental()) {
            return $this->tolakUbahRental($pesanan);
        }

        if (! $pesanan->bisaDiubah()) {
            return $this->tolakKarenaSudahDikerjakan($pesanan);
        }

        $data = $this->normalisasi($request->validate($this->aturan(), $this->pesan(), $this->atribut()));
        $metode = $data['metode'] ?? null;
        unset($data['metode'], $data['lampiran']);

        if ($request->hasFile('lampiran')) {
            if ($pesanan->lampiran) {
                Storage::disk('lampiran')->delete($pesanan->lampiran);
            }
            $data['lampiran'] = $request->file('lampiran')->store('', 'lampiran');
        }

        DB::transaction(function () use ($pesanan, $data, $metode) {
            $pesanan->update($data);
            $pesanan->pembayaran?->update(['metode' => $metode]);
        });

        return redirect()
            ->route('pelanggan.pesanan.show', $pesanan)
            ->with('success', 'Pesanan berhasil diperbarui.');
    }

    /** Membatalkan pesanan: status menjadi "dibatalkan" (riwayat tetap tersimpan). */
    public function batal(Request $request, string $pesanan): RedirectResponse
    {
        $pesanan = $this->milikSaya($request, $pesanan);

        if ($pesanan->isRental()) {
            return $this->tolakUbahRental($pesanan);
        }

        if (! $pesanan->bisaDiubah()) {
            return $this->tolakKarenaSudahDikerjakan($pesanan);
        }

        $pesanan->update(['status' => 'dibatalkan']);

        return redirect()
            ->route('pelanggan.pesanan.show', $pesanan)
            ->with('success', 'Pesanan berhasil dibatalkan.');
    }

    /** Pesanan milik orang lain dianggap tidak ada (404). */
    private function milikSaya(Request $request, string $id): Pesanan
    {
        return $request->user('pelanggan')->pesanans()->findOrFail($id);
    }

    private function tolakUbahRental(Pesanan $pesanan): RedirectResponse
    {
        return redirect()
            ->route('pelanggan.pesanan.show', $pesanan)
            ->with('error', 'Pesanan sewa tidak dapat diubah atau dibatalkan sendiri. Hubungi admin jika ada yang perlu diperbaiki.');
    }

    private function tolakKarenaSudahDikerjakan(Pesanan $pesanan): RedirectResponse
    {
        return redirect()
            ->route('pelanggan.pesanan.show', $pesanan)
            ->with('error', 'Pesanan sudah dikerjakan atau selesai, sehingga tidak dapat diubah atau dibatalkan.');
    }

    /** Aturan validasi yang sama untuk membuat dan mengubah pesanan. */
    private function aturan(): array
    {
        return [
            'nama'                => ['required', 'string', 'max:100'],
            'whatsapp'            => ['required', 'string', 'regex:/^[0-9+\-\s]{9,20}$/'],
            'alamat_pengiriman'   => ['nullable', 'string', 'max:500'],

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
            'lampiran'            => ['nullable', 'file', 'mimes:jpg,jpeg,png,webp,pdf', 'max:2048'],
            'metode'              => ['nullable', Rule::in(array_keys(Pembayaran::LABEL_METODE))],
        ];
    }

    private function pesan(): array
    {
        return [
            'required'    => ':attribute wajib diisi.',
            'required_if' => ':attribute wajib diisi jika memilih "Lainnya".',
            'regex'       => 'Nomor WhatsApp tidak valid.',
        ];
    }

    /** @return array<string, string> */
    private function atribut(): array
    {
        return ['lampiran' => 'Lampiran', 'metode' => 'Metode pembayaran'];
    }

    /** Jika memilih "Lainnya", pakai teks yang diketik sendiri. */
    private function normalisasi(array $data): array
    {
        foreach (['jenis_pakaian', 'model_lengan', 'ukuran', 'bahan'] as $kolom) {
            if (($data[$kolom] ?? null) === 'lainnya') {
                $data[$kolom] = $data[$kolom . '_lain'];
            }
            unset($data[$kolom . '_lain']);
        }

        return $data;
    }
}
