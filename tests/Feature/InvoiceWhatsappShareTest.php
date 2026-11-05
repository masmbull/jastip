<?php

namespace Tests\Feature;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class InvoiceWhatsappShareTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;

    private Order $order;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed();

        $this->admin = User::where('role', 'admin')->firstOrFail();

        $this->order = Order::create([
            'order_number' => 'NDTEST-WA001',
            'customer_name' => 'Siti Rahma',
            'customer_whatsapp' => '081234567890',
            'customer_address' => 'Jl. Asia Afrika No. 1, Bandung',
            'shipping_method' => 'JNE',
            'subtotal' => 200000,
            'shipping_cost' => 15000,
            'total' => 215000,
            'status' => Order::STATUS_AWAITING_PAYMENT,
            'payment_method' => 'manual',
        ]);

        OrderItem::create([
            'order_id' => $this->order->id,
            'product_name' => 'Kaos',
            'product_price' => 100000,
            'quantity' => 2,
            'unit' => 'pcs',
            'subtotal' => 200000,
        ]);
    }

    public function test_invoice_list_offers_browser_sharing_with_customer_caption(): void
    {
        $this->actingAs($this->admin)
            ->get(route('admin.invoices.index'))
            ->assertOk()
            ->assertSee('data-invoice-share', false)
            ->assertSee('data-share-url="'.route('invoice.html', $this->order->public_token).'"', false)
            ->assertSee('https://wa.me/6281234567890?text=', false)
            ->assertSee('Halo Siti Rahma, berikut invoice NDTEST-WA001', false)
            ->assertSee('BCA 2700398369 a/n Nabila Adriyana')
            ->assertSee('SeaBank 901499244727 a/n Nabila Adriyana')
            ->assertSee('BluBCA 001081568345 a/n Nabila Adriyana')
            ->assertSee('Pilih WhatsApp dan kontak pelanggan', false)
            ->assertSee('data-share-spinner', false)
            ->assertDontSee('send-wa')
            ->assertDontSee('wa_gateway');
    }

    public function test_public_invoice_image_source_renders_for_browser_sharing(): void
    {
        $this->get(route('invoice.html', $this->order->public_token))
            ->assertOk()
            ->assertSee('class="invoice-doc"', false)
            ->assertSee('Siti Rahma')
            ->assertSee('NDTEST-WA001');
    }

    public function test_settings_no_longer_expose_gateway_configuration(): void
    {
        $this->actingAs($this->admin)
            ->get(route('admin.settings.index'))
            ->assertOk()
            ->assertDontSee('wa_gateway')
            ->assertDontSee('Gateway WhatsApp');
    }

    public function test_gateway_send_route_is_removed(): void
    {
        $this->actingAs($this->admin)
            ->post('/admin/invoice/'.$this->order->id.'/kirim-wa')
            ->assertNotFound();
    }
}
