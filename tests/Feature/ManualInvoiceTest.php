<?php

namespace Tests\Feature;

use App\Models\Order;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ManualInvoiceTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed();
    }

    public function test_create_page_loads(): void
    {
        $admin = User::where('role', 'admin')->firstOrFail();

        $this->actingAs($admin)
            ->get(route('admin.orders.create'))
            ->assertOk()
            ->assertSee('Buat Invoice');
    }

    public function test_store_creates_order_with_items_and_redirects_to_invoice(): void
    {
        $admin = User::where('role', 'admin')->firstOrFail();

        $response = $this->actingAs($admin)->post(route('admin.orders.store'), [
            'customer_name' => 'Pelanggan Manual',
            'customer_whatsapp' => '081200009999',
            'customer_address' => 'Jl. Contoh 1',
            'shipping_cost' => 10000,
            'fee' => 2500,
            'items' => [
                ['product_name' => 'Barang A', 'unit' => 'pcs', 'quantity' => 2, 'product_price' => 30000],
                ['product_name' => 'Barang B', 'unit' => 'box', 'quantity' => 1, 'product_price' => 45000],
            ],
        ]);

        $order = Order::where('customer_whatsapp', '081200009999')->firstOrFail();

        $response->assertRedirect(route('admin.orders.invoice', ['order' => $order, 'print' => 1]));
        $this->assertSame(60_000 + 45_000, $order->subtotal);
        $this->assertSame(10_000, $order->shipping_cost);
        $this->assertSame(2_500, $order->fee);
        $this->assertSame(117_500, $order->total);
        $this->assertSame(Order::STATUS_AWAITING_PAYMENT, $order->status);
        $this->assertSame('manual', $order->payment_method);
        $this->assertSame(2, $order->items()->count());
    }

    public function test_mark_paid_sets_paid_and_confirmed_status(): void
    {
        $admin = User::where('role', 'admin')->firstOrFail();

        $this->actingAs($admin)->post(route('admin.orders.store'), [
            'customer_name' => 'Pelanggan Lunas',
            'customer_whatsapp' => '081200008888',
            'mark_paid' => '1',
            'items' => [
                ['product_name' => 'Barang C', 'quantity' => 1, 'product_price' => 50000],
            ],
        ])->assertSessionHasNoErrors();

        $order = Order::where('customer_whatsapp', '081200008888')->firstOrFail();
        $this->assertTrue($order->is_paid);
        $this->assertSame(Order::STATUS_CONFIRMED, $order->status);
    }

    public function test_store_requires_at_least_one_item(): void
    {
        $admin = User::where('role', 'admin')->firstOrFail();

        $this->actingAs($admin)->post(route('admin.orders.store'), [
            'customer_name' => 'Tanpa Item',
            'customer_whatsapp' => '081200007777',
            'items' => [],
        ])->assertSessionHasErrors('items');
    }

    public function test_non_admin_cannot_create_invoice(): void
    {
        $customer = User::factory()->create(['role' => 'customer']);

        $this->actingAs($customer)
            ->get(route('admin.orders.create'))
            ->assertForbidden();

        $this->actingAs($customer)
            ->post(route('admin.orders.store'), [])
            ->assertForbidden();
    }

    public function test_invoice_index_lists_only_manual_invoices(): void
    {
        $admin = User::where('role', 'admin')->firstOrFail();

        // Manual invoice (from the admin form).
        Order::create([
            'order_number' => 'NDTEST-MANUAL01',
            'customer_name' => 'Pelanggan Manual',
            'customer_whatsapp' => '081200005555',
            'customer_address' => 'Jl. Manual',
            'subtotal' => 20000,
            'shipping_cost' => 0,
            'total' => 20000,
            'status' => Order::STATUS_AWAITING_PAYMENT,
            'payment_method' => 'manual',
        ]);

        // Regular checkout order — must NOT appear in the invoice list.
        Order::create([
            'order_number' => 'NDTEST-QRIS01',
            'customer_name' => 'Pelanggan Checkout',
            'customer_whatsapp' => '081200004444',
            'customer_address' => 'Jl. Checkout',
            'subtotal' => 30000,
            'shipping_cost' => 0,
            'total' => 30000,
            'status' => Order::STATUS_PENDING,
            'payment_method' => 'qris',
        ]);

        $this->actingAs($admin)
            ->get(route('admin.invoices.index'))
            ->assertOk()
            ->assertSee('NDTEST-MANUAL01')
            ->assertDontSee('NDTEST-QRIS01');
    }

    public function test_customer_and_product_search_return_json(): void
    {
        $admin = User::where('role', 'admin')->firstOrFail();

        Order::create([
            'order_number' => 'NDTEST-SEARCH01',
            'customer_name' => 'Cari Nama Unik',
            'customer_whatsapp' => '081200006666',
            'customer_address' => 'Jl. Search',
            'subtotal' => 1000,
            'shipping_cost' => 0,
            'total' => 1000,
            'status' => Order::STATUS_PENDING,
        ]);

        $this->actingAs($admin)
            ->getJson(route('admin.orders.customer-search', ['q' => 'Cari Nama']))
            ->assertOk()
            ->assertJsonFragment(['customer_whatsapp' => '081200006666']);

        // Empty term short-circuits to an empty list (no 500).
        $this->actingAs($admin)
            ->getJson(route('admin.orders.product-search', ['q' => '']))
            ->assertOk()
            ->assertExactJson([]);
    }
}
