<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Kategori;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class KategoriController extends Controller
{
    /** Daftar kategori sekaligus form tambah kategori. */
    public function index(): View
    {
        return view('admin.kategori.index', [
            'kategoris' => Kategori::withCount('produks')->orderBy('nama_kategori')->get(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        Kategori::create($request->validate($this->aturan(), $this->pesan(), $this->atribut()));

        return redirect()
            ->route('admin.kategori.index')
            ->with('success', 'Kategori berhasil ditambahkan.');
    }

    public function edit(Kategori $kategori): View
    {
        return view('admin.kategori.edit', compact('kategori'));
    }

    public function update(Request $request, Kategori $kategori): RedirectResponse
    {
        $kategori->update($request->validate($this->aturan($kategori), $this->pesan(), $this->atribut()));

        return redirect()
            ->route('admin.kategori.index')
            ->with('success', 'Kategori berhasil diperbarui.');
    }

    /** Kategori yang masih punya produk tidak boleh dihapus. */
    public function destroy(Kategori $kategori): RedirectResponse
    {
        if ($kategori->produks()->exists()) {
            return redirect()
                ->route('admin.kategori.index')
                ->with('error', "Kategori \"{$kategori->nama_kategori}\" masih memiliki produk, jadi tidak dapat dihapus.");
        }

        $kategori->delete();

        return redirect()
            ->route('admin.kategori.index')
            ->with('success', 'Kategori berhasil dihapus.');
    }

    private function aturan(?Kategori $kategori = null): array
    {
        return [
            'nama_kategori' => ['required', 'string', 'max:100', Rule::unique('kategoris', 'nama_kategori')->ignore($kategori?->id_kategori, 'id_kategori')],
            'deskripsi'     => ['nullable', 'string', 'max:1000'],
        ];
    }

    private function pesan(): array
    {
        return [
            'required' => ':attribute wajib diisi.',
            'max'      => ':attribute maksimal :max karakter.',
            'unique'   => ':attribute sudah ada.',
        ];
    }

    private function atribut(): array
    {
        return [
            'nama_kategori' => 'Nama kategori',
            'deskripsi'     => 'Deskripsi',
        ];
    }
}
