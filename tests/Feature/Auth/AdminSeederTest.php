<?php

namespace Tests\Feature\Auth;

use App\Models\Admin;
use Database\Seeders\AdminSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminSeederTest extends TestCase
{
    use RefreshDatabase;

    public function test_seeder_leaves_exactly_one_admin_who_can_sign_in(): void
    {
        Admin::factory()->create(['email' => 'admin@hawabutik.test']);

        $this->seed(AdminSeeder::class);
        $this->seed(AdminSeeder::class);

        $this->assertDatabaseCount('admins', 1);
        $this->assertDatabaseHas('admins', ['email' => 'adminhawa@gmail.com', 'status_akun' => 'aktif']);

        $this->post(route('login.store'), [
            'peran' => 'admin',
            'email' => 'adminhawa@gmail.com',
            'password' => 'admin123',
        ])->assertRedirect(route('admin.dashboard'));

        $this->assertAuthenticated('admin');
    }
}
