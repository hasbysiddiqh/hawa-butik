<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Kategori;
use App\Models\Produk;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class ProdukController extends Controller
{
    /** Daftar semua produk (termasuk yang nonaktif) dengan pencarian. */
    public function index(Request $request): View
    {
        $cari = trim((string) $request->query('cari', ''));

        $produks = Produk::query()
            ->with('kategori')
            ->when($cari !== '', fn ($q) => $q->cari($cari))
            ->latest()
            ->paginate(10)
            ->withQueryString();

        return view('admin.produk.index', compact('produks', 'cari'));
    }

    public function create(): View
    {
        return view('admin.produk.create', [
            'produk' => new Produk(['stok' => 1, 'status_produk' => 'aktif']),
            'kategoris' => Kategori::orderBy('nama_kategori')->get(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate($this->aturan(), $this->pesan(), $this->atribut());

        if ($request->hasFile('foto_produk')) {
            $data['foto_produk'] = $request->file('foto_produk')->store('', 'produk');
        }

        Produk::create($data);

        return redirect()
            ->route('admin.produk.index')
            ->with('success', 'Produk berhasil ditambahkan.');
    }

    public function edit(Produk $produk): View
    {
        return view('admin.produk.edit', [
            'produk' => $produk,
            'kategoris' => Kategori::orderBy('nama_kategori')->get(),
        ]);
    }

    /** Mengubah data produk, termasuk mengelola stok dan status. */
    public function update(Request $request, Produk $produk): RedirectResponse
    {
        $data = $request->validate($this->aturan(), $this->pesan(), $this->atribut());

        if ($request->hasFile('foto_produk')) {
            $this->hapusFoto($produk);
            $data['foto_produk'] = $request->file('foto_produk')->store('', 'produk');
        }

        $produk->update($data);

        return redirect()
            ->route('admin.produk.index')
            ->with('success', 'Produk berhasil diperbarui.');
    }

    public function destroy(Produk $produk): RedirectResponse
    {
        $this->hapusFoto($produk);
        $produk->delete();

        return redirect()
            ->route('admin.produk.index')
            ->with('success', 'Produk berhasil dihapus.');
    }

    private function hapusFoto(Produk $produk): void
    {
        if ($produk->foto_produk) {
            Storage::disk('produk')->delete($produk->foto_produk);
        }
    }

    private function aturan(): array
    {
        return [
            'id_kategori'   => ['required', 'integer', Rule::exists('kategoris', 'id_kategori')],
            'nama_produk'   => ['required', 'string', 'max:150'],
            'deskripsi'     => ['nullable', 'string', 'max:2000'],
            'harga_sewa'    => ['required', 'integer', 'min:0', 'max:100000000'],
            'stok'          => ['required', 'integer', 'min:0', 'max:100000'],
            'status_produk' => ['required', Rule::in(Produk::STATUS_PRODUK)],
            'foto_produk'   => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
        ];
    }

    private function pesan(): array
    {
        return [
            'required' => ':attribute wajib diisi.',
            'integer'  => ':attribute harus berupa angka bulat.',
            'min'      => ':attribute tidak boleh kurang dari :min.',
            'max'      => ':attribute terlalu besar atau panjang (maksimal :max).',
            'exists'   => ':attribute tidak valid.',
            'in'       => ':attribute tidak valid.',
            'image'    => ':attribute harus berupa gambar.',
            'mimes'    => ':attribute harus berformat JPG, PNG, atau WebP.',
        ];
    }

    private function atribut(): array
    {
        return [
            'id_kategori'   => 'Kategori',
            'nama_produk'   => 'Nama produk',
            'deskripsi'     => 'Deskripsi',
            'harga_sewa'    => 'Harga sewa',
            'stok'          => 'Stok',
            'status_produk' => 'Status',
            'foto_produk'   => 'Foto',
        ];
    }
}
