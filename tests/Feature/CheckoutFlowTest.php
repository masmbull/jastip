<?php

namespace Tests\Feature;

use App\Models\Order;
use App\Models\Product;
use Illuminate\Foundation\Testing\RefreshDatabase;
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
        $product = Product::firstWhere('slug', 'fashion-item') ?? Product::firstOrFail();

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
            'shipping_method' => 'JNE',
        ])->assertRedirect(route('checkout.confirmation'));

        $order = Order::latest('id')->firstOrFail();
        $this->assertSame(Order::STATUS_PENDING, $order->status);
        $this->assertSame($product->price * 2, $order->subtotal);

        $item = $order->items()->firstOrFail();
        $this->assertSame($product->name, $item->product_name);
        $this->assertSame($product->unit, $item->unit);

        $this->get(route('checkout.confirmation'))->assertOk()->assertSee($order->order_number, false);

        // Admin area is guarded
        $this->get(route('admin.orders.index'))->assertRedirect(route('admin.login'));

        $this->post(route('admin.login.post'), [
            'email' => 'admin@nitipdiend.com',
            'password' => 'admin123',
        ])->assertRedirect(route('admin.dashboard'));

        $this->get(route('admin.orders.index'))->assertOk()->assertSee($order->order_number, false);

        // Order detail renders snapshotted fields and the Indonesian status label
        $this->get(route('admin.orders.show', $order))
            ->assertOk()
            ->assertSee($order->customer_address, false)
            ->assertSee($item->product_name, false)
            ->assertSee($item->unit, false)
            ->assertSee('Pending', false);

        $this->put(route('admin.orders.status', $order), ['status' => 'processing'])->assertRedirect();

        $this->assertSame(Order::STATUS_PROCESSING, $order->fresh()->status);

        $this->get(route('admin.orders.show', $order))->assertOk()->assertSee('Diproses', false);
    }
}
