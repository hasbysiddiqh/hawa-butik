<?php

namespace Tests\Feature\Admin;

use App\Models\Admin;
use App\Models\Pelanggan;
use App\Models\Penyewaan;
use App\Models\Pesanan;
use App\Models\Produk;
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
            ->get(route('admin.pesanan.index', ['status_kustom' => 'selesai']))
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

        $this->patch(route('admin.pesanan.status', $pesanan), ['status' => 'siap_diambil'])
            ->assertSessionHas('success');
        $this->assertSame('siap_diambil', $pesanan->fresh()->status);

        $this->patch(route('admin.pesanan.status', $pesanan), ['status' => 'selesai'])
            ->assertSessionHas('success');
        $this->assertSame('selesai', $pesanan->fresh()->status);
    }

    public function test_admin_can_cancel_baru_and_dikerjakan_pesanan(): void
    {
        $this->actingAs($this->admin(), 'admin');

        foreach (['baru', 'diproses', 'siap_diambil'] as $status) {
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

            foreach (['baru', 'diproses', 'siap_diambil', 'selesai', 'dibatalkan'] as $tujuan) {
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

        $this->get(route('admin.pesanan.show', Pesanan::factory()->status('diproses')->create()))
            ->assertSee('value="siap_diambil"', false)
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

    // ----- dua tabel: Rental dan Pesan (Kustom)

    public function test_index_separates_rental_and_kustom_orders_into_two_tables(): void
    {
        Pesanan::factory()->rental()->create(['nama' => 'Penyewa Satu']);
        Pesanan::factory()->create(['nama' => 'Pemesan Jahit']);

        $this->actingAs($this->admin(), 'admin')
            ->get(route('admin.pesanan.index'))
            ->assertOk()
            ->assertViewHas('rentals', fn ($daftar) => $daftar->pluck('nama')->all() === ['Penyewa Satu'])
            ->assertViewHas('kustoms', fn ($daftar) => $daftar->pluck('nama')->all() === ['Pemesan Jahit'])
            ->assertSee('Penyewa Satu')
            ->assertSee('Pemesan Jahit');
    }

    public function test_rental_and_kustom_tables_are_filtered_by_their_own_status(): void
    {
        Pesanan::factory()->rental()->status('dipinjam')->create(['nama' => 'Rental Dipinjam']);
        Pesanan::factory()->rental()->status('dikembalikan')->create(['nama' => 'Rental Kembali']);
        Pesanan::factory()->status('baru')->create(['nama' => 'Kustom Baru']);

        $this->actingAs($this->admin(), 'admin')
            ->get(route('admin.pesanan.index', ['status_rental' => 'dikembalikan']))
            ->assertSee('Rental Kembali')
            ->assertDontSee('Rental Dipinjam')
            ->assertSee('Kustom Baru');
    }

    public function test_status_not_belonging_to_the_table_is_ignored_in_filter(): void
    {
        Pesanan::factory()->rental()->create(['nama' => 'Rental Dipinjam']);

        $this->actingAs($this->admin(), 'admin')
            ->get(route('admin.pesanan.index', ['status_rental' => 'siap_diambil']))
            ->assertSee('Rental Dipinjam');
    }

    public function test_rental_status_moves_through_its_own_flow_and_syncs_penyewaan(): void
    {
        $pesanan = Pesanan::factory()->rental()->create();
        $penyewaan = Penyewaan::create([
            'id_pesanan' => $pesanan->id,
            'tgl_mulai' => now()->toDateString(),
            'tgl_kembali' => now()->addDays(2)->toDateString(),
            'status_sewa' => 'dipinjam',
        ]);
        $this->actingAs($this->admin(), 'admin');

        $this->patch(route('admin.pesanan.status', $pesanan), ['status' => 'belum_dikembalikan'])
            ->assertSessionHas('success');
        $this->assertSame('belum_dikembalikan', $pesanan->fresh()->status);
        $this->assertSame('belum_dikembalikan', $penyewaan->fresh()->status_sewa);
        $this->assertNull($penyewaan->fresh()->tgl_selesai);

        $this->patch(route('admin.pesanan.status', $pesanan), ['status' => 'dikembalikan'])
            ->assertSessionHas('success');
        $this->assertSame('dikembalikan', $pesanan->fresh()->status);
        $this->assertSame('dikembalikan', $penyewaan->fresh()->status_sewa);
        $this->assertSame(now()->toDateString(), $penyewaan->fresh()->tgl_selesai->toDateString());
    }

    public function test_rental_can_be_returned_directly_from_dipinjam(): void
    {
        $pesanan = Pesanan::factory()->rental()->create();

        $this->actingAs($this->admin(), 'admin')
            ->patch(route('admin.pesanan.status', $pesanan), ['status' => 'dikembalikan'])
            ->assertSessionHas('success');

        $this->assertSame('dikembalikan', $pesanan->fresh()->status);
    }

    public function test_rental_rejects_kustom_statuses_and_dikembalikan_is_final(): void
    {
        $this->actingAs($this->admin(), 'admin');

        $rental = Pesanan::factory()->rental()->create();
        $this->patch(route('admin.pesanan.status', $rental), ['status' => 'siap_diambil'])->assertSessionHas('error');
        $this->patch(route('admin.pesanan.status', $rental), ['status' => 'dibatalkan'])->assertSessionHas('error');
        $this->assertSame('dipinjam', $rental->fresh()->status);

        $selesai = Pesanan::factory()->rental()->status('dikembalikan')->create();
        foreach (['dipinjam', 'belum_dikembalikan', 'dikembalikan'] as $tujuan) {
            $this->patch(route('admin.pesanan.status', $selesai), ['status' => $tujuan])->assertSessionHas('error');
        }
        $this->assertSame('dikembalikan', $selesai->fresh()->status);
    }

    public function test_kustom_rejects_rental_statuses(): void
    {
        $pesanan = Pesanan::factory()->create();

        $this->actingAs($this->admin(), 'admin')
            ->patch(route('admin.pesanan.status', $pesanan), ['status' => 'dipinjam'])
            ->assertSessionHas('error');

        $this->assertSame('baru', $pesanan->fresh()->status);
    }

    public function test_show_offers_rental_next_statuses_only(): void
    {
        $this->actingAs($this->admin(), 'admin')
            ->get(route('admin.pesanan.show', Pesanan::factory()->rental()->create()))
            ->assertSee('value="belum_dikembalikan"', false)
            ->assertSee('value="dikembalikan"', false)
            ->assertDontSee('value="dibatalkan"', false);
    }

    // ----- stok otomatis

    private function rentalDenganStok(int $stok, int $jumlah, bool $stokDipotong): Pesanan
    {
        $produk = Produk::factory()->create(['stok' => $stok]);

        return Pesanan::factory()->rental()->create([
            'id_produk' => $produk->id_produk,
            'jumlah' => $jumlah,
            'stok_dipotong' => $stokDipotong,
        ]);
    }

    public function test_returning_a_rental_gives_the_stock_back(): void
    {
        $pesanan = $this->rentalDenganStok(stok: 1, jumlah: 2, stokDipotong: true);

        $this->actingAs($this->admin(), 'admin')
            ->patch(route('admin.pesanan.status', $pesanan), ['status' => 'dikembalikan'])
            ->assertSessionHas('success');

        $this->assertSame(3, $pesanan->produk->fresh()->stok);
        $this->assertFalse($pesanan->fresh()->stok_dipotong);
    }

    public function test_stock_stays_reduced_while_the_rental_is_not_yet_returned(): void
    {
        $pesanan = $this->rentalDenganStok(stok: 1, jumlah: 2, stokDipotong: true);
        $this->actingAs($this->admin(), 'admin');

        $this->patch(route('admin.pesanan.status', $pesanan), ['status' => 'belum_dikembalikan'])
            ->assertSessionHas('success');
        $this->assertSame(1, $pesanan->produk->fresh()->stok);

        $this->patch(route('admin.pesanan.status', $pesanan), ['status' => 'dikembalikan'])
            ->assertSessionHas('success');
        $this->assertSame(3, $pesanan->produk->fresh()->stok);
    }

    public function test_old_rental_that_never_reduced_stock_does_not_add_stock_when_returned(): void
    {
        $pesanan = $this->rentalDenganStok(stok: 1, jumlah: 2, stokDipotong: false);

        $this->actingAs($this->admin(), 'admin')
            ->patch(route('admin.pesanan.status', $pesanan), ['status' => 'dikembalikan'])
            ->assertSessionHas('success');

        $this->assertSame(1, $pesanan->produk->fresh()->stok);
    }

    public function test_kustom_status_changes_never_touch_product_stock(): void
    {
        $produk = Produk::factory()->create(['stok' => 4]);
        $pesanan = Pesanan::factory()->create(['id_produk' => $produk->id_produk, 'jumlah' => 2]);

        $this->actingAs($this->admin(), 'admin')
            ->patch(route('admin.pesanan.status', $pesanan), ['status' => 'diproses']);

        $this->assertSame(4, $produk->fresh()->stok);
    }
}
