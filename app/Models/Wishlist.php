<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Wishlist extends Model
{
    protected $table = 'wishlists';

    protected $primaryKey = 'id_wishlist';

    public $timestamps = false;

    protected $fillable = [
        'id_pelanggan',
        'tanggal_buat',
    ];

    /** @return array<string, string> */
    protected function casts(): array
    {
        return [
            'tanggal_buat' => 'datetime',
        ];
    }

    /** @return BelongsTo<Pelanggan, $this> */
    public function pelanggan(): BelongsTo
    {
        return $this->belongsTo(Pelanggan::class, 'id_pelanggan', 'id_pelanggan');
    }

    /**
     * Produk di dalam wishlist (lewat tabel detail_wishlists).
     *
     * @return BelongsToMany<Produk, $this>
     */
    public function produks(): BelongsToMany
    {
        return $this->belongsToMany(Produk::class, 'detail_wishlists', 'id_wishlist', 'id_produk')
            ->withPivot('tanggal_ditambahkan');
    }
}
