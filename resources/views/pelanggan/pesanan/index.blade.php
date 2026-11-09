@extends('layouts.app')

@section('judul', 'Pesanan Saya')

@section('isi')
    <div class="d-flex justify-content-between align-items-end flex-wrap mb-20">
        <div>
            <h1>Pesanan Saya</h1>
            <p class="lead-text mb-0">{{ $pesanans->total() }} pesanan tercatat.</p>
        </div>
        <a href="{{ route('pelanggan.pemesanan') }}" class="submit-btn primary-btn text-uppercase">PEMESANAN<i class="arr"></i></a>
    </div>

    <div class="table-responsive pesanan-card p-0">
        <table class="table pesanan-table mb-0">
            <thead>
            <tr><th>#</th><th>Pakaian</th><th>Ukuran</th><th>Jumlah</th><th>Status</th><th>Tanggal</th><th></th></tr>
            </thead>
            <tbody>
            @forelse ($pesanans as $pesanan)
                <tr>
                    <td>{{ $pesanan->id }}</td>
                    <td>{{ $pesanan->jenis_pakaian ?? '-' }}<br><small class="text-muted">{{ $pesanan->isRental() ? 'Rental: '.$pesanan->produk?->nama_produk : $pesanan->model_lengan }}</small></td>
                    <td>{{ $pesanan->ukuran ?? '-' }}</td>
                    <td>{{ $pesanan->jumlah }}</td>
                    <td>@include('partials.status-pesanan')</td>
                    <td>{{ $pesanan->tanggalDibuat('d M Y H:i') }}</td>
                    <td><a href="{{ route('pelanggan.pesanan.show', $pesanan->id) }}" class="primary-btn btn-kecil">DETAIL</a></td>
                </tr>
            @empty
                <tr><td colspan="7" class="text-center text-muted py-4">Anda belum punya pesanan.</td></tr>
            @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-20 d-flex justify-content-center">{{ $pesanans->links('pagination::bootstrap-4') }}</div>
@endsection
