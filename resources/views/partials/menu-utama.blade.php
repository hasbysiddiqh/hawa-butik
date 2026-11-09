{{-- Menu header (bersama untuk beranda & halaman dalam). Tombol khusus: Login / Logout. --}}
@php
    $pelanggan = Auth::guard('pelanggan')->check();
    $admin = Auth::guard('admin')->check();
@endphp
<a href="{{ route('home') }}">Home</a>
<a href="{{ route('home') }}#about">About</a>
<a href="{{ route('katalog.index') }}">Collection</a>
<a href="{{ route('home') }}#contact">Contact</a>
@if ($admin)
    <a href="{{ route('admin.dashboard') }}">Dashboard</a>
@else
    <a href="{{ route('pelanggan.pemesanan') }}">Order</a>
    @if ($pelanggan)
        <a href="{{ route('pelanggan.pesanan.index') }}">My Orders</a>
    @endif
@endif

@if ($pelanggan || $admin)
    <form class="nav-form" action="{{ route('logout') }}" method="POST">
        @csrf
        <button type="submit" class="nav-pill nav-pill-outline">Logout</button>
    </form>
@else
    <a href="{{ route('login') }}" class="nav-pill">Login</a>
@endif
