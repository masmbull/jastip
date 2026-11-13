<?php

namespace Tests\Feature;

use App\Models\Address;
use App\Models\Order;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ManualInvoiceTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed();
    }

    public function test_create_page_loads(): void
    {
        $admin = User::where('role', 'admin')->firstOrFail();

        $this->actingAs($admin)
            ->get(route('admin.orders.create'))
            ->assertOk()
            ->assertSee('Buat Invoice')
            ->assertSee('name="customer_user_id"', false);
    }

    public function test_invoice_can_select_and_link_an_existing_customer(): void
    {
        $admin = User::where('role', 'admin')->firstOrFail();
        $customer = User::factory()->create([
            'name' => 'Pelanggan Terdaftar',
            'email' => 'terdaftar@example.com',
            'role' => 'customer',
        ]);
        $customer->profile()->updateOrCreate(
            ['user_id' => $customer->id],
            ['phone' => '08123456789'],
        );
        Address::create([
            'user_id' => $customer->id,
            'full_name' => $customer->name,
            'phone' => '08123456789',
            'street_address' => 'Jl. Contoh 10',
            'city' => 'Jakarta',
            'province' => 'DKI Jakarta',
            'postal_code' => '12345',
            'is_default' => true,
        ]);

        $this->actingAs($admin)
            ->get(route('admin.orders.create'))
            ->assertOk()
            ->assertSee('value="'.$customer->id.'"', false)
            ->assertSee('data-name="Pelanggan Terdaftar"', false)
            ->assertSee('data-city="Jakarta"', false)
            ->assertSee('data-address="Jl. Contoh 10, Jakarta, DKI Jakarta, 12345"', false);

        $this->actingAs($admin)
            ->get(route('admin.invoices.index'))
            ->assertOk()
            ->assertSee('value="'.$customer->id.'"', false);

        $this->actingAs($admin)->post(route('admin.orders.store'), [
            'customer_user_id' => $customer->id,
            'customer_name' => $customer->name,
            'customer_whatsapp' => '08123456789',
            'customer_email' => $customer->email,
            'customer_address' => 'Jl. Contoh 10, Jakarta, DKI Jakarta, 12345',
            'items' => [
                ['product_name' => 'Barang Pilihan', 'quantity' => 1, 'product_price' => 50000],
            ],
        ])->assertRedirect(route('admin.invoices.index'))
            ->assertSessionHasNoErrors();

        $order = Order::where('customer_whatsapp', '08123456789')->firstOrFail();
        $this->assertSame($customer->id, $order->user_id);
        $this->assertSame($customer->name, $order->customer_name);
    }

    public function test_store_creates_order_with_items_and_redirects_to_list(): void
    {
        $admin = User::where('role', 'admin')->firstOrFail();

        $response = $this->actingAs($admin)->post(route('admin.orders.store'), [
            'customer_name' => 'Pelanggan Manual',
            'customer_whatsapp' => '081200009999',
            'customer_address' => 'Jl. Contoh 1',
            'shipping_cost' => 10000,
            'fee' => 2500,
            'items' => [
                ['product_name' => 'Barang A', 'unit' => 'pcs', 'quantity' => 2, 'product_price' => 30000],
                ['product_name' => 'Barang B', 'unit' => 'box', 'quantity' => 1, 'product_price' => 45000],
            ],
        ]);

        $order = Order::where('customer_whatsapp', '081200009999')->firstOrFail();

        $response->assertRedirect(route('admin.invoices.index'));
        $this->assertSame(60_000 + 45_000, $order->subtotal);
        $this->assertSame(10_000, $order->shipping_cost);
        $this->assertSame(2_500, $order->fee);
        $this->assertSame(117_500, $order->total);
        $this->assertSame(Order::STATUS_AWAITING_PAYMENT, $order->status);
        $this->assertSame('manual', $order->payment_method);
        $this->assertSame(2, $order->items()->count());
        $this->assertTrue($order->show_fee);

        $this->actingAs($admin)
            ->get(route('admin.orders.invoice', $order))
            ->assertOk()
            ->assertSee('Fee Jastip');
    }

    public function test_mark_paid_sets_paid_and_confirmed_status(): void
    {
        $admin = User::where('role', 'admin')->firstOrFail();

        $this->actingAs($admin)->post(route('admin.orders.store'), [
            'customer_name' => 'Pelanggan Lunas',
            'customer_whatsapp' => '081200008888',
            'customer_address' => 'Jl. Lunas 1',
            'mark_paid' => '1',
            'items' => [
                ['product_name' => 'Barang C', 'quantity' => 1, 'product_price' => 50000],
            ],
        ])->assertSessionHasNoErrors();

        $order = Order::where('customer_whatsapp', '081200008888')->firstOrFail();
        $this->assertTrue($order->is_paid);
        $this->assertSame(Order::STATUS_CONFIRMED, $order->status);
    }

    public function test_store_requires_at_least_one_item(): void
    {
        $admin = User::where('role', 'admin')->firstOrFail();

        $this->actingAs($admin)->post(route('admin.orders.store'), [
            'customer_name' => 'Tanpa Item',
            'customer_whatsapp' => '081200007777',
            'items' => [],
        ])->assertSessionHasErrors('items');
    }

    public function test_hidden_fee_stays_in_total_but_is_omitted_from_customer_invoice(): void
    {
        $admin = User::where('role', 'admin')->firstOrFail();

        $this->actingAs($admin)->post(route('admin.orders.store'), [
            'customer_name' => 'Pelanggan Fee Internal',
            'customer_whatsapp' => '081200009991',
            'customer_address' => 'Jl. Fee 1',
            'fee' => 19000,
            'show_fee' => '0',
            'items' => [
                ['product_name' => 'Barang Fee', 'quantity' => 1, 'product_price' => 100000],
            ],
        ])->assertRedirect(route('admin.invoices.index'))
            ->assertSessionHasNoErrors();

        $order = Order::where('customer_whatsapp', '081200009991')->firstOrFail();
        $this->assertSame(119000, $order->total);
        $this->assertSame(19000, $order->fee);
        $this->assertFalse($order->show_fee);

        $this->actingAs($admin)
            ->get(route('admin.orders.invoice', $order))
            ->assertOk()
            ->assertDontSee('Fee Jastip');

        $this->actingAs($admin)
            ->get(route('admin.orders.show', $order))
            ->assertOk()
            ->assertSee('Fee Jastip (internal)')
            ->assertSee('Rp 19.000');
    }

    public function test_item_fees_are_added_once_per_line_and_shown_in_customer_invoice(): void
    {
        $admin = User::where('role', 'admin')->firstOrFail();

        $this->actingAs($admin)->post(route('admin.orders.store'), [
            'customer_name' => 'Pelanggan Fee per Item',
            'customer_whatsapp' => '081200009992',
            'customer_address' => 'Jl. Fee Item',
            'items' => [
                ['product_name' => 'Barang A', 'quantity' => 3, 'product_price' => 65000, 'fee' => 19000],
                ['product_name' => 'Barang B', 'quantity' => 2, 'product_price' => 50000, 'fee' => 5000],
            ],
        ])->assertRedirect(route('admin.invoices.index'))
            ->assertSessionHasNoErrors();

        $order = Order::where('customer_whatsapp', '081200009992')->firstOrFail();
        // Fee per unit x qty: (19000 x 3) + (5000 x 2) = 67000.
        $this->assertSame(67000, $order->fee);
        $this->assertSame(362000, $order->total);
        $this->assertSame(
            [19000, 5000],
            $order->items->sortBy('id')->map(fn ($item) => (int) $item->fee)->values()->all()
        );

        $this->actingAs($admin)
            ->get(route('admin.orders.invoice', $order))
            ->assertOk()
            ->assertSee('Fee item')
            ->assertSee('Rp 57.000')
            ->assertSee('Rp 10.000')
            ->assertSee('Total Fee Jastip');

        $this->actingAs($admin)->put(route('admin.orders.update', $order), [
            'customer_name' => $order->customer_name,
            'customer_whatsapp' => $order->customer_whatsapp,
            'customer_address' => $order->customer_address,
            'show_fee' => '0',
            'items' => [
                ['product_name' => 'Barang A', 'quantity' => 3, 'product_price' => 65000, 'fee' => 19000],
                ['product_name' => 'Barang B', 'quantity' => 2, 'product_price' => 50000, 'fee' => 5000],
            ],
        ])->assertRedirect(route('admin.orders.show', $order))
            ->assertSessionHasNoErrors();

        $order->refresh();
        $this->assertSame(67000, $order->fee);
        $this->assertSame(362000, $order->total);
        $this->assertTrue($order->isPaymentValid());

        $this->actingAs($admin)
            ->get(route('admin.orders.invoice', $order))
            ->assertOk()
            ->assertDontSee('Fee item')
            ->assertDontSee('Rp 19.000')
            ->assertDontSee('Total Fee Jastip');
    }

    public function test_non_admin_cannot_create_invoice(): void
    {
        $customer = User::factory()->create(['role' => 'customer']);

        $this->actingAs($customer)
            ->get(route('admin.orders.create'))
            ->assertForbidden();

        $this->actingAs($customer)
            ->post(route('admin.orders.store'), [])
            ->assertForbidden();
    }

    public function test_invoice_index_lists_only_manual_invoices(): void
    {
        $admin = User::where('role', 'admin')->firstOrFail();

        // Manual invoice (from the admin form).
        Order::create([
            'order_number' => 'NDTEST-MANUAL01',
            'customer_name' => 'Pelanggan Manual',
            'customer_whatsapp' => '081200005555',
            'customer_address' => 'Jl. Manual',
            'subtotal' => 20000,
            'shipping_cost' => 0,
            'total' => 20000,
            'status' => Order::STATUS_AWAITING_PAYMENT,
            'payment_method' => 'manual',
        ]);

        // Regular checkout order — must NOT appear in the invoice list.
        Order::create([
            'order_number' => 'NDTEST-QRIS01',
            'customer_name' => 'Pelanggan Checkout',
            'customer_whatsapp' => '081200004444',
            'customer_address' => 'Jl. Checkout',
            'subtotal' => 30000,
            'shipping_cost' => 0,
            'total' => 30000,
            'status' => Order::STATUS_PENDING,
            'payment_method' => 'qris',
        ]);

        $this->actingAs($admin)
            ->get(route('admin.invoices.index'))
            ->assertOk()
            ->assertSee('NDTEST-MANUAL01')
            ->assertDontSee('NDTEST-QRIS01');
    }

    public function test_product_search_returns_json(): void
    {
        $admin = User::where('role', 'admin')->firstOrFail();

        // Empty term short-circuits to an empty list (no 500).
        $this->actingAs($admin)
            ->getJson(route('admin.orders.product-search', ['q' => '']))
            ->assertOk()
            ->assertExactJson([]);
    }

    public function test_shipping_options_and_quote_return_json(): void
    {
        $admin = User::where('role', 'admin')->firstOrFail();

        $this->actingAs($admin)
            ->getJson(route('admin.orders.shipping-options'))
            ->assertOk()
            ->assertJsonFragment(['Jakarta']);

        $this->actingAs($admin)
            ->getJson(route('admin.orders.shipping-quote', ['city' => 'Jakarta', 'weight' => 1]))
            ->assertOk()
            ->assertJsonPath('city', 'Jakarta')
            ->assertJsonStructure(['rows' => [['code', 'name', 'price']]]);
    }

    public function test_store_saves_down_payment_and_item_status(): void
    {
        $admin = User::where('role', 'admin')->firstOrFail();

        $this->actingAs($admin)->post(route('admin.orders.store'), [
            'customer_name' => 'Pelanggan DP',
            'customer_whatsapp' => '081200006667',
            'customer_address' => 'Jl. DP 1',
            'shipping_city' => 'Jakarta',
            'shipping_method' => 'JNE Reguler',
            'shipping_cost' => 15000,
            'fee' => 2500,
            'down_payment' => 20000,
            'items' => [
                ['product_name' => 'Barang PO', 'unit' => 'pcs', 'quantity' => 1, 'product_price' => 100000, 'status' => 'po'],
                ['product_name' => 'Barang Ready', 'unit' => 'pcs', 'quantity' => 1, 'product_price' => 50000, 'status' => 'ready'],
            ],
        ])->assertSessionHasNoErrors();

        $order = Order::where('customer_whatsapp', '081200006667')->firstOrFail();
        $this->assertSame(150_000, $order->subtotal);
        $this->assertSame(167_500, $order->total);
        $this->assertSame(20_000, $order->down_payment);
        $this->assertSame(147_500, $order->remaining);
        $this->assertSame('Jakarta', $order->city);
        $this->assertSame('po', $order->items()->where('product_name', 'Barang PO')->firstOrFail()->status);
        $this->assertSame('ready', $order->items()->where('product_name', 'Barang Ready')->firstOrFail()->status);

        $this->actingAs($admin)
            ->get(route('admin.orders.invoice', $order))
            ->assertOk()
            ->assertSee('TOTAL BAYAR')
            ->assertSee('Sisa')
            ->assertSee('Rp 147.500')
            ->assertSee('PO');
    }

    public function test_down_payment_is_summed_from_per_item_deposits(): void
    {
        $admin = User::where('role', 'admin')->firstOrFail();

        $this->actingAs($admin)->post(route('admin.orders.store'), [
            'customer_name' => 'Pelanggan Deposit Item',
            'customer_whatsapp' => '081200007778',
            'customer_address' => 'Jl. Deposit',
            'shipping_cost' => 0,
            'mode' => 'deposit',
            // down_payment sengaja TIDAK dikirim: harus dihitung dari item.
            'items' => [
                ['product_name' => 'A', 'unit' => 'pcs', 'quantity' => 1, 'product_price' => 100000, 'deposit' => 30000],
                ['product_name' => 'B', 'unit' => 'pcs', 'quantity' => 2, 'product_price' => 50000, 'deposit' => 20000],
            ],
        ])->assertSessionHasNoErrors();

        $order = Order::where('customer_whatsapp', '081200007778')->firstOrFail();

        // Deposit total = (30.000 × 1) + (20.000 × 2) = 70.000.
        $this->assertSame(70_000, $order->down_payment);
        $this->assertSame(30_000, (int) $order->items()->where('product_name', 'A')->value('deposit'));
        $this->assertSame(20_000, (int) $order->items()->where('product_name', 'B')->value('deposit'));
        $this->assertSame(200_000 - 70_000, $order->remaining);
    }

    public function test_per_unit_deposit_is_multiplied_by_quantity_for_invoice_total(): void
    {
        $admin = User::where('role', 'admin')->firstOrFail();

        $this->actingAs($admin)->post(route('admin.orders.store'), [
            'customer_name' => 'Pelanggan DP Per Unit',
            'customer_whatsapp' => '081200007779',
            'customer_address' => 'Jl. Deposit Per Unit',
            'items' => [
                ['product_name' => 'Barang DP', 'unit' => 'pcs', 'quantity' => 3, 'product_price' => 100000, 'deposit' => 75000],
            ],
        ])->assertSessionHasNoErrors();

        $order = Order::where('customer_whatsapp', '081200007779')->firstOrFail();
        $this->assertSame(225000, $order->down_payment);
        $this->assertSame(75000, (int) $order->items()->value('deposit'));
        $this->assertSame(75000, $order->remaining);
    }

    public function test_25k_per_unit_with_quantity_three_is_75k_everywhere(): void
    {
        $admin = User::where('role', 'admin')->firstOrFail();

        $this->actingAs($admin)->post(route('admin.orders.store'), [
            'customer_name' => 'Pelanggan DP Tiga Unit',
            'customer_whatsapp' => '081200007781',
            'customer_address' => 'Jl. Deposit Tiga Unit',
            'items' => [
                ['product_name' => 'Barang DP', 'quantity' => 3, 'product_price' => 100000, 'deposit' => 25000],
            ],
        ])->assertSessionHasNoErrors();

        $order = Order::where('customer_whatsapp', '081200007781')->firstOrFail();
        $this->assertSame(75000, $order->down_payment);
        $this->assertSame(225000, $order->remaining);

        $this->actingAs($admin)
            ->get(route('admin.orders.invoice', $order))
            ->assertOk()
            ->assertSee('-Rp 75.000')
            ->assertDontSee('-Rp 225.000');
    }

    public function test_invoice_shows_aggregated_item_deposit_only_in_totals_and_corrects_legacy_amount(): void
    {
        $admin = User::where('role', 'admin')->firstOrFail();

        $this->actingAs($admin)->post(route('admin.orders.store'), [
            'customer_name' => 'Pelanggan DP Legacy',
            'customer_whatsapp' => '081200007782',
            'customer_address' => 'Jl. Deposit Legacy',
            'items' => [
                ['product_name' => 'Barang A', 'quantity' => 2, 'product_price' => 100000, 'deposit' => 25000],
                ['product_name' => 'Barang B', 'quantity' => 3, 'product_price' => 100000, 'deposit' => 25000],
            ],
        ])->assertSessionHasNoErrors();

        $order = Order::where('customer_whatsapp', '081200007782')->firstOrFail();
        $order->update(['down_payment' => 50000]);

        $this->actingAs($admin)
            ->get(route('admin.orders.invoice', $order))
            ->assertOk()
            ->assertSee('Deposit masuk')
            ->assertSee('-Rp 125.000')
            ->assertSee('Rp 375.000')
            ->assertDontSee('-Rp 50.000')
            ->assertDontSee('DP/unit')
            ->assertDontSee('Total DP');
    }

    public function test_per_unit_deposit_cannot_exceed_invoice_total_after_quantity_is_applied(): void
    {
        $admin = User::where('role', 'admin')->firstOrFail();

        $this->actingAs($admin)->post(route('admin.orders.store'), [
            'customer_name' => 'Pelanggan DP Melebihi Total',
            'customer_whatsapp' => '081200007780',
            'customer_address' => 'Jl. Deposit Melebihi Total',
            'items' => [
                ['product_name' => 'Barang DP', 'quantity' => 3, 'product_price' => 65000, 'deposit' => 75000],
            ],
        ])->assertSessionHasErrors('down_payment');

        $this->assertDatabaseMissing('orders', ['customer_whatsapp' => '081200007780']);
    }

    public function test_store_rejects_down_payment_above_total(): void
    {
        $admin = User::where('role', 'admin')->firstOrFail();

        $this->actingAs($admin)->post(route('admin.orders.store'), [
            'customer_name' => 'Pelanggan DP Lebih',
            'customer_whatsapp' => '081200006668',
            'customer_address' => 'Jl. DP 2',
            'down_payment' => 999999999,
            'items' => [
                ['product_name' => 'Barang D', 'quantity' => 1, 'product_price' => 50000],
            ],
        ])->assertSessionHasErrors('down_payment');
    }

    public function test_store_requires_address(): void
    {
        $admin = User::where('role', 'admin')->firstOrFail();

        $this->actingAs($admin)->post(route('admin.orders.store'), [
            'customer_name' => 'Tanpa Alamat',
            'customer_whatsapp' => '081200006669',
            'items' => [
                ['product_name' => 'Barang E', 'quantity' => 1, 'product_price' => 50000],
            ],
        ])->assertSessionHasErrors('customer_address');
    }

    public function test_invoice_index_filters_by_status_and_search(): void
    {
        $admin = User::where('role', 'admin')->firstOrFail();

        Order::create([
            'order_number' => 'NDTEST-MANUAL-OK', 'customer_name' => 'Cari Ini',
            'customer_whatsapp' => '081200004321', 'customer_address' => 'Jl. A',
            'subtotal' => 1000, 'shipping_cost' => 0, 'total' => 1000,
            'status' => Order::STATUS_AWAITING_PAYMENT, 'payment_method' => 'manual',
        ]);
        Order::create([
            'order_number' => 'NDTEST-MANUAL-LUNAS', 'customer_name' => 'Sudah Bayar',
            'customer_whatsapp' => '081200004322', 'customer_address' => 'Jl. B',
            'subtotal' => 1000, 'shipping_cost' => 0, 'total' => 1000,
            'status' => Order::STATUS_CONFIRMED, 'paid_at' => now(), 'payment_method' => 'manual',
        ]);

        $this->actingAs($admin)
            ->get(route('admin.invoices.index', ['search' => 'Cari Ini']))
            ->assertOk()
            ->assertSee('NDTEST-MANUAL-OK')
            ->assertDontSee('NDTEST-MANUAL-LUNAS');

        $this->actingAs($admin)
            ->get(route('admin.invoices.index', ['status' => Order::STATUS_CONFIRMED]))
            ->assertOk()
            ->assertSee('NDTEST-MANUAL-LUNAS')
            ->assertDontSee('NDTEST-MANUAL-OK');
    }

    public function test_admin_can_delete_invoice_from_the_list(): void
    {
        $admin = User::where('role', 'admin')->firstOrFail();

        $order = Order::create([
            'order_number' => 'NDTEST-DEL-01', 'customer_name' => 'Hapus Saya',
            'customer_whatsapp' => '081200004444', 'customer_address' => 'Jl. Hapus',
            'subtotal' => 1000, 'shipping_cost' => 0, 'total' => 1000,
            'status' => Order::STATUS_PENDING, 'payment_method' => 'manual',
        ]);
        $order->items()->create([
            'product_name' => 'Barang Hapus', 'product_price' => 1000,
            'quantity' => 1, 'unit' => 'pcs', 'subtotal' => 1000,
        ]);

        // Tombol hapus ada di daftar invoice.
        $this->actingAs($admin)
            ->get(route('admin.invoices.index'))
            ->assertOk()
            ->assertSee(route('admin.orders.destroy', $order), false);

        // Hapus -> order + item ikut hilang.
        $this->actingAs($admin)
            ->delete(route('admin.orders.destroy', $order))
            ->assertSessionHasNoErrors();

        $this->assertDatabaseMissing('orders', ['id' => $order->id]);
        $this->assertSame(0, $order->items()->count());
    }
}
