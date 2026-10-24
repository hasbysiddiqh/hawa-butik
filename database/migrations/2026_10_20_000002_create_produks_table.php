<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Tabel Produk sesuai ERD: id_produk, id_kategori, nama_produk, deskripsi,
     * harga_sewa, stok, foto_produk, status_produk.
     */
    public function up(): void
    {
        Schema::create('produks', function (Blueprint $table) {
            $table->id('id_produk');
            $table->foreignId('id_kategori')->constrained('kategoris', 'id_kategori')->restrictOnDelete();
            $table->string('nama_produk', 150);
            $table->text('deskripsi')->nullable();
            $table->unsignedInteger('harga_sewa');
            $table->unsignedInteger('stok')->default(0);
            $table->string('foto_produk')->nullable();
            $table->string('status_produk', 20)->default('aktif');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('produks');
    }
};
