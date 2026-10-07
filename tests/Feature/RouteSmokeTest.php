<?php

namespace Tests\Feature;

use App\Models\Address;
use App\Models\Category;
use App\Models\Coupon;
use App\Models\Order;
use App\Models\PaymentProof;
use App\Models\Product;
use App\Models\Review;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
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
            '/profile/addresses',
            '/profile/addresses/create',
            "/profile/addresses/{$this->address->id}/edit",
        ];

        foreach ($paths as $path) {
            $status = $this->get($path)->getStatusCode();
            $this->assertLessThan(500, $status, "GET {$path} returned HTTP {$status}");
        }
    }
}