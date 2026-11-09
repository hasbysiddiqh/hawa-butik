@extends('layouts.app')

@section('judul', 'Ubah Produk')

@section('isi')
    <div class="row justify-content-center">
        <div class="col-lg-7 col-md-10">
            <a href="{{ route('admin.produk.index') }}" class="btn-back"><i class="arr arr-left"></i>KEMBALI</a>
            <div class="text-center">
                <h1>Ubah Produk</h1>
                <p class="lead-text">Perbarui data busana di Collection.</p>
            </div>

            @include('admin.produk._form', ['action' => route('admin.produk.update', $produk), 'metode' => 'PUT', 'tombol' => 'SIMPAN PERUBAHAN'])
        </div>
    </div>
@endsection
