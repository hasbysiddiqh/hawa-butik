<?php

namespace Tests\Feature\Admin;

use App\Models\Admin;
use App\Models\Pelanggan;
use App\Models\Pesanan;
use App\Models\Produk;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DashboardControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_is_redirected_to_login(): void
    {
        $this->get(route('admin.dashboard'))
            ->assertRedirect(route('login'));
    }

    public function test_pelanggan_cannot_open_admin_dashboard(): void
    {
        $this->actingAs(Pelanggan::factory()->create(), 'pelanggan')
            ->get(route('admin.dashboard'))
            ->assertRedirect(route('login'));
    }

    public function test_admin_sees_dashboard_with_name_and_totals(): void
    {
        $admin = Admin::factory()->create(['nama_admin' => 'Bu Hawa']);
        Pelanggan::factory()->count(2)->create();
        Pesanan::factory()->create(['status' => 'baru']);
        Pesanan::factory()->create(['status' => 'diproses']);
        Pesanan::factory()->create(['status' => 'selesai']);
        Produk::factory()->create(['stok' => 3]);
        Produk::factory()->stokHabis()->create();

        $this->actingAs($admin, 'admin')
            ->get(route('admin.dashboard'))
            ->assertOk()
            ->assertSee('Bu Hawa')
            ->assertViewHas('totalPesanan', 3)
            ->assertViewHas('totalPelanggan', 5)
            ->assertViewHas('totalProduk', 2)
            ->assertViewHas('stokHabis', 1)
            ->assertViewHas('perStatus', fn ($perStatus) => $perStatus['baru'] === 1
                && $perStatus['diproses'] === 1
                && $perStatus['selesai'] === 1);
    }

    public function test_dashboard_lists_the_five_latest_pesanan_only(): void
    {
        Pesanan::factory()->count(7)->create();

        $this->actingAs(Admin::factory()->create(), 'admin')
            ->get(route('admin.dashboard'))
            ->assertViewHas('pesananTerbaru', fn ($daftar) => $daftar->count() === 5);
    }
}
