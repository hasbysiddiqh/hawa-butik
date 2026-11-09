@extends('layouts.app')

@section('judul', 'Tambah Produk')

@section('isi')
    <div class="row justify-content-center">
        <div class="col-lg-7 col-md-10">
            <a href="{{ route('admin.produk.index') }}" class="btn-back"><i class="arr arr-left"></i>KEMBALI</a>
            <div class="text-center">
                <h1>Tambah Produk</h1>
                <p class="lead-text">Isi data busana baru untuk Collection.</p>
            </div>

            @include('admin.produk._form', ['action' => route('admin.produk.store'), 'metode' => 'POST', 'tombol' => 'SIMPAN PRODUK'])
        </div>
    </div>
@endsection
