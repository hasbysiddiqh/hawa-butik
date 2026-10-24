<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Penyewaan extends Model
{
    protected $table = 'penyewaans';
    protected $primaryKey = 'id_sewa';

    protected $fillable = [
        'id_pesanan',
        'tgl_mulai',
        'tgl_kembali',
        'tgl_selesai',
        'denda',
        'status_sewa',
    ];

    protected function casts(): array
    {
        return [
            'tgl_mulai' => 'date',
            'tgl_kembali' => 'date',
            'tgl_selesai' => 'date',
        ];
    }

    /** @return BelongsTo<Pesanan, $this> */
    public function pesanan(): BelongsTo
    {
        return $this->belongsTo(Pesanan::class, 'id_pesanan', 'id');
    }
}
