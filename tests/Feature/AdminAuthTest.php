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
            'username' => 'sample-admin',
            'email' => 'admin@example.test',
            'password' => Hash::make('test-only-admin-password'),
            'role' => 'admin',
        ]);

        $this->post(route('admin.login.post'), [
            'username' => 'sample-admin',
            'password' => 'test-only-admin-password',
        ])->assertRedirect(route('admin.dashboard'));

        $this->assertAuthenticated();
    }

    public function test_non_admin_cannot_login_via_admin_form(): void
    {
        User::factory()->create([
            'username' => 'sample-customer',
            'email' => 'customer@example.test',
            'password' => Hash::make('test-only-customer-password'),
            'role' => 'customer',
        ]);

        $this->post(route('admin.login.post'), [
            'username' => 'sample-customer',
            'password' => 'test-only-customer-password',
        ])->assertSessionHasErrors('username');

        $this->assertGuest();
    }

    public function test_wrong_password_is_rejected(): void
    {
        User::factory()->create([
            'username' => 'sample-admin',
            'email' => 'admin@example.test',
            'password' => Hash::make('test-only-admin-password'),
            'role' => 'admin',
        ]);

        $this->post(route('admin.login.post'), [
            'username' => 'sample-admin',
            'password' => 'wrong-test-password',
        ])->assertSessionHasErrors('username');

        $this->assertGuest();
    }
}
