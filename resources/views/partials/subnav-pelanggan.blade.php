@php
    $menu = [
        ['pelanggan.dashboard', 'pelanggan.dashboard', 'Ringkasan'],
        ['pelanggan.pesanan.index', 'pelanggan.pesanan.*', 'Pesanan'],
        ['katalog.index', 'katalog.*', 'Collection'],
    ];
@endphp
<ul class="nav subnav">
    @foreach ($menu as [$rute, $pola, $label])
        <li class="nav-item"><a class="nav-link {{ request()->routeIs($pola) ? 'active' : '' }}" href="{{ route($rute) }}">{{ $label }}</a></li>
    @endforeach
</ul>
