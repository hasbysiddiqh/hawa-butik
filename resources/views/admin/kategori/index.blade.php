@extends('layouts.app')

@section('judul', 'Kategori')

@section('isi')
    <h1>Kategori</h1>
    <p class="lead-text">Kelompok produk di katalog.</p>

    @include('partials.validation-errors')

    <form class="pesanan-form pesanan-card mb-30" action="{{ route('admin.kategori.store') }}" method="POST">
        @csrf
        <h4 class="form-bagian"><span class="lnr lnr-list"></span>Tambah kategori</h4>
        <div class="row">
            <div class="col-md-5">
                <label for="nama_kategori">Nama kategori</label>
                <input id="nama_kategori" name="nama_kategori" type="text" class="form-control" maxlength="100" value="{{ old('nama_kategori') }}" required>
            </div>
            <div class="col-md-7">
                <label for="deskripsi">Deskripsi (opsional)</label>
                <input id="deskripsi" name="deskripsi" type="text" class="form-control" maxlength="1000" value="{{ old('deskripsi') }}">
            </div>
        </div>
        <div class="form-aksi">
            <button type="submit" class="submit-btn primary-btn text-uppercase">TAMBAH<i class="arr"></i></button>
        </div>
    </form>

    <div class="table-responsive pesanan-card p-0">
        <table class="table pesanan-table mb-0">
            <thead><tr><th>Nama</th><th>Deskripsi</th><th>Produk</th><th></th></tr></thead>
            <tbody>
            @forelse ($kategoris as $kategori)
                <tr>
                    <td>{{ $kategori->nama_kategori }}</td>
                    <td>{{ $kategori->deskripsi ?: '-' }}</td>
                    <td>{{ $kategori->produks_count }}</td>
                    <td class="text-nowrap">
                        <a href="{{ route('admin.kategori.edit', $kategori) }}" class="primary-btn btn-kecil">UBAH</a>
                        <form action="{{ route('admin.kategori.destroy', $kategori) }}" method="POST" class="d-inline"
                              onsubmit="return confirm('Hapus kategori ini?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="primary-btn btn-kecil border-0">HAPUS</button>
                        </form>
                    </td>
                </tr>
            @empty
                <tr><td colspan="4" class="text-center text-muted py-4">Belum ada kategori.</td></tr>
            @endforelse
            </tbody>
        </table>
    </div>
@endsection
