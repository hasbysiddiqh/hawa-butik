@extends('layouts.app')

@section('judul', 'Masuk')

@push('styles')
    <style>
        .login-card { padding: 36px 36px 32px; background: #fff; box-shadow: 0 8px 30px rgba(34, 34, 34, .06); }
        .login-card .login-field { margin-top: 20px; }
        .login-card .login-field label { display: block; margin: 0 0 8px; }

        /* Pilihan peran: dua pil dalam satu baris sendiri */
        .peran-switch { display: flex; padding: 4px; border-radius: 28px; background: #f3f3f8; }
        .pesanan-form .peran-switch label { position: relative; flex: 1; margin: 0; cursor: pointer; }
        .peran-switch input { position: absolute; opacity: 0; pointer-events: none; }
        .peran-switch span { display: block; padding: 10px 0; border-radius: 24px; text-align: center; font-size: 13px; font-weight: 700; letter-spacing: .5px; text-transform: uppercase; color: #666; transition: all .25s ease; }
        .peran-switch input:checked + span { color: #fff; background-image: linear-gradient(90deg, #7a77ff 0%, #8cfe86 100%); box-shadow: 0 3px 10px rgba(122, 119, 255, .35); }
        .peran-switch input:focus-visible + span { outline: 2px solid #7a77ff; outline-offset: 2px; }

        .login-card .ingat-saya { display: flex; align-items: center; margin-top: 20px; }
        .login-card .ingat-saya input { width: 16px; height: 16px; margin: 0 10px 0 0; cursor: pointer; }
        .pesanan-form .ingat-saya label { margin: 0; font-size: 14px; font-weight: 600; text-transform: none; letter-spacing: 0; cursor: pointer; }

        .login-card .tombol-masuk { display: block; width: 100%; margin-top: 28px; padding: 0 60px; text-align: center; }
        .login-daftar { margin-top: 24px; text-align: center; }

        @media (max-width: 575px) {
            .login-card { padding: 28px 20px 24px; }
        }
    </style>
@endpush

@section('isi')
    <div class="row justify-content-center">
        <div class="col-xl-4 col-lg-5 col-md-7 col-sm-10">
            <a href="{{ route('home') }}" class="btn-back"><span class="lnr lnr-arrow-left"></span>KEMBALI KE BERANDA</a>

            <div class="text-center mb-30">
                <h1>Masuk</h1>
                <p class="lead-text mb-0">Pelanggan masuk untuk memesan busana. Admin masuk untuk mengelola pesanan.</p>
            </div>

            @include('partials.validation-errors')

            @php($peranTerpilih = old('peran') === 'admin' ? 'admin' : 'pelanggan')

            <form class="pesanan-form pesanan-card login-card" action="{{ route('login.store') }}" method="POST">
                @csrf

                <div>
                    <label id="label-peran" style="display:block; margin:0 0 8px">Masuk sebagai</label>
                    <div class="peran-switch" role="radiogroup" aria-labelledby="label-peran">
                        <label>
                            <input type="radio" name="peran" value="pelanggan" @checked($peranTerpilih === 'pelanggan')>
                            <span>Pelanggan</span>
                        </label>
                        <label>
                            <input type="radio" name="peran" value="admin" @checked($peranTerpilih === 'admin')>
                            <span>Admin</span>
                        </label>
                    </div>
                </div>

                <div class="login-field">
                    <label for="email">Email</label>
                    <input id="email" name="email" type="email" class="form-control" placeholder="nama@email.com"
                           value="{{ old('email') }}" autocomplete="email" required autofocus>
                </div>

                <div class="login-field">
                    <label for="password">Kata sandi</label>
                    <input id="password" name="password" type="password" class="form-control" placeholder="Kata sandi"
                           autocomplete="current-password" required>
                </div>

                <div class="ingat-saya">
                    <input type="checkbox" name="ingat_saya" id="ingat_saya" value="1" @checked(old('ingat_saya'))>
                    <label for="ingat_saya">Ingat saya</label>
                </div>

                <button type="submit" class="submit-btn primary-btn tombol-masuk text-uppercase">Masuk<span class="lnr lnr-arrow-right"></span></button>
            </form>

            <p class="login-daftar mb-0">Belum punya akun? <a href="{{ route('register') }}">Daftar sebagai pelanggan</a></p>
        </div>
    </div>
@endsection
