@extends('layouts.app')

@section('judul', 'Pemesanan')

@section('isi')
    <h1>Pemesanan</h1>
    <p class="lead-text">Mau sewa busana yang sudah ada, atau pesan jahit sesuai keinginanmu?</p>

    <div class="pilihan-stack">
        <a href="{{ route('pelanggan.rental.create') }}" class="pilihan-card">
            <span class="lnr lnr-tag"></span>
            <div>
                <h4>Rental</h4>
                <p class="text-muted">Sewa busana pilihan dari Collection Hawa Butik untuk acara spesialmu.</p>
            </div>
            <i class="arr"></i>
        </a>
        <a href="{{ route('pelanggan.pesanan.create') }}" class="pilihan-card">
            <span class="lnr lnr-pencil"></span>
            <div>
                <h4>Pesan</h4>
                <p class="text-muted">Pesan jahit busana custom: pilih model, ukuran, bahan, dan warna.</p>
            </div>
            <i class="arr"></i>
        </a>
    </div>
@endsection
