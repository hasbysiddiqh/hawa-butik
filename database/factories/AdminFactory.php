<?php

namespace Database\Factories;

use App\Models\Admin;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

/**
 * @extends Factory<Admin>
 */
class AdminFactory extends Factory
{
    /**
     * The current password being used by the factory.
     */
    protected static ?string $password;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'nama_admin' => fake()->name(),
            'email' => fake()->unique()->safeEmail(),
            'password' => static::$password ??= Hash::make('password'),
            'no_telp' => '08'.fake()->numerify('##########'),
            'status_akun' => 'aktif',
            'remember_token' => Str::random(10),
        ];
    }

    /**
     * Akun yang dinonaktifkan tidak boleh masuk.
     */
    public function nonaktif(): static
    {
        return $this->state(fn (array $attributes) => [
            'status_akun' => 'nonaktif',
        ]);
    }
}
