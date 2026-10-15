<!DOCTYPE html>
<html lang="id" class="no-js">
<head>
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta charset="UTF-8">
    <title>@yield('judul', 'Pesanan') - Hawa Butik</title>

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
        .pesanan-form textarea.form-control { height: 130px; }
        .pesanan-form .lain { display: none; margin-top: 10px; }
        .pesanan-form .lain.tampil { display: block; }
        .primary-btn.btn-kecil { line-height: 34px; padding-left: 22px; padding-right: 22px; font-size: 13px; font-weight: 600; text-transform: uppercase; }
        .pesanan-card { border: 1px solid #eee; padding: 30px; }
        .pesanan-table th { font-size: 12px; text-transform: uppercase; letter-spacing: .5px; color: #777; }
        .pesanan-detail dt { color: #777; font-weight: 400; }
        .pesanan-detail dd { font-weight: 600; }
        .badge-status { display: inline-block; padding: 3px 12px; border-radius: 20px; font-size: 12px; font-weight: 700; background: #eef0ff; color: #4b3fc4; }
        .badge-status.diproses { background: #e3ecf8; color: #234a85; }
        .badge-status.selesai { background: #e8f7ee; color: #17703a; }
        .badge-status.dibatalkan { background: #fdecec; color: #b42323; }
    </style>
</head>
<body>

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
                            <a href="{{ url('/') }}">Home</a>
                            <a href="{{ url('/#about') }}">About Me</a>
                            <a href="{{ url('/#gallery') }}">Collection</a>
                            <a href="{{ url('/#contact') }}">Contact</a>
                        </nav>
                        <div class="menu-bar"><span class="lnr lnr-menu"></span></div>
                    </div>
                </div>
            </div>
        </div>
    </header>

    <section class="page-pesanan">
        <div class="container">
            @yield('isi')
        </div>
    </section>

    <footer class="footer-area">
        <div class="container">
            <div class="row footer-bottom d-flex justify-content-between">
                <div class="col-lg-4 col-sm-12 footer-social">
                    <a href="https://www.instagram.com/hawa_butikk/" target="_blank" rel="noopener" aria-label="Instagram"><i class="fa fa-instagram"></i></a>
                    <a href="https://www.tiktok.com/@hawa_butikk" target="_blank" rel="noopener" aria-label="TikTok">
                        <svg viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg" aria-hidden="true"><path d="M12.525.02c1.31-.02 2.61-.01 3.91-.02.08 1.53.63 3.09 1.75 4.17 1.12 1.11 2.7 1.62 4.24 1.79v4.03c-1.44-.05-2.89-.35-4.2-.97-.57-.26-1.1-.59-1.62-.93-.01 2.92.01 5.84-.02 8.75-.08 1.4-.54 2.79-1.35 3.94-1.31 1.92-3.58 3.17-5.91 3.21-1.43.08-2.86-.31-4.08-1.03-2.02-1.19-3.44-3.37-3.65-5.71-.02-.5-.03-1-.01-1.49.18-1.9 1.12-3.72 2.58-4.96 1.66-1.44 3.98-2.13 6.15-1.72.02 1.48-.04 2.96-.04 4.44-.99-.32-2.15-.23-3.02.37-.63.41-1.11 1.04-1.36 1.75-.21.51-.15 1.07-.14 1.61.24 1.64 1.82 3.02 3.5 2.87 1.12-.01 2.19-.66 2.77-1.61.19-.33.4-.67.41-1.06.1-1.79.06-3.57.07-5.36.01-4.03-.01-8.05.02-12.07z"/></svg>
                    </a>
                </div>
            </div>
        </div>
    </footer>

    <script src="{{ asset('js/vendor/jquery-2.2.4.min.js') }}"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.11.0/umd/popper.min.js" integrity="sha384-b/U6ypiBEHpOf/4+1nzFpr53nxSS+GLCkfwBdFNTxtclqqenISfwAzpKaMNFNmj4" crossorigin="anonymous"></script>
    <script src="{{ asset('js/vendor/bootstrap.min.js') }}"></script>
    <script src="{{ asset('js/jquery.ajaxchimp.min.js') }}"></script>
    <script src="{{ asset('js/owl.carousel.min.js') }}"></script>
    <script src="{{ asset('js/jquery.nice-select.min.js') }}"></script>
    <script src="{{ asset('js/jquery.sticky.js') }}"></script>
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
    @stack('scripts')
</body>
</html>
