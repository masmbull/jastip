<?php

namespace App\Providers;

use App\Events\OrderStatusChanged;
use App\Listeners\SendOrderStatusNotification;
use App\Models\Product;
use App\Models\Category;
use App\Observers\ProductCacheObserver;
use App\Observers\CategoryCacheObserver;
use App\Services\CartService;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        // TLS terminated at Cloudflare edge; keep generated URLs https in production
        if ($this->app->environment('production')) {
            URL::forceScheme('https');
        }

        // Register CartService as singleton for consistent cart across the session
        $this->app->singleton(CartService::class, function ($app) {
            return new CartService();
        });

        // Register event listeners
        Event::listen(
            OrderStatusChanged::class,
            SendOrderStatusNotification::class
        );

        // Cache invalidation observers - clear cache when products/categories are modified
        Product::observe(ProductCacheObserver::class);
        Category::observe(CategoryCacheObserver::class);
    }
}
