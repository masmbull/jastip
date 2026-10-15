<?php

namespace Tests\Feature;

use App\Models\Order;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class InvoicePdfTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed();
    }

    private function makeOrder(): Order
    {
        $order = Order::create([
            'order_number' => 'NDTEST-PDF01',
            'customer_name' => 'Pelanggan PDF',
            'customer_whatsapp' => '081200009999',
            'customer_address' => 'Jl. PDF 1',
            'subtotal' => 150000,
            'shipping_cost' => 20000,
            'total' => 170000,
            'status' => Order::STATUS_AWAITING_PAYMENT,
            'payment_method' => 'manual',
        ]);

        $order->items()->create([
            'product_name' => 'Produk PDF',
            'product_price' => 75000,
            'quantity' => 2,
            'unit' => 'pcs',
            'subtotal' => 150000,
        ]);

        return $order;
    }

    public function test_order_gets_a_public_token_automatically(): void
    {
        $order = $this->makeOrder();

        $this->assertNotEmpty($order->public_token);
        $this->assertSame(16, strlen($order->public_token));
        $this->assertStringContainsString('/i/'.$order->public_token, $order->public_pdf_url);
    }

    public function test_public_token_url_returns_pdf_without_login(): void
    {
        $order = $this->makeOrder();

        $res = $this->get($order->public_pdf_url);
        $res->assertOk();
        $res->assertHeader('Content-Type', 'application/pdf');
        $this->assertStringStartsWith('%PDF-', $res->getContent());
    }

    public function test_unknown_token_returns_404(): void
    {
        $this->get(route('invoice.public', ['token' => 'abcdefghijklmnop']))->assertNotFound();
    }

    public function test_short_link_is_short(): void
    {
        $order = $this->makeOrder();

        // Link pendek: {app}/i/{16 char} - jauh lebih rapi dari signed URL.
        $this->assertLessThan(80, strlen($order->public_pdf_url));
    }

    public function test_dashboard_admin_still_renders_invoice_page(): void
    {
        $admin = User::where('role', 'admin')->firstOrFail();
        $order = $this->makeOrder();

        $this->actingAs($admin)
            ->get(route('admin.orders.invoice', $order))
            ->assertOk();
    }
}
