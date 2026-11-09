<?php

namespace Tests\Feature\Auth;

use App\Models\Admin;
use App\Models\Pelanggan;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LoginControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_login_page_renders_for_guest(): void
    {
        $this->get(route('login'))
            ->assertOk()
            ->assertSee('Masuk sebagai');
    }

    public function test_pelanggan_with_valid_credentials_signs_in_and_redirects_to_home(): void
    {
        $pelanggan = Pelanggan::factory()->create(['email' => 'ayu@example.com']);

        $this->post(route('login.store'), [
            'peran' => 'pelanggan',
            'email' => 'ayu@example.com',
            'password' => 'password',
        ])->assertRedirect(route('home'));

        $this->assertAuthenticatedAs($pelanggan, 'pelanggan');
        $this->assertGuest('admin');
    }

    public function test_admin_with_valid_credentials_signs_in_and_redirects_to_ringkasan(): void
    {
        $admin = Admin::factory()->create(['email' => 'admin@example.com']);

        $this->post(route('login.store'), [
            'peran' => 'admin',
            'email' => 'admin@example.com',
            'password' => 'password',
        ])->assertRedirect(route('admin.dashboard'));

        $this->assertAuthenticatedAs($admin, 'admin');
        $this->assertGuest('pelanggan');
    }

    public function test_pelanggan_is_not_sent_to_admin_page_that_was_requested_before_sign_in(): void
    {
        Pelanggan::factory()->create(['email' => 'ayu@example.com']);

        $this->get(route('admin.dashboard'))->assertRedirect(route('login'));

        $this->post(route('login.store'), [
            'peran' => 'pelanggan',
            'email' => 'ayu@example.com',
            'password' => 'password',
        ])->assertRedirect(route('home'));
    }

    public function test_admin_is_sent_back_to_the_page_requested_before_sign_in(): void
    {
        Admin::factory()->create(['email' => 'admin@example.com']);

        $this->withSession(['url.intended' => route('admin.pesanan.index')]);

        $this->post(route('login.store'), [
            'peran' => 'admin',
            'email' => 'admin@example.com',
            'password' => 'password',
        ])->assertRedirect(route('admin.pesanan.index'));
    }

    public function test_email_is_matched_case_insensitively(): void
    {
        Pelanggan::factory()->create(['email' => 'ayu@example.com']);

        $this->post(route('login.store'), [
            'peran' => 'pelanggan',
            'email' => 'AYU@Example.com',
            'password' => 'password',
        ])->assertRedirect(route('home'));

        $this->assertAuthenticated('pelanggan');
    }

    public function test_wrong_password_is_rejected_with_error_message(): void
    {
        Pelanggan::factory()->create(['email' => 'ayu@example.com']);

        $this->from(route('login'))->post(route('login.store'), [
            'peran' => 'pelanggan',
            'email' => 'ayu@example.com',
            'password' => 'salah-total',
        ])
            ->assertRedirect(route('login'))
            ->assertSessionHasErrors('email');

        $this->assertGuest('pelanggan');
    }

    public function test_nonaktif_account_is_rejected(): void
    {
        Pelanggan::factory()->nonaktif()->create(['email' => 'ayu@example.com']);

        $this->post(route('login.store'), [
            'peran' => 'pelanggan',
            'email' => 'ayu@example.com',
            'password' => 'password',
        ])->assertSessionHasErrors('email');

        $this->assertGuest('pelanggan');
    }

    public function test_pelanggan_credentials_do_not_work_for_admin_role(): void
    {
        Pelanggan::factory()->create(['email' => 'ayu@example.com']);

        $this->post(route('login.store'), [
            'peran' => 'admin',
            'email' => 'ayu@example.com',
            'password' => 'password',
        ])->assertSessionHasErrors('email');

        $this->assertGuest('admin');
        $this->assertGuest('pelanggan');
    }

    public function test_unknown_role_is_rejected(): void
    {
        Admin::factory()->create(['email' => 'admin@example.com']);

        $this->post(route('login.store'), [
            'peran' => 'web',
            'email' => 'admin@example.com',
            'password' => 'password',
        ])->assertSessionHasErrors('peran');
    }

    public function test_required_fields_are_validated(): void
    {
        $this->post(route('login.store'), [])
            ->assertSessionHasErrors(['peran', 'email', 'password']);
    }

    public function test_sixth_failed_attempt_is_locked_out(): void
    {
        Pelanggan::factory()->create(['email' => 'ayu@example.com']);

        foreach (range(1, 5) as $percobaan) {
            $this->post(route('login.store'), [
                'peran' => 'pelanggan',
                'email' => 'ayu@example.com',
                'password' => 'salah',
            ])->assertSessionHasErrors('email');
        }

        $this->post(route('login.store'), [
            'peran' => 'pelanggan',
            'email' => 'ayu@example.com',
            'password' => 'password',
        ])->assertSessionHasErrors('email');

        $this->assertGuest('pelanggan');
    }

    public function test_signed_in_pelanggan_visiting_login_is_redirected_to_home(): void
    {
        $this->actingAs(Pelanggan::factory()->create(), 'pelanggan')
            ->get(route('login'))
            ->assertRedirect(route('home'));
    }

    public function test_signed_in_admin_visiting_login_is_redirected_to_ringkasan(): void
    {
        $this->actingAs(Admin::factory()->create(), 'admin')
            ->get(route('login'))
            ->assertRedirect(route('admin.dashboard'));
    }

    public function test_logout_signs_out_and_redirects_home(): void
    {
        $this->actingAs(Pelanggan::factory()->create(), 'pelanggan')
            ->post(route('logout'))
            ->assertRedirect(route('home'));

        $this->assertGuest('pelanggan');
    }

    public function test_hamburger_menu_shows_masuk_to_guest_and_profil_to_signed_in_user(): void
    {
        $this->get(route('home'))
            ->assertSee(route('login'), false)
            ->assertSee(route('pelanggan.pemesanan'), false)
            ->assertDontSee('Keluar');

        $this->actingAs(Pelanggan::factory()->create(), 'pelanggan')
            ->get(route('home'))
            ->assertSee(route('pelanggan.profil.edit'), false)
            ->assertSee(route('pelanggan.pemesanan'), false)
            ->assertDontSee('Keluar')
            ->assertDontSee(route('login'), false);
    }

    public function test_admin_visiting_home_is_redirected_to_ringkasan(): void
    {
        $this->actingAs(Admin::factory()->create(), 'admin')
            ->get(route('home'))
            ->assertRedirect(route('admin.dashboard'));
    }

    public function test_admin_menu_has_profil_but_no_pemesanan_or_keluar(): void
    {
        $this->actingAs(Admin::factory()->create(), 'admin')
            ->get(route('admin.dashboard'))
            ->assertSee(route('admin.profil'), false)
            ->assertDontSee(route('pelanggan.pemesanan'), false)
            ->assertDontSee('Keluar');
    }
}
