<?php

namespace Tests\Feature;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Setting;
use App\Models\User;
use App\Services\InvoiceImageRenderer;
use App\Services\WhatsappGatewayService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Client\Request;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

/**
 * Kirim invoice sebagai gambar ke WhatsApp lewat gateway self-hosted.
 *
 * Semua HTTP keluar di-fake: test tidak pernah menyentuh gateway sungguhan.
 */
class InvoiceWhatsappSendTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;

    private Order $order;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed();

        $this->admin = User::where('role', 'admin')->firstOrFail();

        $this->order = Order::create([
            'order_number' => 'NDTEST-WA001',
            'customer_name' => 'Siti Rahma',
            'customer_whatsapp' => '081234567890',
            'customer_address' => 'Jl. Asia Afrika No. 1, Bandung',
            'shipping_method' => 'JNE',
            'subtotal' => 200000,
            'shipping_cost' => 15000,
            'total' => 215000,
            'status' => Order::STATUS_AWAITING_PAYMENT,
            'payment_method' => 'manual',
        ]);

        OrderItem::create([
            'order_id' => $this->order->id,
            'product_name' => 'Kaos',
            'product_price' => 100000,
            'quantity' => 2,
            'unit' => 'pcs',
            'subtotal' => 200000,
        ]);

        Setting::set('wa_gateway_url', 'http://gateway.test', 'string', 'whatsapp_gateway', false);
        Setting::set('wa_gateway_key', 'secret-key', 'string', 'whatsapp_gateway', false);
        Setting::set('wa_gateway_session', 'session-uuid', 'string', 'whatsapp_gateway', false);
    }

    /**
     * Renderer palsu supaya test tidak butuh Chrome headless.
     */
    private function fakeRenderer(?string $png = 'PNG-BYTES'): void
    {
        $renderer = new class($png) extends InvoiceImageRenderer
        {
            public function __construct(private ?string $png) {}

            public function isAvailable(): bool
            {
                return $this->png !== null;
            }

            public function render(string $html): ?string
            {
                return $this->png;
            }
        };

        $this->app->instance(InvoiceImageRenderer::class, $renderer);
    }

    private function gatewayReady(): void
    {
        Http::fake([
            'gateway.test/api/sessions/session-uuid' => Http::response(['status' => 'ready', 'phone' => '628111'], 200),
            'gateway.test/api/sessions/session-uuid/messages/send-image' => Http::response(['id' => 'msg-1'], 201),
        ]);
    }

    public function test_admin_sends_invoice_image_and_it_is_recorded(): void
    {
        $this->gatewayReady();
        $this->fakeRenderer();

        $this->actingAs($this->admin)
            ->post(route('admin.invoices.send-wa', $this->order))
            ->assertRedirect('https://wa.me/6281234567890')
            ->assertSessionHas('success');

        Http::assertSent(function (Request $request) {
            if (! str_ends_with($request->url(), '/messages/send-image')) {
                return false;
            }

            $body = $request->data();

            return $body['chatId'] === '6281234567890@c.us'
                && $body['mimetype'] === 'image/png'
                && base64_decode($body['base64'], true) === 'PNG-BYTES'
                && str_contains($body['caption'], 'Siti Rahma')
                && str_contains($body['caption'], 'Total: Rp 215.000')
                && str_contains($body['caption'], 'Sisa: Rp 215.000');
        });

        $this->order->refresh();
        $this->assertNotNull($this->order->wa_invoice_sent_at);
        $this->assertNull($this->order->wa_invoice_error);
    }

    public function test_invoice_list_uses_direct_send_button_and_opens_chat_in_new_tab(): void
    {
        $this->actingAs($this->admin)
            ->get(route('admin.invoices.index'))
            ->assertOk()
            ->assertSee('target="_blank" rel="noopener"', false)
            ->assertSee('beserta caption ke WhatsApp 081234567890', false)
            ->assertDontSee('Bagikan invoice sebagai gambar');
    }

    public function test_api_key_is_sent_as_header(): void
    {
        $this->gatewayReady();
        $this->fakeRenderer();

        $this->actingAs($this->admin)->post(route('admin.invoices.send-wa', $this->order));

        Http::assertSent(fn (Request $r) => $r->hasHeader('X-API-Key', 'secret-key'));
    }

    public function test_unconfigured_gateway_fails_without_calling_out(): void
    {
        Setting::set('wa_gateway_url', '', 'string', 'whatsapp_gateway', false);
        Http::fake();
        $this->fakeRenderer();

        $this->actingAs($this->admin)
            ->post(route('admin.invoices.send-wa', $this->order))
            ->assertSessionHas('error');

        Http::assertNothingSent();
        $this->assertNull($this->order->fresh()->wa_invoice_sent_at);
        $this->assertNotNull($this->order->fresh()->wa_invoice_error);
    }

    public function test_disconnected_session_blocks_send(): void
    {
        Http::fake([
            'gateway.test/api/sessions/session-uuid' => Http::response(['status' => 'qr_ready'], 200),
        ]);
        $this->fakeRenderer();

        $this->actingAs($this->admin)
            ->post(route('admin.invoices.send-wa', $this->order))
            ->assertSessionHas('error');

        Http::assertNotSent(fn (Request $r) => str_contains($r->url(), 'send-image'));
        $this->assertNull($this->order->fresh()->wa_invoice_sent_at);
    }

    public function test_missing_browser_reports_clear_error(): void
    {
        $this->gatewayReady();
        $this->fakeRenderer(null);

        $this->actingAs($this->admin)
            ->post(route('admin.invoices.send-wa', $this->order))
            ->assertSessionHas('error', fn ($m) => str_contains($m, 'Chrome'));

        Http::assertNotSent(fn (Request $r) => str_contains($r->url(), 'send-image'));
    }

    public function test_gateway_error_is_surfaced(): void
    {
        Http::fake([
            'gateway.test/api/sessions/session-uuid' => Http::response(['status' => 'ready'], 200),
            'gateway.test/api/sessions/session-uuid/messages/send-image' => Http::response(['message' => 'Session not active'], 400),
        ]);
        $this->fakeRenderer();

        $this->actingAs($this->admin)
            ->post(route('admin.invoices.send-wa', $this->order))
            ->assertSessionHas('error');

        $this->assertNull($this->order->fresh()->wa_invoice_sent_at);
        $this->assertStringContainsString('Session not active', (string) $this->order->fresh()->wa_invoice_error);
    }

    public function test_already_sent_invoice_requires_force(): void
    {
        $this->gatewayReady();
        $this->fakeRenderer();
        $this->order->forceFill(['wa_invoice_sent_at' => now()])->save();

        $this->actingAs($this->admin)
            ->post(route('admin.invoices.send-wa', $this->order))
            ->assertSessionHas('error');

        Http::assertNotSent(fn (Request $r) => str_contains($r->url(), 'send-image'));

        $this->actingAs($this->admin)
            ->post(route('admin.invoices.send-wa', $this->order), ['force' => 1])
            ->assertSessionHas('success');
    }

    public function test_invalid_phone_is_rejected(): void
    {
        $this->order->forceFill(['customer_whatsapp' => 'abc'])->save();
        Http::fake();
        $this->fakeRenderer();

        $this->actingAs($this->admin)
            ->post(route('admin.invoices.send-wa', $this->order))
            ->assertSessionHas('error');

        Http::assertNothingSent();
    }

    public function test_customer_cannot_send_invoice(): void
    {
        $customer = User::factory()->create(['role' => 'customer']);
        Http::fake();
        $this->fakeRenderer();

        $this->actingAs($customer)
            ->post(route('admin.invoices.send-wa', $this->order))
            ->assertForbidden();

        Http::assertNothingSent();
    }

    public function test_wa_chat_id_normalizes_numbers(): void
    {
        $this->assertSame('6281234567890@c.us', wa_chat_id('0812-3456-7890'));
        $this->assertSame('6281234567890@c.us', wa_chat_id('+62 812 3456 7890'));
        $this->assertSame('6281234567890@c.us', wa_chat_id('81234567890'));
        $this->assertSame('', wa_chat_id(''));
        $this->assertSame('', wa_chat_id('12345'));
    }

    public function test_status_reports_unreachable_gateway(): void
    {
        Http::fake([
            'gateway.test/api/sessions/*' => Http::response('', 500),
        ]);

        $status = WhatsappGatewayService::fromSettings()->status();

        $this->assertFalse($status['connected']);
        $this->assertSame('unreachable', $status['status']);
    }
}
