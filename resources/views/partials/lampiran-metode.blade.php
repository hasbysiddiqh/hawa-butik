{{-- Input lampiran catatan + pilihan metode pembayaran. Variabel opsional: $lampiranUrl, $metodeAwal --}}
<label for="lampiran">Lampiran (opsional)</label>
<input id="lampiran" name="lampiran" type="file" class="form-control" accept="image/*,.pdf">
<small class="text-muted d-block">Gambar atau PDF, maksimal 2 MB.@if (! empty($lampiranUrl)) Lampiran saat ini: <a href="{{ $lampiranUrl }}" target="_blank" rel="noopener">lihat</a>. Pilih file baru untuk menggantinya.@endif</small>

<h4 class="form-bagian"><span class="lnr lnr-tag"></span>Pembayaran</h4>
<label for="metode">Metode pembayaran</label>
<select id="metode" name="metode" class="form-control">
    <option value="">Belum dipilih</option>
    @foreach (App\Models\Pembayaran::LABEL_METODE as $nilai => $label)
        <option value="{{ $nilai }}" @selected(old('metode', $metodeAwal ?? '') === $nilai)>{{ $label }}</option>
    @endforeach
</select>
