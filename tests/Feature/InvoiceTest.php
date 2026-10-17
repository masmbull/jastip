<?php

namespace Tests\Feature;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Setting;
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
            ->assertSee('CARA PEMBAYARAN') // unpaid => blok pembayaran
            ->assertSee('TRANSFER HANYA KE REKENING / QRIS DI ATAS');
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
            ->assertSee('Pembayaran sudah diterima')
            ->assertDontSee('TRANSFER HANYA KE REKENING / QRIS DI ATAS');
    }

    public function test_qris_hidden_when_total_above_limit(): void
    {
        Setting::set('qris_enabled', true, 'boolean', 'qris', true);
        Setting::set('qris_max_amount', 500000, 'integer', 'qris', true);
        Setting::set('bank_name', 'BCA', 'string', 'rekening', true);
        Setting::set('bank_account_number', '1234567890', 'string', 'rekening', true);
        Setting::set('bank_account_name', 'Nabila', 'string', 'rekening', true);

        $admin = User::where('role', 'admin')->firstOrFail();
        $order = Order::create([
            'order_number' => 'NDTEST-QRIS-BIG',
            'customer_name' => 'Besar',
            'customer_whatsapp' => '081200000009',
            'customer_address' => 'Jl. Besar',
            'subtotal' => 600000,
            'shipping_cost' => 0,
            'total' => 600000,
            'status' => Order::STATUS_PENDING,
        ]);

        $this->actingAs($admin)
            ->get(route('admin.orders.invoice', $order))
            ->assertOk()
            ->assertDontSee('>QRIS<', false)
            ->assertSee('TRANSFER REKENING')
            ->assertSee('BCA')
            ->assertSee('1234567890')
            ->assertSee('Nabila');
    }

    public function test_qris_shown_when_total_within_limit(): void
    {
        Setting::set('qris_enabled', true, 'boolean', 'qris', true);
        Setting::set('qris_max_amount', 500000, 'integer', 'qris', true);

        $admin = User::where('role', 'admin')->firstOrFail();
        $order = Order::create([
            'order_number' => 'NDTEST-QRIS-SMALL',
            'customer_name' => 'Kecil',
            'customer_whatsapp' => '081200000010',
            'customer_address' => 'Jl. Kecil',
            'subtotal' => 100000,
            'shipping_cost' => 0,
            'total' => 100000,
            'status' => Order::STATUS_PENDING,
        ]);

        $this->actingAs($admin)
            ->get(route('admin.orders.invoice', $order))
            ->assertOk()
            ->assertSee('>QRIS<', false);
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

    public function test_embed_mode_hides_toolbar_for_modal(): void
    {
        $admin = User::where('role', 'admin')->firstOrFail();
        $order = Order::create([
            'order_number' => 'NDTEST-INV004',
            'customer_name' => 'Embed',
            'customer_whatsapp' => '081200000003',
            'customer_address' => 'Z',
            'subtotal' => 1000,
            'shipping_cost' => 0,
            'total' => 1000,
            'status' => Order::STATUS_AWAITING_PAYMENT,
        ]);

        // Full page: back link + print button present.
        $this->actingAs($admin)
            ->get(route('admin.orders.invoice', $order))
            ->assertOk()
            ->assertSee('Kembali ke pesanan');

        // Embed (modal iframe): toolbar stripped, invoice body still rendered.
        $this->actingAs($admin)
            ->get(route('admin.orders.invoice', $order).'?embed=1')
            ->assertOk()
            ->assertDontSee('Kembali ke pesanan')
            ->assertSee('INVOICE');
    }
}
