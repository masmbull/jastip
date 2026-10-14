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

    public function test_store_creates_order_with_items_and_redirects_to_list(): void
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

        $response->assertRedirect(route('admin.invoices.index'));
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
            'customer_address' => 'Jl. Lunas 1',
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

    public function test_product_search_returns_json(): void
    {
        $admin = User::where('role', 'admin')->firstOrFail();

        // Empty term short-circuits to an empty list (no 500).
        $this->actingAs($admin)
            ->getJson(route('admin.orders.product-search', ['q' => '']))
            ->assertOk()
            ->assertExactJson([]);
    }

    public function test_shipping_options_and_quote_return_json(): void
    {
        $admin = User::where('role', 'admin')->firstOrFail();

        $this->actingAs($admin)
            ->getJson(route('admin.orders.shipping-options'))
            ->assertOk()
            ->assertJsonFragment(['Jakarta']);

        $this->actingAs($admin)
            ->getJson(route('admin.orders.shipping-quote', ['city' => 'Jakarta', 'weight' => 1]))
            ->assertOk()
            ->assertJsonPath('city', 'Jakarta')
            ->assertJsonStructure(['rows' => [['code', 'name', 'price']]]);
    }

    public function test_store_saves_down_payment_and_item_status(): void
    {
        $admin = User::where('role', 'admin')->firstOrFail();

        $this->actingAs($admin)->post(route('admin.orders.store'), [
            'customer_name' => 'Pelanggan DP',
            'customer_whatsapp' => '081200006667',
            'customer_address' => 'Jl. DP 1',
            'shipping_city' => 'Jakarta',
            'shipping_method' => 'JNE Reguler',
            'shipping_cost' => 15000,
            'fee' => 2500,
            'down_payment' => 20000,
            'items' => [
                ['product_name' => 'Barang PO', 'unit' => 'pcs', 'quantity' => 1, 'product_price' => 100000, 'status' => 'po'],
                ['product_name' => 'Barang Ready', 'unit' => 'pcs', 'quantity' => 1, 'product_price' => 50000, 'status' => 'ready'],
            ],
        ])->assertSessionHasNoErrors();

        $order = Order::where('customer_whatsapp', '081200006667')->firstOrFail();
        $this->assertSame(150_000, $order->subtotal);
        $this->assertSame(167_500, $order->total);
        $this->assertSame(20_000, $order->down_payment);
        $this->assertSame(147_500, $order->remaining);
        $this->assertSame('Jakarta', $order->city);
        $this->assertSame('po', $order->items()->where('product_name', 'Barang PO')->firstOrFail()->status);
        $this->assertSame('ready', $order->items()->where('product_name', 'Barang Ready')->firstOrFail()->status);

        $this->actingAs($admin)
            ->get(route('admin.orders.invoice', $order))
            ->assertOk()
            ->assertSee('Deposit Masuk')
            ->assertSee('Sisa Bayar')
            ->assertSee('Rp 147.500')
            ->assertSee('PO');
    }

    public function test_store_rejects_down_payment_above_total(): void
    {
        $admin = User::where('role', 'admin')->firstOrFail();

        $this->actingAs($admin)->post(route('admin.orders.store'), [
            'customer_name' => 'Pelanggan DP Lebih',
            'customer_whatsapp' => '081200006668',
            'customer_address' => 'Jl. DP 2',
            'down_payment' => 999999999,
            'items' => [
                ['product_name' => 'Barang D', 'quantity' => 1, 'product_price' => 50000],
            ],
        ])->assertSessionHasErrors('down_payment');
    }

    public function test_store_requires_address(): void
    {
        $admin = User::where('role', 'admin')->firstOrFail();

        $this->actingAs($admin)->post(route('admin.orders.store'), [
            'customer_name' => 'Tanpa Alamat',
            'customer_whatsapp' => '081200006669',
            'items' => [
                ['product_name' => 'Barang E', 'quantity' => 1, 'product_price' => 50000],
            ],
        ])->assertSessionHasErrors('customer_address');
    }

    public function test_invoice_index_filters_by_status_and_search(): void
    {
        $admin = User::where('role', 'admin')->firstOrFail();

        Order::create([
            'order_number' => 'NDTEST-MANUAL-OK', 'customer_name' => 'Cari Ini',
            'customer_whatsapp' => '081200004321', 'customer_address' => 'Jl. A',
            'subtotal' => 1000, 'shipping_cost' => 0, 'total' => 1000,
            'status' => Order::STATUS_AWAITING_PAYMENT, 'payment_method' => 'manual',
        ]);
        Order::create([
            'order_number' => 'NDTEST-MANUAL-LUNAS', 'customer_name' => 'Sudah Bayar',
            'customer_whatsapp' => '081200004322', 'customer_address' => 'Jl. B',
            'subtotal' => 1000, 'shipping_cost' => 0, 'total' => 1000,
            'status' => Order::STATUS_CONFIRMED, 'paid_at' => now(), 'payment_method' => 'manual',
        ]);

        $this->actingAs($admin)
            ->get(route('admin.invoices.index', ['search' => 'Cari Ini']))
            ->assertOk()
            ->assertSee('NDTEST-MANUAL-OK')
            ->assertDontSee('NDTEST-MANUAL-LUNAS');

        $this->actingAs($admin)
            ->get(route('admin.invoices.index', ['status' => Order::STATUS_CONFIRMED]))
            ->assertOk()
            ->assertSee('NDTEST-MANUAL-LUNAS')
            ->assertDontSee('NDTEST-MANUAL-OK');
    }
}
