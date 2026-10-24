<?php

namespace Tests\Feature\Pelanggan;

use App\Models\Admin;
use App\Models\Pelanggan;
use App\Models\Pesanan;
use App\Models\Produk;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RentalControllerTest extends TestCase
{
    use RefreshDatabase;

    /** @return array<string, mixed> */
    private function dataValid(Produk $produk, array $timpa = []): array
    {
        return array_merge([
            'id_produk' => $produk->id_produk,
            'nama' => 'Ayu Lestari',
            'whatsapp' => '081234567890',
            'alamat_pengiriman' => 'Jl. Contoh No. 1',
            'ukuran' => 'M',
            'jumlah' => 1,
            'tanggal_sewa' => now()->addDays(3)->toDateString(),
            'tanggal_kembali' => now()->addDays(5)->toDateString(),
            'detail_pesanan' => 'Untuk wisuda',
        ], $timpa);
    }

    public function test_guest_is_redirected_to_login(): void
    {
        $this->get(route('pelanggan.pemesanan'))->assertRedirect(route('login'));
        $this->get(route('pelanggan.rental.create'))->assertRedirect(route('login'));
        $this->post(route('pelanggan.rental.store'))->assertRedirect(route('login'));
    }

    public function test_admin_cannot_use_rental(): void
    {
        $this->actingAs(Admin::factory()->create(), 'admin')
            ->get(route('pelanggan.rental.create'))
            ->assertRedirect(route('login'));
    }

    public function test_pemesanan_page_offers_rental_and_pesan(): void
    {
        $this->actingAs(Pelanggan::factory()->create(), 'pelanggan')
            ->get(route('pelanggan.pemesanan'))
            ->assertOk()
            ->assertSee(route('pelanggan.rental.create'), false)
            ->assertSee(route('pelanggan.pesanan.create'), false);
    }

    public function test_rental_form_preselects_produk_from_collection(): void
    {
        $produk = Produk::factory()->create(['nama_produk' => 'Kebaya Pilihan']);

        $this->actingAs(Pelanggan::factory()->create(), 'pelanggan')
            ->get(route('pelanggan.rental.create', ['produk' => $produk->id_produk]))
            ->assertOk()
            ->assertSee('Kebaya Pilihan')
            ->assertViewHas('idTerpilih', $produk->id_produk);
    }

    public function test_pelanggan_can_create_a_rental(): void
    {
        $saya = Pelanggan::factory()->create();
        $produk = Produk::factory()->create(['stok' => 3]);

        $this->actingAs($saya, 'pelanggan')
            ->post(route('pelanggan.rental.store'), $this->dataValid($produk))
            ->assertRedirect();

        $this->assertDatabaseHas('pesanans', [
            'id_pelanggan' => $saya->id_pelanggan,
            'tipe' => 'rental',
            'id_produk' => $produk->id_produk,
            'jenis_pakaian' => $produk->kategori->nama_kategori,
            'status' => 'baru',
        ]);
    }

    public function test_rental_requires_return_date_not_before_start_date(): void
    {
        $produk = Produk::factory()->create();

        $this->actingAs(Pelanggan::factory()->create(), 'pelanggan')
            ->post(route('pelanggan.rental.store'), $this->dataValid($produk, [
                'tanggal_sewa' => now()->addDays(5)->toDateString(),
                'tanggal_kembali' => now()->addDays(3)->toDateString(),
            ]))
            ->assertSessionHasErrors('tanggal_kembali');

        $this->assertDatabaseCount('pesanans', 0);
    }

    public function test_rental_cannot_start_in_the_past(): void
    {
        $produk = Produk::factory()->create();

        $this->actingAs(Pelanggan::factory()->create(), 'pelanggan')
            ->post(route('pelanggan.rental.store'), $this->dataValid($produk, [
                'tanggal_sewa' => now()->subDay()->toDateString(),
            ]))
            ->assertSessionHasErrors('tanggal_sewa');
    }

    public function test_rental_cannot_exceed_stok(): void
    {
        $produk = Produk::factory()->create(['stok' => 1]);

        $this->actingAs(Pelanggan::factory()->create(), 'pelanggan')
            ->post(route('pelanggan.rental.store'), $this->dataValid($produk, ['jumlah' => 2]))
            ->assertSessionHasErrors('jumlah');

        $this->assertDatabaseCount('pesanans', 0);
    }

    public function test_nonaktif_produk_cannot_be_rented(): void
    {
        $produk = Produk::factory()->nonaktif()->create();

        $this->actingAs(Pelanggan::factory()->create(), 'pelanggan')
            ->post(route('pelanggan.rental.store'), $this->dataValid($produk))
            ->assertSessionHasErrors('id_produk');
    }

    public function test_rental_pesanan_cannot_be_edited_but_can_be_cancelled(): void
    {
        $saya = Pelanggan::factory()->create();
        $produk = Produk::factory()->create();
        $pesanan = Pesanan::factory()->for($saya, 'pelanggan')->create([
            'tipe' => 'rental',
            'id_produk' => $produk->id_produk,
            'tanggal_sewa' => now()->addDay(),
            'tanggal_kembali' => now()->addDays(2),
        ]);

        $this->actingAs($saya, 'pelanggan')
            ->get(route('pelanggan.pesanan.edit', $pesanan->id))
            ->assertRedirect(route('pelanggan.pesanan.show', $pesanan->id));

        $this->actingAs($saya, 'pelanggan')
            ->patch(route('pelanggan.pesanan.batal', $pesanan->id))
            ->assertRedirect(route('pelanggan.pesanan.show', $pesanan->id));

        $this->assertSame('dibatalkan', $pesanan->fresh()->status);
    }
}
