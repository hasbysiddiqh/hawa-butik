<!DOCTYPE html>
<html lang="id" class="no-js">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('judul') - Hawa Butik</title>
    <link rel="stylesheet" href="{{ asset('css/linearicons.css') }}">
    <link rel="stylesheet" href="{{ asset('css/bootstrap.css') }}">
    <link rel="stylesheet" href="{{ asset('css/main.css') }}">
    @include('partials.head-tema')
    <link rel="stylesheet" href="{{ asset('css/hawa-auth.css') }}?v={{ @filemtime(public_path('css/hawa-auth.css')) }}">
</head>
<body class="hb-home auth3-body">
    <div class="auth3-bg" aria-hidden="true"></div>

    <main class="auth3">
        <div class="auth3-top">
            <a href="{{ route('home') }}" class="auth3-back"><i class="arr arr-left"></i>Kembali ke beranda</a>
        </div>

        <div class="auth3-center">
            <div class="auth3-card @yield('lebar', 'auth3-sempit')">
                <header class="auth3-head" style="--i:0">
                    <a href="{{ route('home') }}" class="auth3-logo"><img src="{{ asset('img/logo.png') }}" alt="Hawa Butik"></a>
                    <span class="eyebrow">@yield('eyebrow')</span>
                    <h1>@yield('judul')</h1>
                    <p>@yield('subjudul')</p>
                </header>

                <div class="auth3-body-isi">
                    @include('partials.validation-errors')
                    @yield('isi')
                </div>
            </div>

            <ul class="auth3-trust">
                <li><span class="lnr lnr-tag"></span>Rental Busana</li>
                <li><span class="lnr lnr-pencil"></span>Jahit Custom</li>
                <li><span class="lnr lnr-checkmark-circle"></span>Pesanan Terpantau</li>
            </ul>
            <p class="auth3-salin">&copy; {{ date('Y') }} Hawa Butik</p>
        </div>
    </main>

    <script>
        /* Tampilkan / sembunyikan kata sandi */
        document.querySelectorAll('.pw-toggle').forEach(function (b) {
            b.addEventListener('click', function () {
                var i = document.getElementById(b.dataset.target);
                var tampil = i.type === 'password';
                i.type = tampil ? 'text' : 'password';
                b.classList.toggle('aktif', tampil);
                b.setAttribute('aria-label', tampil ? 'Sembunyikan kata sandi' : 'Tampilkan kata sandi');
            });
        });
        /* Pilihan peran: penanda geser mengikuti pilihan */
        document.querySelectorAll('.peran-switch').forEach(function (sw) {
            function atur() { sw.dataset.pos = sw.querySelector('input[value="admin"]').checked ? '1' : '0'; }
            sw.addEventListener('change', atur); atur();
        });
    </script>
</body>
</html>
