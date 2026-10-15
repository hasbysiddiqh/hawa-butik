@extends('pesanan.layout')

@section('judul', 'Detail Pesanan #' . $pesanan->id)

@section('isi')
    <div class="row justify-content-center">
        <div class="col-lg-7 col-md-10">
            @if (session('success'))
                <div class="alert alert-success">{{ session('success') }}</div>
            @endif

            <div class="text-center">
                <h1>Detail Pesanan #{{ $pesanan->id }}</h1>
                <p class="lead-text">Dikirim {{ $pesanan->created_at?->format('d M Y, H:i') . ' WITA' }}</p>
            </div>

            <div class="pesanan-card">
                <dl class="row pesanan-detail mb-0">
                    <dt class="col-sm-4">Nama</dt><dd class="col-sm-8">{{ $pesanan->nama }}</dd>
                    <dt class="col-sm-4">WhatsApp</dt><dd class="col-sm-8">{{ $pesanan->whatsapp }}</dd>
                    <dt class="col-sm-4">Jenis pakaian</dt><dd class="col-sm-8">{{ $pesanan->jenis_pakaian ?? '-' }}</dd>
                    <dt class="col-sm-4">Model lengan</dt><dd class="col-sm-8">{{ $pesanan->model_lengan ?? '-' }}</dd>
                    <dt class="col-sm-4">Ukuran</dt><dd class="col-sm-8">{{ $pesanan->ukuran ?? '-' }}</dd>
                    <dt class="col-sm-4">Bahan</dt><dd class="col-sm-8">{{ $pesanan->bahan ?: '-' }}</dd>
                    <dt class="col-sm-4">Warna</dt><dd class="col-sm-8">{{ $pesanan->warna ?: '-' }}</dd>
                    <dt class="col-sm-4">Jumlah</dt><dd class="col-sm-8">{{ $pesanan->jumlah }} potong</dd>
                    <dt class="col-sm-4">Catatan</dt><dd class="col-sm-8">{!! nl2br(e($pesanan->detail_pesanan ?: '-')) !!}</dd>
                    <dt class="col-sm-4">Status</dt><dd class="col-sm-8"><span class="badge-status {{ $pesanan->status }}">{{ ucfirst($pesanan->status) }}</span></dd>
                </dl>
            </div>

            <div class="text-center mt-30">
                <a href="{{ route('pesanan.index') }}" class="submit-btn primary-btn text-uppercase">DAFTAR PESANAN<span class="lnr lnr-arrow-right"></span></a>
                <a href="{{ route('pesanan.create') }}" class="submit-btn primary-btn text-uppercase ml-2">PESAN LAGI<span class="lnr lnr-arrow-right"></span></a>
            </div>
        </div>
    </div>
@endsection
