{{-- Sidebar dashboard: otomatis admin atau pelanggan. --}}
@php
    $admin = request()->routeIs('admin.*');
    $u = Auth::guard($admin ? 'admin' : 'pelanggan')->user();
    $namaSb = \App\Support\DataDashboard::pick($u, ['nama', 'nama_lengkap', 'nama_pelanggan', 'name', 'username'], $admin ? 'Admin' : 'Pelanggan');
    $r = fn ($nama) => \Illuminate\Support\Facades\Route::has($nama) ? route($nama) : '#';
    // [label, ikon, route tujuan, pola route aktif, (opsional) pola route yang dikecualikan]
    $menu = $admin ? [
        ['Dashboard', 'lnr-home', 'admin.dashboard', ['admin.dashboard']],
        ['Kelola Pesanan', 'lnr-inbox', 'admin.pesanan.index', ['admin.pesanan.*']],
        ['Kelola Produk', 'lnr-tag', 'admin.produk.index', ['admin.produk.*']],
        ['Kelola Kategori', 'lnr-list', 'admin.kategori.index', ['admin.kategori.*']],
        ['Kelola Transaksi', 'lnr-chart-bars', 'admin.transaksi.index', ['admin.transaksi.*']],
        ['Profil Admin', 'lnr-user', 'admin.profil', ['admin.profil']],
    ] : [
        ['Dashboard', 'lnr-home', 'pelanggan.dashboard', ['pelanggan.dashboard']],
        ['Pemesanan', 'lnr-cart', 'pelanggan.pemesanan', ['pelanggan.pemesanan', 'pelanggan.rental.*', 'pelanggan.pesanan.create', 'pelanggan.pesanan.store']],
        ['Pesanan Saya', 'lnr-list', 'pelanggan.pesanan.index', ['pelanggan.pesanan.*'], ['pelanggan.pesanan.create', 'pelanggan.pesanan.store']],
        ['Collection', 'lnr-diamond', 'katalog.index', ['katalog.*']],
        ['Wishlist', 'lnr-heart', 'pelanggan.wishlist.index', ['pelanggan.wishlist.*']],
        ['Profil Saya', 'lnr-user', 'pelanggan.profil.edit', ['pelanggan.profil.*']],
    ];
    // Sembunyikan menu yang route-nya belum ada; lengkapi pola "dikecualikan" yang kosong.
    $menu = array_values(array_filter($menu, fn ($m) => \Illuminate\Support\Facades\Route::has($m[2])));
    $menu = array_map(fn ($m) => $m + [4 => []], $menu);
@endphp
<aside class="dash-side">
    <div class="dash-user">
        <span class="dash-avatar">{{ \Illuminate\Support\Str::upper(\Illuminate\Support\Str::substr($namaSb, 0, 1)) }}</span>
        <div>
            <b>{{ \Illuminate\Support\Str::limit($namaSb, 18) }}</b>
            <small>{{ $admin ? 'Admin toko' : 'Pelanggan' }}</small>
        </div>
    </div>
    <nav class="dash-nav">
        <span class="dash-pill" aria-hidden="true"></span>
        <span class="dash-pill dash-pill-hover" aria-hidden="true"></span>
        @foreach ($menu as [$label, $ikon, $tujuan, $aktif, $kecuali])
            <a href="{{ $r($tujuan) }}" class="{{ request()->routeIs(...$aktif) && ! request()->routeIs(...($kecuali ?: ['-'])) ? 'aktif' : '' }}">
                <span class="lnr {{ $ikon }}"></span>{{ $label }}
            </a>
        @endforeach
    </nav>
    <form method="POST" action="{{ route('logout') }}" class="keluar">
        @csrf
        <button type="submit"><span class="lnr lnr-exit"></span>Keluar</button>
    </form>
</aside>

@push('scripts')
    <script>
        /* Penanda menu bergeser: dari menu halaman sebelumnya ke menu aktif, dan bayangan lembut mengikuti kursor. */
        (function () {
            var nav = document.querySelector('.dash-nav');
            if (!nav) { return; }
            var links = [].slice.call(nav.querySelectorAll('a'));
            var aktif = nav.querySelector('a.aktif');
            var pill = nav.querySelector('.dash-pill:not(.dash-pill-hover)');
            var bayangan = nav.querySelector('.dash-pill-hover');
            var kunci = 'hbMenuDashboard';

            function taruh(el, link, langsung) {
                if (!link) { el.style.opacity = 0; return; }
                if (langsung) { el.style.transition = 'none'; }
                el.style.opacity = 1;
                el.style.width = link.offsetWidth + 'px';
                el.style.height = link.offsetHeight + 'px';
                el.style.transform = 'translate(' + link.offsetLeft + 'px,' + link.offsetTop + 'px)';
                if (langsung) { el.offsetHeight; el.style.transition = ''; }
            }
            function simpan(link) { try { sessionStorage.setItem(kunci, links.indexOf(link)); } catch (e) {} }

            nav.classList.add('pill-on');
            var asal = aktif;
            try { var lama = sessionStorage.getItem(kunci); if (lama !== null && links[+lama]) { asal = links[+lama]; } } catch (e) {}
            if (aktif) {
                taruh(pill, asal, true);
                requestAnimationFrame(function () { requestAnimationFrame(function () { taruh(pill, aktif, false); }); });
                simpan(aktif);
            }
            links.forEach(function (link) {
                link.addEventListener('mouseenter', function () {
                    if (link === aktif) { bayangan.style.opacity = 0; return; }
                    taruh(bayangan, link, bayangan.style.opacity !== '1');
                });
                link.addEventListener('click', function () { taruh(pill, link, false); bayangan.style.opacity = 0; simpan(link); });
            });
            nav.addEventListener('mouseleave', function () { bayangan.style.opacity = 0; });
            window.addEventListener('resize', function () { taruh(pill, aktif, true); });
        })();
    </script>
@endpush
