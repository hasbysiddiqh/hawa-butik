@extends('layouts.auth')

@section('judul', 'Masuk')
@section('eyebrow', 'Selamat Datang')
@section('subjudul', 'Pelanggan masuk untuk memesan busana. Admin masuk untuk mengelola pesanan.')

@section('isi')
    @php($peranTerpilih = old('peran') === 'admin' ? 'admin' : 'pelanggan')

    <form action="{{ route('login.store') }}" method="POST" class="auth3-form">
        @csrf

        <div class="auth3-row" style="--i:1">
            <label id="label-peran">Masuk sebagai</label>
            <div class="peran-switch" role="radiogroup" aria-labelledby="label-peran">
                <label><input type="radio" name="peran" value="pelanggan" @checked($peranTerpilih === 'pelanggan')><span>Pelanggan</span></label>
                <label><input type="radio" name="peran" value="admin" @checked($peranTerpilih === 'admin')><span>Admin</span></label>
            </div>
        </div>

        <div class="auth3-row" style="--i:2">
            <label for="email">Email</label>
            <div class="fld">
                <span class="lnr lnr-envelope"></span>
                <input id="email" name="email" type="email" class="form-control" placeholder="nama@email.com"
                       value="{{ old('email') }}" autocomplete="email" required autofocus>
            </div>
        </div>

        <div class="auth3-row" style="--i:3">
            <label for="password">Kata sandi</label>
            <div class="fld">
                <span class="lnr lnr-lock"></span>
                <input id="password" name="password" type="password" class="form-control" placeholder="Kata sandi"
                       autocomplete="current-password" required>
                <button type="button" class="pw-toggle" data-target="password" aria-label="Tampilkan kata sandi"><svg class="eye" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M2 12s3.6-7 10-7 10 7 10 7-3.6 7-10 7S2 12 2 12z"/><circle cx="12" cy="12" r="3"/><line class="slash" x1="4" y1="4" x2="20" y2="20" pathLength="100"/></svg></button>
            </div>
        </div>

        <div class="ingat-saya" style="--i:4">
            <input type="checkbox" name="ingat_saya" id="ingat_saya" value="1" @checked(old('ingat_saya'))>
            <label for="ingat_saya">Ingat saya</label>
        </div>

        <button type="submit" class="primary-btn auth3-submit" style="--i:5">Masuk<i class="arr"></i></button>
    </form>

    <div class="auth3-or" style="--i:6"><span>Belum punya akun?</span></div>
    <a href="{{ route('register') }}" class="btn-garis" style="--i:7">Daftar sebagai pelanggan</a>
@endsection
