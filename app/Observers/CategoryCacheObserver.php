<?php

namespace App\Observers;

use App\Models\Category;
use Illuminate\Support\Facades\Cache;

class CategoryCacheObserver
{
    /**
     * Handle the Category "created" event.
     */
    public function created(Category $category): void
    {
        $this->invalidateCache();
    }

    /**
     * Handle the Category "updated" event.
     */
    public function updated(Category $category): void
    {
        $this->invalidateCache();
    }

    /**
     * Handle the Category "deleted" event.
     */
    public function deleted(Category $category): void
    {
        $this->invalidateCache();
    }

    /**
     * Invalidate all relevant caches when a category changes
     */
    private function invalidateCache(): void
    {
        Cache::forget('home.categories');
        Cache::forget('products.active_categories');
        Cache::forget('products.viral_categories');
    }
}
