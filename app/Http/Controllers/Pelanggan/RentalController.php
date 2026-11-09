<?php

namespace App\Http\Controllers\Pelanggan;

use App\Http\Controllers\Controller;
use App\Models\Pesanan;
use App\Models\Produk;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class RentalController extends Controller
{
    /** Form rental: pilih busana dari Collection, isi tanggal sewa dan kembali. */
    public function create(Request $request): View
    {
        $pelanggan = $request->user('pelanggan');

        $produks = Produk::query()
            ->aktif()
            ->where('stok', '>', 0)
            ->with('kategori')
            ->orderBy('nama_produk')
            ->get();

        return view('pelanggan.rental.create', [
            'produks'     => $produks,
            'idTerpilih'  => (int) $request->query('produk'),
            'ukuran'      => Pesanan::OPSI['ukuran'],
            'nama'        => $pelanggan->nama_pelanggan,
            'whatsapp'    => $pelanggan->no_telp,
            'alamat'      => $pelanggan->alamat,
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'id_produk'         => ['required', 'integer', Rule::exists('produks', 'id_produk')->where('status_produk', 'aktif')],
            'nama'              => ['required', 'string', 'max:100'],
            'whatsapp'          => ['required', 'string', 'regex:/^[0-9+\-\s]{9,20}$/'],
            'alamat_pengiriman' => ['nullable', 'string', 'max:500'],
            'ukuran'            => ['nullable', 'string', Rule::in(Pesanan::OPSI['ukuran'])],
            'jumlah'            => ['required', 'integer', 'min:1', 'max:1000'],
            'tanggal_sewa'      => ['required', 'date', 'after_or_equal:today'],
            'tanggal_kembali'   => ['required', 'date', 'after_or_equal:tanggal_sewa'],
            'detail_pesanan'    => ['nullable', 'string', 'max:2000'],
        ], [
            'required'                    => ':attribute wajib diisi.',
            'regex'                       => 'Nomor WhatsApp tidak valid.',
            'tanggal_sewa.after_or_equal' => 'Tanggal sewa tidak boleh sebelum hari ini.',
            'tanggal_kembali.after_or_equal' => 'Tanggal kembali tidak boleh sebelum tanggal sewa.',
        ], [
            'id_produk'       => 'Busana',
            'nama'            => 'Nama',
            'whatsapp'        => 'Nomor WhatsApp',
            'jumlah'          => 'Jumlah',
            'tanggal_sewa'    => 'Tanggal sewa',
            'tanggal_kembali' => 'Tanggal kembali',
        ]);

        // Cek stok, kurangi stok, lalu simpan pesanan, penyewaan, detail transaksi, dan pembayaran
        // dalam satu transaksi database. Baris produk dikunci agar dua pelanggan tidak menyewa stok yang sama.
        $pesanan = DB::transaction(function () use ($request, $data) {
            $produk = Produk::with('kategori')->lockForUpdate()->findOrFail($data['id_produk']);

            if ($produk->stok < $data['jumlah']) {
                throw ValidationException::withMessages([
                    'jumlah' => "Stok {$produk->nama_produk} tersisa {$produk->stok}.",
                ]);
            }

            $produk->decrement('stok', (int) $data['jumlah']);

            // Harga pada katalog merupakan harga sewa per potong; total dihitung dari harga x jumlah.
            $hargaSatuan = (int) $produk->harga_sewa;
            $subtotal = $hargaSatuan * (int) $data['jumlah'];

            $pesanan = $request->user('pelanggan')->pesanans()->create([
                ...$data,
                'tipe'          => 'rental',
                'jenis_pakaian' => $produk->kategori->nama_kategori,
                'status'        => 'dipinjam',
                'total'         => $subtotal,
                'stok_dipotong' => true,
            ]);

            $pesanan->penyewaan()->create([
                'tgl_mulai' => $data['tanggal_sewa'],
                'tgl_kembali' => $data['tanggal_kembali'],
                'status_sewa' => 'dipinjam',
                'denda' => 0,
            ]);

            $pesanan->detailTransaksis()->create([
                'id_produk'    => $produk->id_produk,
                'jumlah'       => $data['jumlah'],
                'harga_satuan' => $hargaSatuan,
                'subtotal'     => $subtotal,
            ]);

            $pesanan->pembayaran()->create([
                'jumlah_bayar' => 0,
                'status_bayar' => 'menunggu',
            ]);

            return $pesanan;
        });

        return redirect()
            ->route('pelanggan.pesanan.show', $pesanan)
            ->with('success', 'Rental berhasil dikirim. Kami akan menghubungi Anda lewat WhatsApp.');
    }
}
