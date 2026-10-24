<?php

namespace Tests\Feature\Pelanggan;

use App\Models\Admin;
use App\Models\Pelanggan;
use App\Models\Produk;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class WishlistControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_is_redirected_to_login(): void
    {
        $produk = Produk::factory()->create();

        $this->get(route('pelanggan.wishlist.index'))->assertRedirect(route('login'));
        $this->post(route('pelanggan.wishlist.store', $produk))->assertRedirect(route('login'));
        $this->delete(route('pelanggan.wishlist.destroy', $produk))->assertRedirect(route('login'));

        $this->assertDatabaseCount('detail_wishlists', 0);
    }

    public function test_admin_cannot_use_wishlist(): void
    {
        $this->actingAs(Admin::factory()->create(), 'admin')
            ->post(route('pelanggan.wishlist.store', Produk::factory()->create()))
            ->assertRedirect(route('login'));
    }

    public function test_adding_a_produk_creates_the_wishlist_and_detail(): void
    {
        $saya = Pelanggan::factory()->create();
        $produk = Produk::factory()->create();

        $this->actingAs($saya, 'pelanggan')
            ->post(route('pelanggan.wishlist.store', $produk))
            ->assertSessionHas('success');

        $this->assertDatabaseCount('wishlists', 1);
        $this->assertDatabaseHas('detail_wishlists', [
            'id_wishlist' => $saya->wishlist->id_wishlist,
            'id_produk' => $produk->id_produk,
        ]);
    }

    public function test_adding_the_same_produk_twice_keeps_one_row(): void
    {
        $produk = Produk::factory()->create();
        $this->actingAs(Pelanggan::factory()->create(), 'pelanggan');

        $this->post(route('pelanggan.wishlist.store', $produk))->assertSessionHas('success');
        $this->post(route('pelanggan.wishlist.store', $produk))->assertSessionHas('success');

        $this->assertDatabaseCount('detail_wishlists', 1);
    }

    public function test_nonaktif_produk_cannot_be_added(): void
    {
        $this->actingAs(Pelanggan::factory()->create(), 'pelanggan')
            ->post(route('pelanggan.wishlist.store', Produk::factory()->nonaktif()->create()))
            ->assertNotFound();

        $this->assertDatabaseCount('detail_wishlists', 0);
    }

    public function test_removing_a_produk_deletes_only_that_detail(): void
    {
        $saya = Pelanggan::factory()->create();
        $dibuang = Produk::factory()->create();
        $dipertahankan = Produk::factory()->create();
        $saya->ambilWishlist()->produks()->attach([$dibuang->id_produk, $dipertahankan->id_produk]);

        $this->actingAs($saya, 'pelanggan')
            ->delete(route('pelanggan.wishlist.destroy', $dibuang))
            ->assertSessionHas('success');

        $this->assertDatabaseMissing('detail_wishlists', ['id_produk' => $dibuang->id_produk]);
        $this->assertDatabaseHas('detail_wishlists', ['id_produk' => $dipertahankan->id_produk]);
    }

    public function test_index_shows_own_wishlist_only(): void
    {
        $saya = Pelanggan::factory()->create();
        $orangLain = Pelanggan::factory()->create();
        $punyaSaya = Produk::factory()->create(['nama_produk' => 'Kebaya Saya']);
        $punyaOrangLain = Produk::factory()->create(['nama_produk' => 'Gamis Orang Lain']);
        $saya->ambilWishlist()->produks()->attach($punyaSaya->id_produk);
        $orangLain->ambilWishlist()->produks()->attach($punyaOrangLain->id_produk);

        $this->actingAs($saya, 'pelanggan')
            ->get(route('pelanggan.wishlist.index'))
            ->assertOk()
            ->assertSee('Kebaya Saya')
            ->assertDontSee('Gamis Orang Lain');
    }

    public function test_empty_wishlist_shows_message(): void
    {
        $this->actingAs(Pelanggan::factory()->create(), 'pelanggan')
            ->get(route('pelanggan.wishlist.index'))
            ->assertOk()
            ->assertSee('Wishlist Anda masih kosong.');
    }

    public function test_deleting_produk_removes_it_from_wishlists(): void
    {
        $saya = Pelanggan::factory()->create();
        $produk = Produk::factory()->create();
        $saya->ambilWishlist()->produks()->attach($produk->id_produk);

        $produk->delete();

        $this->assertDatabaseCount('detail_wishlists', 0);
    }
}
