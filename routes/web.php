<?php

use App\Http\Controllers\Admin\Auth\LoginController;
use App\Http\Controllers\Admin\CategoryController as AdminCategoryController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\OrderController as AdminOrderController;
use App\Http\Controllers\Admin\ProductController as AdminProductController;
use App\Http\Controllers\Admin\ServiceController;
use App\Http\Controllers\Admin\SettingController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\CheckoutController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\PageController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\SearchController;
use App\Http\Controllers\ShippingController;
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

// Checkout Routes
Route::get('/checkout', [CheckoutController::class, 'index'])->name('checkout.index');
Route::post('/checkout', [CheckoutController::class, 'store'])->name('checkout.store');
Route::get('/checkout/confirmation', [CheckoutController::class, 'confirmation'])->name('checkout.confirmation');

// Order Status (customer can track order)
Route::get('/order/{orderNumber}', [\App\Http\Controllers\OrderStatusController::class, 'show'])->name('order.show');
Route::get('/order/{orderNumber}/status', [\App\Http\Controllers\OrderStatusController::class, 'api'])->name('order.status');

// Customer login: tiada frontend AuthController; arahkan ke login admin
// sebagai satu-satunya pintu autentikasi (redirect target untuk middleware 'auth').
Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
Route::post('/login', [LoginController::class, 'login'])
    ->middleware('throttle:5,1')
    ->name('login.post');
Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

// Product Reviews
Route::get('/produk/{product}/ulasan', [\App\Http\Controllers\ProductReviewController::class, 'index'])->name('products.reviews');
Route::post('/produk/{product}/ulasan', [\App\Http\Controllers\ProductReviewController::class, 'store'])->name('reviews.store');
Route::post('/ulasan/{review}/helpful', [\App\Http\Controllers\ProductReviewController::class, 'toggleHelpful'])->name('reviews.helpful');

// Wishlist toggle (auth-gated; binds explicitly on {product:id} since Product route key is slug)
Route::post('/wishlist/{product:id}/toggle', [\App\Http\Controllers\WishlistController::class, 'toggle'])
    ->middleware('auth')
    ->name('wishlist.toggle');

// User Profile Routes (Protected)
Route::middleware('auth')->group(function () {
    // Profile
    Route::get('/profile', [\App\Http\Controllers\ProfileController::class, 'show'])->name('profile.show');
    Route::get('/profile/edit', [\App\Http\Controllers\ProfileController::class, 'edit'])->name('profile.edit');
    Route::put('/profile', [\App\Http\Controllers\ProfileController::class, 'update'])->name('profile.update');
    Route::get('/profile/settings', [\App\Http\Controllers\ProfileController::class, 'settings'])->name('profile.settings');
    Route::put('/profile/password', [\App\Http\Controllers\ProfileController::class, 'updatePassword'])->name('profile.password');
    Route::put('/profile/notifications-prefs', [\App\Http\Controllers\ProfileController::class, 'updateNotificationPrefs'])->name('profile.notify-prefs');
    Route::get('/profile/loyalty', [\App\Http\Controllers\ProfileController::class, 'loyalty'])->name('profile.loyalty');

    // Orders
    Route::get('/profile/orders', [\App\Http\Controllers\ProfileController::class, 'orders'])->name('profile.orders');
    Route::get('/profile/orders/{order}', [\App\Http\Controllers\ProfileController::class, 'orderDetail'])->name('profile.order-detail');

    // Reviews
    Route::get('/profile/reviews', [\App\Http\Controllers\ProfileController::class, 'reviews'])->name('profile.reviews');

    // Wishlist
    Route::get('/profile/wishlist', [\App\Http\Controllers\WishlistController::class, 'index'])->name('profile.wishlist');

    // Notifications (in-app)
    Route::get('/profile/notifications', [\App\Http\Controllers\NotificationController::class, 'index'])->name('notifications.index');
    Route::post('/profile/notifications/read-all', [\App\Http\Controllers\NotificationController::class, 'markAllRead'])->name('notifications.read-all');
    Route::post('/profile/notifications/{notification}/read', [\App\Http\Controllers\NotificationController::class, 'markRead'])->name('notifications.read');

    // Addresses
    Route::get('/profile/addresses', [\App\Http\Controllers\AddressController::class, 'index'])->name('profile.addresses');
    Route::get('/profile/addresses/create', [\App\Http\Controllers\AddressController::class, 'create'])->name('addresses.create');
    Route::post('/profile/addresses', [\App\Http\Controllers\AddressController::class, 'store'])->name('addresses.store');
    Route::get('/profile/addresses/{address}/edit', [\App\Http\Controllers\AddressController::class, 'edit'])->name('addresses.edit');
    Route::put('/profile/addresses/{address}', [\App\Http\Controllers\AddressController::class, 'update'])->name('addresses.update');
    Route::delete('/profile/addresses/{address}', [\App\Http\Controllers\AddressController::class, 'destroy'])->name('addresses.destroy');
    Route::post('/profile/addresses/{address}/default', [\App\Http\Controllers\AddressController::class, 'setDefault'])->name('addresses.default');
});

