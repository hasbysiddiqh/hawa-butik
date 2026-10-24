<?php

namespace Database\Seeders;

use App\Models\Kategori;
use App\Models\Produk;
use Illuminate\Database\Seeder;

class KatalogSeeder extends Seeder
{
    /**
     * Contoh kategori dan produk agar katalog tidak kosong saat pengembangan.
     */
    public function run(): void
    {
        $katalog = [
            'Kebaya' => [
                ['Kebaya Brokat Klasik', 'Kebaya brokat dengan potongan klasik, cocok untuk wisuda dan pernikahan.', 350000, 5],
                ['Kebaya Modern Satin', 'Kebaya satin dengan lengan panjang dan detail payet halus.', 400000, 3],
            ],
            'Gamis' => [
                ['Gamis Tenun Sulawesi', 'Gamis dengan sentuhan kain tenun khas Sulawesi.', 450000, 4],
            ],
            'Blouse' => [
                ['Blouse Linen Casual', 'Blouse linen yang adem untuk dipakai sehari-hari.', 150000, 8],
            ],
            'Setelan' => [
                ['Setelan Seragam Keluarga', 'Setelan kemeja dan rok untuk acara keluarga.', 500000, 0],
            ],
        ];

        foreach ($katalog as $namaKategori => $produks) {
            $kategori = Kategori::firstOrCreate(['nama_kategori' => $namaKategori]);

            foreach ($produks as [$nama, $deskripsi, $harga, $stok]) {
                Produk::firstOrCreate(
                    ['nama_produk' => $nama],
                    [
                        'id_kategori' => $kategori->id_kategori,
                        'deskripsi' => $deskripsi,
                        'harga_sewa' => $harga,
                        'stok' => $stok,
                        'status_produk' => 'aktif',
                    ],
                );
            }
        }
    }
}
