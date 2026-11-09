{{-- Sub-navigasi Collection (admin): hanya Produk dan Kategori, di tengah atas halaman. --}}
@php
    $menu = [
        ['admin.produk.index', 'admin.produk.*', 'Produk'],
        ['admin.kategori.index', 'admin.kategori.*', 'Kategori'],
    ];
@endphp
<ul class="subnav-koleksi" aria-label="Collection">
    @foreach ($menu as [$rute, $pola, $label])
        <li><a class="{{ request()->routeIs($pola) ? 'active' : '' }}" href="{{ route($rute) }}" @if (request()->routeIs($pola)) aria-current="page" @endif>{{ $label }}</a></li>
    @endforeach
</ul>