// Payment Routes
Route::get('/payment/{order}/waiting', [\App\Http\Controllers\PaymentController::class, 'waiting'])->name('payment.waiting');
Route::post('/payment/{order}/upload-proof', [\App\Http\Controllers\PaymentController::class, 'uploadProof'])->name('payment.upload-proof');
Route::get('/payment/{order}/download-qris', [\App\Http\Controllers\PaymentController::class, 'downloadQris'])->name('payment.download-qris');
Route::get('/payment/{order}/status', [\App\Http\Controllers\PaymentController::class, 'status'])->name('payment.status');

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
        Route::get('/pesanans/{order}', [AdminOrderController::class, 'show'])->name('orders.show');
        Route::get('/pesanans/{order}/quickview', [AdminOrderController::class, 'quickView'])->name('orders.quickview');
        Route::put('/pesanans/{order}/status', [AdminOrderController::class, 'updateStatus'])->name('orders.status');
        Route::delete('/pesanans/{order}', [AdminOrderController::class, 'destroy'])->name('orders.destroy');

        // Shipping / Ongkir
        Route::get('/ongkir', [\App\Http\Controllers\Admin\ShippingController::class, 'index'])->name('shipping.index');
        Route::post('/ongkir/{courier}/toggle', [\App\Http\Controllers\Admin\ShippingController::class, 'toggle'])->name('shipping.toggle');
        Route::post('/ongkir/{courier}/pricing', [\App\Http\Controllers\Admin\ShippingController::class, 'updatePricing'])->name('shipping.update-pricing');
        Route::post('/ongkir/refresh', [\App\Http\Controllers\Admin\ShippingController::class, 'refreshPrices'])->name('shipping.refresh');

        // System Status
        Route::get('/system/status', [\App\Http\Controllers\Admin\SystemStatusController::class, 'index'])->name('system.status');

        // Payment Proofs (QRIS payment verification)
        Route::get('/bukti-pembayaran', [\App\Http\Controllers\Admin\PaymentProofController::class, 'index'])->name('payment-proofs.index');
        Route::get('/bukti-pembayaran/{paymentProof}', [\App\Http\Controllers\Admin\PaymentProofController::class, 'show'])->name('payment-proofs.show');
        Route::post('/bukti-pembayaran/{paymentProof}/verify', [\App\Http\Controllers\Admin\PaymentProofController::class, 'verify'])->name('payment-proofs.verify');
        Route::post('/bukti-pembayaran/{paymentProof}/reject', [\App\Http\Controllers\Admin\PaymentProofController::class, 'reject'])->name('payment-proofs.reject');
        Route::get('/bukti-pembayaran/{paymentProof}/download', [\App\Http\Controllers\Admin\PaymentProofController::class, 'download'])->name('payment-proofs.download');

        // Reviews Moderation
        Route::get('/ulasan', [\App\Http\Controllers\Admin\ReviewController::class, 'index'])->name('reviews.index');
        Route::get('/ulasan/{review}', [\App\Http\Controllers\Admin\ReviewController::class, 'show'])->name('reviews.show');
        Route::post('/ulasan/{review}/approve', [\App\Http\Controllers\Admin\ReviewController::class, 'approve'])->name('reviews.approve');
        Route::post('/ulasan/{review}/reject', [\App\Http\Controllers\Admin\ReviewController::class, 'reject'])->name('reviews.reject');
        Route::delete('/ulasan/{review}', [\App\Http\Controllers\Admin\ReviewController::class, 'destroy'])->name('reviews.destroy');

        // Coupons (explicit: Route::resource [... 'as' => 'admin'] gave double admin.admin.* prefix)
        Route::get('/kupon', [\App\Http\Controllers\Admin\CouponController::class, 'index'])->name('coupons.index');
        Route::get('/kupon/create', [\App\Http\Controllers\Admin\CouponController::class, 'create'])->name('coupons.create');
        Route::post('/kupon', [\App\Http\Controllers\Admin\CouponController::class, 'store'])->name('coupons.store');
        Route::get('/kupon/{coupon}/edit', [\App\Http\Controllers\Admin\CouponController::class, 'edit'])->name('coupons.edit');
        Route::put('/kupon/{coupon}', [\App\Http\Controllers\Admin\CouponController::class, 'update'])->name('coupons.update');
        Route::delete('/kupon/{coupon}', [\App\Http\Controllers\Admin\CouponController::class, 'destroy'])->name('coupons.destroy');

        // Users Management
        Route::get('/user', [\App\Http\Controllers\Admin\UserController::class, 'index'])->name('users.index');
        Route::get('/user/{user}', [\App\Http\Controllers\Admin\UserController::class, 'show'])->name('users.show');
        Route::put('/user/{user}/role', [\App\Http\Controllers\Admin\UserController::class, 'updateRole'])->name('users.update-role');
        Route::delete('/user/{user}', [\App\Http\Controllers\Admin\UserController::class, 'destroy'])->name('users.destroy');

        // Bulk Import
        Route::get('/import', [\App\Http\Controllers\Admin\ImportController::class, 'importForm'])->name('import.form');
        Route::post('/import', [\App\Http\Controllers\Admin\ImportController::class, 'import'])->name('import.store');

        // Reports
        Route::get('/laporan/penjualan', [\App\Http\Controllers\Admin\ReportController::class, 'sales'])->name('reports.sales');
        Route::get('/laporan/inventori', [\App\Http\Controllers\Admin\ReportController::class, 'inventory'])->name('reports.inventory');
        Route::get('/laporan/pelanggan', [\App\Http\Controllers\Admin\ReportController::class, 'customers'])->name('reports.customers');

        // Settings
        Route::get('/settings', [SettingController::class, 'index'])->name('settings.index');
        Route::post('/settings', [SettingController::class, 'update'])->name('settings.update');

        // Services Monitoring
        Route::get('/services', [ServiceController::class, 'index'])->name('services.index');
        Route::post('/services/{service}/check', [ServiceController::class, 'check'])->name('services.check');
        Route::get('/services/check-all', [ServiceController::class, 'checkAll'])->name('services.checkAll');
    });
});
