<?php

namespace Tests\Feature\Admin;

use App\Models\Admin;
use App\Models\Pelanggan;
use App\Models\Pembayaran;
use App\Models\Pesanan;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TransaksiControllerTest extends TestCase
{
    use RefreshDatabase;

    private function admin(): Admin
    {
        return Admin::factory()->create();
    }

    private function transaksiKustom(int $total = 0, array $pesanan = []): Pembayaran
    {
        $pesanan = Pesanan::factory()->create(['tipe' => 'pesan', 'total' => $total, ...$pesanan]);

        return $pesanan->pembayaran()->create(['jumlah_bayar' => 0, 'status_bayar' => 'menunggu']);
    }

    private function transaksiRental(int $total = 300000): Pembayaran
    {
        $pesanan = Pesanan::factory()->create(['tipe' => 'rental', 'status' => 'dipinjam', 'total' => $total]);

        return $pesanan->pembayaran()->create(['jumlah_bayar' => 0, 'status_bayar' => 'menunggu']);
    }

    public function test_guest_and_pelanggan_cannot_open_transaksi(): void
    {
        $transaksi = $this->transaksiRental();

        $this->get(route('admin.transaksi.index'))->assertRedirect(route('login'));
        $this->get(route('admin.transaksi.show', $transaksi))->assertRedirect(route('login'));
        $this->patch(route('admin.transaksi.update', $transaksi), ['status_bayar' => 'lunas', 'metode' => 'tunai'])->assertRedirect(route('login'));

        $this->actingAs(Pelanggan::factory()->create(), 'pelanggan')
            ->get(route('admin.transaksi.index'))
            ->assertRedirect(route('login'));

        $this->assertSame('menunggu', $transaksi->fresh()->status_bayar);
    }

    public function test_index_lists_transaksi_with_summary(): void
    {
        $lunas = $this->transaksiRental(300000);
        $lunas->update(['status_bayar' => 'lunas', 'jumlah_bayar' => 300000, 'metode' => 'tunai']);
        $this->transaksiRental(200000);

        $this->actingAs($this->admin(), 'admin')
            ->get(route('admin.transaksi.index'))
            ->assertOk()
            ->assertViewHas('jumlahTransaksi', 2)
            ->assertViewHas('totalTagihan', 500000)
            ->assertViewHas('totalDibayar', 300000)
            ->assertViewHas('sisaTagihan', 200000);
    }

    public function test_index_can_be_filtered_by_status_and_search(): void
    {
        $this->transaksiRental()->pesanan->update(['nama' => 'Ayu Pertama']);
        $lunas = $this->transaksiRental();
        $lunas->pesanan->update(['nama' => 'Budi Kedua']);
        $lunas->update(['status_bayar' => 'lunas', 'jumlah_bayar' => 300000, 'metode' => 'debit']);

        $this->actingAs($this->admin(), 'admin')
            ->get(route('admin.transaksi.index', ['status' => 'lunas']))
            ->assertSee('Budi Kedua')
            ->assertDontSee('Ayu Pertama');

        $this->actingAs($this->admin(), 'admin')
            ->get(route('admin.transaksi.index', ['cari' => 'Ayu']))
            ->assertSee('Ayu Pertama')
            ->assertDontSee('Budi Kedua');
    }

    public function test_show_displays_transaksi_detail(): void
    {
        $transaksi = $this->transaksiRental(450000);

        $this->actingAs($this->admin(), 'admin')
            ->get(route('admin.transaksi.show', $transaksi))
            ->assertOk()
            ->assertSee('Transaksi #'.$transaksi->id_bayar)
            ->assertSee('450.000');
    }

    public function test_marking_lunas_fills_jumlah_bayar_with_total_and_tgl_bayar(): void
    {
        $transaksi = $this->transaksiRental(300000);

        $this->actingAs($this->admin(), 'admin')
            ->patch(route('admin.transaksi.update', $transaksi), ['status_bayar' => 'lunas', 'metode' => 'tunai'])
            ->assertRedirect(route('admin.transaksi.show', $transaksi));

        $transaksi->refresh();
        $this->assertSame('lunas', $transaksi->status_bayar);
        $this->assertSame(300000, (int) $transaksi->jumlah_bayar);
        $this->assertNotNull($transaksi->tgl_bayar);
        $this->assertSame(0, $transaksi->sisaBayar());
    }

    public function test_cicilan_must_be_between_zero_and_total(): void
    {
        $transaksi = $this->transaksiRental(300000);
        $admin = $this->admin();

        $this->actingAs($admin, 'admin')
            ->patch(route('admin.transaksi.update', $transaksi), ['status_bayar' => 'dicicil', 'metode' => 'debit', 'jumlah_bayar' => 300000])
            ->assertSessionHasErrors('jumlah_bayar');

        $this->actingAs($admin, 'admin')
            ->patch(route('admin.transaksi.update', $transaksi), ['status_bayar' => 'dicicil', 'metode' => 'debit', 'jumlah_bayar' => 100000])
            ->assertSessionHasNoErrors();

        $transaksi->refresh();
        $this->assertSame('dicicil', $transaksi->status_bayar);
        $this->assertSame(200000, $transaksi->sisaBayar());
    }

    public function test_admin_sets_total_for_custom_order_before_lunas(): void
    {
        $transaksi = $this->transaksiKustom(0);
        $admin = $this->admin();

        $this->actingAs($admin, 'admin')
            ->patch(route('admin.transaksi.update', $transaksi), ['status_bayar' => 'lunas', 'metode' => 'tunai'])
            ->assertSessionHasErrors('total');

        $this->actingAs($admin, 'admin')
            ->patch(route('admin.transaksi.update', $transaksi), ['status_bayar' => 'lunas', 'metode' => 'tunai', 'total' => 750000])
            ->assertSessionHasNoErrors();

        $this->assertSame(750000, (int) $transaksi->pesanan->fresh()->total);
        $this->assertSame(750000, (int) $transaksi->fresh()->jumlah_bayar);
    }

    public function test_total_of_rental_cannot_be_changed_from_transaksi(): void
    {
        $transaksi = $this->transaksiRental(300000);

        $this->actingAs($this->admin(), 'admin')
            ->patch(route('admin.transaksi.update', $transaksi), ['status_bayar' => 'lunas', 'metode' => 'tunai', 'total' => 1])
            ->assertSessionHasNoErrors();

        $this->assertSame(300000, (int) $transaksi->pesanan->fresh()->total);
    }

    public function test_menunggu_resets_payment(): void
    {
        $transaksi = $this->transaksiRental(300000);
        $transaksi->update(['status_bayar' => 'lunas', 'jumlah_bayar' => 300000, 'metode' => 'tunai', 'tgl_bayar' => now()]);

        $this->actingAs($this->admin(), 'admin')
            ->patch(route('admin.transaksi.update', $transaksi), ['status_bayar' => 'menunggu'])
            ->assertSessionHasNoErrors();

        $transaksi->refresh();
        $this->assertSame(0, (int) $transaksi->jumlah_bayar);
        $this->assertNull($transaksi->tgl_bayar);
    }
}
