@extends('layouts.app')

@section('judul', 'Ubah Pesanan #' . $pesanan->id)

@section('isi')
    <div class="row justify-content-center">
        <div class="col-lg-7 col-md-10">
            <a href="{{ route('pelanggan.pesanan.show', $pesanan->id) }}" class="btn-back"><i class="arr arr-left"></i>KEMBALI KE DETAIL</a>

            <div class="text-center">
                <h1>Ubah Pesanan #{{ $pesanan->id }}</h1>
                <p class="lead-text">Pesanan hanya dapat diubah selama statusnya masih "Baru".</p>
            </div>

            @include('pelanggan.pesanan._form', [
                'batal' => route('pelanggan.pesanan.show', $pesanan->id),
                'action' => route('pelanggan.pesanan.update', $pesanan->id),
                'metode' => 'PUT',
                'tombol' => 'SIMPAN PERUBAHAN',
            ])
        </div>
    </div>
@endsection
