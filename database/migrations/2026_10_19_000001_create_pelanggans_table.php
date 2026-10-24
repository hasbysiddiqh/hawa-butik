<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Tabel Pelanggan sesuai ERD: id_pelanggan, nama_pelanggan, email, password,
     * no_telp, alamat, tanggal_daftar, status_akun.
     */
    public function up(): void
    {
        Schema::create('pelanggans', function (Blueprint $table) {
            $table->id('id_pelanggan');
            $table->string('nama_pelanggan', 100);
            $table->string('email')->unique();
            $table->string('password');
            $table->string('no_telp', 20);
            $table->text('alamat')->nullable();
            $table->timestamp('tanggal_daftar')->useCurrent();
            $table->string('status_akun', 20)->default('aktif');
            $table->rememberToken();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pelanggans');
    }
};
