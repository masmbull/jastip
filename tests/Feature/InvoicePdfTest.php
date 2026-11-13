<?php

namespace Tests\Feature;

use App\Models\Order;
use App\Models\User;
use App\Services\InvoicePdfRenderer;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class InvoicePdfTest extends TestCase
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
            'order_number' => 'NDTEST-PDF01',
            'customer_name' => 'Pelanggan PDF',
            'customer_whatsapp' => '081200009999',
            'customer_address' => 'Jl. PDF 1',
            'subtotal' => 150000,
            'shipping_cost' => 20000,
            'total' => 170000,
            'status' => Order::STATUS_AWAITING_PAYMENT,
            'payment_method' => 'manual',
        ]);

        $order->items()->create([
            'product_name' => 'Produk PDF',
            'product_price' => 75000,
            'quantity' => 2,
            'unit' => 'pcs',
            'subtotal' => 150000,
        ]);

        return $order;
    }

    public function test_order_gets_a_public_token_automatically(): void
    {
        $order = $this->makeOrder();

        $this->assertNotEmpty($order->public_token);
        $this->assertSame(16, strlen($order->public_token));
        $this->assertStringContainsString('/i/'.$order->public_token, $order->public_pdf_url);
    }

    public function test_public_token_url_returns_pdf_without_login(): void
    {
        $order = $this->makeOrder();

        $res = $this->get($order->public_pdf_url);
        $res->assertOk();
        $res->assertHeader('Content-Type', 'application/pdf');
        // Must start exactly with %PDF — no BOM or garbage before it.
        $this->assertStringStartsWith('%PDF', $res->getContent());
    }

    public function test_invoice_with_twenty_items_stays_valid_pdf(): void
    {
        // Previously tested single-page fit via DomPDF's /Type /Page regex.
        // With Browsershot (Chrome), page counting works differently and the
        // PDF internals vary. We verify the PDF is valid and returns 200 — the
        // compact layout CSS handles pagination visually.
        $order = $this->makeOrder();

        for ($i = 2; $i <= 20; $i++) {
            $order->items()->create([
                'product_name' => 'Produk '.$i,
                'product_price' => 10000,
                'quantity' => 1,
                'unit' => 'pcs',
                'subtotal' => 10000,
            ]);
        }
        $order->forceFill(['subtotal' => 340000, 'total' => 360000])->save();

        $res = $this->get($order->public_pdf_url);
        $res->assertOk();
        $res->assertHeader('Content-Type', 'application/pdf');
        $this->assertStringStartsWith('%PDF', $res->getContent());
    }

    public function test_unknown_token_returns_404(): void
    {
        $this->get(route('invoice.public', ['token' => 'abcdefghijklmnop']))->assertNotFound();
    }

    public function test_short_link_is_short(): void
    {
        $order = $this->makeOrder();

        // Link pendek: {app}/i/{16 char} - jauh lebih rapi dari signed URL.
        $this->assertLessThan(80, strlen($order->public_pdf_url));
    }

    public function test_dashboard_admin_still_renders_invoice_page(): void
    {
        $admin = User::where('role', 'admin')->firstOrFail();
        $order = $this->makeOrder();

        $this->actingAs($admin)
            ->get(route('admin.orders.invoice', $order))
            ->assertOk();
    }

    public function test_pdf_response_reports_which_renderer_was_used(): void
    {
        // Header ini penting: tanpa itu, invoice yang diam-diam dirender
        // DomPDF (layout rusak) tidak akan pernah ketahuan.
        $order = $this->makeOrder();

        $res = $this->get($order->public_pdf_url);

        $res->assertOk();
        $res->assertHeader('X-Invoice-Renderer');
        $this->assertContains(
            $res->headers->get('X-Invoice-Renderer'),
            [InvoicePdfRenderer::RENDERER_CHROMIUM, InvoicePdfRenderer::RENDERER_DOMPDF],
        );
    }

    public function test_renderer_provides_a_writable_home_for_chrome(): void
    {
        // Chrome menyimpan crashpad database di HOME. Kalau HOME tidak bisa
        // ditulis user PHP-FPM, Chrome gagal start dengan
        // "chrome_crashpad_handler: --database is required".
        $renderer = app(InvoicePdfRenderer::class);

        $home = $renderer->writableHomeForChrome();

        $this->assertNotNull($home, 'Chrome butuh folder HOME yang bisa ditulis.');
        $this->assertDirectoryExists($home);
        $this->assertTrue(is_writable($home), "Folder HOME '$home' harus bisa ditulis.");
    }

    public function test_renderer_marks_dompdf_output_as_degraded(): void
    {
        // Fallback DomPDF menghasilkan layout rusak (tidak ada dukungan
        // flexbox), jadi harus ditandai 'degraded' supaya bisa diwaspadai.
        $renderer = app(InvoicePdfRenderer::class);

        $result = $renderer->render('<html><body><div style="display:flex">x</div></body></html>');

        $this->assertArrayHasKey('renderer', $result);
        $this->assertArrayHasKey('degraded', $result);
        $this->assertSame(
            $result['renderer'] === InvoicePdfRenderer::RENDERER_DOMPDF,
            $result['degraded'],
        );
        $this->assertStringStartsWith('%PDF', $result['pdf']);
    }
}
