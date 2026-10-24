<?php

namespace Tests\Feature\Pelanggan;

use App\Models\Admin;
use App\Models\Pelanggan;
use App\Models\Pesanan;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PesananControllerTest extends TestCase
{
    use RefreshDatabase;

    /** @return array<string, mixed> */
    private function dataValid(array $timpa = []): array
    {
        return array_merge([
            'nama' => 'Ayu Lestari',
            'whatsapp' => '081234567890',
            'alamat_pengiriman' => 'Jl. Contoh No. 1',
            'jenis_pakaian' => 'Kebaya',
            'model_lengan' => 'Lengan panjang',
            'ukuran' => 'M',
            'bahan' => 'Brokat',
            'warna' => 'Cokelat susu',
            'jumlah' => 2,
            'detail_pesanan' => 'Untuk wisuda',
        ], $timpa);
    }

    private function pelanggan(): Pelanggan
    {
        return Pelanggan::factory()->create();
    }

    // ----- akses

    public function test_guest_is_redirected_to_login_for_every_pesanan_page(): void
    {
        $pesanan = Pesanan::factory()->create();

        $this->get(route('pelanggan.pesanan.index'))->assertRedirect(route('login'));
        $this->get(route('pelanggan.pesanan.create'))->assertRedirect(route('login'));
        $this->post(route('pelanggan.pesanan.store'), $this->dataValid())->assertRedirect(route('login'));
        $this->get(route('pelanggan.pesanan.show', $pesanan->id))->assertRedirect(route('login'));

        $this->assertDatabaseCount('pesanans', 1);
    }

    public function test_admin_cannot_use_pelanggan_pesanan_pages(): void
    {
        $this->actingAs(Admin::factory()->create(), 'admin')
            ->get(route('pelanggan.pesanan.create'))
            ->assertRedirect(route('login'));
    }

    public function test_guest_visiting_create_page_is_sent_back_to_it_after_login(): void
    {
        $pelanggan = Pelanggan::factory()->create(['email' => 'ayu@example.com']);

        $this->get(route('pelanggan.pesanan.create'))->assertRedirect(route('login'));

        $this->post(route('login.store'), [
            'peran' => 'pelanggan',
            'email' => 'ayu@example.com',
            'password' => 'password',
        ])->assertRedirect(route('pelanggan.pesanan.create'));
    }

    // ----- daftar & buat

    public function test_index_lists_only_own_pesanan(): void
    {
        $saya = $this->pelanggan();
        Pesanan::factory()->for($saya, 'pelanggan')->create(['jenis_pakaian' => 'Gamis']);
        Pesanan::factory()->create(['jenis_pakaian' => 'Seragam']);

        $this->actingAs($saya, 'pelanggan')
            ->get(route('pelanggan.pesanan.index'))
            ->assertOk()
            ->assertSee('Gamis')
            ->assertDontSee('Seragam');
    }

    public function test_create_form_is_prefilled_from_profile(): void
    {
        $saya = Pelanggan::factory()->create([
            'nama_pelanggan' => 'Ayu Lestari',
            'no_telp' => '081200001111',
            'alamat' => 'Jl. Melati 5',
        ]);

        $this->actingAs($saya, 'pelanggan')
            ->get(route('pelanggan.pesanan.create'))
            ->assertOk()
            ->assertSee('Ayu Lestari')
            ->assertSee('081200001111')
            ->assertSee('Jl. Melati 5');
    }

    public function test_valid_order_is_saved_for_the_logged_in_pelanggan_with_status_baru(): void
    {
        $saya = $this->pelanggan();

        $this->actingAs($saya, 'pelanggan')
            ->post(route('pelanggan.pesanan.store'), $this->dataValid())
            ->assertRedirect()
            ->assertSessionHas('success');

        $pesanan = Pesanan::firstOrFail();
        $this->assertSame($saya->id_pelanggan, $pesanan->id_pelanggan);
        $this->assertSame('baru', $pesanan->status);
        $this->assertSame('Kebaya', $pesanan->jenis_pakaian);
        $this->assertSame('Jl. Contoh No. 1', $pesanan->alamat_pengiriman);
    }

    public function test_status_and_owner_cannot_be_set_from_the_form(): void
    {
        $saya = $this->pelanggan();
        $orangLain = $this->pelanggan();

        $this->actingAs($saya, 'pelanggan')
            ->post(route('pelanggan.pesanan.store'), $this->dataValid([
                'status' => 'selesai',
                'id_pelanggan' => $orangLain->id_pelanggan,
            ]));

        $pesanan = Pesanan::firstOrFail();
        $this->assertSame('baru', $pesanan->status);
        $this->assertSame($saya->id_pelanggan, $pesanan->id_pelanggan);
    }

    public function test_required_fields_are_validated(): void
    {
        $this->actingAs($this->pelanggan(), 'pelanggan')
            ->post(route('pelanggan.pesanan.store'), [])
            ->assertSessionHasErrors(['nama', 'whatsapp', 'jenis_pakaian', 'model_lengan', 'ukuran', 'bahan', 'warna', 'jumlah']);

        $this->assertDatabaseCount('pesanans', 0);
    }

    public function test_lainnya_choice_saves_the_typed_text(): void
    {
        $this->actingAs($this->pelanggan(), 'pelanggan')
            ->post(route('pelanggan.pesanan.store'), $this->dataValid([
                'jenis_pakaian' => 'lainnya',
                'jenis_pakaian_lain' => 'Jubah',
            ]));

        $this->assertSame('Jubah', Pesanan::firstOrFail()->jenis_pakaian);
    }

    public function test_lainnya_choice_without_typed_text_is_rejected(): void
    {
        $this->actingAs($this->pelanggan(), 'pelanggan')
            ->post(route('pelanggan.pesanan.store'), $this->dataValid(['bahan' => 'lainnya']))
            ->assertSessionHasErrors('bahan_lain');
    }

    // ----- detail

    public function test_show_displays_own_pesanan(): void
    {
        $saya = $this->pelanggan();
        $pesanan = Pesanan::factory()->for($saya, 'pelanggan')->create(['warna' => 'Merah marun']);

        $this->actingAs($saya, 'pelanggan')
            ->get(route('pelanggan.pesanan.show', $pesanan->id))
            ->assertOk()
            ->assertSee('Merah marun');
    }

    public function test_pesanan_of_another_pelanggan_returns_404_everywhere(): void
    {
        $milikOrangLain = Pesanan::factory()->create();
        $this->actingAs($this->pelanggan(), 'pelanggan');

        $this->get(route('pelanggan.pesanan.show', $milikOrangLain->id))->assertNotFound();
        $this->get(route('pelanggan.pesanan.edit', $milikOrangLain->id))->assertNotFound();
        $this->put(route('pelanggan.pesanan.update', $milikOrangLain->id), $this->dataValid())->assertNotFound();
        $this->patch(route('pelanggan.pesanan.batal', $milikOrangLain->id))->assertNotFound();

        $this->assertSame('baru', $milikOrangLain->fresh()->status);
    }

    // ----- ubah

    public function test_baru_pesanan_can_be_edited(): void
    {
        $saya = $this->pelanggan();
        $pesanan = Pesanan::factory()->for($saya, 'pelanggan')->create(['warna' => 'Hijau']);

        $this->actingAs($saya, 'pelanggan')
            ->get(route('pelanggan.pesanan.edit', $pesanan->id))
            ->assertOk();

        $this->put(route('pelanggan.pesanan.update', $pesanan->id), $this->dataValid(['warna' => 'Biru laut']))
            ->assertRedirect(route('pelanggan.pesanan.show', $pesanan->id))
            ->assertSessionHas('success');

        $this->assertSame('Biru laut', $pesanan->fresh()->warna);
    }

    public function test_update_cannot_change_status(): void
    {
        $saya = $this->pelanggan();
        $pesanan = Pesanan::factory()->for($saya, 'pelanggan')->create();

        $this->actingAs($saya, 'pelanggan')
            ->put(route('pelanggan.pesanan.update', $pesanan->id), $this->dataValid(['status' => 'selesai']));

        $this->assertSame('baru', $pesanan->fresh()->status);
    }

    public function test_dikerjakan_pesanan_cannot_be_edited_or_updated(): void
    {
        $saya = $this->pelanggan();
        $pesanan = Pesanan::factory()->for($saya, 'pelanggan')->status('diproses')->create(['warna' => 'Hijau']);

        $this->actingAs($saya, 'pelanggan')
            ->get(route('pelanggan.pesanan.edit', $pesanan->id))
            ->assertRedirect(route('pelanggan.pesanan.show', $pesanan->id))
            ->assertSessionHas('error');

        $this->put(route('pelanggan.pesanan.update', $pesanan->id), $this->dataValid(['warna' => 'Biru laut']))
            ->assertRedirect(route('pelanggan.pesanan.show', $pesanan->id))
            ->assertSessionHas('error');

        $this->assertSame('Hijau', $pesanan->fresh()->warna);
    }

    public function test_selesai_and_dibatalkan_pesanan_cannot_be_updated(): void
    {
        $saya = $this->pelanggan();
        $this->actingAs($saya, 'pelanggan');

        foreach (['selesai', 'dibatalkan'] as $status) {
            $pesanan = Pesanan::factory()->for($saya, 'pelanggan')->status($status)->create(['warna' => 'Hijau']);

            $this->put(route('pelanggan.pesanan.update', $pesanan->id), $this->dataValid(['warna' => 'Biru laut']))
                ->assertSessionHas('error');

            $this->assertSame('Hijau', $pesanan->fresh()->warna);
        }
    }

    // ----- batal

    public function test_baru_pesanan_can_be_cancelled_and_stays_in_history(): void
    {
        $saya = $this->pelanggan();
        $pesanan = Pesanan::factory()->for($saya, 'pelanggan')->create();

        $this->actingAs($saya, 'pelanggan')
            ->patch(route('pelanggan.pesanan.batal', $pesanan->id))
            ->assertRedirect(route('pelanggan.pesanan.show', $pesanan->id))
            ->assertSessionHas('success');

        $this->assertSame('dibatalkan', $pesanan->fresh()->status);
        $this->assertDatabaseCount('pesanans', 1);
    }

    public function test_dikerjakan_or_selesai_pesanan_cannot_be_cancelled(): void
    {
        $saya = $this->pelanggan();
        $this->actingAs($saya, 'pelanggan');

        foreach (['diproses', 'selesai'] as $status) {
            $pesanan = Pesanan::factory()->for($saya, 'pelanggan')->status($status)->create();

            $this->patch(route('pelanggan.pesanan.batal', $pesanan->id))
                ->assertRedirect(route('pelanggan.pesanan.show', $pesanan->id))
                ->assertSessionHas('error');

            $this->assertSame($status, $pesanan->fresh()->status);
        }
    }

    public function test_show_offers_edit_and_cancel_only_while_baru(): void
    {
        $saya = $this->pelanggan();
        $baru = Pesanan::factory()->for($saya, 'pelanggan')->create();
        $dikerjakan = Pesanan::factory()->for($saya, 'pelanggan')->status('diproses')->create();

        $this->actingAs($saya, 'pelanggan')
            ->get(route('pelanggan.pesanan.show', $baru->id))
            ->assertSee('BATALKAN PESANAN')
            ->assertSee('UBAH PESANAN');

        $this->get(route('pelanggan.pesanan.show', $dikerjakan->id))
            ->assertDontSee('BATALKAN PESANAN')
            ->assertDontSee('UBAH PESANAN')
            ->assertSee('Dikerjakan')
            ->assertSee('sudah dikerjakan');
    }
}
