<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Fitur wishlist dihapus. Tabelnya ikut dibuang (aman dijalankan di database baru maupun lama).
     */
    public function up(): void
    {
        Schema::dropIfExists('detail_wishlists');
        Schema::dropIfExists('wishlists');
    }

    public function down(): void
    {
        // Fitur wishlist sudah dihapus, tidak ada yang perlu dikembalikan.
    }
};
