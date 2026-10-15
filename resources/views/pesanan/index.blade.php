@extends('pesanan.layout')

@section('judul', 'Daftar Pesanan')

@section('isi')
    @if (session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <div class="d-flex justify-content-between align-items-end flex-wrap mb-20">
        <div>
            <h1>Daftar Pesanan</h1>
            <p class="lead-text mb-0">{{ $pesanans->total() }} pesanan tercatat.</p>
        </div>
        <a href="{{ route('pesanan.create') }}" class="submit-btn primary-btn text-uppercase">BUAT PESANAN<span class="lnr lnr-arrow-right"></span></a>
    </div>

    <form class="form-inline mb-20" method="GET" action="{{ route('pesanan.index') }}">
        <input name="cari" type="text" class="form-control mr-2 mb-2" style="min-width:260px"
               placeholder="Cari nama, WhatsApp, atau jenis pakaian" value="{{ $cari }}">
        <button type="submit" class="submit-btn primary-btn btn-kecil mb-2">CARI</button>
    </form>

    <div class="table-responsive pesanan-card p-0">
        <table class="table pesanan-table mb-0">
            <thead>
            <tr>
                <th>#</th>
                <th>Pelanggan</th>
                <th>Pakaian</th>
                <th>Ukuran</th>
                <th>Jumlah</th>
                <th>Status</th>
                <th>Tanggal</th>
                <th></th>
            </tr>
            </thead>
            <tbody>
            @forelse ($pesanans as $pesanan)
                <tr>
                    <td>{{ $pesanan->id }}</td>
                    <td>{{ $pesanan->nama }}<br><small class="text-muted">{{ $pesanan->whatsapp }}</small></td>
                    <td>{{ $pesanan->jenis_pakaian ?? '-' }}<br><small class="text-muted">{{ $pesanan->model_lengan }}</small></td>
                    <td>{{ $pesanan->ukuran ?? '-' }}</td>
                    <td>{{ $pesanan->jumlah }}</td>
                    <td><span class="badge-status {{ $pesanan->status }}">{{ ucfirst($pesanan->status) }}</span></td>
                    <td>{{ $pesanan->created_at?->format('d M Y H:i') . ' WITA' }}</td>
                    <td><a href="{{ route('pesanan.show', $pesanan) }}" class="primary-btn btn-kecil">DETAIL</a></td>
                </tr>
            @empty
                <tr><td colspan="8" class="text-center text-muted py-4">Belum ada pesanan.</td></tr>
            @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-20 d-flex justify-content-center">{{ $pesanans->links('pagination::bootstrap-4') }}</div>
@endsection
