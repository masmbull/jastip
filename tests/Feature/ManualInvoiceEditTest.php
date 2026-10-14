<?php

namespace Tests\Feature;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ManualInvoiceEditTest extends TestCase
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

    private function makeOrder(): Order
    {
        $order = Order::create([
            'order_number' => 'EDIT-0001',
            'customer_name' => 'Pelanggan Edit',
            'customer_whatsapp' => '081200005555',
            'customer_address' => 'Jl. Edit 1',
            'city' => 'Jakarta',
            'shipping_method' => 'Ambil Sendiri',
            'subtotal' => 60_000,
            'shipping_cost' => 5_000,
            'fee' => 2_500,
            'down_payment' => 20_000,
            'total' => 67_500,
            'status' => Order::STATUS_AWAITING_PAYMENT,
            'payment_method' => 'manual',
        ]);

        OrderItem::create([
            'order_id' => $order->id,
            'product_name' => 'Barang Lama',
            'unit' => 'pcs',
            'quantity' => 2,
            'product_price' => 30_000,
            'subtotal' => 60_000,
            'status' => OrderItem::STATUS_READY,
        ]);

        return $order;
    }

    private function payload(array $overrides = []): array
    {
        return array_merge([
            'customer_name' => 'Pelanggan Edit',
            'customer_whatsapp' => '081200005555',
            'customer_address' => 'Jl. Edit 1',
            'shipping_cost' => 0,
            'fee' => 0,
            'down_payment' => 0,
            'items' => [
                ['product_name' => 'Barang Baru', 'unit' => 'pcs', 'quantity' => 3, 'product_price' => 10_000],
            ],
        ], $overrides);
    }

    public function test_edit_page_is_prefilled_with_existing_invoice(): void
    {
        $order = $this->makeOrder();

        $this->actingAs($this->admin())
            ->get(route('admin.orders.edit', $order))
            ->assertOk()
            ->assertSee('Barang Lama')
            ->assertSee('Pelanggan Edit')
            ->assertSee('Simpan Perubahan');
    }

    public function test_update_recalculates_total_and_replaces_items(): void
    {
        $order = $this->makeOrder();

        $this->actingAs($this->admin())
            ->put(route('admin.orders.update', $order), $this->payload())
            ->assertRedirect(route('admin.orders.show', $order));

        $order->refresh();

        $this->assertSame(30_000, $order->subtotal);
        $this->assertSame(0, $order->shipping_cost);
        $this->assertSame(30_000, $order->total);
        $this->assertSame(1, $order->items()->count());
        $this->assertSame('Barang Baru', $order->items()->first()->product_name);
        $this->assertSame(Order::STATUS_AWAITING_PAYMENT, $order->status);
    }

    public function test_update_rejects_total_below_recorded_payments(): void
    {
        $order = $this->makeOrder();
        $order->payments()->create(['amount' => 50_000, 'paid_at' => now()]);

        $this->actingAs($this->admin())
            ->from(route('admin.orders.edit', $order))
            ->put(route('admin.orders.update', $order), $this->payload())
            ->assertSessionHasErrors('items');

        $this->assertSame(67_500, $order->refresh()->total);
    }

    public function test_rekapan_shows_edit_button(): void
    {
        $order = $this->makeOrder();

        $this->actingAs($this->admin())
            ->get(route('admin.invoices.index'))
            ->assertOk()
            ->assertSee(route('admin.orders.edit', $order), false);
    }
}
