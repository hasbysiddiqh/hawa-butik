@extends('layouts.app')

@section('judul', 'Registrasi Akun')

@section('isi')
    <div class="row justify-content-center">
        <div class="col-lg-7 col-md-10">
            <a href="{{ route('login') }}" class="btn-back"><span class="lnr lnr-arrow-left"></span>KEMBALI KE LOGIN</a>

            <div class="text-center">
                <h1>Registrasi Akun</h1>
                <p class="lead-text">Buat akun pelanggan untuk memesan busana di Hawa Butik.</p>
            </div>

            @include('partials.validation-errors')

            <form class="pesanan-form pesanan-card" action="{{ route('register.store') }}" method="POST">
                @csrf

                <div class="row">
                    <div class="col-md-6">
                        <label for="nama_pelanggan">Nama</label>
                        <input id="nama_pelanggan" name="nama_pelanggan" type="text" class="form-control" placeholder="Nama lengkap"
                               value="{{ old('nama_pelanggan') }}" maxlength="100" autocomplete="name" required autofocus>
                    </div>
                    <div class="col-md-6">
                        <label for="no_telp">Nomor telepon / WhatsApp</label>
                        <input id="no_telp" name="no_telp" type="text" class="form-control" placeholder="08xxxxxxxxxx"
                               value="{{ old('no_telp') }}" autocomplete="tel" required>
                    </div>
                </div>

                <label for="email">Email</label>
                <input id="email" name="email" type="email" class="form-control" placeholder="nama@email.com"
                       value="{{ old('email') }}" autocomplete="email" required>

                <label for="alamat">Alamat <small class="text-muted text-lowercase">(boleh dikosongkan)</small></label>
                <textarea id="alamat" name="alamat" class="form-control" maxlength="500" placeholder="Alamat lengkap">{{ old('alamat') }}</textarea>

                <div class="row">
                    <div class="col-md-6">
                        <label for="password">Kata sandi</label>
                        <input id="password" name="password" type="password" class="form-control" placeholder="Minimal 8 karakter"
                               autocomplete="new-password" required>
                    </div>
                    <div class="col-md-6">
                        <label for="password_confirmation">Ulangi kata sandi</label>
                        <input id="password_confirmation" name="password_confirmation" type="password" class="form-control"
                               placeholder="Ketik ulang kata sandi" autocomplete="new-password" required>
                    </div>
                </div>

                <div class="text-center mt-30">
                    <button type="submit" class="submit-btn primary-btn text-uppercase">DAFTAR<span class="lnr lnr-arrow-right"></span></button>
                </div>
            </form>

            <p class="text-center mt-30 mb-0">Sudah punya akun? <a href="{{ route('login') }}">Masuk di sini</a></p>
        </div>
    </div>
@endsection
