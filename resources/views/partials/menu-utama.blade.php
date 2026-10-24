{{-- Isi menu hamburger (dipakai di beranda dan layout).
     Home selalu ke beranda yang sama, sebelum maupun sesudah login. --}}
@php
    $adminLogin = Auth::guard('admin')->check();
    $pelangganLogin = Auth::guard('pelanggan')->check();
@endphp
<a href="{{ route('home') }}">Home</a>
<a href="{{ route('home') }}#about">About Me</a>
{{-- Collection menggulir ke bagian "Our Collection" di beranda (id="gallery"), bukan ke halaman katalog. --}}
<a href="{{ route('home') }}#gallery">Collection</a>
<a href="{{ route('home') }}#contact">Contact</a>

@unless ($adminLogin)
    <a href="{{ route('pelanggan.pemesanan') }}">Pemesanan</a>
@endunless

@if ($adminLogin)
    <a href="{{ route('admin.dashboard') }}">Kelola Toko</a>
    <a href="{{ route('admin.profil') }}">Profil</a>
@elseif ($pelangganLogin)
    <a href="{{ route('pelanggan.pesanan.index') }}">Pesanan Saya</a>
    <a href="{{ route('pelanggan.profil.edit') }}">Profil</a>
@else
    <a href="{{ route('login') }}">Masuk</a>
@endif
