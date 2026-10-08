<?php

namespace App\Observers;

use App\Models\Product;
use Illuminate\Support\Facades\Cache;

class ProductCacheObserver
{
    /**
     * Handle the Product "created" event.
     */
    public function created(Product $product): void
    {
        $this->invalidateCache();
    }

    /**
     * Handle the Product "updated" event.
     */
    public function updated(Product $product): void
    {
        $this->invalidateCache();
    }

    /**
     * Handle the Product "deleted" event.
     */
    public function deleted(Product $product): void
    {
        $this->invalidateCache();
    }

    /**
     * Invalidate all relevant caches when a product changes
     */
    private function invalidateCache(): void
    {
        Cache::forget('home.featured_products');
        Cache::forget('home.popular_products');
        Cache::forget('home.viral_products');
        // home.categories uses withCount('products')/whereHas -> product-dep.
        Cache::forget('home.categories');
        Cache::forget('products.active_categories');
        Cache::forget('products.viral_categories');
        Cache::forget('products.viral_count');
        Cache::forget('products.viral_sold');
    }
}
