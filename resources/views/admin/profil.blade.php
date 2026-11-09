@extends('layouts.app')

@section('judul', 'Profil Admin')

@section('isi')
    <h1>Profil Admin</h1>
    <p class="lead-text">Akun yang sedang masuk.</p>

    <div class="pesanan-card">
        <dl class="row pesanan-detail mb-0">
            <dt class="col-sm-3">Nama</dt><dd class="col-sm-9">{{ $admin->nama_admin }}</dd>
            <dt class="col-sm-3">Email</dt><dd class="col-sm-9">{{ $admin->email }}</dd>
            <dt class="col-sm-3">Telepon</dt><dd class="col-sm-9 mb-0">{{ $admin->no_telp ?: '-' }}</dd>
        </dl>
    </div>
@endsection
