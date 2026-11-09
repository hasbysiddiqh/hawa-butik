@extends('layouts.app')

@section('judul', 'Transaksi #' . $transaksi->id_bayar)

@section('isi')
    @php($pesanan = $transaksi->pesanan)
    <a href="{{ route('admin.transaksi.index') }}" class="btn-back"><i class="arr arr-left"></i>KEMBALI KE DAFTAR</a>

    <div class="d-flex justify-content-between align-items-end flex-wrap mb-20">
        <div>
            <h1>Transaksi #{{ $transaksi->id_bayar }}</h1>
            <p class="lead-text mb-0">Pesanan #{{ $pesanan->id }} &middot; {{ $pesanan->labelTipe() }}</p>
        </div>
        <div><span class="st {{ $transaksi->kelasStatus() }}">{{ $transaksi->labelStatus() }}</span></div>
    </div>

    @include('partials.validation-errors')

    <div class="kelola-grid">
        {{-- Kiri: rincian transaksi --}}
        <div class="pesanan-card">
            <h4 class="kelola-judul"><span class="lnr lnr-list"></span>Rincian transaksi</h4>
            <dl class="row pesanan-detail mb-0">
                <dt class="col-sm-4">Pelanggan</dt><dd class="col-sm-8">{{ $pesanan->pelanggan?->nama_pelanggan ?? $pesanan->nama }}</dd>
                <dt class="col-sm-4">WhatsApp</dt><dd class="col-sm-8">{{ $pesanan->whatsapp }}</dd>
                <dt class="col-sm-4">Pesanan</dt>
                <dd class="col-sm-8"><a href="{{ route('admin.pesanan.show', $pesanan) }}">#{{ $pesanan->id }}</a> &middot; @include('partials.status-pesanan')</dd>
                @forelse ($pesanan->detailTransaksis as $detail)
                    <dt class="col-sm-4">{{ $loop->first ? 'Item' : '' }}</dt>
                    <dd class="col-sm-8">
                        {{ $detail->produk?->nama_produk ?? '(produk sudah dihapus)' }}
                        <small class="d-block text-muted">{{ $detail->jumlah }} &times; Rp {{ number_format((int) $detail->harga_satuan, 0, ',', '.') }} = Rp {{ number_format((int) $detail->subtotal, 0, ',', '.') }}</small>
                    </dd>
                @empty
                    <dt class="col-sm-4">Item</dt>
                    <dd class="col-sm-8">{{ $pesanan->jenis_pakaian ?? '-' }} &times; {{ $pesanan->jumlah }}<small class="d-block text-muted">Jahit custom</small></dd>
                @endforelse
                <dt class="col-sm-4">Total tagihan</dt>
                <dd class="col-sm-8">{{ $transaksi->totalTagihan() > 0 ? 'Rp '.number_format($transaksi->totalTagihan(), 0, ',', '.') : 'Belum ditetapkan' }}</dd>
                <dt class="col-sm-4">Sudah dibayar</dt><dd class="col-sm-8">Rp {{ number_format((int) $transaksi->jumlah_bayar, 0, ',', '.') }}</dd>
                <dt class="col-sm-4">Sisa</dt><dd class="col-sm-8">Rp {{ number_format($transaksi->sisaBayar(), 0, ',', '.') }}</dd>
                <dt class="col-sm-4">Metode</dt><dd class="col-sm-8">{{ $transaksi->labelMetode() }}</dd>
                <dt class="col-sm-4">Tanggal bayar</dt><dd class="col-sm-8">{{ $transaksi->tgl_bayar?->format('d M Y, H:i') ?? '-' }}</dd>
            </dl>
        </div>

        {{-- Kanan: perbarui transaksi --}}
        <div class="pesanan-card pesanan-form">
            <h4 class="kelola-judul"><span class="lnr lnr-pencil"></span>Perbarui transaksi</h4>
            <form action="{{ route('admin.transaksi.update', $transaksi) }}" method="POST" class="kelola-form">
                @csrf
                @method('PATCH')

                @unless ($pesanan->isRental())
                    <label for="total">Total pesanan (Rp)</label>
                    <input id="total" name="total" type="number" min="0" class="form-control" value="{{ old('total', (int) $pesanan->total) }}">
                    <small class="text-muted">Hasil estimasi jahit; isi sebelum menandai dicicil atau lunas.</small>
                @endunless

                <label for="status_bayar">Status transaksi</label>
                <select id="status_bayar" name="status_bayar" class="form-control" required>
                    @foreach (App\Models\Pembayaran::LABEL_STATUS as $nilai => $label)
                        <option value="{{ $nilai }}" @selected(old('status_bayar', $transaksi->status_bayar) === $nilai)>{{ $label }}</option>
                    @endforeach
                </select>

                <label for="metode">Metode pembayaran</label>
                <select id="metode" name="metode" class="form-control">
                    <option value="">Belum dipilih</option>
                    @foreach (App\Models\Pembayaran::LABEL_METODE as $nilai => $label)
                        <option value="{{ $nilai }}" @selected(old('metode', $transaksi->metode) === $nilai)>{{ $label }}</option>
                    @endforeach
                </select>

                <label for="jumlah_bayar">Jumlah dibayar (Rp)</label>
                <input id="jumlah_bayar" name="jumlah_bayar" type="number" min="0" class="form-control" value="{{ old('jumlah_bayar', (int) $transaksi->jumlah_bayar) }}">
                <small class="text-muted">Hanya dipakai untuk status "Dicicil".</small>

                <button type="submit" class="submit-btn primary-btn btn-kecil">SIMPAN TRANSAKSI</button>
            </form>
            <small class="text-muted d-block kelola-catatan">"Lunas" mengisi jumlah dibayar sebesar total. "Menunggu" dan "Belum dibayar" mengosongkan jumlah dibayar.</small>
        </div>
    </div>
@endsection
