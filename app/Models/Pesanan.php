<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Pesanan extends Model
{
    // Laravel secara default menebak "pesanans"; ditulis eksplisit agar jelas.
    protected $table = 'pesanans';

    protected $fillable = [
        'nama',
        'whatsapp',
        'jenis_pakaian',
        'model_lengan',
        'ukuran',
        'bahan',
        'warna',
        'jumlah',
        'detail_pesanan',
        'status',
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

    public const STATUS = ['baru', 'diproses', 'selesai', 'dibatalkan'];
}
