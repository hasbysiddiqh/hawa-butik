@extends('layouts.app')

@section('judul', 'Kelola Transaksi')

@section('isi')
    <div class="d-flex justify-content-between align-items-end flex-wrap mb-20">
        <div>
            <h1>Kelola Transaksi</h1>
            <p class="lead-text mb-0">Pantau pembayaran rental dan pesanan jahit custom.</p>
        </div>
    </div>

    @include('partials.validation-errors')

    {{-- Ringkasan sesuai saringan yang sedang dipakai --}}
    <div class="dstats">
        <div class="dstat">
            <span class="ico lnr lnr-list"></span>
            <div><b>{{ $jumlahTransaksi }}</b><small>Transaksi</small></div>
        </div>
        <div class="dstat">
            <span class="ico lnr lnr-tag"></span>
            <div><b>Rp {{ number_format($totalTagihan, 0, ',', '.') }}</b><small>Total tagihan</small></div>
        </div>
        <div class="dstat aksen">
            <span class="ico lnr lnr-chart-bars"></span>
            <div><b>Rp {{ number_format($totalDibayar, 0, ',', '.') }}</b><small>Sudah dibayar</small></div>
        </div>
        <div class="dstat">
            <span class="ico lnr lnr-hourglass"></span>
            <div><b>Rp {{ number_format($sisaTagihan, 0, ',', '.') }}</b><small>Belum dibayar</small></div>
        </div>
    </div>

    <div class="mb-20">
        <a href="{{ route('admin.transaksi.index', array_filter(['cari' => $cari])) }}" class="chip {{ $status === '' ? 'aktif' : '' }}">Semua</a>
        @foreach (App\Models\Pembayaran::LABEL_STATUS as $nilai => $label)
            <a href="{{ route('admin.transaksi.index', array_filter(['cari' => $cari, 'status' => $nilai])) }}" class="chip {{ $status === $nilai ? 'aktif' : '' }}">{{ $label }}</a>
        @endforeach
    </div>

    <form class="form-inline" method="GET" action="{{ route('admin.transaksi.index') }}">
        @if ($status !== '')
            <input type="hidden" name="status" value="{{ $status }}">
        @endif
        <input name="cari" type="text" class="form-control" placeholder="Cari nama atau WhatsApp pelanggan" value="{{ $cari }}">
        <button type="submit" class="submit-btn primary-btn btn-kecil">CARI</button>
    </form>

    <div class="table-responsive pesanan-card p-0">
        <table class="table pesanan-table mb-0">
            <thead>
            <tr>
                <th>ID</th>
                <th>Pelanggan</th>
                <th>Total</th>
                <th>Dibayar</th>
                <th>Metode</th>
                <th>Status</th>
                <th>Tgl bayar</th>
                <th></th>
            </tr>
            </thead>
            <tbody>
            @forelse ($transaksis as $transaksi)
                @php($pesanan = $transaksi->pesanan)
                <tr>
                    <td>#{{ $transaksi->id_bayar }}</td>
                    <td>
                        {{ $pesanan->pelanggan?->nama_pelanggan ?? $pesanan->nama }}
                        <small class="d-block text-muted">Pesanan #{{ $pesanan->id }} &middot; {{ $pesanan->isRental() ? 'Rental' : 'Jahit custom' }}</small>
                    </td>
                    <td class="uang">
                        @if ($transaksi->totalTagihan() > 0)
                            Rp {{ number_format($transaksi->totalTagihan(), 0, ',', '.') }}
                        @else
                            <span class="text-muted">Belum ditetapkan</span>
                        @endif
                    </td>
                    <td class="uang">
                        Rp {{ number_format((int) $transaksi->jumlah_bayar, 0, ',', '.') }}
                        @if ($transaksi->status_bayar === 'dicicil')
                            <small class="d-block text-muted">Sisa Rp {{ number_format($transaksi->sisaBayar(), 0, ',', '.') }}</small>
                        @endif
                    </td>
                    <td class="tgl">{{ $transaksi->labelMetode() }}</td>
                    <td><span class="st {{ $transaksi->kelasStatus() }}">{{ $transaksi->labelStatus() }}</span></td>
                    <td class="tgl">{{ $transaksi->tgl_bayar?->format('d M Y, H:i') ?? '-' }}</td>
                    <td class="text-nowrap"><a href="{{ route('admin.transaksi.show', $transaksi) }}" class="primary-btn btn-kecil">KELOLA</a></td>
                </tr>
            @empty
                <tr><td colspan="8" class="text-center text-muted py-4">Belum ada transaksi.</td></tr>
            @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-20 d-flex justify-content-center">{{ $transaksis->links('pagination::bootstrap-4') }}</div>
@endsection
