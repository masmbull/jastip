<?php

namespace Tests\Feature;

use App\Models\Order;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\URL;
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

    public function test_signed_pdf_url_returns_pdf_without_login(): void
    {
        $order = $this->makeOrder();

        $url = URL::temporarySignedRoute('invoice.pdf', now()->addDays(30), ['order' => $order->id]);

        $res = $this->get($url);
        $res->assertOk();
        $res->assertHeader('Content-Type', 'application/pdf');
        $this->assertStringStartsWith('%PDF-', $res->getContent());
    }

    public function test_pdf_url_without_valid_signature_is_rejected(): void
    {
        $order = $this->makeOrder();

        $this->get(route('invoice.pdf', ['order' => $order->id]))->assertForbidden();
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
