<?php

use App\Http\Controllers\AddressController;
use App\Http\Controllers\Admin\Auth\LoginController;
use App\Http\Controllers\Admin\CategoryController as AdminCategoryController;
use App\Http\Controllers\Admin\CouponController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\ImportController;
use App\Http\Controllers\Admin\InvoiceController;
use App\Http\Controllers\Admin\OrderController as AdminOrderController;
use App\Http\Controllers\Admin\PaymentProofController;
use App\Http\Controllers\Admin\ProductController as AdminProductController;
use App\Http\Controllers\Admin\ReportController;
use App\Http\Controllers\Admin\ReviewController;
use App\Http\Controllers\Admin\ServiceController;
use App\Http\Controllers\Admin\SettingController;
use App\Http\Controllers\Admin\SystemStatusController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Auth\CustomerAuthController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\ChatController;
use App\Http\Controllers\CheckoutController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\OrderStatusController;
use App\Http\Controllers\PageController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\ProductReviewController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\PushSubscriptionController;
use App\Http\Controllers\SearchController;
use App\Http\Controllers\ShippingController;
use App\Http\Controllers\WishlistController;
use Illuminate\Support\Facades\Route;

// =============================================================================
// Frontend Routes
// =============================================================================

Route::get('/', [HomeController::class, 'index'])->name('home');
// Aliased admin login path: /login/admin -> /admin/login (was 404).
Route::redirect('/login/admin', '/admin/login', 301);

Route::get('/produk', [ProductController::class, 'index'])->name('products.index');
// Harus di atas /produk/{product:slug} supaya tidak dianggap slug produk.
Route::get('/produk-viral', [ProductController::class, 'viral'])->name('products.viral');
Route::get('/cari', [SearchController::class, 'index'])->name('search');
Route::get('/cari/saran', [SearchController::class, 'suggestions'])->name('search.suggestions');
Route::get('/cari/harga-range', [SearchController::class, 'priceRange'])->name('search.price-range');
Route::get('/produk/{product:slug}', [ProductController::class, 'show'])->name('products.show');
Route::get('/kategori', [CategoryController::class, 'index'])->name('categories.index');
Route::get('/kategori/{category:slug}', [CategoryController::class, 'show'])->name('categories.show');

// Cek Ongkir & Tracking Resi
Route::get('/ongkir', [ShippingController::class, 'index'])->name('shipping.index');
// Endpoint JSON read-only (tanpa CSRF) untuk hitung ongkir via JS/AJAX.
Route::get('/ongkir/check', [ShippingController::class, 'check'])->name('shipping.check');

// Cart / Titipan Routes
Route::get('/titipan', [CartController::class, 'index'])->name('cart.index');
Route::post('/titipan/{product:id}/tambah', [CartController::class, 'add'])->name('cart.add');
Route::post('/titipan/{product:id}/update', [CartController::class, 'update'])->name('cart.update');
Route::delete('/titipan/{product:id}/hapus', [CartController::class, 'remove'])->name('cart.remove');
Route::delete('/titipan/clear', [CartController::class, 'clear'])->name('cart.clear');

// Checkout Routes — wajib punya akun (guest hanya bisa menaruh di keranjang).
Route::middleware('auth')->group(function () {
    Route::get('/checkout', [CheckoutController::class, 'index'])->name('checkout.index');
    Route::post('/checkout', [CheckoutController::class, 'store'])->name('checkout.store');
    Route::get('/checkout/confirmation', [CheckoutController::class, 'confirmation'])->name('checkout.confirmation');
});

// Order Status (customer can track order)
Route::get('/order/{orderNumber}', [OrderStatusController::class, 'show'])->name('order.show');
Route::get('/order/{orderNumber}/status', [OrderStatusController::class, 'api'])->name('order.status');

// Customer auth (unified): /login terima admin & customer, redirect by role.
Route::get('/login', [CustomerAuthController::class, 'showLogin'])->name('login');
Route::post('/login', [CustomerAuthController::class, 'login'])
    ->middleware('throttle:5,1')
    ->name('login.post');
