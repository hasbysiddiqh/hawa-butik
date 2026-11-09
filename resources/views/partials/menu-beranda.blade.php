{{-- Menu header BERANDA. Tamu: menu + tombol Login. Pengguna login: menu dashboard (admin/pelanggan). --}}
@if (Auth::guard('pelanggan')->check() || Auth::guard('admin')->check())
    @include('partials.menu-dashboard')
@else
    <a href="{{ route('home') }}">Home</a>
    <a href="{{ route('home') }}#about">About</a>
    <a href="{{ route('home') }}#gallery">Collection</a>
    <a href="{{ route('home') }}#contact">Contact</a>
    <a href="{{ route('pelanggan.pemesanan') }}">Order</a>
    <a href="{{ route('login') }}" class="nav-pill">Login</a>
@endif
