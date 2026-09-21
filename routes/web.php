<?php

use App\Http\Controllers\Admin\Auth\LoginController;
use App\Http\Controllers\Admin\CategoryController as AdminCategoryController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\OrderController as AdminOrderController;
use App\Http\Controllers\Admin\ProductController as AdminProductController;
use App\Http\Controllers\Admin\SettingController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\CheckoutController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\PageController;
use App\Http\Controllers\ProductController;
use Illuminate\Support\Facades\Route;

// =============================================================================
// Frontend Routes
// =============================================================================

Route::get('/', [HomeController::class, 'index'])->name('home');
// Aliased admin login path: /login/admin -> /admin/login (was 404).
Route::redirect('/login/admin', '/admin/login', 301);


Route::get('/produk', [ProductController::class, 'index'])->name('products.index');
Route::get('/produk/{product:slug}', [ProductController::class, 'show'])->name('products.show');
Route::get('/kategori', [CategoryController::class, 'index'])->name('categories.index');
Route::get('/kategori/{category:slug}', [CategoryController::class, 'show'])->name('categories.show');

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

// Static Pages
Route::get('/tentang', [PageController::class, 'about'])->name('about');
Route::get('/cara-nitip', [PageController::class, 'howTo'])->name('how-to');
Route::get('/kontak', [PageController::class, 'contact'])->name('contact');

// =============================================================================
// Admin Routes
// =============================================================================

Route::prefix('admin')->name('admin.')->group(function () {
    // Auth
    Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [LoginController::class, 'login'])->name('login.post');
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

        // Settings
        Route::get('/settings', [SettingController::class, 'index'])->name('settings.index');
        Route::post('/settings', [SettingController::class, 'update'])->name('settings.update');
    });
});
