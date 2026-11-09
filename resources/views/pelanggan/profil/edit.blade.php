@extends('layouts.app')

@section('judul', 'Profil Saya')

@section('isi')
    <h1>Profil Saya</h1>
    <p class="lead-text">Terdaftar sejak {{ $pelanggan->tanggal_daftar?->format('d M Y') }}.</p>

    @include('partials.validation-errors')

    <div class="profil-grid">

            <form class="pesanan-form pesanan-card" action="{{ route('pelanggan.profil.update') }}" method="POST">
                @csrf
                @method('PUT')
                <h4>Data diri</h4>

                <div class="row">
                    <div class="col-md-6">
                        <label for="nama_pelanggan">Nama</label>
                        <input id="nama_pelanggan" name="nama_pelanggan" type="text" class="form-control" maxlength="100"
                               value="{{ old('nama_pelanggan', $pelanggan->nama_pelanggan) }}" required>
                    </div>
                    <div class="col-md-6">
                        <label for="no_telp">Nomor telepon / WhatsApp</label>
                        <input id="no_telp" name="no_telp" type="text" class="form-control"
                               value="{{ old('no_telp', $pelanggan->no_telp) }}" required>
                    </div>
                </div>

                <label for="email">Email</label>
                <input id="email" name="email" type="email" class="form-control" value="{{ old('email', $pelanggan->email) }}" required>

                <label for="alamat">Alamat</label>
                <textarea id="alamat" name="alamat" class="form-control" maxlength="500">{{ old('alamat', $pelanggan->alamat) }}</textarea>

                <div class="form-aksi">
                    <button type="submit" class="submit-btn primary-btn text-uppercase">SIMPAN PROFIL<i class="arr"></i></button>
                </div>
            </form>

            <form class="pesanan-form pesanan-card" action="{{ route('pelanggan.profil.password') }}" method="POST">
                @csrf
                @method('PUT')
                <h4>Ganti kata sandi</h4>

                <label for="password_lama">Kata sandi lama</label>
                <input id="password_lama" name="password_lama" type="password" class="form-control" autocomplete="current-password" required>

                <div class="row">
                    <div class="col-12">
                        <label for="password">Kata sandi baru</label>
                        <input id="password" name="password" type="password" class="form-control" placeholder="Minimal 8 karakter" autocomplete="new-password" required>
                    </div>
                    <div class="col-12">
                        <label for="password_confirmation">Ulangi kata sandi baru</label>
                        <input id="password_confirmation" name="password_confirmation" type="password" class="form-control" autocomplete="new-password" required>
                    </div>
                </div>

                <div class="form-aksi">
                    <button type="submit" class="submit-btn primary-btn text-uppercase">GANTI KATA SANDI<i class="arr"></i></button>
                </div>
            </form>
    </div>
@endsection
