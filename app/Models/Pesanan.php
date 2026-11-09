<?php

namespace App\Models;

use Database\Factories\PesananFactory;
use Illuminate\Database\Eloquent\Builder;
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
        'stok_dipotong',
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

    /** Status "Pesan (Kustom)": nilai di database => teks yang dilihat pengguna. */
    public const LABEL_STATUS_KUSTOM = [
        'baru'         => 'Baru',
        'diproses'     => 'Dikerjakan',
        'siap_diambil' => 'Siap diambil',
        'selesai'      => 'Selesai',
        'dibatalkan'   => 'Dibatalkan',
    ];

    /** Status "Rental". Nilainya juga disalin ke penyewaans.status_sewa. */
    public const LABEL_STATUS_RENTAL = [
        'dipinjam'           => 'Dipinjam',
        'belum_dikembalikan' => 'Belum dikembalikan',
        'dikembalikan'       => 'Dikembalikan',
    ];

    /** Semua label status, untuk lencana pada daftar yang bisa memuat kedua jenis pesanan. */
    public const LABEL_STATUS = [...self::LABEL_STATUS_KUSTOM, ...self::LABEL_STATUS_RENTAL];

    public const STATUS_KUSTOM = ['baru', 'diproses', 'siap_diambil', 'selesai', 'dibatalkan'];

    public const STATUS_RENTAL = ['dipinjam', 'belum_dikembalikan', 'dikembalikan'];

    public const STATUS = [...self::STATUS_KUSTOM, ...self::STATUS_RENTAL];

    /** Perpindahan status Kustom yang diperbolehkan untuk admin. Selesai dan dibatalkan bersifat final. */
    public const TRANSISI_KUSTOM = [
        'baru'         => ['diproses', 'dibatalkan'],
        'diproses'     => ['siap_diambil', 'dibatalkan'],
        'siap_diambil' => ['selesai', 'dibatalkan'],
        'selesai'      => [],
        'dibatalkan'   => [],
    ];

    /** Perpindahan status Rental yang diperbolehkan untuk admin. Dikembalikan bersifat final. */
    public const TRANSISI_RENTAL = [
        'dipinjam'           => ['belum_dikembalikan', 'dikembalikan'],
        'belum_dikembalikan' => ['dikembalikan'],
        'dikembalikan'       => [],
    ];

    /** @return array<string, string> */
    protected function casts(): array
    {
        return [
            'tanggal_sewa'    => 'date',
            'tanggal_kembali' => 'date',
            'stok_dipotong'   => 'boolean',
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

    /** Pesanan Rental (sewa busana dari Collection). */
    public function scopeRental(Builder $query): void
    {
        $query->where('tipe', 'rental');
    }

    /** Pesanan "Pesan (Kustom)" (jahit custom). */
    public function scopeKustom(Builder $query): void
    {
        $query->where('tipe', 'pesan');
    }

    public function isRental(): bool
    {
        return $this->tipe === 'rental';
    }

    public function labelTipe(): string
    {
        return $this->isRental() ? 'Rental' : 'Pesan (jahit custom)';
    }

    /** Hanya pesanan Pesan (Kustom) yang bisa diubah isinya; rental tidak bisa diubah pelanggan. */
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
        $transisi = $this->isRental() ? self::TRANSISI_RENTAL : self::TRANSISI_KUSTOM;

        return $transisi[$this->status] ?? [];
    }

    public function bisaDiubahStatusKe(string $status): bool
    {
        return in_array($status, $this->statusBerikutnya(), true);
    }
}
