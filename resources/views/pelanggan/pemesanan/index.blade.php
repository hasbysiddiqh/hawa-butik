@extends('layouts.app')

@section('judul', 'Pemesanan')

@section('isi')
    <div class="text-center">
        <h1>Pemesanan</h1>
        <p class="lead-text">Mau sewa busana yang sudah ada, atau pesan jahit sesuai keinginanmu?</p>
    </div>

    <div class="row justify-content-center">
        <div class="col-md-5 mb-30">
            <a href="{{ route('pelanggan.rental.create') }}" class="pilihan-card">
                <span class="lnr lnr-tag"></span>
                <h4 class="mt-3">Rental</h4>
                <p class="text-muted mb-0">Sewa busana pilihan dari Collection Hawa Butik untuk acara spesialmu.</p>
            </a>
        </div>
        <div class="col-md-5 mb-30">
            <a href="{{ route('pelanggan.pesanan.create') }}" class="pilihan-card">
                <span class="lnr lnr-pencil"></span>
                <h4 class="mt-3">Pesan</h4>
                <p class="text-muted mb-0">Pesan jahit busana custom: pilih model, ukuran, bahan, dan warna.</p>
            </a>
        </div>
    </div>

    <p class="text-center mb-0"><a href="{{ route('pelanggan.pesanan.index') }}">Lihat pesanan saya</a></p>
@endsection
