{{-- Form bersama untuk tambah & ubah produk. Variabel: $action, $metode, $tombol, $produk, $kategoris --}}
@include('partials.validation-errors')

<form class="pesanan-form pesanan-card" action="{{ $action }}" method="POST" enctype="multipart/form-data">
    @csrf
    @if ($metode !== 'POST')
        @method($metode)
    @endif

    <h4 class="form-bagian"><span class="lnr lnr-tag"></span>Informasi produk</h4>
    <label for="nama_produk">Nama produk</label>
    <input id="nama_produk" name="nama_produk" type="text" class="form-control" maxlength="150" value="{{ old('nama_produk', $produk->nama_produk) }}" required>

    <div class="row">
        <div class="col-md-6">
            <label for="id_kategori">Kategori</label>
            <select id="id_kategori" name="id_kategori" class="form-control" required>
                <option value="">-- Pilih kategori --</option>
                @foreach ($kategoris as $kategori)
                    <option value="{{ $kategori->id_kategori }}" @selected((string) old('id_kategori', $produk->id_kategori) === (string) $kategori->id_kategori)>{{ $kategori->nama_kategori }}</option>
                @endforeach
            </select>
        </div>
        <div class="col-md-6">
            <label for="status_produk">Status</label>
            <select id="status_produk" name="status_produk" class="form-control" required>
                @foreach (App\Models\Produk::STATUS_PRODUK as $status)
                    <option value="{{ $status }}" @selected(old('status_produk', $produk->status_produk) === $status)>{{ ucfirst($status) }}</option>
                @endforeach
            </select>
        </div>
    </div>

    <label for="deskripsi">Deskripsi</label>
    <textarea id="deskripsi" name="deskripsi" class="form-control" maxlength="2000">{{ old('deskripsi', $produk->deskripsi) }}</textarea>

    <h4 class="form-bagian"><span class="lnr lnr-cart"></span>Harga &amp; stok</h4>
    <div class="row">
        <div class="col-md-6">
            <label for="harga_sewa">Harga sewa (Rp)</label>
            <input id="harga_sewa" name="harga_sewa" type="number" min="0" class="form-control" value="{{ old('harga_sewa', $produk->harga_sewa) }}" required>
        </div>
        <div class="col-md-6">
            <label for="stok">Stok</label>
            <input id="stok" name="stok" type="number" min="0" class="form-control" value="{{ old('stok', $produk->stok) }}" required>
        </div>
    </div>

    <h4 class="form-bagian"><span class="lnr lnr-picture"></span>Foto produk</h4>
    <label for="foto_produk">JPG, PNG, atau WebP, maks. 2 MB</label>
    @if ($produk->fotoUrl())
        <div class="mb-2"><img src="{{ $produk->fotoUrl() }}" alt="" style="max-width: 140px"></div>
        <small class="text-muted d-block mb-2">Pilih file baru hanya jika ingin mengganti foto.</small>
    @endif
    <input id="foto_produk" name="foto_produk" type="file" class="form-control" accept="image/jpeg,image/png,image/webp">

    <div class="form-aksi">
        <a href="{{ route('admin.produk.index') }}" class="form-batal">Batal</a>
        <button type="submit" class="submit-btn primary-btn text-uppercase">{{ $tombol }}<i class="arr"></i></button>
    </div>
</form>
