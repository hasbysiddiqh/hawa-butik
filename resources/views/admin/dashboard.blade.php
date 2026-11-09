@extends('layouts.dashboard')
@section('judul', 'Dashboard Admin')

@section('isi')
<div class="dash-top">
    <div>
        <small>Selamat datang kembali, {{ $nama }} 👋</small>
        <h1>Dashboard Admin</h1>
    </div>
    <span class="dchip">{{ $tanggal }}</span>
</div>

{{-- Kartu angka --}}
<div class="dstats">
    <a class="dstat aksen" href="{{ route('admin.pesanan.index') }}">
        <span class="ico lnr lnr-inbox"></span>
        <div><b>{{ $perluN }}</b><small>Perlu diproses</small></div>
    </a>
    <a class="dstat" href="{{ route('admin.transaksi.index') }}">
        <span class="ico lnr lnr-chart-bars"></span>
        <div><b>{{ \App\Support\DataDashboard::rupiah($pendapatan) }}</b><small>Pembayaran masuk</small></div>
    </a>
    <div class="dstat">
        <span class="ico lnr lnr-users"></span>
        <div><b>{{ $pelangganN }}</b><small>Pelanggan</small></div>
    </div>
    <a class="dstat" href="{{ route('admin.produk.index') }}">
        <span class="ico lnr lnr-tag"></span>
        <div><b>{{ $produkN }}</b><small>Produk aktif</small></div>
    </a>
</div>

<div class="dgrid cols-main sama-tinggi">
    <div class="dcol">
        {{-- Perlu diproses --}}
        <div class="dc">
            <div class="dc-head"><h5>Perlu diproses</h5><a href="{{ route('admin.pesanan.index') }}">Lihat semua ›</a></div>
            @if ($perlu)
                <div class="dproses">
                    <span class="dash-avatar big">{{ \Illuminate\Support\Str::upper(\Illuminate\Support\Str::substr($perlu['pelanggan'], 0, 1)) }}</span>
                    <div class="grow">
                        <b>{{ $perlu['pelanggan'] }}</b>
                        <small>{{ $perlu['jenis'] }} · {{ $perlu['jumlah'] }} pcs · {{ $perlu['tanggal'] }}</small>
                        <div>{{ $perlu['pakaian'] }}</div>
                    </div>
                    @if ($perlu['foto'])<img class="dthumb" src="{{ $perlu['foto'] }}" alt="">@endif
                </div>
                <a class="primary-btn btn-kecil" href="{{ route('admin.pesanan.show', $perlu['id']) }}">Kelola Pesanan<i class="arr"></i></a>
            @else
                <p class="dempty">Tidak ada pesanan baru. Semua sudah ditangani 🎉</p>
            @endif
        </div>

        {{-- Pesanan terbaru --}}
        <div class="dc">
            <div class="dc-head"><h5>Pesanan terbaru</h5><a href="{{ route('admin.pesanan.index') }}">Lihat semua ›</a></div>
            @if ($terbaru->isEmpty())
                <p class="dempty">Belum ada pesanan.</p>
            @else
                <div class="dtable-wrap">
                    <table class="dtable">
                        <thead><tr><th>Pelanggan</th><th>Pakaian</th><th>Status</th><th>Tanggal</th></tr></thead>
                        <tbody>
                        @foreach ($terbaru as $t)
                            <tr>
                                <td><span class="dmini">{{ \Illuminate\Support\Str::upper(\Illuminate\Support\Str::substr($t['pelanggan'], 0, 1)) }}</span><a href="{{ route('admin.pesanan.show', $t['id']) }}">{{ $t['pelanggan'] }}</a></td>
                                <td>{{ $t['pakaian'] }} × {{ $t['jumlah'] }}<small class="d-block text-muted">{{ $t['jenis'] }}</small></td>
                                <td><span class="st {{ $t['kelas'] }}">{{ $t['status'] }}</span></td>
                                <td>{{ $t['tanggal'] }}</td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>
    </div>

    <div class="dcol">
        {{-- Statistik --}}
        <div class="dc">
            <div class="dc-head"><h5>Statistik pesanan</h5><a href="{{ route('admin.pesanan.index') }}">Lihat semua ›</a></div>
            @php $tot = max($ringkas['total'], 1); @endphp
            <div class="dseg">
                <i class="seg-baru" style="width:{{ $ringkas['baru'] / $tot * 100 }}%"></i>
                <i class="seg-proses" style="width:{{ $ringkas['proses'] / $tot * 100 }}%"></i>
                <i class="seg-selesai" style="width:{{ $ringkas['selesai'] / $tot * 100 }}%"></i>
                <i class="seg-batal" style="width:{{ $ringkas['batal'] / $tot * 100 }}%"></i>
            </div>
            <div class="dlegend">
                <span><b>{{ $ringkas['total'] }}</b>Total</span>
                <span><i class="seg-baru"></i><b>{{ $ringkas['baru'] }}</b>Baru</span>
                <span><i class="seg-proses"></i><b>{{ $ringkas['proses'] }}</b>Dikerjakan</span>
                <span><i class="seg-selesai"></i><b>{{ $ringkas['selesai'] }}</b>Selesai</span>
                <span><i class="seg-batal"></i><b>{{ $ringkas['batal'] }}</b>Batal</span>
            </div>
            <small class="dlabel">Pesanan per bulan</small>
            @include('partials.grafik-garis', ['g' => $bulan, 'gid' => 'adm'])
        </div>

        {{-- Transaksi --}}
        <div class="dc">
            <div class="dc-head"><h5>Transaksi</h5><a href="{{ route('admin.transaksi.index') }}">Kelola ›</a></div>
            <div class="dlegend ringkas-transaksi">
                <span><b>{{ $transaksi['lunas'] }}</b><small><i class="seg-selesai"></i>Lunas</small></span>
                <span><b>{{ $transaksi['dicicil'] }}</b><small><i class="seg-proses"></i>Dicicil</small></span>
                <span><b>{{ $transaksi['belum'] }}</b><small><i class="seg-baru"></i>Belum bayar</small></span>
            </div>
        </div>

        {{-- Promo / pengingat stok --}}
        <div class="dpromo">
            <small>Pengingat</small>
            @if ($stokHabis > 0)
                <h5>Perbarui stok {{ $stokHabis }} produk yang habis</h5>
                <a class="primary-btn btn-kecil btn-krem" href="{{ route('admin.produk.index') }}">Kelola produk</a>
            @else
                <h5>Koleksi sudah siap. Tambah busana baru?</h5>
                <a class="primary-btn btn-kecil btn-krem" href="{{ route('admin.produk.index') }}">Kelola produk</a>
            @endif
        </div>
    </div>
</div>
@endsection
