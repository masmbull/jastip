<?php

namespace Tests\Feature;

use App\Mail\OrderConfirmation;
use App\Mail\OrderShipped;
use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class CheckoutFlowTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed();
    }

    public function test_order_flow_from_cart_to_admin_status_update(): void
    {
        $customer = User::factory()->create(['role' => 'customer']);
        $this->actingAs($customer);

        $product = Product::firstWhere('slug', 'fashion-item') ?? Product::firstOrFail();
        $product->update(['setbiaya_fee' => 5000]);
        $feeTotal = $product->effectiveFee() * 2;

        // Public catalog
        $this->get(route('home'))->assertOk();
        $this->get(route('products.index'))->assertOk();
        $this->get(route('products.show', $product))->assertOk();

        // cart.add binds explicitly on {product:id} while Product route key is slug
        $this->postJson(route('cart.add', ['product' => $product->id]), ['quantity' => 2])
            ->assertOk()
            ->assertJson(['success' => true]);

        $this->get(route('cart.index'))->assertOk()->assertSee($product->name, false);

        // Checkout
        $this->get(route('checkout.index'))->assertOk();

        $this->post(route('checkout.store'), [
            'name' => 'Smoke Tester',
            'whatsapp' => '08123456789',
            'address' => 'Jl. Testing No. 1, Bandung',
            'notes' => 'Titip 2 ya',
            'city' => 'Jakarta',
            'courier' => 'jne',
        ])->assertRedirect(route('checkout.confirmation'));

        $order = Order::latest('id')->firstOrFail();
        $this->assertSame(Order::STATUS_AWAITING_PAYMENT, $order->status);
        $this->assertSame($product->price * 2, $order->subtotal);
        $this->assertSame($feeTotal, $order->fee);
        $this->assertSame($order->subtotal + $order->shipping_cost + $order->fee, $order->total);

        $item = $order->items()->firstOrFail();
        $this->assertSame($product->name, $item->product_name);
        $this->assertSame($product->unit, $item->unit);

        $this->get(route('checkout.confirmation'))
            ->assertOk()
            ->assertSee($order->order_number, false)
            ->assertSee('wa.me/6285123456789', false)
            ->assertDontSee('wa.me/628123456789', false);

        // Admin area is guarded (log out the customer first — a logged-in
        // non-admin now hits 403, not the login redirect).
        \Illuminate\Support\Facades\Auth::logout();
        $this->get(route('admin.orders.index'))->assertRedirect(route('admin.login'));

        $this->post(route('admin.login.post'), [
            'username' => 'admin',
            'password' => 'admin123',
        ])->assertRedirect(route('admin.dashboard'));

        $this->get(route('admin.orders.index'))->assertOk()->assertSee($order->order_number, false);

        // Order detail renders snapshotted fields and the Indonesian status label
        $this->get(route('admin.orders.show', $order))
            ->assertOk()
            ->assertSee($order->customer_address, false)
            ->assertSee($item->product_name, false)
            ->assertSee($item->unit, false)
            ->assertSee('Menunggu Pembayaran', false)
            ->assertSee('Biaya Fee', false);

        $this->put(route('admin.orders.status', $order), ['status' => 'processing'])->assertRedirect();

        $this->assertSame(Order::STATUS_PROCESSING, $order->fresh()->status);

        $this->get(route('admin.orders.show', $order))->assertOk()->assertSee('Diproses', false);
    }

    public function test_checkout_applies_valid_coupon_and_rejects_invalid(): void
    {
        $customer = User::factory()->create(['role' => 'customer']);
        $this->actingAs($customer);

        $product = Product::firstOrFail();
        $product->update(['setbiaya_fee' => 0]);

        $coupon = \App\Models\Coupon::create([
            'code' => 'HEMAT10',
            'type' => 'percentage',
            'value' => 10,
            'valid_from' => now()->subDay(),
            'valid_to' => now()->addDay(),
            'is_active' => true,
        ]);

        $this->postJson(route('cart.add', ['product' => $product->id]), ['quantity' => 2])
            ->assertOk();

        $subtotal = $product->price * 2;
        $ordersBefore = Order::count();

        // Invalid code bounces back with an error, no order created.
        $this->post(route('checkout.store'), [
            'name' => 'Kupon Tester',
            'whatsapp' => '08123456789',
            'address' => 'Jl. Kupon No. 1, Bandung',
            'city' => 'Jakarta',
            'courier' => 'jne',
            'coupon_code' => 'NOPE',
        ])->assertSessionHas('error');
        $this->assertSame($ordersBefore, Order::count());

        // Valid code discounts 10% and bumps usage_count.
        $this->post(route('checkout.store'), [
            'name' => 'Kupon Tester',
            'whatsapp' => '08123456789',
            'address' => 'Jl. Kupon No. 1, Bandung',
            'city' => 'Jakarta',
            'courier' => 'jne',
            'coupon_code' => 'hemat10',
        ])->assertRedirect(route('checkout.confirmation'));

        $order = Order::latest('id')->firstOrFail();
        $expectedDiscount = (int) round($subtotal * 0.10);
        $this->assertSame($coupon->id, $order->coupon_id);
        $this->assertSame($expectedDiscount, $order->discount);
        $this->assertSame($subtotal + $order->shipping_cost + $order->fee - $expectedDiscount, $order->total);
        $this->assertSame(1, $coupon->fresh()->usage_count);
    }

    public function test_checkout_reserves_stock_and_commit_on_paid(): void
    {
        $customer = User::factory()->create(['role' => 'customer']);
        $this->actingAs($customer);

        $product = Product::firstOrFail();
        $product->update(['stock' => 10, 'reserved' => 0, 'setbiaya_fee' => 0]);

        $this->postJson(route('cart.add', ['product' => $product->id]), ['quantity' => 3])->assertOk();

        $this->post(route('checkout.store'), [
            'name' => 'Stok Tester',
            'whatsapp' => '08123456789',
            'address' => 'Jl. Stok No. 1, Bandung',
            'city' => 'Jakarta',
            'courier' => 'jne',
        ])->assertRedirect(route('checkout.confirmation'));

        // Reserved naik, stock belum berubah.
        $this->assertSame(3, $product->fresh()->reserved);
        $this->assertSame(10, $product->fresh()->stock);

        $order = Order::latest('id')->firstOrFail();
        $admin = \App\Models\User::query()->where('role', 'admin')->firstOrFail();

        // Admin menandai lunas -> commit: stock & reserved sama-sama turun.
        $this->actingAs($admin)->put(route('admin.orders.status', $order), [
            'status' => Order::STATUS_CONFIRMED,
            'mark_paid' => 1,
        ])->assertRedirect();

        $product->refresh();
        $this->assertSame(7, $product->stock);
        $this->assertSame(0, $product->reserved);
        $this->assertTrue($order->fresh()->stock_committed);

        // Idempoten: commit kedua tidak menggandakan decrement.
        $this->actingAs($admin)->put(route('admin.orders.status', $order), [
            'status' => Order::STATUS_SHIPPED,
        ])->assertRedirect();
        $this->assertSame(7, $product->fresh()->stock);
    }

    public function test_checkout_rejects_when_stock_insufficient(): void
    {
        $customer = User::factory()->create(['role' => 'customer']);
        $this->actingAs($customer);

        $product = Product::firstOrFail();
        $product->update(['stock' => 5, 'reserved' => 0, 'setbiaya_fee' => 0]);

        // Isi cart untuk 5 unit, lalu stok ditahan orang lain.
        $this->postJson(route('cart.add', ['product' => $product->id]), ['quantity' => 5])->assertOk();
        $product->update(['reserved' => 5]); // available = 0

        $ordersBefore = Order::count();
        $this->post(route('checkout.store'), [
            'name' => 'Stok Kurang',
            'whatsapp' => '08123456789',
            'address' => 'Jl. Stok No. 2, Bandung',
            'city' => 'Jakarta',
            'courier' => 'jne',
        ])->assertRedirect(route('cart.index'));

        $this->assertSame($ordersBefore, Order::count());
        $this->assertSame(5, $product->fresh()->reserved);
    }

    public function test_checkout_sends_confirmation_email_when_email_provided(): void
    {
        Mail::fake();

        $customer = User::factory()->create(['role' => 'customer']);
        $this->actingAs($customer);

        $product = Product::firstOrFail();
        $product->update(['stock' => 10, 'reserved' => 0, 'setbiaya_fee' => 0]);

        $this->postJson(route('cart.add', ['product' => $product->id]), ['quantity' => 1])->assertOk();

        $this->post(route('checkout.store'), [
            'name' => 'Email Tester',
            'whatsapp' => '08123456789',
            'email' => 'tester@example.com',
            'address' => 'Jl. Email No. 1, Bandung',
            'city' => 'Jakarta',
            'courier' => 'jne',
        ])->assertRedirect(route('checkout.confirmation'));

        $order = Order::latest('id')->firstOrFail();
        $this->assertSame('tester@example.com', $order->customer_email);

        Mail::assertSent(OrderConfirmation::class, fn ($mail) => $mail->hasTo('tester@example.com'));
    }

    public function test_checkout_without_explicit_email_uses_account_email(): void
    {
        Mail::fake();

        $customer = User::factory()->create(['role' => 'customer']);
        $this->actingAs($customer);

        $product = Product::firstOrFail();
        $product->update(['stock' => 10, 'reserved' => 0, 'setbiaya_fee' => 0]);

        $this->postJson(route('cart.add', ['product' => $product->id]), ['quantity' => 1])->assertOk();

        // Field email kosong => pakai email akun yang login.
        $this->post(route('checkout.store'), [
            'name' => 'No Email',
            'whatsapp' => '08123456789',
            'address' => 'Jl. Kosong No. 1, Bandung',
            'city' => 'Jakarta',
            'courier' => 'jne',
        ])->assertRedirect(route('checkout.confirmation'));

        $order = Order::latest('id')->firstOrFail();
        $this->assertSame($customer->email, $order->customer_email);
        Mail::assertSent(OrderConfirmation::class, fn ($mail) => $mail->hasTo($customer->email));
    }

    public function test_shipped_status_emails_customer(): void
    {
        Mail::fake();

        $customer = User::factory()->create(['role' => 'customer']);
        $this->actingAs($customer);

        $product = Product::firstOrFail();
        $product->update(['stock' => 10, 'reserved' => 0, 'setbiaya_fee' => 0]);

        $this->postJson(route('cart.add', ['product' => $product->id]), ['quantity' => 1])->assertOk();
        $this->post(route('checkout.store'), [
            'name' => 'Shipped Tester',
            'whatsapp' => '08123456789',
            'email' => 'shipped@example.com',
            'address' => 'Jl. Kirim No. 1, Bandung',
            'city' => 'Jakarta',
            'courier' => 'jne',
        ])->assertRedirect(route('checkout.confirmation'));

        $order = Order::latest('id')->firstOrFail();
        $admin = \App\Models\User::query()->where('role', 'admin')->firstOrFail();

        $this->actingAs($admin)->put(route('admin.orders.status', $order), [
            'status' => Order::STATUS_SHIPPED,
        ])->assertRedirect();

        Mail::assertSent(OrderShipped::class, fn ($mail) => $mail->hasTo('shipped@example.com'));
    }
}
