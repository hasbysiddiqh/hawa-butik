<?php

namespace Tests\Feature;

use App\Models\Kategori;
use App\Models\Produk;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class KatalogControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_can_browse_the_catalog(): void
    {
        Produk::factory()->create(['nama_produk' => 'Kebaya Brokat']);

        $this->get(route('katalog.index'))
            ->assertOk()
            ->assertSee('Kebaya Brokat');
    }

    public function test_nonaktif_produk_is_hidden_from_the_catalog(): void
    {
        Produk::factory()->create(['nama_produk' => 'Kebaya Tampil']);
        Produk::factory()->nonaktif()->create(['nama_produk' => 'Kebaya Tersembunyi']);

        $this->get(route('katalog.index'))
            ->assertSee('Kebaya Tampil')
            ->assertDontSee('Kebaya Tersembunyi');
    }

    public function test_search_matches_name_and_description(): void
    {
        Produk::factory()->create(['nama_produk' => 'Gamis Tenun', 'deskripsi' => 'Kain sutra']);
        Produk::factory()->create(['nama_produk' => 'Blouse Linen', 'deskripsi' => 'Adem dipakai']);

        $this->get(route('katalog.index', ['cari' => 'Tenun']))
            ->assertSee('Gamis Tenun')
            ->assertDontSee('Blouse Linen');

        $this->get(route('katalog.index', ['cari' => 'adem']))
            ->assertSee('Blouse Linen')
            ->assertDontSee('Gamis Tenun');
    }

    public function test_category_filter_shows_only_that_category(): void
    {
        $kebaya = Kategori::factory()->create(['nama_kategori' => 'Kebaya']);
        $gamis = Kategori::factory()->create(['nama_kategori' => 'Gamis']);
        Produk::factory()->for($kebaya, 'kategori')->create(['nama_produk' => 'Produk Kebaya']);
        Produk::factory()->for($gamis, 'kategori')->create(['nama_produk' => 'Produk Gamis']);

        $this->get(route('katalog.index', ['kategori' => $kebaya->id_kategori]))
            ->assertSee('Produk Kebaya')
            ->assertDontSee('Produk Gamis');
    }

    public function test_empty_result_shows_message(): void
    {
        $this->get(route('katalog.index', ['cari' => 'tidak-ada']))
            ->assertOk()
            ->assertSee('Produk tidak ditemukan.');
    }

    public function test_stok_habis_badge_is_shown(): void
    {
        Produk::factory()->stokHabis()->create();

        $this->get(route('katalog.index'))->assertSee('Stok habis');
    }

    public function test_detail_page_shows_produk_and_price(): void
    {
        $produk = Produk::factory()->create(['nama_produk' => 'Kebaya Brokat', 'harga_sewa' => 350000]);

        $this->get(route('katalog.show', $produk))
            ->assertOk()
            ->assertSee('Kebaya Brokat')
            ->assertSee('Rp 350.000');
    }

    public function test_detail_page_of_nonaktif_produk_returns_404(): void
    {
        $this->get(route('katalog.show', Produk::factory()->nonaktif()->create()))
            ->assertNotFound();
    }

    public function test_collection_groups_produk_by_jenis_pakaian(): void
    {
        $kebaya = Kategori::factory()->create(['nama_kategori' => 'Kebaya']);
        $gamis = Kategori::factory()->create(['nama_kategori' => 'Gamis']);
        Produk::factory()->for($kebaya, 'kategori')->create(['nama_produk' => 'Produk Kebaya']);
        Produk::factory()->for($gamis, 'kategori')->create(['nama_produk' => 'Produk Gamis']);

        $this->get(route('katalog.index'))
            ->assertOk()
            ->assertSeeInOrder(['Gamis', 'Produk Gamis', 'Kebaya', 'Produk Kebaya'])
            ->assertViewHas('kelompok', fn ($kelompok) => $kelompok->count() === 2);
    }

    public function test_kategori_without_active_produk_is_not_listed_as_a_group(): void
    {
        $kosong = Kategori::factory()->create(['nama_kategori' => 'Kategori Kosong']);
        Produk::factory()->nonaktif()->for($kosong, 'kategori')->create();

        $this->get(route('katalog.index'))
            ->assertViewHas('kelompok', fn ($kelompok) => $kelompok->isEmpty());
    }

    public function test_collection_has_sewa_button_and_no_wishlist(): void
    {
        $produk = Produk::factory()->create();

        $this->get(route('katalog.index'))
            ->assertSee(route('pelanggan.rental.create', ['produk' => $produk->id_produk]), false)
            ->assertDontSee('wishlist', false);
    }
}
