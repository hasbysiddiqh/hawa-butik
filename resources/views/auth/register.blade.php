@extends('layouts.auth')

@section('judul', 'Daftar Akun')
@section('eyebrow', 'Pelanggan Baru')
@section('subjudul', 'Buat akun untuk memesan dan memantau pesanan busana di Hawa Butik.')
@section('lebar', 'auth3-lebar')

@section('isi')
    <form action="{{ route('register.store') }}" method="POST" class="auth3-form" novalidate>
        @csrf

        <div class="auth3-grid">
            <div class="auth3-row" style="--i:1">
                <label for="nama_pelanggan">Nama</label>
                <div class="fld">
                    <span class="lnr lnr-user"></span>
                    <input id="nama_pelanggan" name="nama_pelanggan" type="text" class="form-control" placeholder="Nama lengkap"
                           value="{{ old('nama_pelanggan') }}" maxlength="100" autocomplete="name" required autofocus>
                </div>
            </div>
            <div class="auth3-row" style="--i:2">
                <label for="no_telp">Nomor telepon / WhatsApp</label>
                <div class="fld">
                    <span class="lnr lnr-phone-handset"></span>
                    <input id="no_telp" name="no_telp" type="text" class="form-control" placeholder="08xxxxxxxxxx"
                           value="{{ old('no_telp') }}" autocomplete="tel" required>
                </div>
            </div>
        </div>

        <div class="auth3-row" style="--i:3">
            <label for="email">Email</label>
            <div class="fld">
                <span class="lnr lnr-envelope"></span>
                <input id="email" name="email" type="email" class="form-control" placeholder="nama@email.com"
                       value="{{ old('email') }}" autocomplete="email" required>
            </div>
        </div>

        <div class="auth3-row" style="--i:4">
            <label for="alamat">Alamat <small>(boleh dikosongkan)</small></label>
            <div class="fld fld-area">
                <span class="lnr lnr-map-marker"></span>
                <textarea id="alamat" name="alamat" class="form-control" maxlength="500" placeholder="Alamat lengkap">{{ old('alamat') }}</textarea>
            </div>
        </div>

        <div class="auth3-grid">
            <div class="auth3-row" style="--i:5">
                <label for="password">Kata sandi</label>
                <div class="fld">
                    <span class="lnr lnr-lock"></span>
                    <input id="password" name="password" type="password" class="form-control" placeholder="Minimal 8 karakter"
                           autocomplete="new-password" required>
                    <button type="button" class="pw-toggle" data-target="password" aria-label="Tampilkan kata sandi"><svg class="eye" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M2 12s3.6-7 10-7 10 7 10 7-3.6 7-10 7S2 12 2 12z"/><circle cx="12" cy="12" r="3"/><line class="slash" x1="4" y1="4" x2="20" y2="20" pathLength="100"/></svg></button>
                </div>
            </div>
            <div class="auth3-row" style="--i:6">
                <label for="password_confirmation">Ulangi kata sandi</label>
                <div class="fld">
                    <span class="lnr lnr-lock"></span>
                    <input id="password_confirmation" name="password_confirmation" type="password" class="form-control" placeholder="Ketik ulang kata sandi"
                           autocomplete="new-password" required>
                    <button type="button" class="pw-toggle" data-target="password_confirmation" aria-label="Tampilkan kata sandi"><svg class="eye" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M2 12s3.6-7 10-7 10 7 10 7-3.6 7-10 7S2 12 2 12z"/><circle cx="12" cy="12" r="3"/><line class="slash" x1="4" y1="4" x2="20" y2="20" pathLength="100"/></svg></button>
                </div>
            </div>
        </div>

        <button type="submit" class="primary-btn auth3-submit" style="--i:7">Buat Akun<i class="arr"></i></button>
    </form>

    <div class="auth3-or" style="--i:8"><span>Sudah punya akun?</span></div>
    <a href="{{ route('login') }}" class="btn-garis" style="--i:9">Masuk di sini</a>
@endsection
