<?php

namespace Database\Seeders;

use App\Models\Pelanggan;
use Illuminate\Database\Seeder;

class PelangganSeeder extends Seeder
{
    /**
     * Akun pelanggan contoh untuk pengembangan.
     */
    public function run(): void
    {
        Pelanggan::firstOrCreate(
            ['email' => 'pelanggan@hawabutik.test'],
            [
                'nama_pelanggan' => 'Pelanggan Contoh',
                'password' => 'password',
                'no_telp' => '081298765432',
                'alamat' => 'Jl. Contoh No. 1, Makassar',
                'tanggal_daftar' => now(),
                'status_akun' => 'aktif',
            ],
        );
    }
}
