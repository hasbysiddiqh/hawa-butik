<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Pemesanan punya dua jenis: "pesan" (jahit custom, yang sudah ada) dan "rental"
     * (sewa busana dari Collection, lengkap dengan produk dan tanggal sewa/kembali).
     */
    public function up(): void
    {
        Schema::table('pesanans', function (Blueprint $table) {
            $table->string('tipe', 20)->default('pesan')->after('id_pelanggan');
            $table->foreignId('id_produk')->nullable()->after('tipe')
                ->constrained('produks', 'id_produk')->nullOnDelete();
            $table->date('tanggal_sewa')->nullable()->after('jumlah');
            $table->date('tanggal_kembali')->nullable()->after('tanggal_sewa');
        });
    }

    public function down(): void
    {
        Schema::table('pesanans', function (Blueprint $table) {
            $table->dropConstrainedForeignId('id_produk');
            $table->dropColumn(['tipe', 'tanggal_sewa', 'tanggal_kembali']);
        });
    }
};
