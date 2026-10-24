<?php

namespace App\Models;

use Database\Factories\ProdukFactory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Produk extends Model
{
    /** @use HasFactory<ProdukFactory> */
    use HasFactory;

    protected $table = 'produks';

    protected $primaryKey = 'id_produk';

    protected $fillable = [
        'id_kategori',
        'nama_produk',
        'deskripsi',
        'harga_sewa',
        'stok',
        'foto_produk',
        'status_produk',
    ];

    public const STATUS_PRODUK = ['aktif', 'nonaktif'];

    /** @return BelongsTo<Kategori, $this> */
    public function kategori(): BelongsTo
    {
        return $this->belongsTo(Kategori::class, 'id_kategori', 'id_kategori');
    }

    /** @return HasMany<DetailTransaksi, $this> */
    public function detailTransaksis(): HasMany
    {
        return $this->hasMany(DetailTransaksi::class, 'id_produk', 'id_produk');
    }

    /** Produk yang tampil di katalog untuk pelanggan. */
    public function scopeAktif(Builder $query): void
    {
        $query->where('status_produk', 'aktif');
    }

    /** Pencarian sederhana pada nama dan deskripsi produk. */
    public function scopeCari(Builder $query, string $kata): void
    {
        $query->where(function (Builder $q) use ($kata) {
            $q->where('nama_produk', 'like', "%{$kata}%")
                ->orWhere('deskripsi', 'like', "%{$kata}%");
        });
    }

    public function hargaRupiah(): string
    {
        return 'Rp '.number_format($this->harga_sewa, 0, ',', '.');
    }

    public function fotoUrl(): ?string
    {
        return $this->foto_produk ? asset('uploads/produk/'.$this->foto_produk) : null;
    }

    public function stokHabis(): bool
    {
        return $this->stok < 1;
    }
}
