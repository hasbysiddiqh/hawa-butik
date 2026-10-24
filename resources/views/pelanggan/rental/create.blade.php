@extends('layouts.app')

@section('judul', 'Rental')

@section('isi')
    <div class="row justify-content-center">
        <div class="col-lg-7 col-md-10">
            <a href="{{ route('pelanggan.pemesanan') }}" class="btn-back"><span class="lnr lnr-arrow-left"></span>KEMBALI</a>

            <div class="text-center">
                <h1>Rental</h1>
                <p class="lead-text">Pilih busana dari Collection, lalu tentukan tanggal sewa dan tanggal kembali.</p>
            </div>

            @include('partials.validation-errors')

            <form class="pesanan-form pesanan-card" action="{{ route('pelanggan.rental.store') }}" method="POST">
                @csrf

                <label for="id_produk">Busana</label>
                <select id="id_produk" name="id_produk" class="form-control" required>
                    <option value="">-- Pilih busana --</option>
                    @foreach ($produks->groupBy(fn ($p) => $p->kategori->nama_kategori) as $namaKategori => $daftar)
                        <optgroup label="{{ $namaKategori }}">
                            @foreach ($daftar as $produk)
                                <option value="{{ $produk->id_produk }}" @selected((int) old('id_produk', $idTerpilih) === $produk->id_produk)>
                                    {{ $produk->nama_produk }} — {{ $produk->hargaRupiah() }} (stok {{ $produk->stok }})
                                </option>
                            @endforeach
                        </optgroup>
                    @endforeach
                </select>

                <div class="row">
                    <div class="col-md-6">
                        <label for="nama">Nama</label>
                        <input id="nama" name="nama" type="text" class="form-control" maxlength="100"
                               value="{{ old('nama', $nama) }}" required>
                    </div>
                    <div class="col-md-6">
                        <label for="whatsapp">Nomor WhatsApp</label>
                        <input id="whatsapp" name="whatsapp" type="text" class="form-control" placeholder="08xxxxxxxxxx"
                               value="{{ old('whatsapp', $whatsapp) }}" required>
                    </div>
                </div>

                <label for="alamat_pengiriman">Alamat pengiriman (opsional)</label>
                <textarea id="alamat_pengiriman" name="alamat_pengiriman" class="form-control" maxlength="500"
                          style="height: 90px" placeholder="Kosongkan jika diambil langsung di butik">{{ old('alamat_pengiriman', $alamat) }}</textarea>

                <div class="row">
                    <div class="col-md-6">
                        <label for="ukuran">Ukuran (opsional)</label>
                        <select id="ukuran" name="ukuran" class="form-control">
                            <option value="">-- Pilih ukuran --</option>
                            @foreach ($ukuran as $item)
                                <option value="{{ $item }}" @selected(old('ukuran') === $item)>{{ $item }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label for="jumlah">Jumlah</label>
                        <input id="jumlah" name="jumlah" type="number" class="form-control" min="1" max="1000"
                               value="{{ old('jumlah', 1) }}" required>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <label for="tanggal_sewa">Tanggal sewa</label>
                        <input id="tanggal_sewa" name="tanggal_sewa" type="date" class="form-control"
                               min="{{ now()->toDateString() }}" value="{{ old('tanggal_sewa') }}" required>
                    </div>
                    <div class="col-md-6">
                        <label for="tanggal_kembali">Tanggal kembali</label>
                        <input id="tanggal_kembali" name="tanggal_kembali" type="date" class="form-control"
                               min="{{ now()->toDateString() }}" value="{{ old('tanggal_kembali') }}" required>
                    </div>
                </div>

                <label for="detail_pesanan">Catatan (opsional)</label>
                <textarea id="detail_pesanan" name="detail_pesanan" class="form-control" maxlength="2000"
                          placeholder="Contoh: acara wisuda, butuh dipakai jam 7 pagi">{{ old('detail_pesanan') }}</textarea>

                <div class="text-center mt-30">
                    <button type="submit" class="submit-btn primary-btn text-uppercase">KONFIRMASI RENTAL<span class="lnr lnr-arrow-right"></span></button>
                </div>
            </form>
        </div>
    </div>
@endsection
