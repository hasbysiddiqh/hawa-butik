{{-- Kartu produk di Collection. Variabel: $produk --}}
<div class="produk-card">
    <a href="{{ route('katalog.show', $produk) }}" class="produk-foto">
        @if ($produk->fotoUrl())
            <img src="{{ $produk->fotoUrl() }}" alt="{{ $produk->nama_produk }}">
        @else
            <span class="kosong"><i class="fa fa-picture-o"></i></span>
        @endif
    </a>
    <div class="produk-isi">
        <h5><a href="{{ route('katalog.show', $produk) }}" class="text-dark">{{ $produk->nama_produk }}</a></h5>
        <div class="produk-harga mb-2">{{ $produk->hargaRupiah() }}</div>

        <div class="mt-auto">
            @if ($produk->stokHabis())
                <span class="badge-stok">Stok habis</span>
            @else
                <a href="{{ route('pelanggan.rental.create', ['produk' => $produk->id_produk]) }}" class="primary-btn btn-kecil">SEWA</a>
            @endif
        </div>
    </div>
</div>
