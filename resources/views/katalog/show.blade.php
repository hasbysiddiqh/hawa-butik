@extends('layouts.app')

@section('judul', $produk->nama_produk)

@section('isi')
    <a href="{{ route('katalog.index') }}" class="btn-back"><i class="arr arr-left"></i>Kembali ke Collection</a>

    <div class="kat-show">
        <div class="kat-show-foto reveal">
            @if ($produk->fotoUrl())
                <img src="{{ $produk->fotoUrl() }}" alt="{{ $produk->nama_produk }}">
            @else
                <span class="kat-kosong lnr lnr-picture"></span>
            @endif
        </div>

        <div class="kat-show-info reveal" style="--d:.1s">
            <a class="kat-pil" href="{{ route('katalog.index', ['kategori' => $produk->kategori->id_kategori]) }}">{{ $produk->kategori->nama_kategori }}</a>
            <h1>{{ $produk->nama_produk }}</h1>
            <div class="kat-show-harga">{{ $produk->hargaRupiah() }}</div>

            <p class="kat-show-desk">{!! nl2br(e($produk->deskripsi ?: 'Belum ada deskripsi.')) !!}</p>

            <p class="kat-show-stok">
                @if ($produk->stokHabis())
                    <span class="badge-stok">Stok habis</span>
                @else
                    <span class="kat-tersedia"></span>Stok tersedia: {{ $produk->stok }}
                @endif
            </p>

            <div class="kat-show-aksi">
                @unless ($produk->stokHabis())
                    <a href="{{ route('pelanggan.rental.create', ['produk' => $produk->id_produk]) }}" class="primary-btn">Sewa Sekarang<i class="arr"></i></a>
                @endunless
            </div>
        </div>
    </div>
@endsection
