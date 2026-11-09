<?php

namespace Database\Factories;

use App\Models\Pelanggan;
use App\Models\Pesanan;
use App\Models\Produk;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Pesanan>
 */
class PesananFactory extends Factory
{
    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'id_pelanggan' => Pelanggan::factory(),
            'nama' => fake()->name(),
            'whatsapp' => '08'.fake()->numerify('##########'),
            'alamat_pengiriman' => fake()->address(),
            'jenis_pakaian' => fake()->randomElement(Pesanan::OPSI['jenis_pakaian']),
            'model_lengan' => fake()->randomElement(Pesanan::OPSI['model_lengan']),
            'ukuran' => fake()->randomElement(Pesanan::OPSI['ukuran']),
            'bahan' => fake()->randomElement(Pesanan::OPSI['bahan']),
            'warna' => fake()->colorName(),
            'jumlah' => fake()->numberBetween(1, 5),
            'detail_pesanan' => fake()->sentence(),
            'status' => 'baru',
        ];
    }

    /** Pesanan Rental (sewa busana dari Collection). */
    public function rental(): static
    {
        return $this->state(fn (array $attributes) => [
            'tipe' => 'rental',
            'id_produk' => Produk::factory(),
            'model_lengan' => null,
            'bahan' => null,
            'warna' => null,
            'tanggal_sewa' => now()->addDay()->toDateString(),
            'tanggal_kembali' => now()->addDays(3)->toDateString(),
            'status' => 'dipinjam',
            'total' => 100000,
        ]);
    }

    public function status(string $status): static
    {
        return $this->state(fn (array $attributes) => ['status' => $status]);
    }
}
