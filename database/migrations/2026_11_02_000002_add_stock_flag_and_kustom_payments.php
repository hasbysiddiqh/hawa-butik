<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * stok_dipotong menandai rental yang stoknya sudah dikurangi, supaya stok hanya dikembalikan
     * untuk rental yang memang dikurangi. Rental lama dibiarkan false agar stok yang sudah
     * diatur manual tidak berubah. Pesanan lama yang belum punya transaksi dibuatkan transaksi "menunggu".
     */
    public function up(): void
    {
        Schema::table('pesanans', function (Blueprint $table) {
            $table->boolean('stok_dipotong')->default(false)->after('total');
        });

        $tanpaTransaksi = DB::table('pesanans')
            ->leftJoin('pembayarans', 'pembayarans.id_pesanan', '=', 'pesanans.id')
            ->whereNull('pembayarans.id_bayar')
            ->pluck('pesanans.id');

        foreach ($tanpaTransaksi->chunk(100) as $kelompok) {
            DB::table('pembayarans')->insert($kelompok->map(fn ($id) => [
                'id_pesanan' => $id,
                'jumlah_bayar' => 0,
                'status_bayar' => 'menunggu',
                'created_at' => now(),
                'updated_at' => now(),
            ])->all());
        }
    }

    public function down(): void
    {
        Schema::table('pesanans', function (Blueprint $table) {
            $table->dropColumn('stok_dipotong');
        });
    }
};
