<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Menambah kolom untuk pesanan jahit custom pada tabel pesanans
     * (tabel lama: id, nama, whatsapp, detail_pesanan, timestamps).
     */
    public function up(): void
    {
        Schema::table('pesanans', function (Blueprint $table) {
            $table->string('jenis_pakaian')->nullable()->after('whatsapp');
            $table->string('model_lengan')->nullable()->after('jenis_pakaian');
            $table->string('ukuran')->nullable()->after('model_lengan');
            $table->string('bahan')->nullable()->after('ukuran');
            $table->string('warna')->nullable()->after('bahan');
            $table->unsignedInteger('jumlah')->default(1)->after('warna');
            $table->string('status')->default('baru')->after('jumlah');
        });

        // detail_pesanan sekarang menjadi catatan tambahan (boleh kosong)
        Schema::table('pesanans', function (Blueprint $table) {
            $table->text('detail_pesanan')->nullable()->change();
        });
    }

    public function down(): void
    {
        Schema::table('pesanans', function (Blueprint $table) {
            $table->dropColumn([
                'jenis_pakaian',
                'model_lengan',
                'ukuran',
                'bahan',
                'warna',
                'jumlah',
                'status',
            ]);
        });
    }
};
