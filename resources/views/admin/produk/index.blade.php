@extends('layouts.app')

@section('judul', 'Produk')

@section('isi')
    <div class="d-flex justify-content-between align-items-end flex-wrap mb-20">
        <div>
            <h1>Produk</h1>
            <p class="lead-text mb-0">{{ $produks->total() }} produk tercatat.</p>
        </div>
        <a href="{{ route('admin.produk.create') }}" class="submit-btn primary-btn text-uppercase">TAMBAH PRODUK<i class="arr"></i></a>
    </div>

    <form class="form-inline mb-20" method="GET" action="{{ route('admin.produk.index') }}">
        <input name="cari" type="text" class="form-control mr-2 mb-2" style="min-width:260px" placeholder="Cari nama atau deskripsi produk" value="{{ $cari }}">
        <button type="submit" class="submit-btn primary-btn btn-kecil mb-2">CARI</button>
    </form>

    <div class="table-responsive pesanan-card p-0">
        <table class="table pesanan-table mb-0">
            <thead><tr><th>Foto</th><th>Produk</th><th>Kategori</th><th>Harga sewa</th><th>Stok</th><th>Status</th><th></th></tr></thead>
            <tbody>
            @forelse ($produks as $produk)
                <tr>
                    <td style="width: 70px">
                        @if ($produk->fotoUrl())
                            <img src="{{ $produk->fotoUrl() }}" alt="" style="width: 56px; height: 56px; object-fit: cover">
                        @else
                            <span class="text-muted"><i class="fa fa-picture-o"></i></span>
                        @endif
                    </td>
                    <td>{{ $produk->nama_produk }}</td>
                    <td>{{ $produk->kategori->nama_kategori }}</td>
                    <td>{{ $produk->hargaRupiah() }}</td>
                    <td>{{ $produk->stok }} @if ($produk->stokHabis())<span class="badge-stok">Habis</span>@endif</td>
                    <td>{{ ucfirst($produk->status_produk) }}</td>
                    <td class="text-nowrap">
                        <a href="{{ route('admin.produk.edit', $produk) }}" class="primary-btn btn-kecil">UBAH</a>
                        <form action="{{ route('admin.produk.destroy', $produk) }}" method="POST" class="d-inline"
                              onsubmit="return confirm('Hapus produk ini?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="primary-btn btn-kecil border-0">HAPUS</button>
                        </form>
                    </td>
                </tr>
            @empty
                <tr><td colspan="7" class="text-center text-muted py-4">Belum ada produk.</td></tr>
            @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-20 d-flex justify-content-center">{{ $produks->links('pagination::bootstrap-4') }}</div>
@endsection
