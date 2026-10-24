<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Tabel Wishlist (id_wishlist, id_pelanggan, tanggal_buat) dan
     * Detail_Wishlist (id_detail_wishlist, id_wishlist, id_produk, tanggal_ditambahkan) sesuai ERD.
     */
    public function up(): void
    {
        Schema::create('wishlists', function (Blueprint $table) {
            $table->id('id_wishlist');
            $table->foreignId('id_pelanggan')->unique()->constrained('pelanggans', 'id_pelanggan')->cascadeOnDelete();
            $table->timestamp('tanggal_buat')->useCurrent();
        });

        Schema::create('detail_wishlists', function (Blueprint $table) {
            $table->id('id_detail_wishlist');
            $table->foreignId('id_wishlist')->constrained('wishlists', 'id_wishlist')->cascadeOnDelete();
            $table->foreignId('id_produk')->constrained('produks', 'id_produk')->cascadeOnDelete();
            $table->timestamp('tanggal_ditambahkan')->useCurrent();

            $table->unique(['id_wishlist', 'id_produk']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('detail_wishlists');
        Schema::dropIfExists('wishlists');
    }
};
