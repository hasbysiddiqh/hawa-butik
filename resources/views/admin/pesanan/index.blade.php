@extends('layouts.app')

@section('judul', 'Kelola Pesanan')

@section('isi')
    <h1>Kelola Pesanan</h1>
    <p class="lead-text">{{ $pesanans->total() }} pesanan ditemukan.</p>

    <div class="mb-20">
        <a href="{{ route('admin.pesanan.index', array_filter(['cari' => $cari])) }}" class="chip {{ $status === '' ? 'aktif' : '' }}">Semua</a>
        @foreach (App\Models\Pesanan::LABEL_STATUS as $nilai => $label)
            <a href="{{ route('admin.pesanan.index', array_filter(['cari' => $cari, 'status' => $nilai])) }}" class="chip {{ $status === $nilai ? 'aktif' : '' }}">{{ $label }}</a>
        @endforeach
    </div>

    <form class="form-inline mb-20" method="GET" action="{{ route('admin.pesanan.index') }}">
        @if ($status !== '')
            <input type="hidden" name="status" value="{{ $status }}">
        @endif
        <input name="cari" type="text" class="form-control mr-2 mb-2" style="min-width:260px"
               placeholder="Cari nama, WhatsApp, atau jenis pakaian" value="{{ $cari }}">
        <button type="submit" class="submit-btn primary-btn btn-kecil mb-2">CARI</button>
    </form>

    <div class="table-responsive pesanan-card p-0">
        <table class="table pesanan-table mb-0">
            <thead>
            <tr><th>#</th><th>Pelanggan</th><th>Pakaian</th><th>Ukuran</th><th>Jumlah</th><th>Status</th><th>Tanggal</th><th></th></tr>
            </thead>
            <tbody>
            @forelse ($pesanans as $pesanan)
                <tr>
                    <td>{{ $pesanan->id }}</td>
                    <td>{{ $pesanan->nama }}<br><small class="text-muted">{{ $pesanan->whatsapp }}</small></td>
                    <td>{{ $pesanan->jenis_pakaian ?? '-' }}<br><small class="text-muted">{{ $pesanan->isRental() ? 'Rental: '.$pesanan->produk?->nama_produk : $pesanan->model_lengan }}</small></td>
                    <td>{{ $pesanan->ukuran ?? '-' }}</td>
                    <td>{{ $pesanan->jumlah }}</td>
                    <td>@include('partials.status-pesanan')</td>
                    <td>{{ $pesanan->tanggalDibuat('d M Y H:i') }}</td>
                    <td><a href="{{ route('admin.pesanan.show', $pesanan) }}" class="primary-btn btn-kecil">KELOLA</a></td>
                </tr>
            @empty
                <tr><td colspan="8" class="text-center text-muted py-4">Tidak ada pesanan.</td></tr>
            @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-20 d-flex justify-content-center">{{ $pesanans->links('pagination::bootstrap-4') }}</div>
@endsection
