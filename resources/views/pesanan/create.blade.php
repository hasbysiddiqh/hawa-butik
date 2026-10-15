@extends('pesanan.layout')

@section('judul', 'Buat Pesanan')

@php
    $pilihan = [
        'jenis_pakaian' => 'Jenis pakaian',
        'model_lengan'  => 'Model lengan',
        'ukuran'        => 'Ukuran',
        'bahan'         => 'Bahan',
    ];
@endphp

@section('isi')
    <div class="row justify-content-center">
        <div class="col-lg-7 col-md-10">
            <div class="text-center">
                <h1>Buat Pesanan</h1>
                <p class="lead-text">Pilih dari pilihan yang tersedia. Jika tidak ada yang cocok, pilih "Lainnya" lalu ketik sendiri.</p>
            </div>

            @if ($errors->any())
                <div class="alert alert-danger">
                    <ul class="mb-0 pl-3">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form class="pesanan-form pesanan-card" action="{{ route('pesanan.store') }}" method="POST">
                @csrf

                <div class="row">
                    <div class="col-md-6">
                        <label for="nama">Nama</label>
                        <input id="nama" name="nama" type="text" class="form-control" placeholder="Nama lengkap"
                               value="{{ old('nama') }}" maxlength="100" required>
                    </div>
                    <div class="col-md-6">
                        <label for="whatsapp">Nomor WhatsApp</label>
                        <input id="whatsapp" name="whatsapp" type="text" class="form-control" placeholder="08xxxxxxxxxx"
                               value="{{ old('whatsapp') }}" required>
                    </div>
                </div>

                <div class="row">
                    @foreach ($pilihan as $kolom => $label)
                        <div class="col-md-6">
                            <label for="{{ $kolom }}">{{ $label }}</label>
                            <select id="{{ $kolom }}" name="{{ $kolom }}" class="form-control pilih"
                                    data-target="{{ $kolom }}_lain" required>
                                <option value="">-- Pilih {{ strtolower($label) }} --</option>
                                @foreach ($opsi[$kolom] as $item)
                                    <option value="{{ $item }}" @selected(old($kolom) === $item)>{{ $item }}</option>
                                @endforeach
                                <option value="lainnya" @selected(old($kolom) === 'lainnya')>Lainnya (ketik sendiri)</option>
                            </select>
                            <div class="lain" id="{{ $kolom }}_lain_wrap">
                                <input id="{{ $kolom }}_lain" name="{{ $kolom }}_lain" type="text" class="form-control"
                                       placeholder="Ketik {{ strtolower($label) }} yang diinginkan"
                                       value="{{ old($kolom . '_lain') }}" maxlength="100">
                            </div>
                        </div>
                    @endforeach

                    <div class="col-md-6">
                        <label for="warna">Warna</label>
                        <input id="warna" name="warna" type="text" class="form-control" placeholder="mis. Cokelat susu"
                               value="{{ old('warna') }}" maxlength="50" required>
                    </div>
                    <div class="col-md-6">
                        <label for="jumlah">Jumlah (potong)</label>
                        <input id="jumlah" name="jumlah" type="number" class="form-control" min="1" max="1000"
                               value="{{ old('jumlah', 1) }}" required>
                    </div>
                </div>

                <label for="detail_pesanan">Catatan tambahan (opsional)</label>
                <textarea id="detail_pesanan" name="detail_pesanan" class="form-control"
                          placeholder="Mis. ukuran badan, referensi model, tanggal dibutuhkan">{{ old('detail_pesanan') }}</textarea>

                <div class="text-center mt-30">
                    <button type="submit" class="submit-btn primary-btn text-uppercase">KONFIRMASI PEMESANAN<span class="lnr lnr-arrow-right"></span></button>
                </div>
            </form>

            <div class="text-center mt-30"><a href="{{ route('pesanan.index') }}" class="submit-btn primary-btn text-uppercase">LIHAT DAFTAR PESANAN<span class="lnr lnr-arrow-right"></span></a></div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        function aturKolomLain(select) {
            var input = document.getElementById(select.dataset.target);
            var wrap = document.getElementById(select.dataset.target + '_wrap');
            var lain = select.value === 'lainnya';
            wrap.classList.toggle('tampil', lain);
            input.required = lain;
            if (!lain) { input.value = ''; }
        }
        document.querySelectorAll('.pilih').forEach(function (select) {
            aturKolomLain(select);
            select.addEventListener('change', function () { aturKolomLain(select); });
        });
    </script>
@endpush
