<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class AdminUserManagementTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed();

        $this->admin = User::where('role', 'admin')->firstOrFail();
    }

    public function test_index_lists_admins_but_not_customers(): void
    {
        User::factory()->create(['role' => 'customer', 'name' => 'Pelanggan Rahasia']);

        $this->actingAs($this->admin)
            ->get(route('admin.admins.index'))
            ->assertOk()
            ->assertSee($this->admin->name)
            ->assertSee('Tambah Admin')
            ->assertDontSee('Pelanggan Rahasia');
    }

    public function test_store_creates_admin_with_working_credentials(): void
    {
        $this->actingAs($this->admin)
            ->post(route('admin.admins.store'), [
                'name' => 'Admin Kedua',
                'email' => 'admin2@example.test',
                'username' => 'admin2',
                'password' => 'KataSandiKuat123',
                'password_confirmation' => 'KataSandiKuat123',
            ])
            ->assertRedirect(route('admin.admins.index'))
            ->assertSessionHas('success');

        $created = User::where('email', 'admin2@example.test')->firstOrFail();

        $this->assertSame('admin', $created->role);
        $this->assertTrue(Hash::check('KataSandiKuat123', $created->password));
    }

    public function test_store_rejects_short_password_and_duplicate_email(): void
    {
        $this->actingAs($this->admin)
            ->post(route('admin.admins.store'), [
                'name' => 'Admin Lemah',
                'email' => 'admin2@example.test',
                'password' => 'pendek',
                'password_confirmation' => 'pendek',
            ])
            ->assertSessionHasErrors('password');

        $this->actingAs($this->admin)
            ->post(route('admin.admins.store'), [
                'name' => 'Admin Duplikat',
                'email' => $this->admin->email,
                'password' => 'KataSandiKuat123',
                'password_confirmation' => 'KataSandiKuat123',
            ])
            ->assertSessionHasErrors('email');

        $this->assertSame(1, User::where('role', 'admin')->count());
    }

    public function test_update_changes_admin_data(): void
    {
        $this->actingAs($this->admin)
            ->put(route('admin.admins.update', $this->admin), [
                'name' => 'Admin Utama Baru',
                'email' => 'utama@example.test',
                'username' => 'utama',
            ])
            ->assertRedirect(route('admin.admins.edit', $this->admin));

        $this->assertSame('Admin Utama Baru', $this->admin->fresh()->name);
        $this->assertSame('utama@example.test', $this->admin->fresh()->email);
    }

    public function test_non_admin_user_cannot_be_managed_via_admins_routes(): void
    {
        $customer = User::factory()->create(['role' => 'customer']);

        $this->actingAs($this->admin)
            ->get(route('admin.admins.edit', $customer))
            ->assertNotFound();

        $this->actingAs($this->admin)
            ->delete(route('admin.admins.destroy', $customer))
            ->assertNotFound();
    }

    public function test_admin_can_reset_other_admins_password_and_they_can_login(): void
    {
        $second = User::create([
            'name' => 'Admin Resettable',
            'email' => 'resettable@example.test',
            'password' => 'KataSandiLama12345',
            'role' => 'admin',
        ]);

        $this->actingAs($this->admin)
            ->put(route('admin.admins.password', $second), [
                'password' => 'KataSandiBaru12345',
                'password_confirmation' => 'KataSandiBaru12345',
            ])
            ->assertRedirect(route('admin.admins.edit', $second))
            ->assertSessionHas('success');

        $this->assertTrue(Hash::check('KataSandiBaru12345', $second->fresh()->password));
    }

    public function test_changing_own_password_requires_current_password(): void
    {
        // Tanpa kata sandi lama -> ditolak.
        $this->actingAs($this->admin)
            ->put(route('admin.admins.password', $this->admin), [
                'password' => 'KataSandiBaru12345',
                'password_confirmation' => 'KataSandiBaru12345',
            ])
            ->assertSessionHasErrors('current_password');

        // Kata sandi lama salah -> ditolak.
        $this->actingAs($this->admin)
            ->put(route('admin.admins.password', $this->admin), [
                'current_password' => 'salah-total-password',
                'password' => 'KataSandiBaru12345',
                'password_confirmation' => 'KataSandiBaru12345',
            ])
            ->assertSessionHasErrors('current_password');

        // Kata sandi lama benar (di seeding testing = test_bootstrap_password).
        $oldPassword = config('seeding.test_bootstrap_password');
        $this->actingAs($this->admin)
            ->put(route('admin.admins.password', $this->admin), [
                'current_password' => $oldPassword,
                'password' => 'KataSandiBaru12345',
                'password_confirmation' => 'KataSandiBaru12345',
            ])
            ->assertSessionHas('success');

        $this->assertTrue(Hash::check('KataSandiBaru12345', $this->admin->fresh()->password));
    }

    public function test_admin_cannot_delete_themselves_or_last_admin(): void
    {
        // Menghapus diri sendiri ditolak walau masih ada admin lain.
        $this->actingAs($this->admin)
            ->delete(route('admin.admins.destroy', $this->admin))
            ->assertSessionHas('error');

        // Admin terakhir tidak bisa dihapus.
        $this->assertSame(1, User::where('role', 'admin')->count());
        $this->actingAs($this->admin)
            ->delete(route('admin.admins.destroy', $this->admin))
            ->assertSessionHas('error');
    }

    public function test_second_admin_can_be_deleted_by_first(): void
    {
        $second = User::create([
            'name' => 'Admin Sekali Pakai',
            'email' => 'disposable@example.test',
            'password' => 'KataSandi12345XXX',
            'role' => 'admin',
        ]);

        $this->actingAs($this->admin)
            ->delete(route('admin.admins.destroy', $second))
            ->assertRedirect(route('admin.admins.index'))
            ->assertSessionHas('success');

        $this->assertDatabaseMissing('users', ['id' => $second->id]);
    }

    public function test_customer_gets_forbidden_and_guest_is_redirected(): void
    {
        $customer = User::factory()->create(['role' => 'customer']);

        $this->actingAs($customer)
            ->get(route('admin.admins.index'))
            ->assertForbidden();

        auth()->logout();

        $this->get(route('admin.admins.index'))
            ->assertRedirect(route('admin.login'));
    }
}
