@extends('layouts.app')

@section('judul', 'Profil Admin')

@section('isi')
    <div class="row justify-content-center">
        <div class="col-lg-6 col-md-10">
            <div class="text-center">
                <h1>Profil Admin</h1>
                <p class="lead-text">Akun yang sedang masuk.</p>
            </div>

            <div class="pesanan-card">
                <dl class="row pesanan-detail mb-0">
                    <dt class="col-sm-4">Nama</dt><dd class="col-sm-8">{{ $admin->nama_admin }}</dd>
                    <dt class="col-sm-4">Email</dt><dd class="col-sm-8">{{ $admin->email }}</dd>
                    <dt class="col-sm-4">Telepon</dt><dd class="col-sm-8">{{ $admin->no_telp ?: '-' }}</dd>
                </dl>
            </div>

            <form action="{{ route('logout') }}" method="POST" class="text-center mt-30">
                @csrf
                <button type="submit" class="submit-btn primary-btn text-uppercase">KELUAR<span class="lnr lnr-arrow-right"></span></button>
            </form>
        </div>
    </div>
@endsection
