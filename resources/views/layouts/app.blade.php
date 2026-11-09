<!DOCTYPE html>
<html lang="id" class="no-js">
<head>
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta charset="UTF-8">
    <title>@yield('judul', 'Beranda') - Hawa Butik</title>

    <link rel="stylesheet" href="{{ asset('css/linearicons.css') }}">
    <link rel="stylesheet" href="{{ asset('css/font-awesome.min.css') }}">
    <link rel="stylesheet" href="{{ asset('css/magnific-popup.css') }}">
    <link rel="stylesheet" href="{{ asset('css/nice-select.css') }}">
    <link rel="stylesheet" href="{{ asset('css/owl.carousel.css') }}">
    <link rel="stylesheet" href="{{ asset('css/simplelightbox.min.css') }}">
    <link rel="stylesheet" href="{{ asset('css/bootstrap.css') }}">
    <link rel="stylesheet" href="{{ asset('css/main.css') }}">

    <style>
        .page-pesanan { padding: 130px 0 90px; min-height: 100vh; }
        .page-pesanan h1 { margin-bottom: 10px; }
        .page-pesanan .lead-text { color: #777; margin-bottom: 30px; }
        .pesanan-form label { font-size: 13px; font-weight: 700; text-transform: uppercase; letter-spacing: .5px; margin: 18px 0 6px; color: #222; }
        .pesanan-form .form-control { border-radius: 0; background-color: transparent; border: 1px solid #ccc; color: #000; font-size: 14px; padding: 12px; height: auto; }
        .pesanan-form .form-check-label { margin: 0; font-size: 14px; font-weight: 600; text-transform: none; letter-spacing: 0; }
        .pesanan-form textarea.form-control { height: 130px; }
        .pesanan-form .lain { display: none; margin-top: 10px; }
        .pesanan-form .lain.tampil { display: block; }
        .primary-btn.btn-kecil { line-height: 34px; padding-left: 22px; padding-right: 22px; font-size: 13px; font-weight: 600; text-transform: uppercase; }
        .btn-back { display: inline-flex; align-items: center; margin-bottom: 20px; font-size: 13px; font-weight: 700; text-transform: uppercase; letter-spacing: .5px; color: #222; }
        .btn-back .arr { margin-right: 8px; }
        .btn-back:hover { color: #3F6853; text-decoration: none; }
        .btn-back:hover .arr { transform: translateX(-4px); }
        .subnav { border-bottom: 1px solid #eee; margin-bottom: 30px; flex-wrap: wrap; }
        .subnav .nav-link { color: #222; font-size: 12px; font-weight: 700; text-transform: uppercase; letter-spacing: .5px; padding: 10px 18px; border-bottom: 3px solid transparent; }
        .subnav .nav-link:hover { color: #5A8670; text-decoration: none; }
        .subnav .nav-link.active { color: #3F6853; border-bottom-color: #5A8670; }
        .stat-angka { font-size: 32px; font-weight: 700; line-height: 1.2; }
        .produk-card { border: 1px solid #eee; background: #fff; height: 100%; display: flex; flex-direction: column; }
        .produk-foto { position: relative; width: 100%; padding-top: 100%; background: #f4f4f6; overflow: hidden; }
        .produk-foto img { position: absolute; top: 0; left: 0; width: 100%; height: 100%; object-fit: cover; }
        .produk-foto .kosong { position: absolute; top: 0; left: 0; width: 100%; height: 100%; display: flex; align-items: center; justify-content: center; color: #c4c4cc; font-size: 44px; }
        .produk-isi { padding: 16px; flex: 1; display: flex; flex-direction: column; }
        .produk-isi h5 { font-size: 16px; margin-bottom: 4px; }
        .produk-harga { font-weight: 700; color: #3F6853; }
        .chip { display: inline-block; padding: 4px 14px; border: 1px solid #ddd; border-radius: 20px; font-size: 12px; font-weight: 700; margin: 0 6px 8px 0; color: #222; }
        .chip:hover { text-decoration: none; border-color: #5A8670; color: #3F6853; }
        .chip.aktif { background: #5A8670; border-color: #5A8670; color: #fff; }
        .pilihan-card { display: block; border: 1px solid #eee; padding: 40px 30px; height: 100%; color: #222; text-align: center; transition: border-color .2s, box-shadow .2s; }
        .pilihan-card:hover { border-color: #5A8670; box-shadow: 0 6px 20px rgba(90,134,112,.15); text-decoration: none; color: #222; }
        .pilihan-card .lnr { font-size: 40px; color: #5A8670; }
        .kelompok-judul { border-bottom: 2px solid #5A8670; padding-bottom: 8px; margin: 10px 0 24px; }
        .badge-stok { display: inline-block; padding: 2px 10px; border-radius: 20px; font-size: 11px; font-weight: 700; background: #fdecec; color: #b42323; }
        .pesanan-card { border: 1px solid #eee; padding: 30px; }
        .pesanan-table th { font-size: 12px; text-transform: uppercase; letter-spacing: .5px; color: #777; }
        .pesanan-detail dt { color: #777; font-weight: 400; }
        .pesanan-detail dd { font-weight: 600; }
        .badge-status { display: inline-block; padding: 3px 12px; border-radius: 20px; font-size: 12px; font-weight: 700; background: #E6EEDF; color: #3F6853; }
        .badge-status.diproses { background: #e3ecf8; color: #234a85; }
        .badge-status.selesai { background: #e8f7ee; color: #17703a; }
        .badge-status.dibatalkan { background: #fdecec; color: #b42323; }
    </style>
    @include('partials.head-tema')
    @stack('styles')
</head>
@php($areaDashboard = request()->routeIs('admin.*') || (Auth::guard('pelanggan')->check() && request()->routeIs('pelanggan.*', 'katalog.*')))
<body class="hb-home {{ $areaDashboard ? 'hb-dash' : '' }}">

    <!-- Header sama seperti halaman utama -->
    <header class="default-header">
        <div class="container">
            <div class="header-wrap">
                <div class="header-top d-flex justify-content-between align-items-center">
                    <div class="logo">
                        <a href="{{ url('/') }}"><img src="{{ asset('img/logo.png') }}" alt="Hawa Butik"></a>
                    </div>
                    <div class="main-menubar d-flex align-items-center">
                        <nav class="hide">
                            @include('partials.menu-beranda')
                        </nav>
                        <div class="menu-bar"><span class="lnr lnr-menu"></span></div>
                    </div>
                </div>
            </div>
        </div>
    </header>

    @if ($areaDashboard)
        {{-- Area admin & pelanggan: sidebar kiri + isi halaman, sama dengan layout dashboard --}}
        <main class="dash-page">
            <div class="container">
                <div class="dash-shell">
                    @include('partials.sidebar-dashboard')
                    <section class="dash-main page-pesanan">
                        @if (session('success'))
                            <div class="alert alert-success">{{ session('success') }}</div>
                        @endif
                        @if (session('error'))
                            <div class="alert alert-danger">{{ session('error') }}</div>
                        @endif

                        @yield('isi')
                    </section>
                </div>
            </div>
        </main>
    @else
        <section class="page-pesanan">
            <div class="container">
                @if (session('success'))
                    <div class="alert alert-success">{{ session('success') }}</div>
                @endif
                @if (session('error'))
                    <div class="alert alert-danger">{{ session('error') }}</div>
                @endif

                @yield('isi')
            </div>
        </section>
    @endif

    @include('partials.footer-situs')

    <script src="{{ asset('js/vendor/jquery-2.2.4.min.js') }}"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.11.0/umd/popper.min.js" integrity="sha384-b/U6ypiBEHpOf/4+1nzFpr53nxSS+GLCkfwBdFNTxtclqqenISfwAzpKaMNFNmj4" crossorigin="anonymous"></script>
    <script src="{{ asset('js/vendor/bootstrap.min.js') }}"></script>
    <script src="{{ asset('js/jquery.ajaxchimp.min.js') }}"></script>
    <script src="{{ asset('js/owl.carousel.min.js') }}"></script>
    <script src="{{ asset('js/jquery.nice-select.min.js') }}"></script>
    <script>jQuery.fn.sticky = function () { return this; }; /* header sudah fixed lewat CSS; plugin sticky membuat menu bergeser */</script>
    <script src="{{ asset('js/parallax.min.js') }}"></script>
    <script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
    <script src="{{ asset('js/simple-lightbox.min.js') }}"></script>
    <script src="{{ asset('js/main.js') }}"></script>
    <script>
        // main.js mengubah semua <select> menjadi nice-select; dikembalikan ke select biasa
        // agar validasi "required" dan pilihan "Lainnya" berjalan normal.
        $(document).ready(function () {
            $('select').niceSelect('destroy');
        });
    </script>
    <script src="{{ asset('js/hawa-select.js') }}?v={{ @filemtime(public_path('js/hawa-select.js')) }}"></script>
    @stack('scripts')
</body>
</html>
