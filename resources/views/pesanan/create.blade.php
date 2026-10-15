@extends('pesanan.layout')

@section('judul', 'Buat Pesanan')

@section('isi')
    <div class="row justify-content-center">
        <div class="col-lg-7 col-md-10">
            <div class="text-center">
                <h1>Buat Pesanan</h1>
                <p class="lead-text">Pilih dari pilihan yang tersedia. Jika tidak ada yang cocok, pilih "Lainnya" lalu ketik sendiri.</p>
            </div>

            @include('pesanan._form', [
                'action' => route('pesanan.store'),
                'metode' => 'POST',
                'tombol' => 'KONFIRMASI PEMESANAN',
                'pesanan' => null,
            ])

            <div class="text-center mt-30"><a href="{{ route('pesanan.index') }}" class="submit-btn primary-btn text-uppercase">LIHAT DAFTAR PESANAN<span class="lnr lnr-arrow-right"></span></a></div>
        </div>
    </div>
@endsection
