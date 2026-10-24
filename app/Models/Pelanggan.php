<?php

namespace App\Models;

use Database\Factories\PelangganFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;

class Pelanggan extends Authenticatable
{
    /** @use HasFactory<PelangganFactory> */
    use HasFactory;

    protected $table = 'pelanggans';

    protected $primaryKey = 'id_pelanggan';

    protected $fillable = [
        'nama_pelanggan',
        'email',
        'password',
        'no_telp',
        'alamat',
        'tanggal_daftar',
        'status_akun',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    public const STATUS_AKUN = ['aktif', 'nonaktif'];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'password' => 'hashed',
            'tanggal_daftar' => 'datetime',
        ];
    }

    /** @return HasMany<Pesanan, $this> */
    public function pesanans(): HasMany
    {
        return $this->hasMany(Pesanan::class, 'id_pelanggan', 'id_pelanggan');
    }
}
