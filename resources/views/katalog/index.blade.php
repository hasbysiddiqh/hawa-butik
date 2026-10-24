@extends('layouts.app')

@section('judul', 'Collection')

@section('isi')
    <h1>Collection</h1>
    <p class="lead-text">Koleksi busana Hawa Butik, dikelompokkan berdasarkan jenis pakaian. Pilih yang kamu suka lalu sewa lewat menu Pemesanan.</p>

    <form class="form-inline mb-20" method="GET" action="{{ route('katalog.index') }}">
        @if ($idKategori)
            <input type="hidden" name="kategori" value="{{ $idKategori }}">
        @endif
        <input name="cari" type="text" class="form-control mr-2 mb-2" style="min-width:260px"
               placeholder="Cari nama atau deskripsi busana" value="{{ $cari }}">
        <button type="submit" class="submit-btn primary-btn btn-kecil mb-2">CARI</button>
    </form>

    <div class="mb-20">
        <a href="{{ route('katalog.index', array_filter(['cari' => $cari])) }}" class="chip {{ $idKategori ? '' : 'aktif' }}">Semua</a>
        @foreach ($kategoris as $kategori)
            <a href="{{ route('katalog.index', array_filter(['cari' => $cari, 'kategori' => $kategori->id_kategori])) }}"
               class="chip {{ (string) $idKategori === (string) $kategori->id_kategori ? 'aktif' : '' }}">{{ $kategori->nama_kategori }}</a>
        @endforeach
    </div>

    @forelse ($kelompok as $kategori)
        <section class="mb-30" id="kategori-{{ $kategori->id_kategori }}">
            <h3 class="kelompok-judul">{{ $kategori->nama_kategori }}</h3>
            <div class="row">
                @foreach ($kategori->produks as $produk)
                    <div class="col-6 col-md-4 col-lg-3 mb-30">
                        @include('partials.produk-kartu')
                    </div>
                @endforeach
            </div>
        </section>
    @empty
        <p class="text-center text-muted py-5">Produk tidak ditemukan.</p>
    @endforelse
@endsection
