@extends('layouts.app')

@section('judul', 'Detail Pesanan #' . $pesanan->id)

@section('isi')
    <div class="row justify-content-center">
        <div class="col-lg-7 col-md-10">
            <a href="{{ route('pelanggan.pesanan.index') }}" class="btn-back"><span class="lnr lnr-arrow-left"></span>KEMBALI KE DAFTAR</a>

            <div class="text-center">
                <h1>Detail Pesanan #{{ $pesanan->id }}</h1>
                <p class="lead-text">Dikirim {{ $pesanan->tanggalDibuat() }}</p>
            </div>

            <div class="pesanan-card">
                <dl class="row pesanan-detail mb-0">
                    <dt class="col-sm-4">Status</dt><dd class="col-sm-8">@include('partials.status-pesanan')</dd>
                    <dt class="col-sm-4">Nama</dt><dd class="col-sm-8">{{ $pesanan->nama }}</dd>
                    <dt class="col-sm-4">WhatsApp</dt><dd class="col-sm-8">{{ $pesanan->whatsapp }}</dd>
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

            @if ($pesanan->bisaDiubah())
                <div class="d-flex justify-content-center flex-wrap mt-30">
                    @if ($pesanan->bisaDiedit())
                        <a href="{{ route('pelanggan.pesanan.edit', $pesanan->id) }}" class="submit-btn primary-btn text-uppercase m-1">UBAH PESANAN<span class="lnr lnr-arrow-right"></span></a>
                    @endif
                    <form action="{{ route('pelanggan.pesanan.batal', $pesanan->id) }}" method="POST" class="m-1"
                          onsubmit="return confirm('Batalkan pesanan ini? Pesanan yang sudah dibatalkan tidak dapat diaktifkan lagi.')">
                        @csrf
                        @method('PATCH')
                        <button type="submit" class="submit-btn primary-btn text-uppercase">BATALKAN PESANAN<span class="lnr lnr-arrow-right"></span></button>
                    </form>
                </div>
            @elseif ($pesanan->status === 'dibatalkan')
                <p class="text-center text-muted mt-30 mb-0">Pesanan ini sudah dibatalkan.</p>
            @else
                <p class="text-center text-muted mt-30 mb-0">Pesanan sudah {{ $pesanan->status === 'selesai' ? 'selesai' : 'dikerjakan' }}, sehingga tidak dapat diubah atau dibatalkan.</p>
            @endif
        </div>
    </div>
@endsection
