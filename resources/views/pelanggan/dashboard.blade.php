@extends('layouts.app')

@section('judul', 'Ringkasan')

@section('isi')
    <h1>Halo, {{ $pelanggan->nama_pelanggan }}</h1>
    <p class="lead-text">Pantau pesanan Anda di sini.</p>

    <div class="row text-center">
        <div class="col-12 col-md-4 mb-20"><div class="pesanan-card"><div class="stat-angka">{{ $totalPesanan }}</div><span class="text-muted">Total pesanan</span></div></div>
        <div class="col-12 col-md-4 mb-20"><div class="pesanan-card"><div class="stat-angka">{{ $pesananBerjalan }}</div><span class="text-muted">Sedang berjalan</span></div></div>
        <div class="col-12 col-md-4 mb-20"><div class="pesanan-card"><div class="stat-angka">{{ $pesananSelesai }}</div><span class="text-muted">Selesai</span></div></div>
    </div>

    <div class="d-flex flex-wrap mb-30">
        <a href="{{ route('pelanggan.pemesanan') }}" class="submit-btn primary-btn text-uppercase mr-2 mb-2">PEMESANAN<span class="lnr lnr-arrow-right"></span></a>
        <a href="{{ route('katalog.index') }}" class="submit-btn primary-btn text-uppercase mr-2 mb-2">LIHAT COLLECTION<span class="lnr lnr-arrow-right"></span></a>
    </div>

    <h4 class="mb-3">Pesanan terbaru</h4>
    <div class="table-responsive pesanan-card p-0">
        <table class="table pesanan-table mb-0">
            <thead><tr><th>#</th><th>Pakaian</th><th>Jumlah</th><th>Status</th><th>Tanggal</th><th></th></tr></thead>
            <tbody>
            @forelse ($pesananTerbaru as $pesanan)
                <tr>
                    <td>{{ $pesanan->id }}</td>
                    <td>{{ $pesanan->jenis_pakaian ?? '-' }}</td>
                    <td>{{ $pesanan->jumlah }}</td>
                    <td>@include('partials.status-pesanan')</td>
                    <td>{{ $pesanan->tanggalDibuat('d M Y') }}</td>
                    <td><a href="{{ route('pelanggan.pesanan.show', $pesanan->id) }}" class="primary-btn btn-kecil">DETAIL</a></td>
                </tr>
            @empty
                <tr><td colspan="6" class="text-center text-muted py-4">Anda belum punya pesanan.</td></tr>
            @endforelse
            </tbody>
        </table>
    </div>
@endsection
