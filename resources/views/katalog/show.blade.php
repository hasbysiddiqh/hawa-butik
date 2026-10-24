@extends('layouts.app')

@section('judul', $produk->nama_produk)

@section('isi')
    <a href="{{ route('katalog.index') }}" class="btn-back"><span class="lnr lnr-arrow-left"></span>KEMBALI KE COLLECTION</a>

    <div class="row">
        <div class="col-md-5 mb-30">
            <div class="produk-card">
                <div class="produk-foto">
                    @if ($produk->fotoUrl())
                        <img src="{{ $produk->fotoUrl() }}" alt="{{ $produk->nama_produk }}">
                    @else
                        <span class="kosong"><i class="fa fa-picture-o"></i></span>
                    @endif
                </div>
            </div>
        </div>
        <div class="col-md-7">
            <small class="text-muted text-uppercase">{{ $produk->kategori->nama_kategori }}</small>
            <h1>{{ $produk->nama_produk }}</h1>
            <div class="produk-harga mb-3" style="font-size: 22px">{{ $produk->hargaRupiah() }}</div>

            <p>{!! nl2br(e($produk->deskripsi ?: 'Belum ada deskripsi.')) !!}</p>

            <p>
                @if ($produk->stokHabis())
                    <span class="badge-stok">Stok habis</span>
                @else
                    <span class="text-muted">Stok tersedia: {{ $produk->stok }}</span>
                @endif
            </p>

            @unless ($produk->stokHabis())
                <a href="{{ route('pelanggan.rental.create', ['produk' => $produk->id_produk]) }}" class="submit-btn primary-btn text-uppercase">SEWA SEKARANG<span class="lnr lnr-arrow-right"></span></a>
            @endunless
        </div>
    </div>
@endsection
