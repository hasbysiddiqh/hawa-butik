@extends('pesanan.layout')

@section('judul', 'Daftar')

@push('styles')
    <style>
        .auth-gambar { background: url('{{ asset('img/contact-img.jpg') }}') center / cover no-repeat; min-height: 480px; }
        .auth-card { border: 1px solid #eee; background: #fff; }
        .auth-card .isi { padding: 40px 36px; }
        .auth-card .tautan { font-size: 14px; color: #777; }
        .auth-card .tautan a { color: #a67c52; font-weight: 600; }
        .auth-card .badge-peran { display: inline-block; padding: 3px 12px; border-radius: 20px; font-size: 12px; font-weight: 700; background: #eef0ff; color: #4b3fc4; margin-bottom: 14px; }
        .auth-card .bantuan { font-size: 12px; color: #999; margin-top: 4px; }
    </style>
@endpush

@section('isi')
    <div class="row justify-content-center">
        <div class="col-lg-10 col-md-10">
            <div class="row no-gutters auth-card">
                <div class="col-lg-4 d-none d-lg-block auth-gambar" role="img" aria-label="Koleksi Hawa Butik"></div>

                <div class="col-lg-8">
                    <div class="isi">
                        <span class="badge-peran">Pelanggan</span>
                        <h1>Daftar Akun</h1>
                        <p class="lead-text">Buat akun untuk memesan dan memantau pesanan busana Anda di Hawa Butik.</p>

                        @if ($errors->any())
                            <div class="alert alert-danger">
                                <ul class="mb-0 pl-3">
                                    @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif

                        <form class="pesanan-form" action="{{ route('register.proses') }}" method="POST" novalidate>
                            @csrf

                            <div class="row">
                                <div class="col-md-6">
                                    <label for="nama_pelanggan">Nama lengkap</label>
                                    <input id="nama_pelanggan" name="nama_pelanggan" type="text" class="form-control"
                                           placeholder="Nama lengkap" value="{{ old('nama_pelanggan') }}" maxlength="100"
                                           autocomplete="name" autofocus required>
                                </div>
                                <div class="col-md-6">
                                    <label for="no_telp">Nomor telepon / WhatsApp</label>
                                    <input id="no_telp" name="no_telp" type="text" class="form-control"
                                           placeholder="08xxxxxxxxxx" value="{{ old('no_telp') }}"
                                           autocomplete="tel" required>
                                </div>
                            </div>

                            <label for="email">Email</label>
                            <input id="email" name="email" type="email" class="form-control" placeholder="nama@email.com"
                                   value="{{ old('email') }}" maxlength="150" autocomplete="email" required>

                            <label for="alamat">Alamat</label>
                            <textarea id="alamat" name="alamat" class="form-control" style="height: 90px"
                                      placeholder="Alamat lengkap" maxlength="500" autocomplete="street-address" required>{{ old('alamat') }}</textarea>

                            <div class="row">
                                <div class="col-md-6">
                                    <label for="password">Kata sandi</label>
                                    <input id="password" name="password" type="password" class="form-control"
                                           placeholder="Minimal 8 karakter" autocomplete="new-password" required>
                                </div>
                                <div class="col-md-6">
                                    <label for="password_confirmation">Ulangi kata sandi</label>
                                    <input id="password_confirmation" name="password_confirmation" type="password" class="form-control"
                                           placeholder="Ketik ulang kata sandi" autocomplete="new-password" required>
                                </div>
                            </div>

                            <div class="mt-30">
                                <button type="submit" class="primary-btn btn-kecil border-0 text-uppercase">
                                    Daftar<i class="arr"></i>
                                </button>
                            </div>
                        </form>

                        <div class="tautan mt-30">
                            Sudah punya akun? <a href="{{ route('login') }}">Masuk</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
