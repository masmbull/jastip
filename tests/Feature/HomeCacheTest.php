<?php

namespace Tests\Feature;

use App\Models\Product;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

/**
 * Homepage caches Eloquent collections. With the real "database" cache store
 * (default in prod) a misconfigured serializable_classes allow-list makes the
 * second, cache-hit render blow up with __PHP_Incomplete_Class -> 500.
 * Test suite normally runs on the "array" store, so this pins to "database".
 */
class HomeCacheTest extends TestCase
{
    use RefreshDatabase;

    public function test_homepage_renders_on_cache_hit_with_database_store(): void
    {
        Config::set('cache.default', 'database');
        Http::fake();
        $this->seed();

        // First hit populates the cache, second hit reads it back.
        $this->get(route('home'))->assertOk();
        $this->get(route('home'))->assertOk();
    }

    public function test_creating_product_forgets_home_categories_cache(): void
    {
        Http::fake();
        $this->seed();

        // Warm the cache the same way the homepage does, then confirm a new
        // product busts it (home.categories embeds per-category product counts).
        $this->get(route('home'))->assertOk();
        $this->assertNotNull(Cache::get('home.categories'));

        Product::query()->firstOrFail()->update(['name' => 'Updated for cache test']);

        $this->assertNull(Cache::get('home.categories'));
    }
}
