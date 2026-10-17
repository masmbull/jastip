<?php

namespace Tests\Feature;

use App\Models\Order;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class OrderPaymentTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed();
    }

    private function makeOrder(array $over = []): Order
    {
        return Order::create(array_merge([
            'order_number' => 'NDTEST-PAY-'.uniqid(),
            'customer_name' => 'Bayar Tester',
            'customer_whatsapp' => '08120000'.random_int(1000, 9999),
            'customer_address' => 'Jl. Bayar 1',
            'subtotal' => 100000,
            'shipping_cost' => 10000,
            'fee' => 5000,
            'down_payment' => 20000,
            'total' => 115000,
            'status' => Order::STATUS_AWAITING_PAYMENT,
            'payment_method' => 'manual',
        ], $over));
    }

    public function test_store_payment_reduces_remaining(): void
    {
        $admin = User::where('role', 'admin')->firstOrFail();
        $order = $this->makeOrder();

        $this->actingAs($admin)->post(route('admin.orders.payments.store', $order), [
            'amount' => 50000,
            'note' => 'Transfer BCA',
        ])->assertSessionHasNoErrors();

        $order->refresh();
        $this->assertSame(70000, $order->totalPaid);
        $this->assertSame(45000, $order->remaining);
        $this->assertSame('dp', $order->payment_status);

        $this->actingAs($admin)
            ->get(route('admin.orders.invoice', $order))
            ->assertOk()
            ->assertSee('Rp 45.000')
            ->assertSee('Sisa');
    }

    public function test_store_payment_rejects_overpay(): void
    {
        $admin = User::where('role', 'admin')->firstOrFail();
        $order = $this->makeOrder();

        $this->actingAs($admin)->post(route('admin.orders.payments.store', $order), [
            'amount' => 999999999,
        ])->assertSessionHasErrors('amount');
    }

    public function test_full_payment_auto_marks_lunas(): void
    {
        $admin = User::where('role', 'admin')->firstOrFail();
        $order = $this->makeOrder();

        $this->actingAs($admin)->post(route('admin.orders.payments.store', $order), [
            'amount' => 95000,
        ])->assertSessionHasNoErrors();

        $order->refresh();
        $this->assertSame(0, $order->remaining);
        $this->assertTrue($order->is_paid);
        $this->assertSame(Order::STATUS_CONFIRMED, $order->status);
        $this->assertSame('lunas', $order->payment_status);
    }

    public function test_destroy_payment_restores_remaining(): void
    {
        $admin = User::where('role', 'admin')->firstOrFail();
        $order = $this->makeOrder();
        $pay = $order->payments()->create(['amount' => 30000, 'paid_at' => now()]);

        $this->actingAs($admin)
            ->delete(route('admin.orders.payments.destroy', [$order, $pay]))
            ->assertSessionHasNoErrors();

        $this->assertSame(95000, $order->fresh()->remaining);
    }

    public function test_ledger_summary_and_pay_status_filter(): void
    {
        $admin = User::where('role', 'admin')->firstOrFail();

        $a = $this->makeOrder(['order_number' => 'NDTEST-LED-A', 'customer_whatsapp' => '081200001111']);
        $b = $this->makeOrder(['order_number' => 'NDTEST-LED-B', 'customer_whatsapp' => '081200001111', 'down_payment' => 0]);
        $c = $this->makeOrder(['order_number' => 'NDTEST-LED-C', 'customer_whatsapp' => '081200002222', 'down_payment' => 0, 'paid_at' => now(), 'status' => Order::STATUS_CONFIRMED]);

        $res = $this->actingAs($admin)->get(route('admin.invoices.index'));
        $res->assertOk()
            ->assertSee('NDTEST-LED-A')
            ->assertSee('NDTEST-LED-B')
            ->assertSee('NDTEST-LED-C');

        $this->actingAs($admin)
            ->get(route('admin.invoices.index', ['pay_status' => 'belum']))
            ->assertOk()
            ->assertDontSee('NDTEST-LED-A')
            ->assertSee('NDTEST-LED-B')
            ->assertDontSee('NDTEST-LED-C');

        $this->actingAs($admin)
            ->get(route('admin.invoices.index', ['pay_status' => 'dp']))
            ->assertOk()
            ->assertSee('NDTEST-LED-A')
            ->assertDontSee('NDTEST-LED-B')
            ->assertDontSee('NDTEST-LED-C');

        $this->actingAs($admin)
            ->get(route('admin.invoices.index', ['pay_status' => 'lunas']))
            ->assertOk()
            ->assertDontSee('NDTEST-LED-A')
            ->assertDontSee('NDTEST-LED-B')
            ->assertSee('NDTEST-LED-C');
    }

    public function test_export_csv_downloads_filtered_rows(): void
    {
        $admin = User::where('role', 'admin')->firstOrFail();
        $this->makeOrder(['order_number' => 'NDTEST-CSV-1']);

        $res = $this->actingAs($admin)->get(route('admin.invoices.export'));
        $res->assertOk();
        $this->assertStringContainsString('text/csv', $res->headers->get('Content-Type'));
        $this->assertStringContainsString('NDTEST-CSV-1', $res->streamedContent());
    }

    public function test_non_admin_cannot_record_payment(): void
    {
        $order = $this->makeOrder();
        $customer = User::factory()->create(['role' => 'customer']);

        $this->actingAs($customer)
            ->post(route('admin.orders.payments.store', $order), ['amount' => 1000])
            ->assertForbidden();
    }
}
