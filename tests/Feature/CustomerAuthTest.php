<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class CustomerAuthTest extends TestCase
{
    use RefreshDatabase;

    public function test_customer_can_register_and_is_logged_in(): void
    {
        $this->post(route('register.post'), [
            'name' => 'Cici Customer',
            'username' => 'cici',
            'email' => 'cici@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ])->assertRedirect(route('home'));

        $this->assertAuthenticated();
        $user = User::where('username', 'cici')->firstOrFail();
        $this->assertSame('customer', $user->role);
        $this->assertTrue(Hash::check('password123', $user->password));
    }

    public function test_unified_login_accepts_customer_by_username_and_email(): void
    {
        $user = User::factory()->create([
            'username' => 'budi',
            'email' => 'budi@example.com',
            'password' => Hash::make('secret123'),
            'role' => 'customer',
        ]);

        // by username
        $this->post(route('login.post'), ['login' => 'budi', 'password' => 'secret123'])
            ->assertRedirect(route('home'));
        $this->assertAuthenticatedAs($user);

        \Illuminate\Support\Facades\Auth::logout();

        // by email
        $this->post(route('login.post'), ['login' => 'budi@example.com', 'password' => 'secret123'])
            ->assertRedirect(route('home'));
        $this->assertAuthenticatedAs($user);
    }

    public function test_admin_logging_in_via_unified_login_lands_on_dashboard(): void
    {
        $admin = User::factory()->create([
            'username' => 'boss',
            'password' => Hash::make('secret123'),
            'role' => 'admin',
        ]);

        $this->post(route('login.post'), ['login' => 'boss', 'password' => 'secret123'])
            ->assertRedirect(route('admin.dashboard'));
        $this->assertAuthenticatedAs($admin);
    }

    public function test_auth_pages_render(): void
    {
        $this->get(route('login'))->assertOk()->assertSee('Masuk', false);
        $this->get(route('register'))->assertOk()->assertSee('Buat Akun', false);
    }

    public function test_wrong_credentials_are_rejected(): void
    {
        User::factory()->create([
            'username' => 'budi',
            'password' => Hash::make('secret123'),
            'role' => 'customer',
        ]);

        $this->post(route('login.post'), ['login' => 'budi', 'password' => 'nope'])
            ->assertSessionHasErrors('login');
        $this->assertGuest();
    }
}
