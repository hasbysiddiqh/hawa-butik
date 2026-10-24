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
