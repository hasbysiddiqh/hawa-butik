{{-- Menu header untuk pengguna yang sudah login.
     Pelanggan : menu beranda + tombol Dashboard + Logout.
     Admin     : hanya tombol Dashboard + Logout (menu lengkap ada di sidebar; tanpa tautan ke halaman utama). --}}
@php $adminLogin = Auth::guard('admin')->check(); @endphp
@unless ($adminLogin)
    <a href="{{ route('home') }}">Home</a>
    <a href="{{ route('home') }}#about">About</a>
    <a href="{{ route('home') }}#gallery">Collection</a>
    <a href="{{ route('home') }}#contact">Contact</a>
    <a href="{{ route('pelanggan.pemesanan') }}">Order</a>
@endunless
<a href="{{ $adminLogin ? route('admin.dashboard') : route('pelanggan.dashboard') }}" class="nav-pill">Dashboard</a>
<form method="POST" action="{{ route('logout') }}" class="nav-form">
    @csrf
    <button type="submit" class="nav-pill nav-pill-outline">Logout</button>
</form>
