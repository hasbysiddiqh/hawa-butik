@extends('layouts.app')

@section('judul', 'Collection')

@section('isi')
    @php($jumlah = $kelompok->sum(fn ($k) => $k->produks->count()))

    {{-- Pembuka --}}
    <section class="kat-hero reveal">
        <span class="eyebrow">Collection</span>
        <h1>Pilih busana untuk <em>momen spesialmu.</em></h1>
        <p>Koleksi Hawa Butik dikelompokkan berdasarkan jenis pakaian. Pilih yang kamu suka, lalu pesan lewat menu Pemesanan.</p>

        <form class="kat-search" method="GET" action="{{ route('katalog.index') }}">
            @if ($idKategori)
                <input type="hidden" name="kategori" value="{{ $idKategori }}">
            @endif
            <span class="lnr lnr-magnifier"></span>
            <input name="cari" type="text" placeholder="Cari nama atau deskripsi busana" value="{{ $cari }}" aria-label="Cari busana">
            @if ($cari !== '')
                <a class="kat-reset" href="{{ route('katalog.index', array_filter(['kategori' => $idKategori])) }}" aria-label="Hapus pencarian">&times;</a>
            @endif
            <button type="submit" class="primary-btn btn-kecil">Cari</button>
        </form>
    </section>

    {{-- Filter jenis pakaian --}}
    <div class="kat-chips reveal" style="--d:.08s">
        <a href="{{ route('katalog.index', array_filter(['cari' => $cari])) }}" class="chip {{ $idKategori ? '' : 'aktif' }}">Semua</a>
        @foreach ($kategoris as $kategori)
            <a href="{{ route('katalog.index', array_filter(['cari' => $cari, 'kategori' => $kategori->id_kategori])) }}"
               class="chip {{ (string) $idKategori === (string) $kategori->id_kategori ? 'aktif' : '' }}">{{ $kategori->nama_kategori }}</a>
        @endforeach
        <span class="kat-jumlah">{{ $jumlah }} busana</span>
    </div>

    @forelse ($kelompok as $kategori)
        <section class="kat-grup" id="kategori-{{ $kategori->id_kategori }}">
            <h3 class="kelompok-judul">{{ $kategori->nama_kategori }}<small>{{ $kategori->produks->count() }}</small></h3>
            <div class="row">
                @foreach ($kategori->produks as $produk)
                    <div class="col-6 col-md-4 col-lg-3 mb-30">
                        <a href="{{ route('katalog.show', $produk) }}" class="kat-card reveal" style="--d: {{ ($loop->index % 4) * 0.08 }}s">
                            <span class="kat-foto">
                                @if ($produk->fotoUrl())
                                    <img src="{{ $produk->fotoUrl() }}" alt="{{ $produk->nama_produk }}" loading="lazy">
                                @else
                                    <span class="kat-kosong lnr lnr-picture"></span>
                                @endif
                                @if ($produk->stokHabis())<em class="kat-badge">Stok habis</em>@endif
                                <span class="kat-lihat">Lihat detail<i class="arr"></i></span>
                            </span>
                            <span class="kat-info">
                                <small>{{ $kategori->nama_kategori }}</small>
                                <h5>{{ $produk->nama_produk }}</h5>
                                <b class="kat-harga">{{ $produk->hargaRupiah() }}</b>
                            </span>
                        </a>
                    </div>
                @endforeach
            </div>
        </section>
    @empty
        <div class="kat-empty reveal">
            <span class="lnr lnr-diamond"></span>
            <h4>Busana tidak ditemukan</h4>
            <p>Coba kata kunci lain atau tampilkan semua koleksi.</p>
            <a href="{{ route('katalog.index') }}" class="primary-btn btn-kecil">Lihat semua<i class="arr"></i></a>
        </div>
    @endforelse
@endsection
