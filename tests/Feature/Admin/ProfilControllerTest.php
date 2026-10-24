<?php

namespace Tests\Feature\Admin;

use App\Models\Admin;
use App\Models\Pelanggan;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProfilControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_is_redirected_to_login(): void
    {
        $this->get(route('admin.profil'))->assertRedirect(route('login'));
    }

    public function test_pelanggan_cannot_open_admin_profil(): void
    {
        $this->actingAs(Pelanggan::factory()->create(), 'pelanggan')
            ->get(route('admin.profil'))
            ->assertRedirect(route('login'));
    }

    public function test_admin_sees_profil_with_logout_button(): void
    {
        $admin = Admin::factory()->create(['nama_admin' => 'Admin Hawa']);

        $this->actingAs($admin, 'admin')
            ->get(route('admin.profil'))
            ->assertOk()
            ->assertSee('Admin Hawa')
            ->assertSee($admin->email)
            ->assertSee(route('logout'), false);
    }
}
