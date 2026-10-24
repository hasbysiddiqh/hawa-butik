<?php

namespace Tests\Feature\Pelanggan;

use App\Models\Pelanggan;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class ProfilControllerTest extends TestCase
{
    use RefreshDatabase;

    /** @return array<string, string> */
    private function dataValid(array $timpa = []): array
    {
        return array_merge([
            'nama_pelanggan' => 'Ayu Baru',
            'email' => 'ayu.baru@example.com',
            'no_telp' => '081200002222',
            'alamat' => 'Jl. Anggrek 9',
        ], $timpa);
    }

    public function test_guest_is_redirected_to_login(): void
    {
        $this->get(route('pelanggan.profil.edit'))->assertRedirect(route('login'));
        $this->put(route('pelanggan.profil.update'), $this->dataValid())->assertRedirect(route('login'));
    }

    public function test_edit_page_shows_current_data(): void
    {
        $saya = Pelanggan::factory()->create(['nama_pelanggan' => 'Ayu Lestari']);

        $this->actingAs($saya, 'pelanggan')
            ->get(route('pelanggan.profil.edit'))
            ->assertOk()
            ->assertSee('Ayu Lestari');
    }

    public function test_valid_update_changes_profile(): void
    {
        $saya = Pelanggan::factory()->create();

        $this->actingAs($saya, 'pelanggan')
            ->put(route('pelanggan.profil.update'), $this->dataValid())
            ->assertRedirect(route('pelanggan.profil.edit'))
            ->assertSessionHas('success');

        $saya->refresh();
        $this->assertSame('Ayu Baru', $saya->nama_pelanggan);
        $this->assertSame('ayu.baru@example.com', $saya->email);
        $this->assertSame('Jl. Anggrek 9', $saya->alamat);
    }

    public function test_keeping_own_email_is_allowed(): void
    {
        $saya = Pelanggan::factory()->create(['email' => 'ayu@example.com']);

        $this->actingAs($saya, 'pelanggan')
            ->put(route('pelanggan.profil.update'), $this->dataValid(['email' => 'ayu@example.com']))
            ->assertSessionHasNoErrors();
    }

    public function test_email_used_by_another_pelanggan_is_rejected(): void
    {
        Pelanggan::factory()->create(['email' => 'dipakai@example.com']);
        $saya = Pelanggan::factory()->create(['email' => 'ayu@example.com']);

        $this->actingAs($saya, 'pelanggan')
            ->put(route('pelanggan.profil.update'), $this->dataValid(['email' => 'dipakai@example.com']))
            ->assertSessionHasErrors('email');

        $this->assertSame('ayu@example.com', $saya->fresh()->email);
    }

    public function test_update_validates_required_fields_and_phone(): void
    {
        $this->actingAs(Pelanggan::factory()->create(), 'pelanggan')
            ->put(route('pelanggan.profil.update'), $this->dataValid(['nama_pelanggan' => '', 'no_telp' => 'abc']))
            ->assertSessionHasErrors(['nama_pelanggan', 'no_telp']);
    }

    public function test_status_akun_cannot_be_changed_from_profile(): void
    {
        $saya = Pelanggan::factory()->create();

        $this->actingAs($saya, 'pelanggan')
            ->put(route('pelanggan.profil.update'), $this->dataValid(['status_akun' => 'nonaktif']));

        $this->assertSame('aktif', $saya->fresh()->status_akun);
    }

    public function test_password_is_changed_with_correct_current_password(): void
    {
        $saya = Pelanggan::factory()->create();

        $this->actingAs($saya, 'pelanggan')
            ->put(route('pelanggan.profil.password'), [
                'password_lama' => 'password',
                'password' => 'sandi-baru-123',
                'password_confirmation' => 'sandi-baru-123',
            ])
            ->assertRedirect(route('pelanggan.profil.edit'))
            ->assertSessionHas('success');

        $this->assertTrue(Hash::check('sandi-baru-123', $saya->fresh()->password));
    }

    public function test_password_change_is_rejected_with_wrong_current_password(): void
    {
        $saya = Pelanggan::factory()->create();

        $this->actingAs($saya, 'pelanggan')
            ->put(route('pelanggan.profil.password'), [
                'password_lama' => 'salah-total',
                'password' => 'sandi-baru-123',
                'password_confirmation' => 'sandi-baru-123',
            ])
            ->assertSessionHasErrors('password_lama');

        $this->assertTrue(Hash::check('password', $saya->fresh()->password));
    }

    public function test_new_password_must_be_confirmed_and_long_enough(): void
    {
        $saya = Pelanggan::factory()->create();
        $this->actingAs($saya, 'pelanggan');

        $this->put(route('pelanggan.profil.password'), [
            'password_lama' => 'password',
            'password' => 'sandi-baru-123',
            'password_confirmation' => 'beda',
        ])->assertSessionHasErrors('password');

        $this->put(route('pelanggan.profil.password'), [
            'password_lama' => 'password',
            'password' => 'pendek',
            'password_confirmation' => 'pendek',
        ])->assertSessionHasErrors('password');

        $this->assertTrue(Hash::check('password', $saya->fresh()->password));
    }

    public function test_profil_page_has_the_logout_button(): void
    {
        $this->actingAs(Pelanggan::factory()->create(), 'pelanggan')
            ->get(route('pelanggan.profil.edit'))
            ->assertOk()
            ->assertSee(route('logout'), false)
            ->assertSee('KELUAR');
    }
}
