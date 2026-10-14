<?php

namespace Tests\Feature;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class InvoiceModeTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed();
    }

    private function admin(): User
    {
        return User::where('role', 'admin')->firstOrFail();
    }

    private function payload(array $overrides = []): array
    {
        return array_merge([
            'customer_name' => 'Pelanggan Mode',
            'customer_whatsapp' => '081200003333',
            'customer_address' => 'Jl. Mode 1',
            'shipping_cost' => 0,
            'fee' => 0,
            'down_payment' => 0,
            'mode' => 'lunas',
            'items' => [
                ['product_name' => 'Barang Mode', 'unit' => 'pcs', 'quantity' => 1, 'product_price' => 100_000],
            ],
        ], $overrides);
    }

    public function test_deposit_mode_requires_a_deposit_value(): void
    {
        $this->actingAs($this->admin())
            ->from(route('admin.orders.create'))
            ->post(route('admin.orders.store'), $this->payload(['mode' => 'deposit', 'down_payment' => 0]))
            ->assertSessionHasErrors('down_payment');

        // Seeder sudah punya pesanan, jadi yang dicek cuma pesanan ini memang
        // tidak boleh terlanjur masuk.
        $this->assertDatabaseMissing('orders', ['customer_whatsapp' => '081200003333']);
    }

    public function test_deposit_mode_accepts_a_deposit_value(): void
    {
        $this->actingAs($this->admin())
            ->post(route('admin.orders.store'), $this->payload(['mode' => 'deposit', 'down_payment' => 40_000]));

        $order = Order::where('customer_whatsapp', '081200003333')->firstOrFail();

        $this->assertSame(40_000, $order->down_payment);
        $this->assertSame(100_000, $order->total);
        $this->assertSame(60_000, $order->remaining);
    }

    public function test_lunas_mode_forces_deposit_to_zero(): void
    {
        $this->actingAs($this->admin())
            ->post(route('admin.orders.store'), $this->payload(['mode' => 'lunas', 'down_payment' => 40_000]));

        $order = Order::where('customer_whatsapp', '081200003333')->firstOrFail();

        $this->assertSame(0, $order->down_payment);
        $this->assertSame(100_000, $order->remaining);
    }

    public function test_printed_document_title_matches_the_mode(): void
    {
        $deposit = Order::create([
            'order_number' => 'MODE-DP-1',
            'customer_name' => 'Pelanggan DP',
            'customer_whatsapp' => '081200001111',
            'customer_address' => 'Jl. DP 9',
            'subtotal' => 100_000,
            'shipping_cost' => 0,
            'total' => 100_000,
            'down_payment' => 40_000,
            'status' => Order::STATUS_AWAITING_PAYMENT,
            'payment_method' => 'manual',
        ]);

        $lunas = Order::create([
            'order_number' => 'MODE-LUNAS-1',
            'customer_name' => 'Pelanggan Lunas',
            'customer_whatsapp' => '081200002222',
            'customer_address' => 'Jl. Lunas 9',
            'subtotal' => 100_000,
            'shipping_cost' => 0,
            'total' => 100_000,
            'down_payment' => 0,
            'status' => Order::STATUS_AWAITING_PAYMENT,
            'payment_method' => 'manual',
        ]);

        $admin = $this->admin();

        $this->actingAs($admin)
            ->get(route('admin.orders.invoice', $deposit))
            ->assertOk()
            ->assertSee('TAGIHAN DP');

        $this->actingAs($admin)
            ->get(route('admin.orders.invoice', $lunas))
            ->assertOk()
            ->assertSee('INVOICE')
            ->assertDontSee('TAGIHAN DP');
    }

    public function test_rekapan_offers_both_invoice_modes(): void
    {
        $this->actingAs($this->admin())
            ->get(route('admin.invoices.index'))
            ->assertOk()
            ->assertSee('Buat Invoice Deposit')
            ->assertSee('Buat Invoice Lunas');
    }
}
