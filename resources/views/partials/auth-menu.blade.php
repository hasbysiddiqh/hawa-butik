{{-- Menu di header: Katalog, lalu Masuk (tamu) atau Dashboard + Keluar (sudah login). Dipakai di beranda dan layout. --}}
<a href="{{ route('katalog.index') }}">Katalog</a>

@auth('admin')
    <a href="{{ route('admin.dashboard') }}">Dashboard</a>
@endauth
@auth('pelanggan')
    <a href="{{ route('pelanggan.dashboard') }}">Dashboard</a>
@endauth

@if (Auth::guard('admin')->check() || Auth::guard('pelanggan')->check())
    <a href="#" onclick="event.preventDefault(); document.getElementById('form-keluar').submit();">Keluar</a>
    <form id="form-keluar" action="{{ route('logout') }}" method="POST" class="d-none">@csrf</form>
@else
    <a href="{{ route('login') }}">Masuk</a>
@endif
