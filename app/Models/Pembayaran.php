<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Pembayaran extends Model
{
    protected $table = 'pembayarans';
    protected $primaryKey = 'id_bayar';

    protected $fillable = [
        'id_pesanan',
        'tgl_bayar',
        'metode',
        'jumlah_bayar',
        'status_bayar',
    ];

    /** Status transaksi: nilai di database => teks yang dilihat pengguna. */
    public const LABEL_STATUS = [
        'menunggu'      => 'Menunggu',
        'belum_dibayar' => 'Belum dibayar',
        'dicicil'       => 'Dicicil',
        'lunas'         => 'Lunas',
    ];

    /** Metode pembayaran: nilai di database => teks yang dilihat pengguna. */
    public const LABEL_METODE = [
        'debit' => 'Debit',
        'tunai' => 'Tunai',
    ];

    public function labelStatus(): string
    {
        return self::LABEL_STATUS[$this->status_bayar] ?? ucfirst(str_replace('_', ' ', (string) $this->status_bayar));
    }

    public function labelMetode(): string
    {
        return self::LABEL_METODE[$this->metode] ?? 'Belum dipilih';
    }

    /** Kelas lencana (.st) untuk status transaksi. */
    public function kelasStatus(): string
    {
        return match ($this->status_bayar) {
            'lunas'         => 'st-selesai',
            'dicicil'       => 'st-proses',
            'belum_dibayar' => 'st-batal',
            default         => 'st-baru',
        };
    }

    /** Total tagihan = total pesanan (0 jika jahit custom belum diestimasi). */
    public function totalTagihan(): int
    {
        return (int) $this->pesanan?->total;
    }

    /** Sisa yang belum dibayar; tidak pernah negatif. */
    public function sisaBayar(): int
    {
        return max($this->totalTagihan() - (int) $this->jumlah_bayar, 0);
    }

    protected function casts(): array
    {
        return ['tgl_bayar' => 'datetime'];
    }

    /** @return BelongsTo<Pesanan, $this> */
    public function pesanan(): BelongsTo
    {
        return $this->belongsTo(Pesanan::class, 'id_pesanan', 'id');
    }
}
