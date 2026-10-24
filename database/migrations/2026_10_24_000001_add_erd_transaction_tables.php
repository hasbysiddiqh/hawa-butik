<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Menambahkan struktur transaksi sesuai ERD terbaru tanpa menghapus
     * tabel/kolom lama yang masih dipakai website.
     */
    public function up(): void
    {
        Schema::table('pesanans', function (Blueprint $table) {
            $table->unsignedBigInteger('total')->default(0)->after('jumlah');
            $table->foreignId('id_admin_verifikasi')->nullable()->after('status')
                ->constrained('admins', 'id_admin')->nullOnDelete();
        });

        Schema::create('penyewaans', function (Blueprint $table) {
            $table->id('id_sewa');
            $table->foreignId('id_pesanan')->unique()->constrained('pesanans', 'id')->cascadeOnDelete();
            $table->date('tgl_mulai');
            $table->date('tgl_kembali');
            $table->date('tgl_selesai')->nullable();
            $table->unsignedBigInteger('denda')->default(0);
            $table->string('status_sewa', 30)->default('dipesan');
            $table->timestamps();
        });

        Schema::create('detail_transaksis', function (Blueprint $table) {
            $table->id('id_detail');
            // Kolom id pada tabel pesanans merupakan ID pesanan yang digunakan aplikasi saat ini.
            $table->foreignId('id_pesanan')->constrained('pesanans', 'id')->cascadeOnDelete();
            $table->foreignId('id_produk')->nullable()->constrained('produks', 'id_produk')->nullOnDelete();
            $table->unsignedInteger('jumlah')->default(1);
            $table->unsignedBigInteger('harga_satuan')->default(0);
            $table->unsignedBigInteger('subtotal')->default(0);
            $table->timestamps();
        });

        Schema::create('pembayarans', function (Blueprint $table) {
            $table->id('id_bayar');
            $table->foreignId('id_pesanan')->unique()->constrained('pesanans', 'id')->cascadeOnDelete();
            $table->timestamp('tgl_bayar')->nullable();
            $table->string('metode', 50)->nullable();
            $table->unsignedBigInteger('jumlah_bayar')->default(0);
            $table->string('status_bayar', 30)->default('menunggu');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pembayarans');
        Schema::dropIfExists('detail_transaksis');
        Schema::dropIfExists('penyewaans');

        Schema::table('pesanans', function (Blueprint $table) {
            $table->dropConstrainedForeignId('id_admin_verifikasi');
            $table->dropColumn('total');
        });
    }
};
