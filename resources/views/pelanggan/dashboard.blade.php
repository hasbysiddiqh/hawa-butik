@extends('layouts.dashboard')
@section('judul', 'Dashboard')

@section('isi')
<div class="dash-top">
    <div>
        <small>Halo, {{ $nama }} 👋</small>
        <h1>Dashboard Pelanggan</h1>
    </div>
    <span class="dchip">{{ $tanggal }}</span>
</div>

<div class="dgrid cols-main">
    {{-- Hero pesanan aktif --}}
    <div class="dhero">
        <small>Pesanan aktif</small>
        <div class="dhero-num">{{ $aktif }}<span>pesanan</span></div>
        <div class="dhero-chart">@include('partials.grafik-garis', ['g' => $bulan, 'gid' => 'plg'])</div>
        <div class="dhero-foot">Total {{ $total }} pesanan sejak bergabung</div>
    </div>

    {{-- Ring pesanan selesai --}}
    <div class="dc dring-card">
        <div class="dc-head"><h5>Pesanan selesai</h5></div>
        <div class="dring" style="--p: {{ $persen }}"><span>{{ $persen }}%</span></div>
        <div class="dlegend center">
            <span><i class="seg-selesai"></i><b>{{ $selesai }}</b>Selesai</span>
            <span><i class="seg-baru"></i><b>{{ $aktif }}</b>Berjalan</span>
        </div>
    </div>
</div>

<div class="dgrid cols-main">
    <div class="dgrid">
        {{-- Jenis pemesanan --}}
        <div class="dc">
            <div class="dc-head"><h5>Jenis pemesanan</h5></div>
            <div class="dquick">
                <a href="{{ route('pelanggan.rental.create') }}"><span class="lnr lnr-calendar-full"></span>Rental</a>
                <a href="{{ route('pelanggan.pesanan.create') }}"><span class="lnr lnr-pencil"></span>Jahit custom</a>
                <a href="{{ route('home') }}#gallery"><span class="lnr lnr-diamond"></span>Collection</a>
            </div>
        </div>

        {{-- Pesanan terbaru --}}
        <div class="dc">
            <div class="dc-head"><h5>Pesanan terbaru</h5><a href="{{ route('pelanggan.pesanan.index') }}">Lihat semua ›</a></div>
            @if ($terbaru->isEmpty())
                <p class="dempty">Belum ada pesanan. Yuk mulai dari Collection kami ✨</p>
            @else
                <ul class="dlist">
                    @foreach ($terbaru as $t)
                        <li>
                            <span class="dmini"><span class="lnr {{ $t['jenis'] === 'Rental' ? 'lnr-calendar-full' : 'lnr-pencil' }}"></span></span>
                            <div class="grow">
                                <b>{{ $t['pakaian'] }}</b>
                                <small>{{ $t['jenis'] }} · {{ $t['jumlah'] }} pcs · {{ $t['tanggal'] }}</small>
                            </div>
                            <span class="st {{ $t['kelas'] }}">{{ $t['status'] }}</span>
                            <a class="dgo" href="{{ route('pelanggan.pesanan.show', $t['id']) }}" aria-label="Detail">›</a>
                        </li>
                    @endforeach
                </ul>
            @endif
        </div>
    </div>

    {{-- Collection --}}
    <div class="dc">
        <div class="dc-head"><h5>Collection</h5><a href="{{ route('katalog.index') }}">Lihat semua ›</a></div>
        @if ($koleksi->isEmpty())
            <p class="dempty">Belum ada koleksi.</p>
        @else
            <ul class="dlist">
                @foreach ($koleksi as $k)
                    <li>
                        @if ($k['foto'])<img class="dthumb" src="{{ $k['foto'] }}" alt="">@else<span class="dthumb kosong lnr lnr-picture"></span>@endif
                        <div class="grow">
                            <b>{{ \Illuminate\Support\Str::limit($k['nama'], 22) }}</b>
                            <small>{{ $k['harga'] ?: $k['kategori'] }}</small>
                        </div>
                        <a class="dgo" href="{{ route('katalog.show', $k['id']) }}" aria-label="Lihat">›</a>
                    </li>
                @endforeach
            </ul>
        @endif
    </div>
</div>
@endsection
