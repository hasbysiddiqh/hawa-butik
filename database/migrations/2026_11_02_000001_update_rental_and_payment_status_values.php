<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Rental kini memakai status dipinjam / belum_dikembalikan / dikembalikan (sama dengan status sewa).
     * Rental lama dipetakan ke status baru. Pembayaran yang masih "menunggu" belum ada uang masuk,
     * jadi jumlah dibayarnya dikosongkan (sebelumnya otomatis terisi sebesar total).
     */
    public function up(): void
    {
        $peta = ['baru' => 'dipinjam', 'diproses' => 'dipinjam', 'selesai' => 'dikembalikan'];

        foreach ($peta as $lama => $baru) {
            DB::table('pesanans')->where('tipe', 'rental')->where('status', $lama)->update(['status' => $baru]);
        }

        foreach (['dipesan' => 'dipinjam', 'diproses' => 'dipinjam', 'selesai' => 'dikembalikan'] as $lama => $baru) {
            DB::table('penyewaans')->where('status_sewa', $lama)->update(['status_sewa' => $baru]);
        }

        DB::table('pembayarans')->where('status_bayar', 'menunggu')->update(['jumlah_bayar' => 0]);
    }

    public function down(): void
    {
        DB::table('pesanans')->where('tipe', 'rental')->where('status', 'dipinjam')->update(['status' => 'baru']);
        DB::table('pesanans')->where('tipe', 'rental')->where('status', 'dikembalikan')->update(['status' => 'selesai']);
        DB::table('penyewaans')->where('status_sewa', 'dipinjam')->update(['status_sewa' => 'dipesan']);
        DB::table('penyewaans')->where('status_sewa', 'dikembalikan')->update(['status_sewa' => 'selesai']);
    }
};
