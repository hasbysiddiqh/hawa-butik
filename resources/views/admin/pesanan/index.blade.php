@extends('layouts.app')

@section('judul', 'Kelola Pesanan')

@section('isi')
    <h1>Kelola Pesanan</h1>
    <p class="lead-text">{{ $rentals->total() }} rental dan {{ $kustoms->total() }} pesanan kustom ditemukan.</p>

    <form class="form-inline mb-20" method="GET" action="{{ route('admin.pesanan.index') }}">
        @if ($statusRental !== '')
            <input type="hidden" name="status_rental" value="{{ $statusRental }}">
        @endif
        @if ($statusKustom !== '')
            <input type="hidden" name="status_kustom" value="{{ $statusKustom }}">
        @endif
        <input name="cari" type="text" class="form-control mr-2 mb-2" style="min-width:260px"
               placeholder="Cari nama, WhatsApp, atau jenis pakaian" value="{{ $cari }}">
        <button type="submit" class="submit-btn primary-btn btn-kecil mb-2">CARI</button>
    </form>

    {{-- Tabel Rental --}}
    <h2 class="kelompok-judul">Rental<small>{{ $rentals->total() }}</small></h2>

    <div class="mb-20">
        <a href="{{ route('admin.pesanan.index', array_filter(['cari' => $cari, 'status_kustom' => $statusKustom])) }}" class="chip {{ $statusRental === '' ? 'aktif' : '' }}">Semua</a>
        @foreach (App\Models\Pesanan::LABEL_STATUS_RENTAL as $nilai => $label)
            <a href="{{ route('admin.pesanan.index', array_filter(['cari' => $cari, 'status_kustom' => $statusKustom, 'status_rental' => $nilai])) }}" class="chip {{ $statusRental === $nilai ? 'aktif' : '' }}">{{ $label }}</a>
        @endforeach
    </div>

    <div class="table-responsive pesanan-card p-0">
        <table class="table pesanan-table mb-0">
            <thead>
            <tr><th>#</th><th>Pelanggan</th><th>Busana</th><th>Ukuran</th><th>Jumlah</th><th>Status</th><th>Tanggal</th><th></th></tr>
            </thead>
            <tbody>
            @forelse ($rentals as $pesanan)
                <tr>
                    <td>{{ $pesanan->id }}</td>
                    <td>{{ $pesanan->nama }}<br><small class="text-muted">{{ $pesanan->whatsapp }}</small></td>
                    <td>{{ $pesanan->produk?->nama_produk ?? '-' }}<br><small class="text-muted">{{ $pesanan->jenis_pakaian ?? '-' }}</small></td>
                    <td>{{ $pesanan->ukuran ?? '-' }}</td>
                    <td>{{ $pesanan->jumlah }}</td>
                    <td>@include('partials.status-pesanan')</td>
                    <td>{{ $pesanan->tanggalDibuat('d M Y H:i') }}</td>
                    <td><a href="{{ route('admin.pesanan.show', $pesanan) }}" class="primary-btn btn-kecil">KELOLA</a></td>
                </tr>
            @empty
                <tr><td colspan="8" class="text-center text-muted py-4">Tidak ada pesanan rental.</td></tr>
            @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-20 d-flex justify-content-center">{{ $rentals->links('pagination::bootstrap-4') }}</div>

    {{-- Tabel Pesan (Kustom) --}}
    <h2 class="kelompok-judul">Pesan (Kustom)<small>{{ $kustoms->total() }}</small></h2>

    <div class="mb-20">
        <a href="{{ route('admin.pesanan.index', array_filter(['cari' => $cari, 'status_rental' => $statusRental])) }}" class="chip {{ $statusKustom === '' ? 'aktif' : '' }}">Semua</a>
        @foreach (App\Models\Pesanan::LABEL_STATUS_KUSTOM as $nilai => $label)
            <a href="{{ route('admin.pesanan.index', array_filter(['cari' => $cari, 'status_rental' => $statusRental, 'status_kustom' => $nilai])) }}" class="chip {{ $statusKustom === $nilai ? 'aktif' : '' }}">{{ $label }}</a>
        @endforeach
    </div>

    <div class="table-responsive pesanan-card p-0">
        <table class="table pesanan-table mb-0">
            <thead>
            <tr><th>#</th><th>Pelanggan</th><th>Pakaian</th><th>Ukuran</th><th>Jumlah</th><th>Status</th><th>Tanggal</th><th></th></tr>
            </thead>
            <tbody>
            @forelse ($kustoms as $pesanan)
                <tr>
                    <td>{{ $pesanan->id }}</td>
                    <td>{{ $pesanan->nama }}<br><small class="text-muted">{{ $pesanan->whatsapp }}</small></td>
                    <td>{{ $pesanan->jenis_pakaian ?? '-' }}<br><small class="text-muted">{{ $pesanan->model_lengan }}</small></td>
                    <td>{{ $pesanan->ukuran ?? '-' }}</td>
                    <td>{{ $pesanan->jumlah }}</td>
                    <td>@include('partials.status-pesanan')</td>
                    <td>{{ $pesanan->tanggalDibuat('d M Y H:i') }}</td>
                    <td><a href="{{ route('admin.pesanan.show', $pesanan) }}" class="primary-btn btn-kecil">KELOLA</a></td>
                </tr>
            @empty
                <tr><td colspan="8" class="text-center text-muted py-4">Tidak ada pesanan kustom.</td></tr>
            @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-20 d-flex justify-content-center">{{ $kustoms->links('pagination::bootstrap-4') }}</div>
@endsection
