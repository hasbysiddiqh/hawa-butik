{{-- Tautan akun di menu navigasi: Masuk/Daftar (tamu), atau nama + Keluar (sudah login). --}}
@if (auth('admin')->check())
    <a href="{{ route('admin.dashboard') }}">Dashboard Admin</a>
    <a href="#" onclick="event.preventDefault(); document.getElementById('form-logout').submit();">Keluar</a>
    <form id="form-logout" action="{{ route('admin.logout') }}" method="POST" class="d-none">@csrf</form>
@elseif (auth('pelanggan')->check())
    <a href="#" style="cursor:default">Halo, {{ Str::before(auth('pelanggan')->user()->nama_pelanggan, ' ') }}</a>
    <a href="#" onclick="event.preventDefault(); document.getElementById('form-logout').submit();">Keluar</a>
    <form id="form-logout" action="{{ route('logout') }}" method="POST" class="d-none">@csrf</form>
@else
    <a href="{{ route('login') }}">Masuk</a>
    <a href="{{ route('register') }}">Daftar</a>
@endif
