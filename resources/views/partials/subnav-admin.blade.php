@php
    $menu = [
        ['admin.dashboard', 'admin.dashboard', 'Ringkasan'],
        ['admin.pesanan.index', 'admin.pesanan.*', 'Pesanan'],
        ['admin.produk.index', 'admin.produk.*', 'Produk'],
        ['admin.kategori.index', 'admin.kategori.*', 'Kategori'],
    ];
@endphp
<ul class="nav subnav">
    @foreach ($menu as [$rute, $pola, $label])
        <li class="nav-item"><a class="nav-link {{ request()->routeIs($pola) ? 'active' : '' }}" href="{{ route($rute) }}">{{ $label }}</a></li>
    @endforeach
</ul>
