<?php

namespace Database\Seeders;

use App\Models\Admin;
use Illuminate\Database\Seeder;

class AdminSeeder extends Seeder
{
    public const EMAIL = 'adminhawa@gmail.com';

    /**
     * Hanya ada satu admin: adminhawa@gmail.com / admin123.
     * Admin lain yang mungkin tersisa dari data lama dihapus.
     * Ganti kata sandinya sebelum dipakai di server sungguhan.
     */
    public function run(): void
    {
        Admin::where('email', '!=', self::EMAIL)->delete();

        Admin::updateOrCreate(
            ['email' => self::EMAIL],
            [
                'nama_admin' => 'Admin Hawa Butik',
                'password' => 'admin123',
                'no_telp' => '081234567890',
                'status_akun' => 'aktif',
            ],
        );
    }
}
