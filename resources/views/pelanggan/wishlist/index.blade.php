@extends('layouts.app')

@section('judul', 'Wishlist')

@section('isi')
    <h1>Wishlist</h1>
    <p class="lead-text">{{ $produks->count() }} produk tersimpan.</p>

    <div class="row">
        @forelse ($produks as $produk)
            <div class="col-6 col-md-4 col-lg-3 mb-30">
                @include('partials.produk-kartu')
            </div>
        @empty
            <div class="col-12 text-center py-5">
                <p class="text-muted">Wishlist Anda masih kosong.</p>
                <a href="{{ route('katalog.index') }}" class="submit-btn primary-btn text-uppercase">LIHAT KATALOG<i class="arr"></i></a>
            </div>
        @endforelse
    </div>
@endsection
