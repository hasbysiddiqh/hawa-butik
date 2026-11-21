<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /** Alasan denda yang ditulis admin dan dapat dilihat pelanggan. */
    public function up(): void
    {
        Schema::table('penyewaans', function (Blueprint $table) {
            $table->string('catatan_denda', 500)->nullable()->after('denda');
        });
    }

    public function down(): void
    {
        Schema::table('penyewaans', function (Blueprint $table) {
            $table->dropColumn('catatan_denda');
        });
    }
};
