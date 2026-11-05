<?php

namespace Tests\Feature;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Setting;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

/**
 * InvoiceTemplateTest
 *
 * Comprehensive coverage for the redesigned invoice template (v2).
 * Tests every major visual section:
 *  - Header (brand, logo, date, status badge)
 *  - Customer + summary cards
 *  - Product table (single item, multi-item, long names, fee column)
 *  - Payment summary box (subtotal, ongkir, fee, deposit, total, sisa)
 *  - QRIS card (shown, hidden, image, dimensions)
 *  - Transfer Rekening card (all three banks present with account numbers)
 *  - Warning banner
 *  - Footer text
 *  - Compact layout trigger
 *  - PDF endpoint content-type & structure
 */
class InvoiceTemplateTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed();
    }

    // ─────────────────────────────────────────────────────────────────────────
    // Helpers
    // ─────────────────────────────────────────────────────────────────────────

    private function admin(): User
    {
        return User::where('role', 'admin')->firstOrFail();
    }

    private function makeOrder(array $override = []): Order
    {
        return Order::create(array_merge([
            'order_number'        => 'TMPL-' . uniqid(),
            'customer_name'       => 'Andi Wijaya',
            'customer_whatsapp'   => '6281200000' . rand(100, 999),
            'customer_address'    => 'Jl. Raya No. 1, Jakarta',
            'shipping_method'     => 'JNE Reguler',
            'subtotal'            => 100_000,
            'shipping_cost'       => 15_000,
            'fee'                 => 0,
            'show_fee'            => false,
            'down_payment'        => 0,
            'total'               => 115_000,
            'status'              => Order::STATUS_AWAITING_PAYMENT,
            'payment_method'      => 'manual',
        ], $override));
    }

    private function addItem(Order $order, array $override = []): OrderItem
    {
        return $order->items()->create(array_merge([
            'product_name'  => 'Produk Contoh',
            'product_price' => 100_000,
            'unit'          => 'pcs',
            'quantity'      => 1,
            'subtotal'      => 100_000,
            'fee'           => 0,
            'deposit'       => 0,
        ], $override));
    }

    private function invoiceHtml(Order $order): \Illuminate\Testing\TestResponse
    {
        return $this->actingAs($this->admin())
            ->get(route('admin.orders.invoice', $order));
    }

    // ─────────────────────────────────────────────────────────────────────────
    // 1. HEADER
    // ─────────────────────────────────────────────────────────────────────────

    public function test_header_shows_invoice_title(): void
    {
        $order = $this->makeOrder();
        $this->invoiceHtml($order)
            ->assertOk()
            ->assertSee('INVOICE');
    }

    public function test_header_shows_correct_order_number(): void
    {
        $order = $this->makeOrder(['order_number' => 'ND20261105-ABCDEFGH']);
        $this->invoiceHtml($order)
            ->assertOk()
            ->assertSee('ND20261105-ABCDEFGH');
    }

    public function test_header_shows_belum_bayar_badge_for_unpaid(): void
    {
        $order = $this->makeOrder(['paid_at' => null]);
        $this->invoiceHtml($order)
            ->assertOk()
            ->assertSee('BELUM BAYAR');
    }

    public function test_header_shows_lunas_badge_for_paid_order(): void
    {
        $order = $this->makeOrder([
            'paid_at' => now(),
            'status'  => Order::STATUS_CONFIRMED,
        ]);
        $this->invoiceHtml($order)
            ->assertOk()
            ->assertSee('LUNAS')
            ->assertDontSee('BELUM BAYAR');
    }

    public function test_header_date_is_in_asia_jakarta_timezone(): void
    {
        // App timezone is Asia/Jakarta (UTC+7).
        // Store the timestamp as WIB time directly — the controller reads it
        // back in Asia/Jakarta and formats it as 'd M Y, H:i'.
        $wib = \Carbon\Carbon::create(2026, 11, 5, 7, 30, 0, 'Asia/Jakarta');

        $order = $this->makeOrder();

        // Direct DB update bypasses Eloquent's timestamp guard.
        \Illuminate\Support\Facades\DB::table('orders')
            ->where('id', $order->id)
            ->update(['created_at' => $wib->toDateTimeString()]);

        $order->refresh();

        $this->invoiceHtml($order)
            ->assertOk()
            ->assertSee('05 Nov 2026, 07:30');
    }

    public function test_header_shows_brand_name_from_settings(): void
    {
        Setting::set('brand_name', 'TOKO SAYA', 'string', 'branding', true);

        $order = $this->makeOrder();
        $this->invoiceHtml($order)
            ->assertOk()
            ->assertSee('TOKO SAYA');
    }

    public function test_header_shows_brand_owner_and_whatsapp(): void
    {
        Setting::set('brand_owner', 'Nabila Adriyana', 'string', 'branding', true);
        Setting::set('whatsapp', '6282336534191', 'string', 'branding', true);

        $order = $this->makeOrder();
        $res = $this->invoiceHtml($order)->assertOk();
        $res->assertSee('Nabila Adriyana');
        $res->assertSee('6282336534191');
    }

    public function test_tagihan_title_appears_when_deposit_was_made_but_not_fully_paid(): void
    {
        $order = $this->makeOrder(['down_payment' => 30_000, 'paid_at' => null]);
        $this->invoiceHtml($order)
            ->assertOk()
            ->assertSee('TAGIHAN');
    }

    // ─────────────────────────────────────────────────────────────────────────
    // 2. CUSTOMER CARD
    // ─────────────────────────────────────────────────────────────────────────

    public function test_customer_card_shows_name_whatsapp_address(): void
    {
        $order = $this->makeOrder([
            'customer_name'     => 'Siti Rahayu',
            'customer_whatsapp' => '081298765432',
            'customer_address'  => 'Jl. Sudirman No. 5, Bandung',
        ]);

        $res = $this->invoiceHtml($order)->assertOk();
        $res->assertSee('Siti Rahayu');
        $res->assertSee('081298765432');
        $res->assertSee('Jl. Sudirman No. 5, Bandung');
        $res->assertSee('DITAGIHKAN KEPADA');
    }

    public function test_ringkasan_card_shows_expedition_and_item_count(): void
    {
        $order = $this->makeOrder(['shipping_method' => 'SiCepat Express']);
        $this->addItem($order, ['quantity' => 3]);

        $this->invoiceHtml($order)
            ->assertOk()
            ->assertSee('RINGKASAN')
            ->assertSee('SiCepat Express')
            ->assertSee('3 pcs');
    }

    // ─────────────────────────────────────────────────────────────────────────
    // 3. PRODUCT TABLE
    // ─────────────────────────────────────────────────────────────────────────

    public function test_product_table_shows_single_item_correctly(): void
    {
        $order = $this->makeOrder(['subtotal' => 250_000, 'total' => 265_000]);
        $this->addItem($order, [
            'product_name'  => 'Kaos Oversized',
            'product_price' => 250_000,
            'unit'          => 'pcs',
            'quantity'      => 1,
            'subtotal'      => 250_000,
        ]);

        $res = $this->invoiceHtml($order)->assertOk();
        $res->assertSee('Kaos Oversized');
        $res->assertSee('pcs');
        $res->assertSee('Rp 250.000');
    }

    public function test_product_table_shows_multiple_items(): void
    {
        $order = $this->makeOrder(['subtotal' => 300_000, 'total' => 315_000]);
        $this->addItem($order, ['product_name' => 'Item Satu', 'product_price' => 100_000, 'subtotal' => 100_000]);
        $this->addItem($order, ['product_name' => 'Item Dua', 'product_price' => 200_000, 'subtotal' => 200_000]);

        $res = $this->invoiceHtml($order)->assertOk();
        $res->assertSee('Item Satu');
        $res->assertSee('Item Dua');
    }

    public function test_product_table_handles_long_product_name(): void
    {
        $longName = 'Uniqlo Ultra Light Down Jacket Puffer 2024 Limited Edition Size M Color Beige Japanese Fashion';
        $order    = $this->makeOrder();
        $this->addItem($order, ['product_name' => $longName]);

        $this->invoiceHtml($order)
            ->assertOk()
            ->assertSee($longName);
    }

    public function test_product_table_shows_fee_column_when_show_fee_is_true(): void
    {
        $order = $this->makeOrder([
            'subtotal'  => 100_000,
            'fee'       => 20_000,
            'show_fee'  => true,
            'total'     => 135_000,
        ]);
        $this->addItem($order, [
            'product_price' => 100_000,
            'subtotal'      => 100_000,
            'fee'           => 20_000,
        ]);

        $res = $this->invoiceHtml($order)->assertOk();
        $res->assertSee('Fee item');
        $res->assertSee('Total Fee Jastip');
        $res->assertSee('Rp 20.000');
    }

    public function test_product_table_hides_fee_column_when_show_fee_is_false(): void
    {
        $order = $this->makeOrder([
            'fee'      => 20_000,
            'show_fee' => false,
            'total'    => 135_000,
        ]);
        $this->addItem($order, ['fee' => 20_000]);

        $this->invoiceHtml($order)
            ->assertOk()
            ->assertDontSee('Fee item');
    }

    public function test_compact_layout_triggered_for_13_or_more_items(): void
    {
        $order = $this->makeOrder(['subtotal' => 650_000, 'total' => 665_000]);
        for ($i = 1; $i <= 13; $i++) {
            $this->addItem($order, [
                'product_name'  => "Barang $i",
                'product_price' => 50_000,
                'subtotal'      => 50_000,
            ]);
        }

        $this->invoiceHtml($order)
            ->assertOk()
            ->assertSee('data-layout="compact"', false);
    }

    public function test_normal_layout_for_12_or_fewer_items(): void
    {
        $order = $this->makeOrder(['subtotal' => 120_000, 'total' => 135_000]);
        for ($i = 1; $i <= 12; $i++) {
            $this->addItem($order, [
                'product_name'  => "Item $i",
                'product_price' => 10_000,
                'subtotal'      => 10_000,
            ]);
        }

        $this->invoiceHtml($order)
            ->assertOk()
            ->assertSee('data-layout="normal"', false);
    }

    // ─────────────────────────────────────────────────────────────────────────
    // 4. PAYMENT SUMMARY BOX
    // ─────────────────────────────────────────────────────────────────────────

    public function test_payment_summary_shows_subtotal_ongkir_and_total(): void
    {
        $order = $this->makeOrder([
            'subtotal'      => 200_000,
            'shipping_cost' => 20_000,
            'total'         => 220_000,
        ]);

        $res = $this->invoiceHtml($order)->assertOk();
        $res->assertSee('Subtotal');
        $res->assertSee('Rp 200.000');
        $res->assertSee('Ongkir');
        $res->assertSee('Rp 20.000');
        $res->assertSee('TOTAL BAYAR');
        $res->assertSee('Rp 220.000');
    }

    public function test_payment_summary_shows_fee_jastip_when_show_fee(): void
    {
        $order = $this->makeOrder([
            'subtotal'  => 100_000,
            'fee'       => 25_000,
            'show_fee'  => true,
            'total'     => 140_000,
        ]);

        $this->invoiceHtml($order)
            ->assertOk()
            ->assertSee('Total Fee Jastip')
            ->assertSee('Rp 25.000');
    }

    public function test_payment_summary_shows_deposit_deduction(): void
    {
        $order = $this->makeOrder([
            'subtotal'     => 200_000,
            'down_payment' => 50_000,
            'total'        => 215_000,
        ]);

        $res = $this->invoiceHtml($order)->assertOk();
        $res->assertSee('Deposit masuk');
        $res->assertSee('-Rp 50.000');
        $res->assertSee('Sisa Bayar');
        $res->assertSee('Rp 165.000');
    }

    public function test_sisa_bayar_row_absent_when_fully_paid(): void
    {
        // When paid_at is set, the order is marked fully paid.
        // remaining = total - totalPaid; if down_payment = total, remaining = 0.
        $order = $this->makeOrder([
            'subtotal'     => 115_000,
            'total'        => 115_000,
            'down_payment' => 115_000,
            'paid_at'      => now(),
            'status'       => Order::STATUS_CONFIRMED,
        ]);

        // When remaining = 0, $doc['left'] is null → Sisa Bayar row is not rendered.
        $this->invoiceHtml($order)
            ->assertOk()
            ->assertDontSee('Sisa Bayar');
    }

    public function test_discount_row_appears_when_discount_applied(): void
    {
        $order = $this->makeOrder([
            'subtotal' => 200_000,
            'discount' => 20_000,
            'total'    => 195_000,
        ]);

        $this->invoiceHtml($order)
            ->assertOk()
            ->assertSee('Diskon')
            ->assertSee('-Rp 20.000');
    }

    // ─────────────────────────────────────────────────────────────────────────
    // 5. QRIS
    // ─────────────────────────────────────────────────────────────────────────

    public function test_qris_card_visible_when_enabled_and_total_within_limit(): void
    {
        Setting::set('qris_enabled', true, 'boolean', 'qris', true);
        Setting::set('qris_max_amount', 500_000, 'integer', 'qris', true);
        Setting::set('qris_image', 'settings/qris.jpg', 'string', 'qris', true);

        $order = $this->makeOrder(['total' => 100_000]);

        $res = $this->invoiceHtml($order)->assertOk();
        $res->assertSee('PEMBAYARAN QRIS', false);
        $res->assertSee('JASTIP NITIPDIEND', false);
        $res->assertSee('SCAN / UPLOAD QRIS', false);
    }

    public function test_qris_image_has_correct_dimensions_and_white_background(): void
    {
        Setting::set('qris_enabled', true, 'boolean', 'qris', true);
        Setting::set('qris_max_amount', 500_000, 'integer', 'qris', true);
        Setting::set('qris_image', 'settings/invoice-qris.png', 'string', 'qris', true);

        $order = $this->makeOrder(['total' => 100_000]);

        $res = $this->invoiceHtml($order)->assertOk();
        $res->assertSee('width:75mm');
        $res->assertSee('height:75mm');
        $res->assertSee('background:#FFFFFF');
    }

    public function test_qris_hidden_when_total_exceeds_limit(): void
    {
        Setting::set('qris_enabled', true, 'boolean', 'qris', true);
        Setting::set('qris_max_amount', 500_000, 'integer', 'qris', true);

        $order = $this->makeOrder(['total' => 600_000]);

        $this->invoiceHtml($order)
            ->assertOk()
            ->assertDontSee('PEMBAYARAN QRIS', false);
    }

    public function test_qris_hidden_when_globally_disabled(): void
    {
        Setting::set('qris_enabled', false, 'boolean', 'qris', true);

        $order = $this->makeOrder(['total' => 50_000]);

        $this->invoiceHtml($order)
            ->assertOk()
            ->assertDontSee('PEMBAYARAN QRIS', false);
    }

    public function test_qris_placeholder_path_does_not_render_qris_image(): void
    {
        Setting::set('qris_enabled', true, 'boolean', 'qris', true);
        Setting::set('qris_image', 'settings/qris-placeholder.png', 'string', 'qris', true);
        Setting::set('qris_max_amount', 500_000, 'integer', 'qris', true);

        $order = $this->makeOrder(['total' => 50_000]);

        // qris_url() must return null for placeholder paths — no <img> for QRIS.
        $html = $this->invoiceHtml($order)->assertOk()->getContent();
        $this->assertStringNotContainsString('qris-placeholder', $html);
    }

    public function test_real_qris_path_resolves_to_storage_url(): void
    {
        Setting::set('qris_enabled', true, 'boolean', 'qris', true);
        Setting::set('qris_image', 'settings/invoice-qris.png', 'string', 'qris', true);
        Setting::set('qris_max_amount', 500_000, 'integer', 'qris', true);

        $order = $this->makeOrder(['total' => 50_000]);

        $this->invoiceHtml($order)
            ->assertOk()
            ->assertSee('/storage/settings/invoice-qris.png');
    }

    // ─────────────────────────────────────────────────────────────────────────
    // 6. TRANSFER REKENING – all three banks
    // ─────────────────────────────────────────────────────────────────────────

    public function test_transfer_section_shows_all_three_banks(): void
    {
        $order = $this->makeOrder();
        $res   = $this->invoiceHtml($order)->assertOk();

        $res->assertSee('TRANSFER REKENING');
        $res->assertSee('BCA');
        $res->assertSee('SeaBank');
        $res->assertSee('BluBCA');
    }

    public function test_transfer_section_shows_correct_account_numbers(): void
    {
        $order = $this->makeOrder();
        $res   = $this->invoiceHtml($order)->assertOk();

        $res->assertSee('2700398369');
        $res->assertSee('901499244727');
        $res->assertSee('001081568345');
    }

    public function test_transfer_section_shows_account_holder_name(): void
    {
        $order = $this->makeOrder();

        $this->invoiceHtml($order)
            ->assertOk()
            ->assertSee('Nabila Adriyana');
    }

    public function test_bank_logos_are_embedded_as_svg_data_uris(): void
    {
        $order = $this->makeOrder();
        $html  = $this->invoiceHtml($order)->assertOk()->getContent();

        // All three logos use inline SVG base64 data URIs.
        $this->assertStringContainsString('data:image/svg+xml;base64,', $html);
    }

    // ─────────────────────────────────────────────────────────────────────────
    // 7. WARNING BANNER
    // ─────────────────────────────────────────────────────────────────────────

    public function test_warning_banner_shown_on_unpaid_invoice(): void
    {
        $order = $this->makeOrder(['paid_at' => null]);
        $this->invoiceHtml($order)
            ->assertOk()
            ->assertSee('TRANSFER HANYA KE REKENING / QRIS DI ATAS');
    }

    public function test_warning_banner_hidden_on_paid_invoice(): void
    {
        $order = $this->makeOrder([
            'paid_at' => now(),
            'status'  => Order::STATUS_CONFIRMED,
        ]);
        $this->invoiceHtml($order)
            ->assertOk()
            ->assertDontSee('TRANSFER HANYA KE REKENING / QRIS DI ATAS');
    }

    // ─────────────────────────────────────────────────────────────────────────
    // 8. FOOTER
    // ─────────────────────────────────────────────────────────────────────────

    public function test_footer_contains_brand_name(): void
    {
        Setting::set('brand_name', 'NITIPDIEND', 'string', 'branding', true);
        $order = $this->makeOrder();

        $this->invoiceHtml($order)
            ->assertOk()
            ->assertSee('Terima kasih telah menitip di NITIPDIEND');
    }

    public function test_footer_is_present_even_with_many_items(): void
    {
        Setting::set('brand_name', 'NITIPDIEND', 'string', 'branding', true);
        $order = $this->makeOrder(['subtotal' => 1_000_000, 'total' => 1_015_000]);
        for ($i = 1; $i <= 15; $i++) {
            $this->addItem($order, [
                'product_name'  => "Item Panjang Nomor $i yang Sangat Panjang",
                'product_price' => 66_000,
                'subtotal'      => 66_000,
            ]);
        }

        $this->invoiceHtml($order)
            ->assertOk()
            ->assertSee('Terima kasih telah menitip di NITIPDIEND');
    }

    // ─────────────────────────────────────────────────────────────────────────
    // 9. PDF ENDPOINT
    // ─────────────────────────────────────────────────────────────────────────

    public function test_pdf_endpoint_returns_application_pdf(): void
    {
        $order = $this->makeOrder();
        $this->addItem($order);

        $res = $this->get($order->public_pdf_url);
        $res->assertOk();
        $res->assertHeader('Content-Type', 'application/pdf');
    }

    public function test_pdf_starts_with_pdf_header_no_bom(): void
    {
        $order = $this->makeOrder();
        $this->addItem($order);

        $content = $this->get($order->public_pdf_url)->getContent();
        $this->assertStringStartsWith('%PDF', $content, 'PDF must start with %PDF — no BOM or whitespace before it.');
    }

    public function test_pdf_filename_contains_order_number(): void
    {
        $order = $this->makeOrder(['order_number' => 'ND-FILENAME-TEST']);
        $this->addItem($order);

        $res = $this->get($order->public_pdf_url);
        $res->assertOk();
        $this->assertStringContainsString('ND-FILENAME-TEST', $res->headers->get('Content-Disposition'));
    }

    public function test_unknown_public_token_returns_404(): void
    {
        $this->get(route('invoice.public', ['token' => 'xxxxxxxxxxxxxxxx']))
            ->assertNotFound();
    }

    public function test_public_invoice_html_endpoint_renders_invoice_doc(): void
    {
        $order = $this->makeOrder(['customer_name' => 'Pelanggan HTML']);
        $this->addItem($order, ['product_name' => 'Item HTML']);

        $this->get(route('invoice.html', $order->public_token))
            ->assertOk()
            ->assertSee('class="invoice-doc"', false)
            ->assertSee('Pelanggan HTML')
            ->assertSee('Item HTML');
    }

    // ─────────────────────────────────────────────────────────────────────────
    // 10. ACCESS CONTROL
    // ─────────────────────────────────────────────────────────────────────────

    public function test_guest_cannot_access_admin_invoice(): void
    {
        $order = $this->makeOrder();
        $this->get(route('admin.orders.invoice', $order))
            ->assertRedirect(); // redirected to login
    }

    public function test_customer_role_is_forbidden_from_admin_invoice(): void
    {
        $customer = User::factory()->create(['role' => 'customer']);
        $order    = $this->makeOrder();

        $this->actingAs($customer)
            ->get(route('admin.orders.invoice', $order))
            ->assertForbidden();
    }

    // ─────────────────────────────────────────────────────────────────────────
    // 11. FINANCIAL CALCULATIONS (regression guard)
    // ─────────────────────────────────────────────────────────────────────────

    public function test_total_calculation_subtotal_plus_ongkir_plus_fee(): void
    {
        // subtotal 200k + ongkir 25k + fee 15k = 240k
        $order = $this->makeOrder([
            'subtotal'      => 200_000,
            'shipping_cost' => 25_000,
            'fee'           => 15_000,
            'show_fee'      => true,
            'total'         => 240_000,
        ]);

        $res = $this->invoiceHtml($order)->assertOk();
        $res->assertSee('Rp 200.000'); // subtotal
        $res->assertSee('Rp 25.000');  // ongkir
        $res->assertSee('Rp 15.000');  // fee
        $res->assertSee('Rp 240.000'); // total
    }

    public function test_sisa_bayar_reflects_remaining_after_extra_payments(): void
    {
        $order = $this->makeOrder([
            'subtotal'     => 300_000,
            'total'        => 315_000,
            'down_payment' => 0,
        ]);
        // Add an extra payment via OrderPayment.
        $order->payments()->create(['amount' => 100_000, 'paid_at' => now()]);

        $res = $this->invoiceHtml($order)->assertOk();
        $res->assertSee('Pelunasan masuk');
        $res->assertSee('-Rp 100.000');
        $res->assertSee('Sisa Bayar');
        $res->assertSee('Rp 215.000');
    }

    public function test_item_deposit_deducted_from_sisa_bayar(): void
    {
        // 3 items × 25k deposit = 75k deposit; total 300k; sisa = 225k
        $order = $this->makeOrder([
            'subtotal'     => 300_000,
            'total'        => 300_000,
            'down_payment' => 75_000,
        ]);
        $this->addItem($order, [
            'product_price' => 100_000,
            'quantity'      => 3,
            'subtotal'      => 300_000,
            'deposit'       => 25_000,
        ]);

        $res = $this->invoiceHtml($order)->assertOk();
        $res->assertSee('Deposit masuk');
        $res->assertSee('-Rp 75.000');
        $res->assertSee('Rp 225.000');
    }
}
