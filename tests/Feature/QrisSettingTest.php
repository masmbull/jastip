<?php

namespace Tests\Feature;

use App\Models\Order;
use App\Models\Setting;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class QrisSettingTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed();
    }

    private function makeOrder(): Order
    {
        return Order::create([
            'order_number' => 'QG-0001',
            'customer_name' => 'Pelanggan QRIS',
            'customer_whatsapp' => '081200007777',
            'customer_address' => 'Jl. Contoh 2',
            'shipping_method' => 'Ambil Sendiri',
            'subtotal' => 50_000,
            'shipping_cost' => 0,
            'total' => 50_000,
            'status' => Order::STATUS_AWAITING_PAYMENT,
            'payment_method' => 'manual',
        ]);
    }

    private function admin(): User
    {
        return User::where('role', 'admin')->firstOrFail();
    }

    private function setQrisEnabled(bool $enabled): void
    {
        Setting::set('qris_enabled', $enabled, 'boolean', 'qris');
    }

    public function test_invoice_prints_qris_block_when_enabled(): void
    {
        $order = $this->makeOrder();

        $this->actingAs($this->admin())
            ->get(route('admin.orders.invoice', $order))
            ->assertOk()
            ->assertSee('CARA PEMBAYARAN')->assertSee('>QRIS<', false);
    }

    public function test_invoice_hides_qris_block_when_disabled(): void
    {
        $order = $this->makeOrder();
        $this->setQrisEnabled(false);

        $this->actingAs($this->admin())
            ->get(route('admin.orders.invoice', $order))
            ->assertOk()
            ->assertDontSee('Bayar via QRIS');
    }

    public function test_order_detail_hides_qris_panel_when_disabled(): void
    {
        $order = $this->makeOrder();
        $this->setQrisEnabled(false);

        $this->actingAs($this->admin())
            ->get(route('admin.orders.show', $order))
            ->assertOk()
            ->assertDontSee('QRIS yang digunakan saat checkout');
    }

    public function test_payment_waiting_page_flips_copy_with_the_switch(): void
    {
        $order = $this->makeOrder();

        $this->actingAs($this->admin())
            ->get(route('payment.waiting', $order))
            ->assertOk()
            ->assertSee('Petunjuk Pembayaran')
            ->assertDontSee('Menunggu Konfirmasi Admin');

        $this->setQrisEnabled(false);

        $this->actingAs($this->admin())
            ->get(route('payment.waiting', $order))
            ->assertOk()
            ->assertSee('Menunggu Konfirmasi Admin')
            ->assertDontSee('Petunjuk Pembayaran');
    }

    public function test_qris_download_is_refused_when_disabled(): void
    {
        $order = $this->makeOrder();
        $this->setQrisEnabled(false);

        $this->actingAs($this->admin())
            ->from(route('admin.orders.show', $order))
            ->get(route('payment.download-qris', $order))
            ->assertRedirect(route('admin.orders.show', $order))
            ->assertSessionHas('error');
    }
}
