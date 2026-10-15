@extends('pesanan.layout')

@section('judul', 'Ubah Pesanan #' . $pesanan->id)

@section('isi')
    <div class="row justify-content-center">
        <div class="col-lg-7 col-md-10">
            <div class="text-center">
                <h1>Ubah Pesanan #{{ $pesanan->id }}</h1>
                <p class="lead-text">Pesanan hanya dapat diubah selama statusnya masih "Baru".</p>
            </div>

            @include('pesanan._form', [
                'action' => route('pesanan.update', $pesanan),
                'metode' => 'PUT',
                'tombol' => 'SIMPAN PERUBAHAN',
                'pesanan' => $pesanan,
            ])

            <div class="text-center mt-30"><a href="{{ route('pesanan.show', $pesanan) }}" class="submit-btn primary-btn text-uppercase">BATAL<span class="lnr lnr-arrow-right"></span></a></div>
        </div>
    </div>
@endsection
