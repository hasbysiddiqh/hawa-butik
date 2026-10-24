<?php

namespace Tests\Feature\Admin;

use App\Models\Admin;
use App\Models\Pelanggan;
use App\Models\Pesanan;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PesananControllerTest extends TestCase
{
    use RefreshDatabase;

    private function admin(): Admin
    {
        return Admin::factory()->create();
    }

    // ----- akses

    public function test_guest_is_redirected_to_login(): void
    {
        $pesanan = Pesanan::factory()->create();

        $this->get(route('admin.pesanan.index'))->assertRedirect(route('login'));
        $this->get(route('admin.pesanan.show', $pesanan))->assertRedirect(route('login'));
        $this->patch(route('admin.pesanan.status', $pesanan), ['status' => 'diproses'])->assertRedirect(route('login'));

        $this->assertSame('baru', $pesanan->fresh()->status);
    }

    public function test_pelanggan_cannot_change_status_through_admin_route(): void
    {
        $saya = Pelanggan::factory()->create();
        $pesanan = Pesanan::factory()->for($saya, 'pelanggan')->create();

        $this->actingAs($saya, 'pelanggan')
            ->patch(route('admin.pesanan.status', $pesanan), ['status' => 'selesai'])
            ->assertRedirect(route('login'));

        $this->assertSame('baru', $pesanan->fresh()->status);
    }

    // ----- daftar & detail

    public function test_index_lists_pesanan_from_every_pelanggan(): void
    {
        Pesanan::factory()->create(['nama' => 'Ayu Pertama']);
        Pesanan::factory()->create(['nama' => 'Budi Kedua']);

        $this->actingAs($this->admin(), 'admin')
            ->get(route('admin.pesanan.index'))
            ->assertOk()
            ->assertSee('Ayu Pertama')
            ->assertSee('Budi Kedua');
    }

    public function test_index_includes_old_pesanan_without_account(): void
    {
        Pesanan::factory()->create(['id_pelanggan' => null, 'nama' => 'Pesanan Lama']);

        $this->actingAs($this->admin(), 'admin')
            ->get(route('admin.pesanan.index'))
            ->assertSee('Pesanan Lama');
    }

    public function test_index_can_be_filtered_by_status(): void
    {
        Pesanan::factory()->status('baru')->create(['nama' => 'Pesanan Baru']);
        Pesanan::factory()->status('selesai')->create(['nama' => 'Pesanan Selesai']);

        $this->actingAs($this->admin(), 'admin')
            ->get(route('admin.pesanan.index', ['status' => 'selesai']))
            ->assertSee('Pesanan Selesai')
            ->assertDontSee('Pesanan Baru');
    }

    public function test_index_can_be_searched(): void
    {
        Pesanan::factory()->create(['nama' => 'Siti Aminah']);
        Pesanan::factory()->create(['nama' => 'Budi Santoso']);

        $this->actingAs($this->admin(), 'admin')
            ->get(route('admin.pesanan.index', ['cari' => 'Siti']))
            ->assertSee('Siti Aminah')
            ->assertDontSee('Budi Santoso');
    }

    public function test_show_displays_pesanan_and_customer_account(): void
    {
        $pelanggan = Pelanggan::factory()->create(['email' => 'ayu@example.com']);
        $pesanan = Pesanan::factory()->for($pelanggan, 'pelanggan')->create(['warna' => 'Merah marun']);

        $this->actingAs($this->admin(), 'admin')
            ->get(route('admin.pesanan.show', $pesanan))
            ->assertOk()
            ->assertSee('Merah marun')
            ->assertSee('ayu@example.com');
    }

    // ----- ganti status

    public function test_status_moves_through_the_allowed_flow(): void
    {
        $pesanan = Pesanan::factory()->create();
        $this->actingAs($this->admin(), 'admin');

        $this->patch(route('admin.pesanan.status', $pesanan), ['status' => 'diproses'])
            ->assertRedirect(route('admin.pesanan.show', $pesanan))
            ->assertSessionHas('success');
        $this->assertSame('diproses', $pesanan->fresh()->status);

        $this->patch(route('admin.pesanan.status', $pesanan), ['status' => 'selesai'])
            ->assertSessionHas('success');
        $this->assertSame('selesai', $pesanan->fresh()->status);
    }

    public function test_admin_can_cancel_baru_and_dikerjakan_pesanan(): void
    {
        $this->actingAs($this->admin(), 'admin');

        foreach (['baru', 'diproses'] as $status) {
            $pesanan = Pesanan::factory()->status($status)->create();

            $this->patch(route('admin.pesanan.status', $pesanan), ['status' => 'dibatalkan'])
                ->assertSessionHas('success');

            $this->assertSame('dibatalkan', $pesanan->fresh()->status);
        }
    }

    public function test_skipping_a_step_is_rejected(): void
    {
        $pesanan = Pesanan::factory()->create();

        $this->actingAs($this->admin(), 'admin')
            ->patch(route('admin.pesanan.status', $pesanan), ['status' => 'selesai'])
            ->assertSessionHas('error');

        $this->assertSame('baru', $pesanan->fresh()->status);
    }

    public function test_going_backwards_is_rejected(): void
    {
        $pesanan = Pesanan::factory()->status('diproses')->create();

        $this->actingAs($this->admin(), 'admin')
            ->patch(route('admin.pesanan.status', $pesanan), ['status' => 'baru'])
            ->assertSessionHas('error');

        $this->assertSame('diproses', $pesanan->fresh()->status);
    }

    public function test_selesai_and_dibatalkan_are_final(): void
    {
        $this->actingAs($this->admin(), 'admin');

        foreach (['selesai', 'dibatalkan'] as $status) {
            $pesanan = Pesanan::factory()->status($status)->create();

            foreach (['baru', 'diproses', 'selesai', 'dibatalkan'] as $tujuan) {
                $this->patch(route('admin.pesanan.status', $pesanan), ['status' => $tujuan])
                    ->assertSessionHas('error');
            }

            $this->assertSame($status, $pesanan->fresh()->status);
        }
    }

    public function test_unknown_status_value_is_rejected(): void
    {
        $pesanan = Pesanan::factory()->create();

        $this->actingAs($this->admin(), 'admin')
            ->patch(route('admin.pesanan.status', $pesanan), ['status' => 'terbang'])
            ->assertSessionHasErrors('status');

        $this->assertSame('baru', $pesanan->fresh()->status);
    }

    public function test_missing_status_is_rejected(): void
    {
        $this->actingAs($this->admin(), 'admin')
            ->patch(route('admin.pesanan.status', Pesanan::factory()->create()), [])
            ->assertSessionHasErrors('status');
    }

    public function test_show_offers_only_the_allowed_next_statuses(): void
    {
        $this->actingAs($this->admin(), 'admin');

        $this->get(route('admin.pesanan.show', Pesanan::factory()->status('baru')->create()))
            ->assertSee('value="diproses"', false)
            ->assertSee('value="dibatalkan"', false)
            ->assertDontSee('value="selesai"', false);

        $this->get(route('admin.pesanan.show', Pesanan::factory()->status('selesai')->create()))
            ->assertSee('sudah final')
            ->assertDontSee('SIMPAN STATUS');
    }

    public function test_pelanggan_can_no_longer_edit_after_admin_starts_working_on_it(): void
    {
        $saya = Pelanggan::factory()->create();
        $pesanan = Pesanan::factory()->for($saya, 'pelanggan')->create();

        $this->actingAs($this->admin(), 'admin')
            ->patch(route('admin.pesanan.status', $pesanan), ['status' => 'diproses']);

        $this->actingAs($saya, 'pelanggan')
            ->patch(route('pelanggan.pesanan.batal', $pesanan->id))
            ->assertSessionHas('error');

        $this->assertSame('diproses', $pesanan->fresh()->status);
    }
}
