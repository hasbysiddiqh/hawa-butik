{{-- Form bersama untuk buat & ubah pesanan.
     Variabel: $action, $metode ('POST'|'PUT'), $tombol, $opsi, $pesanan (boleh null) --}}
@php
    $pilihan = [
        'jenis_pakaian' => 'Jenis pakaian',
        'model_lengan'  => 'Model lengan',
        'ukuran'        => 'Ukuran',
        'bahan'         => 'Bahan',
    ];
@endphp

@if ($errors->any())
    <div class="alert alert-danger">
        <ul class="mb-0 pl-3">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

<form class="pesanan-form pesanan-card" action="{{ $action }}" method="POST">
    @csrf
    @if ($metode !== 'POST')
        @method($metode)
    @endif

    <div class="row">
        <div class="col-md-6">
            <label for="nama">Nama</label>
            <input id="nama" name="nama" type="text" class="form-control" placeholder="Nama lengkap"
                   value="{{ old('nama', $pesanan->nama ?? '') }}" maxlength="100" required>
        </div>
        <div class="col-md-6">
            <label for="whatsapp">Nomor WhatsApp</label>
            <input id="whatsapp" name="whatsapp" type="text" class="form-control" placeholder="08xxxxxxxxxx"
                   value="{{ old('whatsapp', $pesanan->whatsapp ?? '') }}" required>
        </div>
    </div>

    <div class="row">
        @foreach ($pilihan as $kolom => $label)
            @php
                // Nilai yang dulu diketik sendiri (bukan preset) ditampilkan kembali sebagai "Lainnya".
                $nilai = old($kolom, $pesanan->{$kolom} ?? '');
                $teksLain = old($kolom . '_lain', '');
                if ($nilai !== '' && $nilai !== 'lainnya' && ! in_array($nilai, $opsi[$kolom], true)) {
                    $teksLain = $nilai;
                    $nilai = 'lainnya';
                }
            @endphp
            <div class="col-md-6">
                <label for="{{ $kolom }}">{{ $label }}</label>
                <select id="{{ $kolom }}" name="{{ $kolom }}" class="form-control pilih"
                        data-target="{{ $kolom }}_lain" required>
                    <option value="">-- Pilih {{ strtolower($label) }} --</option>
                    @foreach ($opsi[$kolom] as $item)
                        <option value="{{ $item }}" @selected($nilai === $item)>{{ $item }}</option>
                    @endforeach
                    <option value="lainnya" @selected($nilai === 'lainnya')>Lainnya (ketik sendiri)</option>
                </select>
                <div class="lain" id="{{ $kolom }}_lain_wrap">
                    <input id="{{ $kolom }}_lain" name="{{ $kolom }}_lain" type="text" class="form-control"
                           placeholder="Ketik {{ strtolower($label) }} yang diinginkan"
                           value="{{ $teksLain }}" maxlength="100">
                </div>
            </div>
        @endforeach

        <div class="col-md-6">
            <label for="warna">Warna</label>
            <input id="warna" name="warna" type="text" class="form-control" placeholder="mis. Cokelat susu"
                   value="{{ old('warna', $pesanan->warna ?? '') }}" maxlength="50" required>
        </div>
        <div class="col-md-6">
            <label for="jumlah">Jumlah (potong)</label>
            <input id="jumlah" name="jumlah" type="number" class="form-control" min="1" max="1000"
                   value="{{ old('jumlah', $pesanan->jumlah ?? 1) }}" required>
        </div>
    </div>

    <label for="detail_pesanan">Catatan tambahan (opsional)</label>
    <textarea id="detail_pesanan" name="detail_pesanan" class="form-control"
              placeholder="Mis. ukuran badan, referensi model, tanggal dibutuhkan">{{ old('detail_pesanan', $pesanan->detail_pesanan ?? '') }}</textarea>

    <div class="text-center mt-30">
        <button type="submit" class="submit-btn primary-btn text-uppercase">{{ $tombol }}<span class="lnr lnr-arrow-right"></span></button>
    </div>
</form>

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
