<?php

namespace Tests\Feature;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class InvoiceTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed();
    }

    public function test_invoice_shows_order_fields_and_payment_instructions(): void
    {
        $admin = User::where('role', 'admin')->firstOrFail();
        $order = Order::create([
            'order_number' => 'NDTEST-INV001',
            'customer_name' => 'Budi Santoso',
            'customer_whatsapp' => '081234567890',
            'customer_address' => 'Jl. Merdeka No. 10, Bandung',
            'shipping_method' => 'JNE Reguler',
            'subtotal' => 100000,
            'shipping_cost' => 15000,
            'fee' => 2500,
            'total' => 117500,
            'status' => Order::STATUS_AWAITING_PAYMENT,
        ]);
        OrderItem::create([
            'order_id' => $order->id,
            'product_name' => 'Tas Kulit',
            'product_price' => 100000,
            'unit' => 'pcs',
            'quantity' => 1,
            'subtotal' => 100000,
        ]);

        $this->actingAs($admin)
            ->get(route('admin.orders.invoice', $order))
            ->assertOk()
            ->assertSee('INVOICE')
            ->assertSee('Budi Santoso')
            ->assertSee('081234567890')
            ->assertSee('Jl. Merdeka No. 10, Bandung')
            ->assertSee('JNE Reguler')
            ->assertSee('Tas Kulit')
            ->assertSee('Rp 117.500')
            ->assertSee('BELUM BAYAR')
            ->assertSee('Bayar via QRIS') // unpaid => payment branch
            ->assertSee('img/invoice/rekening.webp'); // rekening image wired
    }

    public function test_paid_invoice_shows_received_banner(): void
    {
        $admin = User::where('role', 'admin')->firstOrFail();
        $order = Order::create([
            'order_number' => 'NDTEST-INV002',
            'customer_name' => 'Sari',
            'customer_whatsapp' => '081200000001',
            'customer_address' => 'Jl. Mawar 2',
            'shipping_method' => 'SiCepat',
            'subtotal' => 50000,
            'shipping_cost' => 8000,
            'total' => 58000,
            'status' => Order::STATUS_CONFIRMED,
            'paid_at' => now(),
        ]);

        $this->actingAs($admin)
            ->get(route('admin.orders.invoice', $order))
            ->assertOk()
            ->assertSee('LUNAS')
            ->assertSee('Pembayaran diterima')
            ->assertSee('img/invoice/payment-diterima.jpeg')
            ->assertDontSee('Bayar via QRIS');
    }

    public function test_non_admin_cannot_view_invoice(): void
    {
        $order = Order::create([
            'order_number' => 'NDTEST-INV003',
            'customer_name' => 'X',
            'customer_whatsapp' => '081200000002',
            'customer_address' => 'Y',
            'subtotal' => 1000,
            'shipping_cost' => 0,
            'total' => 1000,
            'status' => Order::STATUS_PENDING,
        ]);

        $customer = User::factory()->create(['role' => 'customer']);
        $this->actingAs($customer)
            ->get(route('admin.orders.invoice', $order))
            ->assertForbidden();
    }
}