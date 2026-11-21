<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /** Lampiran (gambar/PDF) yang diunggah pelanggan pada bagian catatan pemesanan. */
    public function up(): void
    {
        Schema::table('pesanans', function (Blueprint $table) {
            $table->string('lampiran')->nullable()->after('detail_pesanan');
        });
    }

    public function down(): void
    {
        Schema::table('pesanans', function (Blueprint $table) {
            $table->dropColumn('lampiran');
        });
    }
};
