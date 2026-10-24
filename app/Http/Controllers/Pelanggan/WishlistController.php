<?php

namespace App\Http\Controllers\Pelanggan;

use App\Http\Controllers\Controller;
use App\Models\Produk;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class WishlistController extends Controller
{
    /** Daftar produk yang disimpan pelanggan. */
    public function index(Request $request): View
    {
        $produks = $request->user('pelanggan')
            ->ambilWishlist()
            ->produks()
            ->with('kategori')
            ->orderByPivot('tanggal_ditambahkan', 'desc')
            ->get();

        return view('pelanggan.wishlist.index', [
            'produks' => $produks,
            'idWishlist' => $produks->pluck('id_produk')->all(),
        ]);
    }

    /** Tambah produk ke wishlist. Produk yang sama tidak akan tercatat dua kali. */
    public function store(Request $request, Produk $produk): RedirectResponse
    {
        abort_unless($produk->status_produk === 'aktif', 404);

        $request->user('pelanggan')->ambilWishlist()->produks()->syncWithoutDetaching([$produk->id_produk]);

        return back()->with('success', "{$produk->nama_produk} ditambahkan ke wishlist.");
    }

    public function destroy(Request $request, Produk $produk): RedirectResponse
    {
        $request->user('pelanggan')->ambilWishlist()->produks()->detach($produk->id_produk);

        return back()->with('success', "{$produk->nama_produk} dihapus dari wishlist.");
    }
}
