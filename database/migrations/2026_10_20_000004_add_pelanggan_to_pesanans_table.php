<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Pesanan sekarang dimiliki pelanggan yang login (id_pelanggan) dan punya alamat_pengiriman,
     * sesuai ERD. Pesanan lama (sebelum ada login) dibiarkan dengan id_pelanggan kosong.
     */
    public function up(): void
    {
        Schema::table('pesanans', function (Blueprint $table) {
            $table->foreignId('id_pelanggan')->nullable()->after('id')
                ->constrained('pelanggans', 'id_pelanggan')->nullOnDelete();
            $table->text('alamat_pengiriman')->nullable()->after('whatsapp');
        });
    }

    public function down(): void
    {
        Schema::table('pesanans', function (Blueprint $table) {
            $table->dropConstrainedForeignId('id_pelanggan');
            $table->dropColumn('alamat_pengiriman');
        });
    }
};
