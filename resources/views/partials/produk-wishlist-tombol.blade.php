{{-- Tombol wishlist di halaman detail. Variabel: $produk, $idWishlist --}}
@auth('pelanggan')
    @if (in_array($produk->id_produk, $idWishlist, true))
        <form action="{{ route('pelanggan.wishlist.destroy', $produk) }}" method="POST">
            @csrf
            @method('DELETE')
            <button type="submit" class="submit-btn primary-btn text-uppercase">HAPUS DARI WISHLIST<span class="lnr lnr-arrow-right"></span></button>
        </form>
    @else
        <form action="{{ route('pelanggan.wishlist.store', $produk) }}" method="POST">
            @csrf
            <button type="submit" class="submit-btn primary-btn text-uppercase">SIMPAN KE WISHLIST<span class="lnr lnr-arrow-right"></span></button>
        </form>
    @endif
@else
    <a href="{{ route('login') }}" class="submit-btn primary-btn text-uppercase">MASUK UNTUK MENYIMPAN<span class="lnr lnr-arrow-right"></span></a>
@endauth
