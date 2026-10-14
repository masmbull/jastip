<?php

namespace Tests\Feature;

use App\Models\Order;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class InvoiceImageTest extends TestCase
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
            'order_number' => 'NDTEST-IMG01',
            'customer_name' => 'Gambar Pelanggan',
            'customer_whatsapp' => '081200001234',
            'customer_address' => 'Jl. Gambar 1',
            'subtotal' => 150000,
            'shipping_cost' => 20000,
            'total' => 170000,
            'status' => Order::STATUS_AWAITING_PAYMENT,
            'payment_method' => 'manual',
        ]);

        $order->items()->create([
            'product_name' => 'Produk Gambar',
            'product_price' => 75000,
            'quantity' => 2,
            'unit' => 'pcs',
            'subtotal' => 150000,
        ]);

        return $order;
    }

    public function test_invoice_image_renders_hex_only_canvas(): void
    {
        $admin = User::where('role', 'admin')->firstOrFail();
        $order = $this->makeOrder();

        $res = $this->actingAs($admin)->get(route('admin.orders.invoice-image', $order));
        $res->assertOk();

        // Target render html2canvas harus ada.
        $res->assertSee('id="invoice-canvas"', false);

        // Data invoice ikut tampil.
        $res->assertSee('Gambar Pelanggan');
        $res->assertSee('Produk Gambar');
        $res->assertSee('Rp 170.000');
        $res->assertSee('BELUM BAYAR');

        // Tidak boleh ada utility Tailwind (oklch) di dokumen render gambar.
        $res->assertDontSee('class="bg-', false);
        $res->assertDontSee('dark:', false);
    }

    public function test_non_admin_cannot_render_invoice_image(): void
    {
        $customer = User::factory()->create(['role' => 'customer']);
        $order = $this->makeOrder();

        $this->actingAs($customer)
            ->get(route('admin.orders.invoice-image', $order))
            ->assertForbidden();
    }
}
