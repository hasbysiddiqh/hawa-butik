@extends('layouts.app')

@section('judul', 'Pesanan #' . $pesanan->id)

@section('isi')
    <div class="row justify-content-center">
        <div class="col-lg-7 col-md-10">
            <a href="{{ route('admin.pesanan.index') }}" class="btn-back"><span class="lnr lnr-arrow-left"></span>KEMBALI KE DAFTAR</a>

            <div class="text-center">
                <h1>Pesanan #{{ $pesanan->id }}</h1>
                <p class="lead-text">Dikirim {{ $pesanan->tanggalDibuat() }}</p>
            </div>

            <div class="pesanan-card">
                <dl class="row pesanan-detail mb-0">
                    <dt class="col-sm-4">Status</dt><dd class="col-sm-8">@include('partials.status-pesanan')</dd>
                    <dt class="col-sm-4">Nama</dt><dd class="col-sm-8">{{ $pesanan->nama }}</dd>
                    <dt class="col-sm-4">WhatsApp</dt><dd class="col-sm-8">{{ $pesanan->whatsapp }}</dd>
                    <dt class="col-sm-4">Akun pelanggan</dt>
                    <dd class="col-sm-8">{{ $pesanan->pelanggan ? $pesanan->pelanggan->nama_pelanggan.' ('.$pesanan->pelanggan->email.')' : 'Tanpa akun (pesanan lama)' }}</dd>
                    <dt class="col-sm-4">Alamat pengiriman</dt><dd class="col-sm-8">{{ $pesanan->alamat_pengiriman ?: '-' }}</dd>
                    <dt class="col-sm-4">Jenis pemesanan</dt><dd class="col-sm-8">{{ $pesanan->labelTipe() }}</dd>
                    <dt class="col-sm-4">Jenis pakaian</dt><dd class="col-sm-8">{{ $pesanan->jenis_pakaian ?? '-' }}</dd>
                    @if ($pesanan->isRental())
                        <dt class="col-sm-4">Busana</dt><dd class="col-sm-8">{{ $pesanan->produk?->nama_produk ?? '(produk sudah dihapus)' }}</dd>
                        <dt class="col-sm-4">Ukuran</dt><dd class="col-sm-8">{{ $pesanan->ukuran ?? '-' }}</dd>
                        <dt class="col-sm-4">Tanggal sewa</dt><dd class="col-sm-8">{{ $pesanan->tanggal_sewa?->format('d M Y') }}</dd>
                        <dt class="col-sm-4">Tanggal kembali</dt><dd class="col-sm-8">{{ $pesanan->tanggal_kembali?->format('d M Y') }}</dd>
                        @if ($pesanan->penyewaan)
                            <dt class="col-sm-4">Status sewa</dt><dd class="col-sm-8">{{ ucfirst($pesanan->penyewaan->status_sewa) }}</dd>
                            <dt class="col-sm-4">Tanggal selesai sewa</dt><dd class="col-sm-8">{{ $pesanan->penyewaan->tgl_selesai?->format('d M Y') ?? '-' }}</dd>
                            <dt class="col-sm-4">Denda</dt><dd class="col-sm-8">Rp {{ number_format((int) $pesanan->penyewaan->denda, 0, ',', '.') }}</dd>
                        @endif
                    @else
                        <dt class="col-sm-4">Model lengan</dt><dd class="col-sm-8">{{ $pesanan->model_lengan ?? '-' }}</dd>
                        <dt class="col-sm-4">Ukuran</dt><dd class="col-sm-8">{{ $pesanan->ukuran ?? '-' }}</dd>
                        <dt class="col-sm-4">Bahan</dt><dd class="col-sm-8">{{ $pesanan->bahan ?: '-' }}</dd>
                        <dt class="col-sm-4">Warna</dt><dd class="col-sm-8">{{ $pesanan->warna ?: '-' }}</dd>
                    @endif
                    <dt class="col-sm-4">Jumlah</dt><dd class="col-sm-8">{{ $pesanan->jumlah }} potong</dd>
                    <dt class="col-sm-4">Total</dt>
                    <dd class="col-sm-8">{{ $pesanan->isRental() ? ((int) $pesanan->total > 0 ? 'Rp '.number_format((int) $pesanan->total, 0, ',', '.') : 'Menunggu konfirmasi harga') : 'Dikonfirmasi setelah estimasi jahit' }}</dd>
                    @if ($pesanan->pembayaran)
                        <dt class="col-sm-4">Status pembayaran</dt><dd class="col-sm-8">{{ ucfirst(str_replace('_', ' ', $pesanan->pembayaran->status_bayar)) }}</dd>
                        <dt class="col-sm-4">Metode pembayaran</dt><dd class="col-sm-8">{{ $pesanan->pembayaran->metode ?: 'Belum dipilih' }}</dd>
                    @endif
                    <dt class="col-sm-4">Catatan</dt><dd class="col-sm-8">{!! nl2br(e($pesanan->detail_pesanan ?: '-')) !!}</dd>
                </dl>
            </div>

            @if ($pesanan->adminVerifikasi)
                <p class="text-muted mt-2">Status terakhir diperbarui oleh admin: {{ $pesanan->adminVerifikasi->nama_admin }}</p>
            @endif

            <div class="pesanan-card pesanan-form mt-30">
                <h4>Ganti status</h4>
                @php($berikutnya = $pesanan->statusBerikutnya())
                @if ($berikutnya === [])
                    <p class="text-muted mb-0">Status "{{ $pesanan->labelStatus() }}" sudah final dan tidak dapat diganti lagi.</p>
                @else
                    <form action="{{ route('admin.pesanan.status', $pesanan) }}" method="POST" class="form-inline">
                        @csrf
                        @method('PATCH')
                        <select name="status" class="form-control mr-2 mt-2" aria-label="Status baru" required>
                            @foreach ($berikutnya as $nilai)
                                <option value="{{ $nilai }}">{{ App\Models\Pesanan::LABEL_STATUS[$nilai] }}</option>
                            @endforeach
                        </select>
                        <button type="submit" class="submit-btn primary-btn btn-kecil mt-2">SIMPAN STATUS</button>
                    </form>
                    <small class="text-muted d-block mt-2">Setelah "Dikerjakan", pelanggan tidak bisa lagi mengubah atau membatalkan pesanan ini.</small>
                @endif
            </div>
        </div>
    </div>
@endsection