Route::get('/register', [CustomerAuthController::class, 'showRegister'])->name('register');
Route::post('/register', [CustomerAuthController::class, 'register'])
    ->middleware('throttle:5,1')
    ->name('register.post');
Route::post('/logout', [CustomerAuthController::class, 'logout'])->name('logout');

// Product Reviews
Route::get('/produk/{product}/ulasan', [ProductReviewController::class, 'index'])->name('products.reviews');
Route::post('/produk/{product}/ulasan', [ProductReviewController::class, 'store'])->name('reviews.store');
Route::post('/ulasan/{review}/helpful', [ProductReviewController::class, 'toggleHelpful'])->name('reviews.helpful');

// Wishlist toggle (auth-gated; binds explicitly on {product:id} since Product route key is slug)
Route::post('/wishlist/{product:id}/toggle', [WishlistController::class, 'toggle'])
    ->middleware('auth')
    ->name('wishlist.toggle');

// User Profile Routes (Protected)
Route::middleware('auth')->group(function () {
    // Profile
    Route::get('/profile', [ProfileController::class, 'show'])->name('profile.show');
    Route::get('/profile/edit', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::put('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::get('/profile/settings', [ProfileController::class, 'settings'])->name('profile.settings');
    Route::put('/profile/password', [ProfileController::class, 'updatePassword'])->name('profile.password');
    Route::put('/profile/notifications-prefs', [ProfileController::class, 'updateNotificationPrefs'])->name('profile.notify-prefs');
    Route::get('/profile/loyalty', [ProfileController::class, 'loyalty'])->name('profile.loyalty');

    // Orders
    Route::get('/profile/orders', [ProfileController::class, 'orders'])->name('profile.orders');
    Route::get('/profile/orders/{order}', [ProfileController::class, 'orderDetail'])->name('profile.order-detail');

    // Reviews
    Route::get('/profile/reviews', [ProfileController::class, 'reviews'])->name('profile.reviews');

    // Wishlist
    Route::get('/profile/wishlist', [WishlistController::class, 'index'])->name('profile.wishlist');

    // Notifications (in-app)
    Route::get('/profile/notifications', [NotificationController::class, 'index'])->name('notifications.index');
    Route::post('/profile/notifications/read-all', [NotificationController::class, 'markAllRead'])->name('notifications.read-all');
    Route::post('/profile/notifications/{notification}/read', [NotificationController::class, 'markRead'])->name('notifications.read');

    // Support chat (single thread per customer)
    Route::get('/profile/chat', [ChatController::class, 'index'])->name('chat.index');
    Route::post('/profile/chat', [ChatController::class, 'store'])->name('chat.store');

    // Web Push subscriptions (browser notifications)
    Route::get('/push/key', [PushSubscriptionController::class, 'key'])->name('push.key');
    Route::post('/push/subscribe', [PushSubscriptionController::class, 'store'])->name('push.subscribe');
    Route::post('/push/unsubscribe', [PushSubscriptionController::class, 'destroy'])->name('push.unsubscribe');

    // Addresses
    Route::get('/profile/addresses', [AddressController::class, 'index'])->name('profile.addresses');
    Route::get('/profile/addresses/create', [AddressController::class, 'create'])->name('addresses.create');
    Route::post('/profile/addresses', [AddressController::class, 'store'])->name('addresses.store');
    Route::get('/profile/addresses/{address}/edit', [AddressController::class, 'edit'])->name('addresses.edit');
    Route::put('/profile/addresses/{address}', [AddressController::class, 'update'])->name('addresses.update');
    Route::delete('/profile/addresses/{address}', [AddressController::class, 'destroy'])->name('addresses.destroy');
    Route::post('/profile/addresses/{address}/default', [AddressController::class, 'setDefault'])->name('addresses.default');
});

// Payment Routes — wajib login (owner/admin).
Route::middleware('auth')->group(function () {
    Route::get('/payment/{order}/waiting', [PaymentController::class, 'waiting'])->name('payment.waiting');
    Route::post('/payment/{order}/upload-proof', [PaymentController::class, 'uploadProof'])->name('payment.upload-proof');
    Route::get('/payment/{order}/download-qris', [PaymentController::class, 'downloadQris'])->name('payment.download-qris');
    Route::get('/payment/{order}/status', [PaymentController::class, 'status'])->name('payment.status');
});

// Static Pages
Route::get('/tentang', [PageController::class, 'about'])->name('about');
Route::get('/cara-nitip', [PageController::class, 'howTo'])->name('how-to');
Route::get('/kontak', [PageController::class, 'contact'])->name('contact');
Route::get('/syarat-ketentuan', [PageController::class, 'terms'])->name('terms');
Route::get('/kebijakan-privasi', [PageController::class, 'privacy'])->name('privacy');

// =============================================================================
// Admin Routes
// =============================================================================

Route::prefix('admin')->name('admin.')->group(function () {
    // Auth
    Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [LoginController::class, 'login'])
        ->middleware('throttle:5,1')
        ->name('login.post');
    Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

    // Protected admin routes
    Route::middleware('admin')->group(function () {
        Route::get('/', [DashboardController::class, 'index'])->name('dashboard');

        // Products
        Route::get('/produk', [AdminProductController::class, 'index'])->name('products.index');
        Route::get('/produk/create', [AdminProductController::class, 'create'])->name('products.create');
        Route::post('/produk', [AdminProductController::class, 'store'])->name('products.store');
        Route::get('/produk/{product}', [AdminProductController::class, 'show'])->name('products.show');
        Route::get('/produk/{product}/quickview', [AdminProductController::class, 'quickView'])->name('products.quickview');
        Route::get('/produk/{product}/edit', [AdminProductController::class, 'edit'])->name('products.edit');
        Route::put('/produk/{product}', [AdminProductController::class, 'update'])->name('products.update');
        Route::delete('/produk/{product}', [AdminProductController::class, 'destroy'])->name('products.destroy');

        // Categories
        Route::get('/kategori', [AdminCategoryController::class, 'index'])->name('categories.index');
        Route::get('/kategori/create', [AdminCategoryController::class, 'create'])->name('categories.create');
        Route::post('/kategori', [AdminCategoryController::class, 'store'])->name('categories.store');
        Route::get('/kategori/{category}/edit', [AdminCategoryController::class, 'edit'])->name('categories.edit');
        Route::put('/kategori/{category}', [AdminCategoryController::class, 'update'])->name('categories.update');
        Route::delete('/kategori/{category}', [AdminCategoryController::class, 'destroy'])->name('categories.destroy');

        // Orders
        Route::get('/pesanans', [AdminOrderController::class, 'index'])->name('orders.index');
        Route::get('/pesanans/create', [AdminOrderController::class, 'create'])->name('orders.create');
        Route::post('/pesanans', [AdminOrderController::class, 'store'])->name('orders.store');
        // JSON lookups for the manual-invoice form (must precede /pesanans/{order}).
        Route::get('/pesanans/cari-pelanggan', [AdminOrderController::class, 'customerSearch'])->name('orders.customer-search');
        Route::get('/pesanans/cari-produk', [AdminOrderController::class, 'productSearch'])->name('orders.product-search');
        Route::get('/pesanans/{order}', [AdminOrderController::class, 'show'])->name('orders.show');
        Route::get('/pesanans/{order}/quickview', [AdminOrderController::class, 'quickView'])->name('orders.quickview');
        Route::get('/pesanans/{order}/invoice', [InvoiceController::class, 'invoice'])->name('orders.invoice');
        Route::put('/pesanans/{order}/status', [AdminOrderController::class, 'updateStatus'])->name('orders.status');
        Route::delete('/pesanans/{order}', [AdminOrderController::class, 'destroy'])->name('orders.destroy');

        // Daftar invoice manual (buat via form manual, bukan checkout).
        Route::get('/invoice', [InvoiceController::class, 'index'])->name('invoices.index');

        // Shipping / Ongkir
        Route::get('/ongkir', [App\Http\Controllers\Admin\ShippingController::class, 'index'])->name('shipping.index');
        Route::post('/ongkir/{courier}/toggle', [App\Http\Controllers\Admin\ShippingController::class, 'toggle'])->name('shipping.toggle');
        Route::post('/ongkir/{courier}/pricing', [App\Http\Controllers\Admin\ShippingController::class, 'updatePricing'])->name('shipping.update-pricing');
        Route::post('/ongkir/refresh', [App\Http\Controllers\Admin\ShippingController::class, 'refreshPrices'])->name('shipping.refresh');

        // System Status
        Route::get('/system/status', [SystemStatusController::class, 'index'])->name('system.status');

        // Payment Proofs (QRIS payment verification)
        Route::get('/bukti-pembayaran', [PaymentProofController::class, 'index'])->name('payment-proofs.index');
        Route::get('/bukti-pembayaran/{paymentProof}', [PaymentProofController::class, 'show'])->name('payment-proofs.show');
        Route::post('/bukti-pembayaran/{paymentProof}/verify', [PaymentProofController::class, 'verify'])->name('payment-proofs.verify');
        Route::post('/bukti-pembayaran/{paymentProof}/reject', [PaymentProofController::class, 'reject'])->name('payment-proofs.reject');
        Route::get('/bukti-pembayaran/{paymentProof}/download', [PaymentProofController::class, 'download'])->name('payment-proofs.download');

        // Reviews Moderation
        Route::get('/ulasan', [ReviewController::class, 'index'])->name('reviews.index');
        Route::get('/ulasan/{review}', [ReviewController::class, 'show'])->name('reviews.show');
        Route::post('/ulasan/{review}/approve', [ReviewController::class, 'approve'])->name('reviews.approve');
        Route::post('/ulasan/{review}/reject', [ReviewController::class, 'reject'])->name('reviews.reject');
        Route::delete('/ulasan/{review}', [ReviewController::class, 'destroy'])->name('reviews.destroy');

        // Support Chat
        Route::get('/chat', [App\Http\Controllers\Admin\ChatController::class, 'index'])->name('chat.index');
        Route::get('/chat/{user}', [App\Http\Controllers\Admin\ChatController::class, 'show'])->name('chat.show');
        Route::post('/chat/{user}', [App\Http\Controllers\Admin\ChatController::class, 'store'])->name('chat.store');

        // Coupons (explicit: Route::resource [... 'as' => 'admin'] gave double admin.admin.* prefix)
        Route::get('/kupon', [CouponController::class, 'index'])->name('coupons.index');
        Route::get('/kupon/create', [CouponController::class, 'create'])->name('coupons.create');
        Route::post('/kupon', [CouponController::class, 'store'])->name('coupons.store');
        Route::get('/kupon/{coupon}/edit', [CouponController::class, 'edit'])->name('coupons.edit');
        Route::put('/kupon/{coupon}', [CouponController::class, 'update'])->name('coupons.update');
        Route::delete('/kupon/{coupon}', [CouponController::class, 'destroy'])->name('coupons.destroy');

        // Users Management
        Route::get('/user', [UserController::class, 'index'])->name('users.index');
        Route::get('/user/{user}', [UserController::class, 'show'])->name('users.show');
        Route::put('/user/{user}/role', [UserController::class, 'updateRole'])->name('users.update-role');
        Route::delete('/user/{user}', [UserController::class, 'destroy'])->name('users.destroy');

        // Bulk Import
        Route::get('/import', [ImportController::class, 'importForm'])->name('import.form');
        Route::post('/import', [ImportController::class, 'import'])->name('import.store');

        // Reports
        Route::get('/laporan/penjualan', [ReportController::class, 'sales'])->name('reports.sales');
        Route::get('/laporan/inventori', [ReportController::class, 'inventory'])->name('reports.inventory');
        Route::get('/laporan/pelanggan', [ReportController::class, 'customers'])->name('reports.customers');

        // Settings
        Route::get('/settings', [SettingController::class, 'index'])->name('settings.index');
        Route::post('/settings', [SettingController::class, 'update'])->name('settings.update');

        // Services Monitoring
        Route::get('/services', [ServiceController::class, 'index'])->name('services.index');
        Route::post('/services/{service}/check', [ServiceController::class, 'check'])->name('services.check');
        Route::get('/services/check-all', [ServiceController::class, 'checkAll'])->name('services.checkAll');
    });
});
