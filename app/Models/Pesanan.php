<?php

namespace App\Models;

use Database\Factories\PesananFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Pesanan extends Model
{
    /** @use HasFactory<PesananFactory> */
    use HasFactory;

    // Laravel secara default menebak "pesanans"; ditulis eksplisit agar jelas.
    protected $table = 'pesanans';

    protected $fillable = [
        'id_pelanggan',
        'tipe',
        'id_produk',
        'tanggal_sewa',
        'tanggal_kembali',
        'nama',
        'whatsapp',
        'alamat_pengiriman',
        'jenis_pakaian',
        'model_lengan',
        'ukuran',
        'bahan',
        'warna',
        'jumlah',
        'detail_pesanan',
        'status',
        'total',
        'id_admin_verifikasi',
    ];

    /**
     * Pilihan preset (data yang biasa dipesan).
     * Jika pelanggan tidak menemukan pilihan yang cocok, ia memilih
     * "Lainnya" lalu mengetik sendiri. Daftar ini mudah ditambah/diubah.
     */
    public const OPSI = [
        'jenis_pakaian' => ['Kebaya', 'Gamis', 'Blouse', 'Rok', 'Kemeja', 'Setelan', 'Seragam'],
        'model_lengan'  => ['Lengan pendek', 'Lengan panjang', 'Lengan 3/4', 'Tanpa lengan'],
        'ukuran'        => ['S', 'M', 'L', 'XL', 'XXL', 'XXXL'],
        'bahan'         => ['Brokat', 'Katun', 'Satin', 'Tenun', 'Sifon', 'Linen'],
    ];

    public const TIPE = ['pesan', 'rental'];

    /** Nilai di database => teks yang dilihat pengguna. */
    public const LABEL_STATUS = [
        'baru'       => 'Baru',
        'diproses'   => 'Dikerjakan',
        'selesai'    => 'Selesai',
        'dibatalkan' => 'Dibatalkan',
    ];

    public const STATUS = ['baru', 'diproses', 'selesai', 'dibatalkan'];

    /** Perpindahan status yang diperbolehkan untuk admin. Selesai dan dibatalkan bersifat final. */
    public const TRANSISI_STATUS = [
        'baru'       => ['diproses', 'dibatalkan'],
        'diproses'   => ['selesai', 'dibatalkan'],
        'selesai'    => [],
        'dibatalkan' => [],
    ];

    /** @return array<string, string> */
    protected function casts(): array
    {
        return [
            'tanggal_sewa'    => 'date',
            'tanggal_kembali' => 'date',
        ];
    }

    /** @return BelongsTo<Produk, $this> */
    public function produk(): BelongsTo
    {
        return $this->belongsTo(Produk::class, 'id_produk', 'id_produk');
    }

    /** @return BelongsTo<Pelanggan, $this> */
    public function pelanggan(): BelongsTo
    {
        return $this->belongsTo(Pelanggan::class, 'id_pelanggan', 'id_pelanggan');
    }

    /** @return HasMany<DetailTransaksi, $this> */
    public function detailTransaksis(): HasMany
    {
        return $this->hasMany(DetailTransaksi::class, 'id_pesanan', 'id');
    }

    /** @return HasOne<Penyewaan, $this> */
    public function penyewaan(): HasOne
    {
        return $this->hasOne(Penyewaan::class, 'id_pesanan', 'id');
    }

    /** @return HasOne<Pembayaran, $this> */
    public function pembayaran(): HasOne
    {
        return $this->hasOne(Pembayaran::class, 'id_pesanan', 'id');
    }

    /** @return BelongsTo<Admin, $this> */
    public function adminVerifikasi(): BelongsTo
    {
        return $this->belongsTo(Admin::class, 'id_admin_verifikasi', 'id_admin');
    }

    public function isRental(): bool
    {
        return $this->tipe === 'rental';
    }

    public function labelTipe(): string
    {
        return $this->isRental() ? 'Rental' : 'Pesan (jahit custom)';
    }

    /** Hanya pesanan jahit custom yang bisa diubah isinya; rental cukup dibatalkan lalu dibuat ulang. */
    public function bisaDiedit(): bool
    {
        return $this->bisaDiubah() && ! $this->isRental();
    }

    /** Pelanggan hanya boleh mengubah atau membatalkan pesanan selama statusnya masih "baru". */
    public function bisaDiubah(): bool
    {
        return $this->status === 'baru';
    }

    /** Waktu pesanan dibuat, ditampilkan dalam WITA. */
    public function tanggalDibuat(string $format = 'd M Y, H:i'): string
    {
        return $this->created_at?->format($format).' WITA';
    }

    public function labelStatus(): string
    {
        return self::LABEL_STATUS[$this->status] ?? ucfirst($this->status);
    }

    /**
     * Status yang boleh dipilih admin berikutnya.
     *
     * @return array<int, string>
     */
    public function statusBerikutnya(): array
    {
        return self::TRANSISI_STATUS[$this->status] ?? [];
    }

    public function bisaDiubahStatusKe(string $status): bool
    {
        return in_array($status, $this->statusBerikutnya(), true);
    }
}
