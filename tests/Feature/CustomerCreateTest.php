<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

class CustomerCreateTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed();
    }

    public function test_admin_can_create_customer_with_profile(): void
    {
        $admin = User::where('role', 'admin')->firstOrFail();

        $this->actingAs($admin)
            ->post(route('admin.users.store'), [
                'name' => 'Budi Pelanggan',
                'email' => 'pelangganbaru@example.com',
                'phone' => '08123456789',
                'tier' => 'silver',
                'points' => 250,
            ])
            ->assertRedirect(route('admin.users.index'))
            ->assertSessionHasNoErrors();

        $user = User::where('email', 'pelangganbaru@example.com')->firstOrFail();
        $this->assertSame('customer', $user->role);
        $this->assertSame('pelangganbaru', $user->username);
        $this->assertNotNull($user->password);
        $this->assertSame('08123456789', $user->profile->phone);
        $this->assertSame('silver', $user->profile->membership_tier);
        $this->assertSame(250, $user->profile->loyalty_points);
    }

    public function test_username_is_made_unique(): void
    {
        $admin = User::where('role', 'admin')->firstOrFail();
        User::create(['name' => 'X', 'email' => 'dup@example.com', 'username' => 'dup', 'password' => 'secret', 'role' => 'customer']);

        $this->actingAs($admin)->post(route('admin.users.store'), [
            'name' => 'Y', 'email' => 'dup2@example.com',
        ])->assertSessionHasNoErrors();

        $this->assertSame('dup2', User::where('email', 'dup2@example.com')->value('username'));
    }

    public function test_email_must_be_unique_and_valid(): void
    {
        $admin = User::where('role', 'admin')->firstOrFail();
        User::create(['name' => 'X', 'email' => 'ada@example.com', 'password' => 'secret', 'role' => 'customer']);

        $this->actingAs($admin)->post(route('admin.users.store'), ['name' => 'Y', 'email' => 'ada@example.com'])
            ->assertSessionHasErrors('email');

        $this->actingAs($admin)->post(route('admin.users.store'), ['name' => 'Y', 'email' => 'bukan-email'])
            ->assertSessionHasErrors('email');
    }

    public function test_non_admin_cannot_create_customer(): void
    {
        $customer = User::factory()->create(['role' => 'customer']);

        $this->actingAs($customer)
            ->post(route('admin.users.store'), ['name' => 'Z', 'email' => 'z@example.com'])
            ->assertForbidden();
    }

    #[DataProvider('phoneProvider')]
    public function test_whatsapp_phone_normalization(string $input, string $expected): void
    {
        $this->assertSame($expected, wa_phone($input));
        $this->assertSame('https://wa.me/'.$expected, whatsapp_url($input));
    }

    public static function phoneProvider(): array
    {
        return [
            'leading zero' => ['08123456789', '628123456789'],
            'plus 62 with spaces' => ['+62 812-3456-789', '628123456789'],
            'already 62' => ['628123456789', '628123456789'],
            'missing zero' => ['8123456789', '628123456789'],
            'with parentheses' => ['(0812) 3456 789', '628123456789'],
            'empty' => ['', ''],
        ];
    }
}
