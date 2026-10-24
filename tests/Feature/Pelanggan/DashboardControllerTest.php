<?php

namespace Tests\Feature\Pelanggan;

use App\Models\Admin;
use App\Models\Pelanggan;
use App\Models\Pesanan;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DashboardControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_is_redirected_to_login(): void
    {
        $this->get(route('pelanggan.dashboard'))
            ->assertRedirect(route('login'));
    }

    public function test_admin_cannot_open_pelanggan_dashboard(): void
    {
        $this->actingAs(Admin::factory()->create(), 'admin')
            ->get(route('pelanggan.dashboard'))
            ->assertRedirect(route('login'));
    }

    public function test_dashboard_counts_only_own_pesanan(): void
    {
        $saya = Pelanggan::factory()->create(['nama_pelanggan' => 'Ayu Lestari']);
        Pesanan::factory()->for($saya, 'pelanggan')->status('baru')->create();
        Pesanan::factory()->for($saya, 'pelanggan')->status('diproses')->create();
        Pesanan::factory()->for($saya, 'pelanggan')->status('selesai')->create();
        Pesanan::factory()->for($saya, 'pelanggan')->status('dibatalkan')->create();
        Pesanan::factory()->count(3)->create();

        $this->actingAs($saya, 'pelanggan')
            ->get(route('pelanggan.dashboard'))
            ->assertOk()
            ->assertSee('Ayu Lestari')
            ->assertViewHas('totalPesanan', 4)
            ->assertViewHas('pesananBerjalan', 2)
            ->assertViewHas('pesananSelesai', 1);
    }

    public function test_dashboard_works_for_pelanggan_without_pesanan(): void
    {
        $this->actingAs(Pelanggan::factory()->create(), 'pelanggan')
            ->get(route('pelanggan.dashboard'))
            ->assertOk()
            ->assertSee('Anda belum punya pesanan.');
    }
}
