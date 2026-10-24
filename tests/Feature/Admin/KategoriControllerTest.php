<?php

namespace Tests\Feature\Admin;

use App\Models\Admin;
use App\Models\Kategori;
use App\Models\Pelanggan;
use App\Models\Produk;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class KategoriControllerTest extends TestCase
{
    use RefreshDatabase;

    private function masukSebagaiAdmin(): void
    {
        $this->actingAs(Admin::factory()->create(), 'admin');
    }

    public function test_guest_and_pelanggan_are_redirected_to_login(): void
    {
        $this->get(route('admin.kategori.index'))->assertRedirect(route('login'));
        $this->post(route('admin.kategori.store'), ['nama_kategori' => 'Kebaya'])->assertRedirect(route('login'));

        $this->actingAs(Pelanggan::factory()->create(), 'pelanggan')
            ->get(route('admin.kategori.index'))
            ->assertRedirect(route('login'));

        $this->assertDatabaseCount('kategoris', 0);
    }

    public function test_index_lists_kategori_with_product_count(): void
    {
        $kategori = Kategori::factory()->create(['nama_kategori' => 'Kebaya']);
        Produk::factory()->count(2)->for($kategori, 'kategori')->create();

        $this->masukSebagaiAdmin();

        $this->get(route('admin.kategori.index'))
            ->assertOk()
            ->assertSee('Kebaya')
            ->assertViewHas('kategoris', fn ($daftar) => $daftar->first()->produks_count === 2);
    }

    public function test_admin_can_add_kategori(): void
    {
        $this->masukSebagaiAdmin();

        $this->post(route('admin.kategori.store'), ['nama_kategori' => 'Kebaya', 'deskripsi' => 'Busana adat'])
            ->assertRedirect(route('admin.kategori.index'))
            ->assertSessionHas('success');

        $this->assertDatabaseHas('kategoris', ['nama_kategori' => 'Kebaya', 'deskripsi' => 'Busana adat']);
    }

    public function test_name_is_required_and_must_be_unique(): void
    {
        Kategori::factory()->create(['nama_kategori' => 'Kebaya']);
        $this->masukSebagaiAdmin();

        $this->post(route('admin.kategori.store'), ['nama_kategori' => ''])->assertSessionHasErrors('nama_kategori');
        $this->post(route('admin.kategori.store'), ['nama_kategori' => 'Kebaya'])->assertSessionHasErrors('nama_kategori');

        $this->assertDatabaseCount('kategoris', 1);
    }

    public function test_admin_can_update_kategori_and_keep_its_own_name(): void
    {
        $kategori = Kategori::factory()->create(['nama_kategori' => 'Kebaya']);
        $this->masukSebagaiAdmin();

        $this->get(route('admin.kategori.edit', $kategori))->assertOk()->assertSee('Kebaya');

        $this->put(route('admin.kategori.update', $kategori), ['nama_kategori' => 'Kebaya', 'deskripsi' => 'Baru'])
            ->assertSessionHasNoErrors();
        $this->put(route('admin.kategori.update', $kategori), ['nama_kategori' => 'Kebaya Modern'])
            ->assertRedirect(route('admin.kategori.index'));

        $this->assertSame('Kebaya Modern', $kategori->fresh()->nama_kategori);
    }

    public function test_renaming_to_another_existing_name_is_rejected(): void
    {
        Kategori::factory()->create(['nama_kategori' => 'Gamis']);
        $kategori = Kategori::factory()->create(['nama_kategori' => 'Kebaya']);
        $this->masukSebagaiAdmin();

        $this->put(route('admin.kategori.update', $kategori), ['nama_kategori' => 'Gamis'])
            ->assertSessionHasErrors('nama_kategori');

        $this->assertSame('Kebaya', $kategori->fresh()->nama_kategori);
    }

    public function test_empty_kategori_can_be_deleted(): void
    {
        $kategori = Kategori::factory()->create();
        $this->masukSebagaiAdmin();

        $this->delete(route('admin.kategori.destroy', $kategori))
            ->assertRedirect(route('admin.kategori.index'))
            ->assertSessionHas('success');

        $this->assertDatabaseCount('kategoris', 0);
    }

    public function test_kategori_with_produk_cannot_be_deleted(): void
    {
        $kategori = Kategori::factory()->create();
        Produk::factory()->for($kategori, 'kategori')->create();
        $this->masukSebagaiAdmin();

        $this->delete(route('admin.kategori.destroy', $kategori))
            ->assertRedirect(route('admin.kategori.index'))
            ->assertSessionHas('error');

        $this->assertDatabaseCount('kategoris', 1);
    }
}
