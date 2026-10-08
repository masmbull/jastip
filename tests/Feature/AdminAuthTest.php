<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class AdminAuthTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_login_and_reach_dashboard(): void
    {
        User::factory()->create([
            'username' => 'admin',
            'email' => 'admin@nitipdiend.com',
            'password' => Hash::make('admin123'),
            'role' => 'admin',
        ]);

        $this->post(route('admin.login.post'), [
            'username' => 'admin',
            'password' => 'admin123',
        ])->assertRedirect(route('admin.dashboard'));

        $this->assertAuthenticated();
    }

    public function test_non_admin_cannot_login_via_admin_form(): void
    {
        User::factory()->create([
            'username' => 'budi',
            'email' => 'budi@nitipdiend.com',
            'password' => Hash::make('customer123'),
            'role' => 'customer',
        ]);

        $this->post(route('admin.login.post'), [
            'username' => 'budi',
            'password' => 'customer123',
        ])->assertSessionHasErrors('username');

        $this->assertGuest();
    }

    public function test_wrong_password_is_rejected(): void
    {
        User::factory()->create([
            'username' => 'admin',
            'email' => 'admin@nitipdiend.com',
            'password' => Hash::make('admin123'),
            'role' => 'admin',
        ]);

        $this->post(route('admin.login.post'), [
            'username' => 'admin',
            'password' => 'salah',
        ])->assertSessionHasErrors('username');

        $this->assertGuest();
    }
}
