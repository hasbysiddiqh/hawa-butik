<?php

namespace App\Http\Controllers;

use App\Models\Kategori;
use App\Models\Produk;
use Illuminate\Http\Request;
use Illuminate\View\View;

class KatalogController extends Controller
{
    /**
     * Collection: produk aktif dikelompokkan per jenis pakaian (kategori).
     * Bisa dicari dan difilter per kategori. Terbuka untuk umum.
     */
    public function index(Request $request): View
    {
        $cari = trim((string) $request->query('cari', ''));
        $idKategori = $request->query('kategori');

        $kelompok = Kategori::query()
            ->when($idKategori, fn ($q) => $q->where('id_kategori', $idKategori))
            ->with(['produks' => fn ($q) => $q
                ->where('status_produk', 'aktif')
                ->when($cari !== '', fn ($w) => $w->cari($cari))
                ->latest()])
            ->orderBy('nama_kategori')
            ->get()
            ->filter(fn (Kategori $kategori) => $kategori->produks->isNotEmpty())
            ->values();

        return view('katalog.index', [
            'kelompok' => $kelompok,
            'kategoris' => Kategori::orderBy('nama_kategori')->get(),
            'cari' => $cari,
            'idKategori' => $idKategori,
        ]);
    }

    public function show(Produk $produk): View
    {
        abort_unless($produk->status_produk === 'aktif', 404);

        return view('katalog.show', ['produk' => $produk->load('kategori')]);
    }
}
