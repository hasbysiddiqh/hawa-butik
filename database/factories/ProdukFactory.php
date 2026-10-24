<?php

namespace Database\Factories;

use App\Models\Kategori;
use App\Models\Produk;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Produk>
 */
class ProdukFactory extends Factory
{
    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'id_kategori' => Kategori::factory(),
            'nama_produk' => fake()->unique()->words(3, true),
            'deskripsi' => fake()->paragraph(),
            'harga_sewa' => fake()->numberBetween(5, 100) * 10000,
            'stok' => fake()->numberBetween(1, 10),
            'foto_produk' => null,
            'status_produk' => 'aktif',
        ];
    }

    public function nonaktif(): static
    {
        return $this->state(fn (array $attributes) => ['status_produk' => 'nonaktif']);
    }

    public function stokHabis(): static
    {
        return $this->state(fn (array $attributes) => ['stok' => 0]);
    }
}
