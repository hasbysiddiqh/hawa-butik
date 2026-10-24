<?php

namespace Tests\Feature\Auth;

use App\Models\Pelanggan;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class RegisterControllerTest extends TestCase
{
    use RefreshDatabase;

    /** @return array<string, string> */
    private function dataValid(array $timpa = []): array
    {
        return array_merge([
            'nama_pelanggan' => 'Ayu Lestari',
            'email' => 'ayu@example.com',
            'no_telp' => '081234567890',
            'alamat' => 'Jl. Contoh No. 1',
            'password' => 'rahasia-banget',
            'password_confirmation' => 'rahasia-banget',
        ], $timpa);
    }

    public function test_registration_page_renders_for_guest(): void
    {
        $this->get(route('register'))
            ->assertOk()
            ->assertSee('Registrasi Akun');
    }

    public function test_valid_registration_creates_active_pelanggan_signs_in_and_redirects_to_home(): void
    {
        $this->post(route('register.store'), $this->dataValid())
            ->assertRedirect(route('home'))
            ->assertSessionHas('success');

        $pelanggan = Pelanggan::where('email', 'ayu@example.com')->firstOrFail();

        $this->assertSame('Ayu Lestari', $pelanggan->nama_pelanggan);
        $this->assertSame('aktif', $pelanggan->status_akun);
        $this->assertNotNull($pelanggan->tanggal_daftar);
        $this->assertAuthenticatedAs($pelanggan, 'pelanggan');
    }

    public function test_password_is_stored_hashed(): void
    {
        $this->post(route('register.store'), $this->dataValid());

        $pelanggan = Pelanggan::firstOrFail();

        $this->assertNotSame('rahasia-banget', $pelanggan->password);
        $this->assertTrue(Hash::check('rahasia-banget', $pelanggan->password));
    }

    public function test_email_is_saved_in_lowercase(): void
    {
        $this->post(route('register.store'), $this->dataValid(['email' => 'AYU@Example.COM']));

        $this->assertDatabaseHas('pelanggans', ['email' => 'ayu@example.com']);
    }

    public function test_alamat_is_optional(): void
    {
        $this->post(route('register.store'), $this->dataValid(['alamat' => '']))
            ->assertRedirect(route('home'));

        $this->assertDatabaseCount('pelanggans', 1);
    }

    public function test_duplicate_email_is_rejected(): void
    {
        Pelanggan::factory()->create(['email' => 'ayu@example.com']);

        $this->post(route('register.store'), $this->dataValid())
            ->assertSessionHasErrors('email');

        $this->assertDatabaseCount('pelanggans', 1);
    }

    public function test_password_confirmation_must_match(): void
    {
        $this->post(route('register.store'), $this->dataValid(['password_confirmation' => 'beda']))
            ->assertSessionHasErrors('password');

        $this->assertDatabaseCount('pelanggans', 0);
    }

    public function test_password_shorter_than_eight_characters_is_rejected(): void
    {
        $this->post(route('register.store'), $this->dataValid([
            'password' => 'pendek',
            'password_confirmation' => 'pendek',
        ]))->assertSessionHasErrors('password');

        $this->assertDatabaseCount('pelanggans', 0);
    }

    public function test_invalid_phone_number_is_rejected(): void
    {
        $this->post(route('register.store'), $this->dataValid(['no_telp' => 'abc123']))
            ->assertSessionHasErrors('no_telp');
    }

    public function test_required_fields_are_validated(): void
    {
        $this->post(route('register.store'), [])
            ->assertSessionHasErrors(['nama_pelanggan', 'email', 'no_telp', 'password']);
    }

    public function test_status_akun_cannot_be_set_from_the_form(): void
    {
        $this->post(route('register.store'), $this->dataValid(['status_akun' => 'nonaktif']));

        $this->assertSame('aktif', Pelanggan::firstOrFail()->status_akun);
    }

    public function test_signed_in_pelanggan_visiting_registration_is_redirected_to_home(): void
    {
        $this->actingAs(Pelanggan::factory()->create(), 'pelanggan')
            ->get(route('register'))
            ->assertRedirect(route('home'));
    }
}
