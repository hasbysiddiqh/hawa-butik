<?php

namespace Tests\Feature\Admin;

use App\Models\Admin;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CollectionNavTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_header_menu_has_no_about_or_contact(): void
    {
        $this->actingAs(Admin::factory()->create(), 'admin')
            ->get(route('admin.dashboard'))
            ->assertOk()
            ->assertSee(route('admin.produk.index'), false)
            ->assertSee(route('admin.pesanan.index'), false)
            ->assertSee(route('admin.transaksi.index'), false)
            ->assertDontSee('#about', false)
            ->assertDontSee('#contact', false);
    }

    public function test_produk_page_shows_produk_and_kategori_subnav(): void
    {
        $this->actingAs(Admin::factory()->create(), 'admin')
            ->get(route('admin.produk.index'))
            ->assertOk()
            ->assertSee('subnav-koleksi', false)
            ->assertSee(route('admin.kategori.index'), false);
    }

    public function test_kategori_page_shows_produk_and_kategori_subnav(): void
    {
        $this->actingAs(Admin::factory()->create(), 'admin')
            ->get(route('admin.kategori.index'))
            ->assertOk()
            ->assertSee('subnav-koleksi', false)
            ->assertSee(route('admin.produk.index'), false);
    }

    public function test_other_admin_pages_have_no_collection_subnav(): void
    {
        $admin = Admin::factory()->create();

        foreach (['admin.dashboard', 'admin.pesanan.index', 'admin.transaksi.index'] as $rute) {
            $this->actingAs($admin, 'admin')
                ->get(route($rute))
                ->assertOk()
                ->assertDontSee('subnav-koleksi', false);
        }
    }
}
