@extends('layouts.app')

@section('judul', 'Ubah Kategori')

@section('isi')
    <div class="row justify-content-center">
        <div class="col-lg-7 col-md-10">
            <a href="{{ route('admin.kategori.index') }}" class="btn-back"><i class="arr arr-left"></i>KEMBALI</a>
            <div class="text-center">
                <h1>Ubah Kategori</h1>
                <p class="lead-text">Perbarui nama dan deskripsi kelompok produk.</p>
            </div>

            @include('partials.validation-errors')

            <form class="pesanan-form pesanan-card" action="{{ route('admin.kategori.update', $kategori) }}" method="POST">
                @csrf
                @method('PUT')
                <label for="nama_kategori">Nama kategori</label>
                <input id="nama_kategori" name="nama_kategori" type="text" class="form-control" maxlength="100"
                       value="{{ old('nama_kategori', $kategori->nama_kategori) }}" required>

                <label for="deskripsi">Deskripsi (opsional)</label>
                <textarea id="deskripsi" name="deskripsi" class="form-control" maxlength="1000">{{ old('deskripsi', $kategori->deskripsi) }}</textarea>

                <div class="form-aksi">
                    <a href="{{ route('admin.kategori.index') }}" class="form-batal">Batal</a>
                    <button type="submit" class="submit-btn primary-btn text-uppercase">SIMPAN PERUBAHAN<i class="arr"></i></button>
                </div>
            </form>
        </div>
    </div>
@endsection
