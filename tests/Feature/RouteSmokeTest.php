<?php

namespace Tests\Feature;

use App\Models\Address;
use App\Models\Category;
use App\Models\Coupon;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\PaymentProof;
use App\Models\Product;
use App\Models\Review;
use App\Models\User;
use App\Models\Wishlist;
use App\Models\Notification;
use App\Models\ChatMessage;
use App\Events\OrderStatusChanged;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

/**
 * Crawls every GET route and asserts none return a 5xx.
 * A redirect (302) to login is fine; a 500 is a real runtime bug.
 */
class RouteSmokeTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;
    private Product $product;
    private Category $category;
    private Order $order;
    private Coupon $coupon;
    private Review $review;
    private Address $address;
    private PaymentProof $proof;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed();

        // Block all real outbound HTTP: system-status probes external APIs.
        Http::fake();

        $this->admin = User::where('role', 'admin')->firstOrFail();
        $this->product = Product::firstOrFail();
        $this->category = Category::firstOrFail();

        $this->order = Order::create([
            'order_number' => Order::generateOrderNumber(),
            'user_id' => $this->admin->id,
            'customer_name' => 'Smoke Tester',
            'customer_whatsapp' => '081200000000',
            'customer_address' => 'Jl. Smoke No. 1, Jakarta',
            'shipping_method' => 'JNE',
            'subtotal' => 100000,
            'shipping_cost' => 12000,
            'fee' => 5000,
            'total' => 117000,
            'status' => Order::STATUS_AWAITING_PAYMENT,
        ]);

        // Snapshot line item: exercises order item loops (profile views crashed
        // on real orders while the item-less smoke order hid the bug).
        OrderItem::create([
            'order_id' => $this->order->id,
            'product_id' => $this->product->id,
            'product_name' => $this->product->name,
            'product_price' => $this->product->price,
            'unit' => $this->product->unit ?? 'pcs',
            'quantity' => 2,
            'subtotal' => $this->product->price * 2,
        ]);

        $this->coupon = Coupon::create([
            'code' => 'SMOKE10',
            'description' => 'Smoke test coupon',
            'type' => 'percentage',
            'value' => 10,
            'valid_from' => now()->subDay(),
            'valid_to' => now()->addDay(),
            'is_active' => true,
        ]);

        $this->review = Review::create([
            'user_id' => $this->admin->id,
            'product_id' => $this->product->id,
            'rating' => 5,
            'title' => 'Smoke review',
            'content' => 'Produk sesuai, pengiriman cepat.',
            'status' => 'approved',
        ]);

        $this->address = Address::create([
            'user_id' => $this->admin->id,
            'full_name' => 'Smoke Tester',
            'phone' => '081200000000',
            'street_address' => 'Jl. Smoke No. 1',
            'city' => 'Jakarta',
            'province' => 'DKI Jakarta',
            'postal_code' => '10110',
            'is_default' => true,
        ]);

        $this->proof = PaymentProof::create([
            'order_id' => $this->order->id,
            'file_path' => 'payment-proofs/smoke.jpg',
            'file_name' => 'smoke.jpg',
            'file_size' => 1024,
            'status' => 'pending',
        ]);
    }

    public function test_guest_get_routes_do_not_server_error(): void
    {
        $product = $this->product->slug;
        $category = $this->category->slug;
        $orderNo = $this->order->order_number;
        $orderId = $this->order->id;

        $paths = [
            '/',
            '/produk',
            '/produk-viral',
            '/cari?q=jam',
            '/cari/saran?q=jam',
            '/cari/harga-range',
            "/produk/{$product}",
            '/kategori',
            "/kategori/{$category}",
            '/ongkir',
            '/ongkir/check?city=Jakarta&weight=1',
            '/titipan',
            '/checkout',
            '/checkout/confirmation',
            "/order/{$orderNo}",
            "/order/{$orderNo}/status",
            '/login',
            '/login/admin',
            "/produk/{$product}/ulasan",
            "/payment/{$orderId}/waiting",
            "/payment/{$orderId}/status",
            "/payment/{$orderId}/download-qris",
            '/tentang',
            '/cara-nitip',
            '/kontak',
            '/syarat-ketentuan',
            '/kebijakan-privasi',
            '/admin/login',
        ];

        foreach ($paths as $path) {
            $status = $this->get($path)->getStatusCode();
            $this->assertLessThan(500, $status, "GET {$path} returned HTTP {$status}");
        }
    }

    public function test_authenticated_get_routes_do_not_server_error(): void
    {
        $this->actingAs($this->admin);

        $product = $this->product->slug;
        $category = $this->category->slug;
        $orderId = $this->order->id;

        // Ordering mirrors route registration so static segments are not shadowed.
        $paths = [
            '/admin',
            '/admin/produk',
            '/admin/produk/create',
            "/admin/produk/{$product}",
            "/admin/produk/{$product}/quickview",
            "/admin/produk/{$product}/edit",
            '/admin/kategori',
            '/admin/kategori/create',
            "/admin/kategori/{$category}/edit",
            '/admin/pesanans',
            "/admin/pesanans/{$orderId}",
            "/admin/pesanans/{$orderId}/quickview",
            "/admin/pesanans/{$orderId}/invoice",
            '/admin/ongkir',
            '/admin/bukti-pembayaran',
            "/admin/bukti-pembayaran/{$this->proof->id}",
            "/admin/bukti-pembayaran/{$this->proof->id}/download",
            '/admin/ulasan',
            "/admin/ulasan/{$this->review->id}",
            '/admin/kupon',
            '/admin/kupon/create',
            "/admin/kupon/{$this->coupon->id}/edit",
            '/admin/user',
            "/admin/user/{$this->admin->id}",
            '/admin/import',
            '/admin/laporan/penjualan',
            '/admin/laporan/inventori',
            '/admin/laporan/pelanggan',
            '/admin/settings',
            '/admin/services',
            '/admin/services/check-all',
            '/admin/system/status',
            '/profile',
            '/profile/edit',
            '/profile/settings',
            '/profile/loyalty',
            '/profile/orders',
            "/profile/orders/{$orderId}",
            '/profile/reviews',
            '/profile/wishlist',
            '/profile/notifications',
            '/profile/addresses',
            '/profile/addresses/create',
            "/profile/addresses/{$this->address->id}/edit",
        ];

        foreach ($paths as $path) {
            $status = $this->get($path)->getStatusCode();
            $this->assertLessThan(500, $status, "GET {$path} returned HTTP {$status}");
        }
    }

    public function test_payment_routes_reject_foreign_orders(): void
    {
        // Guest now bounces to login (payment is auth-gated).
        $this->get(route('payment.waiting', $this->order))->assertRedirect(route('login'));
        $this->get(route('payment.status', $this->order))->assertRedirect(route('login'));

        // Different logged-in customer => forbidden.
        $other = User::factory()->create(['role' => 'customer']);
        $this->actingAs($other)->get(route('payment.waiting', $this->order))->assertForbidden();

        // Owner => allowed, and a fresh order must report a positive countdown
        // (Carbon 3 signed diff regression: is_expired must stay false).
        $this->actingAs($this->admin)->get(route('payment.waiting', $this->order))->assertOk();
        $this->actingAs($this->admin)
            ->getJson(route('payment.status', $this->order))
            ->assertOk()
            ->assertJsonPath('is_expired', false)
            ->assertJsonPath('remaining_seconds', fn ($v) => $v > 0);
    }

    public function test_review_pages_render(): void
    {
        // Public review list + product page (routes.products.reviews was orphan
        // before; regression guard so the entry link never breaks again).
        $this->get(route('products.reviews', $this->product))->assertOk();
        $this->get(route('products.show', $this->product))->assertOk();

        // Guest mutations require auth: 401 JSON, not 500. Run before actingAs
        // (Laravel keeps actingAs active for the rest of the test).
        $this->post(route('reviews.store', $this->product), [
            'rating' => 5, 'title' => 'x', 'content' => 'y',
        ])->assertStatus(401);

        $this->post(route('reviews.helpful', $this->review))->assertStatus(401);

        // Helpful vote guards: author cannot self-vote; pending reviews are not votable.
        $voter = User::factory()->create(['role' => 'customer']);
        $this->actingAs($voter)
            ->post(route('reviews.helpful', $this->review))
            ->assertOk()
            ->assertJsonPath('helpful_count', 1);
        $this->assertSame(1, $this->review->fresh()->helpful_count);

        // Self-vote rejected (review belongs to admin), count unchanged.
        $this->actingAs($this->admin)
            ->post(route('reviews.helpful', $this->review))
            ->assertStatus(422);
        $this->assertSame(1, $this->review->fresh()->helpful_count);

        // Pending review => 404, count untouched.
        $pendingAuthor = User::factory()->create(['role' => 'customer']);
        $pending = Review::create([
            'user_id' => $pendingAuthor->id,
            'product_id' => $this->product->id,
            'rating' => 4,
            'title' => 'Pending',
            'content' => 'Belum disetujui.',
            'status' => 'pending',
        ]);
        $this->actingAs($voter)
            ->post(route('reviews.helpful', $pending))
            ->assertStatus(404);

        // Admin moderation detail.
        $this->actingAs($this->admin)
            ->get(route('admin.reviews.show', $this->review))
            ->assertOk();
    }

    public function test_admin_can_update_customer_role_and_invalid_role_is_rejected(): void
    {
        $customer = User::factory()->create(['role' => 'customer']);
        $this->actingAs($this->admin);

        $this->put(route('admin.users.update-role', $customer), ['role' => 'admin'])
            ->assertRedirect();
        $this->assertSame('admin', $customer->fresh()->role);

        $other = User::factory()->create(['role' => 'customer']);
        $this->put(route('admin.users.update-role', $other), ['role' => 'superuser'])
            ->assertSessionHasErrors('role');
        $this->assertSame('customer', $other->fresh()->role);
    }

    public function test_wishlist_toggle_adds_and_removes(): void
    {
        $customer = User::factory()->create(['role' => 'customer']);
        $this->actingAs($customer);

        // First toggle inserts.
        $this->post(route('wishlist.toggle', ['product' => $this->product->id]))
            ->assertOk()
            ->assertJsonPath('wishlisted', true);
        $this->assertDatabaseHas('wishlists', [
            'user_id' => $customer->id,
            'product_id' => $this->product->id,
        ]);

        // Second toggle removes (no duplicate row).
        $this->post(route('wishlist.toggle', ['product' => $this->product->id]))
            ->assertOk()
            ->assertJsonPath('wishlisted', false);
        $this->assertDatabaseMissing('wishlists', [
            'user_id' => $customer->id,
            'product_id' => $this->product->id,
        ]);

        // Guest is rejected by the auth middleware.
        Auth::logout();
        $this->post(route('wishlist.toggle', ['product' => $this->product->id]))
            ->assertRedirect(route('login'));
    }

    public function test_order_status_event_creates_in_app_notification_and_marks_read(): void
    {
        OrderStatusChanged::dispatch($this->order->fresh(), 'awaiting_payment', 'confirmed');

        $this->assertDatabaseHas('notifications', [
            'user_id' => $this->admin->id,
            'type' => 'confirmed',
        ]);

        $notification = Notification::where('user_id', $this->admin->id)->firstOrFail();

        $this->actingAs($this->admin)
            ->post(route('notifications.read', $notification))
            ->assertRedirect(route('notifications.index'));
        $this->assertNotNull($notification->fresh()->read_at);

        // Foreign user cannot mark someone else's notification.
        $other = User::factory()->create(['role' => 'customer']);
        $unread = Notification::create([
            'user_id' => $this->admin->id,
            'title' => 'x', 'message' => 'y', 'type' => 'info',
        ]);
        $this->actingAs($other)
            ->post(route('notifications.read', $unread))
            ->assertForbidden();
    }

    public function test_notification_pref_toggles_and_renders(): void
    {
        $customer = User::factory()->create(['role' => 'customer', 'notify_whatsapp' => true]);
        $this->actingAs($customer);

        $this->get(route('profile.settings'))->assertOk()->assertSee('Notifikasi WhatsApp', false)->assertSee('Notifikasi Email', false);

        $this->put(route('profile.notify-prefs'), ['notify_whatsapp' => '0', 'notify_email' => '0'])
            ->assertRedirect(route('profile.settings'));
        $this->assertFalse($customer->fresh()->notify_whatsapp);
        $this->assertFalse($customer->fresh()->notify_email);

        $this->put(route('profile.notify-prefs'), ['notify_whatsapp' => '1', 'notify_email' => '1'])->assertRedirect();
        $this->assertTrue($customer->fresh()->notify_whatsapp);
        $this->assertTrue($customer->fresh()->notify_email);
    }

    public function test_customer_chat_send_and_admin_reply_roundtrip(): void
    {
        $customer = User::factory()->create(['role' => 'customer']);

        // Customer sends a message; empty message is rejected.
        $this->actingAs($customer);
        $this->get(route('chat.index'))->assertOk();
        $this->post(route('chat.store'), [])->assertSessionHasErrors('body');
        $this->post(route('chat.store'), ['body' => 'Kapan barang saya sampai?'])
            ->assertRedirect(route('chat.index'));

        $this->assertDatabaseHas('chat_messages', [
            'user_id' => $customer->id,
            'sender' => ChatMessage::SENDER_CUSTOMER,
            'body' => 'Kapan barang saya sampai?',
        ]);

        // Admin sees the thread, unread badge, and replies.
        $this->actingAs($this->admin);
        $this->assertNull(ChatMessage::where('sender', ChatMessage::SENDER_CUSTOMER)->firstOrFail()->read_at);

        $this->get(route('admin.chat.index'))->assertOk()->assertSee($customer->name, false);
        // Opening the thread marks the customer's message read.
        $this->get(route('admin.chat.show', $customer))->assertOk()->assertSee('Kapan barang saya sampai?', false);
        $this->assertNotNull(ChatMessage::where('sender', ChatMessage::SENDER_CUSTOMER)->firstOrFail()->read_at);

        // Admin reply.
        $this->post(route('admin.chat.store', $customer), ['body' => 'Sedang dikirim ya.'])
            ->assertRedirect(route('admin.chat.show', $customer));
        $this->assertDatabaseHas('chat_messages', [
            'user_id' => $customer->id,
            'sender' => ChatMessage::SENDER_ADMIN,
            'body' => 'Sedang dikirim ya.',
        ]);

        // Customer reopening the thread marks the admin reply read.
        $this->actingAs($customer);
        $this->get(route('chat.index'))->assertOk();
        $this->assertNotNull(
            ChatMessage::where('sender', ChatMessage::SENDER_ADMIN)->firstOrFail()->read_at
        );

        // Guests are bounced to login.
        Auth::logout();
        $this->get(route('chat.index'))->assertRedirect(route('login'));
    }

    public function test_admin_sets_resi_and_marks_shipped(): void
    {
        $this->actingAs($this->admin);

        $this->put(route('admin.orders.status', $this->order), [
            'status' => Order::STATUS_SHIPPED,
            'resi' => 'JNE123456789',
            'courier' => 'JNE',
        ])->assertRedirect(route('admin.orders.show', $this->order));

        $order = $this->order->fresh();
        $this->assertSame(Order::STATUS_SHIPPED, $order->status);
        $this->assertSame('JNE123456789', $order->resi);
        $this->assertNotNull($order->shipped_at);
        $this->assertStringContainsString('JNE123456789', $order->tracking_url);
    }
}