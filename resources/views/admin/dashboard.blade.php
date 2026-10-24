@extends('layouts.app')

@section('judul', 'Ringkasan Admin')

@section('isi')
    <h1>Ringkasan Admin</h1>
    <p class="lead-text">Selamat datang, {{ $admin->nama_admin }}.</p>

    <div class="row text-center">
        <div class="col-6 col-md-3 mb-20"><div class="pesanan-card"><div class="stat-angka">{{ $totalPesanan }}</div><span class="text-muted">Total pesanan</span></div></div>
        <div class="col-6 col-md-3 mb-20"><div class="pesanan-card"><div class="stat-angka">{{ $perStatus['baru'] ?? 0 }}</div><span class="text-muted">Pesanan baru</span></div></div>
        <div class="col-6 col-md-3 mb-20"><div class="pesanan-card"><div class="stat-angka">{{ $perStatus['diproses'] ?? 0 }}</div><span class="text-muted">Sedang dikerjakan</span></div></div>
        <div class="col-6 col-md-3 mb-20"><div class="pesanan-card"><div class="stat-angka">{{ $perStatus['selesai'] ?? 0 }}</div><span class="text-muted">Selesai</span></div></div>
        <div class="col-6 col-md-4 mb-20"><div class="pesanan-card"><div class="stat-angka">{{ $totalPelanggan }}</div><span class="text-muted">Pelanggan terdaftar</span></div></div>
        <div class="col-6 col-md-4 mb-20"><div class="pesanan-card"><div class="stat-angka">{{ $totalProduk }}</div><span class="text-muted">Produk</span></div></div>
        <div class="col-12 col-md-4 mb-20"><div class="pesanan-card"><div class="stat-angka">{{ $stokHabis }}</div><span class="text-muted">Produk stok habis</span></div></div>
    </div>

    <div class="d-flex justify-content-between align-items-center mt-30 mb-3">
        <h4 class="mb-0">Pesanan terbaru</h4>
        <a href="{{ route('admin.pesanan.index') }}" class="primary-btn btn-kecil">KELOLA PESANAN</a>
    </div>
    <div class="table-responsive pesanan-card p-0">
        <table class="table pesanan-table mb-0">
            <thead><tr><th>#</th><th>Pelanggan</th><th>Pakaian</th><th>Status</th><th>Tanggal</th><th></th></tr></thead>
            <tbody>
            @forelse ($pesananTerbaru as $pesanan)
                <tr>
                    <td>{{ $pesanan->id }}</td>
                    <td>{{ $pesanan->nama }}</td>
                    <td>{{ $pesanan->jenis_pakaian ?? '-' }} &times; {{ $pesanan->jumlah }}</td>
                    <td>@include('partials.status-pesanan')</td>
                    <td>{{ $pesanan->tanggalDibuat('d M Y') }}</td>
                    <td><a href="{{ route('admin.pesanan.show', $pesanan) }}" class="primary-btn btn-kecil">DETAIL</a></td>
                </tr>
            @empty
                <tr><td colspan="6" class="text-center text-muted py-4">Belum ada pesanan.</td></tr>
            @endforelse
            </tbody>
        </table>
    </div>
@endsection
