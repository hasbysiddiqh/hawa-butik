@extends('layouts.app')

@section('judul', 'Buat Pesanan')

@section('isi')
    <div class="row justify-content-center">
        <div class="col-lg-7 col-md-10">
            <a href="{{ route('pelanggan.pesanan.index') }}" class="btn-back"><span class="lnr lnr-arrow-left"></span>KEMBALI</a>

            <div class="text-center">
                <h1>Buat Pesanan</h1>
                <p class="lead-text">Pilih dari pilihan yang tersedia. Jika tidak ada yang cocok, pilih "Lainnya" lalu ketik sendiri.</p>
            </div>

            @include('pelanggan.pesanan._form', [
                'action' => route('pelanggan.pesanan.store'),
                'metode' => 'POST',
                'tombol' => 'KONFIRMASI PEMESANAN',
            ])
        </div>
    </div>
@endsection
